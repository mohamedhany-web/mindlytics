<?php

use App\Domains\Messaging\Http\Controllers\Employee\SalesMetaSocialInboxController;
use Illuminate\Support\Facades\Route;

/**
 * Messenger & Instagram لموظفي/مديري المبيعات.
 * يُحمَّل من routes/web/employee.php داخل مجموعة employee وفي نفس موضعه القديم.
 */
Route::middleware('sales.staff')->prefix('sales/meta-social/inbox')->name('sales.meta-social.inbox.')->group(function () {
    Route::get('/', [SalesMetaSocialInboxController::class, 'index'])->name('index');
    Route::get('/poll', [SalesMetaSocialInboxController::class, 'poll'])->name('poll');
    Route::post('/{conversation}/reply', [SalesMetaSocialInboxController::class, 'reply'])->name('reply');
    Route::put('/{conversation}/messages/{message}', [SalesMetaSocialInboxController::class, 'updateMessage'])->name('messages.update');
    Route::delete('/{conversation}/messages/{message}', [SalesMetaSocialInboxController::class, 'destroyMessage'])->name('messages.destroy');
    Route::post('/{conversation}/assign', [SalesMetaSocialInboxController::class, 'assign'])->name('assign');
    Route::post('/{conversation}/contact', [SalesMetaSocialInboxController::class, 'updateContact'])->name('contact');
    Route::post('/{conversation}/create-lead', [SalesMetaSocialInboxController::class, 'createLead'])->name('create-lead');
    Route::post('/{conversation}/link-lead', [SalesMetaSocialInboxController::class, 'linkLead'])->name('link-lead');
    Route::post('/{conversation}/enrich', [SalesMetaSocialInboxController::class, 'enrich'])->name('enrich');
    Route::post('/{conversation}/request-phone', [SalesMetaSocialInboxController::class, 'requestPhone'])->name('request-phone');
    Route::post('/{conversation}/sync-messages', [SalesMetaSocialInboxController::class, 'syncMessages'])->name('sync-messages');
});
