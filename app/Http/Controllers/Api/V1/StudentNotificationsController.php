<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * إشعارات الطالب — نفس جدول/نموذج [Notification] المستخدم في لوحة الموقع.
 */
class StudentNotificationsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = $user->customNotifications()
            ->with(['sender'])
            ->where(function ($q) {
                $q->whereNull('audience')->orWhere('audience', 'student');
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderBy('is_read', 'asc')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }
        if ($request->filled('status')) {
            if ($request->string('status') === 'read') {
                $query->where('is_read', true);
            } elseif ($request->string('status') === 'unread') {
                $query->where('is_read', false);
            }
        }

        $perPage = min(max((int) $request->input('per_page', 20), 1), 50);
        $paginator = $query->paginate($perPage);

        $base = $user->customNotifications()
            ->where(function ($q) {
                $q->whereNull('audience')->orWhere('audience', 'student');
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });

        $stats = [
            'unread' => (clone $base)->unread()->count(),
            'total' => (clone $base)->count(),
        ];

        return response()->json([
            'notifications' => $paginator->getCollection()
                ->map(fn (Notification $n) => $this->serialize($n))
                ->values()
                ->all(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'stats' => $stats,
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $user = $request->user();
        $count = $user->customNotifications()
            ->unread()
            ->where(function ($q) {
                $q->whereNull('audience')->orWhere('audience', 'student');
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    public function markRead(Request $request, Notification $notification): JsonResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح', 'code' => 'forbidden'], 403);
        }
        if ($notification->audience !== null && $notification->audience !== 'student') {
            return response()->json(['message' => 'هذا الإشعار غير موجّه للطلاب', 'code' => 'invalid_audience'], 403);
        }
        if (! $notification->is_read) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true, 'notification' => $this->serialize($notification->fresh())]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $user = $request->user();
        $count = $user->customNotifications()
            ->unread()
            ->where(function ($q) {
                $q->whereNull('audience')->orWhere('audience', 'student');
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'updated' => $count,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function serialize(Notification $n): array
    {
        $n->loadMissing('sender');

        return [
            'id' => $n->id,
            'title' => $n->title,
            'message' => $n->message,
            'type' => $n->type,
            'priority' => $n->priority,
            'is_read' => $n->is_read,
            'read_at' => $n->read_at?->toIso8601String(),
            'created_at' => $n->created_at?->toIso8601String(),
            'action_url' => $n->action_url,
            'action_text' => $n->action_text,
            'sender_name' => $n->sender?->name,
        ];
    }
}
