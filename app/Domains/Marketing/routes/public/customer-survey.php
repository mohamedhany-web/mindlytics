<?php

use App\Domains\Marketing\Http\Controllers\Public\CustomerSurveyController;
use Illuminate\Support\Facades\Route;

/**
 * استبيان عملاء الأكاديمية.
 * يُحمَّل من routes/web/public.php في نفس موضعه القديم.
 */
Route::get('/customer-survey', [CustomerSurveyController::class, 'show'])
    ->name('public.customer-survey.show');
Route::post('/customer-survey/lookup', [CustomerSurveyController::class, 'lookup'])
    ->middleware('throttle:12,1')
    ->name('public.customer-survey.lookup');
Route::post('/customer-survey', [CustomerSurveyController::class, 'store'])
    ->middleware('throttle:8,1')
    ->name('public.customer-survey.store');
