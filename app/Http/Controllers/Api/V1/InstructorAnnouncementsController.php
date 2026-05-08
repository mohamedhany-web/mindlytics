<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\CourseCommunityPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstructorAnnouncementsController extends Controller
{
    private function teachingCourseIds(Request $request)
    {
        $user = $request->user();
        $directCourseIds = \App\Models\AdvancedCourse::where('instructor_id', $user->id)->pluck('id');
        $assignedFromPaths = $user->teachingLearningPaths()->get()->flatMap(function ($ay) {
            $ids = json_decode($ay->pivot->assigned_courses ?? '[]', true);
            return is_array($ids) ? $ids : [];
        });
        return $directCourseIds->merge($assignedFromPaths)->unique()->filter()->values();
    }

    private function ensureInstructorOwnsCourse(Request $request, AdvancedCourse $course): ?JsonResponse
    {
        $ids = $this->teachingCourseIds($request);
        if (! $ids->contains($course->id)) {
            return response()->json([
                'message' => 'غير مسموح لك بالوصول لهذا الكورس.',
                'code' => 'course_not_owned',
            ], 403);
        }
        return null;
    }

    public function index(Request $request, AdvancedCourse $course): JsonResponse
    {
        if ($deny = $this->ensureInstructorOwnsCourse($request, $course)) {
            return $deny;
        }

        $perPage = (int) $request->input('per_page', 20);
        $perPage = max(5, min(50, $perPage));

        $q = CourseCommunityPost::query()
            ->where('course_id', $course->id)
            ->where('is_pinned', true)
            ->with(['user:id,name,profile_image,role'])
            ->latest('id');

        $p = $q->paginate($perPage)->withQueryString();

        $data = $p->getCollection()->map(fn (CourseCommunityPost $post) => [
            'id' => $post->id,
            'course_id' => $post->course_id,
            'body' => $post->body,
            'created_at' => $post->created_at?->toIso8601String(),
            'user' => [
                'id' => $post->user?->id,
                'name' => $post->user?->name,
                'profile_image_url' => $post->user?->profile_image_url,
            ],
        ])->values();

        return response()->json([
            'announcements' => $data,
            'pagination' => [
                'current_page' => $p->currentPage(),
                'last_page' => $p->lastPage(),
                'per_page' => $p->perPage(),
                'total' => $p->total(),
                'next_page_url' => $p->nextPageUrl(),
            ],
        ]);
    }

    public function store(Request $request, AdvancedCourse $course): JsonResponse
    {
        if ($deny = $this->ensureInstructorOwnsCourse($request, $course)) {
            return $deny;
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:4000'],
        ]);

        $post = CourseCommunityPost::create([
            'course_id' => $course->id,
            'user_id' => $request->user()->id,
            'body' => trim($data['body']),
            'is_pinned' => true,
            'edited_at' => null,
        ]);

        return response()->json(['announcement_id' => $post->id], 201);
    }
}

