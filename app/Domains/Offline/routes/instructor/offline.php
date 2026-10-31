<?php

use App\Domains\Offline\Http\Controllers\Instructor\OfflineActivityController;
use App\Domains\Offline\Http\Controllers\Instructor\OfflineCourseController;
use App\Domains\Offline\Http\Controllers\Instructor\OfflineCurriculumController;
use App\Domains\Offline\Http\Controllers\Instructor\OfflineLectureController;
use App\Domains\Offline\Http\Controllers\Instructor\OfflineResourceController;
use Illuminate\Support\Facades\Route;

/**
 * كورسات المدرب الأوفلاين والأونلاين الجماعية، التقويم، المحاضرات والموارد والأنشطة والمنهج.
 * يُحمَّل من داخل مجموعة instructor في routes/web/instructor.php وفي نفس موضعه القديم.
 */
Route::get('online-group-courses', [OfflineCourseController::class, 'onlineIndex'])->name('online-group-courses.index');
Route::get('online-group-courses/{offlineCourse}', [OfflineCourseController::class, 'onlineShow'])->name('online-group-courses.show');
Route::resource('offline-courses', OfflineCourseController::class)->only(['index', 'show'])->parameters(['offline_course' => 'offlineCourse']);

// تقويم المدرب (جلسات الأوفلاين)
Route::get('/calendar', [OfflineCourseController::class, 'calendar'])->name('calendar');
Route::get('/api/calendar/events', [OfflineCourseController::class, 'calendarEvents'])->name('calendar.events');
// موارد ومحاضرات وأنشطة الكورسات الأوفلاين (واجهات منفصلة عن الأونلاين)
Route::prefix('offline-courses/{offlineCourse}')->name('offline-courses.')->group(function () {
    Route::resource('resources', OfflineResourceController::class)->except(['show'])->parameters(['resource' => 'resource']);
    Route::get('lectures/sessions/{session}', [OfflineLectureController::class, 'showGroupSession'])->name('lectures.sessions.show');
    Route::post('lectures/recording-upload-url', [OfflineLectureController::class, 'createRecordingUploadUrl'])->name('lectures.recording-upload-url');
    Route::post('lectures/recording-upload', [OfflineLectureController::class, 'uploadRecording'])->name('lectures.recording-upload');
    Route::get('lectures/{lecture}/watch', [OfflineLectureController::class, 'watchRecording'])->name('lectures.watch');
    Route::get('lectures/{lecture}/stream', [OfflineLectureController::class, 'streamRecording'])->name('lectures.stream');
    Route::resource('lectures', OfflineLectureController::class)->parameters(['lecture' => 'lecture']);
    Route::resource('activities', OfflineActivityController::class)->parameters(['activity' => 'activity']);
    Route::post('activities/{activity}/submissions/{submission}/grade', [OfflineActivityController::class, 'gradeSubmission'])->name('activities.submissions.grade');

    // حضور وغياب الكورس (موحّد للأوفلاين/الأونلاين حسب نفس المجموعة)
    Route::get('attendance', [OfflineCourseController::class, 'attendanceIndex'])->name('attendance.index');
    Route::get('attendance/sessions/{session}', [OfflineCourseController::class, 'attendanceSession'])->name('attendance.sessions.show');
    Route::post('attendance/mark', [OfflineCourseController::class, 'markAttendance'])->name('attendance.mark');

    // تقارير الطلاب
    Route::get('student-reports', [OfflineCourseController::class, 'studentReportsIndex'])->name('student-reports.index');
    Route::get('student-reports/{student}', [OfflineCourseController::class, 'studentReportsShow'])->name('student-reports.show');

    Route::prefix('curriculum')->name('curriculum.')->group(function () {
        Route::get('/', [OfflineCurriculumController::class, 'index'])->name('index');
        Route::post('/sections', [OfflineCurriculumController::class, 'storeSection'])->name('sections.store');
        Route::put('/sections/{section}', [OfflineCurriculumController::class, 'updateSection'])->name('sections.update');
        Route::delete('/sections/{section}', [OfflineCurriculumController::class, 'destroySection'])->name('sections.destroy');
        Route::post('/sections/{section}/move', [OfflineCurriculumController::class, 'moveSection'])->name('sections.move');
        Route::post('/attach-item', [OfflineCurriculumController::class, 'attachItem'])->name('attach-item');
        Route::post('/sections/{section}/notes', [OfflineCurriculumController::class, 'storeNote'])->name('sections.notes.store');
        Route::post('/items/{item}/move', [OfflineCurriculumController::class, 'moveItem'])->name('items.move');
        Route::delete('/items/{item}', [OfflineCurriculumController::class, 'destroyItem'])->name('items.destroy');
    });
});
