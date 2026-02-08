<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdvancedCourse;
use App\Models\StudentCourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class StudentEnrollmentController extends Controller
{
    /**
     * عرض صفحة إدارة تسجيل الطلاب (الأونلاين)
     */
    public function index(Request $request)
    {
        $query = StudentCourseEnrollment::with(['student', 'course.academicYear', 'course.academicSubject', 'activatedBy']);

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
            $query->where('advanced_course_id', $request->course_id);
        }

        $enrollments = $query->latest('enrolled_at')->paginate(20);

        // البيانات المساعدة للفلاتر - استخدام الكاش
        $courses = Cache::remember('active_courses_list', now()->addHours(1), function () {
            return AdvancedCourse::active()->with(['academicYear', 'academicSubject'])->get();
        });
        
        // استخدام خدمة الكاش للإحصائيات
        $statsService = app(\App\Services\StatisticsCacheService::class);
        $stats = $statsService->getEnrollmentStats();

        return view('admin.online-enrollments.index', compact('enrollments', 'courses', 'stats'));
    }

    /**
     * عرض صفحة إضافة تسجيل جديد
     */
    public function create()
    {
        // استخدام pagination للطلاب بدلاً من get() لتقليل استهلاك الذاكرة
        $students = User::where('role', 'student')
            ->where('is_active', true)
            ->orderBy('name')
            ->select('id', 'name', 'phone')
            ->paginate(50);
        
        // استخدام الكاش للكورسات
        $courses = Cache::remember('active_courses_list', now()->addHours(1), function () {
            return AdvancedCourse::active()
                ->with(['academicYear', 'academicSubject'])
                ->orderBy('title')
                ->get();
        });

        return view('admin.online-enrollments.create', compact('students', 'courses'));
    }

    /**
     * حفظ تسجيل جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'advanced_course_id' => 'required|exists:advanced_courses,id',
            'status' => 'required|in:pending,active',
            'notes' => 'nullable|string|max:1000',
        ], [
            'user_id.required' => 'الطالب مطلوب',
            'user_id.exists' => 'الطالب المحدد غير موجود',
            'advanced_course_id.required' => 'الكورس مطلوب',
            'advanced_course_id.exists' => 'الكورس المحدد غير موجود',
            'status.required' => 'حالة التسجيل مطلوبة',
            'status.in' => 'حالة التسجيل غير صحيحة',
        ]);

        // التحقق من عدم وجود تسجيل مسبق - استخدام exists() بدلاً من first() للأداء الأفضل
        $existingEnrollment = StudentCourseEnrollment::where('user_id', $request->user_id)
                                                  ->where('advanced_course_id', $request->advanced_course_id)
                                                  ->exists();

        if ($existingEnrollment) {
            return back()->withErrors(['error' => 'الطالب مسجل بالفعل في هذا الكورس']);
        }

        // مسح الكاش بعد إضافة تسجيل جديد
        app(\App\Services\StatisticsCacheService::class)->clearStats('enrollment_stats');

        $enrollmentData = [
            'user_id' => $request->user_id,
            'advanced_course_id' => $request->advanced_course_id,
            'status' => $request->status,
            'notes' => $request->notes,
            'enrolled_at' => now(),
        ];

        // إذا كان التسجيل نشط، إضافة بيانات التفعيل
        if ($request->status === 'active') {
            $enrollmentData['activated_at'] = now();
            $enrollmentData['activated_by'] = Auth::id();
        }

        StudentCourseEnrollment::create($enrollmentData);

        return redirect()->route('admin.online-enrollments.index')
                        ->with('success', 'تم تسجيل الطالب في الكورس بنجاح');
    }

    /**
     * عرض تفاصيل التسجيل
     */
    public function show(StudentCourseEnrollment $enrollment)
    {
        $enrollment->load(['student', 'course.academicYear', 'course.academicSubject', 'activatedBy']);
        
        return view('admin.online-enrollments.show', compact('enrollment'));
    }

    /**
     * تفعيل تسجيل الطالب أو إعادة تفعيله
     */
    public function activate(StudentCourseEnrollment $enrollment)
    {
        if ($enrollment->status === 'active') {
            return back()->withErrors(['error' => 'التسجيل مفعل بالفعل']);
        }

        $wasSuspended = $enrollment->status === 'suspended';
        
        $enrollment->update([
            'status' => 'active',
            'activated_at' => now(),
            'activated_by' => Auth::id(),
        ]);

        $message = $wasSuspended 
            ? 'تم إعادة تفعيل التسجيل وفتح الكورس للطالب بنجاح' 
            : 'تم تفعيل التسجيل بنجاح';

        return back()->with('success', $message);
    }

    /**
     * إلغاء تفعيل تسجيل الطالب
     */
    public function deactivate(StudentCourseEnrollment $enrollment)
    {
        if ($enrollment->status !== 'active') {
            return back()->withErrors(['error' => 'التسجيل غير مفعل']);
        }

        $enrollment->update([
            'status' => 'suspended',
        ]);

        return back()->with('success', 'تم إلغاء تفعيل التسجيل');
    }

    /**
     * تحديث تقدم الطالب
     */
    public function updateProgress(Request $request, StudentCourseEnrollment $enrollment)
    {
        $request->validate([
            'progress' => 'required|numeric|min:0|max:100',
        ], [
            'progress.required' => 'نسبة التقدم مطلوبة',
            'progress.numeric' => 'نسبة التقدم يجب أن تكون رقم',
            'progress.min' => 'نسبة التقدم لا يمكن أن تكون أقل من صفر',
            'progress.max' => 'نسبة التقدم لا يمكن أن تزيد عن 100',
        ]);

        $enrollment->update([
            'progress' => $request->progress,
        ]);

        // إذا وصل التقدم إلى 100%، تغيير الحالة إلى مكتمل
        if ($request->progress == 100) {
            $enrollment->update(['status' => 'completed']);
        }

        return back()->with('success', 'تم تحديث تقدم الطالب');
    }

    /**
     * تحديث ملاحظات التسجيل
     */
    public function updateNotes(Request $request, StudentCourseEnrollment $enrollment)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $enrollment->update([
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'تم تحديث الملاحظات');
    }

    /**
     * حذف التسجيل
     */
    public function destroy(StudentCourseEnrollment $enrollment)
    {
        $studentName = $enrollment->student->name;
        $courseName = $enrollment->course->title;
        
        $enrollment->delete();

        return redirect()->route('admin.online-enrollments.index')
                        ->with('success', "تم حذف تسجيل {$studentName} من كورس {$courseName}");
    }

    /**
     * البحث عن الطلاب بالهاتف (AJAX)
     */
    public function searchStudentByPhone(Request $request)
    {
        $phone = $request->get('phone');
        
        if (!$phone) {
            return response()->json(['error' => 'رقم الهاتف مطلوب'], 400);
        }

        $student = User::where('role', 'student')
                      ->where(function($query) use ($phone) {
                          $query->where('phone', $phone)
                                ->orWhere('parent_phone', $phone);
                      })
                      ->first();

        if (!$student) {
            return response()->json(['error' => 'لم يتم العثور على طالب بهذا الرقم'], 404);
        }

        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'phone' => $student->phone,
                'parent_phone' => $student->parent_phone,
            ]
        ]);
    }
}