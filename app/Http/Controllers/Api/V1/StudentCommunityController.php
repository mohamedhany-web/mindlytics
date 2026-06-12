<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\CourseCommunityComment;
use App\Models\CourseCommunityPost;
use App\Models\CourseCommunityPostImage;
use App\Models\CourseCommunityReaction;
use App\Support\CourseCommunityFeedBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StudentCommunityController extends Controller
{
    /**
     * الطالب مشترك في كورس المنشور، والمنشور يظهر ضمن مجتمع الكورس (مؤلف نشط في الفوج أو منشور إداري/مدرب).
     */
    private function ensureStudentCommunityPostAccess(Request $request, CourseCommunityPost $post): ?JsonResponse
    {
        $user = $request->user();
        if (! $user->isEnrolledIn($post->course_id)) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }
        if (! $post->isAuthorVisibleInCommunityCohort()) {
            return response()->json(['message' => 'غير موجود'], 404);
        }

        return null;
    }

    public function courses(Request $request): JsonResponse
    {
        $user = $request->user();
        $courses = $user->activeCourses()->select('advanced_courses.id', 'advanced_courses.title', 'advanced_courses.title_en')->get();

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
        $user = $request->user();
        if (! $user->isEnrolledIn($course->id)) {
            return response()->json(['message' => 'كورس غير مسموح'], 403);
        }

        $postsQuery = CourseCommunityPost::query()
            ->where('course_id', $course->id)
            ->whereAuthorVisibleInCourse($course->id)
            ->with(['user:id,name,profile_image,updated_at,role', 'images'])
            ->withCount(['comments']);

        return CourseCommunityFeedBuilder::buildResponse($request, $course, $user, $postsQuery);
    }

    public function createPost(Request $request, AdvancedCourse $course): JsonResponse
    {
        $user = $request->user();
        if (! $user->isEnrolledIn($course->id)) {
            return response()->json(['message' => 'كورس غير مسموح'], 403);
        }

        if ($request->isJson()) {
            $data = $request->validate([
                'body' => ['required', 'string', 'min:2', 'max:4000'],
            ]);

            $post = CourseCommunityPost::create([
                'course_id' => $course->id,
                'user_id' => $user->id,
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
            'user_id' => $user->id,
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
        if ($deny = $this->ensureStudentCommunityPostAccess($request, $post)) {
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
        if ($deny = $this->ensureStudentCommunityPostAccess($request, $post)) {
            return $deny;
        }

        $user = $request->user();

        $data = $request->validate(
            [
                'body' => ['required', 'string', 'min:1', 'max:2000'],
                'parent_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('course_community_comments', 'id')->where(fn ($q) => $q->where('post_id', $post->id)),
                ],
            ],
            [
                'body.required' => 'اكتب نص التعليق.',
                'body.string' => 'نص التعليق غير صالح.',
                'body.min' => 'التعليق قصير جداً.',
                'body.max' => 'التعليق يتجاوز ٢٠٠٠ حرف.',
            ]
        );

        $body = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $data['body']) ?? '';
        if ($body === '') {
            throw ValidationException::withMessages([
                'body' => ['اكتب نص التعليق.'],
            ]);
        }

        $c = CourseCommunityComment::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'parent_id' => $data['parent_id'] ?? null,
            'body' => $body,
            'edited_at' => null,
        ]);

        return response()->json(['comment_id' => $c->id], 201);
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
        if ($deny = $this->ensureStudentCommunityPostAccess($request, $post)) {
            return $deny;
        }

        $user = $request->user();

        try {
            CourseCommunityReaction::firstOrCreate([
                'reactable_type' => CourseCommunityPost::class,
                'reactable_id' => $post->id,
                'user_id' => $user->id,
                'type' => 'like',
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'تعذر تسجيل التفاعل حالياً'], 500);
        }

        return response()->json(['ok' => true]);
    }

    public function unreactToPost(Request $request, CourseCommunityPost $post): JsonResponse
    {
        if ($deny = $this->ensureStudentCommunityPostAccess($request, $post)) {
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

