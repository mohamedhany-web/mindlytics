<?php

use App\Domains\Learning\Http\Controllers\Admin\AssignmentController;
use App\Domains\Learning\Http\Controllers\Admin\GroupController;
use App\Domains\Learning\Http\Controllers\Admin\LectureController;
use App\Domains\Learning\Http\Controllers\Admin\TaskController;
use Illuminate\Support\Facades\Route;

/**
 * المحاضرات والمهام.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::resource('lectures', LectureController::class);
Route::resource('groups', GroupController::class);
Route::post('/groups/{group}/members', [GroupController::class, 'addMember'])->name('groups.add-member');
Route::delete('/groups/{group}/members/{member}', [GroupController::class, 'removeMember'])->name('groups.remove-member');

// إدارة الواجبات والمشاريع (مسار الكورس قبل المسارات الأخرى لتفادي التعارض)
Route::get('/assignments/course/{course}', [AssignmentController::class, 'indexByCourse'])->name('assignments.by-course');
Route::resource('assignments', AssignmentController::class);
Route::get('/assignments/{assignment}/submissions', [AssignmentController::class, 'submissions'])->name('assignments.submissions');
Route::post('/assignments/{assignment}/grade/{submission}', [AssignmentController::class, 'grade'])->name('assignments.grade');

// إدارة المهام
Route::resource('tasks', TaskController::class);
Route::post('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
Route::post('/tasks/{task}/comments', [TaskController::class, 'addComment'])->name('tasks.add-comment');
Route::post('/tasks/{task}/deliverables/{deliverable}/review', [TaskController::class, 'reviewDeliverable'])->name('tasks.review-deliverable');
