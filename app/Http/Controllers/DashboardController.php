<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\User;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\Order;
use App\Models\AdvancedCourse;
use App\Models\ContactMessage;
use App\Models\LectureAssignment;
use App\Models\Exam;
use App\Models\Certificate;

class DashboardController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect('/login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
        
        // التحقق من أن المستخدم نشط
        if (!$user->is_active) {
            Auth::logout();
            return redirect('/login')->with('error', 'حسابك غير نشط. يرجى التواصل مع الإدارة.');
        }
        
        // التحقق من كون المستخدم موظف أولاً
        if ($user->isEmployee()) {
            return redirect()->route('employee.dashboard');
        }
        
        // التحقق من وجود دور للمستخدم
        if (!$user->role) {
            Auth::logout();
            return redirect('/login')->with('error', 'دور المستخدم غير محدد. يرجى التواصل مع الإدارة.');
        }
        
        // دعم الأدوار القديمة والجديدة للتوافق
        $role = strtolower(trim($user->role));
        
        switch ($role) {
            case 'super_admin':
            case 'admin': // للتوافق مع الأدوار القديمة
                // توجيه المديرين إلى لوحة التحكم الأساسية
                return redirect()->route('admin.dashboard');
            case 'instructor':
            case 'teacher': // للتوافق مع الأدوار القديمة
                return $this->instructorDashboard();
            case 'student':
                return $this->studentDashboard();
            default:
                // إذا كان الدور غير معروف، نعيد إلى الصفحة الرئيسية مع رسالة خطأ
                Auth::logout();
                return redirect('/login')->with('error', 'دور المستخدم غير صالح: ' . $role . '. يرجى التواصل مع الإدارة.');
        }
    }


    private function instructorDashboard()
    {
        $user = Auth::user();
        
        try {
            // معرفات الكورسات التي يدرّسها المدرب: مباشرة (instructor_id) + المعينة له في المسارات (assigned_courses)
            $directCourseIds = \App\Models\AdvancedCourse::where('instructor_id', $user->id)->pluck('id');
            $assignedFromPaths = $user->teachingLearningPaths()->get()->flatMap(function ($ay) {
                $ids = json_decode($ay->pivot->assigned_courses ?? '[]', true);
                return is_array($ids) ? $ids : [];
            });
            $teachingCourseIds = $directCourseIds->merge($assignedFromPaths)->unique()->filter()->values();

            // عدد الكورسات التي يدرّسها
            $myCoursesCount = $teachingCourseIds->count();

            // الكورسات (آخر 5 للعرض)
            $my_courses = $myCoursesCount > 0
                ? \App\Models\AdvancedCourse::whereIn('id', $teachingCourseIds)
                    ->with(['academicSubject', 'academicYear'])
                    ->withCount(['enrollments as active_students_count' => function ($q) {
                        $q->where('status', 'active');
                    }])
                    ->latest()
                    ->take(5)
                    ->get()
                : collect();

            // إحصائيات حقيقية (مبنية على كورسات التدريس فقط)
            $stats = [
                'my_courses' => $myCoursesCount,
                'total_students' => $teachingCourseIds->isEmpty()
                    ? 0
                    : \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $teachingCourseIds)
                        ->where('status', 'active')
                        ->distinct('user_id')
                        ->count('user_id'),
                'my_classrooms' => Classroom::where('teacher_id', $user->id)->count(),
                'total_lectures' => $teachingCourseIds->isEmpty()
                    ? 0
                    : \App\Models\Lecture::whereIn('course_id', $teachingCourseIds)->count(),
                'upcoming_lectures' => $teachingCourseIds->isEmpty()
                    ? 0
                    : \App\Models\Lecture::whereIn('course_id', $teachingCourseIds)
                        ->where('status', 'scheduled')
                        ->where('scheduled_at', '>=', now())
                        ->count(),
                'total_assignments' => \App\Models\Assignment::where('teacher_id', $user->id)->count(),
                'pending_submissions' => \App\Models\AssignmentSubmission::whereHas('assignment', function ($q) use ($user) {
                    $q->where('teacher_id', $user->id);
                })->whereNull('graded_at')->count(),
                'total_exams' => \App\Models\Exam::where('created_by', $user->id)->count(),
                'total_groups' => $teachingCourseIds->isEmpty()
                    ? 0
                    : \App\Models\Group::whereIn('course_id', $teachingCourseIds)->count(),
            ];

            // المحاضرات القادمة (للكورسات التي يدرّسها فقط)
            $upcoming_lectures = $teachingCourseIds->isEmpty()
                ? collect()
                : \App\Models\Lecture::whereIn('course_id', $teachingCourseIds)
                ->where('status', 'scheduled')
                ->where('scheduled_at', '>=', now())
                ->with(['course', 'lesson'])
                ->orderBy('scheduled_at', 'asc')
                ->take(5)
                ->get();

            // الواجبات المعلقة (تسليمات تحتاج تقييم)
            $pending_assignments = \App\Models\AssignmentSubmission::whereHas('assignment', function($q) use ($user) {
                    $q->where('teacher_id', $user->id);
                })
                ->whereNull('graded_at')
                ->with(['assignment', 'student'])
                ->latest()
                ->take(5)
                ->get();

            // المجموعات (للكورسات التي يدرّسها فقط)
            $my_groups = $teachingCourseIds->isEmpty()
                ? collect()
                : \App\Models\Group::whereIn('course_id', $teachingCourseIds)
                    ->with(['course', 'members'])
                    ->latest()
                    ->take(5)
                    ->get();
                
            $my_classrooms = Classroom::where('teacher_id', $user->id)
                ->with('students')
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard.instructor', compact(
                'stats', 
                'my_courses', 
                'my_classrooms',
                'upcoming_lectures',
                'pending_assignments',
                'my_groups'
            ));
        } catch (\Exception $e) {
            // في حالة وجود خطأ، نعيد لوحة تحكم بسيطة
            \Log::error('Instructor Dashboard Error: ' . $e->getMessage());
            $stats = [
                'my_courses' => 0,
                'total_students' => 0,
                'my_classrooms' => 0,
                'total_lectures' => 0,
                'upcoming_lectures' => 0,
                'total_assignments' => 0,
                'pending_submissions' => 0,
                'total_exams' => 0,
                'total_groups' => 0,
            ];
            $my_courses = collect();
            $my_classrooms = collect();
            $upcoming_lectures = collect();
            $pending_assignments = collect();
            $my_groups = collect();
            
            return view('dashboard.instructor', compact(
                'stats', 
                'my_courses', 
                'my_classrooms',
                'upcoming_lectures',
                'pending_assignments',
                'my_groups'
            ));
        }
    }

    private function studentDashboard()
    {
        $user = Auth::user();
        
        $activeCourses = $user->activeCourses()
            ->with(['academicYear', 'academicSubject', 'teacher'])
            ->get();

        $activeCourseIds = $activeCourses->pluck('id')->filter()->unique()->values();

        // تحميل بيانات التسجيلات لضمان توفر التقدم
        $enrollments = $user->courseEnrollments()
            ->whereIn('advanced_course_id', $activeCourseIds)
            ->whereIn('status', ['active', 'completed'])
            ->get()
            ->keyBy('advanced_course_id');

        $activeCourses->each(function ($course) use ($enrollments) {
            if ($enrollment = $enrollments->get($course->id)) {
                $course->setRelation('enrollment', $enrollment);

                if ($course->pivot) {
                    $course->pivot->progress = (float) ($course->pivot->progress ?? $enrollment->progress ?? 0);
                }
            }
        });

        $recentOrders = Order::where('user_id', $user->id)
            ->with(['course.academicYear', 'course.academicSubject'])
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'active_courses' => $activeCourses->count(),
            'pending_orders' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'completed_courses' => $user->courseEnrollments()->where('status', 'completed')->count(),
            'total_progress' => $this->calculateOverallProgress($user),
            'total_learning_hours' => $activeCourseIds->isEmpty()
                ? 0
                : (int) AdvancedCourse::whereIn('id', $activeCourseIds)->sum('duration_hours'),
            'average_score' => round($user->getAverageScore(), 1),
            'completed_exams' => $user->getCompletedExamsCount(),
        ];

        $upcomingAssignments = LectureAssignment::with(['lecture.course.academicSubject'])
            ->whereHas('lecture', function ($query) use ($activeCourseIds) {
                $query->whereIn('course_id', $activeCourseIds);
            })
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhereNotIn('status', ['archived']);
            })
            ->where(function ($query) {
                $query->whereNull('due_date')
                    ->orWhere('due_date', '>=', now()->startOfDay());
            })
            ->get()
            ->sortBy(function ($assignment) {
                return $assignment->due_date ?? now()->addYear();
            })
            ->values()
            ->take(5);

        $upcomingExams = Exam::with(['course.academicSubject'])
            ->whereIn('advanced_course_id', $activeCourseIds)
            ->where('is_active', true)
            ->where('is_published', true)
            ->where(function ($query) {
                $query->whereNull('end_time')
                    ->orWhere('end_time', '>=', now());
            })
            ->get()
            ->sortBy(function ($exam) {
                if ($exam->start_time) {
                    return $exam->start_time;
                }

                if ($exam->start_date) {
                    return $exam->start_date->startOfDay();
                }

                return $exam->created_at ?? now();
            })
            ->values()
            ->take(5);

        $recentExamAttempts = $user->examAttempts()
            ->with(['exam.course'])
            ->latest()
            ->take(5)
            ->get();

        $recentCertificates = Certificate::where('user_id', $user->id)
            ->with('course')
            ->orderByDesc('issued_at')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view(
            'dashboard.student',
            compact(
                'stats',
                'activeCourses',
                'recentOrders',
                'upcomingAssignments',
                'upcomingExams',
                'recentExamAttempts',
                'recentCertificates'
            )
        );
    }

    private function calculateOverallProgress($user)
    {
        $enrollments = $user->courseEnrollments()
            ->whereIn('status', ['active', 'completed'])
            ->get();
        if ($enrollments->isEmpty()) return 0;
        
        $totalProgress = $enrollments->reduce(function ($carry, $enrollment) {
            return $carry + (float) ($enrollment->progress ?? 0);
        }, 0);

        return round($totalProgress / $enrollments->count(), 1);
    }

}
