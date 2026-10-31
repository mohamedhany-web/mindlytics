<?php

use App\Domains\Hr\Http\Controllers\Employee\EmployeeReportController;
use Illuminate\Support\Facades\Route;

/**
 * تقارير وإحصائيات الموظف (المهام والإجازات).
 * يُحمَّل من داخل مجموعة employee في routes/web/employee.php (prefix: employee، name: employee.، auth + employee.work)
 * وفي نفس موضعه القديم.
 */
Route::get('/reports', [EmployeeReportController::class, 'index'])->name('reports');
