<?php

use App\Domains\Accounting\Http\Controllers\Admin\CouponController;
use Illuminate\Support\Facades\Route;

/**
 * الكوبونات.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::resource('coupons', CouponController::class);
