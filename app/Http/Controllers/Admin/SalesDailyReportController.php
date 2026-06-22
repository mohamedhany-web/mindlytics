<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SalesDailyReportService;
use App\Services\SalesDailyReportsExcelExportService;
use App\Support\SalesDailyReportSettings;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesDailyReportController extends Controller
{
    public function index(Request $request, SalesDailyReportService $service): View
    {
        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : now()->subDays(14)->startOfDay();
        $to = $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : now()->endOfDay();
        $userId = $request->filled('user_id') ? (int) $request->user_id : null;
        $status = $request->get('status');

        $reps = User::salesEmployees()->where('is_active', true)->orderBy('name')->get();
        $penaltyEmployees = $userId
            ? $reps->where('id', $userId)
            : $reps;
        $penaltiesSynced = $service->applyDuePenaltiesInRange($from->copy()->startOfDay(), $to->copy()->startOfDay(), $penaltyEmployees);

        $reports = $service->reportsQuery($userId, $from, $to, $status);
        $settings = SalesDailyReportSettings::all();

        $stats = [
            'total' => $reports->count(),
            'submitted' => $reports->where('status', 'submitted')->count(),
            'with_penalty' => $reports->whereNotNull('auto_deduction_id')->count(),
        ];

        return view('admin.sales.daily-reports.index', compact('reports', 'reps', 'from', 'to', 'userId', 'status', 'settings', 'stats', 'penaltiesSynced'));
    }

    public function show(int $id, SalesDailyReportService $service): View
    {
        $report = \App\Models\SalesDailyReport::with(['user', 'contacts.lead', 'autoDeduction'])->findOrFail($id);
        $kpiComparison = $report->user
            ? $service->kpiComparisonForReport($report->user, $report, $report->report_date)
            : null;

        return view('admin.sales.daily-reports.show', compact('report', 'kpiComparison'));
    }

    public function export(Request $request, SalesDailyReportService $service, SalesDailyReportsExcelExportService $excel): StreamedResponse
    {
        $from = Carbon::parse($request->input('from', now()->subDays(30)->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();
        $userId = $request->filled('user_id') ? (int) $request->user_id : null;

        $reports = $service->reportsQuery($userId, $from, $to, $request->get('status'));
        $context = 'من '.$from->format('Y-m-d').' إلى '.$to->format('Y-m-d');
        if ($userId) {
            $rep = User::find($userId);
            $context .= ' — موظف: '.($rep->name ?? $userId);
        }

        $spreadsheet = $excel->buildSpreadsheet($reports, $context);
        $filename = 'تقارير-يومية-مبيعات-'.now()->format('Y-m-d').'.xlsx';

        return response()->streamDownload(function () use ($excel, $spreadsheet) {
            $excel->writeToOutput($spreadsheet);
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function settings(): View
    {
        return view('admin.sales.daily-reports.settings', [
            'settings' => SalesDailyReportSettings::all(),
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        if ($request->filled('deadline_time')) {
            $request->merge([
                'deadline_time' => substr((string) $request->input('deadline_time'), 0, 5),
            ]);
        }

        $validated = $request->validate([
            'enabled' => 'nullable|boolean',
            'work_days_only' => 'nullable|boolean',
            'deadline_time' => 'required|string|regex:/^\d{2}:\d{2}$/',
            'penalty_enabled' => 'nullable|boolean',
            'penalty_amount' => 'required|numeric|min:0.01',
            'penalty_title' => 'required|string|max:255',
            'penalty_description' => 'nullable|string|max:2000',
            'penalty_type' => 'required|in:tax,insurance,loan,penalty,other',
            'penalty_status' => 'required|in:pending,applied,cancelled',
            'kpi_submission_target_pct' => 'required|numeric|min:50|max:100',
        ]);

        SalesDailyReportSettings::save([
            'enabled' => $request->boolean('enabled'),
            'work_days_only' => $request->boolean('work_days_only'),
            'deadline_time' => $validated['deadline_time'],
            'penalty_enabled' => $request->boolean('penalty_enabled'),
            'penalty_amount' => (float) $validated['penalty_amount'],
            'penalty_title' => $validated['penalty_title'],
            'penalty_description' => $validated['penalty_description'] ?? '',
            'penalty_type' => $validated['penalty_type'],
            'penalty_status' => $validated['penalty_status'],
            'kpi_submission_target_pct' => (float) $validated['kpi_submission_target_pct'],
        ]);

        return redirect()->route('admin.sales.daily-reports.settings')
            ->with('success', 'تم حفظ إعدادات التقرير اليومي والخصم التلقائي.');
    }
}
