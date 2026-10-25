<?php

use Illuminate\Support\Facades\Route;

/**
 * Instructor portal. Prefix, name, and role middleware stay on the group in this file.
 * Loaded by routes/web.php. Route order is part of matching behavior.
 */

    // مسارات المدرسين
    Route::prefix('instructor')->name('instructor.')->middleware(['auth', 'role:instructor|teacher'])->group(function () {
        // بروفايل المدرب
        Route::get('/profile', [\App\Http\Controllers\Instructor\ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Instructor\ProfileController::class, 'update'])->name('profile.update');

        // التسويق الشخصي (البراندينغ) — ملف تعريفي للمدرب للمراجعة والنشر
        Route::get('/personal-branding', [\App\Http\Controllers\Instructor\PersonalBrandingController::class, 'edit'])->name('personal-branding.edit');
        Route::put('/personal-branding', [\App\Http\Controllers\Instructor\PersonalBrandingController::class, 'update'])->name('personal-branding.update');
        Route::post('/personal-branding/submit', [\App\Http\Controllers\Instructor\PersonalBrandingController::class, 'submit'])->name('personal-branding.submit');

        require app_path('Domains/Learning/routes/instructor/courses.php');
        require app_path('Domains/Scholarship/routes/instructor.php');
        require app_path('Domains/Learning/routes/instructor/mind-map.php');
        require app_path('Domains/Offline/routes/instructor/offline.php');
        require app_path('Domains/Learning/routes/instructor/teaching.php');
        
        // نظام الاتفاقيات للمدرب
        require app_path('Domains/Accounting/routes/instructor/agreements.php');

        // مراجعة مشاريع الرحلة / البورتفوليو (المدرب يراجع ثم ينشر)
        require app_path('Domains/Community/routes/instructor/portfolio.php');
    });
