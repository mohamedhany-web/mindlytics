<?php

namespace App\Services;

use App\Models\OfflineCourseEnrollment;
use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SalesInsightsAnalyticsService
{
    public function __construct(
        private readonly SalesKpiService $kpi
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function buildTeamDashboard(int $months = 6): array
    {
        $monthly = $this->monthlyTeamTrend($months);
        $direction = $this->directionSummary($monthly);
        $funnel = $this->pipelineFunnel();
        $sources = $this->sourceBreakdown(now()->startOfMonth(), now()->endOfMonth());
        $repComparison = $this->repComparisonChart();

        return [
            'monthly' => $monthly,
            'direction' => $direction,
            'funnel' => $funnel,
            'sources' => $sources,
            'rep_comparison' => $repComparison,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function buildRepCharts(int $userId, Carbon $start, Carbon $end, array $periodReport): array
    {
        $daily = $this->dailyRepSeries($userId, $start, $end);
        $stages = $this->repStageDistribution($userId);
        $pillars = $this->pillarChartData($periodReport);
        $activitiesByType = $this->activitiesByType($userId, $start, $end);

        return [
            'daily' => $daily,
            'stages' => $stages,
            'pillars' => $pillars,
            'activities_by_type' => $activitiesByType,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function monthlyTeamTrend(int $months): array
    {
        $labels = [];
        $created = [];
        $won = [];
        $lost = [];
        $revenue = [];
        $conversion = [];
        $closingRatio = [];
        $academyConverted = [];
        $academyRate = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $start = now()->subMonths($i)->startOfMonth();
            $end = now()->subMonths($i)->endOfMonth();

            $labels[] = $start->locale('ar')->isoFormat('MMM YYYY');

            $c = (int) SalesLead::query()->whereBetween('created_at', [$start, $end])->count();
            $w = (int) SalesLead::query()
                ->where('stage', SalesLead::WON_STAGE)
                ->whereNotNull('closed_at')
                ->whereBetween('closed_at', [$start, $end])
                ->count();
            $l = (int) SalesLead::query()
                ->where('stage', 'lost')
                ->whereNotNull('closed_at')
                ->whereBetween('closed_at', [$start, $end])
                ->count();
            $rev = (float) SalesLead::query()
                ->where('stage', SalesLead::WON_STAGE)
                ->whereNotNull('closed_at')
                ->whereBetween('closed_at', [$start, $end])
                ->sum('expected_value');

            $academy = $this->academyConversionsFromWonLeads($start, $end);

            $created[] = $c;
            $won[] = $w;
            $lost[] = $l;
            $revenue[] = round($rev, 2);
            $conversion[] = $c > 0 ? round($w / $c * 100, 1) : 0;
            $closingRatio[] = ($w + $l) > 0 ? round($w / ($w + $l) * 100, 1) : 0;
            $academyConverted[] = $academy;
            $academyRate[] = $w > 0 ? round($academy / $w * 100, 1) : 0;
        }

        return compact(
            'labels',
            'created',
            'won',
            'lost',
            'revenue',
            'conversion',
            'closingRatio',
            'academyConverted',
            'academyRate'
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
                'label' => 'بيانات غير كافية للاتجاه',
                'summary' => 'يحتاج شهرين على الأقل لقياس الاتجاه.',
                'metrics' => [],
            ];
        }

        $idx = $n - 1;
        $prev = $n - 2;

        $metrics = [
            'leads' => [
                'label' => 'Leads جديدة',
                'current' => $monthly['created'][$idx] ?? 0,
                'previous' => $monthly['created'][$prev] ?? 0,
                'pct' => $this->pctDelta($monthly['created'][$prev] ?? 0, $monthly['created'][$idx] ?? 0),
            ],
            'won' => [
                'label' => 'صفقات فوز',
                'current' => $monthly['won'][$idx] ?? 0,
                'previous' => $monthly['won'][$prev] ?? 0,
                'pct' => $this->pctDelta($monthly['won'][$prev] ?? 0, $monthly['won'][$idx] ?? 0),
            ],
            'revenue' => [
                'label' => 'إيراد المبيعات',
                'current' => $monthly['revenue'][$idx] ?? 0,
                'previous' => $monthly['revenue'][$prev] ?? 0,
                'pct' => $this->pctDelta($monthly['revenue'][$prev] ?? 0, $monthly['revenue'][$idx] ?? 0),
            ],
            'conversion' => [
                'label' => 'معدل التحويل',
                'current' => $monthly['conversion'][$idx] ?? 0,
                'previous' => $monthly['conversion'][$prev] ?? 0,
                'pct' => $this->pctDelta($monthly['conversion'][$prev] ?? 0, $monthly['conversion'][$idx] ?? 0, absolute: true),
            ],
            'academy' => [
                'label' => 'تحويل الأكاديمية',
                'current' => $monthly['academyRate'][$idx] ?? 0,
                'previous' => $monthly['academyRate'][$prev] ?? 0,
                'pct' => $this->pctDelta($monthly['academyRate'][$prev] ?? 0, $monthly['academyRate'][$idx] ?? 0, absolute: true),
            ],
        ];

        $positive = 0;
        foreach (['won', 'revenue', 'conversion', 'academy'] as $key) {
            $pct = $metrics[$key]['pct'] ?? null;
            if ($pct !== null && $pct > 0) {
                $positive++;
            }
        }

        $academyUp = ($metrics['academy']['pct'] ?? 0) > 0;
        $conversionUp = ($metrics['conversion']['pct'] ?? 0) > 0;
        $revenueUp = ($metrics['revenue']['pct'] ?? 0) > 0;

        if ($positive >= 3 && $academyUp) {
            $status = 'growth';
            $label = 'اتجاه نمو — المبيعات والأكاديمية تتحسّن';
            $summary = 'مؤشرات الفوز والإيراد وتحويل الأكاديمية في صعود. استمر على نفس الاستراتيجية وركّز على جودة المتابعة.';
        } elseif ($positive >= 2 || ($conversionUp && $revenueUp)) {
            $status = 'stable';
            $label = 'اتجاه مستقر — تحسّن جزئي';
            $summary = 'بعض المؤشرات تتحسّن. راجع المصادر ضعيفة الأداء وعزّز ربط الفوز بتسجيل الكورسات.';
        } elseif ($positive <= 1 && (($metrics['won']['pct'] ?? 0) < 0 || ($metrics['revenue']['pct'] ?? 0) < 0)) {
            $status = 'decline';
            $label = 'اتجاه تراجع — يحتاج تدخل';
            $summary = 'انخفاض في الفوز أو الإيراد. راجع SLA، أسباب الخسارة، واكتمال بيانات البريد لربط الأكاديمية.';
        } else {
            $status = 'neutral';
            $label = 'اتجاه محايد — مراقبة مستمرة';
            $summary = 'الأرقام متقاربة مع الشهر السابق. راقب تحويل الأكاديمية ومعدل الإغلاق.';
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
    private function pipelineFunnel(): array
    {
        $stages = [
            'new_lead',
            'first_contact',
            'no_answer',
            'connected',
            'qualification',
            'interested',
            'objection',
            'follow_up_scheduled',
            'offer_sent',
            'payment_pending',
        ];
        $labels = [];
        $values = [];

        foreach ($stages as $stage) {
            $labels[] = SalesLead::STAGES[$stage] ?? $stage;
            $values[] = (int) SalesLead::query()
                ->where('stage', $stage)
                ->count();
        }

        $labels[] = 'Enrollment (هذا الشهر)';
        $values[] = (int) SalesLead::query()
            ->where('stage', SalesLead::WON_STAGE)
            ->whereNotNull('closed_at')
            ->whereBetween('closed_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        return compact('labels', 'values');
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    private function sourceBreakdown(Carbon $start, Carbon $end): array
    {
        $rows = SalesLead::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('source, COUNT(*) as total')
            ->groupBy('source')
            ->orderByDesc('total')
            ->get();

        $labels = [];
        $values = [];

        foreach ($rows as $row) {
            $labels[] = SalesLead::SOURCES[$row->source] ?? (string) $row->source;
            $values[] = (int) $row->total;
        }

        return compact('labels', 'values');
    }

    /**
     * @return array{labels: list<string>, composite: list<float>, won: list<int>, revenue: list<float>}
     */
    private function repComparisonChart(): array
    {
        $rows = $this->kpi->adminOverview();

        return [
            'labels' => collect($rows)->pluck('user.name')->all(),
            'composite' => collect($rows)->pluck('composite')->map(fn ($v) => round((float) $v, 1))->all(),
            'won' => collect($rows)->pluck('month_won')->map(fn ($v) => (int) $v)->all(),
            'revenue' => collect($rows)->pluck('month_revenue')->map(fn ($v) => round((float) $v, 0))->all(),
        ];
    }

    /**
     * @return array{labels: list<string>, leads: list<int>, activities: list<int>, wins: list<int>}
     */
    private function dailyRepSeries(int $userId, Carbon $start, Carbon $end): array
    {
        $labels = [];
        $leads = [];
        $activities = [];
        $wins = [];

        $cursor = $start->copy()->startOfDay();
        $endDay = $end->copy()->startOfDay();

        while ($cursor->lte($endDay)) {
            $dayStart = $cursor->copy()->startOfDay();
            $dayEnd = $cursor->copy()->endOfDay();

            $labels[] = $cursor->format('m-d');

            $leads[] = (int) SalesLead::query()
                ->forAssignee($userId)
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->count();

            $activities[] = (int) SalesActivity::query()
                ->where('user_id', $userId)
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->count();

            $wins[] = (int) SalesLead::query()
                ->forAssignee($userId)
                ->where('stage', SalesLead::WON_STAGE)
                ->whereNotNull('closed_at')
                ->whereBetween('closed_at', [$dayStart, $dayEnd])
                ->count();

            $cursor->addDay();
        }

        return compact('labels', 'leads', 'activities', 'wins');
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    private function repStageDistribution(int $userId): array
    {
        $rows = SalesLead::query()
            ->forAssignee($userId)
            ->selectRaw('stage, COUNT(*) as total')
            ->groupBy('stage')
            ->get();

        $labels = [];
        $values = [];

        foreach ($rows as $row) {
            $labels[] = SalesLead::STAGES[$row->stage] ?? (string) $row->stage;
            $values[] = (int) $row->total;
        }

        return compact('labels', 'values');
    }

    /**
     * @param  array<string, mixed>  $periodReport
     * @return array{labels: list<string>, scores: list<float>, targets: list<float>}
     */
    private function pillarChartData(array $periodReport): array
    {
        $pillars = (array) ($periodReport['pillars'] ?? []);
        $labels = [];
        $scores = [];
        $targets = [];

        foreach ($pillars as $pillar) {
            if (! is_array($pillar)) {
                continue;
            }
            $labels[] = (string) ($pillar['label'] ?? '');
            $scores[] = round((float) ($pillar['score'] ?? 0), 1);
            $targets[] = 70.0;
        }

        return compact('labels', 'scores', 'targets');
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    private function activitiesByType(int $userId, Carbon $start, Carbon $end): array
    {
        $types = array_keys(\App\Models\SalesActivity::TYPES);
        $labels = [];
        $values = [];

        foreach ($types as $type) {
            $count = (int) SalesActivity::query()
                ->where('user_id', $userId)
                ->where('type', $type)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            if ($count > 0) {
                $labels[] = \App\Models\SalesActivity::typeLabel($type);
                $values[] = $count;
            }
        }

        return compact('labels', 'values');
    }

    private function academyConversionsFromWonLeads(Carbon $start, Carbon $end): int
    {
        $wonLeads = SalesLead::query()
            ->where('stage', SalesLead::WON_STAGE)
            ->whereBetween('closed_at', [$start, $end])
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get(['email', 'closed_at']);

        if ($wonLeads->isEmpty()) {
            return 0;
        }

        $emails = $wonLeads->pluck('email')
            ->map(fn ($e) => strtolower(trim((string) $e)))
            ->unique()
            ->values()
            ->all();

        /** @var Collection<string, int> $usersByEmail */
        $usersByEmail = User::query()
            ->whereIn('email', $emails)
            ->get(['id', 'email'])
            ->mapWithKeys(fn ($u) => [strtolower((string) $u->email) => (int) $u->id]);

        $converted = 0;

        foreach ($wonLeads as $lead) {
            $email = strtolower(trim((string) $lead->email));
            $userId = $usersByEmail[$email] ?? null;

            if (! $userId || ! $lead->closed_at) {
                continue;
            }

            $windowEnd = $lead->closed_at->copy()->addDays(90);

            $hasEnrollment = StudentCourseEnrollment::query()
                ->where('user_id', $userId)
                ->whereBetween('enrolled_at', [$lead->closed_at, $windowEnd])
                ->exists()
                || OfflineCourseEnrollment::query()
                    ->where('user_id', $userId)
                    ->whereBetween('enrolled_at', [$lead->closed_at, $windowEnd])
                    ->exists();

            if ($hasEnrollment) {
                $converted++;
            }
        }

        return $converted;
    }

    private function pctDelta(float $previous, float $current, bool $absolute = false): ?float
    {
        if ($absolute) {
            return round($current - $previous, 1);
        }

        if (abs($previous) < 0.0001) {
            return $current > 0 ? 100.0 : ($current < 0 ? -100.0 : 0.0);
        }

        return round((($current - $previous) / abs($previous)) * 100, 1);
    }
}
