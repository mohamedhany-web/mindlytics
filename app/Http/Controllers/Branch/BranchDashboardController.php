<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\AdvancedCourse;
use App\Models\Invoice;
use App\Models\OfflineCourse;
use App\Models\Order;
use App\Models\Payment;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BranchDashboardController extends Controller
{
    public function index(): View
    {
        $branch = auth()->user()->branch;
        abort_unless($branch, 404);
        $branchId = (int) $branch->id;

        $now = now();
        $currentPeriodStart = $now->copy()->startOfMonth();
        $currentPeriodEnd = $now;
        $previousPeriodStart = $now->copy()->subMonth()->startOfMonth();
        $previousPeriodEnd = $now->copy()->subMonth()->endOfMonth();

        $usersInBranch = User::query()->where('branch_id', $branchId);

        $monthlyStats = [
            'new_users_this_month' => (clone $usersInBranch)->whereBetween('created_at', [$currentPeriodStart, $currentPeriodEnd])->count(),
            'new_students_this_month' => (clone $usersInBranch)->where('role', 'student')->whereBetween('created_at', [$currentPeriodStart, $currentPeriodEnd])->count(),
            'new_instructors_this_month' => (clone $usersInBranch)->whereIn('role', ['instructor', 'teacher'])->whereBetween('created_at', [$currentPeriodStart, $currentPeriodEnd])->count(),
            'new_courses_this_month' => AdvancedCourse::query()->where('branch_id', $branchId)->whereBetween('created_at', [$currentPeriodStart, $currentPeriodEnd])->count(),
            'new_enrollments_this_month' => StudentCourseEnrollment::query()->where('branch_id', $branchId)->whereBetween('created_at', [$currentPeriodStart, $currentPeriodEnd])->count(),
        ];

        $lastDayPrevMonth = (int) $previousPeriodEnd->format('j');
        $targetDay = min((int) $now->format('j'), $lastDayPrevMonth);
        $previousMonthMtdEnd = $previousPeriodStart->copy()->addDays($targetDay - 1)->endOfDay();
        $previousMonthMtdRevenue = (float) (Payment::query()
            ->where('branch_id', $branchId)
            ->where('status', 'completed')
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$previousPeriodStart->copy()->startOfDay(), $previousMonthMtdEnd])
            ->sum('amount') ?? 0);

        $monthlyRevenue = (float) (Payment::query()
            ->where('branch_id', $branchId)
            ->where('status', 'completed')
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$currentPeriodStart, $currentPeriodEnd])
            ->sum('amount') ?? 0);

        $monthlyComparisons = [
            'new_users' => $this->calculateChange(
                $monthlyStats['new_users_this_month'],
                (clone $usersInBranch)->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])->count()
            ),
            'new_students' => $this->calculateChange(
                (clone $usersInBranch)->where('role', 'student')->whereBetween('created_at', [$currentPeriodStart, $currentPeriodEnd])->count(),
                (clone $usersInBranch)->where('role', 'student')->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])->count()
            ),
            'new_instructors' => $this->calculateChange(
                (clone $usersInBranch)->whereIn('role', ['instructor', 'teacher'])->whereBetween('created_at', [$currentPeriodStart, $currentPeriodEnd])->count(),
                (clone $usersInBranch)->whereIn('role', ['instructor', 'teacher'])->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])->count()
            ),
            'new_courses' => $this->calculateChange(
                $monthlyStats['new_courses_this_month'],
                AdvancedCourse::query()->where('branch_id', $branchId)->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])->count()
            ),
            'active_enrollments' => $this->calculateChange(
                StudentCourseEnrollment::query()->where('branch_id', $branchId)->where('status', 'active')->whereBetween('created_at', [$currentPeriodStart, $currentPeriodEnd])->count(),
                StudentCourseEnrollment::query()->where('branch_id', $branchId)->where('status', 'active')->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])->count()
            ),
            'monthly_revenue' => $this->calculateChange($monthlyRevenue, $previousMonthMtdRevenue),
            'pending_invoices' => $this->calculateChange(
                Invoice::query()->where('branch_id', $branchId)->where('status', 'pending')->whereBetween('created_at', [$currentPeriodStart, $currentPeriodEnd])->count(),
                Invoice::query()->where('branch_id', $branchId)->where('status', 'pending')->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])->count()
            ),
        ];

        $totalStudents = (clone $usersInBranch)->where('role', 'student')->count();
        $totalInstructors = (clone $usersInBranch)->whereIn('role', ['instructor', 'teacher'])->count();
        $totalCourses = AdvancedCourse::query()->where('branch_id', $branchId)->count();
        $totalEnrollments = StudentCourseEnrollment::query()->where('branch_id', $branchId)->count();
        $totalRevenue = (float) (Payment::query()
            ->where('branch_id', $branchId)
            ->where('status', 'completed')
            ->sum('amount') ?? 0);
        $pendingInvoicesCount = Invoice::query()->where('branch_id', $branchId)->where('status', 'pending')->count();

        $metrics = [
            'users' => [
                'total' => (clone $usersInBranch)->count(),
                'new_this_month' => $monthlyStats['new_users_this_month'],
                'trend' => $monthlyComparisons['new_users'],
            ],
            'students' => [
                'total' => $totalStudents,
                'new_this_month' => $monthlyComparisons['new_students']['current'],
                'trend' => $monthlyComparisons['new_students'],
            ],
            'instructors' => [
                'total' => $totalInstructors,
                'new_this_month' => $monthlyComparisons['new_instructors']['current'],
                'trend' => $monthlyComparisons['new_instructors'],
            ],
            'courses' => [
                'total' => $totalCourses,
                'new_this_month' => $monthlyComparisons['new_courses']['current'],
                'trend' => $monthlyComparisons['new_courses'],
            ],
            'enrollments' => [
                'total' => $totalEnrollments,
                'new_this_month' => $monthlyComparisons['active_enrollments']['current'],
                'trend' => $monthlyComparisons['active_enrollments'],
            ],
            'monthly_revenue' => [
                'current' => $monthlyRevenue,
                'trend' => $monthlyComparisons['monthly_revenue'],
            ],
            'pending_invoices' => [
                'total' => $pendingInvoicesCount,
                'new_this_month' => $monthlyComparisons['pending_invoices']['current'],
                'trend' => $monthlyComparisons['pending_invoices'],
            ],
        ];

        $stats = [
            'users' => (clone $usersInBranch)->count(),
            'students' => $totalStudents,
            'instructors' => $totalInstructors,
            'advanced_courses' => $totalCourses,
            'offline_courses' => OfflineCourse::query()->where('branch_id', $branchId)->count(),
            'learning_paths' => AcademicYear::query()->where('branch_id', $branchId)->count(),
            'enrollments' => $totalEnrollments,
            'orders' => Order::query()->where('branch_id', $branchId)->count(),
            'orders_pending' => Order::query()->where('branch_id', $branchId)->where('status', Order::STATUS_PENDING)->count(),
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'pending_invoices' => $pendingInvoicesCount,
        ];

        $recentUsers = (clone $usersInBranch)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get(['id', 'name', 'email', 'role', 'is_active', 'created_at']);

        $recentOrders = Order::query()
            ->where('branch_id', $branchId)
            ->with(['user', 'course', 'learningPath'])
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $recentPayments = Payment::query()
            ->where('branch_id', $branchId)
            ->where('status', 'completed')
            ->with(['user', 'invoice'])
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $pendingInvoices = Invoice::query()
            ->where('branch_id', $branchId)
            ->where('status', 'pending')
            ->with('user')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $recentCourses = AdvancedCourse::query()
            ->where('branch_id', $branchId)
            ->with(['academicSubject', 'instructor'])
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();

        $userIds = User::query()->where('branch_id', $branchId)->pluck('id');

        $recentActivities = $userIds->isEmpty()
            ? collect()
            : ActivityLog::query()
                ->whereIn('user_id', $userIds)
                ->with('user')
                ->latest()
                ->limit(8)
                ->get();

        $weeklyActivity = $this->weeklyActivityForBranch($userIds);

        return view('branch-office.dashboard', compact(
            'branch',
            'stats',
            'metrics',
            'recentUsers',
            'recentOrders',
            'recentPayments',
            'pendingInvoices',
            'recentCourses',
            'recentActivities',
            'weeklyActivity'
        ));
    }

    /**
     * @param  \Illuminate\Support\Collection<int, int>  $userIds
     */
    private function weeklyActivityForBranch(\Illuminate\Support\Collection $userIds): \Illuminate\Support\Collection
    {
        if ($userIds->isEmpty()) {
            return collect();
        }

        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            return ActivityLog::query()
                ->whereIn('user_id', $userIds)
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->select(
                    DB::raw("strftime('%Y-%m-%d', created_at) as date"),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        }

        return ActivityLog::query()
            ->whereIn('user_id', $userIds)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * @return array{current: float, previous: float, difference: float, percent: float}
     */
    private function calculateChange(float|int $current, float|int $previous): array
    {
        $current = (float) $current;
        $previous = (float) $previous;
        $difference = $current - $previous;
        $percent = $previous > 0
            ? round(($difference / $previous) * 100, 1)
            : ($current > 0 ? 100.0 : 0.0);

        return [
            'current' => $current,
            'previous' => $previous,
            'difference' => $difference,
            'percent' => $percent,
        ];
    }
}
