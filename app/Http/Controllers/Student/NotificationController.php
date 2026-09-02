<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->customNotifications()->with(['sender'])
            ->where(function ($q) {
                $q->whereNull('audience')->orWhere('audience', 'student');
            });

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'read') {
                $query->where('is_read', true);
            } elseif ($request->status === 'unread') {
                $query->where('is_read', false);
            }
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('q')) {
            $term = trim((string) $request->q);
            if ($term !== '') {
                $query->where(function ($q) use ($term) {
                    $q->where('title', 'like', '%'.$term.'%')
                        ->orWhere('message', 'like', '%'.$term.'%');
                });
            }
        }

        $notifications = $query->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderBy('is_read', 'asc')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $baseStudentNotifications = Auth::user()->customNotifications()
            ->where(function ($q) {
                $q->whereNull('audience')->orWhere('audience', 'student');
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });

        $stats = [
            'total' => (clone $baseStudentNotifications)->count(),
            'unread' => (clone $baseStudentNotifications)->unread()->count(),
            'today' => (clone $baseStudentNotifications)->whereDate('created_at', today())->count(),
            'urgent' => (clone $baseStudentNotifications)->where('priority', 'urgent')->unread()->count(),
        ];

        $notificationTypes = Notification::getTypes();
        $priorities = Notification::getPriorities();

        return view('student.notifications.index', compact('notifications', 'stats', 'notificationTypes', 'priorities'));
    }

    public function show(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return redirect()->route('notifications')->with('error', __('student.notif_forbidden'));
        }
        if ($notification->audience !== null && $notification->audience !== 'student') {
            return redirect()->route('notifications')->with('error', __('student.notif_not_for_students'));
        }

        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        $notification->load(['sender']);

        $otherNotifications = Auth::user()->customNotifications()
            ->where(function ($q) {
                $q->whereNull('audience')->orWhere('audience', 'student');
            })
            ->where('id', '!=', $notification->id)
            ->valid()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $notificationTypes = Notification::getTypes();
        $priorities = Notification::getPriorities();

        return view('student.notifications.show', compact('notification', 'otherNotifications', 'notificationTypes', 'priorities'));
    }

    public function go(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return redirect()->route('notifications')->with('error', __('student.notif_forbidden'));
        }
        if ($notification->audience !== null && $notification->audience !== 'student') {
            return redirect()->route('notifications')->with('error', __('student.notif_not_for_students'));
        }
        if (empty($notification->action_url)) {
            return redirect()->route('notifications');
        }

        $url = $notification->action_url;
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '/';
        $host = $parsed['host'] ?? null;
        $appUrl = parse_url(config('app.url'));
        $appHost = $appUrl['host'] ?? null;

        if ($host && $host !== $appHost) {
            return redirect()->route('notifications')->with('error', __('student.notif_link_forbidden'));
        }
        if (preg_match('#^/(employee|admin)(/|$)#', $path)) {
            return redirect()->route('notifications')->with('error', __('student.notif_link_forbidden'));
        }

        return redirect()->to($url);
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => __('student.notif_forbidden')], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $count = Auth::user()
            ->customNotifications()
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => __('student.notif_marked_read_count', ['count' => $count]),
            'count' => $count,
        ]);
    }

    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => __('student.notif_forbidden')], 403);
        }

        $notification->delete();

        return response()->json(['success' => true, 'message' => __('student.notif_deleted_success')]);
    }

    public function getUnreadCount()
    {
        $count = Auth::user()->customNotifications()->unread()->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })->count();

        return response()->json(['count' => $count]);
    }

    public function getRecent()
    {
        $notifications = Auth::user()
            ->customNotifications()
            ->with(['sender'])
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json($notifications);
    }

    public function cleanup()
    {
        $count = Auth::user()
            ->customNotifications()
            ->where('is_read', true)
            ->where('created_at', '<', now()->subDays(30))
            ->delete();

        return response()->json([
            'success' => true,
            'message' => __('student.notif_cleanup_count', ['count' => $count]),
            'count' => $count,
        ]);
    }
}
