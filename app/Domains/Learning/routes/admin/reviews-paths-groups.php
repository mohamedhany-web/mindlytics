<?php

use App\Domains\Learning\Http\Controllers\Admin\AttendanceController;
use App\Domains\Learning\Http\Controllers\Admin\GroupController;
use App\Domains\Learning\Http\Controllers\Admin\LearningPathReviewController;
use App\Domains\Learning\Http\Controllers\Admin\LectureController;
use App\Domains\Learning\Http\Controllers\Admin\ReviewController;
use Illuminate\Support\Facades\Route;

/**
 * التقييمات، المسارات التعليمية، مزودو الفيديو، التدريب، المجموعات.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::resource('reviews', ReviewController::class)->only(['index', 'show', 'update', 'destroy']);
Route::resource('learning-path-reviews', LearningPathReviewController::class)
    ->only(['index', 'show', 'update', 'destroy'])
    ->parameters(['learning-path-reviews' => 'learningPathReview']);

// إدارة المحاضرات (مسار الكورس قبل الـ resource لتفادي التعارض)
Route::get('/lectures/course/{course}', [LectureController::class, 'indexByCourse'])->name('lectures.by-course');
Route::resource('lectures', LectureController::class);

// إدارة الحضور
Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('index');
    Route::get('/lecture/{lecture}', [AttendanceController::class, 'showLectureAttendance'])->name('lecture');
    Route::post('/lecture/{lecture}/upload-teams', [AttendanceController::class, 'uploadTeamsFile'])->name('upload-teams');
});

// إدارة المجموعات
Route::resource('groups', GroupController::class);
