<?php

use App\Domains\Hr\Http\Controllers\Admin\AdminLeaveController;
use Illuminate\Support\Facades\Route;

/**
 * إدارة الإجازات.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::get('/leaves', [AdminLeaveController::class, 'index'])->name('leaves.index');
Route::get('/leaves/{leave}', [AdminLeaveController::class, 'show'])->name('leaves.show');
Route::post('/leaves/{leave}/approve', [AdminLeaveController::class, 'approve'])->name('leaves.approve');
Route::post('/leaves/{leave}/reject', [AdminLeaveController::class, 'reject'])->name('leaves.reject');
