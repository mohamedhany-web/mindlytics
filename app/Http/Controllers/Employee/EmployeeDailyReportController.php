<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDailyReport;
use App\Services\EmployeeDailyReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeDailyReportController extends Controller
{
    public function index(Request $request, EmployeeDailyReportService $service)
    {
        $user = Auth::user();
        abort_unless($user->isEmployee(), 403);

        $date = $request->filled('date')
            ? Carbon::parse($request->date)->startOfDay()
            : now()->startOfDay();

        $service->applyDuePenaltiesInRange(
            now()->subDays(3)->startOfDay(),
            now()->startOfDay(),
            collect([$user])
        );

        $reports = EmployeeDailyReport::forUser($user->id)
            ->with('autoDeduction')
            ->orderByDesc('report_date')
            ->limit(60)
            ->get();

        $todayReport = EmployeeDailyReport::forUser($user->id)->whereDate('report_date', today())->first();

        $monthStart = now()->startOfMonth();
        $submittedThisMonth = EmployeeDailyReport::forUser($user->id)
            ->where('status', EmployeeDailyReport::STATUS_SUBMITTED)
            ->whereBetween('report_date', [$monthStart, now()])
            ->count();

        return view('employee.daily-reports.index', compact('reports', 'todayReport', 'submittedThisMonth', 'date'));
    }

    public function edit(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->isEmployee(), 403);

        $date = $request->filled('date')
            ? Carbon::parse($request->date)->startOfDay()
            : today();

        $report = EmployeeDailyReport::firstOrNew([
            'user_id' => $user->id,
            'report_date' => $date->toDateString(),
        ]);

        return view('employee.daily-reports.edit', compact('report', 'date'));
    }

    public function store(Request $request, EmployeeDailyReportService $service)
    {
        $user = Auth::user();
        abort_unless($user->isEmployee(), 403);

        $validated = $request->validate([
            'report_date' => 'required|date',
            'summary' => 'nullable|string|max:5000',
            'tasks_done' => 'nullable|string|max:10000',
            'tomorrow_plan' => 'nullable|string|max:5000',
            'blockers' => 'nullable|string|max:3000',
            'hours_worked' => 'nullable|numeric|min:0|max:24',
            'submit' => 'nullable|boolean',
        ]);

        $submit = $request->input('submit') === '1';
        if ($submit) {
            $request->validate([
                'summary' => 'required|string|min:10|max:5000',
                'tasks_done' => 'required|string|min:5|max:10000',
            ]);
        }

        $date = Carbon::parse($validated['report_date'])->startOfDay();
        $service->saveReport($user, $date, $validated, $submit);

        return redirect()
            ->route('employee.daily-reports.index')
            ->with('success', $submit ? 'تم إرسال التقرير اليومي.' : 'تم حفظ المسودة.');
    }
}
