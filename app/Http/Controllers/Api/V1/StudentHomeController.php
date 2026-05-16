<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MobileAppSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentHomeController extends Controller
{
    /**
     * الصفحة الرئيسية للطالب في التطبيق — نصوص من لوحة التحكم + كورسات نشطة فقط تُظهر التقدم الحقيقي.
     */
    public function home(Request $request): JsonResponse
    {
        $user = $request->user();
        $settings = MobileAppSetting::singleton();

        $enrollments = $user->activeCourses()->get()->map(function ($course) {
            $progress = (float) ($course->pivot->progress ?? 0);

            return [
                'id' => $course->id,
                'title' => [
                    'ar' => $course->title,
                    'en' => $course->title_en ?: $course->title,
                ],
                'progress_percent' => round(min(100, max(0, $progress)), 1),
                'web_path' => '/my-courses/'.$course->id,
            ];
        })->values();

        $avgProgress = $enrollments->isEmpty()
            ? 0.0
            : round((float) $enrollments->pluck('progress_percent')->avg(), 1);

        return response()->json([
            'has_active_enrollment' => $enrollments->isNotEmpty(),
            'enrollments' => $enrollments,
            'stats' => [
                'average_progress_percent' => $avgProgress,
                'active_courses_count' => $enrollments->count(),
            ],
            'catalog_web_path' => $settings->catalog_web_path ?: '/courses',
            'chats_full_url' => $settings->chats_full_url ? trim((string) $settings->chats_full_url) : null,
            'copy' => [
                'welcome_title' => [
                    'ar' => $settings->welcome_title_ar,
                    'en' => $settings->welcome_title_en,
                ],
                'welcome_subtitle' => [
                    'ar' => $settings->welcome_subtitle_ar,
                    'en' => $settings->welcome_subtitle_en,
                ],
                'mission_headline' => [
                    'ar' => $settings->mission_headline_ar,
                    'en' => $settings->mission_headline_en,
                ],
                'mission_body' => [
                    'ar' => $settings->mission_body_ar,
                    'en' => $settings->mission_body_en,
                ],
                'no_subscription_title' => [
                    'ar' => $settings->no_subscription_title_ar,
                    'en' => $settings->no_subscription_title_en,
                ],
                'no_subscription_body' => [
                    'ar' => $settings->no_subscription_body_ar,
                    'en' => $settings->no_subscription_body_en,
                ],
            ],
        ]);
    }
}
