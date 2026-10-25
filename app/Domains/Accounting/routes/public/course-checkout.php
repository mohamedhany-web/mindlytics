<?php

use App\Domains\Accounting\Http\Controllers\Public\CheckoutController;
use App\Domains\Accounting\Http\Controllers\Public\FawaterkPluginController;
use Illuminate\Support\Facades\Route;

/**
 * Checkout الكورسات: الصفحة، التسجيل السريع، الإتمام، كاشير، فواتيرك (plugin + prepare + return)، التسجيل المجاني.
 * ⚠️ checkout/fawaterak/{status} و js/checkout-pay-widget.v1.js مسجّلة عند Fawaterak – لا تتغير.
 * يُحمَّل من routes/web/public.php في نفس موضعه القديم.
 */
Route::get('/course/{courseId}/checkout', [CheckoutController::class, 'show'])
    ->name('public.course.checkout');

Route::post('/course/{courseId}/checkout/quick-register', [CheckoutController::class, 'quickRegister'])
    ->name('public.course.checkout.quick-register');

Route::post('/course/{courseId}/checkout/complete', [CheckoutController::class, 'complete'])
    ->middleware('auth')
    ->name('public.course.checkout.complete');

// التوجيه لبوابة الدفع كاشير (كورس)
Route::post('/course/{courseId}/checkout/kashier', [CheckoutController::class, 'redirectToKashier'])
    ->middleware('auth')
    ->name('public.course.checkout.kashier');

// فواتيرك (iframe): تجهيز الإضافة + بروكسي السكربت
Route::get('/js/checkout-pay-widget.v1.js', FawaterkPluginController::class)
    ->name('public.fawaterk.plugin');
Route::post('/course/{courseId}/checkout/fawaterak/prepare', [CheckoutController::class, 'fawaterakPrepare'])
    ->middleware('auth')
    ->name('public.course.checkout.fawaterak.prepare');
Route::get('/checkout/fawaterak/{status}', [CheckoutController::class, 'fawaterakReturn'])
    ->middleware('auth')
    ->where('status', 'success|fail|pending')
    ->name('public.checkout.fawaterak.return');

// تسجيل مجاني للكورسات المجانية
Route::post('/course/{courseId}/enroll-free', [CheckoutController::class, 'enrollFree'])
    ->middleware('auth')
    ->name('public.course.enroll.free');
