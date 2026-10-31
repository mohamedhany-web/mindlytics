<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

/**
 * Shared authenticated entry: dashboard, branch office, place office, navbar notifications.
 * Loaded by routes/web.php. Route order is part of matching behavior.
 */

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // لوحة مدير الفرع (بيانات الفرع المرتبط بحسابه فقط)
    Route::prefix('branch-office')->name('branch.office.')->middleware(['role:branch_manager', 'branch.office'])->group(function () {
        Route::get('/', function () {
            return redirect()->route('branch.office.dashboard');
        });
        Route::get('/dashboard', [\App\Http\Controllers\Branch\BranchDashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [\App\Http\Controllers\Branch\BranchUsersController::class, 'index'])->name('users');
        require app_path('Domains/Accounting/routes/branch/orders.php');
        require app_path('Domains/Learning/routes/branch/courses-online.php');
        require app_path('Domains/Offline/routes/branch/courses-offline.php');
        require app_path('Domains/Learning/routes/branch/learning-paths.php');
        require app_path('Domains/Accounting/routes/branch/finance.php');
    });

    // لوحة مدير المكان الإداري (تسجيل الساعات والمخالصة الشهرية)
    require app_path('Domains/Places/routes/office.php');

    // API لإشعارات الناف بار (تعمل للطالب/المدرب/الموظف)
    Route::prefix('api/nav-notifications')->name('nav-notifications.')->group(function () {
        Route::get('/unread-count', [\App\Http\Controllers\NavbarNotificationController::class, 'unreadCount'])->name('unread-count');
        Route::get('/recent', [\App\Http\Controllers\NavbarNotificationController::class, 'recent'])->name('recent');
        Route::post('/{notification}/mark-read', [\App\Http\Controllers\NavbarNotificationController::class, 'markRead'])->name('mark-read');
        Route::post('/mark-all-read', [\App\Http\Controllers\NavbarNotificationController::class, 'markAllRead'])->name('mark-all-read');
    });
