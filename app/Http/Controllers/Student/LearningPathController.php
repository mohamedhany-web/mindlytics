<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\LearningPathEnrollment;
use App\Models\StudentCourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LearningPathController extends Controller
{
    /**
     * عرض صفحة المسار التعليمي للطالب
     */
    public function show($slug)
    {
        $user = Auth::user();
        
        if (!$user->isStudent()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        // البحث عن المسار
        $academicYear = AcademicYear::active()
            ->with(['linkedCourses' => function($query) {
                $query->where('is_active', true)
                      ->with(['academicSubject', 'academicYear', 'instructor'])
                      ->withCount('lessons');
            }, 'academicSubjects'])
            ->get()
            ->first(function($year) use ($slug) {
                return Str::slug($year->name) === $slug;
            });
        
        if (!$academicYear) {
            abort(404, 'المسار التعليمي غير موجود');
        }

        // التحقق من أن الطالب مسجل في المسار
        $enrollment = LearningPathEnrollment::where('user_id', $user->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return redirect()->route('public.learning-path.show', ['slug' => $slug])
                        ->with('error', 'أنت غير مسجل في هذا المسار التعليمي');
        }

        // جلب الكورسات من المواد الدراسية
        $subjectIds = $academicYear->academicSubjects->pluck('id')->toArray();
        $courses = collect();
        if (!empty($subjectIds)) {
            $courses = \App\Models\AdvancedCourse::where('is_active', true)
                ->whereIn('academic_subject_id', $subjectIds)
                ->with(['academicSubject', 'academicYear', 'instructor'])
                ->withCount('lessons')
                ->get();
        }

        // دمج الكورسات المرتبطة مباشرة مع الكورسات من المواد الدراسية
        $linkedCourses = $academicYear->linkedCourses()->where('is_active', true)->get();
        $allCourses = $linkedCourses->merge($courses)->unique('id');

        // التحقق من التسجيل في كل كورس
        $enrolledCourseIds = StudentCourseEnrollment::where('user_id', $user->id)
            ->where('status', 'active')
            ->pluck('advanced_course_id')
            ->toArray();

        // إضافة معلومات التسجيل لكل كورس
        $allCourses = $allCourses->map(function($course) use ($enrolledCourseIds) {
            $course->is_enrolled = in_array($course->id, $enrolledCourseIds);
            return $course;
        });

        // حساب التقدم
        $enrolledCourses = $allCourses->filter(function($course) {
            return $course->is_enrolled ?? false;
        });
        
        $progress = $allCourses->count() > 0 
            ? round(($enrolledCourses->count() / $allCourses->count()) * 100, 1) 
            : 0;

        // تحديث تقدم المسار
        $enrollment->update(['progress' => $progress]);

        // إنشاء كائن المسار
        $learningPath = (object)[
            'id' => $academicYear->id,
            'name' => $academicYear->name,
            'description' => $academicYear->description,
            'video_url' => $academicYear->video_url,
            'slug' => Str::slug($academicYear->name),
            'icon' => $academicYear->icon,
            'color' => $academicYear->color,
            'code' => $academicYear->code,
            'courses' => $allCourses,
            'courses_count' => $allCourses->count(),
            'enrolled_courses_count' => $enrolledCourses->count(),
            'progress' => $progress,
        ];

        return view('student.learning-path.show', compact('learningPath', 'enrollment'));
    }
}
