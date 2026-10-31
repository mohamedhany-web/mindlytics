<?php

use App\Domains\Learning\Http\Controllers\Student\AcademicYearController;
use App\Domains\Learning\Http\Controllers\Student\CourseController;
use App\Domains\Learning\Http\Controllers\Student\SubjectController;
use Illuminate\Support\Facades\Route;

/**
 * السنوات والمواد وصفحة الكورس للطالب.
 * يُحمَّل من routes/web/student.php وفي نفس موضعه القديم.
 */
Route::get('/academic-years', [AcademicYearController::class, 'index'])->name('academic-years');
Route::get('/academic-years/{academicYear}/subjects', [AcademicYearController::class, 'subjects'])->name('academic-years.subjects');
Route::get('/subjects/{academicSubject}/courses', [SubjectController::class, 'courses'])->name('subjects.courses');
Route::get('/courses/{advancedCourse}', [CourseController::class, 'show'])->name('courses.show');
