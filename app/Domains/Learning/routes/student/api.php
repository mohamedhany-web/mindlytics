<?php

use App\Domains\Learning\Http\Controllers\Api\VideoInfoController;
use Illuminate\Support\Facades\Route;

/**
 * API معلومات الفيديو والدروس وطلاب الكورس (للمدرب).
 * يُحمَّل من routes/web/student.php وفي نفس موضعه القديم.
 */
Route::post('/api/video/info', [VideoInfoController::class, 'getInfo'])->name('api.video.info');
    
// API للدروس - محمية بالتأكد من التسجيل
Route::get('/api/lessons/{lesson}', function(\App\Domains\Learning\Models\CourseLesson $lesson) {
    $user = auth()->user();
        
    if (!$user->isStudent()) {
        return response()->json(['error' => 'غير مصرح'], 403);
    }
        
    if (!$user->isEnrolledIn($lesson->advanced_course_id)) {
        return response()->json(['error' => 'غير مصرح - غير مسجل في الكورس'], 403);
    }
        
    $progress = $lesson->progress()->where('user_id', $user->id)->first();

    $videoUrl = $lesson->video_url ? trim($lesson->video_url) : null;
    $videoPlatform = null;
    if ($videoUrl) {
        $resolved = \App\Domains\Learning\Support\LectureRecordingResolver::resolve(
            $videoUrl,
            \App\Helpers\VideoHelper::getVideoSource($videoUrl)
        );
        $videoUrl = $resolved['recording_url'] ?: $videoUrl;
        $videoPlatform = $resolved['video_platform'];
    }

    return response()->json([
        'id' => $lesson->id,
        'title' => $lesson->title,
        'description' => $lesson->description,
        'content' => $lesson->content,
        'type' => $lesson->type,
        'video_url' => $videoUrl,
        'video_platform' => $videoPlatform,
        'duration_minutes' => $lesson->duration_minutes,
        'attachments' => $lesson->attachments ? json_decode($lesson->attachments, true) : null,
        'progress' => $progress ? [
            'is_completed' => (bool) $progress->is_completed,
            'progress_percent' => (int) ($progress->progress_percent ?? 0),
            'watch_time' => (int) ($progress->watch_time ?? 0),
        ] : null
    ]);
});

// API للطلاب المسجلين في الكورس - محمية بـ role middleware
Route::get('/api/courses/{course}/students', function($course) {
    $instructor = auth()->user();
        
    // التحقق من أن المستخدم مدرب
    if (!$instructor->isInstructor()) {
        return response()->json(['error' => 'غير مصرح'], 403);
    }
        
    // التحقق من أن الكورس يخص المدرب
    $advancedCourse = \App\Domains\Learning\Models\AdvancedCourse::where('id', $course)
        ->where('instructor_id', $instructor->id)
        ->firstOrFail();
        
    // جلب الطلاب المسجلين في الكورس
    $enrollments = \App\Domains\Learning\Models\StudentCourseEnrollment::where('advanced_course_id', $course)
        ->where('status', 'active')
        ->with('user')
        ->get();
        
    $students = $enrollments->map(function($enrollment) {
        $user = $enrollment->user;
        return [
            'id' => $user->id,
            'name' => $user->name ?? $user->full_name ?? ($user->first_name . ' ' . $user->last_name),
            'full_name' => $user->full_name ?? ($user->first_name . ' ' . $user->last_name),
            'first_name' => $user->first_name ?? '',
            'last_name' => $user->last_name ?? '',
            'email' => $user->email,
        ];
    });
        
    return response()->json([
        'students' => $students,
        'count' => $students->count()
    ]);
})->middleware(['auth', 'role:instructor|teacher']);
