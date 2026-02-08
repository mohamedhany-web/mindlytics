<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentCourseEnrollment;
use App\Models\OfflineCourseEnrollment;
use App\Models\EmployeeTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QualityControlController extends Controller
{
    /**
     * لوحة الرقابة والجودة الرئيسية
     */
    public function index()
    {
        // إحصائيات الطلاب
        $studentStats = [
            'total' => User::students()->count(),
            'active' => User::students()->where('is_active', true)->count(),
            'recent_registrations' => User::students()
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
            'enrollments_this_month' => StudentCourseEnrollment::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        // إحصائيات المدربين
        $instructorStats = [
            'total' => User::instructors()->count(),
            'active' => User::instructors()->where('is_active', true)->count(),
            'with_agreements' => \App\Models\InstructorAgreement::where('status', 'active')->distinct('instructor_id')->count(),
        ];

        // إحصائيات الموظفين
        $employeeStats = [
            'total' => User::employees()->count(),
            'active' => User::employees()->where('is_active', true)->whereNull('termination_date')->count(),
            'pending_tasks' => EmployeeTask::pending()->count(),
            'overdue_tasks' => EmployeeTask::overdue()->count(),
        ];

        // النشاطات الأخيرة
        $recentActivities = \App\Models\ActivityLog::with('user')
            ->latest()
            ->take(20)
            ->get();

        // العمليات المعلقة
        $pendingOperations = [
            'pending_enrollments' => StudentCourseEnrollment::where('status', 'pending')->count(),
            'pending_offline_enrollments' => OfflineCourseEnrollment::where('status', 'pending')->count(),
            'pending_tasks' => EmployeeTask::pending()->count(),
        ];

        return view('admin.quality-control.index', compact(
            'studentStats',
            'instructorStats',
            'employeeStats',
            'recentActivities',
            'pendingOperations'
        ));
    }

    /**
     * رقابة الطلاب
     */
    public function students(Request $request)
    {
        $query = User::students()->with(['academicYear']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $students = $query->latest()->paginate(20);

        // إحصائيات لكل طالب
        $students->getCollection()->transform(function($student) {
            $student->enrollments_count = $student->courseEnrollments()->count();
            $student->completed_courses = $student->courseEnrollments()->where('status', 'completed')->count();
            $student->last_activity = $student->last_login_at;
            return $student;
        });

        return view('admin.quality-control.students', compact('students'));
    }

    /**
     * رقابة المدربين
     */
    public function instructors(Request $request)
    {
        $query = User::instructors()->with(['employeeJob']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $instructors = $query->latest()->paginate(20);

        // إحصائيات لكل مدرب
        $instructors->getCollection()->transform(function($instructor) {
            $instructor->courses_count = \App\Models\AdvancedCourse::where('instructor_id', $instructor->id)->count();
            $instructor->agreements_count = \App\Models\InstructorAgreement::where('instructor_id', $instructor->id)->count();
            $instructor->last_activity = $instructor->last_login_at;
            return $instructor;
        });

        return view('admin.quality-control.instructors', compact('instructors'));
    }

    /**
     * رقابة الموظفين
     */
    public function employees(Request $request)
    {
        $query = User::employees()->with(['employeeJob']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        $employees = $query->latest('hire_date')->paginate(20);

        // إحصائيات لكل موظف
        $employees->getCollection()->transform(function($employee) {
            $employee->tasks_count = $employee->employeeTasks()->count();
            $employee->completed_tasks = $employee->employeeTasks()->where('status', 'completed')->count();
            $employee->pending_tasks = $employee->employeeTasks()->where('status', 'pending')->count();
            $employee->overdue_tasks = $employee->employeeTasks()
                ->where('deadline', '<', now())
                ->whereIn('status', ['pending', 'in_progress'])
                ->count();
            return $employee;
        });

        return view('admin.quality-control.employees', compact('employees'));
    }

    /**
     * متابعة العمليات
     */
    public function operations()
    {
        // عمليات التسجيل
        $enrollmentOperations = [
            'online_pending' => StudentCourseEnrollment::where('status', 'pending')->count(),
            'online_active' => StudentCourseEnrollment::where('status', 'active')->count(),
            'offline_pending' => OfflineCourseEnrollment::where('status', 'pending')->count(),
            'offline_active' => OfflineCourseEnrollment::where('status', 'active')->count(),
        ];

        // عمليات المهام
        $taskOperations = [
            'pending' => EmployeeTask::pending()->count(),
            'in_progress' => EmployeeTask::inProgress()->count(),
            'completed_today' => EmployeeTask::where('status', 'completed')
                ->whereDate('completed_at', today())
                ->count(),
            'overdue' => EmployeeTask::overdue()->count(),
        ];

        // عمليات الدفع
        $paymentOperations = [
            'pending' => \App\Models\Payment::where('status', 'pending')->count(),
            'completed_today' => \App\Models\Payment::where('status', 'completed')
                ->whereDate('paid_at', today())
                ->sum('amount'),
        ];

        // سجل النشاطات
        $activityLog = \App\Models\ActivityLog::with('user')
            ->latest()
            ->take(50)
            ->get();

        return view('admin.quality-control.operations', compact(
            'enrollmentOperations',
            'taskOperations',
            'paymentOperations',
            'activityLog'
        ));
    }
}
