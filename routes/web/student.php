<?php

use Illuminate\Support\Facades\Route;

/**
 * Student learning routes. Loaded inside the auth + prevent-concurrent group.
 * Loaded by routes/web.php. Route order is part of matching behavior.
 */

    // مسارات الطلاب
    require app_path('Domains/Learning/routes/student/catalog.php');
    
        // كورساتي المفعلة - محمية للطلاب فقط
        Route::middleware(['role:student'])->group(function () {
            require app_path('Domains/Learning/routes/student/my-courses.php');
            
            // الكورسات الأوفلاين للطلاب (مسار مستقل عن الأونلاين)
            require app_path('Domains/Offline/routes/student/offline-courses.php');

            // المسار التعليمي للطالب — القائمة ثم التفاصيل
            require app_path('Domains/Learning/routes/student/learning.php');
    });
    
    // الإحالات
    require app_path('Domains/Marketing/routes/student/referrals.php');
    require app_path('Domains/Offline/routes/student/promo-code.php');
    
    // API للتحقق من الكوبون
    require app_path('Domains/Accounting/routes/student/validate-coupon.php');
    
    // API لمعلومات الفيديو
    require app_path('Domains/Learning/routes/student/api.php');

    // نظام الطلبات - محمي للطلاب فقط
    require app_path('Domains/Accounting/routes/student/orders.php');
    
    // امتحانات الطلاب - محمية للطلاب فقط
    require app_path('Domains/Learning/routes/student/exams.php');

    // صفحات الطلاب - محمية للطلاب فقط
    Route::middleware(['role:student'])->group(function () {
        Route::get('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/settings', [\App\Http\Controllers\Student\SettingsController::class, 'index'])->name('settings');
        Route::get('/notifications', [\App\Http\Controllers\Student\NotificationController::class, 'index'])->name('notifications');
        Route::get('/notifications/{notification}/go', [\App\Http\Controllers\Student\NotificationController::class, 'go'])
            ->name('notifications.go');
        Route::get('/notifications/{notification}', [\App\Http\Controllers\Student\NotificationController::class, 'show'])
            ->middleware(['ownership:notification,notification'])
            ->name('notifications.show');
        Route::post('/notifications/{notification}/mark-read', [\App\Http\Controllers\Student\NotificationController::class, 'markAsRead'])
            ->middleware(['ownership:notification,notification'])
            ->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Student\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::delete('/notifications/{notification}', [\App\Http\Controllers\Student\NotificationController::class, 'destroy'])
            ->middleware(['ownership:notification,notification'])
            ->name('notifications.destroy');
        Route::post('/notifications/cleanup', [\App\Http\Controllers\Student\NotificationController::class, 'cleanup'])->name('notifications.cleanup');
        Route::get('/api/notifications/unread-count', [\App\Http\Controllers\Student\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
        Route::get('/api/notifications/recent', [\App\Http\Controllers\Student\NotificationController::class, 'getRecent'])->name('notifications.recent');
        Route::get('/calendar', [\App\Http\Controllers\Student\CalendarController::class, 'index'])->name('calendar');
        Route::get('/api/calendar/events', [\App\Http\Controllers\Student\CalendarController::class, 'getEvents'])->name('calendar.events');
        Route::get('/mobile-app', function () {
            return view('student.mobile-app');
        })->name('mobile-app');
        // رحلة التعلم / البورتفوليو - مشاريع الطالب
        require app_path('Domains/Community/routes/student/portfolio.php');
    });
