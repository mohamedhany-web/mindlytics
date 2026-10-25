<?php

use App\Domains\Hr\Http\Controllers\Employee\EmployeeTaskController;
use Illuminate\Support\Facades\Route;

/**
 * مهام الموظف وتسليماتها.
 * يُحمَّل من داخل مجموعة employee في routes/web/employee.php (prefix: employee، name: employee.، auth + employee.work)
 * وفي نفس موضعه القديم.
 */
Route::get('/tasks', [EmployeeTaskController::class, 'index'])->name('tasks.index');
Route::get('/tasks/{task}', [EmployeeTaskController::class, 'show'])->name('tasks.show');
Route::put('/tasks/{task}/status', [EmployeeTaskController::class, 'updateStatus'])->name('tasks.update-status');
Route::get('/tasks/{task}/deliverables/montage-excel-template', [EmployeeTaskController::class, 'downloadMontageExcelTemplate'])->name('tasks.deliverables.montage-excel-template');
Route::get('/tasks/{task}/deliverables/montage-export', [EmployeeTaskController::class, 'exportMontageDeliverables'])->name('tasks.deliverables.montage-export');
Route::post('/tasks/{task}/deliverables', [EmployeeTaskController::class, 'submitDeliverable'])->name('tasks.submit-deliverable');
Route::match(['put', 'patch'], '/tasks/{task}/deliverables/{deliverable}', [EmployeeTaskController::class, 'updateDeliverable'])->name('tasks.deliverables.update');
Route::delete('/tasks/{task}/deliverables/{deliverable}', [EmployeeTaskController::class, 'destroyDeliverable'])->name('tasks.deliverables.destroy');
