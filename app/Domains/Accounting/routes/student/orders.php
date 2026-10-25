<?php

use App\Domains\Accounting\Http\Controllers\Student\OrderController;
use Illuminate\Support\Facades\Route;

/**
 * طلبات الطالب (role:student).
 * يُحمَّل من routes/web/student.php وفي نفس موضعه القديم.
 */
Route::middleware(['role:student'])->group(function () {
    Route::post('/courses/{advancedCourse}/order', [OrderController::class, 'store'])->name('courses.order');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->middleware(['ownership:order,order'])
        ->name('orders.show');
});
