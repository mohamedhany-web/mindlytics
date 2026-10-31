<?php

use App\Domains\Offline\Http\Controllers\Admin\WorkshopController;
use Illuminate\Support\Facades\Route;

/**
 * الورش: التأكيدات، التفعيل، CRUD، التصدير، إرسال القبول.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 * (routes واتساب الورش من Messaging تأتي بعده مباشرة، ثم admin/workshops-checkin.php)
 */
Route::get('workshops/{workshop}/confirmations', [WorkshopController::class, 'confirmations'])
    ->name('workshops.confirmations');
Route::post('workshops/{workshop}/deactivate', [WorkshopController::class, 'deactivate'])
    ->name('workshops.deactivate');
Route::post('workshops/{workshop}/activate', [WorkshopController::class, 'activate'])
    ->name('workshops.activate');
Route::resource('workshops', WorkshopController::class);
Route::get('workshops/{workshop}/export', [WorkshopController::class, 'exportRegistrations'])
    ->name('workshops.export');
Route::post('workshops/{workshop}/send-acceptance', [WorkshopController::class, 'sendAcceptanceEmails'])
    ->name('workshops.send-acceptance');
