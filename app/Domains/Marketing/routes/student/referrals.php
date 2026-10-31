<?php

use App\Domains\Marketing\Http\Controllers\Student\ReferralController;
use Illuminate\Support\Facades\Route;

/**
 * إحالات الطالب.
 * يُحمَّل من routes/web/student.php في نفس موضعه القديم.
 */
Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');
Route::post('/referrals/copy-link', [ReferralController::class, 'copyLink'])->name('referrals.copy-link');
