<?php

use App\Domains\Sales\Http\Controllers\Employee\SalesCourseBoardController;
use App\Domains\Sales\Http\Controllers\Employee\SalesPolicyController;
use Illuminate\Support\Facades\Route;

/**
 * صفحات مشتركة لموظفي ومديري المبيعات (sales.staff).
 * يُحمَّل من routes/web/employee.php داخل مجموعة employee وفي نفس موضعه القديم.
 */
Route::middleware('sales.staff')->prefix('sales')->name('sales.')->group(function () {
    Route::get('course-board', [SalesCourseBoardController::class, 'index'])->name('course-board.index');
    Route::get('policy', [SalesPolicyController::class, 'index'])->name('policy.index');
});
