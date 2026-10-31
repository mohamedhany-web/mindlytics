<?php

use App\Domains\Offline\Http\Controllers\Branch\BranchOfflineCoursesController;
use Illuminate\Support\Facades\Route;

/**
 * كورسات الفرع الأوفلاين.
 * يُحمَّل من داخل مجموعة branch-office في routes/web/portal.php وفي نفس موضعه القديم.
 */
Route::get('/courses-offline', [BranchOfflineCoursesController::class, 'index'])->name('courses-offline');
