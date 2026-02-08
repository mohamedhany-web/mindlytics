<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $instructor = Auth::user();
        
        // جلب الكورسات التي تم تعيينها لهذا المدرس
        $query = AdvancedCourse::where('instructor_id', $instructor->id)
            ->with(['academicYear', 'academicSubject'])
            ->withCount(['lectures', 'enrollments']);

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(15);

        // إحصائيات
        $stats = [
            'total' => AdvancedCourse::where('instructor_id', $instructor->id)->count(),
            'active' => AdvancedCourse::where('instructor_id', $instructor->id)->where('is_active', true)->count(),
            'inactive' => AdvancedCourse::where('instructor_id', $instructor->id)->where('is_active', false)->count(),
            'total_students' => \App\Models\StudentCourseEnrollment::whereHas('course', function($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id);
            })->where('status', 'active')->count(),
        ];

        return view('instructor.courses.index', compact('courses', 'stats'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $instructor = Auth::user();
        
        $course = AdvancedCourse::where('instructor_id', $instructor->id)
            ->with(['academicYear', 'academicSubject', 'instructor'])
            ->withCount(['lectures', 'enrollments'])
            ->findOrFail($id);

        // المحاضرات
        $lectures = \App\Models\Lecture::where('course_id', $course->id)
            ->with(['instructor'])
            ->orderBy('scheduled_at', 'desc')
            ->paginate(10, ['*'], 'lectures_page');

        // الاختبارات
        $exams = \App\Models\AdvancedExam::where('advanced_course_id', $course->id)
            ->with(['lesson', 'advancedCourse'])
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'exams_page');

        // الواجبات
        $assignments = \App\Models\Assignment::where(function($q) use ($course) {
                $q->where('advanced_course_id', $course->id)
                  ->orWhere('course_id', $course->id);
            })
            ->with(['lesson', 'teacher'])
            ->withCount('submissions')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'assignments_page');

        // الطلاب المسجلين
        $enrollments = \App\Models\StudentCourseEnrollment::where('advanced_course_id', $course->id)
            ->with('user')
            ->where('status', 'active')
            ->latest()
            ->paginate(20, ['*'], 'students_page');

        // المجموعات
        $groups = \App\Models\Group::whereHas('course', function($q) use ($course) {
                $q->where('id', $course->id);
            })
            ->with(['course', 'members'])
            ->withCount('members')
            ->latest()
            ->get();

        // إحصائيات شاملة (محاضرات فقط — تم إلغاء الدروس)
        $stats = [
            'total_lectures' => \App\Models\Lecture::where('course_id', $course->id)->count(),
            'upcoming_lectures' => \App\Models\Lecture::where('course_id', $course->id)
                ->where('status', 'scheduled')
                ->where('scheduled_at', '>=', now())
                ->count(),
            'total_exams' => \App\Models\AdvancedExam::where('advanced_course_id', $course->id)->count(),
            'active_exams' => \App\Models\AdvancedExam::where('advanced_course_id', $course->id)
                ->where('is_active', true)
                ->count(),
            'total_assignments' => \App\Models\Assignment::where(function($q) use ($course) {
                    $q->where('advanced_course_id', $course->id)
                      ->orWhere('course_id', $course->id);
                })->count(),
            'pending_submissions' => \App\Models\AssignmentSubmission::whereHas('assignment', function($q) use ($course) {
                    $q->where(function($q2) use ($course) {
                        $q2->where('advanced_course_id', $course->id)
                           ->orWhere('course_id', $course->id);
                    });
                })
                ->whereNull('graded_at')
                ->count(),
            'total_students' => $enrollments->total(),
            'total_groups' => $groups->count(),
            'total_attendance_records' => \App\Models\AttendanceRecord::whereHas('lecture', function($q) use ($course) {
                    $q->where('course_id', $course->id);
                })->count(),
        ];

        return view('instructor.courses.show', compact(
            'course', 
            'enrollments', 
            'lectures', 
            'exams', 
            'assignments', 
            'groups', 
            'stats'
        ));
    }
}
