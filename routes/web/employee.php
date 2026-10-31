<?php

use Illuminate\Support\Facades\Route;

/**
 * Employee portal, including sales, moderator, and attendance routes.
 * Loaded by routes/web.php. Route order is part of matching behavior.
 */

    // لوحة الموظفين
    Route::prefix('employee')->name('employee.')->middleware(['auth', 'employee.work'])->group(function () {
        require app_path('Domains/Hr/routes/employee/attendance.php');

        require app_path('Domains/Sales/routes/employee/team-chat.php');

        Route::get('/dashboard', [\App\Http\Controllers\Employee\EmployeeController::class, 'dashboard'])->name('dashboard');

        Route::middleware('sales.employee')->prefix('sales')->name('sales.')->group(function () {
            require app_path('Domains/Sales/routes/employee/sales.php');

            require app_path('Domains/Messaging/routes/employee/whatsapp-inbox.php');
        });

        require app_path('Domains/Sales/routes/employee/staff.php');

        // CRM الموحّد لموظف ومدير المبيعات (docs/SALES_CRM.md)
        Route::middleware('sales.staff')->prefix('crm')->name('crm.')->group(function () {
            require app_path('Domains/Sales/routes/crm.php');
        });

        // Messenger & Instagram لموظفي/مديري المبيعات (كل الرسايل — الربط عند الرد/الأكشن)
        require app_path('Domains/Messaging/routes/employee/meta-social-inbox.php');

        Route::middleware('sales.manager')->prefix('sales-manager')->name('sales-manager.')->group(function () {
            require app_path('Domains/Sales/routes/employee/manager.php');
            require app_path('Domains/Messaging/routes/employee/manager-whatsapp.php');
        });

        require app_path('Domains/Marketing/routes/employee/moderation.php');

        require app_path('Domains/Hr/routes/employee/tasks.php');
        
        // طلبات الإجازة
        require app_path('Domains/Hr/routes/employee/leaves.php');
        
        // المحاسبة والراتب
        require app_path('Domains/Accounting/routes/employee/accounting.php');

        require app_path('Domains/Hr/routes/employee/daily-reports.php');
        
        // اتفاقيات الموظف
        require app_path('Domains/Hr/routes/employee/agreements.php');
        
        // الملف الشخصي
        Route::get('/profile', [\App\Http\Controllers\Employee\EmployeeProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Employee\EmployeeProfileController::class, 'update'])->name('profile.update');
        
        // الإشعارات
        Route::get('/notifications', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'index'])->name('notifications');
        Route::get('/notifications/{notification}/go', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'go'])->name('notifications.go');
        Route::get('/notifications/{notification}', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'show'])->name('notifications.show');
        Route::post('/notifications/{notification}/mark-read', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        
        // مهام التسويق اليومية (تأكيد التنفيذ)
        require app_path('Domains/Marketing/routes/employee/marketing-today.php');

        // التقويم
        Route::get('/calendar', [\App\Http\Controllers\Employee\EmployeeCalendarController::class, 'index'])->name('calendar');
        Route::get('/api/calendar/events', [\App\Http\Controllers\Employee\EmployeeCalendarController::class, 'getEvents'])->name('calendar.events');
        
        // التقارير والإحصائيات
        require app_path('Domains/Hr/routes/employee/reports.php');
        Route::get('/documentation', [\App\Http\Controllers\Employee\EmployeeController::class, 'documentation'])->name('documentation');
        Route::get('/business-developer/guide', [\App\Http\Controllers\Employee\EmployeeController::class, 'businessDeveloperGuide'])->name('business-developer.guide');
        require app_path('Domains/Marketing/routes/employee/business-developer.php');
        
        // الإعدادات
        Route::get('/settings', [\App\Http\Controllers\Employee\EmployeeSettingsController::class, 'index'])->name('settings');
        Route::put('/settings', [\App\Http\Controllers\Employee\EmployeeSettingsController::class, 'update'])->name('settings.update');
        
        // API للإشعارات
        Route::get('/api/notifications/unread', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'getUnread'])->name('notifications.unread');
        Route::post('/api/notifications/{notification}/mark-read', [\App\Http\Controllers\Employee\EmployeeNotificationController::class, 'markAsRead'])->name('notifications.api.mark-read');
    });
