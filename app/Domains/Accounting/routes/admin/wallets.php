<?php

use App\Domains\Accounting\Http\Controllers\Admin\WalletController;
use Illuminate\Support\Facades\Route;

/**
 * المحافظ الذكية (الحركات والتقارير).
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::resource('wallets', WalletController::class);
Route::get('/wallets/{wallet}/transactions', [WalletController::class, 'transactions'])->name('wallets.transactions');
Route::get('/wallets/{wallet}/reports', [WalletController::class, 'reports'])->name('wallets.reports');
Route::post('/wallets/{wallet}/generate-report', [WalletController::class, 'generateReport'])->name('wallets.generate-report');
