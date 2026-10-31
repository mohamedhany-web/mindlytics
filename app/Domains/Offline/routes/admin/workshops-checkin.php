<?php

use App\Domains\Offline\Http\Controllers\Admin\WorkshopController;
use Illuminate\Support\Facades\Route;

/**
 * تسجيل حضور الورشة وتحويل المسجلين لعملاء محتملين.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::post('workshops/{workshop}/checkin', [WorkshopController::class, 'checkin'])
    ->name('workshops.checkin');
Route::post('workshops/{workshop}/convert-to-leads', [WorkshopController::class, 'convertRegistrationsToLeads'])
    ->name('workshops.convert-to-leads');
