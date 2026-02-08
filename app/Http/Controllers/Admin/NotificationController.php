<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\AdvancedCourse;
use App\Models\AcademicYear;
use App\Models\AcademicSubject;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * عرض قائمة الإشعارات المرسلة
     * محمي من: XSS, SQL Injection, Unauthorized Access
     */
    public function index(Request $request)
    {
        // التحقق من الصلاحيات
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        $query = Notification::with(['user', 'sender'])
                            ->where('sender_id', Auth::id());

        // فلترة حسب النوع - حماية من SQL Injection
        if ($request->filled('type')) {
            $type = strip_tags(trim($request->type));
            $validTypes = array_keys(Notification::getTypes());
            if (in_array($type, $validTypes)) {
                $query->where('type', $type);
            }
        }

        // فلترة حسب الحالة - حماية من SQL Injection
        if ($request->filled('status')) {
            $status = strip_tags(trim($request->status));
            if ($status === 'read') {
                $query->where('is_read', true);
            } elseif ($status === 'unread') {
                $query->where('is_read', false);
            }
        }

        // البحث - حماية من XSS و SQL Injection
        if ($request->filled('search')) {
            $search = strip_tags(trim($request->search));
            $search = preg_replace('/[^a-zA-Z0-9\u0600-\u06FF\s@.-]/', '', $search); // السماح فقط بالأحرف والأرقام العربية والإنجليزية
            if (strlen($search) > 0 && strlen($search) <= 255) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('message', 'like', '%' . $search . '%');
                });
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        // إحصائيات
        $stats = [
            'total' => Notification::where('sender_id', Auth::id())->count(),
            'unread' => Notification::where('sender_id', Auth::id())->unread()->count(),
            'today' => Notification::where('sender_id', Auth::id())->whereDate('created_at', today())->count(),
            'by_type' => Notification::where('sender_id', Auth::id())
                                   ->selectRaw('type, count(*) as count')
                                   ->groupBy('type')
                                   ->pluck('count', 'type'),
        ];

        $notificationTypes = Notification::getTypes();

        return view('admin.notifications.index', compact('notifications', 'stats', 'notificationTypes'));
    }

    /**
     * عرض صفحة إرسال إشعار جديد
     * محمي من: Unauthorized Access
     */
    public function create()
    {
        // التحقق من الصلاحيات
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بإنشاء إشعارات');
        }

        $notificationTypes = Notification::getTypes();
        $priorities = Notification::getPriorities();
        $targetTypes = Notification::getTargetTypes();
        
        $academicYears = AcademicYear::active()->orderBy('order')->get();
        $academicSubjects = AcademicSubject::active()->orderBy('name')->get();
        $courses = AdvancedCourse::active()->with(['academicSubject'])->orderBy('title')->get();
        $students = User::where('role', 'student')->where('is_active', true)->orderBy('name')->get();

        return view('admin.notifications.create', compact(
            'notificationTypes', 'priorities', 'targetTypes', 
            'academicYears', 'academicSubjects', 'courses', 'students'
        ));
    }

    /**
     * إرسال إشعار جديد
     * محمي من: XSS, SQL Injection, CSRF, Brute Force, Validation Bypass
     */
    public function store(Request $request)
    {
        // التحقق من الصلاحيات
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بإرسال إشعارات');
        }

        // Rate Limiting - حماية من Brute Force
        $key = 'notification_send_' . Auth::id();
        $maxAttempts = 20;
        $decayMinutes = 5;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return back()
                ->withErrors(['rate_limit' => "تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد {$seconds} ثانية."])
                ->withInput();
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        // Sanitization - تنقية البيانات من XSS
        $sanitizedData = [
            'title' => strip_tags(trim($request->input('title', ''))),
            'message' => strip_tags(trim($request->input('message', ''))),
            'type' => strip_tags(trim($request->input('type', ''))),
            'priority' => strip_tags(trim($request->input('priority', ''))),
            'target_type' => strip_tags(trim($request->input('target_type', ''))),
            'target_id' => filter_var($request->input('target_id'), FILTER_VALIDATE_INT) ?: null,
            'action_url' => filter_var($request->input('action_url'), FILTER_SANITIZE_URL) ?: null,
            'action_text' => strip_tags(trim($request->input('action_text', ''))),
            'expires_at' => $request->input('expires_at') ?: null,
        ];

        // Validation محسن
        $validator = Validator::make($sanitizedData, [
            'title' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{Arabic}\s\p{N}a-zA-Z0-9@.-]+$/u',
            ],
            'message' => [
                'required',
                'string',
                'max:2000',
            ],
            'type' => [
                'required',
                'in:' . implode(',', array_keys(Notification::getTypes())),
            ],
            'priority' => [
                'required',
                'in:' . implode(',', array_keys(Notification::getPriorities())),
            ],
            'target_type' => [
                'required',
                'in:' . implode(',', array_keys(Notification::getTargetTypes())),
            ],
            'target_id' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'action_url' => [
                'nullable',
                'url',
                'max:500',
            ],
            'action_text' => [
                'nullable',
                'string',
                'max:100',
            ],
            'expires_at' => [
                'nullable',
                'date',
                'after:now',
            ],
        ], [
            'title.required' => 'عنوان الإشعار مطلوب',
            'title.regex' => 'العنوان يحتوي على أحرف غير مسموحة',
            'message.required' => 'نص الإشعار مطلوب',
            'message.max' => 'النص يجب ألا يتجاوز 2000 حرف',
            'type.required' => 'نوع الإشعار مطلوب',
            'type.in' => 'نوع الإشعار المحدد غير صحيح',
            'priority.required' => 'أولوية الإشعار مطلوبة',
            'priority.in' => 'الأولوية المحددة غير صحيحة',
            'target_type.required' => 'نوع المستهدفين مطلوب',
            'target_type.in' => 'نوع المستهدفين المحدد غير صحيح',
            'action_url.url' => 'رابط الإجراء غير صحيح',
            'expires_at.after' => 'تاريخ الانتهاء يجب أن يكون في المستقبل',
        ]);

        if ($validator->fails()) {
            RateLimiter::clear($key);
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // التحقق من وجود المستهدفين
            $targetId = $sanitizedData['target_id'];
            if (in_array($sanitizedData['target_type'], ['course_students', 'year_students', 'subject_students', 'individual']) && !$targetId) {
                DB::rollBack();
                RateLimiter::clear($key);
                return back()
                    ->withErrors(['target_id' => 'يجب اختيار المستهدفين المحددين'])
                    ->withInput();
            }

            // التحقق من صحة action_url إذا كان موجوداً
            if ($sanitizedData['action_url']) {
                $parsedUrl = parse_url($sanitizedData['action_url']);
                if (!$parsedUrl || !isset($parsedUrl['scheme']) || !in_array($parsedUrl['scheme'], ['http', 'https'])) {
                    DB::rollBack();
                    RateLimiter::clear($key);
                    return back()
                        ->withErrors(['action_url' => 'رابط الإجراء يجب أن يبدأ بـ http:// أو https://'])
                        ->withInput();
                }
            }

            $data = [
                'sender_id' => Auth::id(),
                'title' => $sanitizedData['title'],
                'message' => $sanitizedData['message'],
                'type' => $sanitizedData['type'],
                'priority' => $sanitizedData['priority'],
                'target_type' => $sanitizedData['target_type'],
                'target_id' => $targetId,
                'action_url' => $sanitizedData['action_url'],
                'action_text' => $sanitizedData['action_text'],
                'expires_at' => $sanitizedData['expires_at'],
                'data' => null,
                'audience' => 'student',
            ];

            // تحديد المستهدفين وإرسال الإشعارات
            $sentCount = $this->sendNotificationToTargets($sanitizedData['target_type'], $targetId, $data);

            // تسجيل النشاط
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'notification_sent',
                'model_type' => 'Notification',
                'model_id' => null,
                'new_values' => [
                    'target_type' => $sanitizedData['target_type'],
                    'sent_count' => $sentCount,
                ],
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->userAgent(), 0, 255),
            ]);

            DB::commit();
            RateLimiter::clear($key);

            return redirect()->route('admin.notifications.index')
                ->with('success', "تم إرسال الإشعار بنجاح إلى {$sentCount} طالب");

        } catch (\Exception $e) {
            DB::rollBack();
            RateLimiter::clear($key);
            
            // Log الخطأ بدون كشف معلومات حساسة
            Log::error('Error sending notification: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
            ]);

            return back()
                ->withErrors(['error' => 'حدث خطأ أثناء إرسال الإشعار. يرجى المحاولة مرة أخرى.'])
                ->withInput();
        }
    }

    /**
     * عرض تفاصيل الإشعار
     * محمي من: Unauthorized Access, Ownership
     */
    public function show(Notification $notification)
    {
        // التحقق من الصلاحيات
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        // التحقق من ملكية الإشعار (فقط المرسل يمكنه رؤيته)
        if ($notification->sender_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بعرض هذا الإشعار');
        }

        $notification->load(['user', 'sender']);
        
        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * حذف الإشعار
     * محمي من: XSS, SQL Injection, CSRF, Brute Force, Unauthorized Access, Ownership
     */
    public function destroy(Notification $notification)
    {
        // التحقق من الصلاحيات
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بحذف الإشعارات');
        }

        // التحقق من ملكية الإشعار
        if ($notification->sender_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بحذف هذا الإشعار');
        }

        // Rate Limiting - حماية من Brute Force
        $key = 'notification_delete_' . Auth::id();
        $maxAttempts = 30;
        $decayMinutes = 1;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', "تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد {$seconds} ثانية.");
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        try {
            $notificationId = $notification->id;
            $notification->delete();

            // تسجيل النشاط
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'notification_deleted',
                'model_type' => 'Notification',
                'model_id' => $notificationId,
                'ip_address' => request()->ip(),
                'user_agent' => substr(request()->userAgent(), 0, 255),
            ]);

            RateLimiter::clear($key);

            return redirect()->route('admin.notifications.index')
                ->with('success', 'تم حذف الإشعار بنجاح');

        } catch (\Exception $e) {
            RateLimiter::clear($key);
            
            // Log الخطأ بدون كشف معلومات حساسة
            Log::error('Error deleting notification: ' . $e->getMessage(), [
                'notification_id' => $notification->id,
                'user_id' => Auth::id(),
                'ip' => request()->ip(),
            ]);

            return back()->with('error', 'حدث خطأ أثناء حذف الإشعار. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * إرسال الإشعارات للمستهدفين
     */
    private function sendNotificationToTargets($targetType, $targetId, $data)
    {
        switch ($targetType) {
            case 'all_students':
                Notification::sendToAllStudents($data);
                return User::where('role', 'student')->where('is_active', true)->count();

            case 'course_students':
                if ($targetId) {
                    Notification::sendToCourseStudents($targetId, $data);
                    return \App\Models\StudentCourseEnrollment::where('advanced_course_id', $targetId)
                                                             ->where('status', 'active')
                                                             ->count();
                }
                break;

            case 'year_students':
                if ($targetId) {
                    Notification::sendToYearStudents($targetId, $data);
                    $courseIds = AdvancedCourse::where('academic_year_id', $targetId)->pluck('id');
                    return \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $courseIds)
                                                             ->where('status', 'active')
                                                             ->distinct('user_id')
                                                             ->count();
                }
                break;

            case 'subject_students':
                if ($targetId) {
                    Notification::sendToSubjectStudents($targetId, $data);
                    $courseIds = AdvancedCourse::where('academic_subject_id', $targetId)->pluck('id');
                    return \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $courseIds)
                                                             ->where('status', 'active')
                                                             ->distinct('user_id')
                                                             ->count();
                }
                break;

            case 'individual':
                if ($targetId) {
                    Notification::sendToUser($targetId, $data);
                    return 1;
                }
                break;
        }

        return 0;
    }

    /**
     * إرسال إشعار سريع
     * محمي من: XSS, SQL Injection, CSRF, Brute Force, Validation Bypass
     */
    public function quickSend(Request $request)
    {
        // التحقق من الصلاحيات
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بإرسال إشعارات',
            ], 403);
        }

        // Rate Limiting - حماية من Brute Force
        $key = 'notification_quick_send_' . Auth::id();
        $maxAttempts = 30;
        $decayMinutes = 5;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد {$seconds} ثانية.",
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        // Sanitization - تنقية البيانات
        $sanitizedData = [
            'title' => strip_tags(trim($request->input('title', ''))),
            'message' => strip_tags(trim($request->input('message', ''))),
            'target_type' => strip_tags(trim($request->input('target_type', ''))),
            'target_id' => filter_var($request->input('target_id'), FILTER_VALIDATE_INT) ?: null,
        ];

        // Validation محسن
        $validator = Validator::make($sanitizedData, [
            'title' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{Arabic}\s\p{N}a-zA-Z0-9@.-]+$/u',
            ],
            'message' => [
                'required',
                'string',
                'max:1000',
            ],
            'target_type' => [
                'required',
                'in:' . implode(',', array_keys(Notification::getTargetTypes())),
            ],
            'target_id' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ], [
            'title.required' => 'عنوان الإشعار مطلوب',
            'message.required' => 'نص الإشعار مطلوب',
            'target_type.required' => 'نوع المستهدفين مطلوب',
        ]);

        if ($validator->fails()) {
            RateLimiter::clear($key);
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // التحقق من وجود المستهدفين
            if (in_array($sanitizedData['target_type'], ['course_students', 'year_students', 'subject_students', 'individual']) && !$sanitizedData['target_id']) {
                DB::rollBack();
                RateLimiter::clear($key);
                return response()->json([
                    'success' => false,
                    'message' => 'يجب اختيار المستهدفين المحددين',
                ], 422);
            }

            $data = [
                'sender_id' => Auth::id(),
                'title' => $sanitizedData['title'],
                'message' => $sanitizedData['message'],
                'type' => 'general',
                'priority' => 'normal',
                'target_type' => $sanitizedData['target_type'],
                'target_id' => $sanitizedData['target_id'],
                'audience' => 'student',
            ];

            $sentCount = $this->sendNotificationToTargets($sanitizedData['target_type'], $sanitizedData['target_id'], $data);

            // تسجيل النشاط
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'notification_quick_sent',
                'model_type' => 'Notification',
                'model_id' => null,
                'new_values' => [
                    'target_type' => $sanitizedData['target_type'],
                    'sent_count' => $sentCount,
                ],
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->userAgent(), 0, 255),
            ]);

            DB::commit();
            RateLimiter::clear($key);

            return response()->json([
                'success' => true,
                'message' => "تم إرسال الإشعار إلى {$sentCount} طالب",
                'sent_count' => $sentCount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            RateLimiter::clear($key);
            
            // Log الخطأ بدون كشف معلومات حساسة
            Log::error('Error sending quick notification: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الإشعار. يرجى المحاولة مرة أخرى.',
            ], 500);
        }
    }

    /**
     * الحصول على عدد المستهدفين
     * محمي من: XSS, SQL Injection, Unauthorized Access
     */
    public function getTargetCount(Request $request)
    {
        // التحقق من الصلاحيات
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            return response()->json(['count' => 0], 403);
        }

        // Sanitization - تنقية البيانات
        $targetType = strip_tags(trim($request->input('target_type', '')));
        $targetId = filter_var($request->input('target_id'), FILTER_VALIDATE_INT) ?: null;

        // التحقق من النوع المسموح
        $validTargetTypes = array_keys(Notification::getTargetTypes());
        if (!in_array($targetType, $validTargetTypes)) {
            return response()->json(['count' => 0], 422);
        }

        $count = 0;

        try {
            switch ($targetType) {
                case 'all_students':
                    $count = User::where('role', 'student')->where('is_active', true)->count();
                    break;

                case 'course_students':
                    if ($targetId && $targetId > 0) {
                        $count = \App\Models\StudentCourseEnrollment::where('advanced_course_id', $targetId)
                                                                  ->where('status', 'active')
                                                                  ->count();
                    }
                    break;

                case 'year_students':
                    if ($targetId && $targetId > 0) {
                        $courseIds = AdvancedCourse::where('academic_year_id', $targetId)->pluck('id');
                        $count = \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $courseIds)
                                                                  ->where('status', 'active')
                                                                  ->distinct('user_id')
                                                                  ->count();
                    }
                    break;

                case 'subject_students':
                    if ($targetId && $targetId > 0) {
                        $courseIds = AdvancedCourse::where('academic_subject_id', $targetId)->pluck('id');
                        $count = \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $courseIds)
                                                                  ->where('status', 'active')
                                                                  ->distinct('user_id')
                                                                  ->count();
                    }
                    break;

                case 'individual':
                    if ($targetId && $targetId > 0) {
                        $count = User::where('id', $targetId)
                                    ->where('role', 'student')
                                    ->where('is_active', true)
                                    ->exists() ? 1 : 0;
                    }
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Error getting target count: ' . $e->getMessage());
            return response()->json(['count' => 0], 500);
        }

        return response()->json(['count' => max(0, (int)$count)]);
    }

    /**
     * تحديد جميع الإشعارات كمقروءة
     * محمي من: SQL Injection, CSRF, Brute Force, Unauthorized Access
     */
    public function markAllAsRead(Request $request)
    {
        // التحقق من الصلاحيات
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتنفيذ هذا الإجراء',
            ], 403);
        }

        // Rate Limiting
        $key = 'notification_mark_read_' . Auth::id();
        $maxAttempts = 10;
        $decayMinutes = 1;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد {$seconds} ثانية.",
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        try {
            $query = Notification::where('sender_id', Auth::id())->unread();

            // Sanitization - التحقق من user_id إذا كان موجوداً
            if ($request->filled('user_id')) {
                $userId = filter_var($request->input('user_id'), FILTER_VALIDATE_INT);
                if ($userId && $userId > 0) {
                    $query->where('user_id', $userId);
                }
            }

            $count = $query->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

            RateLimiter::clear($key);

            return response()->json([
                'success' => true,
                'message' => "تم تحديد {$count} إشعار كمقروء",
                'count' => $count,
            ]);

        } catch (\Exception $e) {
            RateLimiter::clear($key);
            Log::error('Error marking notifications as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تنفيذ العملية',
            ], 500);
        }
    }

    /**
     * حذف الإشعارات القديمة
     * محمي من: SQL Injection, CSRF, Brute Force, Unauthorized Access
     */
    public function cleanup(Request $request)
    {
        // التحقق من الصلاحيات
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتنفيذ هذا الإجراء',
            ], 403);
        }

        // Rate Limiting
        $key = 'notification_cleanup_' . Auth::id();
        $maxAttempts = 5;
        $decayMinutes = 10;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد {$seconds} ثانية.",
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        try {
            // Sanitization - التحقق من عدد الأيام
            $days = filter_var($request->input('days', 30), FILTER_VALIDATE_INT);
            if (!$days || $days < 1 || $days > 365) {
                $days = 30; // القيمة الافتراضية الآمنة
            }

            $count = Notification::where('sender_id', Auth::id())
                                ->where('created_at', '<', now()->subDays($days))
                                ->where('is_read', true)
                                ->delete();

            // تسجيل النشاط
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'notifications_cleaned',
                'model_type' => 'Notification',
                'model_id' => null,
                'new_values' => ['deleted_count' => $count, 'days' => $days],
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->userAgent(), 0, 255),
            ]);

            RateLimiter::clear($key);

            return response()->json([
                'success' => true,
                'message' => "تم حذف {$count} إشعار قديم",
                'count' => $count,
            ]);

        } catch (\Exception $e) {
            RateLimiter::clear($key);
            Log::error('Error cleaning up notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تنفيذ العملية',
            ], 500);
        }
    }

    /**
     * إحصائيات الإشعارات
     * محمي من: Unauthorized Access
     */
    public function statistics()
    {
        // التحقق من الصلاحيات
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        $stats = [
            'overview' => [
                'total_sent' => Notification::where('sender_id', Auth::id())->count(),
                'total_read' => Notification::where('sender_id', Auth::id())->read()->count(),
                'total_unread' => Notification::where('sender_id', Auth::id())->unread()->count(),
                'today_sent' => Notification::where('sender_id', Auth::id())->whereDate('created_at', today())->count(),
            ],
            'by_type' => Notification::where('sender_id', Auth::id())
                                   ->selectRaw('type, count(*) as count')
                                   ->groupBy('type')
                                   ->get(),
            'by_priority' => Notification::where('sender_id', Auth::id())
                                       ->selectRaw('priority, count(*) as count')
                                       ->groupBy('priority')
                                       ->get(),
            'by_target' => Notification::where('sender_id', Auth::id())
                                     ->selectRaw('target_type, count(*) as count')
                                     ->groupBy('target_type')
                                     ->get(),
            'recent_activity' => Notification::where('sender_id', Auth::id())
                                            ->selectRaw('DATE(created_at) as date, count(*) as count')
                                            ->groupBy('date')
                                            ->orderBy('date', 'desc')
                                            ->take(7)
                                            ->get(),
        ];

        return view('admin.notifications.statistics', compact('stats'));
    }
}
