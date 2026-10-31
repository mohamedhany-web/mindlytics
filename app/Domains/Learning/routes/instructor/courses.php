<?php

use App\Domains\Learning\Http\Controllers\Instructor\CourseController;
use Illuminate\Support\Facades\Route;

/**
 * كورسات المدرب.
 * يُحمَّل من داخل مجموعة instructor في routes/web/instructor.php وفي نفس موضعه القديم.
 */
Route::resource('courses', CourseController::class)->only(['index', 'show']);
