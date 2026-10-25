<?php

use App\Domains\Scholarship\Http\Controllers\Instructor\GroupController;
use App\Domains\Scholarship\Http\Controllers\Instructor\RegistrationController;
use App\Domains\Scholarship\Http\Controllers\Instructor\ScholarshipController;
use Illuminate\Support\Facades\Route;

/**
 * صفحات المنح في لوحة المدرب.
 * يُحمَّل من داخل مجموعة instructor في routes/web/instructor.php (prefix/name/middleware المدرب)
 * وفي نفس موضعه القديم بين courses و courses/{course}/mind-map.
 */
Route::get('scholarships', [ScholarshipController::class, 'index'])->name('scholarships.index');
Route::get('scholarships/students', [ScholarshipController::class, 'students'])->name('scholarships.students.index');
Route::post('scholarships/registrations/{registration}/activate', [RegistrationController::class, 'activate'])->name('scholarships.registrations.activate');
Route::post('scholarships/registrations/{registration}/deactivate', [RegistrationController::class, 'deactivate'])->name('scholarships.registrations.deactivate');
Route::post('scholarships/registrations/{registration}/reject', [RegistrationController::class, 'reject'])->name('scholarships.registrations.reject');
Route::post('scholarships/groups', [GroupController::class, 'store'])->name('scholarships.groups.store');
Route::put('scholarships/groups/{group}', [GroupController::class, 'update'])->name('scholarships.groups.update');
Route::delete('scholarships/groups/{group}', [GroupController::class, 'destroy'])->name('scholarships.groups.destroy');
Route::get('scholarships/{program}', [ScholarshipController::class, 'show'])->name('scholarships.show');
