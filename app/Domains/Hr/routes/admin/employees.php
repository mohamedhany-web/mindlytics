<?php

use App\Domains\Hr\Http\Controllers\Admin\EmployeeAttendanceReportController;
use App\Domains\Hr\Http\Controllers\Admin\EmployeeController;
use App\Domains\Hr\Http\Controllers\Admin\EmployeeJobController;
use App\Domains\Hr\Http\Controllers\Admin\EmployeeTaskController;
use App\Domains\Hr\Http\Controllers\Admin\WorkScheduleController;
use Illuminate\Support\Facades\Route;

/**
 * إدارة الموظفين: الموظفون، جداول العمل، تقارير الحضور، الوظائف، المهام.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::resource('employees', EmployeeController::class);
Route::resource('work-schedules', WorkScheduleController::class)->except(['show']);
Route::get('employee-attendance', [EmployeeAttendanceReportController::class, 'index'])->name('employee-attendance.index');
Route::get('employee-attendance/export', [EmployeeAttendanceReportController::class, 'export'])->name('employee-attendance.export');
Route::get('employee-attendance/penalty-settings', [EmployeeAttendanceReportController::class, 'penaltySettings'])->name('employee-attendance.penalty-settings');
Route::put('employee-attendance/penalty-settings', [EmployeeAttendanceReportController::class, 'updatePenaltySettings'])->name('employee-attendance.penalty-settings.update');
Route::post('employee-attendance/apply-penalties', [EmployeeAttendanceReportController::class, 'applyPenalties'])->name('employee-attendance.apply-penalties');
Route::get('employee-attendance/employees/{employee}', [EmployeeAttendanceReportController::class, 'employeeSummary'])->name('employee-attendance.employee');
Route::resource('employee-jobs', EmployeeJobController::class);
Route::match(['put', 'patch'], 'employee-tasks/{employee_task}/deliverables/{deliverable}', [EmployeeTaskController::class, 'updateDeliverable'])->name('employee-tasks.deliverables.update');
Route::delete('employee-tasks/{employee_task}/deliverables/{deliverable}', [EmployeeTaskController::class, 'destroyDeliverable'])->name('employee-tasks.deliverables.destroy');
Route::get('employee-tasks/{employee_task}/deliverables/montage-excel-template', [EmployeeTaskController::class, 'downloadMontageExcelTemplate'])->name('employee-tasks.deliverables.montage-excel-template');
Route::post('employee-tasks/{employee_task}/deliverables/import-excel', [EmployeeTaskController::class, 'importMontageExcel'])->name('employee-tasks.deliverables.import-excel');
Route::resource('employee-tasks', EmployeeTaskController::class);
