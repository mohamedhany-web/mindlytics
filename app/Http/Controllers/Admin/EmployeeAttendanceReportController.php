<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendanceRecord;
use App\Models\EmployeeJob;
use App\Models\EmployeeSalaryDeduction;
use App\Models\User;
use App\Services\EmployeeAttendanceExcelExport;
use App\Services\EmployeeAttendancePenaltyService;
use App\Support\EmployeeAttendanceSettings;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeAttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeAttendanceRecord::query()
            ->with(['user.employeeJob', 'workSchedule', 'lateDeduction', 'absenceDeduction', 'incompleteDeduction'])
            ->orderByDesc('work_date')
            ->orderByDesc('id');
        if ($request->filled('employee_id')) {
            $query->where('user_id', (int) $request->employee_id);
        }

        if ($request->filled('job_id')) {
            $query->whereHas('user', fn ($q) => $q->where('employee_job_id', (int) $request->job_id));
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

        if ($request->boolean('late_only')) {
            $query->where('is_late', true);
        }

        $records = $query->paginate(30)->withQueryString();

        $statsBase = EmployeeAttendanceRecord::query()
            ->when($request->filled('from'), fn ($q) => $q->whereDate('work_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('work_date', '<=', $request->to));

        $stats = [
            'total' => (clone $statsBase)->count(),
            'completed' => (clone $statsBase)->where('status', 'completed')->count(),
            'late' => (clone $statsBase)->where('is_late', true)->count(),
            'absent' => (clone $statsBase)->whereNull('clock_in_at')->whereIn('status', ['pending', 'absent'])->count(),
            'avg_hours' => round(((clone $statsBase)->whereNotNull('worked_minutes')->avg('worked_minutes') ?? 0) / 60, 2),
            'total_deductions' => (float) EmployeeSalaryDeduction::query()
                ->where('notes', 'خصم تلقائي — حضور وانصراف')
                ->when($request->filled('from'), fn ($q) => $q->whereDate('deduction_date', '>=', $request->from))
                ->when($request->filled('to'), fn ($q) => $q->whereDate('deduction_date', '<=', $request->to))
                ->sum('amount'),
        ];

        $employees = User::employees()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $jobs = EmployeeJob::active()->orderBy('name')->get(['id', 'name']);

        return view('admin.employee-attendance.index', compact('records', 'stats', 'employees', 'jobs'));
    }

    public function employeeSummary(User $employee)
    {
        abort_unless($employee->isEmployee(), 404);

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
            'tasks_completed' => $employee->employeeTasks()->where('status', 'completed')->count(),
            'tasks_overdue' => $employee->employeeTasks()
                ->where('deadline', '<', now())
                ->whereIn('status', ['pending', 'in_progress'])
                ->count(),
        ];

        return view('admin.employee-attendance.employee', compact('employee', 'records', 'summary'));
    }

    public function export(Request $request, EmployeeAttendanceExcelExport $export): StreamedResponse
    {
        return $export->streamFromRequest($request);
    }

    public function penaltySettings()
    {
        return view('admin.employee-attendance.penalty-settings', [
            'settings' => EmployeeAttendanceSettings::all(),
        ]);
    }

    public function updatePenaltySettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'late_penalty_amount' => 'required|numeric|min:0',
            'absence_penalty_amount' => 'required|numeric|min:0',
            'incomplete_penalty_amount' => 'required|numeric|min:0',
            'late_penalty_title' => 'required|string|max:255',
            'absence_penalty_title' => 'required|string|max:255',
            'incomplete_penalty_title' => 'required|string|max:255',
            'penalty_type' => 'required|in:tax,insurance,loan,penalty,other',
            'penalty_status' => 'required|in:pending,applied,cancelled',
        ]);

        EmployeeAttendanceSettings::save([
            'penalties_enabled' => $request->boolean('penalties_enabled'),
            'late_penalty_enabled' => $request->boolean('late_penalty_enabled'),
            'absence_penalty_enabled' => $request->boolean('absence_penalty_enabled'),
            'incomplete_penalty_enabled' => $request->boolean('incomplete_penalty_enabled'),
            'notify_employee' => $request->boolean('notify_employee'),
            'late_penalty_amount' => (float) $validated['late_penalty_amount'],
            'absence_penalty_amount' => (float) $validated['absence_penalty_amount'],
            'incomplete_penalty_amount' => (float) $validated['incomplete_penalty_amount'],
            'late_penalty_title' => $validated['late_penalty_title'],
            'absence_penalty_title' => $validated['absence_penalty_title'],
            'incomplete_penalty_title' => $validated['incomplete_penalty_title'],
            'penalty_type' => $validated['penalty_type'],
            'penalty_status' => $validated['penalty_status'],
        ]);

        return back()->with('success', 'تم حفظ إعدادات خصومات الحضور.');
    }

    public function applyPenalties(Request $request, EmployeeAttendancePenaltyService $penalties): RedirectResponse
    {
        $validated = $request->validate([
            'date' => 'nullable|date',
        ]);

        $date = isset($validated['date']) ? Carbon::parse($validated['date']) : yesterday();
        $counts = $penalties->processDate($date);

        return back()->with('success', sprintf(
            'تم تطبيق الخصومات لتاريخ %s — تأخير: %d، غياب: %d، عدم إكمال: %d',
            $date->format('Y-m-d'),
            $counts['late'],
            $counts['absence'],
            $counts['incomplete'],
        ));
    }
}