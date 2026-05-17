<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SalesDailyReportSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * إعدادات خصم التقرير اليومي — تُعرض من قسم خصومات الموظفين.
 */
class SalesDailyReportPenaltyController extends Controller
{
    public function edit(): View
    {
        return view('admin.employee-deductions.daily-report-penalty-settings', [
            'settings' => SalesDailyReportSettings::all(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
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

        return redirect()->route('admin.employee-deductions.daily-report-penalty-settings')
            ->with('success', 'تم حفظ إعدادات الخصم التلقائي للتقارير اليومية.');
    }
}
