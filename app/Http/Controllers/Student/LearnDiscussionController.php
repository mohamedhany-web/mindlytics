<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\LearnDiscussion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LearnDiscussionController extends Controller
{
    public function index(Request $request, AdvancedCourse $course): JsonResponse
    {
        $this->assertStudentEnrolled($course);

        $data = $request->validate([
            'context_type' => ['required', Rule::in(LearnDiscussion::CONTEXT_TYPES)],
            'context_id' => ['required', 'integer', 'min:1'],
            'kind' => ['required', Rule::in([LearnDiscussion::KIND_DISCUSSION, LearnDiscussion::KIND_QA])],
        ]);

        $threads = LearnDiscussion::query()
            ->where('course_id', $course->id)
            ->where('context_type', $data['context_type'])
            ->where('context_id', $data['context_id'])
            ->where('kind', $data['kind'])
            ->whereNull('parent_id')
            ->with([
                'user:id,name,role,profile_image',
                'replies' => fn ($q) => $q->with('user:id,name,role,profile_image'),
            ])
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn (LearnDiscussion $post) => $this->serialize($post, true));

        return response()->json(['data' => $threads]);
    }

    public function store(Request $request, AdvancedCourse $course): JsonResponse
    {
        $user = Auth::user();
        $this->assertStudentEnrolled($course);

        $data = $request->validate([
            'context_type' => ['required', Rule::in(LearnDiscussion::CONTEXT_TYPES)],
            'context_id' => ['required', 'integer', 'min:1'],
            'kind' => ['required', Rule::in([LearnDiscussion::KIND_DISCUSSION, LearnDiscussion::KIND_QA])],
            'body' => ['required', 'string', 'min:2', 'max:5000'],
            'parent_id' => ['nullable', 'integer', 'exists:learn_discussions,id'],
        ]);

        $parentId = $data['parent_id'] ?? null;
        if ($parentId) {
            $parent = LearnDiscussion::query()
                ->where('id', $parentId)
                ->where('course_id', $course->id)
                ->where('context_type', $data['context_type'])
                ->where('context_id', $data['context_id'])
                ->where('kind', $data['kind'])
                ->whereNull('parent_id')
                ->firstOrFail();
            $parentId = $parent->id;
        }

        $post = LearnDiscussion::create([
            'course_id' => $course->id,
            'context_type' => $data['context_type'],
            'context_id' => $data['context_id'],
            'kind' => $data['kind'],
            'user_id' => $user->id,
            'parent_id' => $parentId,
            'body' => trim($data['body']),
        ]);

        $post->load('user:id,name,role,profile_image');

        return response()->json(['data' => $this->serialize($post, false)], 201);
    }

    public function destroy(AdvancedCourse $course, LearnDiscussion $discussion): JsonResponse
    {
        $user = Auth::user();
        $this->assertStudentEnrolled($course);

        if ((int) $discussion->course_id !== (int) $course->id) {
            abort(404);
        }

        if ((int) $discussion->user_id !== (int) $user->id) {
            abort(403, 'يمكنك حذف مشاركاتك فقط.');
        }

        $discussion->delete();

        return response()->json(['ok' => true]);
    }

    private function assertStudentEnrolled(AdvancedCourse $course): void
    {
        $user = Auth::user();
        $enrolled = $user->activeCourses()->where('advanced_courses.id', $course->id)->exists()
            || $user->courseEnrollments()->where('advanced_course_id', $course->id)->whereIn('status', ['active', 'completed'])->exists();

        if (! $enrolled) {
            abort(403);
        }
    }

    private function serialize(LearnDiscussion $post, bool $withReplies): array
    {
        $payload = [
            'id' => $post->id,
            'body' => $post->body,
            'kind' => $post->kind,
            'parent_id' => $post->parent_id,
            'created_at' => optional($post->created_at)?->diffForHumans(),
            'created_at_iso' => optional($post->created_at)?->toIso8601String(),
            'is_mine' => (int) $post->user_id === (int) Auth::id(),
            'is_instructor' => $post->isInstructorAuthor(),
            'user' => [
                'id' => $post->user?->id,
                'name' => $post->user?->name ?? 'مستخدم',
                'role_label' => $post->isInstructorAuthor() ? 'مدرب' : 'طالب',
            ],
        ];

        if ($withReplies) {
            $payload['replies'] = $post->replies->map(fn (LearnDiscussion $r) => $this->serialize($r, false))->values();
        }

        return $payload;
    }
}
