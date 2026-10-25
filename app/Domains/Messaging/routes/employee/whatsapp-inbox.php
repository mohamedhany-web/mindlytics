<?php

use App\Domains\Messaging\Http\Controllers\Employee\SalesWhatsAppInboxController;
use Illuminate\Support\Facades\Route;

/**
 * صندوق واتساب موظف المبيعات.
 * يُحمَّل من routes/web/employee.php داخل مجموعة employee → مجموعة sales.employee (prefix: employee/sales، name: employee.sales.) وفي نفس موضعه القديم.
 */
Route::prefix('whatsapp/inbox')->name('whatsapp.inbox.')->group(function () {
    Route::get('/', [SalesWhatsAppInboxController::class, 'index'])->name('index');
    Route::get('/templates', [SalesWhatsAppInboxController::class, 'templates'])->name('templates');
    Route::get('/suggested-templates', [SalesWhatsAppInboxController::class, 'suggestedTemplates'])->name('suggested-templates');
    Route::get('/poll', [SalesWhatsAppInboxController::class, 'poll'])->name('poll');
    Route::post('/start', [SalesWhatsAppInboxController::class, 'start'])->name('start');
    Route::post('/{conversation}/reply', [SalesWhatsAppInboxController::class, 'reply'])->name('reply');
    Route::post('/{conversation}/react', [SalesWhatsAppInboxController::class, 'react'])->name('react');
    Route::post('/{conversation}/media', [SalesWhatsAppInboxController::class, 'sendMedia'])->name('media-send');
    Route::get('/{conversation}/messages/{message}/media', [SalesWhatsAppInboxController::class, 'messageMedia'])->name('media');
    Route::post('/{conversation}/template', [SalesWhatsAppInboxController::class, 'sendTemplate'])->name('template');
    Route::post('/{conversation}/read', [SalesWhatsAppInboxController::class, 'markRead'])->name('read');
    Route::post('/{conversation}/status', [SalesWhatsAppInboxController::class, 'updateStatus'])->name('status');
    Route::post('/{conversation}/lead-stage', [SalesWhatsAppInboxController::class, 'updateLeadStage'])->name('lead-stage');
    Route::post('/{conversation}/next-follow', [SalesWhatsAppInboxController::class, 'setNextFollow'])->name('next-follow');
    Route::post('/{conversation}/notes', [SalesWhatsAppInboxController::class, 'storeNote'])->name('notes');
    Route::post('/{conversation}/tags/{tag}', [SalesWhatsAppInboxController::class, 'syncTag'])->name('tag');
    Route::get('/{conversation}', [SalesWhatsAppInboxController::class, 'showConversation'])->name('conversation');
});
