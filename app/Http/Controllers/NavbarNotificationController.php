<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NavbarNotificationController extends Controller
{
    public function unreadCount(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        $user = Auth::user();

        $count = $user->notifications()
            ->valid()
            ->unread()
            ->where(function ($q) use ($user) {
                // audience قد يكون null للإشعارات العامة
                if ($user->isEmployee()) {
                    $q->whereNull('audience')->orWhere('audience', 'employee');
                } elseif ($user->isInstructor() || $user->isTeacher()) {
                    $q->whereNull('audience')->orWhere('audience', 'instructor');
                } else {
                    $q->whereNull('audience')->orWhere('audience', 'student');
                }
            })
            ->count();

        return response()->json(['count' => (int) $count]);
    }

    public function recent(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['unread_count' => 0, 'items' => []]);
        }

        $limit = (int) $request->input('limit', 8);
        if ($limit < 1 || $limit > 20) {
            $limit = 8;
        }

        $user = Auth::user();

        $base = $user->notifications()
            ->valid()
            ->where(function ($q) use ($user) {
                if ($user->isEmployee()) {
                    $q->whereNull('audience')->orWhere('audience', 'employee');
                } elseif ($user->isInstructor() || $user->isTeacher()) {
                    $q->whereNull('audience')->orWhere('audience', 'instructor');
                } else {
                    $q->whereNull('audience')->orWhere('audience', 'student');
                }
            });

        $unreadCount = (clone $base)->unread()->count();

        $items = (clone $base)
            ->orderByDesc('created_at')
            ->take($limit)
            ->get()
            ->map(function (Notification $n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'type' => $n->type,
                    'type_icon' => $n->type_icon,
                    'type_color' => $n->type_color,
                    'priority' => $n->priority,
                    'is_read' => (bool) $n->is_read,
                    'action_url' => $n->action_url,
                    'action_text' => $n->action_text,
                    'created_at' => optional($n->created_at)->toIso8601String(),
                    'created_human' => $n->created_at ? $n->created_at->diffForHumans() : null,
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'unread_count' => (int) $unreadCount,
            'items' => $items,
        ]);
    }

    public function markRead(Request $request, Notification $notification)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }

        if ((int) $notification->user_id !== (int) Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }

        $user = Auth::user();

        $updated = $user->notifications()
            ->valid()
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true, 'count' => (int) $updated]);
    }
}

