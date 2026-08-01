<?php

namespace App\Services;

use App\Models\SalesActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class SalesLiveBoardService
{
    public function __construct(
        private SalesTeamService $teams,
        private SalesDailyResultService $dailyResults,
        private SalesDayBlockService $blocks
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function boardForManager(User $manager, ?Carbon $day = null): array
    {
        $day = ($day ?? today())->copy()->startOfDay();
        $end = $day->copy()->endOfDay();
        $team = $this->teams->managedTeamOrFail($manager);
        $memberIds = $this->teams->memberUserIds($team);

        $members = User::query()
            ->whereIn('id', $memberIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $rows = [];
        $teamMetrics = [
            'call_attempts_daily' => 0,
            'calls_answered_daily' => 0,
            'qualified_conversations_daily' => 0,
            'discovery_sessions_daily' => 0,
            'proposals_daily' => 0,
            'paid_enrollments_daily' => 0,
        ];
        $teamTargets = [
            'call_attempts_daily' => 0,
            'calls_answered_daily' => 0,
            'qualified_conversations_daily' => 0,
            'discovery_sessions_daily' => 0,
            'proposals_daily' => 0,
            'paid_enrollments_daily' => 0,
        ];

        $blockSnap = $this->blocks->snapshot();

        foreach ($members as $member) {
            $cmp = $this->dailyResults->comparisonFor($member, $day);
            $onPace = $this->blocks->isOnPace($member);
            $outcomes = $this->dailyResults->outcomeBreakdown($member->id, $day, $end);

            foreach ($teamMetrics as $k => $_) {
                $teamMetrics[$k] += (int) ($cmp['metrics'][$k] ?? 0);
                $teamTargets[$k] += (float) ($cmp['targets'][$k] ?? 0);
            }

            $rows[] = [
                'user' => $member,
                'overall_pct' => $cmp['overall_pct'],
                'status' => $cmp['status'],
                'status_label' => $cmp['status_label'],
                'metrics' => $cmp['metrics'],
                'targets' => $cmp['targets'],
                'lines' => $cmp['lines'],
                'on_pace' => $onPace,
                'behind_pulse' => ! $onPace,
                'outcomes' => $outcomes,
                'paid' => (int) ($cmp['metrics']['paid_enrollments_daily'] ?? 0),
                'calls' => (int) ($cmp['metrics']['call_attempts_daily'] ?? 0),
            ];
        }

        usort($rows, fn ($a, $b) => $b['overall_pct'] <=> $a['overall_pct']);

        $topSeller = $rows[0] ?? null;
        if ($topSeller && (float) $topSeller['overall_pct'] <= 0 && (int) $topSeller['paid'] === 0 && (int) $topSeller['calls'] === 0) {
            $topSeller = null;
        }

        // ترتيب بديل حسب التسجيلات المدفوعة إن تساوت النسب
        $byPaid = collect($rows)->sortByDesc(fn ($r) => [$r['paid'], $r['overall_pct']])->values();
        if ($byPaid->isNotEmpty() && (int) $byPaid->first()['paid'] > 0) {
            $topSeller = $byPaid->first();
        }

        $teamPcts = [];
        foreach ($teamTargets as $k => $t) {
            $a = (int) ($teamMetrics[$k] ?? 0);
            $teamPcts[] = $t > 0 ? min(100, round($a / $t * 100, 1)) : ($a > 0 ? 100.0 : 0.0);
        }
        $teamOverall = count($teamPcts) ? round(array_sum($teamPcts) / count($teamPcts), 1) : 0.0;

        $conversion = $teamMetrics['call_attempts_daily'] > 0
            ? round($teamMetrics['paid_enrollments_daily'] / $teamMetrics['call_attempts_daily'] * 100, 2)
            : 0.0;

        $behind = array_values(array_filter($rows, fn ($r) => $r['behind_pulse']));

        $outcomeTotals = [];
        foreach (array_keys(\App\Models\SalesActivity::OUTCOMES) as $key) {
            $outcomeTotals[$key] = (int) collect($rows)->sum(fn ($r) => (int) ($r['outcomes'][$key] ?? 0));
        }

        return [
            'team' => $team,
            'day' => $day,
            'block' => $blockSnap,
            'rows' => $rows,
            'top_seller' => $topSeller,
            'team_metrics' => $teamMetrics,
            'team_targets' => $teamTargets,
            'team_overall_pct' => $teamOverall,
            'conversion_pct' => $conversion,
            'behind_pulse' => $behind,
            'outcome_totals' => $outcomeTotals,
            'members_count' => $members->count(),
        ];
    }
}
