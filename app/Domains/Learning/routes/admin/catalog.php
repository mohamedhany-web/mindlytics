<?php

use App\Domains\Learning\Http\Controllers\Admin\AcademicSubjectController;
use App\Domains\Learning\Http\Controllers\Admin\AcademicYearController;
use App\Domains\Learning\Http\Controllers\Admin\AdvancedCourseController;
use App\Domains\Learning\Http\Controllers\Admin\LearningPathManagementController;
use Illuminate\Support\Facades\Route;

/**
 * السنوات والمواد والكورسات المتقدمة والفصول.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::resource('academic-years', AcademicYearController::class);
Route::post('/academic-years/{academicYear}/toggle-status', [AcademicYearController::class, 'toggleStatus'])->name('academic-years.toggle-status');
Route::post('/academic-years/reorder', [AcademicYearController::class, 'reorder'])->name('academic-years.reorder');
Route::post('/academic-years/{academicYear}/add-course', [AcademicYearController::class, 'addCourse'])->name('academic-years.add-course');
Route::delete('/academic-years/{academicYear}/remove-course/{course}', [AcademicYearController::class, 'removeCourse'])->name('academic-years.remove-course');
Route::post('/academic-years/{academicYear}/add-instructor', [AcademicYearController::class, 'addInstructor'])->name('academic-years.add-instructor');
Route::delete('/academic-years/{academicYear}/remove-instructor/{instructor}', [AcademicYearController::class, 'removeInstructor'])->name('academic-years.remove-instructor');

// إدارة الكورسات والمدربين في المسارات التعليمية
Route::prefix('learning-paths')->name('learning-paths.')->group(function () {
    // إدارة الكورسات
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/', [LearningPathManagementController::class, 'coursesIndex'])->name('index');
        Route::get('/{academicYear}/manage', [LearningPathManagementController::class, 'coursesManage'])->name('manage');
        Route::post('/{academicYear}', [LearningPathManagementController::class, 'coursesStore'])->name('store');
        Route::delete('/{academicYear}/{course}', [LearningPathManagementController::class, 'coursesDestroy'])->name('destroy');
        Route::post('/{academicYear}/update-order', [LearningPathManagementController::class, 'coursesUpdateOrder'])->name('update-order');
    });
            
    // إدارة المدربين
    Route::prefix('instructors')->name('instructors.')->group(function () {
        Route::get('/', [LearningPathManagementController::class, 'instructorsIndex'])->name('index');
        Route::get('/{academicYear}/manage', [LearningPathManagementController::class, 'instructorsManage'])->name('manage');
        Route::post('/{academicYear}', [LearningPathManagementController::class, 'instructorsStore'])->name('store');
        Route::delete('/{academicYear}/{instructor}', [LearningPathManagementController::class, 'instructorsDestroy'])->name('destroy');
        Route::put('/{academicYear}/{instructor}/update-courses', [LearningPathManagementController::class, 'instructorsUpdateCourses'])->name('update-courses');
    });
});

// إدارة المواد الدراسية
Route::resource('academic-subjects', AcademicSubjectController::class);
Route::post('/academic-subjects/{academicSubject}/toggle-status', [AcademicSubjectController::class, 'toggleStatus'])->name('academic-subjects.toggle-status');
Route::post('/academic-subjects/reorder', [AcademicSubjectController::class, 'reorder'])->name('academic-subjects.reorder');

// إدارة الكورسات المتطورة
Route::resource('advanced-courses', AdvancedCourseController::class);
Route::post('/advanced-courses/{advancedCourse}/activate-student', [AdvancedCourseController::class, 'activateStudent'])->name('advanced-courses.activate-student');
Route::get('/advanced-courses/{advancedCourse}/students', [AdvancedCourseController::class, 'students'])->name('advanced-courses.students');
Route::post('/advanced-courses/{advancedCourse}/toggle-status', [AdvancedCourseController::class, 'toggleStatus'])->name('advanced-courses.toggle-status');
Route::post('/advanced-courses/{advancedCourse}/toggle-featured', [AdvancedCourseController::class, 'toggleFeatured'])->name('advanced-courses.toggle-featured');
Route::get('/advanced-courses/{advancedCourse}/orders', [AdvancedCourseController::class, 'orders'])->name('advanced-courses.orders');
Route::get('/advanced-courses/{advancedCourse}/statistics', [AdvancedCourseController::class, 'statistics'])->name('advanced-courses.statistics');
Route::post('/advanced-courses/{advancedCourse}/duplicate', [AdvancedCourseController::class, 'duplicate'])->name('advanced-courses.duplicate');
Route::get('/get-subjects-by-year', [AdvancedCourseController::class, 'getSubjectsByYear'])->name('advanced-courses.get-subjects-by-year');
