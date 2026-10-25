<?php

use App\Domains\Accounting\Http\Controllers\Admin\WithdrawalRequestController;
use Illuminate\Support\Facades\Route;

/**
 * طلبات سحب المدربين.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
    Route::get('/', [WithdrawalRequestController::class, 'index'])->name('index');
    Route::get('/{withdrawal}', [WithdrawalRequestController::class, 'show'])->name('show');
    Route::post('/{withdrawal}/approve', [WithdrawalRequestController::class, 'approve'])
        ->middleware('throttle:10,1')
        ->name('approve');
    Route::post('/{withdrawal}/reject', [WithdrawalRequestController::class, 'reject'])
        ->middleware('throttle:10,1')
        ->name('reject');
    Route::post('/{withdrawal}/complete', [WithdrawalRequestController::class, 'complete'])
        ->middleware('throttle:10,1')
        ->name('complete');
});
