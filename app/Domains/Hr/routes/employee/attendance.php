<?php

use App\Domains\Hr\Http\Controllers\Employee\EmployeeAttendanceController;
use App\Domains\Hr\Http\Controllers\Employee\EmployeePresenceController;
use Illuminate\Support\Facades\Route;

/**
 * حضور الموظف ونبض التواجد.
 * يُحمَّل من داخل مجموعة employee في routes/web/employee.php (prefix: employee، name: employee.، auth + employee.work)
 * وفي نفس موضعه القديم.
 */
Route::post('/attendance/clock-in', [EmployeeAttendanceController::class, 'clockIn'])->name('attendance.clock-in');
Route::post('/attendance/clock-out', [EmployeeAttendanceController::class, 'clockOut'])->name('attendance.clock-out');
Route::get('/attendance/status', [EmployeeAttendanceController::class, 'status'])->name('attendance.status');
Route::post('/presence/heartbeat', [EmployeePresenceController::class, 'heartbeat'])->name('presence.heartbeat');
Route::get('/presence/status', [EmployeePresenceController::class, 'status'])->name('presence.status');
