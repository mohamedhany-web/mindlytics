<?php

use App\Domains\Messaging\Http\Controllers\Admin\MetaSocialAgentLinkController;
use App\Domains\Messaging\Http\Controllers\Admin\MetaSocialController;
use App\Domains\Messaging\Http\Controllers\Admin\MetaSocialInboxController;
use App\Domains\Messaging\Http\Controllers\Admin\MetaSocialLeadCenterController;
use App\Domains\Messaging\Http\Controllers\Admin\MetaSocialOAuthController;
use App\Domains\Messaging\Http\Controllers\Admin\MetaSocialPageController;
use App\Domains\Messaging\Http\Controllers\Admin\MetaSocialSettingsController;
use Illuminate\Support\Facades\Route;

/**
 * السوشيال — Meta Graph (Facebook Pages + Messenger + Instagram).
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('meta-social')->name('meta-social.')->group(function () {
    Route::get('/', [MetaSocialController::class, 'index'])->name('index');
    Route::get('/settings', [MetaSocialSettingsController::class, 'edit'])->name('settings');
    Route::post('/settings', [MetaSocialSettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/webhook-sync', [MetaSocialSettingsController::class, 'syncWebhook'])->name('settings.webhook-sync');
    Route::get('/settings/test', [MetaSocialSettingsController::class, 'testConnection'])->name('settings.test');
    Route::get('/oauth/redirect', [MetaSocialOAuthController::class, 'redirect'])->name('oauth.redirect');
    Route::get('/oauth/callback', [MetaSocialOAuthController::class, 'callback'])->name('oauth.callback');
    Route::post('/oauth/disconnect', [MetaSocialOAuthController::class, 'disconnect'])->name('oauth.disconnect');
    Route::get('/pages', [MetaSocialPageController::class, 'index'])->name('pages.index');
    Route::post('/pages/sync', [MetaSocialPageController::class, 'sync'])->name('pages.sync');
    Route::post('/pages/bulk-activate', [MetaSocialPageController::class, 'bulkActivate'])->name('pages.bulk-activate');
    Route::post('/pages/bulk-deactivate', [MetaSocialPageController::class, 'bulkDeactivate'])->name('pages.bulk-deactivate');
    Route::post('/pages/activate-all', [MetaSocialPageController::class, 'activateAll'])->name('pages.activate-all');
    Route::post('/pages/{page}/activate', [MetaSocialPageController::class, 'activate'])->name('pages.activate');
    Route::post('/pages/{page}/deactivate', [MetaSocialPageController::class, 'deactivate'])->name('pages.deactivate');
    Route::post('/pages/{page}/sync-conversations', [MetaSocialPageController::class, 'syncConversations'])->name('pages.sync-conversations');
    Route::get('/agents', [MetaSocialAgentLinkController::class, 'index'])->name('agents.index');
    Route::post('/agents/sync', [MetaSocialAgentLinkController::class, 'sync'])->name('agents.sync');
    Route::post('/agents', [MetaSocialAgentLinkController::class, 'store'])->name('agents.store');
    Route::put('/agents/{agent}', [MetaSocialAgentLinkController::class, 'update'])->name('agents.update');
    Route::get('/inbox', [MetaSocialInboxController::class, 'index'])->name('inbox.index');
    Route::get('/inbox/poll', [MetaSocialInboxController::class, 'poll'])->name('inbox.poll');
    Route::post('/inbox/{conversation}/reply', [MetaSocialInboxController::class, 'reply'])->name('inbox.reply');
    Route::put('/inbox/{conversation}/messages/{message}', [MetaSocialInboxController::class, 'updateMessage'])->name('inbox.messages.update');
    Route::delete('/inbox/{conversation}/messages/{message}', [MetaSocialInboxController::class, 'destroyMessage'])->name('inbox.messages.destroy');
    Route::post('/inbox/{conversation}/assign', [MetaSocialInboxController::class, 'assign'])->name('inbox.assign');
    Route::post('/inbox/{conversation}/contact', [MetaSocialInboxController::class, 'updateContact'])->name('inbox.contact');
    Route::post('/inbox/{conversation}/create-lead', [MetaSocialInboxController::class, 'createLead'])->name('inbox.create-lead');
    Route::post('/inbox/{conversation}/link-lead', [MetaSocialInboxController::class, 'linkLead'])->name('inbox.link-lead');
    Route::post('/inbox/{conversation}/enrich', [MetaSocialInboxController::class, 'enrich'])->name('inbox.enrich');
    Route::post('/inbox/{conversation}/request-phone', [MetaSocialInboxController::class, 'requestPhone'])->name('inbox.request-phone');
    Route::post('/inbox/{conversation}/sync-messages', [MetaSocialInboxController::class, 'syncMessages'])->name('inbox.sync-messages');
    Route::post('/inbox/{conversation}/lead-stage', [MetaSocialInboxController::class, 'updateLeadStage'])->name('inbox.lead-stage');

    Route::get('/leads', [MetaSocialLeadCenterController::class, 'index'])->name('leads.index');
    Route::get('/leads/poll', [MetaSocialLeadCenterController::class, 'poll'])->name('leads.poll');
    Route::get('/leads/export', [MetaSocialLeadCenterController::class, 'export'])->name('leads.export');
    Route::post('/leads/bulk', [MetaSocialLeadCenterController::class, 'bulk'])->name('leads.bulk');
    Route::post('/leads/{conversation}/create-lead', [MetaSocialLeadCenterController::class, 'createLead'])->name('leads.create');
    Route::post('/leads/{conversation}/assign', [MetaSocialLeadCenterController::class, 'assign'])->name('leads.assign');
    Route::post('/leads/{conversation}/contact', [MetaSocialLeadCenterController::class, 'updateContact'])->name('leads.contact');
    Route::post('/leads/{conversation}/stage', [MetaSocialLeadCenterController::class, 'updateStage'])->name('leads.stage');
    Route::post('/leads/{conversation}/priority', [MetaSocialLeadCenterController::class, 'updatePriority'])->name('leads.priority');
    Route::post('/leads/{conversation}/reminder', [MetaSocialLeadCenterController::class, 'updateReminder'])->name('leads.reminder');
    Route::post('/leads/{conversation}/labels', [MetaSocialLeadCenterController::class, 'updateLabels'])->name('leads.labels');
    Route::post('/leads/{conversation}/done', [MetaSocialLeadCenterController::class, 'markDone'])->name('leads.done');
    Route::post('/leads/{conversation}/request-phone', [MetaSocialLeadCenterController::class, 'requestPhone'])->name('leads.request-phone');
    Route::post('/leads/{conversation}/reply', [MetaSocialLeadCenterController::class, 'reply'])->name('leads.reply');
});
