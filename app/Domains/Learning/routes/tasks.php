<?php

use App\Domains\Learning\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/**
 * المهام (TaskController).
 * يُحمَّل من routes/web/tasks.php في نفس موضعه القديم.
 */
Route::resource('tasks', TaskController::class);
