<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\VideoToken;
use App\Models\VideoWatch;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
    /**
     * إنشاء توكن لمشاهدة الفيديو
     */
    public function generateToken(Request $request, $lessonId)
    {
        $user = Auth::user();
        $lesson = Lesson::findOrFail($lessonId);

        // التحقق من صلاحية الوصول للدرس
        if (!$this->canAccessLesson($user, $lesson)) {
            return response()->json(['error' => 'غير مسموح لك بمشاهدة هذا الفيديو'], 403);
        }

        // إنشاء توكن جديد
        $token = VideoToken::generateToken(
            $lessonId,
            $user->id,
            $request->ip()
        );

        // تسجيل النشاط
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'video_token_generated',
            'model_type' => 'Lesson',
            'model_id' => $lessonId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'token' => $token->token,
            'expires_at' => $token->expires_at,
            'video_url' => $this->getProtectedVideoUrl($lesson, $token)
        ]);
    }

    /**
     * تدفق الفيديو المحمي
     */
    public function streamVideo(Request $request, $token)
    {
        $videoToken = VideoToken::where('token', $token)->first();

        if (!$videoToken || !$videoToken->isValid()) {
            return response()->json(['error' => 'توكن غير صالح أو منتهي الصلاحية'], 403);
        }

        // التحقق من IP إذا كان محدد
        if ($videoToken->ip_address && $videoToken->ip_address !== $request->ip()) {
            return response()->json(['error' => 'غير مسموح الوصول من هذا الجهاز'], 403);
        }

        $lesson = $videoToken->lesson;
        
        // تعيين التوكن كمستخدم (استخدام واحد فقط)
        $videoToken->markAsUsed();

        // إرجاع رابط الفيديو الحقيقي مع headers الحماية
        return $this->serveProtectedVideo($lesson, $request);
    }

    /**
     * تتبع تقدم مشاهدة الفيديو
     */
    public function trackProgress(Request $request, $lessonId)
    {
        $request->validate([
            'watch_time' => 'required|integer|min:0',
            'video_duration' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        
        $watch = VideoWatch::updateOrCreate(
            [
                'lesson_id' => $lessonId,
                'user_id' => $user->id,
            ],
            []
        );

        $watch->updateProgress(
            $request->watch_time,
            $request->video_duration
        );

        return response()->json([
            'progress' => $watch->progress_percentage,
            'completed' => $watch->completed,
        ]);
    }

    /**
     * التحقق من صلاحية الوصول للدرس
     */
    private function canAccessLesson($user, $lesson)
    {
        // المدير والمدرس يمكنهم الوصول لكل شيء
        if ($user->isAdmin() || $user->isTeacher()) {
            return true;
        }

        // التحقق من تسجيل الطالب في الكورس
        if ($user->isStudent()) {
            return $user->enrolledCourses()->where('course_id', $lesson->course_id)->exists();
        }

        // تم إزالة دور ولي الأمر - لم يعد متاحاً
        // if ($user->isParent()) {
        //     return $user->children()
        //         ->whereHas('enrolledCourses', function($query) use ($lesson) {
        //             $query->where('course_id', $lesson->course_id);
        //         })
        //         ->exists();
        // }

        return false;
    }

    /**
     * إنشاء رابط فيديو محمي
     */
    private function getProtectedVideoUrl($lesson, $token)
    {
        return route('video.stream', ['token' => $token->token]);
    }

    /**
     * تقديم الفيديو مع الحماية
     */
    private function serveProtectedVideo($lesson, $request)
    {
        // إذا كان الفيديو على YouTube أو Vimeo
        if ($this->isExternalVideo($lesson->video_url)) {
            return response()->json([
                'type' => 'external',
                'url' => $lesson->video_url,
                'platform' => $this->getVideoPlatform($lesson->video_url)
            ]);
        }

        // إذا كان فيديو محلي
        $videoPath = storage_path('app/private/videos/' . basename($lesson->video_url));
        
        if (!file_exists($videoPath)) {
            return response()->json(['error' => 'الفيديو غير موجود'], 404);
        }

        return response()->file($videoPath, [
            'Content-Type' => 'video/mp4',
            'X-Accel-Buffering' => 'no',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * التحقق من نوع الفيديو الخارجي
     */
    private function isExternalVideo($url)
    {
        return str_contains($url, 'youtube.com') || 
               str_contains($url, 'youtu.be') || 
               str_contains($url, 'vimeo.com');
    }

    /**
     * تحديد منصة الفيديو
     */
    private function getVideoPlatform($url)
    {
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            return 'youtube';
        }
        if (str_contains($url, 'vimeo.com')) {
            return 'vimeo';
        }
        return 'unknown';
    }
}