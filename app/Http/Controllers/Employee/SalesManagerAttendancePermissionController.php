<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesAttendancePermission;
use App\Models\User;
use App\Services\EmployeeAttendanceService;
use App\Services\SalesTeamService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesManagerAttendancePermissionController extends Controller
{
    public function __construct(
        private SalesTeamService $teamService,
        private EmployeeAttendanceService $attendance,
    ) {
        $this->middleware('sales.manager');
    }

    public function index(Request $request): View
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);

        $members = User::query()
            ->whereIn('id', $memberIds)
            ->orderBy('name')
            ->get(['id', 'name', 'work_mode', 'offline_attendance_type', 'onsite_days', 'work_week_plan']);

        $offlineMembers = $members->filter(function (User $member) {
            return $member->isOfflineWorker()
                || $member->isHybridWorker()
                || $member->requiresManagerApprovalFor(today());
        })->values();

        $date = $request->filled('date')
            ? Carbon::parse($request->date)->startOfDay()
            : today();

        $permissions = SalesAttendancePermission::query()
            ->whereIn('employee_id', $memberIds)
            ->when($request->filled('date'), fn ($q) => $q->whereDate('work_date', $date->toDateString()))
            ->with(['employee:id,name', 'granter:id,name', 'revoker:id,name'])
            ->orderByDesc('work_date')
            ->orderByDesc('id')
            ->limit(60)
            ->get();

        return view('employee.sales-manager.attendance.permissions', [
            'team' => $team,
            'members' => $members,
            'offlineMembers' => $offlineMembers,
            'permissions' => $permissions,
            'date' => $date,
            'typeLabels' => SalesAttendancePermission::typeLabels(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);

        $validated = $request->validate([
            'employee_id' => 'required|integer|in:'.implode(',', $memberIds ?: [0]),
            'type' => 'required|in:'.SalesAttendancePermission::TYPE_DAY_ABSENCE.','.SalesAttendancePermission::TYPE_EARLY_DEPARTURE,
            'work_date' => 'required|date',
            'early_departure_time' => 'nullable|date_format:H:i',
            'reason' => 'nullable|string|max:500',
        ], [
            'employee_id.required' => 'اختر الموظف.',
            'employee_id.in' => 'الموظف ليس ضمن فريقك.',
            'type.required' => 'اختر نوع الإذن.',
            'work_date.required' => 'حدد تاريخ الإذن.',
        ]);

        $employee = User::query()->findOrFail((int) $validated['employee_id']);
        $workDate = Carbon::parse($validated['work_date'])->startOfDay();

        if (! $employee->isOfflineWorker() && ! $employee->isHybridWorker() && ! $employee->requiresManagerApprovalFor($workDate)) {
            return back()->withErrors(['employee_id' => 'الإذن مخصص لأعضاء الفريق اللي بينزلوا أوفلاين/مقر في هذا اليوم.'])->withInput();
        }

        if ($validated['type'] === SalesAttendancePermission::TYPE_EARLY_DEPARTURE
            && empty($validated['early_departure_time'])) {
            return back()->withErrors(['early_departure_time' => 'حدد وقت الانصراف المبكر.'])->withInput();
        }

        $exists = SalesAttendancePermission::query()
            ->approved()
            ->forEmployeeDate((int) $employee->id, $workDate)
            ->where('type', $validated['type'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['type' => 'يوجد إذن ساري من نفس النوع لهذا الموظف في هذا اليوم.'])->withInput();
        }

        $permission = SalesAttendancePermission::create([
            'employee_id' => $employee->id,
            'granted_by' => Auth::id(),
            'type' => $validated['type'],
            'work_date' => $workDate->toDateString(),
            'early_departure_time' => $validated['type'] === SalesAttendancePermission::TYPE_EARLY_DEPARTURE
                ? ($validated['early_departure_time'] ?? null)
                : null,
            'reason' => $validated['reason'] ?? null,
            'status' => SalesAttendancePermission::STATUS_APPROVED,
        ]);

        if ($permission->type === SalesAttendancePermission::TYPE_DAY_ABSENCE) {
            $this->applyDayAbsenceToRecord($employee, $workDate);
        }

        $label = $permission->typeLabel();

        return back()->with('success', "تم تسجيل {$label} لـ {$employee->name} بتاريخ {$workDate->format('Y-m-d')}.");
    }

    public function revoke(Request $request, SalesAttendancePermission $permission): RedirectResponse
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);

        abort_unless(in_array((int) $permission->employee_id, $memberIds, true), 404);
        abort_unless($permission->isActive(), 422);

        $validated = $request->validate([
            'revoke_reason' => 'nullable|string|max:500',
        ]);

        $permission->update([
            'status' => SalesAttendancePermission::STATUS_REVOKED,
            'revoked_at' => now(),
            'revoked_by' => Auth::id(),
            'revoke_reason' => $validated['revoke_reason'] ?? 'أُلغي بواسطة مدير المبيعات',
        ]);

        if ($permission->type === SalesAttendancePermission::TYPE_DAY_ABSENCE) {
            $employee = $permission->employee;
            if ($employee) {
                $this->attendance->resyncTodayAfterEmployeeUpdate($employee);
            }
        }

        return back()->with('success', 'تم إلغاء الإذن.');
    }

    private function applyDayAbsenceToRecord(User $employee, Carbon $workDate): void
    {
        $schedule = $this->attendance->resolveSchedule($employee);
        if (! $schedule) {
            return;
        }

        $record = $this->attendance->ensureRecordForDate($employee, $schedule, $workDate);

        if ($record->clock_in_at) {
            return;
        }

        $record->update([
            'status' => 'on_leave',
            'metadata' => array_merge($record->metadata ?? [], [
                'sales_day_absence_at' => now()->toIso8601String(),
                'sales_day_absence_by' => Auth::id(),
            ]),
        ]);
    }
}
