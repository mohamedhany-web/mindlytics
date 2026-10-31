<?php

use App\Domains\Marketing\Http\Controllers\Admin\LoyaltyController;
use App\Domains\Marketing\Http\Controllers\Admin\ReferralController;
use Illuminate\Support\Facades\Route;

/**
 * الإحالات وبرامج الولاء.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('referrals')->name('referrals.')->group(function () {
    Route::get('/', [ReferralController::class, 'index'])->name('index');
    Route::get('/{referral}', [ReferralController::class, 'show'])->name('show');
});
Route::prefix('loyalty')->name('loyalty.')->group(function () {
    Route::get('/', [LoyaltyController::class, 'index'])->name('index');
    Route::post('/', [LoyaltyController::class, 'store'])->name('store');
    Route::get('/{loyaltyProgram}', [LoyaltyController::class, 'show'])->name('show');
    Route::put('/{loyaltyProgram}', [LoyaltyController::class, 'update'])->name('update');
});
