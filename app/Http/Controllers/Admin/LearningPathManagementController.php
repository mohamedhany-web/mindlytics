<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\User;
use App\Models\LearningPathEnrollment;
use App\Models\StudentCourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LearningPathManagementController extends Controller
{
    /**
     * عرض صفحة إدارة الكورسات في المسارات التعليمية
     */
    public function coursesIndex(Request $request)
    {
        $query = AcademicYear::withCount('linkedCourses')
            ->orderBy('order')
            ->orderBy('name');

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $learningPaths = $query->paginate(20);

        // جلب جميع الكورسات النشطة
        $allCourses = AdvancedCourse::where('is_active', true)
            ->with(['instructor', 'academicSubject', 'academicYear'])
            ->orderBy('title')
            ->get();

        return view('admin.learning-paths.courses.index', compact('learningPaths', 'allCourses'));
    }

    /**
     * عرض صفحة إدارة الكورسات لمسار معين
     */
    public function coursesManage(AcademicYear $academicYear)
    {
        $academicYear->load([
            'linkedCourses' => function($query) {
                $query->where('is_active', true)
                      ->with(['instructor', 'academicSubject', 'academicYear'])
                      ->withCount('lessons')
                      ->orderBy('academic_year_courses.order');
            }
        ]);

        // جلب جميع الكورسات النشطة المتاحة
        $availableCourses = AdvancedCourse::where('is_active', true)
            ->with(['instructor', 'academicSubject', 'academicYear'])
            ->whereDoesntHave('packages', function($query) use ($academicYear) {
                // يمكن إضافة فلترة إضافية هنا
            })
            ->orderBy('title')
            ->get();

        return view('admin.learning-paths.courses.manage', compact('academicYear', 'availableCourses'));
    }

    /**
     * إضافة كورس للمسار
     */
    public function coursesStore(Request $request, AcademicYear $academicYear)
    {
        $request->validate([
            'course_id' => 'required|exists:advanced_courses,id',
            'order' => 'nullable|integer|min:0',
            'is_required' => 'nullable|boolean',
        ]);

        // التحقق من عدم وجود الكورس بالفعل
        if ($academicYear->linkedCourses()->where('advanced_course_id', $request->course_id)->exists()) {
            return back()->withErrors(['error' => 'هذا الكورس مرتبط بالفعل بالمسار']);
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $academicYear->linkedCourses()->attach($request->course_id, [
                'order' => $request->order ?? 0,
                'is_required' => $request->has('is_required'),
            ]);

            // تفعيل الكورس الجديد لجميع الطلاب المسجلين في المسار
            $this->activateCourseForPathStudents($academicYear, $request->course_id);

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'تم إضافة الكورس للمسار بنجاح وتم تفعيله للطلاب المسجلين');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ أثناء إضافة الكورس: ' . $e->getMessage()]);
        }
    }

    /**
     * تفعيل كورس لجميع الطلاب المسجلين في المسار
     */
    private function activateCourseForPathStudents(AcademicYear $academicYear, $courseId)
    {
        // جلب جميع الطلاب المسجلين في المسار بحالة نشطة
        $activeEnrollments = LearningPathEnrollment::where('academic_year_id', $academicYear->id)
            ->where('status', 'active')
            ->get();

        foreach ($activeEnrollments as $enrollment) {
            // تسجيل الطالب في الكورس الجديد
            StudentCourseEnrollment::firstOrCreate(
                [
                    'user_id' => $enrollment->user_id,
                    'advanced_course_id' => $courseId,
                ],
                [
                    'status' => 'active',
                    'enrolled_at' => now(),
                    'activated_at' => now(),
                    'activated_by' => Auth::id() ?? $enrollment->activated_by,
                    'progress' => 0,
                ]
            );
        }
    }

    /**
     * إزالة كورس من المسار
     */
    public function coursesDestroy(AcademicYear $academicYear, AdvancedCourse $course)
    {
        $academicYear->linkedCourses()->detach($course->id);
        return back()->with('success', 'تم إزالة الكورس من المسار بنجاح');
    }

    /**
     * تحديث ترتيب الكورسات
     */
    public function coursesUpdateOrder(Request $request, AcademicYear $academicYear)
    {
        $request->validate([
            'courses' => 'required|array',
            'courses.*.id' => 'required|exists:advanced_courses,id',
            'courses.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->courses as $courseData) {
            $academicYear->linkedCourses()->updateExistingPivot($courseData['id'], [
                'order' => $courseData['order']
            ]);
        }

        return response()->json(['success' => true, 'message' => 'تم تحديث الترتيب بنجاح']);
    }

    /**
     * عرض صفحة إدارة المدربين في المسارات التعليمية
     */
    public function instructorsIndex(Request $request)
    {
        $query = AcademicYear::withCount('instructors')
            ->orderBy('order')
            ->orderBy('name');

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $learningPaths = $query->paginate(20);

        // جلب جميع المدربين النشطين
        $allInstructors = User::where('role', 'instructor')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.learning-paths.instructors.index', compact('learningPaths', 'allInstructors'));
    }

    /**
     * عرض صفحة إدارة المدربين لمسار معين
     */
    public function instructorsManage(AcademicYear $academicYear)
    {
        $academicYear->load([
            'instructors' => function($query) {
                $query->orderBy('name');
            },
            'linkedCourses' => function($query) {
                $query->where('is_active', true)
                      ->orderBy('academic_year_courses.order');
            }
        ]);

        // جلب جميع المدربين النشطين المتاحين
        $existingInstructorIds = $academicYear->instructors->pluck('id')->toArray();
        $availableInstructors = User::where('role', 'instructor')
            ->where('is_active', true)
            ->whereNotIn('id', $existingInstructorIds)
            ->orderBy('name')
            ->get();

        return view('admin.learning-paths.instructors.manage', compact('academicYear', 'availableInstructors'));
    }

    /**
     * إضافة مدرب للمسار
     */
    public function instructorsStore(Request $request, AcademicYear $academicYear)
    {
        $request->validate([
            'instructor_id' => 'required|exists:users,id',
            'assigned_courses' => 'nullable|array',
            'assigned_courses.*' => 'exists:advanced_courses,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        // التحقق من أن المستخدم مدرب
        $instructor = User::findOrFail($request->instructor_id);
        if ($instructor->role !== 'instructor') {
            return back()->withErrors(['error' => 'المستخدم المحدد ليس مدرب']);
        }

        // التحقق من عدم وجود المدرب بالفعل
        if ($academicYear->instructors()->where('instructor_id', $request->instructor_id)->exists()) {
            return back()->withErrors(['error' => 'هذا المدرب مرتبط بالفعل بالمسار']);
        }

        $academicYear->instructors()->attach($request->instructor_id, [
            'assigned_courses' => json_encode($request->assigned_courses ?? []),
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'تم إضافة المدرب للمسار بنجاح');
    }

    /**
     * إزالة مدرب من المسار
     */
    public function instructorsDestroy(AcademicYear $academicYear, User $instructor)
    {
        $academicYear->instructors()->detach($instructor->id);
        return back()->with('success', 'تم إزالة المدرب من المسار بنجاح');
    }

    /**
     * تحديث الكورسات المخصصة للمدرب
     */
    public function instructorsUpdateCourses(Request $request, AcademicYear $academicYear, User $instructor)
    {
        $request->validate([
            'assigned_courses' => 'nullable|array',
            'assigned_courses.*' => 'exists:advanced_courses,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $academicYear->instructors()->updateExistingPivot($instructor->id, [
            'assigned_courses' => json_encode($request->assigned_courses ?? []),
            'notes' => $request->notes ?? $academicYear->instructors()->where('instructor_id', $instructor->id)->first()->pivot->notes,
        ]);

        return back()->with('success', 'تم تحديث الكورسات المخصصة للمدرب بنجاح');
    }
}
