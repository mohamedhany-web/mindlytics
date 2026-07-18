<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Lecture;
use App\Models\LearnDiscussion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LearnDiscussionController extends Controller
{
    public function index(Request $request): View
    {
        $instructor = Auth::user();
        $courseIds = AdvancedCourse::query()
            ->where('instructor_id', $instructor->id)
            ->pluck('id');

        $kind = $request->query('kind');
        $courseFilter = $request->query('course_id');

        $query = LearnDiscussion::query()
            ->whereIn('course_id', $courseIds)
            ->whereNull('parent_id')
            ->with([
                'user:id,name,role',
                'course:id,title',
                'replies' => fn ($q) => $q->with('user:id,name,role')->latest()->limit(5),
            ])
            ->withCount('replies')
            ->latest();

        if (in_array($kind, [LearnDiscussion::KIND_DISCUSSION, LearnDiscussion::KIND_QA], true)) {
            $query->where('kind', $kind);
        }

        if ($courseFilter && $courseIds->contains((int) $courseFilter)) {
            $query->where('course_id', (int) $courseFilter);
        }

        $threads = $query->paginate(20)->withQueryString();
        $courses = AdvancedCourse::query()
            ->where('instructor_id', $instructor->id)
            ->orderBy('title')
            ->get(['id', 'title']);

        $unreadQa = LearnDiscussion::query()
            ->whereIn('course_id', $courseIds)
            ->where('kind', LearnDiscussion::KIND_QA)
            ->whereNull('parent_id')
            ->whereDoesntHave('replies', function ($q) use ($instructor) {
                $q->where('user_id', $instructor->id);
            })
            ->count();

        return view('instructor.learn-discussions.index', compact('threads', 'courses', 'kind', 'courseFilter', 'unreadQa'));
    }

    public function show(LearnDiscussion $discussion): View
    {
        $this->assertOwnsCourse($discussion->course_id);

        $discussion->load([
            'user:id,name,role,profile_image',
            'course:id,title',
            'replies.user:id,name,role,profile_image',
        ]);

        $contextTitle = $this->resolveContextTitle($discussion);

        return view('instructor.learn-discussions.show', compact('discussion', 'contextTitle'));
    }

    public function reply(Request $request, LearnDiscussion $discussion): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $this->assertOwnsCourse($discussion->course_id);

        if ($discussion->parent_id) {
            abort(422, 'الرد يكون على المنشور الرئيسي فقط.');
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:5000'],
        ]);

        $reply = LearnDiscussion::create([
            'course_id' => $discussion->course_id,
            'context_type' => $discussion->context_type,
            'context_id' => $discussion->context_id,
            'kind' => $discussion->kind,
            'user_id' => Auth::id(),
            'parent_id' => $discussion->id,
            'body' => trim($data['body']),
        ]);

        if ($request->expectsJson()) {
            $reply->load('user:id,name,role');

            return response()->json([
                'data' => [
                    'id' => $reply->id,
                    'body' => $reply->body,
                    'created_at' => $reply->created_at?->diffForHumans(),
                    'is_instructor' => true,
                    'user' => [
                        'id' => $reply->user?->id,
                        'name' => $reply->user?->name,
                        'role_label' => 'مدرب',
                    ],
                ],
            ], 201);
        }

        return back()->with('success', 'تم إرسال ردك للطلاب.');
    }

    public function store(Request $request, AdvancedCourse $course): JsonResponse
    {
        $this->assertOwnsCourse($course->id);

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
                ->whereNull('parent_id')
                ->firstOrFail();
            $parentId = $parent->id;
        }

        $post = LearnDiscussion::create([
            'course_id' => $course->id,
            'context_type' => $data['context_type'],
            'context_id' => $data['context_id'],
            'kind' => $data['kind'],
            'user_id' => Auth::id(),
            'parent_id' => $parentId,
            'body' => trim($data['body']),
        ]);

        $post->load('user:id,name,role');

        return response()->json([
            'data' => [
                'id' => $post->id,
                'body' => $post->body,
                'created_at' => $post->created_at?->diffForHumans(),
                'is_instructor' => true,
                'is_mine' => true,
                'user' => [
                    'id' => $post->user?->id,
                    'name' => $post->user?->name,
                    'role_label' => 'مدرب',
                ],
            ],
        ], 201);
    }

    private function assertOwnsCourse(int $courseId): void
    {
        $owns = AdvancedCourse::query()
            ->where('id', $courseId)
            ->where('instructor_id', Auth::id())
            ->exists();

        if (! $owns && ! Auth::user()?->isAdmin()) {
            abort(403);
        }
    }

    private function resolveContextTitle(LearnDiscussion $discussion): string
    {
        if ($discussion->context_type === 'lecture') {
            return Lecture::query()->where('id', $discussion->context_id)->value('title')
                ?? ('محاضرة #'.$discussion->context_id);
        }

        return $discussion->context_type.' #'.$discussion->context_id;
    }
}
