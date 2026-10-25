<?php

use App\Domains\Hr\Http\Controllers\Employee\EmployeeDailyReportController;
use Illuminate\Support\Facades\Route;

/**
 * التقرير اليومي للموظف.
 * يُحمَّل من داخل مجموعة employee في routes/web/employee.php (prefix: employee، name: employee.، auth + employee.work)
 * وفي نفس موضعه القديم.
 */
Route::get('daily-reports', [EmployeeDailyReportController::class, 'index'])->name('daily-reports.index');
Route::get('daily-reports/edit', [EmployeeDailyReportController::class, 'edit'])->name('daily-reports.edit');
Route::post('daily-reports', [EmployeeDailyReportController::class, 'store'])->name('daily-reports.store');
