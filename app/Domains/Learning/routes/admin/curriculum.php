<?php

use App\Domains\Learning\Http\Controllers\Admin\CurriculumController;
use App\Domains\Learning\Http\Controllers\Instructor\LectureVideoQuestionController;
use Illuminate\Support\Facades\Route;

/**
 * بناء المنهج (أدمن) وأسئلة الفيديو.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::get('/curriculum', [CurriculumController::class, 'hub'])->name('curriculum.hub');
Route::get('/advanced-courses/{course}/curriculum', [CurriculumController::class, 'index'])->name('advanced-courses.curriculum');
Route::put('/advanced-courses/{course}/unlock-policy', [CurriculumController::class, 'updateUnlockPolicy'])->name('advanced-courses.unlock-policy');
Route::put('/advanced-courses/{course}/watch-percent', [CurriculumController::class, 'applyWatchPercent'])->name('advanced-courses.watch-percent');
Route::put('/lectures/{lecture}/watch-percent', [CurriculumController::class, 'updateLectureWatchPercent'])->name('lectures.watch-percent');
Route::post('/advanced-courses/{course}/curriculum/exams', [CurriculumController::class, 'storeExamFromCurriculum'])->name('advanced-courses.curriculum.exams.store');
Route::post('/advanced-courses/{course}/curriculum/assignments', [CurriculumController::class, 'storeAssignmentFromCurriculum'])->name('advanced-courses.curriculum.assignments.store');
Route::post('/advanced-courses/{course}/sections', [CurriculumController::class, 'storeSection'])->name('advanced-courses.sections.store');
Route::post('/advanced-courses/{course}/sections/order', [CurriculumController::class, 'updateSectionsOrder'])->name('advanced-courses.sections.order');
Route::put('/sections/{section}', [CurriculumController::class, 'updateSection'])->name('sections.update');
Route::put('/sections/{section}/visibility', [CurriculumController::class, 'updateSectionVisibility'])->name('sections.visibility');
Route::delete('/sections/{section}', [CurriculumController::class, 'destroySection'])->name('sections.destroy');
Route::post('/sections/{section}/items', [CurriculumController::class, 'addItem'])->name('sections.items.store');
Route::post('/sections/{section}/items/order', [CurriculumController::class, 'updateItemsOrder'])->name('sections.items.order');
Route::put('/curriculum-items/{item}/visibility', [CurriculumController::class, 'updateItemVisibility'])->name('curriculum-items.visibility');
Route::delete('/curriculum-items/{item}', [CurriculumController::class, 'removeItem'])->name('curriculum-items.destroy');
Route::post('/curriculum-items/{item}/move', [CurriculumController::class, 'moveItem'])->name('curriculum-items.move');
Route::get('/lectures/{lecture}/video-questions', [LectureVideoQuestionController::class, 'index'])->name('lectures.video-questions.index');
Route::post('/lectures/{lecture}/video-questions', [LectureVideoQuestionController::class, 'store'])->name('lectures.video-questions.store');
Route::delete('/lectures/{lecture}/video-questions/{videoQuestion}', [LectureVideoQuestionController::class, 'destroy'])->name('lectures.video-questions.destroy');
