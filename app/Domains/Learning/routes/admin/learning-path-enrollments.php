<?php

use App\Domains\Learning\Http\Controllers\Admin\LearningPathEnrollmentController;
use Illuminate\Support\Facades\Route;

/**
 * تسجيلات المسارات التعليمية.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('learning-path-enrollments')->name('learning-path-enrollments.')->group(function () {
    Route::get('/', [LearningPathEnrollmentController::class, 'index'])->name('index');
    Route::get('/create', [LearningPathEnrollmentController::class, 'create'])->name('create');
    Route::post('/', [LearningPathEnrollmentController::class, 'store'])->name('store');
    Route::post('/{enrollment}/toggle-status', [LearningPathEnrollmentController::class, 'toggleStatus'])->name('toggle-status');
    Route::delete('/{enrollment}', [LearningPathEnrollmentController::class, 'destroy'])->name('destroy');
});
