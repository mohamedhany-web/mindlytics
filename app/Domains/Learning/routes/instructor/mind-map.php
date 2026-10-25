<?php

use App\Domains\Learning\Http\Controllers\Instructor\CourseMindMapController;
use Illuminate\Support\Facades\Route;

/**
 * خريطة الكورس الذهنية.
 * يُحمَّل من داخل مجموعة instructor في routes/web/instructor.php وفي نفس موضعه القديم.
 */
Route::get('courses/{course}/mind-map', [CourseMindMapController::class, 'edit'])->name('courses.mind-map.edit');
Route::put('courses/{course}/mind-map', [CourseMindMapController::class, 'update'])->name('courses.mind-map.update');
