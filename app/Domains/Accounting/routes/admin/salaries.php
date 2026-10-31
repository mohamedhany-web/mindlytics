<?php

use App\Domains\Accounting\Http\Controllers\Admin\SalaryController;
use Illuminate\Support\Facades\Route;

/**
 * ماليات المدربين (المستحقات والدفع).
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('salaries')->name('salaries.')->group(function () {
    Route::get('/', [SalaryController::class, 'index'])->name('index');
    Route::get('/instructor/{instructor}', [SalaryController::class, 'instructor'])->name('instructor');
    Route::post('/instructor/{instructor}/pay-now/{agreement}', [SalaryController::class, 'payNowFromAgreement'])->name('pay-now-from-agreement');
    Route::get('/pay/{payment}', [SalaryController::class, 'pay'])->name('pay');
    Route::post('/pay/{payment}', [SalaryController::class, 'markPaid'])->name('mark-paid');
});
