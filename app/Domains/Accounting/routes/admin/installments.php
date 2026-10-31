<?php

use App\Domains\Accounting\Http\Controllers\Admin\InstallmentAgreementController;
use App\Domains\Accounting\Http\Controllers\Admin\InstallmentPlanController;
use Illuminate\Support\Facades\Route;

/**
 * خطط واتفاقيات التقسيط.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('installments')->name('installments.')->group(function () {
    Route::resource('plans', InstallmentPlanController::class);
    Route::get('agreements/manual-booking', [InstallmentAgreementController::class, 'createManualBooking'])
        ->name('agreements.manual-booking');
    Route::post('agreements/manual-booking', [InstallmentAgreementController::class, 'storeManualBooking'])
        ->name('agreements.manual-booking.store');
    Route::resource('agreements', InstallmentAgreementController::class);
    Route::post('/agreements/payments/{payment}/mark', [InstallmentAgreementController::class, 'markPayment'])
        ->name('agreements.mark-payment');
});
