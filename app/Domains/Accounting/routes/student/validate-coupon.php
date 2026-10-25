<?php

use App\Domains\Accounting\Http\Controllers\Student\CouponController;
use Illuminate\Support\Facades\Route;

/**
 * API التحقق من الكوبون.
 * يُحمَّل من routes/web/student.php وفي نفس موضعه القديم.
 */
Route::post('/api/validate-coupon', [CouponController::class, 'validateCoupon'])->name('api.validate-coupon');
