<?php

use App\Domains\Messaging\Http\Controllers\Webhooks\MetaSocialWebhookController;
use App\Domains\Messaging\Http\Controllers\Webhooks\WhatsAppWebhookController;
use App\Http\Middleware\CheckActiveStatus;
use App\Http\Middleware\EnsureTwoFactorEnabled;
use App\Http\Middleware\FileUploadSecurityMiddleware;
use App\Http\Middleware\InputSanitizationMiddleware;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

/**
 * Webhooks الواتساب و Meta Social.
 * ⚠️ الروابط مسجّلة عند Meta ومستثناة من CSRF في bootstrap/app.php – لا تتغير.
 * يُحمَّل من routes/web/infrastructure.php في نفس موضعه القديم (قبل باقي الـ routes).
 */
Route::get('/webhooks/whatsapp', [WhatsAppWebhookController::class, 'verify'])
    ->name('webhooks.whatsapp.verify')
    ->withoutMiddleware([
        SetLocale::class,
        InputSanitizationMiddleware::class,
        FileUploadSecurityMiddleware::class,
        EnsureTwoFactorEnabled::class,
        CheckActiveStatus::class,
    ]);
Route::post('/webhooks/whatsapp', [WhatsAppWebhookController::class, 'handle'])
    ->name('webhooks.whatsapp.handle')
    ->withoutMiddleware([
        SetLocale::class,
        InputSanitizationMiddleware::class,
        FileUploadSecurityMiddleware::class,
        EnsureTwoFactorEnabled::class,
        CheckActiveStatus::class,
    ]);

Route::get('/webhooks/meta-social', [MetaSocialWebhookController::class, 'verify'])
    ->name('webhooks.meta-social.verify')
    ->withoutMiddleware([
        SetLocale::class,
        InputSanitizationMiddleware::class,
        FileUploadSecurityMiddleware::class,
        EnsureTwoFactorEnabled::class,
        CheckActiveStatus::class,
    ]);
Route::post('/webhooks/meta-social', [MetaSocialWebhookController::class, 'handle'])
    ->name('webhooks.meta-social.handle')
    ->withoutMiddleware([
        SetLocale::class,
        InputSanitizationMiddleware::class,
        FileUploadSecurityMiddleware::class,
        EnsureTwoFactorEnabled::class,
        CheckActiveStatus::class,
    ]);
