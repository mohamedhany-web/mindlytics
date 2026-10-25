<?php

use App\Domains\Offline\Http\Controllers\Admin\OfflineCourseBookingController;
use App\Domains\Offline\Http\Controllers\Admin\OfflineEnrollmentsController;
use App\Domains\Offline\Http\Controllers\Admin\OnlineCourseBookingController;
use App\Domains\Offline\Http\Controllers\Admin\OnlineManagementController;
use Illuminate\Support\Facades\Route;

/**
 * تسجيلات الأوفلاين، حجوزات وإدارة المجموعات الأونلاين.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::prefix('offline-enrollments')->name('offline-enrollments.')->group(function () {
    Route::get('/', [OfflineEnrollmentsController::class, 'index'])->name('index');
    Route::get('/create', [OfflineEnrollmentsController::class, 'create'])->name('create');
    Route::post('/', [OfflineEnrollmentsController::class, 'store'])->name('store');
    Route::get('/{offlineEnrollment}', [OfflineEnrollmentsController::class, 'show'])->name('show');
    Route::put('/{offlineEnrollment}/status', [OfflineEnrollmentsController::class, 'updateStatus'])->name('update-status');
    Route::delete('/{offlineEnrollment}', [OfflineEnrollmentsController::class, 'destroy'])->name('destroy');
});

Route::prefix('offline-course-bookings')->name('offline-course-bookings.')->group(function () {
    Route::get('/', [OfflineCourseBookingController::class, 'index'])->name('index');
    Route::get('/{offlineCourseBooking}', [OfflineCourseBookingController::class, 'show'])->name('show');
    Route::post('/{offlineCourseBooking}/approve', [OfflineCourseBookingController::class, 'approve'])->name('approve');
    Route::post('/{offlineCourseBooking}/reject', [OfflineCourseBookingController::class, 'reject'])->name('reject');
});
Route::prefix('online-course-bookings')->name('online-course-bookings.')->group(function () {
    Route::get('/', [OnlineCourseBookingController::class, 'index'])->name('index');
    Route::get('/{offlineCourseBooking}', [OnlineCourseBookingController::class, 'show'])->name('show');
    Route::post('/{offlineCourseBooking}/approve', [OnlineCourseBookingController::class, 'approve'])->name('approve');
    Route::post('/{offlineCourseBooking}/reject', [OnlineCourseBookingController::class, 'reject'])->name('reject');
});

// إدارة الأونلاين — كورسات بمجموعات مفعّل لها الأونلاين + كورس أونلاين فقط + تسجيل بالإيميل
Route::prefix('online-management')->name('online-management.')->group(function () {
    Route::get('/', [OnlineManagementController::class, 'index'])->name('index');
    Route::get('/courses/create', [OnlineManagementController::class, 'createCourse'])->name('courses.create');
    Route::post('/courses', [OnlineManagementController::class, 'storeCourse'])->name('courses.store');
    Route::get('/enroll', [OnlineManagementController::class, 'enrollForm'])->name('enroll');
    Route::post('/enroll', [OnlineManagementController::class, 'enrollStore'])->name('enroll.store');
});
