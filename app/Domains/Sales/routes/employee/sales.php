<?php

use App\Domains\Sales\Http\Controllers\Employee\SalesCommissionController;
use App\Domains\Sales\Http\Controllers\Employee\SalesCourseCatalogController;
use App\Domains\Sales\Http\Controllers\Employee\SalesDailyReportController;
use App\Domains\Sales\Http\Controllers\Employee\SalesGroupWhatsAppController;
use App\Domains\Sales\Http\Controllers\Employee\SalesKpiController;
use App\Domains\Sales\Http\Controllers\Employee\SalesLeadController;
use App\Domains\Sales\Http\Controllers\Employee\SalesLeadGroupController;
use App\Domains\Sales\Http\Controllers\Employee\SalesPenaltiesController;
use App\Domains\Sales\Http\Controllers\Employee\SalesReportController;
use App\Domains\Sales\Http\Controllers\Employee\SalesShiftController;
use Illuminate\Support\Facades\Route;

/**
 * بوابة موظف المبيعات.
 * القوائم/العرض الأساسية → CRM الموحّد.
 * باقي الأدوات التشغيلية تُعرض داخل CRM layout (تمت إعادة تسكين الـ Blades).
 */
$toCrm = fn (string $name, array $params = []) => redirect()->route('employee.crm.'.$name, $params);

Route::get('/', fn () => $toCrm('home'))->name('dashboard');
Route::get('kpi', [SalesKpiController::class, 'index'])->name('kpi.index');
Route::get('penalties', [SalesPenaltiesController::class, 'index'])->name('penalties.index');
Route::get('commissions', [SalesCommissionController::class, 'index'])->name('commissions.index');
Route::get('courses', [SalesCourseCatalogController::class, 'index'])->name('courses.index');
Route::get('reports', [SalesReportController::class, 'index'])->name('reports.index');
Route::get('daily-reports', [SalesDailyReportController::class, 'index'])->name('daily-reports.index');
Route::get('daily-reports/edit', [SalesDailyReportController::class, 'edit'])->name('daily-reports.edit');
Route::post('daily-reports', [SalesDailyReportController::class, 'store'])->name('daily-reports.store');
Route::post('daily-reports/sync-auto', [SalesDailyReportController::class, 'syncAuto'])->name('daily-reports.sync-auto');
Route::get('groups/{group}/print-pdf', [SalesLeadGroupController::class, 'printPdf'])->name('groups.print-pdf');
Route::resource('groups', SalesLeadGroupController::class)->except(['edit']);
Route::post('groups/{group}/whatsapp-bulk', [SalesGroupWhatsAppController::class, 'store'])->name('groups.whatsapp.store');
Route::get('groups/{group}/whatsapp-batches/{batch}', [SalesGroupWhatsAppController::class, 'showBatch'])->name('groups.whatsapp-batches.show');
Route::get('groups/{group}/whatsapp-batches/{batch}/status', [SalesGroupWhatsAppController::class, 'statusJson'])->name('groups.whatsapp-batches.status');
Route::post('leads/{lead}/activities', [SalesLeadController::class, 'storeActivity'])->name('leads.activities.store');
Route::post('leads/{lead}/pipeline', [SalesLeadController::class, 'advanceStage'])->name('leads.pipeline');
Route::post('leads/{lead}/quick-activity', [SalesLeadController::class, 'quickActivity'])->name('leads.quick-activity');
Route::post('leads/{lead}/next-follow', [SalesLeadController::class, 'setNextFollow'])->name('leads.next-follow');
Route::post('leads/{lead}/csat', [SalesLeadController::class, 'storeCsat'])->name('leads.csat.store');
Route::get('follow-ups', fn () => $toCrm('leads.index', ['quick' => 'today']))->name('follow-ups.index');
Route::get('shifts', [SalesShiftController::class, 'index'])->name('shifts.index');
Route::post('shifts/swap', [SalesShiftController::class, 'storeSwap'])->name('shifts.swap.store');
Route::delete('shifts/swap/{swap}', [SalesShiftController::class, 'cancelSwap'])->name('shifts.swap.cancel')->whereNumber('swap');

Route::get('leads', fn () => $toCrm('leads.index'))->name('leads.index');
Route::get('leads/create', fn () => $toCrm('leads.create'))->name('leads.create');
Route::post('leads', [SalesLeadController::class, 'store'])->name('leads.store');
Route::get('leads/{lead}', fn ($lead) => redirect()->route('employee.crm.leads.show', $lead))->name('leads.show');
Route::get('leads/{lead}/edit', fn ($lead) => redirect()->route('employee.crm.leads.edit', $lead))->name('leads.edit');
Route::put('leads/{lead}', [SalesLeadController::class, 'update'])->name('leads.update');
Route::patch('leads/{lead}', [SalesLeadController::class, 'update']);
Route::delete('leads/{lead}', [SalesLeadController::class, 'destroy'])->name('leads.destroy');
