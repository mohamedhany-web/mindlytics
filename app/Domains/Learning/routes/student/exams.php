<?php

use App\Domains\Learning\Http\Controllers\Student\ExamController;
use App\Http\Middleware\VideoProtectionMiddleware;
use Illuminate\Support\Facades\Route;

/**
 * امتحانات الطالب (role:student).
 * يُحمَّل من routes/web/student.php وفي نفس موضعه القديم.
 */
Route::prefix('exams')->name('student.exams.')->middleware(['role:student'])->group(function () {
    Route::get('/', [ExamController::class, 'index'])->name('index');
    Route::get('/{exam}', [ExamController::class, 'show'])->name('show');
    Route::match(['get', 'post'], '/{exam}/start', [ExamController::class, 'start'])->name('start');
    Route::get('/{exam}/attempts/{attempt}/take', [ExamController::class, 'take'])
        ->middleware(VideoProtectionMiddleware::class)
        ->name('take');
    Route::post('/{exam}/attempts/{attempt}/save-answer', [ExamController::class, 'saveAnswer'])->name('save-answer');
    Route::match(['get', 'post'], '/{exam}/attempts/{attempt}/submit', [ExamController::class, 'submit'])->name('submit');
    Route::get('/{exam}/attempts/{attempt}/result', [ExamController::class, 'result'])->name('result');
    Route::post('/{exam}/attempts/{attempt}/tab-switch', [ExamController::class, 'logTabSwitch'])->name('tab-switch');
});
