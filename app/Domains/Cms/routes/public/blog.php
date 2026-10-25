<?php

use App\Domains\Cms\Http\Controllers\Public\BlogController;
use Illuminate\Support\Facades\Route;

/**
 * المدونة العامة.
 * يُحمَّل من routes/web/public.php في نفس موضعه القديم.
 */
Route::get('/blog', [BlogController::class, 'index'])->name('public.blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('public.blog.show');
