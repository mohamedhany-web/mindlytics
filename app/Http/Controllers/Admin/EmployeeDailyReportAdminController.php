<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDailyReport;
use App\Models\User;
use App\Services\EmployeeDailyReportService;
use App\Support\EmployeeDailyReportSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeDailyReportAdminController extends Controller
{
    public function index(Request $request, EmployeeDailyReportService $service)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->date)
            : today();

        $employees = User::employees()->where('is_active', true)->orderBy('name')->get();

        $rangeFrom = $request->filled('date')
            ? $date->copy()->startOfDay()
            : now()->copy()->startOfMonth()->startOfDay();
        $rangeTo = $request->filled('date')
            ? $date->copy()->startOfDay()
            : now()->copy()->startOfDay();

        $penaltyEmployees = $request->filled('user_id')
            ? $employees->where('id', (int) $request->user_id)
            : $employees;
        $penaltiesSynced = $service->applyDuePenaltiesInRange($rangeFrom, $rangeTo, $penaltyEmployees);

        $query = EmployeeDailyReport::query()->with(['user', 'autoDeduction']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date')) {
            $query->whereDate('report_date', $date);
        } else {
            $query->whereBetween('report_date', [now()->startOfMonth(), now()]);
        }

        $reports = $query->orderByDesc('report_date')->paginate(30)->withQueryString();

        $missingToday = [];
        foreach ($employees as $emp) {
            if (! $service->employeeRequiresReport($emp, today())) {
                continue;
            }
            $has = EmployeeDailyReport::forUser($emp->id)
                ->whereDate('report_date', today())
                ->where('status', EmployeeDailyReport::STATUS_SUBMITTED)
                ->exists();
            if (! $has) {
                $missingToday[] = $emp;
            }
        }

        $complianceRows = $service->submissionRateForMonth($employees, now());

        $stats = [
            'total_today' => EmployeeDailyReport::whereDate('report_date', today())->where('status', 'submitted')->count(),
            'missing_today' => count($missingToday),
            'employees' => $employees->count(),
            'with_penalty' => EmployeeDailyReport::query()
                ->whereBetween('report_date', [$rangeFrom->toDateString(), $rangeTo->toDateString()])
                ->whereNotNull('auto_deduction_id')
                ->count(),
        ];

        return view('admin.employee-daily-reports.index', compact(
            'reports', 'employees', 'missingToday', 'complianceRows', 'stats', 'date', 'penaltiesSynced'
        ));
    }

    public function show(int $id)
    {
        $report = EmployeeDailyReport::with(['user', 'autoDeduction'])->findOrFail($id);

        return view('admin.employee-daily-reports.show', compact('report'));
    }

    public function settings()
    {
        return view('admin.employee-daily-reports.settings', [
            'settings' => EmployeeDailyReportSettings::all(),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'enabled' => 'nullable|boolean',
            'penalty_enabled' => 'nullable|boolean',
            'penalty_amount' => 'nullable|numeric|min:0',
            'work_days_only' => 'nullable|boolean',
            'exclude_sales_employees' => 'nullable|boolean',
        ]);

        EmployeeDailyReportSettings::save([
            'enabled' => $request->boolean('enabled'),
            'penalty_enabled' => $request->boolean('penalty_enabled'),
            'penalty_amount' => (float) ($validated['penalty_amount'] ?? EmployeeDailyReportSettings::penaltyAmount()),
            'work_days_only' => $request->boolean('work_days_only'),
            'exclude_sales_employees' => $request->boolean('exclude_sales_employees'),
        ]);

        return back()->with('success', 'تم حفظ إعدادات التقارير اليومية.');
    }
}
