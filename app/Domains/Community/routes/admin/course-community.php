<?php

use App\Domains\Community\Http\Controllers\Admin\AdminCourseCommunityController;
use Illuminate\Support\Facades\Route;

/**
 * مجتمع الكورس (منشورات التطبيق).
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('course-community')->name('course-community.')->group(function () {
    Route::get('/', [AdminCourseCommunityController::class, 'index'])->name('index');
    Route::get('/posts/create', [AdminCourseCommunityController::class, 'create'])->name('posts.create');
    Route::post('/posts', [AdminCourseCommunityController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('posts.store');
    Route::get('/posts/{post}', [AdminCourseCommunityController::class, 'show'])->name('posts.show');
    Route::delete('/posts/{post}', [AdminCourseCommunityController::class, 'destroyPost'])->name('posts.destroy');
    Route::post('/posts/{post}/pin', [AdminCourseCommunityController::class, 'togglePin'])->name('posts.pin');
    Route::delete('/posts/{post}/comments/{comment}', [AdminCourseCommunityController::class, 'destroyComment'])
        ->name('posts.comments.destroy');
});
