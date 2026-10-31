<?php

use App\Domains\Hr\Http\Controllers\Admin\EmployeeAgreementController;
use Illuminate\Support\Facades\Route;

/**
 * اتفاقيات الموظفين.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('employee-agreements')->name('employee-agreements.')->group(function () {
    Route::get('/', [EmployeeAgreementController::class, 'index'])->name('index');
    Route::get('/create', [EmployeeAgreementController::class, 'create'])->name('create');
    Route::post('/', [EmployeeAgreementController::class, 'store'])
        ->middleware('throttle:20,5')
        ->name('store');
    Route::post('payments/{payment}/mark-paid', [EmployeeAgreementController::class, 'markPaymentPaid'])->name('payments.mark-paid');
    Route::post('{employeeAgreement}/payments', [EmployeeAgreementController::class, 'storePayment'])->name('payments.store');
    Route::post('{employeeAgreement}/stop', [EmployeeAgreementController::class, 'stop'])
        ->middleware('throttle:20,5')
        ->name('stop');
    Route::post('{employeeAgreement}/reactivate', [EmployeeAgreementController::class, 'reactivate'])
        ->middleware('throttle:20,5')
        ->name('reactivate');
    Route::get('/{employeeAgreement}', [EmployeeAgreementController::class, 'show'])->name('show');
    Route::get('/{employeeAgreement}/edit', [EmployeeAgreementController::class, 'edit'])->name('edit');
    Route::put('/{employeeAgreement}', [EmployeeAgreementController::class, 'update'])
        ->middleware('throttle:20,5')
        ->name('update');
    Route::delete('/{employeeAgreement}', [EmployeeAgreementController::class, 'destroy'])
        ->middleware('throttle:10,1')
        ->name('destroy');
});
