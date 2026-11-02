<?php

use Illuminate\Support\Facades\Route;

/**
 * Admin panel. Prefix, name, and role middleware stay on the group in this file.
 * Loaded by routes/web.php. Route order is part of matching behavior.
 */

    // مسارات الإدارة - محمية بالـ role middleware للإداريين فقط
    Route::prefix('admin')->name('admin.')->middleware(['role:admin|super_admin'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('branches/rollout-plan', [\App\Http\Controllers\Admin\BranchController::class, 'rolloutPlan'])->name('branches.rollout-plan');
        Route::get('branch-managers/create', [\App\Http\Controllers\Admin\BranchController::class, 'createBranchManager'])->name('branch-managers.create');
        Route::post('branch-managers', [\App\Http\Controllers\Admin\BranchController::class, 'storeBranchManagerGlobal'])
            ->middleware('throttle:10,1')
            ->name('branch-managers.store');
        Route::post('branches/{branch}/branch-managers', [\App\Http\Controllers\Admin\BranchController::class, 'storeBranchManager'])
            ->middleware('throttle:10,1')
            ->name('branches.branch-managers.store');
        Route::resource('branches', \App\Http\Controllers\Admin\BranchController::class);

        require app_path('Domains/Cms/routes/admin/support-tickets.php');

        Route::prefix('mobile-app')->name('mobile-app.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\MobileAppSettingsController::class, 'edit'])->name('edit');
            Route::put('/', [\App\Http\Controllers\Admin\MobileAppSettingsController::class, 'update'])->name('update');
            Route::get('/notifications', [\App\Http\Controllers\Admin\MobileAppNotificationsController::class, 'index'])->name('notifications');
            Route::post('/notifications', [\App\Http\Controllers\Admin\MobileAppNotificationsController::class, 'store'])
                ->middleware('throttle:30,1')
                ->name('notifications.store');
            Route::get('/maintenance', [\App\Http\Controllers\Admin\MobileAppPagesController::class, 'maintenance'])->name('maintenance');
            Route::get('/links', [\App\Http\Controllers\Admin\MobileAppPagesController::class, 'links'])->name('links');
            Route::get('/appearance', [\App\Http\Controllers\Admin\MobileAppPagesController::class, 'appearance'])->name('appearance');

            require app_path('Domains/Community/routes/admin/course-community.php');
        });

        require app_path('Domains/Sales/routes/admin/sales.php');

        // نفس CRM المبيعات بنطاق كل البيانات (docs/SALES_CRM.md)
        Route::prefix('crm')->name('crm.')->group(function () {
            require app_path('Domains/Sales/routes/crm.php');
            require app_path('Domains/Sales/routes/admin/crm-settings.php');
        });

        // HR (Recruitment / ATS)
        require app_path('Domains/Hr/routes/admin/recruitment.php');

        // قسم المنح الدراسية
        require app_path('Domains/Scholarship/routes/admin.php');

        // الإرشاد المهني (docs/ADVISING.md)
        require app_path('Domains/Advising/routes/admin.php');

        // قسم الاستثمار
        require app_path('Domains/Investment/routes/admin.php');

        // بروفايل الأدمن
        Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

        // إدارة المستخدمين
        Route::get('/users', [\App\Http\Controllers\Admin\AdminController::class, 'users'])->name('users.index');
        Route::get('/users/create', [\App\Http\Controllers\Admin\AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [\App\Http\Controllers\Admin\AdminController::class, 'storeUser'])
            ->middleware('throttle:20,1')
            ->name('users.store');
        Route::get('/users/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'showUser'])->name('users.show')->where('id', '[0-9]+');
        Route::get('/users/{id}/edit', [\App\Http\Controllers\Admin\AdminController::class, 'editUser'])->name('users.edit')->where('id', '[0-9]+');
        Route::put('/users/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'updateUser'])->name('users.update')->where('id', '[0-9]+');
        Route::delete('/users/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'deleteUser'])->name('users.delete')->where('id', '[0-9]+');
        
        // إدارة السنوات الدراسية
        require app_path('Domains/Learning/routes/admin/catalog.php');

        require app_path('Domains/Sales/routes/admin/boards.php');

        // بناء المنهج (أدمن) + سياسة فتح الفيديوهات
        require app_path('Domains/Learning/routes/admin/curriculum.php');

        // الاجتماعات / الورش
        require app_path('Domains/Offline/routes/admin/workshops.php');
        require app_path('Domains/Messaging/routes/admin/workshop-whatsapp.php');
        require app_path('Domains/Offline/routes/admin/workshops-checkin.php');
        require app_path('Domains/Learning/routes/admin/courses-content.php');

        // إعدادات النظام (الهوية البصرية: اللوجو والأيقونة)
        Route::get('/system-settings', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'index'])->name('system-settings.index');
        Route::put('/system-settings', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'update'])->name('system-settings.update');

        // سجل النشاطات
        Route::get('/activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log');
        Route::get('/activity-log/{activityLog}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activity-log.show');
        Route::post('/activity-log/clear', [\App\Http\Controllers\Admin\ActivityLogController::class, 'destroy'])->name('activity-log.destroy');

        // سجل أخطاء المنصة
        Route::get('/platform-errors', [\App\Http\Controllers\Admin\PlatformErrorLogController::class, 'index'])->name('platform-errors.index');
        Route::get('/platform-errors/{platformError}', [\App\Http\Controllers\Admin\PlatformErrorLogController::class, 'show'])->name('platform-errors.show');
        Route::patch('/platform-errors/{platformError}/status', [\App\Http\Controllers\Admin\PlatformErrorLogController::class, 'updateStatus'])->name('platform-errors.update-status');
        Route::post('/platform-errors/bulk', [\App\Http\Controllers\Admin\PlatformErrorLogController::class, 'bulkUpdate'])->name('platform-errors.bulk');

        // سجلات التحقق الثنائي (2FA)
        Route::get('/two-factor-logs', [\App\Http\Controllers\Admin\TwoFactorLogController::class, 'index'])->name('two-factor-logs.index');

        // الإحصائيات
        Route::get('/statistics', [\App\Http\Controllers\Admin\StatisticsController::class, 'index'])->name('statistics.index');
        Route::get('/statistics/users', [\App\Http\Controllers\Admin\StatisticsController::class, 'users'])->name('statistics.users');
        Route::get('/statistics/courses', [\App\Http\Controllers\Admin\StatisticsController::class, 'courses'])->name('statistics.courses');

        // إدارة الطلبات
        require app_path('Domains/Accounting/routes/admin/orders.php');

        // إدارة الصلاحيات والأدوار
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
        Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class);
        Route::post('/roles/{role}/permissions', [\App\Http\Controllers\Admin\RoleController::class, 'updatePermissions'])->name('roles.update-permissions');
        
        // إدارة صلاحيات المستخدمين
        Route::get('/user-permissions', [\App\Http\Controllers\Admin\UserPermissionController::class, 'index'])->name('user-permissions.index');
        Route::get('/user-permissions/{user}', [\App\Http\Controllers\Admin\UserPermissionController::class, 'show'])->name('user-permissions.show');
        Route::put('/user-permissions/{user}', [\App\Http\Controllers\Admin\UserPermissionController::class, 'update'])->name('user-permissions.update');
        Route::post('/user-permissions/{user}/attach', [\App\Http\Controllers\Admin\UserPermissionController::class, 'attachPermission'])->name('user-permissions.attach');
        Route::post('/user-permissions/{user}/detach', [\App\Http\Controllers\Admin\UserPermissionController::class, 'detachPermission'])->name('user-permissions.detach');

        // إدارة المحافظ الذكية
        require app_path('Domains/Accounting/routes/admin/wallets.php');

        // إدارة المحاضرات والجروبات
        require app_path('Domains/Learning/routes/admin/lectures-tasks.php');

        // إدارة الصفحات الخارجية
        require app_path('Domains/Cms/routes/admin/blog.php');
        // البورتفوليو - الرقابة والجودة (الأدمن يرى الكل ويمكنه إخفاء مشروع)
        require app_path('Domains/Community/routes/admin/portfolio.php');

        // قسم التدريب — Internships
        require app_path('Domains/Hr/routes/admin/internships.php');

        // مجتمع البيانات والذكاء الاصطناعي — للإدارة العليا فقط (صلاحية super_admin أو admin)
        require app_path('Domains/Community/routes/admin/community.php');

        // الإدارة العليا (من نحن وغيرها)
        Route::get('about', [\App\Http\Controllers\Admin\AboutPageController::class, 'index'])->name('about.index');
        Route::get('about/view', [\App\Http\Controllers\Admin\AboutPageController::class, 'viewPublic'])->name('about.view-public');

        require app_path('Domains/Cms/routes/admin/contact-messages.php');
        
        // إدارة الأسعار والباقات
        require app_path('Domains/Accounting/routes/admin/packages.php');

        // إدارة الإشعارات
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])
                ->middleware('throttle:20,5')
                ->name('store');
            Route::get('/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('show');
            Route::delete('/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])
                ->middleware('throttle:30,1')
                ->name('destroy');
            Route::post('/quick-send', [\App\Http\Controllers\Admin\NotificationController::class, 'quickSend'])
                ->middleware('throttle:30,5')
                ->name('quick-send');
            Route::get('/target-count', [\App\Http\Controllers\Admin\NotificationController::class, 'getTargetCount'])
                ->middleware('throttle:60,1')
                ->name('target-count');
            Route::post('/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])
                ->middleware('throttle:10,1')
                ->name('mark-all-read');
            Route::post('/cleanup', [\App\Http\Controllers\Admin\NotificationController::class, 'cleanup'])
                ->middleware('throttle:5,10')
                ->name('cleanup');
            Route::get('/statistics', [\App\Http\Controllers\Admin\NotificationController::class, 'statistics'])->name('statistics');
        });

        // إشعارات الموظفين
        Route::prefix('employee-notifications')->name('employee-notifications.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\EmployeeNotificationController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\EmployeeNotificationController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\EmployeeNotificationController::class, 'store'])
                ->middleware('throttle:10,1')
                ->name('store');
            Route::get('/{notification}', [\App\Http\Controllers\Admin\EmployeeNotificationController::class, 'show'])->name('show');
        });

        // إدارة تسجيل الطلاب في الكورسات الأونلاين
        require app_path('Domains/Learning/routes/admin/online-enrollments.php');

        // إدارة تسجيل الطلاب في الكورسات الأوفلاين
        require app_path('Domains/Offline/routes/admin/enrollments-and-online.php');

        // إدارة تسجيل الطلاب في المسارات التعليمية
        require app_path('Domains/Learning/routes/admin/learning-path-enrollments.php');

        // إدارة الأماكن للأوفلاين
        require app_path('Domains/Offline/routes/admin/locations.php');

        require app_path('Domains/Places/routes/admin.php');

        // إدارة الكورسات الأوفلاين
        require app_path('Domains/Offline/routes/admin/offline-courses.php');

        // إدارة الأنشطة الأوفلاين
        Route::prefix('offline-courses/{offlineCourse}/activities')->name('offline-courses.activities.')->group(function () {
            // Route::get('/', [\App\Http\Controllers\Admin\OfflineActivityController::class, 'index'])->name('index');
            // Route::get('/create', [\App\Http\Controllers\Admin\OfflineActivityController::class, 'create'])->name('create');
            // Route::post('/', [\App\Http\Controllers\Admin\OfflineActivityController::class, 'store'])->name('store');
            // Route::get('/{activity}', [\App\Http\Controllers\Admin\OfflineActivityController::class, 'show'])->name('show');
            // Route::get('/{activity}/edit', [\App\Http\Controllers\Admin\OfflineActivityController::class, 'edit'])->name('edit');
            // Route::put('/{activity}', [\App\Http\Controllers\Admin\OfflineActivityController::class, 'update'])->name('update');
            // Route::delete('/{activity}', [\App\Http\Controllers\Admin\OfflineActivityController::class, 'destroy'])->name('destroy');
        });

        // إدارة اتفاقيات المدربين للأوفلاين
        require app_path('Domains/Accounting/routes/admin/offline-agreements.php');

        // إدارة الموظفين
        require app_path('Domains/Hr/routes/admin/employees.php');

        require app_path('Domains/Marketing/routes/admin/production.php');

        require app_path('Domains/Hr/routes/admin/deductions.php');

        require app_path('Domains/Hr/routes/admin/daily-reports.php');
        
        // إدارة الإجازات
        require app_path('Domains/Hr/routes/admin/leaves.php');

        // طلبات المدربين للإدارة
        require app_path('Domains/Learning/routes/admin/instructor-requests.php');

        // الرقابة والجودة
        Route::prefix('quality-control')->name('quality-control.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\QualityControlController::class, 'index'])->name('index');
            Route::get('/students', [\App\Http\Controllers\Admin\QualityControlController::class, 'students'])->name('students');
            Route::get('/instructors', [\App\Http\Controllers\Admin\QualityControlController::class, 'instructors'])->name('instructors');
            Route::get('/instructors/{instructor}', [\App\Http\Controllers\Admin\QualityControlController::class, 'instructorShow'])->name('instructors.show');
            Route::get('/instructors/{instructor}/export', [\App\Http\Controllers\Admin\QualityControlController::class, 'instructorExport'])->name('instructors.export');
            Route::get('/employees', [\App\Http\Controllers\Admin\QualityControlController::class, 'employees'])->name('employees');
            Route::get('/operations', [\App\Http\Controllers\Admin\QualityControlController::class, 'operations'])->name('operations');
        });

        // قسم الواتساب — Meta Cloud API (رسمي)
        require app_path('Domains/Messaging/routes/admin/whatsapp.php');

        // إدارة السوشيال — Meta Graph (Facebook Pages + Messenger + Instagram)
        require app_path('Domains/Messaging/routes/admin/meta-social.php');

        // إدارة الرسائل والتقارير
        require app_path('Domains/Messaging/routes/admin/messages.php');

        // إدارة المحاسبة
        require app_path('Domains/Accounting/routes/admin/accounting.php');

        // رواتب الموظفين — مسير شهري ودفع من المحفظة
        require app_path('Domains/Hr/routes/admin/salaries.php');

        // الماليات الخاصة بالمدربين (قائمة المدربين ثم المطلوب دفعه لكل مدرب)
        require app_path('Domains/Accounting/routes/admin/salaries.php');

        // التقارير الشاملة
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('index');
            Route::get('/users', [\App\Http\Controllers\Admin\ReportsController::class, 'users'])->name('users');
            Route::get('/courses', [\App\Http\Controllers\Admin\ReportsController::class, 'courses'])->name('courses');
            Route::get('/financial', [\App\Http\Controllers\Admin\ReportsController::class, 'financial'])->name('financial');
            Route::get('/academic', [\App\Http\Controllers\Admin\ReportsController::class, 'academic'])->name('academic');
            Route::get('/activities', [\App\Http\Controllers\Admin\ReportsController::class, 'activities'])->name('activities');
            Route::get('/comprehensive', [\App\Http\Controllers\Admin\ReportsController::class, 'comprehensive'])->name('comprehensive');
            
            // تصدير التقارير
            Route::get('/export/users', [\App\Http\Controllers\Admin\ReportsController::class, 'exportUsers'])
                ->middleware('throttle:10,5')
                ->name('export.users');
            Route::get('/export/courses', [\App\Http\Controllers\Admin\ReportsController::class, 'exportCourses'])
                ->middleware('throttle:10,5')
                ->name('export.courses');
            Route::get('/export/financial', [\App\Http\Controllers\Admin\ReportsController::class, 'exportFinancial'])
                ->middleware('throttle:10,5')
                ->name('export.financial');
            Route::get('/export/comprehensive', [\App\Http\Controllers\Admin\ReportsController::class, 'exportComprehensive'])
                ->middleware('throttle:5,10')
                ->name('export.comprehensive');
        });
        require app_path('Domains/Accounting/routes/admin/installments.php');

        // نظام الاتفاقيات للمدربين
        require app_path('Domains/Accounting/routes/admin/instructor-agreements.php');

        // نظام اتفاقيات الموظفين
        require app_path('Domains/Hr/routes/admin/agreements.php');
        
        // إدارة طلبات السحب للمدربين
        require app_path('Domains/Accounting/routes/admin/withdrawals.php');

        // إدارة التسويق
        require app_path('Domains/Marketing/routes/admin/marketing.php');

        require app_path('Domains/Messaging/routes/admin/meta-ads.php');

        require app_path('Domains/Marketing/routes/admin/reviews-and-surveys.php');
        require app_path('Domains/Accounting/routes/admin/coupons.php');
        // إدارة برامج الإحالات
        require app_path('Domains/Marketing/routes/admin/referral-programs.php');
        require app_path('Domains/Offline/routes/admin/workshop-promo-codes.php');
        
        // إدارة الإحالات
        require app_path('Domains/Marketing/routes/admin/referrals-loyalty.php');

        // إدارة الشهادات والإنجازات
        require app_path('Domains/Learning/routes/admin/certificates.php');
        require app_path('Domains/Community/routes/admin/achievements.php');
        require app_path('Domains/Learning/routes/admin/reviews-paths-groups.php');

        // إدارة الأداء
        Route::prefix('performance')->name('performance.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PerformanceController::class, 'index'])->name('index');
            Route::post('/clear-cache', [\App\Http\Controllers\Admin\PerformanceController::class, 'clearCache'])
                ->middleware('throttle:10,1')
                ->name('clear-cache');
            Route::post('/optimize-cache', [\App\Http\Controllers\Admin\PerformanceController::class, 'optimizeCache'])
                ->middleware('throttle:5,5')
                ->name('optimize-cache');
            Route::post('/clear-temp-files', [\App\Http\Controllers\Admin\PerformanceController::class, 'clearTempFiles'])
                ->middleware('throttle:5,5')
                ->name('clear-temp-files');
            Route::post('/optimize-database', [\App\Http\Controllers\Admin\PerformanceController::class, 'optimizeDatabase'])
                ->middleware('throttle:3,10')
                ->name('optimize-database');
        });

    });
