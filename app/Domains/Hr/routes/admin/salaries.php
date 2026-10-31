<?php

use App\Domains\Hr\Http\Controllers\Admin\EmployeeSalaryPayrollController;
use Illuminate\Support\Facades\Route;

/**
 * رواتب الموظفين — مسير شهري ودفع من المحفظة.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('employee-salaries')->name('employee-salaries.')->group(function () {
    Route::get('/', [EmployeeSalaryPayrollController::class, 'index'])->name('index');
    Route::post('/generate', [EmployeeSalaryPayrollController::class, 'generate'])->name('generate');
    Route::get('/export', [EmployeeSalaryPayrollController::class, 'export'])->name('export');
    Route::post('/pay-batch', [EmployeeSalaryPayrollController::class, 'payBatch'])->name('pay-batch');
    Route::get('/pay/{payment}', [EmployeeSalaryPayrollController::class, 'pay'])->name('pay');
    Route::post('/pay/{payment}', [EmployeeSalaryPayrollController::class, 'markPaid'])->name('mark-paid');
});
