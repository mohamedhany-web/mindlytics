<?php

use App\Domains\Accounting\Http\Controllers\Employee\AccountingController;
use Illuminate\Support\Facades\Route;

/**
 * المحاسبة والراتب في بوابة الموظف.
 * يُحمَّل من routes/web/employee.php داخل مجموعة employee (auth + employee.work) وفي نفس موضعه القديم.
 */
Route::get('/accounting', [AccountingController::class, 'index'])->name('accounting.index');
Route::post('/accounting/bank-account', [AccountingController::class, 'updateBankAccount'])->name('accounting.update-bank');
