<?php

namespace App\Http\Controllers;

use App\Models\CourseLesson;
use App\Models\Lecture;
use App\Models\AdvancedCourse;
use App\Helpers\VideoHelper;
use App\Support\LectureRecordingResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProtectedVideoController extends Controller
{
    /** عدد فيديوهات المعاينة المعروضة في قائمة الكورس */
    public const PREVIEW_LIST_COUNT = 3;

    /** عدد فيديوهات المعاينة المفتوحة للتشغيل (الباقي مقفول) */
    public const PREVIEW_UNLOCKED_COUNT = 2;

    /**
     * عرض الفيديو عبر رابط موقّع (معاينة أو طالب مسجّل).
     */
    public function watch(Request $request): RedirectResponse|StreamedResponse|JsonResponse
    {
        $request->validate([
            'course_id' => 'required|integer',
            'lesson_id' => 'nullable|integer',
            'lecture_id' => 'nullable|integer',
            'preview' => 'nullable|in:0,1',
        ]);

        $courseId = (int) $request->course_id;
        $lessonId = $request->filled('lesson_id') ? (int) $request->lesson_id : null;
        $lectureId = $request->filled('lecture_id') ? (int) $request->lecture_id : null;
        $isPreview = (int) ($request->preview ?? 0) === 1;

        if (!$lessonId && !$lectureId) {
            abort(422, 'يجب تحديد الدرس أو المحاضرة.');
        }

        AdvancedCourse::where('id', $courseId)->where('is_active', true)->firstOrFail();

        if ($lectureId) {
            return $this->watchLecture($courseId, $lectureId, $isPreview);
        }

        return $this->watchLesson($courseId, $lessonId, $isPreview);
    }

    /**
     * رابط مشاهدة موقّع لمعاينة محاضرة/درس على صفحة الكورس العامة.
     * الـ parameter اسمه lectureId لكنه يدعم أيضاً lesson كـ fallback.
     */
    public function getPreviewWatchUrl(Request $request, int $courseId, int $lectureId): JsonResponse
    {
        $key = 'preview-video:' . ($request->ip() ?? 'guest');
        if (RateLimiter::tooManyAttempts($key, 30)) {
            return response()->json(['error' => 'تجاوزت حد طلبات المعاينة. حاول لاحقاً.'], 429);
        }
        RateLimiter::hit($key, 60);

        // المصدر الأساسي: محاضرات الكورس التي لديها تسجيل
        if ($this->isPreviewLecture($courseId, $lectureId)) {
            $lecture = Lecture::where('id', $lectureId)
                ->where('course_id', $courseId)
                ->whereNotNull('recording_url')
                ->where('recording_url', '!=', '')
                ->first();

            if (!$lecture) {
                return response()->json(['error' => 'غير مسموح بمعاينة هذا الفيديو.'], 403);
            }

            $expiresMinutes = 120;
            $cacheKey = 'preview_watch_url_lecture_v1:'.$courseId.':'.$lectureId;
            $payload = Cache::remember($cacheKey, now()->addMinutes(90), function () use ($courseId, $lectureId, $expiresMinutes, $lecture) {
                $url = trim((string) $lecture->recording_url);
                $platform = $lecture->video_platform ?: VideoHelper::getVideoSource($url);
                if ($platform === 'bunny' || VideoHelper::getVideoSource($url) === 'bunny') {
                    LectureRecordingResolver::resolve($url, 'bunny');
                }

                return [
                    'watch_url' => URL::temporarySignedRoute('video.protected.watch', now()->addMinutes($expiresMinutes), [
                        'course_id' => $courseId,
                        'lecture_id' => $lectureId,
                        'preview' => 1,
                    ]),
                    'expires_in_minutes' => $expiresMinutes,
                ];
            });

            return response()->json($payload);
        }

        // توافق خلفي: دروس video_url إن وُجدت
        if ($this->isPreviewLesson($courseId, $lectureId)) {
            $lesson = CourseLesson::where('id', $lectureId)
                ->where('advanced_course_id', $courseId)
                ->where('is_active', true)
                ->where('type', 'video')
                ->first();

            if (!$lesson || !filled($lesson->video_url)) {
                return response()->json(['error' => 'غير مسموح بمعاينة هذا الفيديو.'], 403);
            }

            $expiresMinutes = 120;
            $cacheKey = 'preview_watch_url_lesson_v3:'.$courseId.':'.$lectureId;
            $payload = Cache::remember($cacheKey, now()->addMinutes(90), function () use ($courseId, $lectureId, $expiresMinutes, $lesson) {
                $url = trim((string) $lesson->video_url);
                if (VideoHelper::getVideoSource($url) === 'bunny') {
                    LectureRecordingResolver::resolve($url, 'bunny');
                }

                return [
                    'watch_url' => URL::temporarySignedRoute('video.protected.watch', now()->addMinutes($expiresMinutes), [
                        'course_id' => $courseId,
                        'lesson_id' => $lectureId,
                        'preview' => 1,
                    ]),
                    'expires_in_minutes' => $expiresMinutes,
                ];
            });

            return response()->json($payload);
        }

        return response()->json(['error' => 'غير مسموح بمعاينة هذا الفيديو.'], 403);
    }

    /**
     * أول محاضرات الكورس التي لديها تسجيل — للمعاينة العامة.
     *
     * @return \Illuminate\Support\Collection<int, Lecture>
     */
    public static function previewLecturesForCourse(int $courseId, ?int $limit = null)
    {
        $limit = $limit ?? self::PREVIEW_LIST_COUNT;

        return Lecture::where('course_id', $courseId)
            ->whereNotNull('recording_url')
            ->where('recording_url', '!=', '')
            ->orderByRaw('CASE WHEN scheduled_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('scheduled_at')
            ->orderBy('id')
            ->limit($limit)
            ->get(['id', 'title', 'duration_minutes', 'recording_url', 'video_platform', 'scheduled_at']);
    }

    private function watchLecture(int $courseId, int $lectureId, bool $isPreview): RedirectResponse|StreamedResponse|JsonResponse
    {
        $lecture = Lecture::where('id', $lectureId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        if ($isPreview) {
            if (!$this->isPreviewLecture($courseId, $lectureId)) {
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

        $videoUrl = $lecture->recording_url ? trim($lecture->recording_url) : null;
        if (!$videoUrl) {
            abort(404, 'لا يوجد رابط فيديو لهذه المحاضرة.');
        }

        return $this->playVideoUrl($videoUrl, $lecture->video_platform, $lecture->title ?: 'Video');
    }

    private function watchLesson(int $courseId, int $lessonId, bool $isPreview): RedirectResponse|StreamedResponse|JsonResponse
    {
        $lesson = CourseLesson::where('id', $lessonId)
            ->where('advanced_course_id', $courseId)
            ->where('is_active', true)
            ->where('type', 'video')
            ->firstOrFail();

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

        return $this->playVideoUrl($videoUrl, null, $lesson->title ?: 'Video');
    }

    private function playVideoUrl(string $videoUrl, ?string $platform, string $title): RedirectResponse|StreamedResponse|JsonResponse
    {
        $embedUrl = VideoHelper::getEmbedUrl($videoUrl);
        $source = $platform ? strtolower(trim($platform)) : VideoHelper::getVideoSource($videoUrl);
        if ($source === '' || $source === 'unknown' || $source === 'other') {
            $source = VideoHelper::getVideoSource($videoUrl);
        }

        if (in_array($source, ['youtube', 'vimeo', 'google_drive'], true)) {
            return response()
                ->view('video.protected-embed', [
                    'type' => 'iframe',
                    'src' => $embedUrl ?: $videoUrl,
                    'title' => $title,
                ])
                ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate')
                ->header('Pragma', 'no-cache');
        }

        if ($source === 'bunny') {
            $resolved = LectureRecordingResolver::resolve($videoUrl, 'bunny');
            $signed = $resolved['recording_url'] ?: $embedUrl;

            return response()
                ->view('video.protected-embed', [
                    'type' => 'iframe',
                    'src' => $signed,
                    'title' => $title,
                ])
                ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate')
                ->header('Pragma', 'no-cache');
        }

        if ($source === 'direct') {
            return $this->streamLocalVideo($videoUrl);
        }

        return response()
            ->view('video.protected-embed', [
                'type' => 'iframe',
                'src' => $embedUrl ?: $videoUrl,
                'title' => $title,
            ])
            ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    private function isPreviewLecture(int $courseId, int $lectureId): bool
    {
        $previewIds = Cache::remember(
            'preview_unlocked_lecture_ids_v1:'.$courseId,
            now()->addMinutes(30),
            function () use ($courseId) {
                return self::previewLecturesForCourse($courseId, self::PREVIEW_UNLOCKED_COUNT)
                    ->pluck('id')
                    ->all();
            }
        );

        return in_array($lectureId, $previewIds, true);
    }

    private function isPreviewLesson(int $courseId, int $lessonId): bool
    {
        // إن وُجدت محاضرات للمعاينة نفضّلها ولا نفتح دروساً فارغة
        if (self::previewLecturesForCourse($courseId, 1)->isNotEmpty()) {
            return false;
        }

        $previewIds = Cache::remember(
            'preview_unlocked_lesson_ids_v3:'.$courseId,
            now()->addMinutes(30),
            function () use ($courseId) {
                return CourseLesson::where('advanced_course_id', $courseId)
                    ->where('is_active', true)
                    ->where('type', 'video')
                    ->whereNotNull('video_url')
                    ->where('video_url', '!=', '')
                    ->orderBy('order')
                    ->orderBy('id')
                    ->limit(self::PREVIEW_UNLOCKED_COUNT)
                    ->pluck('id')
                    ->all();
            }
        );

        return in_array($lessonId, $previewIds, true);
    }

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

    public static function signedWatchUrlForLesson(int $courseId, int $lessonId, int $expiresMinutes = 60): string
    {
        return URL::temporarySignedRoute('video.protected.watch', now()->addMinutes($expiresMinutes), [
            'course_id' => $courseId,
            'lesson_id' => $lessonId,
            'preview' => 0,
        ]);
    }
}
