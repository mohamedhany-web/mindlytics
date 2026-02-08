<?php

namespace App\Services;

use App\Models\User;
use App\Models\AdvancedCourse;
use App\Models\StudentCourseEnrollment;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Wallet;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class StatisticsCacheService
{
    /**
     * الحصول على إحصائيات لوحة التحكم مع التخزين المؤقت
     */
    public function getDashboardStats(): array
    {
        return Cache::remember('dashboard_stats', now()->addMinutes(5), function () {
            $now = now();
            $currentPeriodStart = $now->copy()->startOfMonth();
            $currentPeriodEnd = $now;
            $previousPeriodStart = $now->copy()->subMonth()->startOfMonth();
            $previousPeriodEnd = $now->copy()->subMonth()->endOfMonth();

            $stats = [
                'total_users' => User::count(),
                'total_students' => User::where('role', 'student')->count(),
                'total_instructors' => User::where('role', 'instructor')->count(),
                'total_courses' => AdvancedCourse::count(),
                'published_courses' => AdvancedCourse::where('is_active', true)->count(),
                'total_subjects' => \App\Models\AcademicSubject::count(),
                'total_exams' => \App\Models\AdvancedExam::count(),
                'total_question_banks' => \App\Models\QuestionBank::count(),
                'active_students' => User::where('role', 'student')
                                       ->where('is_active', true)
                                       ->whereHas('courseEnrollments')
                                       ->count(),
                'total_revenue' => Payment::where('status', 'completed')->sum('amount') ?? 0,
                'pending_invoices' => Invoice::where('status', 'pending')->count() ?? 0,
                'overdue_invoices' => Invoice::where('status', 'overdue')->count() ?? 0,
                'total_enrollments' => StudentCourseEnrollment::where('status', 'active')->count() ?? 0,
                'monthly_revenue' => Payment::where('status', 'completed')
                    ->whereMonth('paid_at', now()->month)
                    ->whereYear('paid_at', now()->year)
                    ->sum('amount') ?? 0,
                'total_wallet_balance' => Wallet::where('is_active', true)->sum('balance') ?? 0,
            ];

            return $stats;
        });
    }

    /**
     * الحصول على إحصائيات التسجيلات مع التخزين المؤقت
     */
    public function getEnrollmentStats(): array
    {
        return Cache::remember('enrollment_stats', now()->addMinutes(10), function () {
            return [
                'total' => StudentCourseEnrollment::count(),
                'pending' => StudentCourseEnrollment::where('status', 'pending')->count(),
                'active' => StudentCourseEnrollment::where('status', 'active')->count(),
                'completed' => StudentCourseEnrollment::where('status', 'completed')->count(),
            ];
        });
    }

    /**
     * الحصول على إحصائيات المستخدمين مع التخزين المؤقت
     */
    public function getUserStats(): array
    {
        return Cache::remember('user_stats', now()->addMinutes(10), function () {
            return [
                'total' => User::count(),
                'students' => User::where('role', 'student')->count(),
                'instructors' => User::where('role', 'instructor')->count(),
                'active_students' => User::where('role', 'student')
                    ->where('is_active', true)
                    ->count(),
            ];
        });
    }

    /**
     * مسح جميع الإحصائيات من الكاش
     */
    public function clearAllStats(): void
    {
        Cache::forget('dashboard_stats');
        Cache::forget('enrollment_stats');
        Cache::forget('user_stats');
    }

    /**
     * مسح إحصائيات محددة
     */
    public function clearStats(string $key): void
    {
        Cache::forget($key);
    }
}
