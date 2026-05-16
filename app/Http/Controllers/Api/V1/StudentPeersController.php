<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiPublicStudentResource;
use App\Models\Notification;
use App\Models\PeerConnectionRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentPeersController extends Controller
{
    public function recommended(Request $request): JsonResponse
    {
        $me = $request->user();
        $limit = min(max((int) $request->query('limit', 15), 1), 40);
        $q = trim((string) $request->query('q', ''));

        $myCourseIds = $this->activeAdvancedCourseIdsForUser($me->id);
        if ($myCourseIds->isEmpty()) {
            return response()->json(['users' => []]);
        }

        $optionalCourseId = $request->query('course_id');
        if ($optionalCourseId !== null && $optionalCourseId !== '') {
            $cid = (int) $optionalCourseId;
            if (! $myCourseIds->contains($cid)) {
                return response()->json([
                    'message' => 'غير مسجّل في هذا الكورس.',
                    'code' => 'course_not_enrolled',
                ], 403);
            }
            $courseScope = collect([$cid]);
        } else {
            $courseScope = $myCourseIds;
        }

        $excludeIds = $this->excludedPeerIdsForDiscovery($me->id);

        $query = User::query()
            ->students()
            ->active()
            ->whereKeyNot($me->id)
            ->whereNotIn('id', $excludeIds)
            ->whereExists(function ($sub) use ($courseScope) {
                $sub->select(DB::raw('1'))
                    ->from('student_course_enrollments as sce')
                    ->whereColumn('sce.user_id', 'users.id')
                    ->whereIn('sce.status', ['active', 'completed'])
                    ->whereIn('sce.advanced_course_id', $courseScope);
            });

        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where(function ($sub) use ($like) {
                $sub->where('name', 'like', $like)
                    ->orWhere('headline', 'like', $like)
                    ->orWhere('bio', 'like', $like);
            });
        }

        $users = $query->orderBy('name')->limit($limit)->get();

        return response()->json([
            'users' => ApiPublicStudentResource::collection($users)->resolve(),
        ]);
    }

    public function show(Request $request, User $user): JsonResponse
    {
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Use /student/profile', 'code' => 'self'], 400);
        }
        if (! $user->isStudent() || ! $user->is_active) {
            return response()->json(['message' => 'Not found', 'code' => 'not_found'], 404);
        }

        if ($deny = $this->ensureSharesActiveCourseWith($request->user(), $user)) {
            return $deny;
        }

        return response()->json([
            'user' => (new ApiPublicStudentResource($user))->resolve(),
        ]);
    }

    public function socialState(Request $request, User $user): JsonResponse
    {
        if ($user->id === $request->user()->id) {
            return response()->json(['state' => 'none']);
        }
        if (! $user->isStudent() || ! $user->is_active) {
            return response()->json(['message' => 'Not found', 'code' => 'not_found'], 404);
        }

        if ($deny = $this->ensureSharesActiveCourseWith($request->user(), $user)) {
            return $deny;
        }

        $me = $request->user()->id;
        $peer = $user->id;

        $row = PeerConnectionRequest::query()
            ->where(function ($q) use ($me, $peer) {
                $q->where(function ($q2) use ($me, $peer) {
                    $q2->where('requester_id', $me)->where('recipient_id', $peer);
                })->orWhere(function ($q2) use ($me, $peer) {
                    $q2->where('requester_id', $peer)->where('recipient_id', $me);
                });
            })
            ->first();

        if ($row === null) {
            return response()->json(['state' => 'none']);
        }
        if ($row->status === 'accepted') {
            return response()->json(['state' => 'connected']);
        }
        if ($row->status === 'declined') {
            return response()->json(['state' => 'none']);
        }
        if ((int) $row->requester_id === $me) {
            return response()->json(['state' => 'outgoing']);
        }

        return response()->json(['state' => 'incoming']);
    }

    public function connect(Request $request, User $user): JsonResponse
    {
        $me = $request->user();
        if ($user->id === $me->id) {
            return response()->json(['message' => 'Invalid peer', 'code' => 'self'], 400);
        }
        if (! $user->isStudent() || ! $user->is_active) {
            return response()->json(['message' => 'Not found', 'code' => 'not_found'], 404);
        }

        if ($deny = $this->ensureSharesActiveCourseWith($me, $user)) {
            return $deny;
        }

        $existing = PeerConnectionRequest::query()
            ->where(function ($q) use ($me, $user) {
                $q->where('requester_id', $me->id)->where('recipient_id', $user->id);
            })->orWhere(function ($q) use ($me, $user) {
                $q->where('requester_id', $user->id)->where('recipient_id', $me->id);
            })
            ->first();

        if ($existing !== null) {
            if ($existing->status === 'accepted') {
                return response()->json(['message' => 'Already connected', 'code' => 'already_connected', 'state' => 'connected'], 422);
            }
            if ($existing->status === 'pending') {
                if ((int) $existing->requester_id === $me->id) {
                    return response()->json(['message' => 'Request already sent', 'code' => 'duplicate', 'state' => 'outgoing']);
                }

                return response()->json([
                    'message' => 'This user already sent you a request. Check notifications.',
                    'code' => 'incoming_exists',
                    'state' => 'incoming',
                ], 409);
            }
            if ($existing->status === 'declined') {
                $existing->delete();
            }
        }

        DB::transaction(function () use ($me, $user) {
            PeerConnectionRequest::create([
                'requester_id' => $me->id,
                'recipient_id' => $user->id,
                'status' => 'pending',
            ]);

            Notification::sendToUser($user->id, [
                'sender_id' => $me->id,
                'title' => 'New connection request',
                'message' => $me->name.' wants to connect with you on Mindlytics.',
                'type' => 'general',
                'priority' => 'normal',
                'audience' => 'student',
                'action_url' => null,
                'action_text' => null,
            ]);
        });

        return response()->json([
            'message' => 'Request sent',
            'state' => 'outgoing',
        ], 201);
    }

    public function incoming(Request $request): JsonResponse
    {
        $me = $request->user()->id;
        $myCourseIds = $this->activeAdvancedCourseIdsForUser($me);
        if ($myCourseIds->isEmpty()) {
            return response()->json(['requests' => []]);
        }

        $rows = PeerConnectionRequest::query()
            ->where('recipient_id', $me)
            ->where('status', 'pending')
            ->whereHas('requester', function ($q) use ($myCourseIds) {
                $q->whereExists(function ($sub) use ($myCourseIds) {
                    $sub->select(DB::raw('1'))
                        ->from('student_course_enrollments as sce')
                        ->whereColumn('sce.user_id', 'users.id')
                        ->where('sce.status', 'active')
                        ->whereIn('sce.advanced_course_id', $myCourseIds);
                });
            })
            ->with(['requester:id,name,profile_image,profile_image_disk,headline'])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $items = $rows->map(function (PeerConnectionRequest $r) {
            $from = $r->requester;

            return [
                'from_user_id' => $r->requester_id,
                'name' => $from?->name ?? 'User',
                'headline' => $from?->headline,
                'profile_image_url' => $from?->profile_image_url,
            ];
        })->all();

        return response()->json(['requests' => $items]);
    }

    public function accept(Request $request, User $user): JsonResponse
    {
        $me = $request->user();
        if ($user->id === $me->id) {
            return response()->json(['message' => 'Invalid', 'code' => 'self'], 400);
        }

        $row = PeerConnectionRequest::query()
            ->where('requester_id', $user->id)
            ->where('recipient_id', $me->id)
            ->where('status', 'pending')
            ->first();

        if ($row === null) {
            return response()->json(['message' => 'No pending request', 'code' => 'not_found'], 404);
        }

        $row->update(['status' => 'accepted']);

        Notification::sendToUser($user->id, [
            'sender_id' => $me->id,
            'title' => 'Connection accepted',
            'message' => $me->name.' accepted your connection request.',
            'type' => 'general',
            'priority' => 'normal',
            'audience' => 'student',
        ]);

        return response()->json(['message' => 'Accepted', 'state' => 'connected']);
    }

    public function decline(Request $request, User $user): JsonResponse
    {
        $me = $request->user();

        $row = PeerConnectionRequest::query()
            ->where('requester_id', $user->id)
            ->where('recipient_id', $me->id)
            ->where('status', 'pending')
            ->first();

        if ($row === null) {
            return response()->json(['message' => 'No pending request', 'code' => 'not_found'], 404);
        }

        $row->update(['status' => 'declined']);

        return response()->json(['message' => 'Declined', 'state' => 'none']);
    }

    public function cancelOutgoing(Request $request, User $user): JsonResponse
    {
        $me = $request->user();
        $deleted = PeerConnectionRequest::query()
            ->where('requester_id', $me->id)
            ->where('recipient_id', $user->id)
            ->where('status', 'pending')
            ->delete();

        return response()->json([
            'message' => $deleted ? 'Cancelled' : 'No pending request',
            'state' => 'none',
        ]);
    }

    /**
     * @return array<int, int>
     */
    private function excludedPeerIdsForDiscovery(int $myUserId): array
    {
        $exclude = collect([$myUserId]);

        // أظهر زملاء الكورس حتى مع طلبات معلّقة؛ نستبعد فقط المرتبطين فعلياً بعد القبول.
        $connected = PeerConnectionRequest::query()
            ->where('status', 'accepted')
            ->where(function ($q) use ($myUserId) {
                $q->where('requester_id', $myUserId)->orWhere('recipient_id', $myUserId);
            })
            ->get();

        foreach ($connected as $r) {
            $other = (int) $r->requester_id === $myUserId ? (int) $r->recipient_id : (int) $r->requester_id;
            $exclude->push($other);
        }

        return $exclude->unique()->values()->all();
    }

    /**
     * @return \Illuminate\Support\Collection<int, int>
     */
    private function activeAdvancedCourseIdsForUser(int $userId)
    {
        return DB::table('student_course_enrollments')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->pluck('advanced_course_id')
            ->unique()
            ->values();
    }

    private function ensureSharesActiveCourseWith(User $me, User $peer): ?JsonResponse
    {
        $myCourses = $this->activeAdvancedCourseIdsForUser($me->id);
        if ($myCourses->isEmpty()) {
            return response()->json([
                'message' => 'لا يوجد لديك كورس نشط.',
                'code' => 'no_courses',
            ], 403);
        }

        $shared = DB::table('student_course_enrollments')
            ->where('user_id', $peer->id)
            ->whereIn('status', ['active', 'completed'])
            ->whereIn('advanced_course_id', $myCourses)
            ->exists();

        if (! $shared) {
            return response()->json([
                'message' => 'Not found',
                'code' => 'not_found',
            ], 404);
        }

        return null;
    }
}
