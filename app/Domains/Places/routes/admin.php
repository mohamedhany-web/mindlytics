<?php

use App\Domains\Places\Http\Controllers\Admin\PlaceDailyExpenseController;
use App\Domains\Places\Http\Controllers\Admin\PlaceSettlementController;
use App\Domains\Places\Http\Controllers\Admin\PlaceUsageLogController;
use Illuminate\Support\Facades\Route;

/**
 * اعتماد سجلات الاستخدام والمصروفات اليومية والتسويات الشهرية للأماكن.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::get('place-usage-logs', [PlaceUsageLogController::class, 'index'])->name('place-usage-logs.index');
Route::post('place-usage-logs/{placeUsageLog}/approve', [PlaceUsageLogController::class, 'approve'])->name('place-usage-logs.approve');
Route::post('place-usage-logs/{placeUsageLog}/reject', [PlaceUsageLogController::class, 'reject'])->name('place-usage-logs.reject');
Route::post('place-daily-expenses/{placeDailyExpense}/approve', [PlaceDailyExpenseController::class, 'approve'])->name('place-daily-expenses.approve');
Route::post('place-daily-expenses/{placeDailyExpense}/reject', [PlaceDailyExpenseController::class, 'reject'])->name('place-daily-expenses.reject');

Route::get('place-settlements', [PlaceSettlementController::class, 'index'])->name('place-settlements.index');
Route::get('place-settlements/{placeSettlement}', [PlaceSettlementController::class, 'show'])->name('place-settlements.show');
Route::post('place-settlements/{placeSettlement}/approve', [PlaceSettlementController::class, 'approve'])->name('place-settlements.approve');
Route::post('place-settlements/{placeSettlement}/close', [PlaceSettlementController::class, 'close'])->name('place-settlements.close');
