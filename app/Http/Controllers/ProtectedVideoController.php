<?php

namespace App\Http\Controllers;

use App\Models\CourseLesson;
use App\Models\AdvancedCourse;
use App\Helpers\VideoHelper;
use App\Models\VideoProvider;
use App\Support\BunnyStreamSigner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProtectedVideoController extends Controller
{
    /**
     * عرض الفيديو عبر رابط موقّع (معاينة أو طالب مسجّل).
     * التحقق من التوقيع ثم إما إعادة التوجيه إلى رابط التضمين أو بث الفيديو المحلي.
     */
    public function watch(Request $request): RedirectResponse|StreamedResponse|JsonResponse
    {
        $request->validate([
            'course_id' => 'required|integer',
            'lesson_id' => 'required|integer',
            'preview' => 'nullable|in:0,1',
        ]);

        $courseId = (int) $request->course_id;
        $lessonId = (int) $request->lesson_id;
        $isPreview = (int) ($request->preview ?? 0) === 1;

        $lesson = CourseLesson::where('id', $lessonId)
            ->where('advanced_course_id', $courseId)
            ->where('is_active', true)
            ->where('type', 'video')
            ->firstOrFail();

        $course = AdvancedCourse::where('id', $courseId)->where('is_active', true)->firstOrFail();

        if ($isPreview) {
            if (!$this->isPreviewLesson($courseId, $lessonId)) {
                abort(403, 'هذا الفيديو غير متاح للمعاينة.');
            }
        } else {
            if (!auth()->check()) {
                abort(401, 'يجب تسجيل الدخول.');
            }
            if (!auth()->user()->isEnrolledIn($courseId)) {
                abort(403, 'يجب التسجيل في الكورس لمشاهدة هذا الفيديو.');
            }
        }

        $videoUrl = $lesson->video_url ? trim($lesson->video_url) : null;
        if (!$videoUrl) {
            abort(404, 'لا يوجد رابط فيديو لهذا الدرس.');
        }

        $embedUrl = VideoHelper::getEmbedUrl($videoUrl);
        $source = VideoHelper::getVideoSource($videoUrl);

        if (in_array($source, ['youtube', 'vimeo', 'google_drive'], true)) {
            return redirect()->away($embedUrl);
        }

        if ($source === 'bunny') {
            $signed = $embedUrl;
            $provider = VideoProvider::where('platform', 'bunny')->where('is_active', true)->orderByDesc('id')->first();
            $key = $provider?->token_auth_key ? trim((string) $provider->token_auth_key) : '';
            if ($key !== '') {
                $signed = BunnyStreamSigner::signEmbedUrl($embedUrl, $key, now()->addMinutes(20)->timestamp);
            }

            return response()
                ->view('video.protected-embed', [
                    'type' => 'iframe',
                    'src' => $signed,
                    'title' => $lesson->title ?: 'Video',
                ])
                ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate')
                ->header('Pragma', 'no-cache');
        }

        if ($source === 'direct') {
            return $this->streamLocalVideo($videoUrl);
        }

        return redirect()->away($videoUrl);
    }

    /**
     * الحصول على رابط مشاهدة موقّع لمعاينة فيديو (لصفحة الكورس العامة).
     * يُستدعى عند الضغط على "معاينة" دون تمرير الرابط الحقيقي في الصفحة.
     */
    public function getPreviewWatchUrl(Request $request, int $courseId, int $lessonId): JsonResponse
    {
        $key = 'preview-video:' . ($request->ip() ?? 'guest');
        if (RateLimiter::tooManyAttempts($key, 30)) {
            return response()->json(['error' => 'تجاوزت حد طلبات المعاينة. حاول لاحقاً.'], 429);
        }
        RateLimiter::hit($key, 60);

        $lesson = CourseLesson::where('id', $lessonId)
            ->where('advanced_course_id', $courseId)
            ->where('is_active', true)
            ->where('type', 'video')
            ->first();

        if (!$lesson || !$this->isPreviewLesson($courseId, $lessonId)) {
            return response()->json(['error' => 'غير مسموح بمعاينة هذا الفيديو.'], 403);
        }

        $watchUrl = URL::temporarySignedRoute('video.protected.watch', now()->addMinutes(15), [
            'course_id' => $courseId,
            'lesson_id' => $lessonId,
            'preview' => 1,
        ]);

        return response()->json([
            'watch_url' => $watchUrl,
            'expires_in_minutes' => 15,
        ]);
    }

    /**
     * التحقق من أن الدرس من ضمن أول 3 فيديوهات معاينة في الكورس
     */
    private function isPreviewLesson(int $courseId, int $lessonId): bool
    {
        $previewIds = CourseLesson::where('advanced_course_id', $courseId)
            ->where('is_active', true)
            ->where('type', 'video')
            ->orderBy('order')
            ->limit(3)
            ->pluck('id')
            ->toArray();

        return in_array($lessonId, $previewIds, true);
    }

    /**
     * بث فيديو محلي (ملف مباشر) — يحل الرابط إلى مسار محلي على السيرفر فقط
     */
    private function streamLocalVideo(string $videoUrl): StreamedResponse|RedirectResponse
    {
        $path = null;
        $base = rtrim(request()->getSchemeAndHttpHost(), '/');
        if (str_starts_with($videoUrl, $base . '/storage/')) {
            $relative = substr($videoUrl, strlen($base) + strlen('/storage/'));
            $path = storage_path('app/public/' . ltrim($relative, '/'));
        } elseif (str_starts_with($videoUrl, '/storage/')) {
            $path = storage_path('app/public/' . ltrim(parse_url($videoUrl, PHP_URL_PATH) ?? '', '/'));
        } elseif (!str_contains($videoUrl, '://')) {
            $path = storage_path('app/private/videos/' . basename($videoUrl));
        }

        if (!$path || !is_file($path) || !is_readable($path)) {
            abort(404, 'ملف الفيديو غير موجود.');
        }

        $mime = 'video/mp4';
        if (preg_match('/\.(webm|ogg)(\?.*)?$/i', $path)) {
            $mime = 'video/webm';
        }

        return response()->streamDownload(function () use ($path) {
            $h = fopen($path, 'rb');
            if ($h) {
                while (!feof($h)) {
                    echo fread($h, 8192);
                    flush();
                }
                fclose($h);
            }
        }, basename($path), [
            'Content-Type' => $mime,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * إنشاء رابط مشاهدة موقّع لطالب مسجّل (للاستخدام من API الدروس)
     */
    public static function signedWatchUrlForLesson(int $courseId, int $lessonId, int $expiresMinutes = 60): string
    {
        return URL::temporarySignedRoute('video.protected.watch', now()->addMinutes($expiresMinutes), [
            'course_id' => $courseId,
            'lesson_id' => $lessonId,
            'preview' => 0,
        ]);
    }
}
