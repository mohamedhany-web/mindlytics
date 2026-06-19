<?php

namespace App\Services;

use App\Models\DesignTaskCycle;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EmployeeMonthlyPerformanceInsightsService
{
    /**
     * @param  array<string, mixed>  $current
     * @param  array<string, mixed>  $previous
     * @param  list<array{employee: \App\Models\User, required: int, submitted: int, rate: float|null}>  $dailyCompliance
     * @return array<string, mixed>
     */
    public function build(array $current, array $previous, array $dailyCompliance): array
    {
        $rows = $current['rows'] ?? [];
        $summary = $current['summary'] ?? [];
        $prevSummary = $previous['summary'] ?? [];
        $start = $current['start'];
        $end = $current['end'];

        $dailyByUserId = collect($dailyCompliance)->keyBy(fn ($r) => $r['employee']->id);

        $enrichedRows = [];
        foreach ($rows as $row) {
            $uid = $row['user']->id;
            $daily = $dailyByUserId->get($uid);
            $row['daily_report_required'] = $daily['required'] ?? 0;
            $row['daily_report_submitted'] = $daily['submitted'] ?? 0;
            $row['daily_report_rate_pct'] = $daily['rate'] ?? null;
            $row['health_score'] = $this->healthScore($row);
            $row['risk_flags'] = $this->riskFlags($row);
            $enrichedRows[] = $row;
        }

        usort($enrichedRows, fn ($a, $b) => ($a['health_score'] ?? 0) <=> ($b['health_score'] ?? 0));

        $atRisk = array_values(array_filter($enrichedRows, fn ($r) => ($r['health_score'] ?? 100) < 65 || count($r['risk_flags']) > 0));
        $topPerformers = array_values(array_filter($enrichedRows, fn ($r) => ($r['health_score'] ?? 0) >= 80 && ($r['tasks_completed_in_month'] > 0 || $r['designer_submissions_in_month'] > 0)));
        usort($topPerformers, fn ($a, $b) => ($b['health_score'] ?? 0) <=> ($a['health_score'] ?? 0));

        return [
            'direction' => $this->buildDirection($summary, $prevSummary, $start, $end),
            'alerts' => $this->buildAlerts($enrichedRows, $summary, $dailyCompliance),
            'at_risk' => array_slice($atRisk, 0, 12),
            'top_performers' => array_slice($topPerformers, 0, 8),
            'charts' => $this->buildCharts($current, $previous, $enrichedRows, $dailyCompliance, $start, $end),
            'enriched_rows' => $enrichedRows,
            'team_health_score' => $this->teamHealthScore($enrichedRows),
        ];
    }

    /**
     * @param  array<string, mixed>  $summary
     * @param  array<string, mixed>  $prevSummary
     * @return array<string, mixed>
     */
    private function buildDirection(array $summary, array $prevSummary, Carbon $start, Carbon $end): array
    {
        $completedDelta = $this->deltaPct($summary['tasks_completed'] ?? 0, $prevSummary['tasks_completed'] ?? 0);
        $onTimeDelta = $this->deltaPoints($summary['tasks_on_time_rate_pct'], $prevSummary['tasks_on_time_rate_pct']);
        $overdueDelta = ($summary['open_overdue_tasks'] ?? 0) - ($prevSummary['open_overdue_tasks'] ?? 0);

        $score = 0;
        if ($completedDelta !== null) {
            $score += $completedDelta > 5 ? 2 : ($completedDelta < -5 ? -2 : 0);
        }
        if ($onTimeDelta !== null) {
            $score += $onTimeDelta > 3 ? 2 : ($onTimeDelta < -3 ? -2 : 0);
        }
        if ($overdueDelta > 2) {
            $score -= 2;
        } elseif ($overdueDelta < 0) {
            $score += 1;
        }

        $status = match (true) {
            $score >= 2 => 'growth',
            $score <= -2 => 'decline',
            default => 'stable',
        };

        $label = match ($status) {
            'growth' => 'اتجاه إيجابي — الأداء يتحسّن',
            'decline' => 'اتجاه مقلق — تراجع في الالتزام أو الإنتاجية',
            default => 'اتجاه مستقر — مراقبة مستمرة مطلوبة',
        };

        $parts = [];
        if ($completedDelta !== null) {
            $parts[] = 'المهام المكتملة '.($completedDelta >= 0 ? 'ارتفعت' : 'انخفضت').' '.abs($completedDelta).'% عن الشهر السابق';
        }
        if ($onTimeDelta !== null) {
            $parts[] = 'التزام الموعد '.($onTimeDelta >= 0 ? 'تحسّن' : 'تراجع').' بـ '.abs($onTimeDelta).' نقطة';
        }
        if (($summary['open_overdue_tasks'] ?? 0) > 0) {
            $parts[] = 'يوجد '.number_format($summary['open_overdue_tasks']).' مهمة مفتوحة متأخرة بنهاية الشهر';
        }
        if (($summary['designer_late'] ?? 0) > ($summary['designer_on_time'] ?? 0)) {
            $parts[] = 'تسليمات المصممين: المتأخر أكثر من الملتزم بالموعد';
        }

        return [
            'status' => $status,
            'label' => $label,
            'summary' => $parts !== [] ? implode(' · ', $parts) : 'لا توجد بيانات كافية للمقارنة مع الشهر السابق.',
            'current_month_label' => $start->translatedFormat('F Y'),
            'previous_month_label' => $start->copy()->subMonth()->translatedFormat('F Y'),
            'metrics' => [
                ['label' => 'مهام مكتملة', 'current' => $summary['tasks_completed'] ?? 0, 'previous' => $prevSummary['tasks_completed'] ?? 0, 'delta_pct' => $completedDelta],
                ['label' => 'التزام الموعد %', 'current' => $summary['tasks_on_time_rate_pct'], 'previous' => $prevSummary['tasks_on_time_rate_pct'], 'delta_pts' => $onTimeDelta],
                ['label' => 'متأخرة مفتوحة', 'current' => $summary['open_overdue_tasks'] ?? 0, 'previous' => $prevSummary['open_overdue_tasks'] ?? 0, 'delta' => $overdueDelta],
                ['label' => 'تسليمات مصمم', 'current' => $summary['designer_submissions_month'] ?? 0, 'previous' => $prevSummary['designer_submissions_month'] ?? 0, 'delta_pct' => $this->deltaPct($summary['designer_submissions_month'] ?? 0, $prevSummary['designer_submissions_month'] ?? 0)],
            ],
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @param  list<array{employee: \App\Models\User, required: int, submitted: int, rate: float|null}>  $dailyCompliance
     * @return list<array<string, mixed>>
     */
    private function buildAlerts(array $rows, array $summary, array $dailyCompliance): array
    {
        $alerts = [];

        if (($summary['open_overdue_tasks'] ?? 0) >= 5) {
            $alerts[] = [
                'level' => 'critical',
                'icon' => 'fa-clock',
                'title' => 'تراكم مهام متأخرة',
                'message' => 'يوجد '.number_format($summary['open_overdue_tasks']).' مهمة مفتوحة تجاوزت موعدها — خطر على التسليم والجودة.',
            ];
        } elseif (($summary['open_overdue_tasks'] ?? 0) > 0) {
            $alerts[] = [
                'level' => 'warning',
                'icon' => 'fa-exclamation-triangle',
                'title' => 'مهام متأخرة مفتوحة',
                'message' => number_format($summary['open_overdue_tasks']).' مهمة لم تُغلق بعد موعدها النهائي.',
            ];
        }

        if ($summary['tasks_on_time_rate_pct'] !== null && $summary['tasks_on_time_rate_pct'] < 70) {
            $alerts[] = [
                'level' => $summary['tasks_on_time_rate_pct'] < 50 ? 'critical' : 'warning',
                'icon' => 'fa-calendar-times',
                'title' => 'ضعف التزام بالمواعيد (مهام)',
                'message' => 'نسبة الإنجاز في الموعد '.round($summary['tasks_on_time_rate_pct'], 1).'% فقط — أقل من المستوى المطلوب (70%+).',
            ];
        }

        if ($summary['designer_on_time_rate_pct'] !== null && $summary['designer_on_time_rate_pct'] < 70) {
            $alerts[] = [
                'level' => 'warning',
                'icon' => 'fa-palette',
                'title' => 'تأخر في تسليمات التصميم',
                'message' => 'التزام المصممين بالموعد '.round($summary['designer_on_time_rate_pct'], 1).'% — راجع أحمال العمل والمواعيد.',
            ];
        }

        $lowDaily = collect($dailyCompliance)->filter(fn ($r) => $r['required'] >= 5 && $r['rate'] !== null && $r['rate'] < 70);
        if ($lowDaily->isNotEmpty()) {
            $names = $lowDaily->take(5)->map(fn ($r) => $r['employee']->name)->implode('، ');
            $alerts[] = [
                'level' => 'critical',
                'icon' => 'fa-clipboard-list',
                'title' => 'عدم التزام بالتقارير اليومية',
                'message' => $lowDaily->count().' موظف/ين أقل من 70% التزاماً بتسليم التقرير اليومي. أبرزهم: '.$names.'.',
                'employees' => $lowDaily->pluck('employee')->values()->all(),
            ];
        }

        $lowCompletion = collect($rows)->filter(fn ($r) => $r['tasks_assigned_in_month'] >= 3
            && $r['tasks_completion_rate_pct'] !== null
            && $r['tasks_completion_rate_pct'] < 50);
        if ($lowCompletion->isNotEmpty()) {
            $alerts[] = [
                'level' => 'warning',
                'icon' => 'fa-tasks',
                'title' => 'إنجاز منخفض للمهام المسندة',
                'message' => $lowCompletion->count().' موظف/ين أكملوا أقل من نصف مهامهم المسندة هذا الشهر.',
            ];
        }

        $idle = collect($rows)->filter(fn ($r) => $r['tasks_assigned_in_month'] === 0
            && $r['tasks_completed_in_month'] === 0
            && $r['design_cycles_as_designer'] === 0
            && $r['design_cycles_created_as_moderator'] === 0
            && ($r['daily_report_required'] ?? 0) > 0);
        if ($idle->count() >= 2) {
            $alerts[] = [
                'level' => 'info',
                'icon' => 'fa-user-clock',
                'title' => 'موظفون بلا نشاط مسجّل',
                'message' => $idle->count().' موظف/ين بدون مهام أو دورات تصميم في الشهر — تحقق من التوزيع أو الإجازات.',
            ];
        }

        if (($summary['tasks_completed'] ?? 0) > 0 && ($summary['tasks_on_time_rate_pct'] ?? 0) >= 85) {
            $alerts[] = [
                'level' => 'success',
                'icon' => 'fa-thumbs-up',
                'title' => 'التزام جيد بالمواعيد',
                'message' => 'نسبة الإنجاز في الموعد '.round($summary['tasks_on_time_rate_pct'], 1).'% — استمر في دعم الفريق.',
            ];
        }

        if ($alerts === []) {
            $alerts[] = [
                'level' => 'info',
                'icon' => 'fa-info-circle',
                'title' => 'لا تنبيهات حرجة',
                'message' => 'الأداء ضمن النطاق المتوقع لهذا الشهر. راجع الجداول والمخططات للتفاصيل.',
            ];
        }

        usort($alerts, fn ($a, $b) => $this->alertPriority($a['level']) <=> $this->alertPriority($b['level']));

        return $alerts;
    }

    private function alertPriority(string $level): int
    {
        return match ($level) {
            'critical' => 0,
            'warning' => 1,
            'info' => 2,
            'success' => 3,
            default => 4,
        };
    }

    /**
     * @param  array<string, mixed>  $row
     * @return list<string>
     */
    private function riskFlags(array $row): array
    {
        $flags = [];

        if ($row['open_overdue_tasks_end_of_month'] >= 2) {
            $flags[] = 'مهام متأخرة مفتوحة ('.$row['open_overdue_tasks_end_of_month'].')';
        }
        if ($row['tasks_on_time_rate_pct'] !== null && $row['tasks_on_time_rate_pct'] < 60 && ($row['tasks_on_time'] + $row['tasks_late']) >= 2) {
            $flags[] = 'تأخر متكرر في المهام';
        }
        if ($row['tasks_completion_rate_pct'] !== null && $row['tasks_completion_rate_pct'] < 50 && $row['tasks_assigned_in_month'] >= 2) {
            $flags[] = 'إنجاز منخفض ('.$row['tasks_completion_rate_pct'].'%)';
        }
        if ($row['designer_on_time_rate_pct'] !== null && $row['designer_on_time_rate_pct'] < 60 && $row['designer_submissions_in_month'] >= 1) {
            $flags[] = 'تأخر تسليم تصميم';
        }
        if ($row['daily_report_rate_pct'] !== null && $row['daily_report_required'] >= 5 && $row['daily_report_rate_pct'] < 70) {
            $flags[] = 'تقارير يومية ناقصة ('.$row['daily_report_rate_pct'].'%)';
        }
        if ($row['design_cycles_cancelled_as_moderator'] >= 2) {
            $flags[] = 'دورات ملغاة كمشرف';
        }

        return $flags;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function healthScore(array $row): ?float
    {
        $parts = [];
        $weights = [];

        if ($row['tasks_assigned_in_month'] > 0 && $row['tasks_completion_rate_pct'] !== null) {
            $parts[] = $row['tasks_completion_rate_pct'];
            $weights[] = 25;
        }
        if (($row['tasks_on_time'] + $row['tasks_late']) > 0 && $row['tasks_on_time_rate_pct'] !== null) {
            $parts[] = $row['tasks_on_time_rate_pct'];
            $weights[] = 30;
        }
        if ($row['designer_submissions_in_month'] > 0 && $row['designer_on_time_rate_pct'] !== null) {
            $parts[] = $row['designer_on_time_rate_pct'];
            $weights[] = 20;
        }
        if (($row['daily_report_required'] ?? 0) >= 3 && $row['daily_report_rate_pct'] !== null) {
            $parts[] = $row['daily_report_rate_pct'];
            $weights[] = 25;
        }

        if ($parts === []) {
            return null;
        }

        $wSum = array_sum($weights);
        $score = 0.0;
        foreach ($parts as $i => $p) {
            $score += $p * ($weights[$i] / $wSum);
        }

        $penalty = min(25, $row['open_overdue_tasks_end_of_month'] * 5);

        return max(0, min(100, round($score - $penalty, 1)));
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    private function teamHealthScore(array $rows): ?float
    {
        $scores = array_values(array_filter(array_map(fn ($r) => $r['health_score'] ?? null, $rows), fn ($s) => $s !== null));

        return $scores !== [] ? round(array_sum($scores) / count($scores), 1) : null;
    }

    /**
     * @param  array<string, mixed>  $current
     * @param  array<string, mixed>  $previous
     * @param  list<array<string, mixed>>  $rows
     * @param  list<array{employee: \App\Models\User, required: int, submitted: int, rate: float|null}>  $dailyCompliance
     * @return array<string, mixed>
     */
    private function buildCharts(array $current, array $previous, array $rows, array $dailyCompliance, Carbon $start, Carbon $end): array
    {
        $summary = $current['summary'];
        $prevSummary = $previous['summary'];

        $weekLabels = [];
        $weekCompleted = [];
        $weeks = $this->weekBuckets($start, $end);
        foreach ($weeks as $w) {
            $weekLabels[] = $w['label'];
            $weekCompleted[] = 0;
        }

        /** @var Collection<int, \App\Models\EmployeeTask> $completedTasks */
        $completedTasks = $current['completed_tasks'] ?? collect();
        foreach ($completedTasks as $t) {
            if (! $t->completed_at) {
                continue;
            }
            $idx = $this->weekIndex($t->completed_at, $weeks);
            if ($idx !== null) {
                $weekCompleted[$idx]++;
            }
        }

        $designStatus = [
            DesignTaskCycle::STATUS_PENDING_DESIGN => 0,
            DesignTaskCycle::STATUS_DESIGN_IN_PROGRESS => 0,
            DesignTaskCycle::STATUS_DESIGN_SUBMITTED => 0,
            DesignTaskCycle::STATUS_MODERATOR_DELIVERY_PENDING => 0,
            DesignTaskCycle::STATUS_COMPLETED => 0,
            DesignTaskCycle::STATUS_CANCELLED => 0,
        ];
        foreach ($current['design_cycles'] ?? [] as $c) {
            if (isset($designStatus[$c->status])) {
                $designStatus[$c->status]++;
            }
        }

        $sortedByHealth = $rows;
        usort($sortedByHealth, fn ($a, $b) => ($a['health_score'] ?? 0) <=> ($b['health_score'] ?? 0));
        $worst = array_slice(array_filter($sortedByHealth, fn ($r) => $r['health_score'] !== null), 0, 8);

        $dailyLabels = [];
        $dailyRates = [];
        foreach (collect($dailyCompliance)->filter(fn ($r) => $r['required'] > 0)->sortBy('rate')->take(10) as $d) {
            $dailyLabels[] = $d['employee']->name;
            $dailyRates[] = $d['rate'] ?? 0;
        }

        $typeTotals = ['design' => 0, 'video_editing' => 0, 'sales' => 0, 'other' => 0];
        foreach ($rows as $r) {
            $typeTotals['design'] += $r['tasks_completed_design'];
            $typeTotals['video_editing'] += $r['tasks_completed_video'];
            $typeTotals['sales'] += $r['tasks_completed_sales'];
            $typeTotals['other'] += $r['tasks_completed_other'];
        }

        $withAssigned = collect($rows)->filter(fn ($r) => $r['tasks_assigned_in_month'] > 0)
            ->sortByDesc('tasks_assigned_in_month')
            ->take(10);

        $designLabels = [];
        $designData = [];
        foreach ($designStatus as $status => $count) {
            if ($count > 0) {
                $designLabels[] = DesignTaskCycle::statusLabel($status);
                $designData[] = $count;
            }
        }

        return [
            'week_labels' => $weekLabels,
            'week_completed' => $weekCompleted,
            'on_time_late' => [
                'labels' => ['في الموعد', 'متأخر'],
                'data' => [$summary['tasks_on_time'] ?? 0, $summary['tasks_late'] ?? 0],
            ],
            'designer_on_time_late' => [
                'labels' => ['في الموعد', 'متأخر'],
                'data' => [$summary['designer_on_time'] ?? 0, $summary['designer_late'] ?? 0],
            ],
            'month_compare' => [
                'labels' => ['مهام مكتملة', 'تسليمات', 'دورات نشطة', 'متأخرة مفتوحة'],
                'current' => [
                    $summary['tasks_completed'] ?? 0,
                    $summary['deliverables'] ?? 0,
                    $summary['design_cycles_touched_month'] ?? 0,
                    $summary['open_overdue_tasks'] ?? 0,
                ],
                'previous' => [
                    $prevSummary['tasks_completed'] ?? 0,
                    $prevSummary['deliverables'] ?? 0,
                    $prevSummary['design_cycles_touched_month'] ?? 0,
                    $prevSummary['open_overdue_tasks'] ?? 0,
                ],
            ],
            'design_cycle_status' => [
                'labels' => $designLabels,
                'data' => $designData,
            ],
            'task_types' => [
                'labels' => ['تصميم', 'مونتاج', 'مبيعات', 'أخرى'],
                'data' => array_values($typeTotals),
            ],
            'health_worst' => [
                'labels' => array_map(fn ($r) => $r['user']->name, $worst),
                'data' => array_map(fn ($r) => $r['health_score'], $worst),
            ],
            'daily_compliance' => [
                'labels' => $dailyLabels,
                'data' => $dailyRates,
            ],
            'completion_rates' => [
                'labels' => $withAssigned->map(fn ($r) => $r['user']->name)->values()->all(),
                'data' => $withAssigned->map(fn ($r) => $r['tasks_completion_rate_pct'] ?? 0)->values()->all(),
            ],
        ];
    }

    /**
     * @return list<array{start: Carbon, end: Carbon, label: string}>
     */
    private function weekBuckets(Carbon $start, Carbon $end): array
    {
        $buckets = [];
        $cursor = $start->copy()->startOfDay();
        $i = 1;
        while ($cursor->lte($end)) {
            $wEnd = $cursor->copy()->addDays(6)->endOfDay();
            if ($wEnd->gt($end)) {
                $wEnd = $end->copy()->endOfDay();
            }
            $buckets[] = [
                'start' => $cursor->copy(),
                'end' => $wEnd,
                'label' => 'أسبوع '.$i,
            ];
            $cursor->addDays(7);
            $i++;
        }

        return $buckets;
    }

    /**
     * @param  list<array{start: Carbon, end: Carbon, label: string}>  $weeks
     */
    private function weekIndex(Carbon $date, array $weeks): ?int
    {
        foreach ($weeks as $i => $w) {
            if ($date->between($w['start'], $w['end'])) {
                return $i;
            }
        }

        return null;
    }

    private function deltaPct(float|int $current, float|int $previous): ?float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function deltaPoints(?float $current, ?float $previous): ?float
    {
        if ($current === null || $previous === null) {
            return null;
        }

        return round($current - $previous, 1);
    }
}
