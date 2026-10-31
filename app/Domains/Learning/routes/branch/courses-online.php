<?php

use App\Domains\Learning\Http\Controllers\Branch\BranchOnlineCoursesController;
use Illuminate\Support\Facades\Route;

/**
 * كورسات الفرع الأونلاين.
 * يُحمَّل من داخل مجموعة branch-office في routes/web/portal.php وفي نفس موضعه القديم.
 */
Route::get('/courses-online', [BranchOnlineCoursesController::class, 'index'])->name('courses-online');
