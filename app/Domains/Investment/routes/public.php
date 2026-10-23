<?php

use App\Domains\Investment\Http\Controllers\Public\InvestmentController;
use Illuminate\Support\Facades\Route;

/**
 * صفحات الاستثمار العامة.
 * يُحمَّل بـ require من ملف routes الويب العام في نفس موضعه القديم (ترتيب الـ routes جزء من المطابقة).
 */
Route::prefix('investment')->name('investment.')->group(function () {
    Route::get('/', [InvestmentController::class, 'index'])->name('index');
    Route::get('/{slug}', [InvestmentController::class, 'show'])->name('show');
    Route::post('/{slug}/apply', [InvestmentController::class, 'apply'])->name('apply');
});
