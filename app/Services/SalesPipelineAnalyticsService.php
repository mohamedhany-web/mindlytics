<?php

namespace App\Services;

use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesPipelineAnalyticsService
{
    public function __construct(private SalesTeamService $teams) {}

    /**
     * @return array<string, mixed>
     */
    public function boardForManager(User $manager, ?Carbon $day = null): array
    {
        $day = ($day ?? today())->copy()->startOfDay();
        $end = $day->copy()->endOfDay();
        $team = $this->teams->managedTeamOrFail($manager);
        $memberIds = $this->teams->memberUserIds($team);

        $base = SalesLead::query()->whereIn('assigned_to', $memberIds);

        $stageCounts = [];
        foreach (array_keys(SalesLead::STAGES) as $stage) {
            $stageCounts[$stage] = (int) (clone $base)->where('stage', $stage)->count();
        }

        $highlights = [
            'new_leads' => $stageCounts['new_lead'] ?? 0,
            'no_contact' => (int) (clone $base)->whereIn('stage', ['new_lead'])->whereNull('last_contacted_at')->count(),
            'qualification' => $stageCounts['qualification'] ?? 0,
            'objection' => $stageCounts['objection'] ?? 0,
            'payment_pending' => $stageCounts['payment_pending'] ?? 0,
            'paid_today' => (int) (clone $base)->whereIn('stage', SalesLead::PAID_STAGES)
                ->where(function ($q) use ($day, $end) {
                    $q->whereBetween('paid_at', [$day, $end])
                        ->orWhereBetween('closed_at', [$day, $end]);
                })->count(),
            'dormant' => $stageCounts['dormant'] ?? 0,
            'lost' => $stageCounts['lost'] ?? 0,
        ];

        $conversions = $this->stageConversions($memberIds, $day->copy()->subDays(30), $end);
        $dwell = $this->averageDwellHours($memberIds);
        $lossReasons = $this->topReasons($memberIds, 'lost_reason', 8);
        $objectionReasons = $this->topReasons($memberIds, 'objection_reason', 8);
        $repRows = $this->repPerformance($memberIds, $day, $end);

        return [
            'team' => $team,
            'day' => $day,
            'stage_counts' => $stageCounts,
            'highlights' => $highlights,
            'conversions' => $conversions,
            'dwell_hours' => $dwell,
            'loss_reasons' => $lossReasons,
            'objection_reasons' => $objectionReasons,
            'reps' => $repRows,
        ];
    }

    /**
     * @param  list<int>  $memberIds
     * @return list<array{from:string,to:string,count:int,rate:float}>
     */
    private function stageConversions(array $memberIds, Carbon $from, Carbon $to): array
    {
        $pairs = [
            ['new_lead', 'first_contact'],
            ['first_contact', 'connected'],
            ['connected', 'qualification'],
            ['qualification', 'interested'],
            ['interested', 'offer_sent'],
            ['offer_sent', 'payment_pending'],
            ['payment_pending', 'payment_received'],
            ['payment_received', 'enrollment_completed'],
        ];

        $changes = SalesActivity::query()
            ->where('type', 'stage_change')
            ->whereBetween('created_at', [$from, $to])
            ->whereHas('lead', fn ($q) => $q->whereIn('assigned_to', $memberIds))
            ->get(['meta', 'sales_lead_id']);

        $out = [];
        foreach ($pairs as [$a, $b]) {
            $enteredA = $changes->filter(fn ($x) => ($x->meta['to'] ?? null) === $a)->unique('sales_lead_id')->count();
            $aToB = $changes->filter(fn ($x) => ($x->meta['from'] ?? null) === $a && ($x->meta['to'] ?? null) === $b)->unique('sales_lead_id')->count();
            // fallback: count currently in later stages as rough rate from stock
            $stockA = SalesLead::query()->whereIn('assigned_to', $memberIds)->where('stage', $a)->count();
            $denom = max($enteredA, $stockA, 1);
            $out[] = [
                'from' => $a,
                'to' => $b,
                'from_label' => SalesLead::stageLabel($a),
                'to_label' => SalesLead::stageLabel($b),
                'count' => $aToB,
                'rate' => round(min(100, $aToB / $denom * 100), 1),
            ];
        }

        return $out;
    }

    /**
     * @param  list<int>  $memberIds
     * @return array<string, float>
     */
    private function averageDwellHours(array $memberIds): array
    {
        $rows = SalesLead::query()
            ->whereIn('assigned_to', $memberIds)
            ->whereNotNull('stage_entered_at')
            ->get(['stage', 'stage_entered_at']);

        $buckets = [];
        foreach ($rows as $lead) {
            $hours = max(0, $lead->stage_entered_at->diffInMinutes(now()) / 60);
            $buckets[$lead->stage][] = $hours;
        }

        $avg = [];
        foreach ($buckets as $stage => $vals) {
            $avg[$stage] = round(array_sum($vals) / max(count($vals), 1), 1);
        }

        return $avg;
    }

    /**
     * @param  list<int>  $memberIds
     * @return list<array{reason:string,label:string,count:int}>
     */
    private function topReasons(array $memberIds, string $column, int $limit): array
    {
        if (! \Illuminate\Support\Facades\Schema::hasColumn('sales_leads', $column)) {
            return [];
        }

        $rows = SalesLead::query()
            ->whereIn('assigned_to', $memberIds)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->select($column.' as reason', DB::raw('COUNT(*) as c'))
            ->groupBy($column)
            ->orderByDesc('c')
            ->limit($limit)
            ->get();

        $labels = $column === 'objection_reason' ? SalesLead::OBJECTION_REASONS : SalesLead::LOSS_REASONS;

        return $rows->map(fn ($r) => [
            'reason' => (string) $r->reason,
            'label' => $labels[$r->reason] ?? (string) $r->reason,
            'count' => (int) $r->c,
        ])->all();
    }

    /**
     * @param  list<int>  $memberIds
     * @return list<array<string, mixed>>
     */
    private function repPerformance(array $memberIds, Carbon $day, Carbon $end): array
    {
        $users = User::query()->whereIn('id', $memberIds)->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $rows = [];

        foreach ($users as $user) {
            $calls = SalesActivity::query()
                ->where('user_id', $user->id)
                ->where('type', 'call')
                ->whereBetween('created_at', [$day, $end])
                ->count();

            $enrollments = SalesLead::query()
                ->where('assigned_to', $user->id)
                ->where('stage', SalesLead::WON_STAGE)
                ->where(function ($q) use ($day, $end) {
                    $q->whereBetween('closed_at', [$day, $end])
                        ->orWhereBetween('won_confirmed_at', [$day, $end]);
                })
                ->count();

            $salesValue = (float) SalesLead::query()
                ->where('assigned_to', $user->id)
                ->whereIn('stage', SalesLead::WON_LIKE_STAGES)
                ->whereBetween('closed_at', [$day->copy()->startOfMonth(), $end])
                ->sum('expected_value');

            $avgResponse = SalesLead::query()
                ->where('assigned_to', $user->id)
                ->whereNotNull('last_contacted_at')
                ->whereBetween('created_at', [$day->copy()->subDays(14), $end])
                ->get(['created_at', 'last_contacted_at'])
                ->map(fn ($l) => $l->created_at->diffInMinutes($l->last_contacted_at))
                ->filter(fn ($m) => $m >= 0)
                ->avg();

            $rows[] = [
                'user' => $user,
                'calls' => $calls,
                'enrollments' => $enrollments,
                'sales_value' => $salesValue,
                'avg_response_minutes' => $avgResponse !== null ? round((float) $avgResponse, 0) : null,
            ];
        }

        usort($rows, fn ($a, $b) => [$b['enrollments'], $b['calls']] <=> [$a['enrollments'], $a['calls']]);

        return $rows;
    }
}
