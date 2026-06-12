<?php

namespace App\Services;

use App\Models\AdvancedCourse;
use App\Models\OfflineCourseEnrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminDashboardAnalyticsService
{
    /**
     * @return array<string, mixed>
     */
    public function build(int $months = 6): array
    {
        $monthly = $this->monthlyAcademyTrend($months);
        $direction = $this->directionSummary($monthly);
        $enrollmentMix = $this->enrollmentMix();
        $userRoles = $this->userRoleBreakdown();
        $dailyRevenue = $this->dailyRevenueSeries(14);
        $topCourses = $this->topCoursesByEnrollment(6);

        return [
            'monthly' => $monthly,
            'direction' => $direction,
            'enrollment_mix' => $enrollmentMix,
            'user_roles' => $userRoles,
            'daily_revenue' => $dailyRevenue,
            'top_courses' => $topCourses,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function monthlyAcademyTrend(int $months): array
    {
        $labels = [];
        $revenue = [];
        $onlineEnrollments = [];
        $offlineEnrollments = [];
        $newStudents = [];
        $orders = [];
        $paymentsCount = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $start = now()->subMonths($i)->startOfMonth();
            $end = now()->subMonths($i)->endOfMonth();

            $labels[] = $start->locale('ar')->isoFormat('MMM YYYY');

            $revenue[] = round((float) Payment::query()
                ->where('status', 'completed')
                ->whereBetween('paid_at', [$start, $end])
                ->sum('amount'), 2);

            $onlineEnrollments[] = (int) StudentCourseEnrollment::query()
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $offlineEnrollments[] = (int) OfflineCourseEnrollment::query()
                ->whereBetween('enrolled_at', [$start, $end])
                ->count();

            $newStudents[] = (int) User::query()
                ->where('role', 'student')
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $orders[] = (int) Order::query()
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $paymentsCount[] = (int) Payment::query()
                ->where('status', 'completed')
                ->whereBetween('paid_at', [$start, $end])
                ->count();
        }

        $totalEnrollments = [];
        foreach ($onlineEnrollments as $idx => $online) {
            $totalEnrollments[] = $online + ($offlineEnrollments[$idx] ?? 0);
        }

        return compact(
            'labels',
            'revenue',
            'onlineEnrollments',
            'offlineEnrollments',
            'totalEnrollments',
            'newStudents',
            'orders',
            'paymentsCount'
        );
    }

    /**
     * @param  array<string, mixed>  $monthly
     * @return array<string, mixed>
     */
    private function directionSummary(array $monthly): array
    {
        $n = count($monthly['labels'] ?? []);
        if ($n < 2) {
            return [
                'status' => 'neutral',
                'label' => 'بيانات غير كافية',
                'summary' => 'يحتاج شهرين على الأقل لقياس اتجاه الأكاديمية.',
                'metrics' => [],
            ];
        }

        $idx = $n - 1;
        $prev = $n - 2;

        $metrics = [
            'revenue' => [
                'label' => 'الإيراد',
                'current' => $monthly['revenue'][$idx] ?? 0,
                'previous' => $monthly['revenue'][$prev] ?? 0,
                'pct' => $this->pctDelta($monthly['revenue'][$prev] ?? 0, $monthly['revenue'][$idx] ?? 0),
            ],
            'enrollments' => [
                'label' => 'التسجيلات',
                'current' => $monthly['totalEnrollments'][$idx] ?? 0,
                'previous' => $monthly['totalEnrollments'][$prev] ?? 0,
                'pct' => $this->pctDelta($monthly['totalEnrollments'][$prev] ?? 0, $monthly['totalEnrollments'][$idx] ?? 0),
            ],
            'students' => [
                'label' => 'طلاب جدد',
                'current' => $monthly['newStudents'][$idx] ?? 0,
                'previous' => $monthly['newStudents'][$prev] ?? 0,
                'pct' => $this->pctDelta($monthly['newStudents'][$prev] ?? 0, $monthly['newStudents'][$idx] ?? 0),
            ],
            'orders' => [
                'label' => 'الطلبات',
                'current' => $monthly['orders'][$idx] ?? 0,
                'previous' => $monthly['orders'][$prev] ?? 0,
                'pct' => $this->pctDelta($monthly['orders'][$prev] ?? 0, $monthly['orders'][$idx] ?? 0),
            ],
        ];

        $positive = collect($metrics)->filter(fn ($m) => ($m['pct'] ?? 0) > 0)->count();
        $revenueUp = ($metrics['revenue']['pct'] ?? 0) > 0;
        $enrollUp = ($metrics['enrollments']['pct'] ?? 0) > 0;

        if ($positive >= 3 && $revenueUp && $enrollUp) {
            $status = 'growth';
            $label = 'نمو قوي — الأكاديمية في صعود';
            $summary = 'الإيراد والتسجيلات والطلاب الجدد يتحسّنون. استمر في التسويق وجودة المحتوى.';
        } elseif ($positive >= 2 || ($revenueUp && $enrollUp)) {
            $status = 'stable';
            $label = 'اتجاه إيجابي — تحسّن جزئي';
            $summary = 'بعض المؤشرات تتحسّن. ركّز على تحويل الطلبات والفواتير المعلّقة.';
        } elseif ($positive <= 1 && (($metrics['revenue']['pct'] ?? 0) < 0 || ($metrics['enrollments']['pct'] ?? 0) < 0)) {
            $status = 'decline';
            $label = 'تراجع — يحتاج تدخل';
            $summary = 'انخفاض في الإيراد أو التسجيلات. راجع الحملات، الأسعار، وتجربة التسجيل.';
        } else {
            $status = 'neutral';
            $label = 'اتجاه مستقر';
            $summary = 'الأرقام متقاربة مع الشهر السابق. راقب الطلبات والفواتير.';
        }

        return [
            'status' => $status,
            'label' => $label,
            'summary' => $summary,
            'metrics' => $metrics,
            'current_month_label' => $monthly['labels'][$idx] ?? '',
            'previous_month_label' => $monthly['labels'][$prev] ?? '',
        ];
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    private function enrollmentMix(): array
    {
        $online = (int) StudentCourseEnrollment::query()->where('status', 'active')->count();
        $offline = (int) OfflineCourseEnrollment::query()->where('status', 'active')->count();

        return [
            'labels' => ['أونلاين', 'أوفلاين'],
            'values' => [$online, $offline],
        ];
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    private function userRoleBreakdown(): array
    {
        $roles = [
            'student' => 'طلاب',
            'instructor' => 'مدربون',
            'employee' => 'موظفون',
            'super_admin' => 'إدارة',
        ];

        $labels = [];
        $values = [];

        foreach ($roles as $role => $label) {
            $count = (int) User::query()->where('role', $role)->count();
            if ($count > 0) {
                $labels[] = $label;
                $values[] = $count;
            }
        }

        $other = (int) User::query()->whereNotIn('role', array_keys($roles))->count();
        if ($other > 0) {
            $labels[] = 'أخرى';
            $values[] = $other;
        }

        return compact('labels', 'values');
    }

    /**
     * @return array{labels: list<string>, revenue: list<float>, payments: list<int>}
     */
    private function dailyRevenueSeries(int $days): array
    {
        $labels = [];
        $revenue = [];
        $payments = [];

        $cursor = now()->subDays($days - 1)->startOfDay();
        $end = now()->endOfDay();

        while ($cursor->lte($end)) {
            $dayStart = $cursor->copy()->startOfDay();
            $dayEnd = $cursor->copy()->endOfDay();

            $labels[] = $cursor->format('m-d');

            $revenue[] = round((float) Payment::query()
                ->where('status', 'completed')
                ->whereBetween('paid_at', [$dayStart, $dayEnd])
                ->sum('amount'), 2);

            $payments[] = (int) Payment::query()
                ->where('status', 'completed')
                ->whereBetween('paid_at', [$dayStart, $dayEnd])
                ->count();

            $cursor->addDay();
        }

        return compact('labels', 'revenue', 'payments');
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    private function topCoursesByEnrollment(int $limit): array
    {
        $rows = AdvancedCourse::query()
            ->withCount(['enrollments as enrollments_count' => function ($q) {
                $q->where('status', 'active');
            }])
            ->orderByDesc('enrollments_count')
            ->limit($limit)
            ->get(['id', 'title']);

        return [
            'labels' => $rows->pluck('title')->map(fn ($t) => \Illuminate\Support\Str::limit((string) $t, 28))->all(),
            'values' => $rows->pluck('enrollments_count')->map(fn ($v) => (int) $v)->all(),
        ];
    }

    /**
     * @param  Collection<int, object>|array<int, object>  $weeklyActivity
     * @return array{labels: list<string>, values: list<int>}
     */
    public function formatWeeklyActivity(Collection|array $weeklyActivity): array
    {
        $items = $weeklyActivity instanceof Collection ? $weeklyActivity : collect($weeklyActivity);

        return [
            'labels' => $items->pluck('date')->map(fn ($d) => Carbon::parse($d)->format('m-d'))->all(),
            'values' => $items->pluck('count')->map(fn ($c) => (int) $c)->all(),
        ];
    }

    private function pctDelta(float $previous, float $current): ?float
    {
        if (abs($previous) < 0.0001) {
            return $current > 0 ? 100.0 : ($current < 0 ? -100.0 : 0.0);
        }

        return round((($current - $previous) / abs($previous)) * 100, 1);
    }
}
