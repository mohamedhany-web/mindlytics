<?php

use App\Domains\Sales\Http\Controllers\Employee\SalesTeamChatController;
use Illuminate\Support\Facades\Route;

/**
 * شات فريق المبيعات.
 * يُحمَّل من routes/web/employee.php داخل مجموعة employee (auth + employee.work) وفي نفس موضعه القديم.
 */
Route::middleware('sales.staff')->prefix('team-chat')->name('team-chat.')->group(function () {
    Route::get('/bootstrap', [SalesTeamChatController::class, 'bootstrap'])->name('bootstrap');
    Route::get('/conversations', [SalesTeamChatController::class, 'conversations'])->name('conversations');
    Route::get('/unread', [SalesTeamChatController::class, 'unread'])->name('unread');
    Route::post('/conversations/team', [SalesTeamChatController::class, 'openTeam'])->name('conversations.team');
    Route::post('/conversations/direct', [SalesTeamChatController::class, 'openDirect'])->name('conversations.direct');
    Route::get('/conversations/{conversation}/messages', [SalesTeamChatController::class, 'messages'])->name('messages');
    Route::post('/conversations/{conversation}/messages', [SalesTeamChatController::class, 'send'])->name('messages.send');
    Route::post('/conversations/{conversation}/read', [SalesTeamChatController::class, 'read'])->name('read');
    Route::post('/messages/{message}/reactions', [SalesTeamChatController::class, 'react'])->name('reactions');
    Route::delete('/messages/{message}', [SalesTeamChatController::class, 'destroyMessage'])->name('messages.destroy');
});
