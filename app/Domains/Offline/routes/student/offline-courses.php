<?php

use App\Domains\Offline\Http\Controllers\Student\OfflineCourseBookingController;
use App\Domains\Offline\Http\Controllers\Student\OfflineCourseController;
use Illuminate\Support\Facades\Route;

/**
 * كورسات الطالب الأوفلاين.
 * يُحمَّل من routes/web/student.php وفي نفس موضعه القديم.
 */
Route::prefix('offline-courses')->name('student.offline-courses.')->group(function () {
    Route::get('/', [OfflineCourseController::class, 'index'])->name('index');
    Route::get('/booking/catalog', function () {
        return redirect()
            ->route('student.offline-courses.index')
            ->with('info', 'تم إيقاف صفحة كتالوج الحجز العام. للتسجيل تواصل مع الإدارة أو استخدم رابط الحجز المباشر إن وُجد.');
    });
    Route::get('/{offlineCourse}/booking/create', [OfflineCourseBookingController::class, 'create'])->name('booking.create');
    Route::post('/{offlineCourse}/booking', [OfflineCourseBookingController::class, 'store'])->name('booking.store');
    Route::get('/{offlineCourse}/curriculum', [OfflineCourseController::class, 'curriculum'])->name('curriculum');
    Route::get('/{offlineCourse}/schedule', [OfflineCourseController::class, 'schedule'])->name('schedule');
    Route::get('/{offlineCourse}/resources', [OfflineCourseController::class, 'resources'])->name('resources');
    Route::get('/{offlineCourse}/lectures', [OfflineCourseController::class, 'lectures'])->name('lectures');
    Route::get('/{offlineCourse}/lectures/{lecture}/watch', [OfflineCourseController::class, 'watchLectureRecording'])->name('lectures.watch');
    Route::get('/{offlineCourse}/lectures/{lecture}/stream', [OfflineCourseController::class, 'streamLectureRecording'])->name('lectures.stream');
    Route::get('/{offlineCourse}/activities/{activity}', [OfflineCourseController::class, 'activityShow'])->name('activities.show');
    Route::post('/{offlineCourse}/activities/{activity}/submit', [OfflineCourseController::class, 'activitySubmit'])->name('activities.submit');
    Route::get('/{offlineCourse}', [OfflineCourseController::class, 'show'])->name('show');
});

// كورسات الأونلاين (مجموعات أونلاين) — مسار وقائمة منفصلان؛ الظهور بعد تفعيل «بوابة الطالب» من الإدارة
Route::prefix('online-courses')->name('student.online-courses.')->group(function () {
    Route::get('/', [OfflineCourseController::class, 'index'])->name('index');
    Route::get('/{offlineCourse}/curriculum', [OfflineCourseController::class, 'curriculum'])->name('curriculum');
    Route::get('/{offlineCourse}/schedule', [OfflineCourseController::class, 'schedule'])->name('schedule');
    Route::get('/{offlineCourse}/resources', [OfflineCourseController::class, 'resources'])->name('resources');
    Route::get('/{offlineCourse}/lectures', [OfflineCourseController::class, 'lectures'])->name('lectures');
    Route::get('/{offlineCourse}/lectures/{lecture}/watch', [OfflineCourseController::class, 'watchLectureRecording'])->name('lectures.watch');
    Route::get('/{offlineCourse}/lectures/{lecture}/stream', [OfflineCourseController::class, 'streamLectureRecording'])->name('lectures.stream');
    Route::get('/{offlineCourse}/activities/{activity}', [OfflineCourseController::class, 'activityShow'])->name('activities.show');
    Route::post('/{offlineCourse}/activities/{activity}/submit', [OfflineCourseController::class, 'activitySubmit'])->name('activities.submit');
    Route::get('/{offlineCourse}', [OfflineCourseController::class, 'show'])->name('show');
});
