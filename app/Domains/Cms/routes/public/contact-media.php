<?php

use App\Domains\Cms\Http\Controllers\Public\ContactController;
use App\Domains\Cms\Http\Controllers\Public\MediaController;
use Illuminate\Support\Facades\Route;

/**
 * صفحة التواصل والوسائط العامة.
 * يُحمَّل من routes/web/public.php في نفس موضعه القديم.
 */
Route::get('/contact', [ContactController::class, 'index'])->name('public.contact');
Route::post('/contact', [ContactController::class, 'store'])->name('public.contact.store');

// معرض الصور والفيديوهات
Route::get('/media', [MediaController::class, 'index'])->name('public.media.index');
Route::get('/media/{media}', [MediaController::class, 'show'])->name('public.media.show');
