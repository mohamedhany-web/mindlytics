<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Notification;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * إشعارات تطبيق الطلاب — إنشاء سجلات في جدول [notifications] كما يقرأها الـ API والويب.
 */
class MobileAppNotificationsController extends Controller
{
    public function index(): View
    {
        $types = Notification::getTypes();
        $priorities = Notification::getPriorities();

        $courses = AdvancedCourse::query()
            ->orderByDesc('id')
            ->limit(500)
            ->get(['id', 'title']);

        $students = User::query()
            ->where('role', 'student')
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(800)
            ->get(['id', 'name', 'email']);

        $recentRaw = Notification::query()
            ->with('sender')
            ->where(function ($q) {
                $q->whereNull('audience')->orWhere('audience', 'student');
            })
            ->latest()
            ->limit(500)
            ->get();

        $recentBatches = $recentRaw
            ->groupBy(function (Notification $n) {
                return $n->title.'|'.$n->message.'|'.$n->created_at->format('Y-m-d H:i');
            })
            ->map(function ($group) {
                /** @var \Illuminate\Support\Collection<int, Notification> $group */
                $first = $group->first();

                return [
                    'title' => $first->title,
                    'message' => $first->message,
                    'sent_at' => $first->created_at,
                    'recipients' => $group->count(),
                    'type' => $first->type,
                    'priority' => $first->priority,
                    'sender' => $first->sender,
                    'action_url' => $first->action_url,
                ];
            })
            ->values()
            ->take(60);

        return view('admin.mobile-app.notifications', compact(
            'types',
            'priorities',
            'courses',
            'students',
            'recentBatches'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $typeKeys = implode(',', array_keys(Notification::getTypes()));
        $priorityKeys = implode(',', array_keys(Notification::getPriorities()));

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:8000'],
            'type' => ['required', 'string', 'in:'.$typeKeys],
            'priority' => ['required', 'string', 'in:'.$priorityKeys],
            'scope' => ['required', 'string', 'in:all_students,course,user'],
            'advanced_course_id' => ['nullable', 'integer', 'exists:advanced_courses,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'action_url' => ['nullable', 'string', 'max:2048'],
            'action_text' => ['nullable', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date'],
        ]);

        if ($validated['scope'] === 'course' && empty($validated['advanced_course_id'])) {
            return back()->withErrors(['advanced_course_id' => 'اختر الكورس'])->withInput();
        }
        if ($validated['scope'] === 'user' && empty($validated['user_id'])) {
            return back()->withErrors(['user_id' => 'اختر الطالب'])->withInput();
        }

        $base = [
            'title' => $validated['title'],
            'message' => $validated['message'],
            'type' => $validated['type'],
            'priority' => $validated['priority'],
            'sender_id' => $request->user()->id,
            'audience' => 'student',
            'action_url' => $validated['action_url'] ?: null,
            'action_text' => $validated['action_text'] ?: null,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_read' => false,
        ];

        try {
            if ($validated['scope'] === 'all_students') {
                $count = User::where('role', 'student')->where('is_active', true)->count();
                if ($count === 0) {
                    return back()->withErrors(['store' => 'لا يوجد طلاب نشطون في المنصة.'])->withInput();
                }
                $base['target_type'] = 'all_students';
                $base['target_id'] = null;
                Notification::sendToAllStudents($base);

                return redirect()->route('admin.mobile-app.notifications')->with('success', 'تم إنشاء الإشعار لجميع الطلاب النشطين ('.$count.' مستلم).');
            }

            if ($validated['scope'] === 'course') {
                $courseId = (int) $validated['advanced_course_id'];
                $count = StudentCourseEnrollment::where('advanced_course_id', $courseId)
                    ->where('status', 'active')
                    ->count();
                if ($count === 0) {
                    return back()->withErrors(['advanced_course_id' => 'لا يوجد طلاب مسجّلون نشطون في هذا الكورس.'])->withInput();
                }
                $base['target_type'] = 'course_students';
                $base['target_id'] = $courseId;
                Notification::sendToCourseStudents($courseId, $base);

                return redirect()->route('admin.mobile-app.notifications')->with('success', 'تم إنشاء الإشعار لطلاب الكورس ('.$count.' مستلم).');
            }

            $uid = (int) $validated['user_id'];
            $student = User::find($uid);
            if (! $student || $student->role !== 'student' || ! $student->is_active) {
                return back()->withErrors(['user_id' => 'المستخدم ليس طالباً نشطاً.'])->withInput();
            }
            $base['target_type'] = 'individual';
            $base['target_id'] = $uid;
            Notification::sendToUser($uid, $base);

            return redirect()->route('admin.mobile-app.notifications')->with('success', 'تم إنشاء الإشعار للطالب المحدد.');
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['store' => 'تعذّر الإرسال: '.$e->getMessage()])->withInput();
        }
    }
}
