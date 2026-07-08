<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendanceRecord;
use App\Models\User;
use App\Services\SalesTeamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesManagerAttendanceController extends Controller
{
    public function __construct(
        private SalesTeamService $teamService
    ) {
        $this->middleware('sales.manager');
    }

    public function index(Request $request)
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);
        $members = User::query()->whereIn('id', $memberIds)->orderBy('name')->get(['id', 'name']);

        $query = EmployeeAttendanceRecord::query()
            ->with(['user:id,name', 'workSchedule'])
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
        ];

        return view('employee.sales-manager.attendance.index', compact('records', 'stats', 'members', 'team'));
    }

    public function employee(User $employee)
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

        return view('employee.sales-manager.attendance.employee', compact('employee', 'records', 'summary', 'team'));
    }
}
