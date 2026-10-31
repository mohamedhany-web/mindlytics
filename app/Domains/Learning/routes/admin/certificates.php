<?php

use App\Domains\Learning\Http\Controllers\Admin\CertificateController;
use Illuminate\Support\Facades\Route;

/**
 * الشهادات وتصميمها.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::get('certificates/designs/{key}/preview', [CertificateController::class, 'designPreview'])
    ->name('certificates.design-preview');
Route::resource('certificates', CertificateController::class);
Route::get('certificates-branding', [CertificateController::class, 'branding'])->name('certificates.branding');
Route::put('certificates-branding', [CertificateController::class, 'updateBranding'])->name('certificates.branding.update');
