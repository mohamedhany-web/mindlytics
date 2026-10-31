<?php

use App\Domains\Community\Http\Controllers\Admin\AchievementController;
use App\Domains\Community\Http\Controllers\Admin\BadgeController;
use Illuminate\Support\Facades\Route;

/**
 * الإنجازات والأوسمة.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::resource('achievements', AchievementController::class);
Route::resource('badges', BadgeController::class);
