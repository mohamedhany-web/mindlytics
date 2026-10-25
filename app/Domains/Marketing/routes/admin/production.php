<?php

use App\Domains\Marketing\Http\Controllers\Admin\DesignTaskCycleController;
use App\Domains\Marketing\Http\Controllers\Admin\MediaOrgChartController;
use App\Domains\Marketing\Http\Controllers\Admin\ModeratorMarketingPlanController;
use App\Domains\Marketing\Http\Controllers\Admin\ModeratorMontageRequestController;
use Illuminate\Support\Facades\Route;

/**
 * دورات التصميم، طلبات المونتاج، خطط تسويق المشرفين.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::get('design-task-cycles/performance-report', [DesignTaskCycleController::class, 'performanceReport'])->name('design-task-cycles.performance-report');
Route::get('design-task-cycles/performance-report/excel', [DesignTaskCycleController::class, 'performanceReportExcel'])->name('design-task-cycles.performance-report.excel');
Route::prefix('design-task-cycles')->name('design-task-cycles.')->group(function () {
    Route::get('/', [DesignTaskCycleController::class, 'index'])->name('index');
    Route::get('/create', [DesignTaskCycleController::class, 'create'])->name('create');
    Route::post('/', [DesignTaskCycleController::class, 'store'])->name('store');
    Route::get('{design_task_cycle}/edit', [DesignTaskCycleController::class, 'edit'])->name('edit');
    Route::put('{design_task_cycle}', [DesignTaskCycleController::class, 'update'])->name('update');
    Route::delete('{design_task_cycle}', [DesignTaskCycleController::class, 'destroy'])->name('destroy');
    Route::post('{design_task_cycle}/notes', [DesignTaskCycleController::class, 'updateNotes'])->name('notes.update');
    Route::post('{design_task_cycle}/cancel', [DesignTaskCycleController::class, 'cancel'])->name('cancel');
    Route::get('{design_task_cycle}', [DesignTaskCycleController::class, 'show'])->name('show');
});

Route::prefix('montage-requests')->name('montage-requests.')->group(function () {
    Route::get('/', [ModeratorMontageRequestController::class, 'index'])->name('index');
    Route::get('{montage_request}', [ModeratorMontageRequestController::class, 'show'])->name('show');
    Route::post('{montage_request}/cancel', [ModeratorMontageRequestController::class, 'cancel'])->name('cancel');
});

Route::get('media/org-chart', [MediaOrgChartController::class, 'index'])->name('media.org-chart.index');

Route::get('moderator-marketing-plans/settings', [ModeratorMarketingPlanController::class, 'settings'])->name('moderator-marketing-plans.settings');
Route::put('moderator-marketing-plans/settings', [ModeratorMarketingPlanController::class, 'updateSettings'])->name('moderator-marketing-plans.settings.update');
Route::prefix('moderator-marketing-plans')->name('moderator-marketing-plans.')->group(function () {
    Route::get('/', [ModeratorMarketingPlanController::class, 'index'])->name('index');
    Route::get('/create', [ModeratorMarketingPlanController::class, 'create'])->name('create');
    Route::post('/', [ModeratorMarketingPlanController::class, 'store'])->name('store');
    Route::get('{plan}/edit', [ModeratorMarketingPlanController::class, 'edit'])->name('edit');
    Route::put('{plan}', [ModeratorMarketingPlanController::class, 'update'])->name('update');
    Route::delete('{plan}', [ModeratorMarketingPlanController::class, 'destroy'])->name('destroy');
    Route::post('{plan}/platforms', [ModeratorMarketingPlanController::class, 'storePlatform'])->name('platforms.store');
    Route::put('{plan}/platforms/{platform}', [ModeratorMarketingPlanController::class, 'updatePlatform'])->name('platforms.update');
    Route::delete('{plan}/platforms/{platform}', [ModeratorMarketingPlanController::class, 'destroyPlatform'])->name('platforms.destroy');
    Route::post('{plan}/events', [ModeratorMarketingPlanController::class, 'storeEvent'])->name('events.store');
    Route::put('{plan}/events/{event}', [ModeratorMarketingPlanController::class, 'updateEvent'])->name('events.update');
    Route::delete('{plan}/events/{event}', [ModeratorMarketingPlanController::class, 'destroyEvent'])->name('events.destroy');
    Route::post('{plan}/events/{event}/confirm', [ModeratorMarketingPlanController::class, 'confirmEvent'])->name('events.confirm');
    Route::get('{plan}', [ModeratorMarketingPlanController::class, 'show'])->name('show');
});
