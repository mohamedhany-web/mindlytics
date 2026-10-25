<?php

use App\Domains\Cms\Http\Controllers\Admin\ContactMessageController;
use Illuminate\Support\Facades\Route;

/**
 * رسائل التواصل.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::resource('contact-messages', ContactMessageController::class)->only(['index', 'show', 'destroy']);
Route::post('/contact-messages/{contactMessage}/mark-as-read', [ContactMessageController::class, 'markAsRead'])->name('contact-messages.mark-as-read');
Route::post('/contact-messages/{contactMessage}/mark-as-unread', [ContactMessageController::class, 'markAsUnread'])->name('contact-messages.mark-as-unread');
