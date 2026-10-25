<?php

use App\Domains\Accounting\Http\Controllers\Instructor\AgreementController;
use App\Domains\Accounting\Http\Controllers\Instructor\TransferAccountController;
use App\Domains\Accounting\Http\Controllers\Instructor\WithdrawalRequestController;
use Illuminate\Support\Facades\Route;

/**
 * اتفاقيات المدرب ومستحقاته وطلبات السحب وحساب التحويل.
 * يُحمَّل من داخل مجموعة instructor في routes/web/instructor.php وفي نفس موضعه القديم.
 */
Route::prefix('agreements')->name('agreements.')->group(function () {
    Route::get('/', [AgreementController::class, 'index'])->name('index');
    Route::get('/{agreement}/export-activations', [AgreementController::class, 'exportActivations'])->name('export-activations');
    Route::get('/{agreement}', [AgreementController::class, 'show'])->name('show');
});

// حساب التحويل (بيانات استلام المبالغ)
Route::get('/transfer-account', [TransferAccountController::class, 'index'])->name('transfer-account.index');
Route::post('/transfer-account', [TransferAccountController::class, 'store'])->name('transfer-account.store');
        
// طلبات السحب للمدرب
Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
    Route::get('/', [WithdrawalRequestController::class, 'index'])->name('index');
    Route::get('/create', [WithdrawalRequestController::class, 'create'])->name('create');
    Route::post('/', [WithdrawalRequestController::class, 'store'])->name('store');
    Route::get('/{withdrawal}', [WithdrawalRequestController::class, 'show'])->name('show');
    Route::post('/{withdrawal}/cancel', [WithdrawalRequestController::class, 'cancel'])->name('cancel');
});
