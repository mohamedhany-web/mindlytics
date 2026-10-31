<?php

use App\Domains\Sales\Http\Controllers\Employee\SalesManagerAttendanceController;
use App\Domains\Sales\Http\Controllers\Employee\SalesManagerAttendancePermissionController;
use App\Domains\Sales\Http\Controllers\Employee\SalesManagerCampaignReportController;
use App\Domains\Sales\Http\Controllers\Employee\SalesManagerDailyReportController;
use App\Domains\Sales\Http\Controllers\Employee\SalesManagerDistributionController;
use App\Domains\Sales\Http\Controllers\Employee\SalesManagerKpiController;
use App\Domains\Sales\Http\Controllers\Employee\SalesManagerLeadController;
use App\Domains\Sales\Http\Controllers\Employee\SalesManagerPresenceController;
use App\Domains\Sales\Http\Controllers\Employee\SalesManagerScorecardController;
use App\Domains\Sales\Http\Controllers\Employee\SalesManagerShiftSwapController;
use App\Domains\Sales\Http\Controllers\Employee\SalesManagerTransferController;
use Illuminate\Support\Facades\Route;

/**
 * بوابة مدير المبيعات — كل واجهات GET تذهب لـ CRM.
 */
$toCrm = fn (string $name, array $params = []) => redirect()->route('employee.crm.'.$name, $params);

Route::get('/', fn () => $toCrm('home'))->name('dashboard');
Route::get('live-board', fn () => $toCrm('home'))->name('live-board');
Route::get('pipeline', fn () => $toCrm('opportunities.index'))->name('pipeline');
Route::get('kpi', fn () => $toCrm('reports.kpi'))->name('kpi.index');
Route::get('kpi/targets', fn () => $toCrm('admin.index'))->name('kpi.targets');
Route::put('kpi/targets', [SalesManagerKpiController::class, 'updateTargets'])->name('kpi.targets.update');
Route::get('campaign-reports', fn () => $toCrm('reports.campaigns'))->name('campaign-reports.index');
Route::get('campaign-reports/export', [SalesManagerCampaignReportController::class, 'export'])->name('campaign-reports.export');
Route::get('scorecard', fn () => $toCrm('reports.index'))->name('scorecard.index');
Route::get('scorecard/pdf', [SalesManagerScorecardController::class, 'exportPdf'])->name('scorecard.pdf');
Route::get('scorecard/excel', [SalesManagerScorecardController::class, 'exportExcel'])->name('scorecard.excel');
Route::get('scorecard/{employee}', fn () => $toCrm('reports.index'))->name('scorecard.show');
Route::post('scorecard/{employee}/review', [SalesManagerScorecardController::class, 'review'])->name('scorecard.review');
Route::get('scorecard/{employee}/pdf', [SalesManagerScorecardController::class, 'exportEmployeePdf'])->name('scorecard.employee.pdf');
Route::get('team/{employee}', fn () => $toCrm('admin.index'))->name('team.show');
Route::get('team/{employee}/report', fn () => $toCrm('reports.index'))->name('team.report');
Route::get('team/{employee}/report/pdf', fn () => $toCrm('reports.index'))->name('team.report.pdf');
Route::get('leads', fn () => $toCrm('leads.index'))->name('leads.index');
Route::get('leads/{lead}', fn ($lead) => redirect()->route('employee.crm.leads.show', $lead))->name('leads.show');
Route::post('leads/{lead}/transfer', [SalesManagerLeadController::class, 'transfer'])->name('leads.transfer');
Route::get('distribution', fn () => $toCrm('admin.index'))->name('distribution.index');
Route::post('distribution/{lead}/assign', [SalesManagerDistributionController::class, 'assign'])->name('distribution.assign');
Route::get('org-chart', fn () => $toCrm('admin.index'))->name('org-chart.index');
Route::get('schedule-calendar', fn () => $toCrm('calendar.index'))->name('schedule-calendar.index');
Route::get('shifts', fn () => $toCrm('calendar.index'))->name('shifts.index');
Route::get('shifts/{employee}', fn () => $toCrm('calendar.index'))->name('shifts.show')->whereNumber('employee');
Route::get('shift-swaps', fn () => $toCrm('calendar.index'))->name('shift-swaps.index');
Route::post('shift-swaps/{swap}/review', [SalesManagerShiftSwapController::class, 'review'])->name('shift-swaps.review')->whereNumber('swap');
Route::get('follow-ups', fn () => $toCrm('leads.index', ['quick' => 'today']))->name('follow-ups.index');
Route::get('transfer', fn () => $toCrm('admin.index'))->name('transfer.index');
Route::post('transfer', [SalesManagerTransferController::class, 'store'])->name('transfer.store');
Route::get('attendance', fn () => $toCrm('admin.index'))->name('attendance.index');
Route::get('attendance/offline-day', fn () => $toCrm('admin.index'))->name('attendance.offline-day');
Route::post('attendance/offline-day/confirm', [SalesManagerAttendanceController::class, 'confirmOfflineDay'])->name('attendance.offline-day.confirm');
Route::get('attendance/permissions', fn () => $toCrm('admin.index'))->name('attendance.permissions.index');
Route::post('attendance/permissions', [SalesManagerAttendancePermissionController::class, 'store'])->name('attendance.permissions.store');
Route::post('attendance/permissions/{permission}/revoke', [SalesManagerAttendancePermissionController::class, 'revoke'])->name('attendance.permissions.revoke')->whereNumber('permission');
Route::get('attendance/employees/{employee}', fn () => $toCrm('admin.index'))->name('attendance.employee');
Route::post('attendance/employees/{employee}/unlock', [SalesManagerAttendanceController::class, 'unlock'])->name('attendance.unlock');
Route::post('attendance/employees/{employee}/unlocks/{unlock}/revoke', [SalesManagerAttendanceController::class, 'revokeUnlock'])->name('attendance.unlock.revoke');
Route::post('attendance/records/{record}/approve', [SalesManagerAttendanceController::class, 'approve'])->name('attendance.approve');
Route::post('attendance/records/{record}/reject', [SalesManagerAttendanceController::class, 'reject'])->name('attendance.reject');
Route::post('attendance/records/{record}/waive-late', [SalesManagerAttendanceController::class, 'waiveLate'])->name('attendance.waive-late');
Route::get('ops-board', fn () => $toCrm('admin.index'))->name('ops-board');
Route::get('presence', fn () => $toCrm('admin.index'))->name('presence.index');
Route::get('presence/poll', [SalesManagerPresenceController::class, 'poll'])->name('presence.poll');
Route::post('presence/violations/{violation}/acknowledge', [SalesManagerPresenceController::class, 'acknowledge'])->name('presence.acknowledge');
Route::get('daily-reports', fn () => $toCrm('reports.daily'))->name('daily-reports.index');
Route::get('daily-reports/{report}', fn () => $toCrm('reports.daily'))->name('daily-reports.show');
Route::get('commissions', fn () => $toCrm('opportunities.index', ['quick' => 'won_month']))->name('commissions.index');
Route::get('commissions/{employee}', fn () => $toCrm('opportunities.index', ['quick' => 'won_month']))->name('commissions.show')->whereNumber('employee');
Route::get('team-reports', fn () => $toCrm('reports.index'))->name('team-reports.index');
Route::get('team-reports/edit', fn () => $toCrm('reports.index'))->name('team-reports.edit');
Route::post('team-reports', [SalesManagerDailyReportController::class, 'storeTeamReport'])->name('team-reports.store');
