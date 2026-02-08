<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LearningPathController extends Controller
{
    /**
     * عرض قائمة المسارات التعليمية التي يدرب فيها المدرب
     */
    public function index()
    {
        $instructor = Auth::user();
        
        if (!$instructor->isInstructor()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        // جلب المسارات التي يدرب فيها المدرب
        $learningPaths = AcademicYear::active()
            ->whereHas('instructors', function($query) use ($instructor) {
                $query->where('instructor_id', $instructor->id);
            })
            ->with(['academicSubjects', 'linkedCourses'])
            ->withCount(['academicSubjects', 'linkedCourses'])
            ->orderBy('name')
            ->get()
            ->map(function($year) {
                return (object)[
                    'id' => $year->id,
                    'name' => $year->name,
                    'description' => $year->description,
                    'slug' => Str::slug($year->name),
                    'icon' => $year->icon,
                    'color' => $year->color,
                    'code' => $year->code,
                    'academic_subjects_count' => $year->academic_subjects_count,
                    'linked_courses_count' => $year->linked_courses_count,
                ];
            });

        return view('instructor.learning-path.index', compact('learningPaths'));
    }

    /**
     * عرض صفحة إدارة المسار التعليمي للمدرب
     */
    public function show($slug)
    {
        $instructor = Auth::user();
        
        if (!$instructor->isInstructor()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        // البحث عن المسار
        $academicYear = AcademicYear::active()
            ->with(['linkedCourses' => function($query) {
                $query->where('is_active', true)
                      ->with(['academicSubject', 'academicYear', 'instructor'])
                      ->withCount('lessons')
                      ->orderBy('academic_year_courses.order');
            }, 'academicSubjects' => function($query) {
                $query->where('is_active', true)
                      ->with(['advancedCourses' => function($q) {
                          $q->where('is_active', true)
                            ->with(['instructor'])
                            ->withCount('lessons')
                            ->orderBy('title');
                      }]);
            }, 'instructors'])
            ->get()
            ->first(function($year) use ($slug) {
                return Str::slug($year->name) === $slug;
            });
        
        if (!$academicYear) {
            abort(404, 'المسار التعليمي غير موجود');
        }

        // التحقق من أن المدرب مسؤول عن هذا المسار
        $isInstructor = $academicYear->instructors->contains('id', $instructor->id);
        
        if (!$isInstructor) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا المسار');
        }

        // جمع جميع الكورسات
        $linkedCourses = $academicYear->linkedCourses ?? collect();
        $subjectCourses = $academicYear->academicSubjects->flatMap(function($subject) {
            return $subject->advancedCourses ?? collect();
        });
        $allCourses = $linkedCourses->merge($subjectCourses)->unique('id');

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
            'academic_subjects' => $academicYear->academicSubjects,
        ];

        return view('instructor.learning-path.show', compact('learningPath'));
    }
}
