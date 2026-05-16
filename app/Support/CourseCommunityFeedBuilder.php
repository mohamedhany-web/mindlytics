<?php

namespace App\Support;

use App\Models\AdvancedCourse;
use App\Models\CourseCommunityComment;
use App\Models\CourseCommunityPost;
use App\Models\CourseCommunityPostImage;
use App\Models\CourseCommunityReaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * خلاصة مجتمع الكورس: ترقيم بالمؤشر (cursor) أو بالصفحة (legacy).
 *
 * ترتيب المنشورات: المثبتة أولاً ثم id تنازليًا — المؤشر يرمز إلى (is_pinned, id) لآخر عنصر في الصفحة.
 */
final class CourseCommunityFeedBuilder
{
    public static function buildResponse(
        Request $request,
        AdvancedCourse $course,
        User $user,
        Builder $postsQuery,
    ): JsonResponse {
        $perPage = (int) $request->input('per_page', 15);
        $perPage = max(5, min(30, $perPage));

        $useLegacyPage = $request->filled('page') && ! $request->filled('cursor');

        if ($useLegacyPage) {
            return self::legacyPageResponse($request, $course, $user, $postsQuery, $perPage);
        }

        return self::cursorResponse($request, $course, $user, $postsQuery, $perPage);
    }

    private static function cursorResponse(
        Request $request,
        AdvancedCourse $course,
        User $user,
        Builder $postsQuery,
        int $perPage,
    ): JsonResponse {
        $q = clone $postsQuery;

        $cursor = $request->string('cursor')->trim()->toString();
        if ($cursor !== '') {
            $decoded = self::decodeCursor($cursor);
            if ($decoded !== null) {
                $p = (int) ($decoded['p'] ?? 0);
                $id = (int) ($decoded['i'] ?? 0);
                if ($id > 0) {
                    $q->where(function ($w) use ($p, $id) {
                        if ($p === 1) {
                            $w->where(function ($inner) use ($id) {
                                $inner->where('is_pinned', true)->where('id', '<', $id);
                            })->orWhere('is_pinned', false);
                        } else {
                            $w->where('is_pinned', false)->where('id', '<', $id);
                        }
                    });
                }
            }
        }

        $limit = $perPage + 1;
        /** @var Collection<int, CourseCommunityPost> $posts */
        $posts = $q->orderByDesc('is_pinned')->orderByDesc('id')->limit($limit)->get();
        $hasMore = $posts->count() > $perPage;
        $slice = $posts->take($perPage);
        $last = $slice->last();
        $nextCursor = ($hasMore && $last instanceof CourseCommunityPost) ? self::encodeCursor($last) : null;

        $data = self::serializePosts($slice, $user);

        return response()->json([
            'course' => self::courseBlock($course),
            'posts' => $data,
            'pagination' => [
                'per_page' => $perPage,
                'has_more' => $hasMore,
                'next_cursor' => $nextCursor,
                'next_page_url' => null,
            ],
        ]);
    }

    private static function legacyPageResponse(
        Request $request,
        AdvancedCourse $course,
        User $user,
        Builder $postsQuery,
        int $perPage,
    ): JsonResponse {
        $posts = (clone $postsQuery)
            ->orderByDesc('is_pinned')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        $data = self::serializePosts($posts->getCollection(), $user);

        return response()->json([
            'course' => self::courseBlock($course),
            'posts' => $data,
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'next_page_url' => $posts->nextPageUrl(),
                'has_more' => $posts->hasMorePages(),
                'next_cursor' => null,
            ],
        ]);
    }

    /**
     * @param  Collection<int, CourseCommunityPost>  $collection
     * @return array<int, array<string, mixed>>
     */
    private static function serializePosts(Collection $collection, User $user): array
    {
        $postIds = $collection->pluck('id');
        if ($postIds->isEmpty()) {
            return [];
        }

        $reactionsCount = CourseCommunityReaction::query()
            ->where('reactable_type', CourseCommunityPost::class)
            ->whereIn('reactable_id', $postIds)
            ->where('type', 'like')
            ->selectRaw('reactable_id, COUNT(*) as c')
            ->groupBy('reactable_id')
            ->pluck('c', 'reactable_id');

        $myReactions = CourseCommunityReaction::query()
            ->where('reactable_type', CourseCommunityPost::class)
            ->whereIn('reactable_id', $postIds)
            ->where('type', 'like')
            ->where('user_id', $user->id)
            ->pluck('reactable_id')
            ->flip();

        $latestByPostId = collect();
        $latestIds = DB::table('course_community_comments')
            ->selectRaw('post_id, MAX(id) as max_id')
            ->whereIn('post_id', $postIds)
            ->groupBy('post_id')
            ->pluck('max_id', 'post_id');
        if ($latestIds->isNotEmpty()) {
            $latestByPostId = CourseCommunityComment::query()
                ->whereIn('id', $latestIds->values())
                ->with(['user:id,name', 'parent.user:id,name'])
                ->get()
                ->keyBy('post_id');
        }

        return $collection->map(function (CourseCommunityPost $p) use ($reactionsCount, $myReactions, $latestByPostId) {
            /** @var CourseCommunityComment|null $last */
            $last = $latestByPostId->get($p->id);

            return [
                'id' => $p->id,
                'course_id' => $p->course_id,
                'body' => $p->body,
                'is_pinned' => (bool) $p->is_pinned,
                'created_at' => $p->created_at?->toIso8601String(),
                'edited_at' => $p->edited_at?->toIso8601String(),
                'user' => [
                    'id' => $p->user?->id,
                    'name' => $p->user?->name,
                    'profile_image_url' => $p->user?->profile_image_url,
                ],
                'counts' => [
                    'comments' => (int) ($p->comments_count ?? 0),
                    'likes' => (int) ($reactionsCount[$p->id] ?? 0),
                ],
                'viewer' => [
                    'liked' => $myReactions->has($p->id),
                ],
                'images' => $p->images->map(fn (CourseCommunityPostImage $img) => [
                    'url' => $img->url,
                ])->values(),
                'last_comment' => $last ? [
                    'body_preview' => Str::limit(trim(preg_replace('/\s+/u', ' ', strip_tags((string) $last->body))), 140),
                    'author_name' => $last->user?->name ?? '—',
                    'is_reply' => (bool) $last->parent_id,
                    'in_reply_to' => $last->parent_id ? ($last->parent?->user?->name) : null,
                ] : null,
            ];
        })->values()->all();
    }

    /**
     * @return array<string, mixed>
     */
    private static function courseBlock(AdvancedCourse $course): array
    {
        return [
            'id' => $course->id,
            'title' => [
                'ar' => $course->title,
                'en' => $course->title_en ?: $course->title,
            ],
        ];
    }

    private static function encodeCursor(CourseCommunityPost $last): string
    {
        $payload = json_encode([
            'p' => $last->is_pinned ? 1 : 0,
            'i' => $last->id,
        ], JSON_THROW_ON_ERROR);

        return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    }

    /**
     * @return array{p: int, i: int}|null
     */
    private static function decodeCursor(string $raw): ?array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }
        $b64 = strtr($raw, '-_', '+/');
        $pad = strlen($b64) % 4;
        if ($pad > 0) {
            $b64 .= str_repeat('=', 4 - $pad);
        }
        $json = base64_decode($b64, true);
        if ($json === false) {
            return null;
        }
        $data = json_decode($json, true);
        if (! is_array($data)) {
            return null;
        }
        if (! isset($data['i']) || ! is_numeric($data['i'])) {
            return null;
        }

        return [
            'p' => isset($data['p']) ? (int) $data['p'] : 0,
            'i' => (int) $data['i'],
        ];
    }
}
