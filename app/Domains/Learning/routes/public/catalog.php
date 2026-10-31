<?php

use App\Domains\Learning\Http\Controllers\ProtectedVideoController;
use App\Domains\Learning\Http\Controllers\Public\CourseCatalogController;
use App\Domains\Learning\Http\Controllers\Public\CourseMindMapController;
use App\Domains\Learning\Http\Controllers\Public\InstructorController;
use Illuminate\Support\Facades\Route;

/**
 * كتالوج الكورسات، صفحة الكورس، المدربون، الخريطة الذهنية.
 * يُحمَّل من routes/web/public.php في نفس موضعه القديم.
 */
Route::get('/courses', [CourseCatalogController::class, 'index'])
    ->name('public.courses');
// صفحة المدربين (الملفات التعريفية المعتمدة)
Route::get('/instructors', [InstructorController::class, 'index'])->name('public.instructors.index');
Route::get('/instructors/{instructor}', [InstructorController::class, 'show'])->name('public.instructors.show');

// روابط الفيديو المحمية (موقّعة — لا تُعرَض الروابط الحقيقية في الصفحة)
Route::get('/v/watch', [ProtectedVideoController::class, 'watch'])
    ->name('video.protected.watch')
    ->middleware('signed');
Route::get('/course/{courseId}/preview-watch-url/{lessonId}', [ProtectedVideoController::class, 'getPreviewWatchUrl'])
    ->name('video.preview.watch-url');

// صفحة تفاصيل الكورس العامة
Route::get('/course/{id}', function ($id) {
    $course = \App\Domains\Learning\Models\AdvancedCourse::where('id', $id)
        ->where('is_active', true)
        ->publicCatalog()
        ->visibleOnCurrentHost()
        ->with(['academicSubject', 'academicYear', 'instructor'])
        ->withCount(['lectures', 'lessons'])
        ->firstOrFail();

    // التقسيمات (عناوين فقط للمعاينة قبل الشراء)
    $sections = \App\Domains\Learning\Models\CourseSection::where('advanced_course_id', $course->id)
        ->where('is_active', true)
        ->whereNull('parent_id')
        ->orderBy('order')
        ->get(['id', 'title', 'order']);

    // أول 3 محاضرات لها تسجيل: أول 2 مفتوحان عبر بلاير موقّع، الثالث مقفول
    $previewUnlockedCount = \App\Domains\Learning\Http\Controllers\ProtectedVideoController::PREVIEW_UNLOCKED_COUNT;
    $previewVideoLessons = \App\Domains\Learning\Http\Controllers\ProtectedVideoController::previewLecturesForCourse((int) $course->id);

    // إن لم توجد محاضرات مسجّلة، نستخدم دروس video_url إن وُجدت
    if ($previewVideoLessons->isEmpty()) {
        $previewVideoLessons = \App\Domains\Learning\Models\CourseLesson::where('advanced_course_id', $course->id)
            ->where('is_active', true)
            ->where('type', 'video')
            ->whereNotNull('video_url')
            ->where('video_url', '!=', '')
            ->orderBy('order')
            ->orderBy('id')
            ->limit(\App\Domains\Learning\Http\Controllers\ProtectedVideoController::PREVIEW_LIST_COUNT)
            ->get(['id', 'title', 'duration_minutes', 'video_url', 'order']);
    }

    // التحقق من التسجيل في الكورس
    $isEnrolled = false;
    if(auth()->check()) {
        $isEnrolled = \App\Domains\Learning\Models\StudentCourseEnrollment::where('user_id', auth()->id())
            ->where('advanced_course_id', $course->id)
            ->where('status', 'active')
            ->exists();
    }

    // كورسات ذات صلة
    $relatedCourses = \App\Domains\Learning\Models\AdvancedCourse::where('is_active', true)
        ->visibleOnCurrentHost()
        ->publicCatalog()
        ->where('id', '!=', $course->id)
        ->where(function($query) use ($course) {
            $query->where('level', $course->level)
                  ->orWhere('academic_subject_id', $course->academic_subject_id)
                  ->orWhere('is_featured', true);
        })
        ->with(['academicSubject', 'instructor'])
        ->withCount(['lectures', 'lessons'])
        ->limit(3)
        ->get();

    $approvedReviews = \App\Domains\Learning\Models\CourseReview::query()
        ->where('course_id', $course->id)
        ->where('is_approved', true)
        ->with('user')
        ->orderByDesc('is_featured')
        ->orderByDesc('is_marketing')
        ->latest()
        ->limit(40)
        ->get();

    $reviewsAvg = (float) (\App\Domains\Learning\Models\CourseReview::query()
        ->where('course_id', $course->id)
        ->where('is_approved', true)
        ->avg('rating') ?? 0);
    $reviewsCount = (int) \App\Domains\Learning\Models\CourseReview::query()
        ->where('course_id', $course->id)
        ->where('is_approved', true)
        ->count();

    $mindMapSteps = $course->mind_map_steps;
    $courseMindMapVisible = $course->mind_map_published
        && is_array($mindMapSteps)
        && count($mindMapSteps) >= 2;

    return view('course-show', compact(
        'course',
        'relatedCourses',
        'isEnrolled',
        'sections',
        'previewVideoLessons',
        'previewUnlockedCount',
        'approvedReviews',
        'reviewsAvg',
        'reviewsCount',
        'courseMindMapVisible'
    ));
})->name('public.course.show');

Route::get('/course/{course}/mind-map', [CourseMindMapController::class, 'show'])
    ->name('public.course.mind-map');
