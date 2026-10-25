<?php

use App\Domains\Hr\Http\Controllers\Admin\InternshipApplicationController;
use App\Domains\Hr\Http\Controllers\Admin\InternshipController;
use Illuminate\Support\Facades\Route;

/**
 * التدريب (Internships) وطلباته.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::resource('internships', InternshipController::class)->except(['show']);
Route::get('internship-applications', [InternshipApplicationController::class, 'index'])->name('internship-applications.index');
Route::get('internship-applications/{application}', [InternshipApplicationController::class, 'show'])->name('internship-applications.show');
Route::put('internship-applications/{application}/status', [InternshipApplicationController::class, 'updateStatus'])->name('internship-applications.status');
Route::delete('internship-applications/{application}', [InternshipApplicationController::class, 'destroy'])->name('internship-applications.destroy');
