<?php

use App\Domains\Messaging\Http\Controllers\Admin\ExcelWhatsAppController;
use App\Domains\Messaging\Http\Controllers\Admin\WhatsAppBatchController;
use App\Domains\Messaging\Http\Controllers\Admin\WhatsAppCloudController;
use App\Domains\Messaging\Http\Controllers\Admin\WhatsAppController;
use App\Domains\Messaging\Http\Controllers\Admin\WhatsAppInboxController;
use App\Domains\Messaging\Http\Controllers\Admin\WhatsAppReportController;
use App\Domains\Messaging\Http\Controllers\Admin\WhatsAppSuggestedTemplateController;
use App\Domains\Messaging\Http\Controllers\Admin\WhatsAppTemplateController;
use Illuminate\Support\Facades\Route;

/**
 * الواتساب — Meta Cloud API.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
    Route::get('/', [WhatsAppController::class, 'index'])->name('index');
    Route::get('/send', [WhatsAppController::class, 'sendForm'])->name('send');
    Route::post('/send', [WhatsAppController::class, 'sendMessage'])->name('send.post');
    Route::get('/excel-campaign', [ExcelWhatsAppController::class, 'index'])->name('excel-campaign');
    Route::get('/excel-campaign/sample', [ExcelWhatsAppController::class, 'downloadSample'])->name('excel-campaign.sample');
    Route::post('/excel-campaign/preview', [ExcelWhatsAppController::class, 'preview'])->name('excel-campaign.preview');
    Route::post('/excel-campaign/template', [ExcelWhatsAppController::class, 'createTemplate'])->name('excel-campaign.template');
    Route::post('/excel-campaign/sync', [ExcelWhatsAppController::class, 'syncTemplates'])->name('excel-campaign.sync');
    Route::post('/excel-campaign/send', [ExcelWhatsAppController::class, 'send'])->name('excel-campaign.send');
    Route::get('/messages', [WhatsAppController::class, 'messages'])->name('messages');
    Route::post('/messages/{message}/resend', [WhatsAppController::class, 'resendMessage'])->name('messages.resend');
    Route::get('/templates', [WhatsAppTemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/create', [WhatsAppTemplateController::class, 'create'])->name('templates.create');
    Route::get('/templates/workshop-preset/{workshop}', [WhatsAppTemplateController::class, 'workshopPreset'])->name('templates.workshop-preset');
    Route::post('/templates', [WhatsAppTemplateController::class, 'store'])->name('templates.store');
    Route::post('/templates/sync', [WhatsAppTemplateController::class, 'sync'])->name('templates.sync');
    Route::post('/templates/seed-suggested', [WhatsAppTemplateController::class, 'seedSuggestedLibrary'])->name('templates.seed-suggested');
    Route::get('/templates/suggested/{suggestedTemplate}/edit', [WhatsAppSuggestedTemplateController::class, 'edit'])->name('templates.suggested.edit');
    Route::put('/templates/suggested/{suggestedTemplate}', [WhatsAppSuggestedTemplateController::class, 'update'])->name('templates.suggested.update');
    Route::post('/templates/suggested/{suggestedTemplate}/meta-draft', [WhatsAppSuggestedTemplateController::class, 'createMetaDraft'])->name('templates.suggested.meta-draft');
    Route::post('/templates/suggested/{suggestedTemplate}/submit-meta', [WhatsAppSuggestedTemplateController::class, 'submitMeta'])->name('templates.suggested.submit-meta');
    Route::post('/templates/access-mode', [WhatsAppTemplateController::class, 'updateAccessMode'])->name('templates.access-mode');
    Route::get('/templates/{template}', [WhatsAppTemplateController::class, 'show'])->name('templates.show');
    Route::get('/templates/{template}/edit', [WhatsAppTemplateController::class, 'edit'])->name('templates.edit');
    Route::put('/templates/{template}', [WhatsAppTemplateController::class, 'update'])->name('templates.update');
    Route::put('/templates/{template}/display-name', [WhatsAppTemplateController::class, 'updateDisplayName'])->name('templates.display-name');
    Route::put('/templates/{template}/access', [WhatsAppTemplateController::class, 'updateAccess'])->name('templates.access');
    Route::post('/templates/{template}/submit', [WhatsAppTemplateController::class, 'submit'])->name('templates.submit');
    Route::post('/templates/{template}/duplicate', [WhatsAppTemplateController::class, 'duplicate'])->name('templates.duplicate');
    Route::delete('/templates/{template}', [WhatsAppTemplateController::class, 'destroy'])->name('templates.destroy');
    Route::get('/inbox', [WhatsAppInboxController::class, 'index'])->name('inbox');
    Route::get('/reports', [WhatsAppReportController::class, 'index'])->name('reports');
    Route::get('/inbox/templates', [WhatsAppInboxController::class, 'templates'])->name('inbox.templates');
    Route::get('/inbox/suggested-templates', [WhatsAppInboxController::class, 'suggestedTemplates'])->name('inbox.suggested-templates');
    Route::get('/inbox/poll', [WhatsAppInboxController::class, 'poll'])->name('inbox.poll');
    Route::post('/inbox/start', [WhatsAppInboxController::class, 'start'])->name('inbox.start');
    Route::post('/inbox/{conversation}/reply', [WhatsAppInboxController::class, 'reply'])->name('inbox.reply');
    Route::post('/inbox/{conversation}/react', [WhatsAppInboxController::class, 'react'])->name('inbox.react');
    Route::post('/inbox/{conversation}/media', [WhatsAppInboxController::class, 'sendMedia'])->name('inbox.media-send');
    Route::get('/inbox/{conversation}/messages/{message}/media', [WhatsAppInboxController::class, 'messageMedia'])->name('inbox.media');
    Route::post('/inbox/{conversation}/template', [WhatsAppInboxController::class, 'sendTemplate'])->name('inbox.template');
    Route::post('/inbox/{conversation}/read', [WhatsAppInboxController::class, 'markRead'])->name('inbox.read');
    Route::post('/inbox/{conversation}/status', [WhatsAppInboxController::class, 'updateStatus'])->name('inbox.status');
    Route::post('/inbox/{conversation}/lead-stage', [WhatsAppInboxController::class, 'updateLeadStage'])->name('inbox.lead-stage');
    Route::post('/inbox/{conversation}/next-follow', [WhatsAppInboxController::class, 'setNextFollow'])->name('inbox.next-follow');
    Route::post('/inbox/{conversation}/transfer', [WhatsAppInboxController::class, 'transfer'])->name('inbox.transfer');
    Route::post('/inbox/{conversation}/assign', [WhatsAppInboxController::class, 'assign'])->name('inbox.assign');
    Route::post('/inbox/{conversation}/notes', [WhatsAppInboxController::class, 'storeNote'])->name('inbox.notes');
    Route::post('/inbox/{conversation}/tags/{tag}', [WhatsAppInboxController::class, 'syncTag'])->name('inbox.tag');
    Route::get('/inbox/{conversation}', [WhatsAppInboxController::class, 'showConversation'])->name('inbox.conversation');
    Route::get('/settings', [WhatsAppController::class, 'settings'])->name('settings');
    Route::post('/settings', [WhatsAppCloudController::class, 'saveSettings'])->name('settings.update');
    Route::post('/test-connection', [WhatsAppCloudController::class, 'testConnection'])->name('test-connection');
    Route::post('/disconnect', [WhatsAppCloudController::class, 'disconnect'])->name('disconnect');
    Route::get('/status', [WhatsAppCloudController::class, 'statusJson'])->name('status');
    Route::post('/webhook/resubscribe', [WhatsAppCloudController::class, 'resubscribeWebhook'])->name('webhook.resubscribe');
    Route::post('/webhook/refresh', [WhatsAppCloudController::class, 'refreshWebhook'])->name('webhook.refresh');
    Route::get('/batches', [WhatsAppBatchController::class, 'index'])->name('batches.index');
    Route::get('/batches/{batch}', [WhatsAppBatchController::class, 'show'])->name('batches.show');
    Route::get('/batches/{batch}/status', [WhatsAppBatchController::class, 'statusJson'])->name('batches.status');
    Route::post('/batches/{batch}/process', [WhatsAppBatchController::class, 'process'])->name('batches.process');
    Route::post('/batches/{batch}/retry', [WhatsAppBatchController::class, 'retry'])->name('batches.retry');
    Route::post('/batches/{batch}/items/{item}/retry', [WhatsAppBatchController::class, 'retryItem'])->name('batches.items.retry');
    Route::post('/batches/{batch}/cancel', [WhatsAppBatchController::class, 'cancel'])->name('batches.cancel');
});
