<?php

use App\Domains\Accounting\Http\Controllers\Public\CheckoutController;
use Illuminate\Support\Facades\Route;

/**
 * Checkout المسارات التعليمية.
 * يُحمَّل من routes/web/public.php في نفس موضعه القديم.
 */
Route::get('/learning-path/{slug}/checkout', [CheckoutController::class, 'showLearningPath'])
    ->middleware('auth')
    ->name('public.learning-path.checkout');

Route::post('/learning-path/{slug}/checkout/complete', [CheckoutController::class, 'completeLearningPath'])
    ->middleware('auth')
    ->name('public.learning-path.checkout.complete');

// التوجيه لبوابة الدفع كاشير (مسار تعليمي)
Route::post('/learning-path/{slug}/checkout/kashier', [CheckoutController::class, 'redirectToKashierLearningPath'])
    ->middleware('auth')
    ->name('public.learning-path.checkout.kashier');
