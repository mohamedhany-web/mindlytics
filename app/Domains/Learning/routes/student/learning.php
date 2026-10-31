<?php

use App\Domains\Learning\Http\Controllers\Student\LearningPathController;
use App\Domains\Learning\Http\Controllers\Student\LearningPatternController;
use App\Domains\Learning\Http\Controllers\Student\MyCourseController;
use App\Http\Middleware\VideoProtectionMiddleware;
use Illuminate\Support\Facades\Route;

/**
 * المسار التعليمي للطالب، صفحة التعلّم، المحاضرات والدروس، أنماط التعلّم — داخل مجموعة role:student.
 * يُحمَّل من routes/web/student.php وفي نفس موضعه القديم.
 */
    Route::get('/student/learning-path', [LearningPathController::class, 'index'])->name('student.learning-path.index');
    Route::get('/student/learning-path/{slug}', [LearningPathController::class, 'show'])->name('student.learning-path.show');
Route::get('/my-courses/{course}/learn', [MyCourseController::class, 'learn'])
    ->middleware(['ownership:course,course'])
    ->name('my-courses.learn');
Route::get('/my-courses/{course}/lectures/{lecture}', [MyCourseController::class, 'getLectureData'])
    ->middleware(['ownership:course,course'])
    ->name('my-courses.lectures.show');
Route::get('/my-courses/{course}/lectures/{lecture}/materials/{material}/download', [MyCourseController::class, 'downloadLectureMaterial'])
    ->middleware(['ownership:course,course'])
    ->name('my-courses.lectures.material.download');
Route::post('/my-courses/{course}/lectures/{lecture}/video-questions/{videoQuestion}/answer', [MyCourseController::class, 'submitLectureVideoQuestionAnswer'])
    ->middleware(['ownership:course,course'])
    ->name('my-courses.lectures.video-question.answer');
Route::post('/my-courses/{course}/lectures/{lecture}/progress', [MyCourseController::class, 'updateLectureProgress'])
    ->middleware(['ownership:course,course'])
    ->name('my-courses.lectures.progress');
Route::get('/my-courses/{course}/lessons/{lesson}/watch', [MyCourseController::class, 'watchLesson'])
    ->middleware([VideoProtectionMiddleware::class, 'ownership:course,course'])
    ->name('my-courses.lesson.watch');
Route::post('/my-courses/{course}/lessons/{lesson}/progress', [MyCourseController::class, 'updateLessonProgress'])
    ->middleware(['ownership:course,course'])
    ->name('my-courses.lesson.progress');
Route::get('/my-courses/{course}/curriculum/locks', [MyCourseController::class, 'curriculumLocks'])
    ->middleware(['ownership:course,course'])
    ->name('my-courses.curriculum.locks');
        
// أنماط التعلم التفاعلية
Route::prefix('my-courses/{course}/learning-patterns')->name('my-courses.learning-patterns.')->group(function () {
    Route::get('/{pattern}', [LearningPatternController::class, 'show'])
        ->middleware(['ownership:course,course'])
        ->name('show');
    Route::post('/{pattern}/start', [LearningPatternController::class, 'startAttempt'])
        ->middleware(['ownership:course,course'])
        ->name('start');
    Route::post('/{pattern}/attempts/{attempt}/save', [LearningPatternController::class, 'saveProgress'])
        ->middleware(['ownership:course,course'])
        ->name('save-progress');
    Route::post('/{pattern}/attempts/{attempt}/complete', [LearningPatternController::class, 'completeAttempt'])
        ->middleware(['ownership:course,course'])
        ->name('complete');
});
