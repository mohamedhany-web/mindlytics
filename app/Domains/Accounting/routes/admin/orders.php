<?php

use App\Domains\Accounting\Http\Controllers\Admin\OrderController;
use Illuminate\Support\Facades\Route;

/**
 * إدارة الطلبات (اعتماد/رفض).
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::post('/orders/{order}/approve', [OrderController::class, 'approve'])
    ->middleware('throttle:10,1')
    ->name('orders.approve');
Route::post('/orders/{order}/reject', [OrderController::class, 'reject'])
    ->middleware('throttle:10,1')
    ->name('orders.reject');
