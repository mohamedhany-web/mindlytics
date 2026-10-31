<?php

use App\Domains\Hr\Http\Controllers\Admin\EmployeeAdditionController;
use App\Domains\Hr\Http\Controllers\Admin\EmployeeDeductionController;
use Illuminate\Support\Facades\Route;

/**
 * الخصومات والإضافات على رواتب الموظفين.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::get('employee-deductions/daily-report-penalty-settings', [EmployeeDeductionController::class, 'dailyReportPenaltySettings'])->name('employee-deductions.daily-report-penalty-settings');
Route::put('employee-deductions/daily-report-penalty-settings', [EmployeeDeductionController::class, 'updateDailyReportPenaltySettings'])->name('employee-deductions.daily-report-penalty-settings.update');
Route::post('employee-deductions/bulk-delete-by-date', [EmployeeDeductionController::class, 'bulkDestroyByDateRange'])->name('employee-deductions.bulk-delete-by-date');
Route::resource('employee-deductions', EmployeeDeductionController::class);
Route::resource('employee-additions', EmployeeAdditionController::class);
