<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Assignment;
use App\Models\CourseLesson;
use App\Models\CurriculumItem;
use App\Models\Exam;
use App\Models\LearningPattern;
use App\Models\Lecture;
use App\Models\AdvancedExam;
use App\Support\LectureRecordingResolver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * تعلّم الطالب من التطبيق — نفس هيكل المنهج والواجبات والاختبارات المعروضة على الويب.
 */
class StudentLearnController extends Controller
{
    /**
     * قائمة الكورسات النشطة للطالب مع التقدّم.
     */
    public function courses(Request $request): JsonResponse
    {
        $user = $request->user();
        $rows = $user->activeCourses()
            ->with(['instructor', 'academicSubject'])
            ->get();

        $payload = $rows->map(function ($course) {
            $progress = (float) ($course->pivot->progress ?? 0);

            return [
                'id' => $course->id,
                'title' => [
                    'ar' => $course->title,
                    'en' => $course->title_en ?: $course->title,
                ],
                'progress_percent' => round(min(100, max(0, $progress)), 1),
                'instructor_name' => $course->instructor?->name,
                'subject_name' => [
                    'ar' => $course->academicSubject?->name_ar ?? $course->academicSubject?->name ?? '',
                    'en' => $course->academicSubject?->name_en ?? $course->academicSubject?->name ?? '',
                ],
                'web_paths' => [
                    'overview' => '/my-courses/'.$course->id,
                    'learn' => '/my-courses/'.$course->id.'/learn',
                ],
            ];
        });

        return response()->json(['courses' => $payload]);
    }

    /**
     * منهج الكورس (أقسام + عناصر) مع روابط فتح الصفحة على الموقع.
     */
    public function courseOutline(Request $request, AdvancedCourse $course): JsonResponse
    {
        $user = $request->user();
        if (! $user->isEnrolledIn($course->id)) {
            return response()->json([
                'message' => 'أنت غير مسجل في هذا الكورس.',
                'code' => 'not_enrolled',
            ], 403);
        }

        $sections = $course->activeSections()
            ->with(['activeItems' => function ($query) {
                $query->orderBy('order')->with('item');
            }])
            ->orderBy('order')
            ->get();

        foreach ($sections as $section) {
            foreach ($section->activeItems as $curriculumItem) {
                $entity = $curriculumItem->item;
                if ($entity instanceof CourseLesson) {
                    $entity->load(['progress' => fn ($q) => $q->where('user_id', $user->id)]);
                } elseif ($entity instanceof LearningPattern) {
                    $entity->load(['attempts' => fn ($q) => $q->where('user_id', $user->id)->latest()]);
                } elseif ($entity instanceof AdvancedExam || $entity instanceof Exam) {
                    $entity->load(['attempts' => fn ($q) => $q->where('user_id', $user->id)->whereNotNull('submitted_at')]);
                } elseif ($entity instanceof Assignment) {
                    $entity->load(['submissions' => fn ($q) => $q->where('student_id', $user->id)]);
                } elseif ($entity instanceof Lecture) {
                    $entity->load(['watchProgress' => fn ($q) => $q->where('user_id', $user->id)]);
                }
            }
        }

        $sectionPayload = $sections->map(function ($section) use ($course, $user) {
            $items = $section->activeItems->map(function (CurriculumItem $ci) use ($course, $user) {
                return $this->serializeCurriculumItem($ci, $course, $user);
            })->filter()->values();

            return [
                'id' => $section->id,
                'title' => $section->title,
                'description' => $section->description,
                'order' => $section->order,
                'items' => $items,
            ];
        });

        return response()->json([
            'course' => [
                'id' => $course->id,
                'title' => [
                    'ar' => $course->title,
                    'en' => $course->title_en ?: $course->title,
                ],
            ],
            'sections' => $sectionPayload,
        ]);
    }

    /**
     * رابط تشغيل محاضرة موقّع/جاهز (نفس منطق صفحة التعلّم على الويب).
     */
    public function lecturePlayback(Request $request, AdvancedCourse $course, int $lecture): JsonResponse
    {
        $user = $request->user();
        if (! $user->isEnrolledIn($course->id)) {
            return response()->json([
                'message' => 'أنت غير مسجل في هذا الكورس.',
                'code' => 'not_enrolled',
            ], 403);
        }

        $lectureModel = $course->lectures()->find($lecture);
        if (! $lectureModel) {
            return response()->json(['message' => 'المحاضرة غير موجودة.'], 404);
        }

        $cacheKey = sprintf('api_lecture_playback:%d:%d:%d', $course->id, $lectureModel->id, $user->id);

        $payload = Cache::remember($cacheKey, now()->addMinutes(55), function () use ($lectureModel, $course, $user) {
            $resolved = LectureRecordingResolver::resolve(
                $lectureModel->recording_url,
                $lectureModel->video_platform
            );

            if (empty($resolved['recording_url'])) {
                return null;
            }

            $watch = $lectureModel->watchProgress()
                ->where('user_id', $user->id)
                ->first();

            return [
                'id' => $lectureModel->id,
                'title' => $lectureModel->title ?? 'محاضرة',
                'recording_url' => $resolved['recording_url'],
                'video_platform' => $resolved['video_platform'],
                'duration_minutes' => $lectureModel->duration_minutes,
                'watch_progress_percent' => $watch ? (int) $watch->progress_percent : null,
                'web_path' => '/my-courses/'.$course->id.'/lectures/'.$lectureModel->id,
            ];
        });

        if ($payload === null) {
            return response()->json([
                'message' => 'لا يوجد تسجيل فيديو لهذه المحاضرة.',
                'code' => 'no_recording',
            ], 404);
        }

        return response()->json($payload);
    }

    /**
     * جميع الواجبات المنشورة لكورسات الطالب النشطة.
     *
     * @queryParam course_id optional تصفية لكورس واحد
     */
    public function assignments(Request $request): JsonResponse
    {
        $user = $request->user();
        $courseIds = $user->activeCourses()->pluck('advanced_courses.id');

        $q = Assignment::query()
            ->whereIn('advanced_course_id', $courseIds)
            ->where('status', 'published')
            ->with(['course', 'lesson', 'teacher']);

        if ($request->filled('course_id')) {
            $cid = (int) $request->input('course_id');
            if (! $courseIds->contains($cid)) {
                return response()->json(['message' => 'كورس غير مسموح'], 403);
            }
            $q->where('advanced_course_id', $cid);
        }

        $assignments = $q->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->get();

        $submissionIds = $assignments->pluck('id');
        $submissions = \App\Models\AssignmentSubmission::where('student_id', $user->id)
            ->whereIn('assignment_id', $submissionIds)
            ->get()
            ->keyBy('assignment_id');

        $payload = $assignments->map(function (Assignment $a) use ($submissions) {
            $sub = $submissions->get($a->id);

            return [
                'id' => $a->id,
                'title' => $a->title,
                'due_date' => $a->due_date?->toIso8601String(),
                'max_score' => $a->max_score,
                'course_id' => $a->advanced_course_id,
                'course_title' => [
                    'ar' => $a->course?->title,
                    'en' => $a->course?->title_en ?: $a->course?->title,
                ],
                'has_submission' => $sub !== null,
                'submission_status' => $sub?->status,
                'web_path' => '/assignments/'.$a->id,
            ];
        });

        return response()->json(['assignments' => $payload]);
    }

    /**
     * الاختبارات المتاحة حالياً لكورسات الطالب الأونلاين.
     *
     * @queryParam course_id optional تصفية لكورس واحد
     */
    public function exams(Request $request): JsonResponse
    {
        $user = $request->user();
        $onlineCourseIds = $user->activeCourses()->pluck('advanced_courses.id');

        $q = Exam::query()
            ->whereIn('advanced_course_id', $onlineCourseIds)
            ->available()
            ->with(['course']);

        if ($request->filled('course_id')) {
            $cid = (int) $request->input('course_id');
            if (! $onlineCourseIds->contains($cid)) {
                return response()->json(['message' => 'كورس غير مسموح'], 403);
            }
            $q->where('advanced_course_id', $cid);
        }

        $exams = $q->orderBy('created_at', 'desc')->get();

        $payload = $exams->map(function (Exam $exam) use ($user) {
            return [
                'id' => $exam->id,
                'title' => $exam->title,
                'description' => $exam->description,
                'duration_minutes' => $exam->duration_minutes,
                'total_marks' => $exam->total_marks,
                'course_id' => $exam->advanced_course_id,
                'course_title' => [
                    'ar' => $exam->course?->title,
                    'en' => $exam->course?->title_en ?: $exam->course?->title,
                ],
                'attempts_used' => $exam->attempts()->where('user_id', $user->id)->count(),
                'attempts_allowed' => $exam->attempts_allowed,
                'can_attempt' => $exam->canAttempt($user->id),
                'web_path' => '/exams/'.$exam->id,
            ];
        });

        return response()->json(['exams' => $payload]);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function serializeCurriculumItem(CurriculumItem $ci, AdvancedCourse $course, \App\Models\User $user): ?array
    {
        $item = $ci->item;
        if (! $item) {
            return null;
        }

        $base = [
            'curriculum_item_id' => $ci->id,
            'order' => $ci->order,
        ];

        if ($item instanceof CourseLesson) {
            $p = $item->progress->first();

            return array_merge($base, [
                'type' => 'lesson',
                'id' => $item->id,
                'title' => [
                    'ar' => $item->title,
                    'en' => $item->title,
                ],
                'completed' => $p && $p->is_completed,
                'web_path' => '/my-courses/'.$course->id.'/learn?lesson='.$item->id,
            ]);
        }

        if ($item instanceof Assignment) {
            $sub = $item->submissions->firstWhere('student_id', $user->id);

            return array_merge($base, [
                'type' => 'assignment',
                'id' => $item->id,
                'title' => $item->title,
                'due_date' => $item->due_date?->toIso8601String(),
                'has_submission' => $sub !== null,
                'web_path' => '/assignments/'.$item->id,
            ]);
        }

        if ($item instanceof AdvancedExam || $item instanceof Exam) {
            $examId = $item->id;

            return array_merge($base, [
                'type' => 'exam',
                'id' => $examId,
                'title' => $item->title,
                'duration_minutes' => $item->duration_minutes,
                'web_path' => '/exams/'.$examId,
            ]);
        }

        if ($item instanceof LearningPattern) {
            return array_merge($base, [
                'type' => 'learning_pattern',
                'id' => $item->id,
                'title' => $item->title ?? 'نمط تعلم',
                'web_path' => '/my-courses/'.$course->id.'/learning-patterns/'.$item->id,
            ]);
        }

        if ($item instanceof Lecture) {
            $resolved = LectureRecordingResolver::resolve($item->recording_url, $item->video_platform);
            $watch = $item->watchProgress->first();

            return array_merge($base, [
                'type' => 'lecture',
                'id' => $item->id,
                'title' => $item->title ?? 'محاضرة',
                'description' => $item->description,
                'duration_minutes' => $item->duration_minutes,
                'video_platform' => $resolved['video_platform'],
                'recording_url' => $resolved['recording_url'],
                'watch_progress_percent' => $watch ? (int) $watch->progress_percent : null,
                'web_path' => '/my-courses/'.$course->id.'/lectures/'.$item->id,
            ]);
        }

        return array_merge($base, [
            'type' => 'unknown',
            'title' => 'عنصر',
            'web_path' => '/my-courses/'.$course->id,
        ]);
    }
}
