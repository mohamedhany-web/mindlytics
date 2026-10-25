<?php

use App\Domains\Community\Http\Controllers\Public\ChallengesController;
use Illuminate\Support\Facades\Route;

/**
 * التحديات العامة.
 * يُحمَّل من routes/web/public.php في نفس موضعه القديم.
 */
Route::get('/challenges', [ChallengesController::class, 'index'])->name('public.challenges');
