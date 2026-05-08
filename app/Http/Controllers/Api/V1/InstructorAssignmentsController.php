<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstructorAssignmentsController extends Controller
{
    private function teachingCourseIds(Request $request)
    {
        $user = $request->user();
        $directCourseIds = AdvancedCourse::where('instructor_id', $user->id)->pluck('id');
        $assignedFromPaths = $user->teachingLearningPaths()->get()->flatMap(function ($ay) {
            $ids = json_decode($ay->pivot->assigned_courses ?? '[]', true);
            return is_array($ids) ? $ids : [];
        });
        return $directCourseIds->merge($assignedFromPaths)->unique()->filter()->values();
    }

    public function assignments(Request $request): JsonResponse
    {
        $courseIds = $this->teachingCourseIds($request);

        $q = Assignment::query()
            ->whereIn('advanced_course_id', $courseIds)
            ->with(['course:id,title,title_en'])
            ->withCount([
                'submissions as submissions_total_count',
                'submissions as submissions_pending_count' => function ($s) {
                    $s->whereNull('graded_at');
                },
            ])
            ->orderByDesc('id');

        if ($request->filled('course_id')) {
            $cid = (int) $request->input('course_id');
            if (! $courseIds->contains($cid)) {
                return response()->json(['message' => 'كورس غير مسموح'], 403);
            }
            $q->where('advanced_course_id', $cid);
        }

        $perPage = min(max((int) $request->input('per_page', 25), 1), 50);
        $p = $q->paginate($perPage)->withQueryString();

        $data = $p->getCollection()->map(function (Assignment $a) {
            return [
                'id' => $a->id,
                'title' => $a->title,
                'due_date' => $a->due_date?->toIso8601String(),
                'max_score' => $a->max_score,
                'course' => [
                    'id' => $a->advanced_course_id,
                    'title' => [
                        'ar' => $a->course?->title,
                        'en' => $a->course?->title_en ?: $a->course?->title,
                    ],
                ],
                'counts' => [
                    'submissions_total' => (int) ($a->submissions_total_count ?? 0),
                    'submissions_pending' => (int) ($a->submissions_pending_count ?? 0),
                ],
            ];
        })->values();

        return response()->json([
            'assignments' => $data,
            'pagination' => [
                'current_page' => $p->currentPage(),
                'last_page' => $p->lastPage(),
                'per_page' => $p->perPage(),
                'total' => $p->total(),
            ],
        ]);
    }

    public function submissions(Request $request, Assignment $assignment): JsonResponse
    {
        $courseIds = $this->teachingCourseIds($request);
        $courseId = $assignment->advanced_course_id ?? $assignment->course_id;
        if (! $courseId || ! $courseIds->contains($courseId)) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        $perPage = min(max((int) $request->input('per_page', 25), 1), 50);
        $q = AssignmentSubmission::query()
            ->where('assignment_id', $assignment->id)
            ->with(['student:id,name,email,profile_image', 'grader:id,name'])
            ->orderByRaw('CASE WHEN graded_at IS NULL THEN 0 ELSE 1 END')
            ->orderByDesc('submitted_at')
            ->orderByDesc('id');

        $p = $q->paginate($perPage)->withQueryString();

        $items = $p->getCollection()->map(function (AssignmentSubmission $s) {
            return [
                'id' => $s->id,
                'student' => [
                    'id' => $s->student?->id,
                    'name' => $s->student?->name,
                    'email' => $s->student?->email,
                    'profile_image_url' => $s->student?->profile_image_url,
                ],
                'status' => $s->status,
                'submitted_at' => $s->submitted_at?->toIso8601String(),
                'score' => $s->score,
                'feedback' => $s->feedback,
                'graded_at' => $s->graded_at?->toIso8601String(),
                'graded_by' => $s->grader?->name,
            ];
        })->values();

        return response()->json([
            'assignment' => [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'max_score' => $assignment->max_score,
                'course_id' => $courseId,
            ],
            'submissions' => $items,
            'pagination' => [
                'current_page' => $p->currentPage(),
                'last_page' => $p->lastPage(),
                'per_page' => $p->perPage(),
                'total' => $p->total(),
            ],
        ]);
    }

    public function grade(Request $request, AssignmentSubmission $submission): JsonResponse
    {
        $assignment = $submission->assignment;
        if (! $assignment) {
            return response()->json(['message' => 'غير موجود'], 404);
        }

        $courseIds = $this->teachingCourseIds($request);
        $courseId = $assignment->advanced_course_id ?? $assignment->course_id;
        if (! $courseId || ! $courseIds->contains($courseId)) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        $data = $request->validate([
            'score' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'feedback' => ['nullable', 'string', 'max:5000'],
            'status' => ['nullable', 'string', 'in:submitted,graded'],
        ]);

        $submission->update([
            'score' => array_key_exists('score', $data) ? $data['score'] : $submission->score,
            'feedback' => array_key_exists('feedback', $data) ? $data['feedback'] : $submission->feedback,
            'status' => $data['status'] ?? 'graded',
            'graded_at' => now(),
            'graded_by' => $request->user()->id,
        ]);

        return response()->json(['ok' => true]);
    }
}

