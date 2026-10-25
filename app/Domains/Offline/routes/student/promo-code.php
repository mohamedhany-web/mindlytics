<?php

use App\Domains\Offline\Http\Controllers\Student\WorkshopPromoController;
use Illuminate\Support\Facades\Route;

/**
 * تفعيل كود خصم الورشة.
 * يُحمَّل من routes/web/student.php وفي نفس موضعه القديم.
 */
Route::post('/promo-code/activate', [WorkshopPromoController::class, 'activate'])
    ->middleware('auth')
    ->name('promo-code.activate');
