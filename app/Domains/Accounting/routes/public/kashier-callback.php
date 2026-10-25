<?php

use App\Domains\Accounting\Http\Controllers\Public\CheckoutController;
use Illuminate\Support\Facades\Route;

/**
 * عودة كاشير بعد الدفع + التسجيل المجاني للمسارات.
 * ⚠️ checkout/kashier/callback مسجّل عند Kashier – لا يتغير.
 * يُحمَّل من routes/web/public.php في نفس موضعه القديم.
 */
Route::get('/checkout/kashier/callback', [CheckoutController::class, 'kashierCallback'])
    ->name('public.checkout.kashier.callback');

// تسجيل مجاني للمسارات المجانية
Route::post('/learning-path/{slug}/enroll-free', [CheckoutController::class, 'enrollFreeLearningPath'])
    ->middleware('auth')
    ->name('public.learning-path.enroll.free');
