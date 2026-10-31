<?php

use App\Domains\Accounting\Http\Controllers\Student\InvoiceController;
use App\Domains\Accounting\Http\Controllers\Student\WalletController;
use Illuminate\Support\Facades\Route;

/**
 * فواتير الطالب ومحفظته.
 * يُحمَّل من داخل مجموعة student في routes/web/student-account.php (role:student) وفي نفس موضعه القديم.
 */
Route::resource('invoices', InvoiceController::class)->only(['index', 'show']);
Route::post('invoices/{invoice}/payment-proof', [InvoiceController::class, 'storePaymentProof'])
    ->name('invoices.payment-proof');
Route::resource('wallet', WalletController::class)->only(['index', 'show']);
Route::post('wallet/transfer', [WalletController::class, 'transfer'])->name('wallet.transfer');
