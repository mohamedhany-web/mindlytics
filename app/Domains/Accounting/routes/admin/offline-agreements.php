<?php

use App\Domains\Accounting\Http\Controllers\Admin\OfflineAgreementController;
use Illuminate\Support\Facades\Route;

/**
 * اتفاقيات المدربين للكورسات الأوفلاين.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('offline-agreements')->name('offline-agreements.')->group(function () {
    Route::get('/', [OfflineAgreementController::class, 'index'])->name('index');
    Route::get('/create', [OfflineAgreementController::class, 'create'])->name('create');
    Route::post('/', [OfflineAgreementController::class, 'store'])->name('store');
    Route::get('/{agreement}', [OfflineAgreementController::class, 'show'])->name('show');
    Route::get('/{agreement}/edit', [OfflineAgreementController::class, 'edit'])->name('edit');
    Route::put('/{agreement}', [OfflineAgreementController::class, 'update'])->name('update');
    Route::delete('/{agreement}', [OfflineAgreementController::class, 'destroy'])->name('destroy');
});
