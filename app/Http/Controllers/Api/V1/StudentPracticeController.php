<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LearningPattern;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentPracticeController extends Controller
{
    /**
     * قائمة التمارين (Learning Patterns) لكورسات الطالب النشطة.
     *
     * @queryParam course_id optional تصفية لكورس واحد
     * @queryParam type optional تصفية بنوع النمط
     * @queryParam q optional بحث بالعنوان/الوصف
     */
    public function patterns(Request $request): JsonResponse
    {
        $user = $request->user();
        $courseIds = $user->activeCourses()->pluck('advanced_courses.id');

        $q = LearningPattern::query()
            ->whereIn('advanced_course_id', $courseIds)
            ->where('is_active', true)
            ->with(['course:id,title,title_en', 'instructor:id,name'])
            ->latest('id');

        if ($request->filled('course_id')) {
            $cid = (int) $request->input('course_id');
            if (! $courseIds->contains($cid)) {
                return response()->json(['message' => 'كورس غير مسموح'], 403);
            }
            $q->where('advanced_course_id', $cid);
        }

        if ($request->filled('type')) {
            $q->where('type', (string) $request->input('type'));
        }

        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));
            if ($term !== '') {
                $q->where(function ($sub) use ($term) {
                    $sub->where('title', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
            }
        }

        $rows = $q->limit(200)->get();

        $payload = $rows->map(function (LearningPattern $p) {
            $typeInfo = $p->getTypeInfo();
            $courseId = (int) $p->advanced_course_id;

            return [
                'id' => $p->id,
                'course_id' => $courseId,
                'course_title' => [
                    'ar' => $p->course?->title,
                    'en' => $p->course?->title_en ?: $p->course?->title,
                ],
                'title' => $p->title,
                'description' => $p->description,
                'type' => $p->type,
                'type_display' => [
                    'name' => $typeInfo['name'] ?? $p->type,
                    'icon' => $typeInfo['icon'] ?? 'fas fa-puzzle-piece',
                ],
                'points' => (int) ($p->points ?? 0),
                'time_limit_minutes' => $p->time_limit_minutes,
                'difficulty_level' => $p->difficulty_level,
                'web_path' => "/my-courses/{$courseId}/learning-patterns/{$p->id}",
            ];
        })->values();

        return response()->json([
            'patterns' => $payload,
            'available_types' => LearningPattern::getAvailableTypes(),
        ]);
    }

    /**
     * تفاصيل تمرين واحد.
     */
    public function pattern(Request $request, LearningPattern $pattern): JsonResponse
    {
        $user = $request->user();
        $courseId = (int) $pattern->advanced_course_id;
        if (! $user->isEnrolledIn($courseId)) {
            return response()->json([
                'message' => 'أنت غير مسجل في هذا الكورس.',
                'code' => 'not_enrolled',
            ], 403);
        }

        if (! $pattern->is_active) {
            return response()->json([
                'message' => 'هذا التمرين غير متاح حالياً.',
                'code' => 'inactive',
            ], 404);
        }

        $pattern->load(['course:id,title,title_en', 'instructor:id,name']);
        $typeInfo = $pattern->getTypeInfo();

        return response()->json([
            'pattern' => [
                'id' => $pattern->id,
                'course_id' => $courseId,
                'course_title' => [
                    'ar' => $pattern->course?->title,
                    'en' => $pattern->course?->title_en ?: $pattern->course?->title,
                ],
                'title' => $pattern->title,
                'description' => $pattern->description,
                'instructions' => $pattern->instructions,
                'type' => $pattern->type,
                'type_display' => [
                    'name' => $typeInfo['name'] ?? $pattern->type,
                    'icon' => $typeInfo['icon'] ?? 'fas fa-puzzle-piece',
                ],
                'points' => (int) ($pattern->points ?? 0),
                'time_limit_minutes' => $pattern->time_limit_minutes,
                'difficulty_level' => $pattern->difficulty_level,
                'allow_multiple_attempts' => (bool) $pattern->allow_multiple_attempts,
                'max_attempts' => $pattern->max_attempts,
                'web_path' => "/my-courses/{$courseId}/learning-patterns/{$pattern->id}",
                'pattern_data' => $pattern->pattern_data,
            ],
        ]);
    }
}

