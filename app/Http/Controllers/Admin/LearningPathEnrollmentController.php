<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\LearningPathEnrollment;
use App\Models\StudentCourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LearningPathEnrollmentController extends Controller
{
    /**
     * عرض صفحة إدارة تسجيلات المسارات التعليمية
     */
    public function index(Request $request)
    {
        $query = LearningPathEnrollment::with(['student', 'learningPath', 'activatedBy']);

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

        // فلترة حسب المسار
        if ($request->filled('learning_path_id')) {
            $query->where('academic_year_id', $request->learning_path_id);
        }

        $enrollments = $query->latest('enrolled_at')->paginate(20);

        // البيانات المساعدة للفلاتر
        $learningPaths = AcademicYear::active()
            ->orderBy('name')
            ->get();

        // الإحصائيات
        $stats = [
            'total' => LearningPathEnrollment::count(),
            'active' => LearningPathEnrollment::where('status', 'active')->count(),
            'pending' => LearningPathEnrollment::where('status', 'pending')->count(),
            'completed' => LearningPathEnrollment::where('status', 'completed')->count(),
        ];

        return view('admin.learning-path-enrollments.index', compact('enrollments', 'learningPaths', 'stats'));
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
        
        $learningPaths = AcademicYear::active()
            ->orderBy('name')
            ->get();

        return view('admin.learning-path-enrollments.create', compact('students', 'learningPaths'));
    }

    /**
     * حفظ تسجيل جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'status' => 'required|in:pending,active',
            'notes' => 'nullable|string|max:1000',
        ], [
            'user_id.required' => 'الطالب مطلوب',
            'user_id.exists' => 'الطالب المحدد غير موجود',
            'academic_year_id.required' => 'المسار التعليمي مطلوب',
            'academic_year_id.exists' => 'المسار التعليمي المحدد غير موجود',
            'status.required' => 'حالة التسجيل مطلوبة',
            'status.in' => 'حالة التسجيل غير صحيحة',
        ]);

        // التحقق من عدم وجود تسجيل مسبق
        $existingEnrollment = LearningPathEnrollment::where('user_id', $request->user_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->first();

        if ($existingEnrollment) {
            return back()->withErrors(['error' => 'الطالب مسجل بالفعل في هذا المسار التعليمي']);
        }

        DB::beginTransaction();
        try {
            $enrollment = LearningPathEnrollment::create([
                'user_id' => $request->user_id,
                'academic_year_id' => $request->academic_year_id,
                'status' => $request->status,
                'enrolled_at' => now(),
                'activated_at' => $request->status === 'active' ? now() : null,
                'activated_by' => $request->status === 'active' ? Auth::id() : null,
                'notes' => $request->notes,
                'progress' => 0,
            ]);

            // إذا كان التسجيل نشط، تسجيل الطالب في جميع الكورسات في المسار
            if ($request->status === 'active') {
                $this->enrollInPathCourses($enrollment);
            }

            DB::commit();

            return redirect()->route('admin.learning-path-enrollments.index')
                ->with('success', 'تم تسجيل الطالب في المسار التعليمي بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ أثناء التسجيل: ' . $e->getMessage()]);
        }
    }

    /**
     * تفعيل/إيقاف تسجيل
     */
    public function toggleStatus(LearningPathEnrollment $enrollment)
    {
        DB::beginTransaction();
        try {
            if ($enrollment->status === 'active') {
                $enrollment->update([
                    'status' => 'suspended',
                ]);
                $message = 'تم إيقاف التسجيل بنجاح';
            } else {
                $enrollment->update([
                    'status' => 'active',
                    'activated_at' => now(),
                    'activated_by' => Auth::id(),
                ]);
                
                // تسجيل الطالب في جميع الكورسات في المسار
                $this->enrollInPathCourses($enrollment);
                
                $message = 'تم تفعيل التسجيل بنجاح';
            }

            DB::commit();
            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }

    /**
     * حذف تسجيل
     */
    public function destroy(LearningPathEnrollment $enrollment)
    {
        DB::beginTransaction();
        try {
            $enrollment->delete();
            DB::commit();
            return back()->with('success', 'تم حذف التسجيل بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ أثناء الحذف']);
        }
    }

    /**
     * تسجيل الطالب في جميع الكورسات في المسار (المجانية والمدفوعة)
     */
    private function enrollInPathCourses(LearningPathEnrollment $enrollment)
    {
        // تحميل المسار مع العلاقات المطلوبة
        $learningPath = $enrollment->learningPath()->with(['linkedCourses', 'academicSubjects'])->first();
        
        if (!$learningPath) {
            return;
        }
        
        // جمع الكورسات من المسار
        $courses = collect();
        
        // الكورسات المرتبطة مباشرة
        // تحديد الجدول بشكل صريح لتجنب مشكلة ambiguous column
        $linkedCourses = $learningPath->linkedCourses()->where('advanced_courses.is_active', true)->get();
        $courses = $courses->merge($linkedCourses);
        
        // الكورسات من المواد الدراسية
        $subjectCourses = $learningPath->academicSubjects->flatMap(function($subject) {
            return $subject->advancedCourses()->where('is_active', true)->get();
        });
        
        $courses = $courses->merge($subjectCourses)->unique('id');

        // تسجيل الطالب في جميع الكورسات (المجانية والمدفوعة)
        foreach ($courses as $course) {
            StudentCourseEnrollment::firstOrCreate(
                [
                    'user_id' => $enrollment->user_id,
                    'advanced_course_id' => $course->id,
                ],
                [
                    'status' => 'active',
                    'enrolled_at' => now(),
                    'activated_at' => now(),
                    'activated_by' => Auth::id(),
                    'progress' => 0,
                ]
            );
        }
    }
}
