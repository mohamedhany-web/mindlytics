<?php

use App\Domains\Learning\Http\Controllers\Public\PublicReviewController;
use Illuminate\Support\Facades\Route;

/**
 * تقييمات الكورسات.
 * يُحمَّل من routes/web/public.php في نفس موضعه القديم.
 */
Route::post('/course/{courseId}/reviews', [PublicReviewController::class, 'storeCourse'])
    ->middleware('auth')
    ->name('public.course.reviews.store');
