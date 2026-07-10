<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendanceRecord;
use App\Models\LeaveRequest;
use App\Models\SalesLead;
use App\Models\User;
use App\Services\EmployeeAttendanceService;
use App\Services\SalesTeamService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
            'won' => SalesLead::query()->where('assigned_to', $employee->id)->where('stage', 'won')->count(),
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
}
