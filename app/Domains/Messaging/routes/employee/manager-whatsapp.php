<?php

use App\Domains\Messaging\Http\Controllers\Employee\SalesManagerWhatsAppInboxController;
use App\Domains\Messaging\Http\Controllers\Employee\SalesWhatsAppQueueController;
use Illuminate\Support\Facades\Route;

/**
 * طابور وصندوق واتساب مدير المبيعات.
 * يُحمَّل من routes/web/employee.php داخل مجموعة employee → مجموعة sales.manager (prefix: employee/sales-manager، name: employee.sales-manager.) وفي نفس موضعه القديم.
 */
Route::prefix('whatsapp/queue')->name('whatsapp.queue.')->group(function () {
    Route::get('/', [SalesWhatsAppQueueController::class, 'index'])->name('index');
    Route::get('/count', [SalesWhatsAppQueueController::class, 'count'])->name('count');
    Route::post('/{conversation}/assign', [SalesWhatsAppQueueController::class, 'assign'])->name('assign');
});

Route::prefix('whatsapp/inbox')->name('whatsapp.inbox.')->group(function () {
    Route::get('/', [SalesManagerWhatsAppInboxController::class, 'index'])->name('index');
    Route::get('/templates', [SalesManagerWhatsAppInboxController::class, 'templates'])->name('templates');
    Route::get('/suggested-templates', [SalesManagerWhatsAppInboxController::class, 'suggestedTemplates'])->name('suggested-templates');
    Route::get('/poll', [SalesManagerWhatsAppInboxController::class, 'poll'])->name('poll');
    Route::post('/start', [SalesManagerWhatsAppInboxController::class, 'start'])->name('start');
    Route::post('/{conversation}/reply', [SalesManagerWhatsAppInboxController::class, 'reply'])->name('reply');
    Route::post('/{conversation}/react', [SalesManagerWhatsAppInboxController::class, 'react'])->name('react');
    Route::post('/{conversation}/media', [SalesManagerWhatsAppInboxController::class, 'sendMedia'])->name('media-send');
    Route::get('/{conversation}/messages/{message}/media', [SalesManagerWhatsAppInboxController::class, 'messageMedia'])->name('media');
    Route::post('/{conversation}/template', [SalesManagerWhatsAppInboxController::class, 'sendTemplate'])->name('template');
    Route::post('/{conversation}/read', [SalesManagerWhatsAppInboxController::class, 'markRead'])->name('read');
    Route::post('/{conversation}/status', [SalesManagerWhatsAppInboxController::class, 'updateStatus'])->name('status');
    Route::post('/{conversation}/lead-stage', [SalesManagerWhatsAppInboxController::class, 'updateLeadStage'])->name('lead-stage');
    Route::post('/{conversation}/next-follow', [SalesManagerWhatsAppInboxController::class, 'setNextFollow'])->name('next-follow');
    Route::post('/{conversation}/notes', [SalesManagerWhatsAppInboxController::class, 'storeNote'])->name('notes');
    Route::post('/{conversation}/tags/{tag}', [SalesManagerWhatsAppInboxController::class, 'syncTag'])->name('tag');
    Route::get('/{conversation}', [SalesManagerWhatsAppInboxController::class, 'showConversation'])->name('conversation');
});
