<?php

use App\Domains\Marketing\Http\Controllers\Employee\DesignTaskCycleController;
use App\Domains\Marketing\Http\Controllers\Employee\ModeratorMarketingPlanController;
use App\Domains\Marketing\Http\Controllers\Employee\ModeratorMontageRequestController;
use Illuminate\Support\Facades\Route;

/**
 * دورات التصميم وخطط تسويق المشرفين وطلبات المونتاج (moderator.employee).
 * يُحمَّل من داخل مجموعة employee في routes/web/employee.php (auth + employee.work) وفي نفس موضعه القديم.
 */
Route::middleware('moderator.employee')->prefix('design-cycles')->name('design-cycles.')->group(function () {
    Route::get('/', [DesignTaskCycleController::class, 'index'])->name('index');
    Route::get('/create', [DesignTaskCycleController::class, 'create'])->name('create');
    Route::post('/', [DesignTaskCycleController::class, 'store'])->name('store');
    Route::post('/{design_task_cycle}/planner-items', [DesignTaskCycleController::class, 'storePlannerItem'])->name('planner-items.store');
    Route::patch('/{design_task_cycle}/planner-items/{planner_item}', [DesignTaskCycleController::class, 'updatePlannerItem'])->name('planner-items.update');
    Route::delete('/{design_task_cycle}/planner-items/{planner_item}', [DesignTaskCycleController::class, 'destroyPlannerItem'])->name('planner-items.destroy');
    Route::get('/{design_task_cycle}', [DesignTaskCycleController::class, 'show'])->name('show');
    Route::post('/{design_task_cycle}/moderator-delivery', [DesignTaskCycleController::class, 'storeModeratorDelivery'])->name('moderator-delivery.store');
    Route::post('/{design_task_cycle}/cancel', [DesignTaskCycleController::class, 'cancel'])->name('cancel');
});

Route::middleware('moderator.employee')->prefix('montage-requests')->name('montage-requests.')->group(function () {
    Route::get('/', [ModeratorMontageRequestController::class, 'index'])->name('index');
    Route::get('/create', [ModeratorMontageRequestController::class, 'create'])->name('create');
    Route::post('/', [ModeratorMontageRequestController::class, 'store'])->name('store');
    Route::get('/{montage_request}', [ModeratorMontageRequestController::class, 'show'])->name('show');
    Route::post('/{montage_request}/moderator-delivery', [ModeratorMontageRequestController::class, 'storeModeratorDelivery'])->name('moderator-delivery.store');
    Route::post('/{montage_request}/cancel', [ModeratorMontageRequestController::class, 'cancel'])->name('cancel');
});

Route::middleware('moderator.employee')->prefix('marketing-plans')->name('marketing-plans.')->group(function () {
    Route::get('/', [ModeratorMarketingPlanController::class, 'index'])->name('index');
    Route::get('/create', [ModeratorMarketingPlanController::class, 'create'])->name('create');
    Route::post('/', [ModeratorMarketingPlanController::class, 'store'])->name('store');
    Route::get('/{marketing_plan}', [ModeratorMarketingPlanController::class, 'show'])->name('show');
    Route::get('/{marketing_plan}/edit', [ModeratorMarketingPlanController::class, 'edit'])->name('edit');
    Route::put('/{marketing_plan}', [ModeratorMarketingPlanController::class, 'update'])->name('update');
    Route::delete('/{marketing_plan}', [ModeratorMarketingPlanController::class, 'destroy'])->name('destroy');
    Route::post('/{marketing_plan}/platforms', [ModeratorMarketingPlanController::class, 'storePlatform'])->name('platforms.store');
    Route::put('/{marketing_plan}/platforms/{platform}', [ModeratorMarketingPlanController::class, 'updatePlatform'])->name('platforms.update');
    Route::delete('/{marketing_plan}/platforms/{platform}', [ModeratorMarketingPlanController::class, 'destroyPlatform'])->name('platforms.destroy');
    Route::post('/{marketing_plan}/events', [ModeratorMarketingPlanController::class, 'storeEvent'])->name('events.store');
    Route::put('/{marketing_plan}/events/{event}', [ModeratorMarketingPlanController::class, 'updateEvent'])->name('events.update');
    Route::delete('/{marketing_plan}/events/{event}', [ModeratorMarketingPlanController::class, 'destroyEvent'])->name('events.destroy');
});
