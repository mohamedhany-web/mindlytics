<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfflineEnrollmentsController extends Controller
{
    /**
     * عرض قائمة جميع تسجيلات الأوفلاين
     */
    public function index(Request $request)
    {
        $query = OfflineCourseEnrollment::with(['student', 'course.instructor', 'course.locationModel', 'group']);

        // البحث بالاسم أو رقم الهاتف
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%");
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب الكورس
        if ($request->filled('course_id')) {
            $query->where('offline_course_id', $request->course_id);
        }

        $enrollments = $query->latest('enrolled_at')->paginate(20);

        // البيانات المساعدة للفلاتر
        $courses = OfflineCourse::where('status', 'active')
            ->with(['instructor', 'locationModel'])
            ->orderBy('title')
            ->get();

        // الإحصائيات
        $stats = [
            'total' => OfflineCourseEnrollment::count(),
            'active' => OfflineCourseEnrollment::where('status', 'active')->count(),
            'pending' => OfflineCourseEnrollment::where('status', 'pending')->count(),
            'completed' => OfflineCourseEnrollment::where('status', 'completed')->count(),
        ];

        return view('admin.offline-enrollments.index', compact('enrollments', 'courses', 'stats'));
    }

    /**
     * عرض صفحة إضافة تسجيل جديد
     */
    public function create()
    {
        $students = User::where('role', 'student')
            ->where('is_active', true)
            ->orderBy('name')
            ->select('id', 'name', 'phone')
            ->paginate(50);

        $courses = OfflineCourse::where('status', 'active')
            ->with(['instructor', 'locationModel'])
            ->orderBy('title')
            ->get();

        return view('admin.offline-enrollments.create', compact('students', 'courses'));
    }

    /**
     * حفظ تسجيل جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'offline_course_id' => 'required|exists:offline_courses,id',
            'group_id' => 'nullable|exists:offline_course_groups,id',
            'status' => 'required|in:pending,active',
        ]);

        // التحقق من عدم وجود تسجيل مسبق
        $existing = OfflineCourseEnrollment::where('user_id', $validated['user_id'])
            ->where('offline_course_id', $validated['offline_course_id'])
            ->exists();

        if ($existing) {
            return back()->withErrors(['error' => 'الطالب مسجل بالفعل في هذا الكورس']);
        }

        // التحقق من إمكانية التسجيل
        $course = OfflineCourse::findOrFail($validated['offline_course_id']);
        $currentEnrollments = $course->enrollments()->where('status', 'active')->count();
        
        if ($course->max_students > 0 && $currentEnrollments >= $course->max_students) {
            return back()->withErrors(['error' => 'تم الوصول إلى الحد الأقصى لعدد الطلاب في هذا الكورس']);
        }

        OfflineCourseEnrollment::create([
            'user_id' => $validated['user_id'],
            'offline_course_id' => $validated['offline_course_id'],
            'group_id' => $validated['group_id'] ?? null,
            'status' => $validated['status'],
            'enrolled_at' => now(),
        ]);

        return redirect()->route('admin.offline-enrollments.index')
                        ->with('success', 'تم تسجيل الطالب في الكورس الأوفلاين بنجاح');
    }

    /**
     * عرض تفاصيل التسجيل
     */
    public function show(OfflineCourseEnrollment $offlineEnrollment)
    {
        $offlineEnrollment->load(['student', 'course.instructor', 'course.locationModel', 'group']);
        
        return view('admin.offline-enrollments.show', compact('offlineEnrollment'));
    }

    /**
     * تحديث حالة التسجيل
     */
    public function updateStatus(Request $request, OfflineCourseEnrollment $offlineEnrollment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,active,completed,suspended',
        ]);

        $offlineEnrollment->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'تم تحديث حالة التسجيل بنجاح');
    }

    /**
     * حذف التسجيل
     */
    public function destroy(OfflineCourseEnrollment $offlineEnrollment)
    {
        $studentName = $offlineEnrollment->student->name;
        $courseName = $offlineEnrollment->course->title;
        
        $offlineEnrollment->delete();

        return redirect()->route('admin.offline-enrollments.index')
                        ->with('success', "تم حذف تسجيل {$studentName} من كورس {$courseName}");
    }
}
