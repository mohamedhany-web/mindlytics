<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeNotificationController extends Controller
{
    /**
     * عرض الإشعارات للموظف
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isEmployee()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $query = $user->notifications()->with(['sender'])
            ->where('audience', 'employee');

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'read') {
                $query->where('is_read', true);
            } elseif ($request->status === 'unread') {
                $query->where('is_read', false)->whereNull('read_at');
            }
        }

        // فلترة حسب الأولوية
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $notifications = $query->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderBy('is_read', 'asc')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // إحصائيات (إشعارات الموظف فقط)
        $baseQuery = $user->notifications()->where('audience', 'employee')
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'unread' => (clone $baseQuery)->where('is_read', false)->whereNull('read_at')->count(),
            'today' => (clone $baseQuery)->whereDate('created_at', today())->count(),
            'urgent' => (clone $baseQuery)->where('priority', 'urgent')
                ->where('is_read', false)->whereNull('read_at')->count(),
        ];

        $notificationTypes = Notification::getTypes();
        $priorities = Notification::getPriorities();

        return view('employee.notifications.index', compact('notifications', 'stats', 'notificationTypes', 'priorities'));
    }

    /**
     * عرض تفاصيل الإشعار
     */
    public function show(Notification $notification)
    {
        $user = Auth::user();
        
        if (!$user->isEmployee()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        // التحقق من الصلاحية والمستهدف (منع عرض إشعارات الطلاب في لوحة الموظف)
        if ($notification->user_id !== $user->id) {
            return redirect()->route('employee.notifications')->with('error', 'غير مصرح لك بعرض هذا الإشعار');
        }
        if ($notification->audience !== 'employee') {
            return redirect()->route('employee.notifications')->with('error', 'هذا الإشعار غير موجّه للموظفين');
        }

        // تحديد كمقروء
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        $notification->load(['sender']);

        return view('employee.notifications.show', compact('notification'));
    }

    /**
     * انتقال آمن لرابط الإشعار (منع فتح روابط لوحة الطالب)
     */
    public function go(Notification $notification)
    {
        $user = Auth::user();
        if ($notification->user_id !== $user->id) {
            return redirect()->route('employee.notifications')->with('error', 'غير مصرح');
        }
        if ($notification->audience !== 'employee') {
            return redirect()->route('employee.notifications')->with('error', 'هذا الإشعار غير موجّه للموظفين');
        }
        if (empty($notification->action_url)) {
            return redirect()->route('employee.notifications');
        }
        $url = $notification->action_url;
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '/';
        $host = $parsed['host'] ?? null;
        $appUrl = parse_url(config('app.url'));
        $appHost = $appUrl['host'] ?? null;
        if ($host && $host !== $appHost) {
            return redirect()->route('employee.notifications')->with('error', 'رابط غير مسموح');
        }
        if (!preg_match('#^/employee(/|$)#', $path)) {
            return redirect()->route('employee.notifications')->with('error', 'رابط غير مسموح للموظف');
        }
        return redirect()->to($url);
    }

    /**
     * تحديد الإشعار كمقروء
     */
    public function markAsRead(Notification $notification)
    {
        $user = Auth::user();
        
        if ($notification->user_id !== $user->id) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * تحديد جميع الإشعارات كمقروءة
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        $count = $user->notifications()
                    ->where('audience', 'employee')
                    ->where('is_read', false)
                    ->whereNull('read_at')
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                    ]);

        return response()->json([
            'success' => true,
            'message' => "تم تحديد {$count} إشعار كمقروء",
            'count' => $count,
        ]);
    }

    /**
     * الحصول على الإشعارات غير المقروءة (API)
     */
    public function getUnread()
    {
        $user = Auth::user();
        
        if (!$user->isEmployee()) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        $notifications = $user->notifications()
            ->where('audience', 'employee')
            ->where('is_read', false)
            ->whereNull('read_at')
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->with(['sender'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'priority' => $notification->priority,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'sender' => $notification->sender ? $notification->sender->name : 'النظام',
                    'action_url' => route('employee.notifications.go', $notification),
                    'action_text' => $notification->action_text,
                ];
            });

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'count' => $notifications->count(),
        ]);
    }
}
