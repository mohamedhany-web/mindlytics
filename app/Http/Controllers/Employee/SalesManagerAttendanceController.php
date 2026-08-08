<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendanceRecord;
use App\Models\EmployeeWorkUnlock;
use App\Models\User;
use App\Services\EmployeeAttendanceService;
use App\Services\SalesShiftScheduleService;
use App\Services\SalesTeamService;
use App\Support\EmployeeAttendanceSettings;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesManagerAttendanceController extends Controller
{
    public function __construct(
        private SalesTeamService $teamService,
        private EmployeeAttendanceService $attendance,
        private SalesShiftScheduleService $shifts,
    ) {
        $this->middleware('sales.manager');
    }

    public function index(Request $request): View
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);
        $members = User::query()
            ->whereIn('id', $memberIds)
            ->with(['workSchedule', 'employeeJob'])
            ->orderBy('name')
            ->get(['id', 'name', 'work_schedule_id', 'weekly_off_day', 'employee_job_id', 'work_mode', 'offline_attendance_type', 'onsite_days']);

        $query = EmployeeAttendanceRecord::query()
            ->with(['user:id,name,work_mode', 'workSchedule'])
            ->whereIn('user_id', $memberIds)
            ->orderByDesc('work_date')
            ->orderByDesc('id');

        if ($request->filled('employee_id') && in_array((int) $request->employee_id, $memberIds, true)) {
            $query->where('user_id', (int) $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('work_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('work_date', '<=', $request->to);
        }

        $records = $query->paginate(30)->withQueryString();

        $statsBase = EmployeeAttendanceRecord::query()->whereIn('user_id', $memberIds)
            ->when($request->filled('from'), fn ($q) => $q->whereDate('work_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('work_date', '<=', $request->to));

        $stats = [
            'total' => (clone $statsBase)->count(),
            'completed' => (clone $statsBase)->where('status', 'completed')->count(),
            'late' => (clone $statsBase)->where('is_late', true)->count(),
            'active_now' => (clone $statsBase)->where('status', 'active')->count(),
            'absent' => (clone $statsBase)->whereNull('clock_in_at')->whereIn('status', ['pending', 'absent'])->count(),
            'pending_approval' => EmployeeAttendanceRecord::query()
                ->whereIn('user_id', $memberIds)
                ->whereDate('work_date', today())
                ->where('attendance_approval_status', EmployeeAttendanceRecord::APPROVAL_PENDING)
                ->whereNull('clock_in_at')
                ->count(),
        ];

        $pendingApprovals = EmployeeAttendanceRecord::query()
            ->with(['user:id,name,work_mode'])
            ->whereIn('user_id', $memberIds)
            ->whereDate('work_date', today())
            ->where('attendance_approval_status', EmployeeAttendanceRecord::APPROVAL_PENDING)
            ->whereNull('clock_in_at')
            ->orderBy('attendance_requested_at')
            ->get();

        $activeUnlocks = EmployeeWorkUnlock::query()
            ->with(['user:id,name', 'unlockedBy:id,name'])
            ->whereIn('user_id', $memberIds)
            ->active()
            ->orderByDesc('id')
            ->get();

        $memberStates = [];
        foreach ($members as $member) {
            $memberStates[$member->id] = $this->attendance->getState($member);
        }

        return view('employee.sales-manager.attendance.index', [
            'records' => $records,
            'stats' => $stats,
            'members' => $members,
            'team' => $team,
            'activeUnlocks' => $activeUnlocks,
            'pendingApprovals' => $pendingApprovals,
            'memberStates' => $memberStates,
            'durationOptions' => EmployeeWorkUnlock::durationOptions(),
            'latenessLabels' => EmployeeAttendanceRecord::latenessDecisionLabels(),
        ]);
    }

    public function employee(User $employee): View
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);

        abort_unless(in_array((int) $employee->id, $memberIds, true), 403);

        $employee->load('employeeJob', 'workSchedule');

        $records = EmployeeAttendanceRecord::query()
            ->where('user_id', $employee->id)
            ->with('workSchedule')
            ->orderByDesc('work_date')
            ->paginate(30);

        $summary = [
            'completed_days' => EmployeeAttendanceRecord::where('user_id', $employee->id)->where('status', 'completed')->count(),
            'late_days' => EmployeeAttendanceRecord::where('user_id', $employee->id)->where('is_late', true)->count(),
            'total_hours' => round(EmployeeAttendanceRecord::where('user_id', $employee->id)->sum('worked_minutes') / 60, 1),
            'active_days' => EmployeeAttendanceRecord::where('user_id', $employee->id)->where('status', 'active')->count(),
        ];

        $state = $this->attendance->getState($employee);
        $activeUnlock = $this->attendance->activeUnlock($employee);
        $unlockHistory = EmployeeWorkUnlock::query()
            ->with(['unlockedBy:id,name', 'revokedBy:id,name'])
            ->where('user_id', $employee->id)
            ->orderByDesc('id')
            ->limit(15)
            ->get();

        return view('employee.sales-manager.attendance.employee', [
            'employee' => $employee,
            'records' => $records,
            'summary' => $summary,
            'team' => $team,
            'state' => $state,
            'activeUnlock' => $activeUnlock,
            'unlockHistory' => $unlockHistory,
            'durationOptions' => EmployeeWorkUnlock::durationOptions(),
        ]);
    }

    public function unlock(Request $request, User $employee): RedirectResponse
    {
        $manager = Auth::user();
        $team = $this->teamService->managedTeamOrFail($manager);
        $memberIds = $this->teamService->memberUserIds($team);

        abort_unless(in_array((int) $employee->id, $memberIds, true), 403);

        $data = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
            'duration' => ['required', 'string', 'in:2h,4h,8h,end_of_day'],
        ], [
            'reason.required' => 'سبب فتح النظام مطلوب.',
            'reason.min' => 'اكتب سبباً أوضح (5 أحرف على الأقل).',
            'duration.required' => 'اختر مدة الفتح.',
            'duration.in' => 'مدة الفتح غير صالحة.',
        ]);

        $unlock = $this->attendance->unlockForManager(
            $employee,
            $manager,
            $data['reason'],
            $data['duration'],
        );

        return back()->with(
            'success',
            'تم فتح النظام لـ «'.$employee->name.'» حتى '.$unlock->expires_at->format('Y-m-d H:i').' — يمكنه تسجيل الحضور والعمل الآن.'
        );
    }

    public function revokeUnlock(Request $request, User $employee, EmployeeWorkUnlock $unlock): RedirectResponse
    {
        $manager = Auth::user();
        $team = $this->teamService->managedTeamOrFail($manager);
        $memberIds = $this->teamService->memberUserIds($team);

        abort_unless(in_array((int) $employee->id, $memberIds, true), 403);
        abort_unless((int) $unlock->user_id === (int) $employee->id, 404);

        $data = $request->validate([
            'revoke_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $this->attendance->revokeUnlock($unlock, $manager, $data['revoke_reason'] ?? null);

        return back()->with('success', 'تم إلغاء فتح النظام لـ «'.$employee->name.'».');
    }

    public function approve(Request $request, EmployeeAttendanceRecord $record): RedirectResponse
    {
        $manager = Auth::user();
        $team = $this->teamService->managedTeamOrFail($manager);
        $memberIds = $this->teamService->memberUserIds($team);

        abort_unless(in_array((int) $record->user_id, $memberIds, true), 403);

        $data = $request->validate([
            'lateness_decision' => ['required', 'in:on_time,excused_late,confirmed_late'],
        ], [
            'lateness_decision.required' => 'اختر قرار الحضور (في الميعاد / إعفاء / تأخير بخصم).',
        ]);

        $this->attendance->approveAttendanceRequest($record, $manager, $data['lateness_decision'], $request->ip());

        $labels = EmployeeAttendanceRecord::latenessDecisionLabels();

        return back()->with(
            'success',
            'تم قبول حضور «'.($record->user?->name ?? 'الموظف').'» — '.$labels[$data['lateness_decision']]
        );
    }

    public function reject(Request $request, EmployeeAttendanceRecord $record): RedirectResponse
    {
        $manager = Auth::user();
        $team = $this->teamService->managedTeamOrFail($manager);
        $memberIds = $this->teamService->memberUserIds($team);

        abort_unless(in_array((int) $record->user_id, $memberIds, true), 403);

        $data = $request->validate([
            'reason' => ['required', 'string', 'min:3', 'max:500'],
        ]);

        $this->attendance->rejectAttendanceRequest($record, $manager, $data['reason']);

        return back()->with('success', 'تم رفض طلب الحضور.');
    }

    public function waiveLate(Request $request, EmployeeAttendanceRecord $record): RedirectResponse
    {
        $manager = Auth::user();
        $team = $this->teamService->managedTeamOrFail($manager);
        $memberIds = $this->teamService->memberUserIds($team);

        abort_unless(in_array((int) $record->user_id, $memberIds, true), 403);

        $data = $request->validate([
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $this->attendance->waiveLatePenalty($record, $manager, $data['note'] ?? null);

        return back()->with('success', 'تم إعفاء خصم التأخير لـ «'.($record->user?->name ?? 'الموظف').'».');
    }

    public function offlineDay(Request $request): View
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);

        $date = $request->filled('date')
            ? Carbon::parse($request->input('date'))->startOfDay()
            : today()->startOfDay();

        $roster = $this->shifts->officeRosterForDate($date, $memberIds);
        $members = $roster['members'];

        $stats = [
            'office' => count($members),
            'confirmed' => count(array_filter($members, fn ($m) => $m['manager_confirmed'])),
            'late' => count(array_filter($members, fn ($m) => $m['manager_confirmed'] && $m['is_late'])),
            'awaiting' => count(array_filter($members, fn ($m) => ! $m['manager_confirmed'])),
        ];

        return view('employee.sales-manager.attendance.offline-day', [
            'team' => $team,
            'date' => $date,
            'roster' => $roster,
            'stats' => $stats,
            'defaultDeduction' => EmployeeAttendanceSettings::latePenaltyAmount(),
        ]);
    }

    public function confirmOfflineDay(Request $request): RedirectResponse
    {
        $manager = Auth::user();
        $team = $this->teamService->managedTeamOrFail($manager);
        $memberIds = $this->teamService->memberUserIds($team);

        $data = $request->validate([
            'employee_id' => ['required', 'integer'],
            'date' => ['required', 'date'],
            'decision' => ['required', 'in:on_time,late'],
            'deduction_amount' => ['required_if:decision,late', 'nullable', 'numeric', 'min:0'],
        ], [
            'employee_id.required' => 'الموظف مطلوب.',
            'decision.required' => 'اختر: في الميعاد أو متأخر.',
            'decision.in' => 'قرار الحضور غير صالح.',
            'deduction_amount.required_if' => 'أدخل مبلغ الخصم عند التأخير.',
            'deduction_amount.numeric' => 'مبلغ الخصم يجب أن يكون رقمًا.',
            'deduction_amount.min' => 'مبلغ الخصم لا يمكن أن يكون سالبًا.',
        ]);

        abort_unless(in_array((int) $data['employee_id'], $memberIds, true), 403);

        $employee = User::query()->findOrFail((int) $data['employee_id']);
        $date = Carbon::parse($data['date'])->startOfDay();
        $amount = $data['decision'] === 'late'
            ? (float) ($data['deduction_amount'] ?? 0)
            : null;

        $this->attendance->confirmOfficeShiftPresence(
            $employee,
            $manager,
            $date,
            $data['decision'],
            $amount,
            $request->ip(),
        );

        $label = $data['decision'] === 'late'
            ? 'متأخر (خصم '.number_format((float) $amount, 2).' ج.م)'
            : 'في الميعاد';

        return redirect()
            ->route('employee.sales-manager.attendance.offline-day', ['date' => $date->toDateString()])
            ->with('success', 'تم تأكيد حضور «'.$employee->name.'» — '.$label);
    }
}
