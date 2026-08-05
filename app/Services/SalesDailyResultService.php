<?php

namespace App\Services;

use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

/**
 * KPI يومي قائم على النتائج (محاولات، ردود، مؤهل، جلسات، عروض، تسجيلات).
 */
class SalesDailyResultService
{
    /**
     * @return array<string, int|float>
     */
    public function defaultTargets(): array
    {
        $defaults = config('sales_kpi.defaults', []);

        return [
            'call_attempts_daily' => (int) ($defaults['call_attempts_daily'] ?? 120),
            'calls_answered_daily' => (int) ($defaults['calls_answered_daily'] ?? 35),
            'qualified_conversations_daily' => (int) ($defaults['qualified_conversations_daily'] ?? 15),
            'discovery_sessions_daily' => (int) ($defaults['discovery_sessions_daily'] ?? 8),
            'proposals_daily' => (int) ($defaults['proposals_daily'] ?? 5),
            'paid_enrollments_daily' => (int) ($defaults['paid_enrollments_daily'] ?? 2),
        ];
    }

    /**
     * @return array<string, int|float>
     */
    public function targetsFor(User $user, ?Carbon $day = null): array
    {
        $day = $day ?? today();
        $merged = app(SalesKpiService::class)->mergedTargets($user, $day->copy()->startOfMonth());
        $defaults = $this->defaultTargets();

        $out = [];
        foreach ($defaults as $key => $fallback) {
            $out[$key] = (float) ($merged[$key] ?? $fallback);
        }

        return $out;
    }

    /**
     * @return array{
     *   metrics: array<string, int>,
     *   targets: array<string, float>,
     *   lines: list<array{key:string,label:string,actual:int,target:float,pct:float,status:string}>,
     *   overall_pct: float,
     *   status: string,
     *   status_label: string
     * }
     */
    public function comparisonFor(User $user, ?Carbon $day = null): array
    {
        $day = ($day ?? today())->copy()->startOfDay();
        $targets = $this->targetsFor($user, $day);
        $metrics = $this->metricsFor($user->id, $day, $day->copy()->endOfDay());

        $labels = [
            'call_attempts_daily' => 'محاولات اتصال',
            'calls_answered_daily' => 'مكالمات تم الرد',
            'qualified_conversations_daily' => 'محادثات مؤهلة',
            'discovery_sessions_daily' => 'اجتماعات / جلسات',
            'proposals_daily' => 'عروض سعر',
            'paid_enrollments_daily' => 'تسجيلات مدفوعة',
        ];

        $lines = [];
        $pcts = [];
        foreach ($labels as $key => $label) {
            $actual = (int) ($metrics[$key] ?? 0);
            $target = max(0.0, (float) ($targets[$key] ?? 0));
            $pct = $target > 0 ? min(100.0, round($actual / $target * 100, 1)) : ($actual > 0 ? 100.0 : 0.0);
            $status = $pct >= 100 ? 'met' : ($pct >= 70 ? 'near' : 'behind');
            $lines[] = [
                'key' => $key,
                'label' => $label,
                'actual' => $actual,
                'target' => $target,
                'pct' => $pct,
                'status' => $status,
            ];
            $pcts[] = $pct;
        }

        $overall = count($pcts) ? round(array_sum($pcts) / count($pcts), 1) : 0.0;
        $status = $overall >= 100 ? 'met' : ($overall >= 70 ? 'near' : 'behind');

        return [
            'metrics' => $metrics,
            'targets' => $targets,
            'lines' => $lines,
            'overall_pct' => $overall,
            'status' => $status,
            'status_label' => match ($status) {
                'met' => 'تحقيق الأهداف — ممتاز',
                'near' => 'قريب من الهدف',
                default => 'أقل من الهدف — يحتاج دفعة',
            },
        ];
    }

    /**
     * @return array<string, int>
     */
    public function metricsFor(int $userId, Carbon $from, Carbon $to): array
    {
        // CRM verified: نشاط مرتبط بعميل (sales_lead_id) ومسجّل باسم الموظف.
        $callsQ = SalesActivity::query()
            ->where('user_id', $userId)
            ->where('type', 'call')
            ->whereNotNull('sales_lead_id')
            ->whereBetween('created_at', [$from, $to]);

        $callAttempts = (clone $callsQ)->count();

        $answered = Schema::hasColumn('sales_activities', 'outcome')
            ? (clone $callsQ)->whereIn('outcome', SalesActivity::ANSWERED_OUTCOMES)->count()
            : (clone $callsQ)->count();

        $qualifiedFromStage = (int) SalesActivity::query()
            ->where('user_id', $userId)
            ->where('type', 'stage_change')
            ->whereNotNull('sales_lead_id')
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->filter(fn (SalesActivity $a) => in_array($a->meta['to'] ?? null, ['qualification', 'qualified'], true))
            ->count();

        $qualified = Schema::hasColumn('sales_activities', 'outcome')
            ? max(
                (clone $callsQ)->whereIn('outcome', SalesActivity::QUALIFIED_OUTCOMES)->count(),
                $qualifiedFromStage
            )
            : $qualifiedFromStage;

        $meetings = SalesActivity::query()
            ->where('user_id', $userId)
            ->where('type', 'meeting')
            ->whereNotNull('sales_lead_id')
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $proposals = SalesActivity::query()
            ->where('user_id', $userId)
            ->where('type', 'stage_change')
            ->whereNotNull('sales_lead_id')
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->filter(fn (SalesActivity $a) => in_array($a->meta['to'] ?? null, ['offer_sent', 'proposal'], true))
            ->count();

        $paid = SalesLead::query()
            ->where('assigned_to', $userId)
            ->whereIn('stage', SalesLead::PAID_STAGES)
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('paid_at', [$from, $to])
                    ->orWhereBetween('won_confirmed_at', [$from, $to])
                    ->orWhere(function ($q2) use ($from, $to) {
                        $q2->whereNull('paid_at')
                            ->whereNull('won_confirmed_at')
                            ->whereBetween('closed_at', [$from, $to]);
                    });
            })
            ->count();

        return [
            'call_attempts_daily' => $callAttempts,
            'calls_answered_daily' => $answered,
            'qualified_conversations_daily' => $qualified,
            'discovery_sessions_daily' => $meetings,
            'proposals_daily' => $proposals,
            'paid_enrollments_daily' => $paid,
        ];
    }

    /**
     * @return array<string, int>
     */
    public function outcomeBreakdown(int $userId, Carbon $from, Carbon $to): array
    {
        if (! Schema::hasColumn('sales_activities', 'outcome')) {
            return [];
        }

        $rows = SalesActivity::query()
            ->where('user_id', $userId)
            ->where('type', 'call')
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('outcome')
            ->selectRaw('outcome, COUNT(*) as c')
            ->groupBy('outcome')
            ->pluck('c', 'outcome')
            ->all();

        $out = [];
        foreach (array_keys(SalesActivity::OUTCOMES) as $key) {
            $out[$key] = (int) ($rows[$key] ?? 0);
        }

        return $out;
    }
}
