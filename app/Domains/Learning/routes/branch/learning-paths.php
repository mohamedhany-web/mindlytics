<?php

use App\Domains\Learning\Http\Controllers\Branch\BranchLearningPathsController;
use Illuminate\Support\Facades\Route;

/**
 * مسارات الفرع التعليمية.
 * يُحمَّل من داخل مجموعة branch-office في routes/web/portal.php وفي نفس موضعه القديم.
 */
Route::get('/learning-paths', [BranchLearningPathsController::class, 'index'])->name('learning-paths');
