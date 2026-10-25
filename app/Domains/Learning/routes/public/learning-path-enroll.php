<?php

use App\Domains\Learning\Http\Controllers\Public\LearningPathController;
use Illuminate\Support\Facades\Route;

/**
 * الاشتراك في مسار (توافق قديم).
 * يُحمَّل من routes/web/public.php في نفس موضعه القديم.
 */
Route::post('/learning-path/{slug}/enroll', [LearningPathController::class, 'enroll'])
    ->middleware('auth')
    ->name('public.learning-path.enroll');
