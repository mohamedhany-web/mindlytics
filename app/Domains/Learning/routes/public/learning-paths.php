<?php

use App\Domains\Learning\Http\Controllers\Public\LearningPathController;
use App\Domains\Learning\Http\Controllers\Public\PublicReviewController;
use Illuminate\Support\Facades\Route;

/**
 * المسارات التعليمية العامة وتقييماتها.
 * يُحمَّل من routes/web/public.php في نفس موضعه القديم.
 */
Route::get('/learning-paths', [LearningPathController::class, 'index'])->name('public.learning-paths.index');

// صفحة تفاصيل مسار تعليمي (يجب أن يكون قبل الـ routes المحمية)
// ملاحظة: يجب أن يكون هذا الـ route قبل أي route محمي يستخدم نفس الـ pattern
Route::get('/learning-path/{slug}', [LearningPathController::class, 'show'])
    ->where('slug', '[a-z0-9-]+')
    ->name('public.learning-path.show');

Route::post('/learning-path/{slug}/reviews', [PublicReviewController::class, 'storeLearningPath'])
    ->middleware('auth')
    ->where('slug', '[a-z0-9-]+')
    ->name('public.learning-path.reviews.store');
