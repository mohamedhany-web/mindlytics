<?php

use App\Domains\Cms\Http\Controllers\Admin\SupportTicketsController;
use Illuminate\Support\Facades\Route;

/**
 * تذاكر الدعم.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('support-tickets')->name('support-tickets.')->group(function () {
    Route::get('/', [SupportTicketsController::class, 'index'])->name('index');
    Route::get('/{ticket}', [SupportTicketsController::class, 'show'])->name('show');
    Route::post('/{ticket}/reply', [SupportTicketsController::class, 'reply'])->name('reply');
    Route::post('/{ticket}/close', [SupportTicketsController::class, 'close'])->name('close');
});
