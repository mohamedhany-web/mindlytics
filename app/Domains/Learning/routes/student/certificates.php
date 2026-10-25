<?php

use App\Domains\Learning\Http\Controllers\Student\CertificateController;
use Illuminate\Support\Facades\Route;

/**
 * شهادات الطالب.
 * يُحمَّل من داخل مجموعة student في routes/web/student-account.php وفي نفس موضعه القديم.
 */
Route::get('certificates/designs/{key}/preview', [CertificateController::class, 'designPreview'])
    ->name('certificates.design-preview');
Route::get('certificates/claim/{course}', [CertificateController::class, 'claim'])
    ->name('certificates.claim')
    ->whereNumber('course');
Route::post('certificates/claim/{course}', [CertificateController::class, 'storeClaim'])
    ->name('certificates.claim.store')
    ->whereNumber('course');
Route::resource('certificates', CertificateController::class)->only(['index', 'show']);
