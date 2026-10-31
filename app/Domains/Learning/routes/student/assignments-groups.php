<?php

use App\Domains\Learning\Http\Controllers\Student\AssignmentController;
use App\Domains\Learning\Http\Controllers\Student\GroupController;
use App\Domains\Learning\Http\Controllers\Student\TaskController;
use Illuminate\Support\Facades\Route;

/**
 * واجبات ومجموعات الطالب.
 * يُحمَّل من داخل مجموعة student في routes/web/student-account.php وفي نفس موضعه القديم.
 */
Route::resource('assignments', AssignmentController::class)->only(['index', 'show']);
Route::post('/assignments/{assignment}/submit', [AssignmentController::class, 'submit'])
    ->middleware(['ownership:assignment,assignment'])
    ->name('assignments.submit');
Route::resource('tasks', TaskController::class);
// مجموعاتي: عرض المجموعات والمحادثة والتسليمات
Route::get('groups', [GroupController::class, 'index'])->name('groups.index');
Route::get('groups/{group}/messages', [GroupController::class, 'getMessages'])->name('groups.messages.index');
Route::get('groups/{group}/assignments', [GroupController::class, 'assignments'])->name('groups.assignments.index');
Route::get('groups/{group}', [GroupController::class, 'show'])->name('groups.show');
Route::post('groups/{group}/messages', [GroupController::class, 'storeMessage'])->name('groups.messages.store');
Route::post('groups/{group}/assignments/{assignment}/submit', [GroupController::class, 'submitAssignment'])->name('groups.assignments.submit');
