<?php

namespace App\Services;

use App\Models\SalesActivity;
use App\Models\SalesKpiTarget;
use App\Models\SalesLead;
use App\Models\User;
use Carbon\Carbon;

class SalesKpiService
{
    /**
     * دمج الأهداف الافتراضية مع صف الشهر للموظف.
     *
     * @return array<string, mixed>
     */
    public function mergedTargets(User $rep, Carbon $month): array
    {
        $defaults = config('sales_kpi.defaults', []);
        $key = SalesKpiTarget::yearMonthKey($month);
        $row = SalesKpiTarget::where('user_id', $rep->id)->where('year_month', $key)->first();
        $custom = $row?->targets ?? [];

        return array_merge($defaults, is_array($custom) ? $custom : []);
    }

    /**
     * @return array<string, mixed>
     */
    public function buildReport(User $rep, ?Carbon $reference = null): array
    {
        $ref = ($reference ?? Carbon::now())->copy();

        $today = $ref->copy()->startOfDay();
        $todayEnd = $ref->copy()->endOfDay();
        $weekStart = $ref->copy()->startOfWeek();
        $weekEnd = $ref->copy()->endOfWeek();
        $monthStart = $ref->copy()->startOfMonth();
        $monthEnd = $ref->copy()->endOfMonth();

        $targets = $this->mergedTargets($rep, $monthStart);

        $d = $this->metricsBucket($rep->id, $today, $todayEnd);
        $w = $this->metricsBucket($rep->id, $weekStart, $weekEnd);
        $m = $this->metricsBucket($rep->id, $monthStart, $monthEnd);

        $monthlyScores = $this->scoreMonthlyPillars($rep->id, $monthStart, $monthEnd, $targets);
        $weights = config('sales_kpi.weights', []);

        $weightedTotal = 0.0;
        foreach ($weights as $k => $wgt) {
            $weightedTotal += ($monthlyScores['pillars'][$k]['score'] ?? 0) * (float) $wgt;
        }

        return [
            'reference' => $ref,
            'targets' => $targets,
            'day' => array_merge($d, $this->scoreDay($d, $targets)),
            'week' => array_merge($w, $this->scoreWeek($w, $targets)),
            'month' => array_merge($m, [
                'pillars' => $monthlyScores['pillars'],
                'kpi_lines' => $monthlyScores['lines'],
            ]),
            'composite_month' => round($weightedTotal, 1),
            'alert_flags' => $this->alertFlags($monthlyScores['pillars'], $weightedTotal, $m),
        ];
    }

    /**
     * @return list<array{user: User, composite: float, month_revenue: float, month_won: int, open_pipeline: int, flags: list<string>, pillars: array, overdue_followups: int, stale_open_leads: int, avg_response_minutes: ?float}>
     */
    public function adminOverview(): array
    {
        $reps = User::salesEmployees()->where('is_active', true)->orderBy('name')->get();
        $ref = Carbon::now();
        $rows = [];
        foreach ($reps as $rep) {
            $report = $this->buildReport($rep, $ref);
            $rows[] = [
                'user' => $rep,
                'composite' => $report['composite_month'],
                'month_revenue' => $report['month']['revenue_closed'],
                'month_won' => $report['month']['won_closed'],
                'open_pipeline' => $report['month']['open_opportunities'],
                'flags' => $report['alert_flags'],
                'pillars' => $report['month']['pillars'] ?? [],
                'overdue_followups' => (int) ($report['month']['overdue_followups'] ?? 0),
                'stale_open_leads' => (int) ($report['month']['stale_open_leads'] ?? 0),
                'avg_response_minutes' => $report['month']['avg_response_minutes'] ?? null,
            ];
        }

        return $rows;
    }

    /**
     * @return array<string, mixed>
     */
    private function metricsBucket(int $userId, Carbon $start, Carbon $end): array
    {
        $base = SalesLead::query()->forAssignee($userId);

        $newLeads = (clone $base)->whereBetween('created_at', [$start, $end])->count();

        $wonClosed = (clone $base)->where('stage', 'won')
            ->whereNotNull('closed_at')
            ->whereBetween('closed_at', [$start, $end])
            ->count();

        $lostClosed = (clone $base)->where('stage', 'lost')
            ->whereNotNull('closed_at')
            ->whereBetween('closed_at', [$start, $end])
            ->count();

        $revenue = (float) (clone $base)->where('stage', 'won')
            ->whereNotNull('closed_at')
            ->whereBetween('closed_at', [$start, $end])
            ->sum('expected_value');

        $conversionPct = $newLeads > 0 ? round($wonClosed / $newLeads * 100, 1) : null;

        $avgDeal = $wonClosed > 0 ? round($revenue / $wonClosed, 2) : null;

        $activityQ = SalesActivity::query()
            ->where('user_id', $userId)
            ->whereHas('lead', fn ($q) => $q->where('assigned_to', $userId))
            ->whereBetween('created_at', [$start, $end]);

        $calls = (clone $activityQ)->where('type', 'call')->count();
        $meetings = (clone $activityQ)->where('type', 'meeting')->count();
        $followups = (clone $activityQ)->where('type', 'follow_up')->count();

        $openOpportunities = (clone $base)->openPipeline()->count();
        $pipelineValue = (float) (clone $base)->openPipeline()->whereNotNull('expected_value')->sum('expected_value');

        $closingDen = $wonClosed + $lostClosed;
        $closingRatioPct = $closingDen > 0 ? round($wonClosed / $closingDen * 100, 1) : null;
        $lossRatioPct = $closingDen > 0 ? round($lostClosed / $closingDen * 100, 1) : null;

        $csatAvg = (clone $base)->where('stage', 'won')
            ->whereNotNull('csat_rating')
            ->whereNotNull('closed_at')
            ->whereBetween('closed_at', [$start, $end])
            ->avg('csat_rating');

        $csatAvg = $csatAvg !== null ? round((float) $csatAvg, 2) : null;

        $wonLeadsPeriod = (clone $base)->where('stage', 'won')
            ->whereNotNull('closed_at')
            ->whereBetween('closed_at', [$start, $end])
            ->get(['created_at', 'closed_at']);

        $salesCycleAvg = null;
        if ($wonLeadsPeriod->isNotEmpty()) {
            $totalDays = 0;
            foreach ($wonLeadsPeriod as $wl) {
                $totalDays += $wl->created_at->diffInDays($wl->closed_at);
            }
            $salesCycleAvg = round($totalDays / $wonLeadsPeriod->count(), 1);
        }

        $avgResponseMinutes = $this->averageFirstResponseMinutes($userId, $start, $end);

        $daysInRange = max(1, (int) $start->diffInDays($end) + 1);
        $totalActivities = (clone $activityQ)->count();
        $crmActivitiesDailyAvg = round($totalActivities / $daysInRange, 2);

        $openLeads = (clone $base)->openPipeline()->get(['id', 'updated_at']);
        $freshOpen = $openLeads->filter(fn ($l) => $l->updated_at && $l->updated_at->gte(now()->subDays(7)))->count();
        $dataFreshPct = $openLeads->count() > 0 ? round($freshOpen / $openLeads->count() * 100, 1) : 100.0;

        $distinctDays = SalesActivity::query()
            ->where('user_id', $userId)
            ->whereHas('lead', fn ($q) => $q->where('assigned_to', $userId))
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as d')
            ->distinct()
            ->pluck('d')
            ->count();

        $workingDaysApprox = max(1, min($daysInRange, 31));
        $engagementPct = min(100.0, round($distinctDays / $workingDaysApprox * 100, 1));

        $staleCount = $this->staleOpenCount($userId);
        $overdueFollowups = $this->overdueFollowupCount($userId);

        return [
            'new_leads' => $newLeads,
            'won_closed' => $wonClosed,
            'lost_closed' => $lostClosed,
            'revenue_closed' => $revenue,
            'conversion_pct' => $conversionPct,
            'avg_deal_size' => $avgDeal,
            'calls' => $calls,
            'meetings' => $meetings,
            'followups' => $followups,
            'open_opportunities' => $openOpportunities,
            'pipeline_value' => $pipelineValue,
            'closing_ratio_pct' => $closingRatioPct,
            'loss_ratio_pct' => $lossRatioPct,
            'csat_avg' => $csatAvg,
            'sales_cycle_avg_days' => $salesCycleAvg,
            'avg_response_minutes' => $avgResponseMinutes,
            'crm_activities_daily_avg' => $crmActivitiesDailyAvg,
            'data_fresh_open_pct' => $dataFreshPct,
            'engagement_days_pct' => $engagementPct,
            'total_activities' => $totalActivities,
            'stale_open_leads' => $staleCount,
            'overdue_followups' => $overdueFollowups,
        ];
    }

    private function staleOpenCount(int $userId): int
    {
        $d = SalesLead::STALE_CONTACT_DAYS;
        $base = SalesLead::query()->forAssignee($userId)->openPipeline();

        return (clone $base)->where(function ($q) use ($d) {
            $q->where(function ($q2) use ($d) {
                $q2->whereNull('last_contacted_at')
                    ->where('created_at', '<', now()->subDays($d));
            })->orWhere('last_contacted_at', '<', now()->subDays($d));
        })->count();
    }

    private function overdueFollowupCount(int $userId): int
    {
        return SalesLead::query()->forAssignee($userId)->openPipeline()
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<', now())
            ->count();
    }

    private function averageFirstResponseMinutes(int $userId, Carbon $start, Carbon $end): ?float
    {
        $leads = SalesLead::query()
            ->forAssignee($userId)
            ->whereBetween('created_at', [$start, $end])
            ->with(['activities' => fn ($q) => $q->where('user_id', $userId)->orderBy('created_at')])
            ->get(['id', 'created_at']);

        $diffs = [];
        foreach ($leads as $lead) {
            $first = $lead->activities->first();
            if ($first) {
                $diffs[] = $lead->created_at->diffInMinutes($first->created_at);
            }
        }

        if ($diffs === []) {
            return null;
        }

        return round(array_sum($diffs) / count($diffs), 1);
    }

    private function achievementUp(float $actual, float $target): float
    {
        if ($target <= 0) {
            return $actual > 0 ? 100.0 : 50.0;
        }

        return min(100.0, max(0.0, $actual / $target * 100));
    }

    private function achievementDown(float $actual, float $targetMax): float
    {
        if ($targetMax <= 0) {
            return 100.0;
        }
        if ($actual <= 0) {
            return 100.0;
        }

        return min(100.0, max(0.0, $targetMax / max($actual, 0.01) * 100));
    }

    /**
     * @param  array<string, mixed>  $m
     * @param  array<string, mixed>  $t
     * @return array<string, mixed>
     */
    private function scoreDay(array $m, array $t): array
    {
        return [
            'scores' => [
                'leads' => round($this->achievementUp((float) $m['new_leads'], (float) $t['leads_daily']), 1),
                'calls' => round($this->achievementUp((float) $m['calls'], (float) $t['calls_daily']), 1),
                'meetings' => round($this->achievementUp((float) $m['meetings'], (float) $t['meetings_daily']), 1),
                'followups' => round($this->achievementUp((float) $m['followups'], (float) $t['followups_daily']), 1),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $m
     * @param  array<string, mixed>  $t
     * @return array<string, mixed>
     */
    private function scoreWeek(array $m, array $t): array
    {
        $dealsTarget = (float) $t['deals_weekly'];
        $leadsTarget = (float) $t['leads_weekly'];
        $revSlice = max(1.0, (float) $t['revenue_monthly'] / 4.33);

        return [
            'scores' => [
                'leads' => round($this->achievementUp((float) $m['new_leads'], $leadsTarget), 1),
                'deals' => round($this->achievementUp((float) $m['won_closed'], $dealsTarget), 1),
                'revenue' => round($this->achievementUp((float) $m['revenue_closed'], $revSlice), 1),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $t
     * @return array{pillars: array<string, array{score: float, label: string}>, lines: array<string, array<string, mixed>>}
     */
    private function scoreMonthlyPillars(int $userId, Carbon $monthStart, Carbon $monthEnd, array $t): array
    {
        $m = $this->metricsBucket($userId, $monthStart, $monthEnd);

        $dealsMonthlyTarget = (float) $t['deals_weekly'] * 4.33;
        $leadsMonthlyTarget = (float) $t['leads_weekly'] * 4.33;
        $dim = (int) $monthStart->daysInMonth;
        $activityDaysFactor = max(1, (int) round($dim * 22 / 30));

        $lines = [
            'new_leads_month' => ['label' => 'Leads جديدة (الشهر)', 'actual' => $m['new_leads'], 'target' => round($leadsMonthlyTarget, 0)],
            'revenue' => ['label' => 'إيرادات (قيمة متوقعة للصفقات المكتملة)', 'actual' => $m['revenue_closed'], 'target' => $t['revenue_monthly']],
            'won_deals' => ['label' => 'صفقات مغلقة (فوز)', 'actual' => $m['won_closed'], 'target' => round($dealsMonthlyTarget, 0)],
            'avg_deal' => ['label' => 'متوسط قيمة الصفقة', 'actual' => $m['avg_deal_size'], 'target' => null],
            'calls_month' => ['label' => 'مكالمات', 'actual' => $m['calls'], 'target' => round((float) $t['calls_daily'] * $activityDaysFactor, 0)],
            'meetings_month' => ['label' => 'اجتماعات / ديمو', 'actual' => $m['meetings'], 'target' => round((float) $t['meetings_daily'] * $activityDaysFactor, 0)],
            'followups_month' => ['label' => 'متابعات مسجّلة', 'actual' => $m['followups'], 'target' => round((float) $t['followups_daily'] * $activityDaysFactor, 0)],
            'conversion' => ['label' => 'نسبة التحويل %', 'actual' => $m['conversion_pct'], 'target' => $t['conversion_pct_target']],
            'response' => ['label' => 'متوسط أول رد (دقيقة)', 'actual' => $m['avg_response_minutes'], 'target' => $t['response_minutes_max']],
            'closing' => ['label' => 'نسبة الإغلاق won/(won+lost)', 'actual' => $m['closing_ratio_pct'], 'target' => $t['closing_ratio_pct_min']],
            'csat' => ['label' => 'متوسط رضا العملاء (CSAT)', 'actual' => $m['csat_avg'], 'target' => $t['csat_min']],
            'loss_ratio' => ['label' => 'نسبة خسارة الصفقات lost/(won+lost)', 'actual' => $m['loss_ratio_pct'], 'target' => $t['loss_ratio_max_pct']],
            'open_pipe' => ['label' => 'فرص مفتوحة (الأنبوب)', 'actual' => $m['open_opportunities'], 'target' => $t['open_opportunities_min']],
            'cycle' => ['label' => 'متوسط دورة البيع (يوم)', 'actual' => $m['sales_cycle_avg_days'], 'target' => $t['sales_cycle_max_days']],
            'crm_daily' => ['label' => 'متوسط أنشطة CRM يومية', 'actual' => $m['crm_activities_daily_avg'], 'target' => $t['crm_activities_daily_min']],
            'data_fresh' => ['label' => 'حداثة بيانات الفرص المفتوحة %', 'actual' => $m['data_fresh_open_pct'], 'target' => $t['data_fresh_open_pct_min']],
            'engagement' => ['label' => 'أيام بتفاعل مسجّل %', 'actual' => $m['engagement_days_pct'], 'target' => $t['engagement_days_pct_min']],
        ];

        $results = $this->meanScores([
            $this->achievementUp((float) $m['revenue_closed'], (float) $t['revenue_monthly']),
            $this->achievementUp((float) $m['won_closed'], max(1.0, $dealsMonthlyTarget)),
            $this->achievementUp((float) $m['new_leads'], max(1.0, $leadsMonthlyTarget)),
            $m['conversion_pct'] !== null
                ? $this->achievementUp((float) $m['conversion_pct'], (float) $t['conversion_pct_target'])
                : 50.0,
        ]);

        $activity = $this->meanScores([
            $this->achievementUp((float) $m['calls'], max(1.0, (float) $t['calls_daily'] * $activityDaysFactor)),
            $this->achievementUp((float) $m['meetings'], max(1.0, (float) $t['meetings_daily'] * $activityDaysFactor)),
            $this->achievementUp((float) $m['followups'], max(1.0, (float) $t['followups_daily'] * $activityDaysFactor)),
        ]);

        $qualityCore = $this->meanScores([
            $m['closing_ratio_pct'] !== null
                ? $this->achievementUp((float) $m['closing_ratio_pct'], (float) $t['closing_ratio_pct_min'])
                : 60.0,
            $m['csat_avg'] !== null
                ? $this->achievementUp((float) $m['csat_avg'], (float) $t['csat_min'])
                : 70.0,
            $m['loss_ratio_pct'] !== null
                ? $this->achievementDown((float) $m['loss_ratio_pct'], (float) $t['loss_ratio_max_pct'])
                : 70.0,
        ]);

        $quality = $this->meanScores([
            $qualityCore,
            $this->achievementUp((float) $m['open_opportunities'], (float) $t['open_opportunities_min']),
            $m['sales_cycle_avg_days'] !== null
                ? $this->achievementDown((float) $m['sales_cycle_avg_days'], (float) $t['sales_cycle_max_days'])
                : 75.0,
            $m['avg_response_minutes'] !== null
                ? $this->achievementDown((float) $m['avg_response_minutes'], (float) $t['response_minutes_max'])
                : 80.0,
        ]);

        $discipline = $this->meanScores([
            $this->achievementUp((float) $m['crm_activities_daily_avg'], (float) $t['crm_activities_daily_min']),
            $this->achievementUp((float) $m['data_fresh_open_pct'], (float) $t['data_fresh_open_pct_min']),
            $this->achievementUp((float) $m['engagement_days_pct'], (float) $t['engagement_days_pct_min']),
        ]);

        $pillars = [
            'results' => ['score' => round($results, 1), 'label' => 'النتائج 40٪ — إيراد، صفقات، Leads، تحويل'],
            'activity' => ['score' => round($activity, 1), 'label' => 'النشاط 30٪ — مكالمات، اجتماعات، متابعات'],
            'quality' => ['score' => round($quality, 1), 'label' => 'الجودة 20٪ — إغلاق، رضا، أنبوب، دورة، سرعة رد'],
            'discipline' => ['score' => round($discipline, 1), 'label' => 'الالتزام 10٪ — CRM، تحديث بيانات، تفاعل يومي'],
        ];

        return compact('pillars', 'lines');
    }

    /**
     * @param  list<float|int>  $vals
     */
    private function meanScores(array $vals): float
    {
        $vals = array_values(array_filter($vals, fn ($v) => $v !== null && is_numeric($v)));
        if ($vals === []) {
            return 50.0;
        }

        return (float) (array_sum($vals) / count($vals));
    }

    /**
     * @param  array<string, array{score: float, label: string}>  $pillars
     * @return list<string>
     */
    /**
     * @param  array<string, array{score: float, label: string}>  $pillars
     * @param  array<string, mixed>  $monthMetrics
     * @return list<string>
     */
    private function alertFlags(array $pillars, float $composite, array $monthMetrics): array
    {
        $alerts = config('sales_kpi.alerts', []);
        $flags = [];

        if ($composite < (float) ($alerts['composite_critical'] ?? 45)) {
            $flags[] = 'تنبيه حرج: المؤشر المركّب للشهر أقل من '.($alerts['composite_critical'] ?? 45);
        } elseif ($composite < (float) ($alerts['composite_warning'] ?? 65)) {
            $flags[] = 'تنبيه: المؤشر المركّب دون المستوى المطلوب';
        }

        foreach ($pillars as $key => $p) {
            if (($p['score'] ?? 100) < 50) {
                $flags[] = 'عمود «'.$key.'» ضعيف ('.($p['score'] ?? 0).'/100)';
            }
        }

        if (($monthMetrics['stale_open_leads'] ?? 0) >= (int) ($alerts['stale_leads_per_rep'] ?? 5)) {
            $flags[] = 'مراقبة: '.$monthMetrics['stale_open_leads'].' عميل مفتوح بلا تواصل كافٍ';
        }
        if (($monthMetrics['overdue_followups'] ?? 0) >= (int) ($alerts['overdue_followups'] ?? 3)) {
            $flags[] = 'مراقبة: '.$monthMetrics['overdue_followups'].' متابعة متأخرة في الأنبوب';
        }

        return $flags;
    }
}
