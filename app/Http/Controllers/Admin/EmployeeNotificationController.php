<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class EmployeeNotificationController extends Controller
{
    /**
     * عرض قائمة إشعارات الموظفين
     */
    public function index(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        $query = Notification::with(['user', 'sender'])
                            ->where('sender_id', Auth::id())
                            ->where('type', 'employee'); // إشعارات الموظفين فقط

        // فلترة
        if ($request->filled('status')) {
            if ($request->status === 'read') {
                $query->where('is_read', true);
            } elseif ($request->status === 'unread') {
                $query->where('is_read', false)->whereNull('read_at');
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        // إحصائيات
        $stats = [
            'total' => Notification::where('type', 'employee')->where('sender_id', Auth::id())->count(),
            'unread' => Notification::where('type', 'employee')
                ->where('sender_id', Auth::id())
                ->where('is_read', false)
                ->whereNull('read_at')
                ->count(),
            'today' => Notification::where('type', 'employee')
                ->where('sender_id', Auth::id())
                ->whereDate('created_at', today())
                ->count(),
        ];

        return view('admin.employee-notifications.index', compact('notifications', 'stats'));
    }

    /**
     * عرض صفحة إنشاء إشعار جديد
     */
    public function create()
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بإنشاء إشعارات');
        }

        $employees = User::where('is_employee', true)
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->get();

        $priorities = Notification::getPriorities();

        return view('admin.employee-notifications.create', compact('employees', 'priorities'));
    }

    /**
     * إرسال إشعار للموظفين
     */
    public function store(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بإرسال إشعارات');
        }

        // Rate Limiting
        $key = 'employee_notification_send_' . Auth::id();
        $maxAttempts = 20;
        $decayMinutes = 5;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return back()
                ->withErrors(['rate_limit' => "تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد {$seconds} ثانية."])
                ->withInput();
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'priority' => 'required|in:low,normal,high,urgent',
            'target_type' => 'required|in:all_employees,specific_employee',
            'employee_id' => 'required_if:target_type,specific_employee|nullable|exists:users,id',
        ], [
            'title.required' => 'عنوان الإشعار مطلوب',
            'title.max' => 'عنوان الإشعار يجب ألا يتجاوز 255 حرف',
            'message.required' => 'محتوى الإشعار مطلوب',
            'message.max' => 'محتوى الإشعار يجب ألا يتجاوز 2000 حرف',
            'priority.required' => 'الأولوية مطلوبة',
            'priority.in' => 'الأولوية المحددة غير صحيحة',
            'target_type.required' => 'نوع الهدف مطلوب',
            'target_type.in' => 'نوع الهدف المحدد غير صحيح',
            'employee_id.required_if' => 'يجب اختيار موظف عند اختيار موظف محدد',
            'employee_id.exists' => 'الموظف المحدد غير موجود',
        ]);

        try {
            DB::beginTransaction();

            $data = [
                'sender_id' => Auth::id(),
                'title' => strip_tags(trim($validated['title'])),
                'message' => strip_tags(trim($validated['message'])),
                'type' => 'employee',
                'priority' => $validated['priority'],
                'audience' => 'employee',
                'is_read' => false,
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $sentCount = 0;

            if ($validated['target_type'] === 'all_employees') {
                // إرسال لجميع الموظفين
                $employeeIds = User::where('is_employee', true)
                                  ->where('is_active', true)
                                  ->pluck('id');
                
                if ($employeeIds->isEmpty()) {
                    return back()
                        ->withErrors(['error' => 'لا يوجد موظفين نشطين لإرسال الإشعار لهم'])
                        ->withInput();
                }
                
                $sentCount = Notification::sendToEmployees($employeeIds, $data);
            } else {
                // إرسال لموظف معين
                if (empty($validated['employee_id'])) {
                    return back()
                        ->withErrors(['employee_id' => 'يجب اختيار موظف'])
                        ->withInput();
                }
                
                $sentCount = Notification::sendToEmployee($validated['employee_id'], $data);
            }

            if ($sentCount === 0) {
                return back()
                    ->withErrors(['error' => 'فشل إرسال الإشعار. يرجى المحاولة مرة أخرى.'])
                    ->withInput();
            }

            DB::commit();
            RateLimiter::clear($key);

            return redirect()->route('admin.employee-notifications.index')
                ->with('success', "تم إرسال الإشعار بنجاح إلى {$sentCount} موظف");

        } catch (\Exception $e) {
            DB::rollBack();
            RateLimiter::clear($key);
            
            \Log::error('Error sending employee notification: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->except(['_token']),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withErrors(['error' => 'حدث خطأ أثناء إرسال الإشعار: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * عرض تفاصيل الإشعار
     */
    public function show(Notification $notification)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        if ($notification->sender_id !== Auth::id() || $notification->type !== 'employee') {
            abort(403, 'غير مصرح لك بعرض هذا الإشعار');
        }

        $notification->load(['user', 'sender']);

        return view('admin.employee-notifications.show', compact('notification'));
    }
}
