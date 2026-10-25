<?php

use App\Domains\Hr\Http\Controllers\Employee\AgreementController;
use Illuminate\Support\Facades\Route;

/**
 * اتفاقيات الموظف.
 * يُحمَّل من داخل مجموعة employee في routes/web/employee.php (prefix: employee، name: employee.، auth + employee.work)
 * وفي نفس موضعه القديم.
 */
Route::get('/agreements', [AgreementController::class, 'index'])->name('agreements.index');
Route::get('/agreements/{agreement}', [AgreementController::class, 'show'])->name('agreements.show');
