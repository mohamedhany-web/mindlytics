<?php

use App\Domains\Offline\Http\Controllers\Admin\OfflineCourseController;
use App\Domains\Offline\Http\Controllers\Admin\OfflineEnrollmentController;
use App\Domains\Offline\Http\Controllers\Admin\OfflineGroupController;
use Illuminate\Support\Facades\Route;

/**
 * الكورسات الأوفلاين ومجموعاتها وحجوزاتها.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::resource('offline-courses', OfflineCourseController::class);
        
// إدارة المجموعات للكورسات الأوفلاين
Route::prefix('offline-courses/{offlineCourse}/groups')->name('offline-courses.groups.')->group(function () {
    Route::get('/', [OfflineGroupController::class, 'index'])->name('index');
    Route::post('/', [OfflineGroupController::class, 'store'])->name('store');
    Route::put('/{group}', [OfflineGroupController::class, 'update'])->name('update');
    Route::delete('/{group}', [OfflineGroupController::class, 'destroy'])->name('destroy');

    // جلسات المجموعة
    Route::post('/{group}/sessions', [OfflineGroupController::class, 'storeSession'])->name('sessions.store');
    Route::put('/{group}/sessions/{session}', [OfflineGroupController::class, 'updateSession'])->name('sessions.update');
    Route::delete('/{group}/sessions/{session}', [OfflineGroupController::class, 'destroySession'])->name('sessions.destroy');
    Route::post('/{group}/sessions/bulk', [OfflineGroupController::class, 'bulkCreateSessions'])->name('sessions.bulk');
});

// إدارة التسجيلات في الكورسات الأوفلاين
Route::prefix('offline-courses/{offlineCourse}/enrollments')->name('offline-courses.enrollments.')->group(function () {
    Route::get('/', [OfflineEnrollmentController::class, 'index'])->name('index');
    Route::post('/', [OfflineEnrollmentController::class, 'store'])->name('store');
    Route::put('/{enrollment}/status', [OfflineEnrollmentController::class, 'updateStatus'])->name('update-status');
    Route::put('/{enrollment}/financial', [OfflineEnrollmentController::class, 'updateFinancial'])->name('update-financial');
    Route::post('/{enrollment}/payment', [OfflineEnrollmentController::class, 'addPayment'])->name('add-payment');
    Route::post('/{enrollment}/refund', [OfflineEnrollmentController::class, 'refund'])->name('refund');
    Route::delete('/{enrollment}', [OfflineEnrollmentController::class, 'destroy'])->name('destroy');
});
