<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendanceRecord;
use App\Models\LeaveRequest;
use App\Models\SalesLead;
use App\Models\SalesLeadGroup;
use App\Models\User;
use App\Services\EmployeeAttendanceService;
use App\Services\SalesEmployeeReportPdfService;
use App\Services\SalesManagerEmployeeReportService;
use App\Services\SalesTeamService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesManagerTeamController extends Controller
{
    public function __construct(
        private SalesTeamService $teamService,
        private EmployeeAttendanceService $attendance,
    ) {
        $this->middleware('sales.manager');
    }

    public function show(User $employee): View
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);

        abort_unless(in_array((int) $employee->id, $memberIds, true), 403);

        $employee->load(['employeeJob', 'workSchedule']);

        $open = SalesLead::query()
            ->where('assigned_to', $employee->id)
            ->openPipeline();

        $leadStats = [
            'total' => SalesLead::query()->where('assigned_to', $employee->id)->count(),
            'open' => (clone $open)->count(),
            'won' => SalesLead::query()->where('assigned_to', $employee->id)->where('stage', SalesLead::WON_STAGE)->count(),
            'followups_today' => (clone $open)
                ->whereNotNull('next_follow_up_at')
                ->whereDate('next_follow_up_at', today())
                ->count(),
            'followups_overdue' => (clone $open)
                ->whereNotNull('next_follow_up_at')
                ->where('next_follow_up_at', '<', now())
                ->count(),
        ];

        $leaves = LeaveRequest::query()
            ->where('employee_id', $employee->id)
            ->orderByDesc('start_date')
            ->limit(20)
            ->get();

        $activeLeave = LeaveRequest::query()
            ->where('employee_id', $employee->id)
            ->approved()
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->first();

        $pendingLeaves = LeaveRequest::query()
            ->where('employee_id', $employee->id)
            ->pending()
            ->orderBy('start_date')
            ->get();

        $upcomingLeaves = LeaveRequest::query()
            ->where('employee_id', $employee->id)
            ->approved()
            ->whereDate('start_date', '>', today())
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        $attendanceState = $this->attendance->getState($employee);

        $recentAttendance = EmployeeAttendanceRecord::query()
            ->where('user_id', $employee->id)
            ->orderByDesc('work_date')
            ->limit(7)
            ->get();

        return view('employee.sales-manager.team.show', compact(
            'employee',
            'team',
            'leadStats',
            'leaves',
            'activeLeave',
            'pendingLeaves',
            'upcomingLeaves',
            'attendanceState',
            'recentAttendance',
        ));
    }

    public function report(Request $request, User $employee, SalesManagerEmployeeReportService $reports): View
    {
        $this->assertTeamMember($employee);

        $validated = $this->validateReportFilters($request, $employee);

        $start = Carbon::parse($validated['date_from'])->startOfDay();
        $end = Carbon::parse($validated['date_to'])->endOfDay();

        $employeeReport = $reports->build(
            $employee,
            $start,
            $end,
            $validated['lead_scope'],
            $validated['group_id'] ?? null,
        );

        $repGroups = SalesLeadGroup::query()
            ->forAssignee($employee->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        $team = $this->teamService->managedTeamOrFail(Auth::user());

        return view('employee.sales-manager.team.report', [
            'employee' => $employee,
            'team' => $team,
            'employeeReport' => $employeeReport,
            'selectedRep' => $employee,
            'repGroups' => $repGroups,
            'filters' => $validated,
        ]);
    }

    public function reportPdf(Request $request, User $employee, SalesManagerEmployeeReportService $reports, SalesEmployeeReportPdfService $pdf): StreamedResponse
    {
        $this->assertTeamMember($employee);

        $validated = $this->validateReportFilters($request, $employee);

        $start = Carbon::parse($validated['date_from'])->startOfDay();
        $end = Carbon::parse($validated['date_to'])->endOfDay();

        $employeeReport = $reports->build(
            $employee,
            $start,
            $end,
            $validated['lead_scope'],
            $validated['group_id'] ?? null,
        );

        return $pdf->download($employeeReport);
    }

    private function assertTeamMember(User $employee): void
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);

        abort_unless(in_array((int) $employee->id, $memberIds, true), 403);
        // عضوية الفريق كافية — لا نمنع التقرير لو كود الوظيفة غير مضبوط على sales
    }

    /**
     * @return array{date_from: string, date_to: string, lead_scope: string, group_id: ?int}
     */
    private function validateReportFilters(Request $request, User $employee): array
    {
        $request->mergeIfMissing([
            'date_from' => now()->startOfMonth()->toDateString(),
            'date_to' => now()->toDateString(),
            'lead_scope' => 'touched',
        ]);

    $validated = $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'lead_scope' => ['required', 'string', Rule::in(['touched', 'new', 'transferred_from_admin', 'in_groups'])],
            'group_id' => ['nullable', 'integer', Rule::exists('sales_lead_groups', 'id')],
        ], [
            'date_from.required' => 'حدد تاريخ البداية.',
            'date_to.required' => 'حدد تاريخ النهاية.',
            'date_to.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي البداية.',
        ]);

        // Normalize empty query values like group_id=
        if ($request->input('group_id') === '' || $request->input('group_id') === null) {
            $validated['group_id'] = null;
        }

        if (! empty($validated['group_id'])) {
            $group = SalesLeadGroup::query()->findOrFail((int) $validated['group_id']);
            abort_unless($group->userHasAccess($employee->id), 403, 'المجموعة غير مسندة لهذا الموظف.');
            $validated['lead_scope'] = 'in_groups';
        } else {
            $validated['group_id'] = null;
        }

        return $validated;
    }
}
