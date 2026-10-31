<?php

use App\Domains\Scholarship\Http\Controllers\Public\ScholarshipRegistrationController;
use Illuminate\Support\Facades\Route;

/**
 * تسجيل المنح العام برابط خاص (scholarships/{slug}).
 * يُحمَّل من routes/web/auth.php في نفس موضعه القديم.
 */
Route::prefix('scholarships')->name('scholarships.')->group(function () {
    Route::get('{slug}', [ScholarshipRegistrationController::class, 'landing'])->name('show');
    Route::get('{slug}/register', [ScholarshipRegistrationController::class, 'show'])->name('register');
    Route::post('{slug}/register', [ScholarshipRegistrationController::class, 'register'])
        ->middleware('throttle:register')
        ->name('register.post');
});
