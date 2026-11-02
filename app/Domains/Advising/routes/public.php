<?php

use App\Domains\Advising\Http\Controllers\Public\AdvisingController;
use Illuminate\Support\Facades\Route;

/**
 * حجز جلسة الإرشاد المهني (docs/ADVISING.md).
 * يُحمَّل من routes/web/auth.php.
 */
Route::prefix('advising')->name('advising.')->group(function () {
    Route::get('/', [AdvisingController::class, 'show'])->name('show');
    Route::get('slots', [AdvisingController::class, 'slots'])->middleware('throttle:60,1')->name('slots');
    Route::post('/', [AdvisingController::class, 'store'])->middleware('throttle:8,10')->name('store');
});
