<?php

use App\Domains\Messaging\Http\Controllers\Admin\WorkshopWhatsAppController;
use Illuminate\Support\Facades\Route;

/**
 * إرسال واتساب لمسجلي الورش (قوالب Meta).
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 * يقع بين routes الورش (admin/workshops/*).
 */
Route::post('workshops/{workshop}/whatsapp-bulk', [WorkshopWhatsAppController::class, 'store'])
    ->name('workshops.whatsapp-bulk');
Route::get('workshops/{workshop}/whatsapp-template/create', [WorkshopWhatsAppController::class, 'createTemplateForm'])
    ->name('workshops.whatsapp-template.create.form');
Route::post('workshops/{workshop}/whatsapp-template/create', [WorkshopWhatsAppController::class, 'createTemplate'])
    ->name('workshops.whatsapp-template.create');
Route::post('workshops/{workshop}/whatsapp-template/sync', [WorkshopWhatsAppController::class, 'syncTemplate'])
    ->name('workshops.whatsapp-template.sync');
Route::post('workshops/{workshop}/whatsapp-template/send', [WorkshopWhatsAppController::class, 'sendTemplate'])
    ->name('workshops.whatsapp-template.send');
