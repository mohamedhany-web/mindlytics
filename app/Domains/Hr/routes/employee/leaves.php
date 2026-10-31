<?php

use App\Domains\Hr\Http\Controllers\Employee\EmployeeLeaveController;
use Illuminate\Support\Facades\Route;

/**
 * طلبات الإجازة.
 * يُحمَّل من داخل مجموعة employee في routes/web/employee.php (prefix: employee، name: employee.، auth + employee.work)
 * وفي نفس موضعه القديم.
 */
Route::resource('leaves', EmployeeLeaveController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
