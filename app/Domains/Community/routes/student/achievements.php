<?php

use App\Domains\Community\Http\Controllers\Student\AchievementController;
use Illuminate\Support\Facades\Route;

/**
 * إنجازات الطالب.
 * يُحمَّل من داخل مجموعة student في routes/web/student-account.php وفي نفس موضعه القديم.
 */
Route::resource('achievements', AchievementController::class)->only(['index', 'show']);
