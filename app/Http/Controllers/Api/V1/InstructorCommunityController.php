<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\CourseCommunityPost;
use App\Models\CourseCommunityComment;
use App\Models\CourseCommunityPostImage;
use App\Models\CourseCommunityReaction;
use App\Support\CourseCommunityFeedBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InstructorCommunityController extends Controller
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

    private function ensureInstructorPostAccess(Request $request, CourseCommunityPost $post): ?JsonResponse
    {
        $course = AdvancedCourse::find($post->course_id);
        if (! $course) {
            return response()->json(['message' => 'غير موجود'], 404);
        }
        if ($deny = $this->ensureInstructorOwnsCourse($request, $course)) {
            return $deny;
        }
        if (! $post->isAuthorVisibleInCommunityCohort()) {
            return response()->json(['message' => 'غير موجود'], 404);
        }
        return null;
    }

    public function courses(Request $request): JsonResponse
    {
        $ids = $this->teachingCourseIds($request);
        $courses = $ids->isEmpty()
            ? collect()
            : AdvancedCourse::whereIn('id', $ids)->get(['id', 'title', 'title_en']);

        $payload = $courses->map(fn (AdvancedCourse $c) => [
            'id' => $c->id,
            'title' => [
                'ar' => $c->title,
                'en' => $c->title_en ?: $c->title,
            ],
        ])->values();

        return response()->json(['courses' => $payload]);
    }

    public function feed(Request $request, AdvancedCourse $course): JsonResponse
    {
        if ($deny = $this->ensureInstructorOwnsCourse($request, $course)) {
            return $deny;
        }

        $user = $request->user();
        $postsQuery = CourseCommunityPost::query()
            ->where('course_id', $course->id)
            ->whereAuthorVisibleInCourse($course->id)
            ->with(['user:id,name,profile_image,updated_at,role', 'images'])
            ->withCount(['comments']);

        return CourseCommunityFeedBuilder::buildResponse($request, $course, $user, $postsQuery);
    }

    public function createPost(Request $request, AdvancedCourse $course): JsonResponse
    {
        if ($deny = $this->ensureInstructorOwnsCourse($request, $course)) {
            return $deny;
        }

        if ($request->isJson()) {
            $data = $request->validate([
                'body' => ['required', 'string', 'min:2', 'max:4000'],
            ]);

            $post = CourseCommunityPost::create([
                'course_id' => $course->id,
                'user_id' => $request->user()->id,
                'body' => trim($data['body']),
                'is_pinned' => false,
                'edited_at' => null,
            ]);

            return response()->json(['post_id' => $post->id], 201);
        }

        $request->validate([
            'body' => ['nullable', 'string', 'max:4000'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['file', 'image', 'max:8192'],
        ], [
            'images.max' => 'يمكن رفع 10 صور كحد أقصى',
            'images.*.image' => 'يجب أن تكون الملفات صورًا',
            'images.*.max' => 'حجم كل صورة يجب ألا يتجاوز 8 ميجابايت',
        ]);

        $body = trim((string) $request->input('body', ''));
        $files = $request->file('images', []);
        if (! is_array($files)) {
            $files = [];
        }

        if ($body === '' && count($files) === 0) {
            throw ValidationException::withMessages([
                'body' => ['أضف نصًا أو صورة واحدة على الأقل.'],
            ]);
        }

        $post = CourseCommunityPost::create([
            'course_id' => $course->id,
            'user_id' => $request->user()->id,
            'body' => $body,
            'is_pinned' => false,
            'edited_at' => null,
        ]);

        $diskName = student_mobile_disk();
        foreach (array_values($files) as $index => $uploaded) {
            if (! $uploaded || ! $uploaded->isValid()) {
                continue;
            }
            $path = $uploaded->storePublicly(
                'community/posts/'.$post->id,
                ['disk' => $diskName]
            );
            CourseCommunityPostImage::create([
                'post_id' => $post->id,
                'path' => $path,
                'disk' => $diskName,
                'sort_order' => $index,
            ]);
        }

        return response()->json(['post_id' => $post->id], 201);
    }

    public function post(Request $request, CourseCommunityPost $post): JsonResponse
    {
        if ($deny = $this->ensureInstructorPostAccess($request, $post)) {
            return $deny;
        }

        $post->load(['user:id,name,profile_image,updated_at,role', 'images']);

        $comments = CourseCommunityComment::query()
            ->where('post_id', $post->id)
            ->with(['user:id,name,profile_image,updated_at,role', 'parent.user:id,name,profile_image,updated_at,role'])
            ->orderBy('id')
            ->limit(200)
            ->get();

        $commentTotal = CourseCommunityComment::query()->where('post_id', $post->id)->count();

        $likes = CourseCommunityReaction::query()
            ->where('reactable_type', CourseCommunityPost::class)
            ->where('reactable_id', $post->id)
            ->where('type', 'like')
            ->count();

        $user = $request->user();
        $viewerLiked = CourseCommunityReaction::query()
            ->where('reactable_type', CourseCommunityPost::class)
            ->where('reactable_id', $post->id)
            ->where('type', 'like')
            ->where('user_id', $user->id)
            ->exists();

        return response()->json([
            'post' => [
                'id' => $post->id,
                'course_id' => $post->course_id,
                'body' => $post->body,
                'is_pinned' => (bool) $post->is_pinned,
                'created_at' => $post->created_at?->toIso8601String(),
                'edited_at' => $post->edited_at?->toIso8601String(),
                'user' => [
                    'id' => $post->user?->id,
                    'name' => $post->user?->name,
                    'profile_image_url' => $post->user?->profile_image_url,
                ],
                'counts' => [
                    'comments' => $commentTotal,
                    'likes' => $likes,
                ],
                'viewer' => [
                    'liked' => $viewerLiked,
                ],
                'images' => $post->images->map(fn (CourseCommunityPostImage $img) => [
                    'url' => $img->url,
                ])->values(),
            ],
            'comments' => $comments->map(fn (CourseCommunityComment $c) => $this->commentForApi($c))->values(),
        ]);
    }

    public function createComment(Request $request, CourseCommunityPost $post): JsonResponse
    {
        if ($deny = $this->ensureInstructorPostAccess($request, $post)) {
            return $deny;
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:2000'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('course_community_comments', 'id')->where(fn ($q) => $q->where('post_id', $post->id)),
            ],
        ]);

        $body = trim($data['body']);
        if ($body === '') {
            throw ValidationException::withMessages([
                'body' => ['Comment body cannot be empty.'],
            ]);
        }

        $comment = CourseCommunityComment::create([
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
            'parent_id' => $data['parent_id'] ?? null,
            'body' => $body,
            'edited_at' => null,
        ]);

        return response()->json(['comment_id' => $comment->id], 201);
    }

    /**
     * @return array<string, mixed>
     */
    private function commentForApi(CourseCommunityComment $c): array
    {
        $parentPayload = null;
        if ($c->parent_id && $c->relationLoaded('parent') && $c->parent) {
            $parentPayload = [
                'id' => $c->parent->id,
                'user' => [
                    'name' => $c->parent->user?->name ?? '—',
                ],
                'body_preview' => Str::limit(trim(preg_replace('/\s+/u', ' ', strip_tags((string) $c->parent->body))), 120),
            ];
        }

        return [
            'id' => $c->id,
            'body' => $c->body,
            'parent_id' => $c->parent_id,
            'parent' => $parentPayload,
            'created_at' => $c->created_at?->toIso8601String(),
            'edited_at' => $c->edited_at?->toIso8601String(),
            'user' => [
                'id' => $c->user?->id,
                'name' => $c->user?->name,
                'profile_image_url' => $c->user?->profile_image_url,
            ],
        ];
    }

    public function reactToPost(Request $request, CourseCommunityPost $post): JsonResponse
    {
        if ($deny = $this->ensureInstructorPostAccess($request, $post)) {
            return $deny;
        }

        $user = $request->user();
        CourseCommunityReaction::firstOrCreate([
            'reactable_type' => CourseCommunityPost::class,
            'reactable_id' => $post->id,
            'user_id' => $user->id,
            'type' => 'like',
        ]);

        return response()->json(['ok' => true]);
    }

    public function unreactToPost(Request $request, CourseCommunityPost $post): JsonResponse
    {
        if ($deny = $this->ensureInstructorPostAccess($request, $post)) {
            return $deny;
        }

        $user = $request->user();
        CourseCommunityReaction::query()
            ->where('reactable_type', CourseCommunityPost::class)
            ->where('reactable_id', $post->id)
            ->where('user_id', $user->id)
            ->where('type', 'like')
            ->delete();

        return response()->json(['ok' => true]);
    }
}

