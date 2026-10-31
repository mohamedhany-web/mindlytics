<?php

use App\Domains\Messaging\Http\Controllers\Admin\MessagesController;
use App\Domains\Messaging\Http\Controllers\Admin\WhatsAppSettingsController;
use Illuminate\Support\Facades\Route;

/**
 * الرسائل والتقارير وإعدادات API الواتساب.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('messages')->name('messages.')->group(function () {
    Route::get('/', [MessagesController::class, 'index'])->name('index');
    Route::get('/create', [MessagesController::class, 'create'])->name('create');
    Route::post('/send-single', [MessagesController::class, 'sendSingle'])->name('send-single');
    Route::post('/send-bulk', [MessagesController::class, 'sendBulk'])->name('send-bulk');
    Route::get('/{message}', [MessagesController::class, 'show'])->name('show');
    Route::post('/{message}/resend', [MessagesController::class, 'resend'])->name('resend');
    Route::delete('/{message}', [MessagesController::class, 'destroy'])->name('destroy');
            
    // التقارير الشهرية
    Route::get('/monthly-reports', [MessagesController::class, 'monthlyReports'])->name('monthly-reports');
    Route::post('/generate-monthly-reports', [MessagesController::class, 'generateMonthlyReports'])->name('generate-monthly-reports');
            
    // قوالب الرسائل
    Route::get('/templates', [MessagesController::class, 'templates'])->name('templates');
    Route::post('/templates', [MessagesController::class, 'storeTemplate'])->name('templates.store');
    Route::delete('/templates/{template}', [MessagesController::class, 'destroyTemplate'])->name('templates.destroy');
            
    // إعدادات WhatsApp API
    Route::get('/settings', [WhatsAppSettingsController::class, 'settings'])->name('settings');
    Route::post('/save-api-settings', [WhatsAppSettingsController::class, 'saveApiSettings'])->name('save-api-settings');
    Route::post('/test-api', [WhatsAppSettingsController::class, 'testApi'])->name('test-api');
});
