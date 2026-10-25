<?php

use App\Domains\Learning\Http\Controllers\Admin\InstructorRequestController;
use Illuminate\Support\Facades\Route;

/**
 * طلبات المدربين.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('instructor-requests')->name('instructor-requests.')->group(function () {
    Route::get('/', [InstructorRequestController::class, 'index'])->name('index');
    Route::get('/{instructorRequest}', [InstructorRequestController::class, 'show'])->name('show');
    Route::post('/{instructorRequest}/respond', [InstructorRequestController::class, 'respond'])->name('respond');
});
