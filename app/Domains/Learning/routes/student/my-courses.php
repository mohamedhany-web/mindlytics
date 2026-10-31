<?php

use App\Domains\Learning\Http\Controllers\Student\MyCourseController;
use Illuminate\Support\Facades\Route;

/**
 * كورساتي (القائمة والتفاصيل) — داخل مجموعة role:student.
 * يُحمَّل من routes/web/student.php وفي نفس موضعه القديم.
 */
Route::get('/my-courses', [MyCourseController::class, 'index'])->name('my-courses.index');
Route::get('/my-courses/{course}', [MyCourseController::class, 'show'])
    ->middleware(['ownership:course,course'])
    ->name('my-courses.show');
