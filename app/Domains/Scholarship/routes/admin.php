<?php

use App\Domains\Scholarship\Http\Controllers\Admin\CourseController;
use App\Domains\Scholarship\Http\Controllers\Admin\DashboardController;
use App\Domains\Scholarship\Http\Controllers\Admin\GroupController;
use App\Domains\Scholarship\Http\Controllers\Admin\InstructorController;
use App\Domains\Scholarship\Http\Controllers\Admin\ProgramController;
use App\Domains\Scholarship\Http\Controllers\Admin\RegistrationController;
use Illuminate\Support\Facades\Route;

/**
 * لوحة إدارة المنح الدراسية.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('scholarships')->name('scholarships.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('programs', ProgramController::class);
    Route::get('courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('instructors', [InstructorController::class, 'index'])->name('instructors.index');
    Route::get('instructors/{instructor}', [InstructorController::class, 'show'])->name('instructors.show');
    Route::get('students', [RegistrationController::class, 'index'])->name('students.index');
    Route::get('groups', [GroupController::class, 'index'])->name('groups.index');
    Route::post('groups', [GroupController::class, 'store'])->name('groups.store');
    Route::put('groups/{group}', [GroupController::class, 'update'])->name('groups.update');
    Route::delete('groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');
    Route::post('registrations/{registration}/activate', [RegistrationController::class, 'activate'])->name('registrations.activate');
    Route::post('registrations/{registration}/deactivate', [RegistrationController::class, 'deactivate'])->name('registrations.deactivate');
    Route::post('registrations/{registration}/reject', [RegistrationController::class, 'reject'])->name('registrations.reject');
});
