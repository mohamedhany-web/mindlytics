<?php

use App\Domains\Hr\Http\Controllers\Admin\EmployeeDailyReportAdminController;
use Illuminate\Support\Facades\Route;

/**
 * التقارير اليومية للموظفين.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('employee-daily-reports')->name('employee-daily-reports.')->group(function () {
    Route::get('/', [EmployeeDailyReportAdminController::class, 'index'])->name('index');
    Route::get('/settings', [EmployeeDailyReportAdminController::class, 'settings'])->name('settings');
    Route::put('/settings', [EmployeeDailyReportAdminController::class, 'updateSettings'])->name('settings.update');
    Route::get('/{id}', [EmployeeDailyReportAdminController::class, 'show'])->name('show')->where('id', '[0-9]+');
});
