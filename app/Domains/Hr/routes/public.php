<?php

use App\Domains\Hr\Http\Controllers\Public\CareersController;
use App\Domains\Hr\Http\Controllers\Public\InternshipController;
use App\Domains\Hr\Models\HrJobPosting;
use Illuminate\Support\Facades\Route;

/**
 * التوظيف العام (careers) والتدريب العام (internships).
 * يُحمَّل من routes/web/public.php في نفس موضعه القديم.
 */
Route::get('/careers', [CareersController::class, 'index'])->name('careers.index');
Route::get('/careers/{job}', [CareersController::class, 'show'])->name('careers.show');
Route::get('/careers/{job}/apply', function (HrJobPosting $job) {
    abort_unless($job->is_published && $job->isOpen(), 404);

    return redirect()->route('careers.show', $job);
})->name('careers.apply.form');
Route::post('/careers/{job}/apply', [CareersController::class, 'apply'])
    ->middleware('throttle:20,1')
    ->name('careers.apply');

// Internships / التدريب (Public)
Route::get('/internships', [InternshipController::class, 'index'])->name('public.internships.index');
Route::get('/internships/{slug}', [InternshipController::class, 'show'])->name('public.internships.show')->where('slug', '[A-Za-z0-9\-]+');
Route::post('/internships/{slug}/apply', [InternshipController::class, 'apply'])
    ->middleware('throttle:20,1')
    ->name('public.internships.apply')
    ->where('slug', '[A-Za-z0-9\-]+');
