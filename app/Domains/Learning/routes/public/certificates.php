<?php

use App\Domains\Learning\Http\Controllers\Public\CertificateVerificationController;
use Illuminate\Support\Facades\Route;

/**
 * التحقق من الشهادات.
 * يُحمَّل من routes/web/public.php في نفس موضعه القديم.
 */
Route::get('/certificates/verify', [CertificateVerificationController::class, 'verify'])->name('public.certificates.verify');
Route::get('/certificates/verify/{code}', [CertificateVerificationController::class, 'verify'])->name('public.certificates.verify.code');
