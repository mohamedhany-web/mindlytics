<?php

use App\Domains\Accounting\Http\Controllers\Admin\InstructorAgreementController;
use Illuminate\Support\Facades\Route;

/**
 * اتفاقيات المدربين.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('agreements')->name('agreements.')->group(function () {
    Route::get('/', [InstructorAgreementController::class, 'index'])->name('index');
    Route::get('/create', [InstructorAgreementController::class, 'create'])->name('create');
    Route::post('/', [InstructorAgreementController::class, 'store'])
        ->middleware('throttle:20,5')
        ->name('store');
    Route::put('/payments/{payment}', [InstructorAgreementController::class, 'updatePayment'])
        ->name('payments.update');
    Route::delete('/payments/{payment}', [InstructorAgreementController::class, 'destroyPayment'])
        ->name('payments.destroy');
    Route::get('/{agreement}', [InstructorAgreementController::class, 'show'])->name('show');
    Route::post('/{agreement}/apply-percentage', [InstructorAgreementController::class, 'applyPercentage'])
        ->middleware('throttle:10,1')
        ->name('apply-percentage');
    Route::get('/{agreement}/edit', [InstructorAgreementController::class, 'edit'])->name('edit');
    Route::put('/{agreement}', [InstructorAgreementController::class, 'update'])
        ->middleware('throttle:20,5')
        ->name('update');
    Route::delete('/{agreement}', [InstructorAgreementController::class, 'destroy'])
        ->middleware('throttle:10,1')
        ->name('destroy');
});
