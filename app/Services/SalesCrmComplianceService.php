<?php

namespace App\Services;

use App\Models\MetaSocialMessage;
use App\Models\SalesActivity;
use App\Models\SalesDailyReport;
use App\Models\SalesLead;
use App\Models\User;
use App\Models\WhatsAppConversationMessage;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * تدقيق إداري لاستخدام CRM — يحاسب على نشاط مربوط بعميل فقط.
 */
class SalesCrmComplianceService
{
    /**
     * @return array{
     *   from: Carbon,
     *   to: Carbon,
     *   rows: list<array<string, mixed>>,
     *   summary: array<string, mixed>,
     *   insights: list<array{code: string, title: string, detail: string, severity: string}>,
     *   exceptions: list<array<string, mixed>>
     * }
     */
    public function buildBoard(Carbon $from, Carbon $to, ?int $employeeId = null): array
    {
        $from = $from->copy()->startOfDay();
        $to = $to->copy()->endOfDay();

        $reps = User::salesEmployees()
            ->where('is_active', true)
            ->with('employeeJob:id,code,name')
            ->orderBy('name')
            ->get();

        if ($employeeId) {
            $reps = $reps->where('id', $employeeId)->values();
        }

        $rows = [];
        $exceptions = [];

        foreach ($reps as $rep) {
            $row = $this->buildEmployee($rep, $from, $to);
            $rows[] = $row;
            foreach ($row['exceptions'] as $ex) {
                $exceptions[] = array_merge($ex, [
                    'employee_id' => $rep->id,
                    'employee_name' => $rep->name,
                ]);
            }
        }

        usort($rows, fn ($a, $b) => $b['compliance_score'] <=> $a['compliance_score']);

        return [
            'from' => $from,
            'to' => $to,
            'rows' => $rows,
            'summary' => $this->summarizeRows($rows),
            'insights' => $this->insightsForRows($rows),
            'exceptions' => $exceptions,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array<string, mixed>
     */
    public function summarizeRows(array $rows): array
    {
        return $this->summarize($rows);
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array{code: string, title: string, detail: string, severity: string}>
     */
    public function insightsForRows(array $rows): array
    {
        return $this->buildInsights($rows);
    }

    /**
     * @return array<string, mixed>
     */
    public function buildEmployee(User $employee, Carbon $from, Carbon $to): array
    {
        $from = $from->copy()->startOfDay();
        $to = $to->copy()->endOfDay();
        $targets = config('sales_crm_compliance.targets', []);

        $usage = $this->crmUsageMetrics($employee, $from, $to);
        $quality = $this->recordingQualityMetrics($employee->id, $from, $to);
        $social = $this->socialLinkMetrics($employee->id, $from, $to);
        $report = $this->reportAccuracyMetrics($employee->id, $from, $to);
        $finance = $this->financeMetrics($employee->id, $from, $to);
        $pipeline = $this->pipelineMetrics($employee->id, $from, $to);
        $exceptions = $this->collectExceptions($employee->id, $usage, $quality, $social, $report, $finance, $pipeline);

        $pillars = $this->scorePillars($usage, $quality, $social, $report, $finance, $targets);
        $weights = config('sales_crm_compliance.weights', []);
        $score = 0.0;
        foreach ($weights as $key => $wgt) {
            $score += ($pillars[$key]['score'] ?? 0) * (float) $wgt;
        }
        $score = round($score, 1);

        $alerts = config('sales_crm_compliance.alerts', []);
        $tone = $score >= 85 ? 'excellent'
            : ($score >= (float) ($alerts['warning_below'] ?? 65) ? 'good'
                : ($score >= (float) ($alerts['critical_below'] ?? 45) ? 'warning' : 'critical'));

        return [
            'user' => $employee,
            'employee_id' => $employee->id,
            'name' => $employee->name,
            'job_title' => $employee->employeeJob->name ?? 'مبيعات',
            'compliance_score' => $score,
            'tone' => $tone,
            'pillars' => $pillars,
            'usage' => $usage,
            'quality' => $quality,
            'social' => $social,
            'report' => $report,
            'finance' => $finance,
            'pipeline' => $pipeline,
            'exceptions' => $exceptions,
            'recent_activities' => $this->verifiedActivities($employee->id, $from, $to)->take(50)->values(),
            'stage_changes' => $this->stageChanges($employee->id, $from, $to)->take(40)->values(),
            'leads_touched' => $this->leadsTouched($employee->id, $from, $to)->take(40)->values(),
        ];
    }

    /**
     * خط زمني كامل لعميل واحد (كل خطوة موثّقة).
     *
     * @return array<string, mixed>
     */
    public function leadTimeline(SalesLead $lead): array
    {
        $activities = SalesActivity::query()
            ->where('sales_lead_id', $lead->id)
            ->with('user:id,name')
            ->orderBy('created_at')
            ->get();

        $steps = $activities->map(function (SalesActivity $a) {
            $meta = is_array($a->meta) ? $a->meta : [];

            return [
                'id' => $a->id,
                'at' => $a->created_at,
                'type' => $a->type,
                'type_label' => SalesActivity::typeLabel($a->type),
                'outcome' => $a->outcome,
                'outcome_label' => SalesActivity::outcomeLabel($a->outcome),
                'title' => $a->title,
                'body' => $a->body,
                'user_id' => $a->user_id,
                'user_name' => $a->user->name ?? '—',
                'from_stage' => $meta['from'] ?? null,
                'to_stage' => $meta['to'] ?? null,
                'from_label' => isset($meta['from']) ? SalesLead::stageLabel((string) $meta['from']) : null,
                'to_label' => isset($meta['to']) ? SalesLead::stageLabel((string) $meta['to']) : null,
                'duration_seconds' => $a->duration_seconds,
                'has_recording' => filled($a->recording_url ?? null),
            ];
        })->values()->all();

        $qualificationFields = config('sales_crm_compliance.qualification_fields', []);
        $filled = 0;
        $fieldStatus = [];
        foreach ($qualificationFields as $field) {
            $val = $lead->{$field} ?? null;
            $ok = ! ($val === null || $val === '');
            if ($ok) {
                $filled++;
            }
            $fieldStatus[$field] = $ok;
        }
        $fillPct = count($qualificationFields) > 0
            ? round($filled / count($qualificationFields) * 100, 1)
            : 100.0;

        return [
            'lead' => $lead,
            'steps' => $steps,
            'steps_count' => count($steps),
            'stage_changes_count' => collect($steps)->where('type', 'stage_change')->count(),
            'qualification_fill_pct' => $fillPct,
            'qualification_fields' => $fieldStatus,
            'finance_verified' => filled($lead->payment_txn_ref) || filled($lead->won_confirmed_at),
        ];
    }

    /**
     * @return array<string, int|float|null>
     */
    private function crmUsageMetrics(User $employee, Carbon $from, Carbon $to): array
    {
        $workDays = $this->workDaysInRange($employee, $from, $to);
        $activityDays = SalesActivity::query()
            ->where('user_id', $employee->id)
            ->whereNotNull('sales_lead_id')
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as d')
            ->distinct()
            ->pluck('d')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->values();

        $engagedWorkDays = 0;
        $cursor = $from->copy()->startOfDay();
        $end = min($to->copy()->startOfDay(), today());
        $engagedSet = $activityDays->flip();
        while ($cursor->lte($end)) {
            if ($this->isWorkDay($employee, $cursor) && $engagedSet->has($cursor->toDateString())) {
                $engagedWorkDays++;
            }
            $cursor->addDay();
        }

        $crmActivities = SalesActivity::query()
            ->where('user_id', $employee->id)
            ->whereNotNull('sales_lead_id')
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $usagePct = $workDays > 0
            ? round($engagedWorkDays / $workDays * 100, 1)
            : null;

        $avgPerWorkday = $workDays > 0
            ? round($crmActivities / $workDays, 1)
            : (float) $crmActivities;

        return [
            'work_days' => $workDays,
            'engaged_work_days' => $engagedWorkDays,
            'usage_pct' => $usagePct,
            'crm_activities' => $crmActivities,
            'avg_activities_per_workday' => $avgPerWorkday,
            'leads_touched' => SalesActivity::query()
                ->where('user_id', $employee->id)
                ->whereNotNull('sales_lead_id')
                ->whereBetween('created_at', [$from, $to])
                ->pluck('sales_lead_id')
                ->unique()
                ->count(),
        ];
    }

    /**
     * @return array<string, int|float|null>
     */
    private function recordingQualityMetrics(int $userId, Carbon $from, Carbon $to): array
    {
        $calls = SalesActivity::query()
            ->where('user_id', $userId)
            ->where('type', 'call')
            ->whereNotNull('sales_lead_id')
            ->whereBetween('created_at', [$from, $to]);

        $callCount = (clone $calls)->count();
        $withOutcome = Schema::hasColumn('sales_activities', 'outcome')
            ? (clone $calls)->whereNotNull('outcome')->where('outcome', '!=', '')->count()
            : $callCount;
        $withDuration = (clone $calls)->whereNotNull('duration_seconds')->where('duration_seconds', '>', 0)->count();
        $outcomePct = $callCount > 0 ? round($withOutcome / $callCount * 100, 1) : null;

        $qualFields = config('sales_crm_compliance.qualification_fields', []);
        $touchedLeadIds = SalesActivity::query()
            ->where('user_id', $userId)
            ->whereNotNull('sales_lead_id')
            ->whereBetween('created_at', [$from, $to])
            ->pluck('sales_lead_id')
            ->unique()
            ->all();

        $qualificationLeads = empty($touchedLeadIds)
            ? collect()
            : SalesLead::query()
                ->whereIn('id', $touchedLeadIds)
                ->where(function ($q) {
                    $q->whereIn('stage', [
                        'qualification', 'interested', 'objection', 'follow_up_scheduled',
                        'offer_sent', 'payment_pending', 'payment_received',
                        'enrollment_completed', 'upsell',
                    ])->orWhereNotNull('profile_type');
                })
                ->get($qualFields);

        $fillScores = [];
        foreach ($qualificationLeads as $lead) {
            if (empty($qualFields)) {
                continue;
            }
            $filled = 0;
            foreach ($qualFields as $field) {
                $val = $lead->{$field} ?? null;
                if (! ($val === null || $val === '')) {
                    $filled++;
                }
            }
            $fillScores[] = $filled / count($qualFields) * 100;
        }
        $qualificationFillPct = count($fillScores) > 0
            ? round(array_sum($fillScores) / count($fillScores), 1)
            : null;

        $pipeline = $this->stageContactDiscipline($userId, $from, $to);

        $parts = array_filter([
            $outcomePct,
            $qualificationFillPct,
            $pipeline['with_contact_pct'],
        ], fn ($v) => $v !== null);
        $qualityScore = count($parts) > 0 ? round(array_sum($parts) / count($parts), 1) : null;

        return [
            'calls_total' => $callCount,
            'calls_with_outcome' => $withOutcome,
            'calls_with_duration' => $withDuration,
            'calls_with_outcome_pct' => $outcomePct,
            'qualification_leads' => $qualificationLeads->count(),
            'qualification_fill_pct' => $qualificationFillPct,
            'stage_changes' => $pipeline['stage_changes'],
            'stage_with_prior_contact' => $pipeline['with_contact'],
            'stage_without_prior_contact' => $pipeline['without_contact'],
            'stage_with_contact_pct' => $pipeline['with_contact_pct'],
            'quality_score' => $qualityScore,
        ];
    }

    /**
     * @return array{stage_changes: int, with_contact: int, without_contact: int, with_contact_pct: float|null}
     */
    private function stageContactDiscipline(int $userId, Carbon $from, Carbon $to): array
    {
        $softTargets = config('sales_crm_compliance.soft_stage_targets', []);
        $changes = SalesActivity::query()
            ->where('user_id', $userId)
            ->where('type', 'stage_change')
            ->whereNotNull('sales_lead_id')
            ->whereBetween('created_at', [$from, $to])
            ->get(['id', 'sales_lead_id', 'created_at', 'meta']);

        $with = 0;
        $without = 0;

        foreach ($changes as $change) {
            $meta = is_array($change->meta) ? $change->meta : [];
            $toStage = (string) ($meta['to'] ?? '');
            if ($toStage !== '' && in_array($toStage, $softTargets, true)) {
                continue;
            }

            $windowStart = Carbon::parse($change->created_at)->subDay();
            $hadContact = SalesActivity::query()
                ->where('sales_lead_id', $change->sales_lead_id)
                ->where('user_id', $userId)
                ->whereIn('type', ['call', 'whatsapp', 'meeting', 'follow_up', 'email'])
                ->where('created_at', '>=', $windowStart)
                ->where('created_at', '<', $change->created_at)
                ->exists();

            if ($hadContact) {
                $with++;
            } else {
                $without++;
            }
        }

        $scored = $with + $without;
        $pct = $scored > 0 ? round($with / $scored * 100, 1) : null;

        return [
            'stage_changes' => $changes->count(),
            'with_contact' => $with,
            'without_contact' => $without,
            'with_contact_pct' => $pct,
        ];
    }

    /**
     * @return array<string, int|float|null>
     */
    private function socialLinkMetrics(int $userId, Carbon $from, Carbon $to): array
    {
        $waLinked = 0;
        $waUnlinked = 0;
        if (Schema::hasTable('whatsapp_conversation_messages')) {
            $waLinked = WhatsAppConversationMessage::query()
                ->where('sent_by_user_id', $userId)
                ->where('direction', 'outbound')
                ->whereBetween('created_at', [$from, $to])
                ->whereHas('conversation', fn ($q) => $q->whereNotNull('sales_lead_id'))
                ->count();
            $waUnlinked = WhatsAppConversationMessage::query()
                ->where('sent_by_user_id', $userId)
                ->where('direction', 'outbound')
                ->whereBetween('created_at', [$from, $to])
                ->whereHas('conversation', fn ($q) => $q->whereNull('sales_lead_id'))
                ->count();
        }

        $metaLinked = 0;
        $metaUnlinked = 0;
        if (Schema::hasTable('meta_social_messages')) {
            $metaLinked = MetaSocialMessage::query()
                ->where('sent_by_user_id', $userId)
                ->where('direction', 'outbound')
                ->whereBetween('created_at', [$from, $to])
                ->whereHas('conversation', fn ($q) => $q->whereNotNull('sales_lead_id'))
                ->count();
            $metaUnlinked = MetaSocialMessage::query()
                ->where('sent_by_user_id', $userId)
                ->where('direction', 'outbound')
                ->whereBetween('created_at', [$from, $to])
                ->whereHas('conversation', fn ($q) => $q->whereNull('sales_lead_id'))
                ->count();
        }

        $linked = $waLinked + $metaLinked;
        $unlinked = $waUnlinked + $metaUnlinked;
        $total = $linked + $unlinked;
        $linkPct = $total > 0 ? round($linked / $total * 100, 1) : null;

        return [
            'whatsapp_linked' => $waLinked,
            'whatsapp_unlinked' => $waUnlinked,
            'meta_linked' => $metaLinked,
            'meta_unlinked' => $metaUnlinked,
            'linked_total' => $linked,
            'unlinked_total' => $unlinked,
            'link_pct' => $linkPct,
        ];
    }

    /**
     * تطابق أرقام التقرير اليومي مع أنشطة CRM الموثّقة.
     *
     * @return array<string, int|float|null>
     */
    private function reportAccuracyMetrics(int $userId, Carbon $from, Carbon $to): array
    {
        $reports = SalesDailyReport::query()
            ->forUser($userId)
            ->submitted()
            ->whereDate('report_date', '>=', $from->toDateString())
            ->whereDate('report_date', '<=', $to->toDateString())
            ->get();

        if ($reports->isEmpty()) {
            return [
                'submitted_reports' => 0,
                'accuracy_pct' => null,
                'claimed_calls' => 0,
                'verified_calls' => 0,
                'claimed_meetings' => 0,
                'verified_meetings' => 0,
                'claimed_followups' => 0,
                'verified_followups' => 0,
                'inflated_days' => 0,
            ];
        }

        $dayScores = [];
        $claimedCalls = 0;
        $verifiedCalls = 0;
        $claimedMeetings = 0;
        $verifiedMeetings = 0;
        $claimedFollowups = 0;
        $verifiedFollowups = 0;
        $inflatedDays = 0;

        foreach ($reports as $report) {
            $dayStart = Carbon::parse($report->report_date)->startOfDay();
            $dayEnd = Carbon::parse($report->report_date)->endOfDay();

            $vCalls = SalesActivity::query()
                ->where('user_id', $userId)
                ->whereNotNull('sales_lead_id')
                ->where('type', 'call')
                ->whereDate('created_at', $dayStart->toDateString())
                ->count();
            $vMeetings = SalesActivity::query()
                ->where('user_id', $userId)
                ->whereNotNull('sales_lead_id')
                ->where('type', 'meeting')
                ->whereDate('created_at', $dayStart->toDateString())
                ->count();
            $vFollowups = SalesActivity::query()
                ->where('user_id', $userId)
                ->whereNotNull('sales_lead_id')
                ->where('type', 'follow_up')
                ->whereDate('created_at', $dayStart->toDateString())
                ->count();

            $cCalls = (int) ($report->calls_made ?? 0);
            $cMeetings = (int) ($report->meetings_held ?? 0);
            $cFollowups = (int) ($report->followups_done ?? 0);

            $claimedCalls += $cCalls;
            $verifiedCalls += $vCalls;
            $claimedMeetings += $cMeetings;
            $verifiedMeetings += $vMeetings;
            $claimedFollowups += $cFollowups;
            $verifiedFollowups += $vFollowups;

            $pairs = [
                [$cCalls, $vCalls],
                [$cMeetings, $vMeetings],
                [$cFollowups, $vFollowups],
            ];
            $weighted = 0.0;
            $weightTotal = 0;
            foreach ($pairs as [$claimed, $verified]) {
                $w = max($claimed, $verified);
                if ($w === 0) {
                    continue;
                }
                $pairScore = max(0.0, 100.0 - (abs($claimed - $verified) / $w * 100.0));
                $weighted += $pairScore * $w;
                $weightTotal += $w;
            }
            $dayScore = $weightTotal > 0 ? ($weighted / $weightTotal) : 100.0;
            $dayScores[] = $dayScore;

            if ($cCalls > $vCalls + 2 || $cMeetings > $vMeetings + 1 || $cFollowups > $vFollowups + 2) {
                $inflatedDays++;
            }
        }

        return [
            'submitted_reports' => $reports->count(),
            'accuracy_pct' => round(array_sum($dayScores) / count($dayScores), 1),
            'claimed_calls' => $claimedCalls,
            'verified_calls' => $verifiedCalls,
            'claimed_meetings' => $claimedMeetings,
            'verified_meetings' => $verifiedMeetings,
            'claimed_followups' => $claimedFollowups,
            'verified_followups' => $verifiedFollowups,
            'inflated_days' => $inflatedDays,
        ];
    }

    /**
     * @return array<string, int|float>
     */
    private function financeMetrics(int $userId, Carbon $from, Carbon $to): array
    {
        $paidLeads = SalesLead::query()
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
            ->get(['id', 'expected_value', 'payment_amount', 'paid_at', 'won_confirmed_at', 'payment_txn_ref']);

        $crmDeclared = $paidLeads->count();
        $crmRevenue = (float) $paidLeads->sum(fn ($l) => (float) ($l->payment_amount ?: $l->expected_value ?: 0));
        $verified = $paidLeads->filter(fn ($l) => filled($l->payment_txn_ref) || filled($l->won_confirmed_at));
        $financeVerified = $verified->count();
        $financeRevenue = (float) $verified->sum(fn ($l) => (float) ($l->payment_amount ?: $l->expected_value ?: 0));
        $verifiedPct = $crmDeclared > 0
            ? round($financeVerified / $crmDeclared * 100, 1)
            : null;

        return [
            'crm_declared_paid' => $crmDeclared,
            'crm_declared_revenue' => $crmRevenue,
            'finance_verified_paid' => $financeVerified,
            'finance_verified_revenue' => $financeRevenue,
            'verified_pct' => $verifiedPct,
            'unverified_paid' => max(0, $crmDeclared - $financeVerified),
        ];
    }

    /**
     * @return array<string, int|float>
     */
    private function pipelineMetrics(int $userId, Carbon $from, Carbon $to): array
    {
        $changes = SalesActivity::query()
            ->where('user_id', $userId)
            ->where('type', 'stage_change')
            ->whereNotNull('sales_lead_id')
            ->whereBetween('created_at', [$from, $to])
            ->get(['meta']);

        $pairs = [];
        foreach ($changes as $change) {
            $meta = is_array($change->meta) ? $change->meta : [];
            $fromStage = (string) ($meta['from'] ?? '?');
            $toStage = (string) ($meta['to'] ?? '?');
            $key = $fromStage.'→'.$toStage;
            $pairs[$key] = ($pairs[$key] ?? 0) + 1;
        }
        arsort($pairs);

        return [
            'stage_changes' => $changes->count(),
            'unique_leads_moved' => SalesActivity::query()
                ->where('user_id', $userId)
                ->where('type', 'stage_change')
                ->whereNotNull('sales_lead_id')
                ->whereBetween('created_at', [$from, $to])
                ->pluck('sales_lead_id')
                ->unique()
                ->count(),
            'top_transitions' => array_slice($pairs, 0, 8, true),
        ];
    }

    /**
     * @param  array<string, mixed>  $usage
     * @param  array<string, mixed>  $quality
     * @param  array<string, mixed>  $social
     * @param  array<string, mixed>  $report
     * @param  array<string, mixed>  $finance
     * @param  array<string, mixed>  $targets
     * @return array<string, array{score: float, label: string, details: list<string>}>
     */
    private function scorePillars(
        array $usage,
        array $quality,
        array $social,
        array $report,
        array $finance,
        array $targets,
    ): array {
        $usageScore = $usage['usage_pct'] !== null
            ? (float) $usage['usage_pct']
            : 0.0;
        // تخفيف إذا النشاط اليومي ضعيف حتى لو «دخل» كل يوم
        $avgTarget = (float) ($targets['min_crm_activities_per_workday'] ?? 8);
        if ($avgTarget > 0 && ($usage['avg_activities_per_workday'] ?? 0) < $avgTarget) {
            $intensity = min(100.0, ((float) ($usage['avg_activities_per_workday'] ?? 0) / $avgTarget) * 100.0);
            $usageScore = round(($usageScore * 0.7) + ($intensity * 0.3), 1);
        }

        $qualityScore = $quality['quality_score'] !== null
            ? (float) $quality['quality_score']
            : (($quality['calls_total'] ?? 0) === 0 && ($quality['stage_changes'] ?? 0) === 0 ? 0.0 : 70.0);

        $socialScore = $social['link_pct'] !== null
            ? (float) $social['link_pct']
            : (($social['linked_total'] ?? 0) + ($social['unlinked_total'] ?? 0) === 0 ? 100.0 : 0.0);

        $reportScore = $report['accuracy_pct'] !== null
            ? (float) $report['accuracy_pct']
            : 50.0; // لا تقارير مسلّمة = نصف درجة (غير مثبت الاهتمام بالتوثيق)

        $financeScore = $finance['verified_pct'] !== null
            ? (float) $finance['verified_pct']
            : 100.0; // لا مدفوعات معلنة = لا عقوبة

        return [
            'crm_usage' => [
                'score' => round($usageScore, 1),
                'label' => 'استخدام CRM 30٪ — أيام تسجيل فعلي + كثافة النشاط',
                'details' => [
                    'أيام نشطة: '.($usage['engaged_work_days'] ?? 0).'/'.($usage['work_days'] ?? 0),
                    'متوسط أنشطة/يوم: '.($usage['avg_activities_per_workday'] ?? 0),
                ],
            ],
            'recording_quality' => [
                'score' => round($qualityScore, 1),
                'label' => 'جودة التسجيل 25٪ — نتائج مكالمات + Qualification + خطوات Pipeline',
                'details' => [
                    'مكالمات بنتيجة: '.($quality['calls_with_outcome_pct'] ?? '—').'%',
                    'اكتمال Qualification: '.($quality['qualification_fill_pct'] ?? '—').'%',
                    'مرحلة مع تواصل سابق: '.($quality['stage_with_contact_pct'] ?? '—').'%',
                ],
            ],
            'social_link' => [
                'score' => round($socialScore, 1),
                'label' => 'ربط السوشيال 15٪ — واتساب / Meta مربوط بـ Lead',
                'details' => [
                    'مربوط: '.($social['linked_total'] ?? 0),
                    'غير مربوط: '.($social['unlinked_total'] ?? 0),
                ],
            ],
            'report_accuracy' => [
                'score' => round($reportScore, 1),
                'label' => 'دقة التقرير 15٪ — ادّعاء التقرير مقابل CRM',
                'details' => [
                    'تقارير مسلّمة: '.($report['submitted_reports'] ?? 0),
                    'مكالمات: '.($report['verified_calls'] ?? 0).'/'.($report['claimed_calls'] ?? 0).' (موثّق/معلن)',
                    'أيام تضخيم: '.($report['inflated_days'] ?? 0),
                ],
            ],
            'finance_verification' => [
                'score' => round($financeScore, 1),
                'label' => 'توثيق الإيراد 15٪ — معلن CRM مقابل مؤكد مالياً',
                'details' => [
                    'معلن: '.($finance['crm_declared_paid'] ?? 0),
                    'مؤكد: '.($finance['finance_verified_paid'] ?? 0),
                ],
            ],
        ];
    }

    /**
     * @return list<array{code: string, title: string, detail: string, severity: string}>
     */
    private function collectExceptions(
        int $userId,
        array $usage,
        array $quality,
        array $social,
        array $report,
        array $finance,
        array $pipeline,
    ): array {
        $ex = [];
        $targets = config('sales_crm_compliance.targets', []);

        if (($usage['usage_pct'] ?? 100) !== null && (float) $usage['usage_pct'] < 50) {
            $ex[] = [
                'code' => 'low_crm_usage',
                'title' => 'استخدام CRM ضعيف',
                'detail' => 'نشط '.$usage['engaged_work_days'].' من '.$usage['work_days'].' يوم عمل ('.$usage['usage_pct'].'%)',
                'severity' => 'critical',
            ];
        }

        if (($quality['calls_with_outcome_pct'] ?? 100) !== null
            && (float) $quality['calls_with_outcome_pct'] < (float) ($targets['calls_with_outcome_pct'] ?? 90)
            && ($quality['calls_total'] ?? 0) > 0) {
            $ex[] = [
                'code' => 'call_no_outcome',
                'title' => 'مكالمات بدون نتيجة',
                'detail' => ($quality['calls_total'] - $quality['calls_with_outcome']).' من '.$quality['calls_total'].' بدون outcome',
                'severity' => 'warning',
            ];
        }

        if (($quality['stage_without_prior_contact'] ?? 0) > 0) {
            $ex[] = [
                'code' => 'stage_jump_no_contact',
                'title' => 'قفزات Pipeline بدون تواصل',
                'detail' => $quality['stage_without_prior_contact'].' انتقال مرحلة خلال 24 ساعة بلا مكالمة/واتساب/اجتماع',
                'severity' => 'warning',
            ];
        }

        if (($quality['qualification_fill_pct'] ?? 100) !== null
            && (float) $quality['qualification_fill_pct'] < (float) ($targets['qualification_fill_pct'] ?? 90)
            && ($quality['qualification_leads'] ?? 0) > 0) {
            $ex[] = [
                'code' => 'incomplete_qualification',
                'title' => 'بيانات Qualification ناقصة',
                'detail' => 'اكتمال الحقول '.$quality['qualification_fill_pct'].'% على '.$quality['qualification_leads'].' عميل',
                'severity' => 'warning',
            ];
        }

        if (($social['unlinked_total'] ?? 0) > 0) {
            $ex[] = [
                'code' => 'social_unlinked',
                'title' => 'رسائل سوشيال غير مربوطة',
                'detail' => 'واتساب '.$social['whatsapp_unlinked'].' · Meta '.$social['meta_unlinked'],
                'severity' => ($social['link_pct'] ?? 100) < 50 ? 'critical' : 'warning',
            ];
        }

        if (($report['inflated_days'] ?? 0) > 0) {
            $ex[] = [
                'code' => 'report_inflated',
                'title' => 'تضخيم في التقرير اليومي',
                'detail' => $report['inflated_days'].' يوم أرقام التقرير أعلى من CRM الموثّق',
                'severity' => 'critical',
            ];
        }

        if (($finance['unverified_paid'] ?? 0) > 0) {
            $ex[] = [
                'code' => 'payment_unverified',
                'title' => 'مدفوعات معلنة غير مؤكدة',
                'detail' => $finance['unverified_paid'].' تسجيل بلا payment_txn_ref / won_confirmed',
                'severity' => 'warning',
            ];
        }

        if (($pipeline['stage_changes'] ?? 0) === 0 && ($usage['crm_activities'] ?? 0) > 5) {
            $ex[] = [
                'code' => 'no_pipeline_movement',
                'title' => 'لا حركة في الـ Pipeline',
                'detail' => 'نشاط CRM موجود لكن بلا stage_change في الفترة',
                'severity' => 'info',
            ];
        }

        return $ex;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array<string, mixed>
     */
    private function summarize(array $rows): array
    {
        if ($rows === []) {
            return [
                'employees' => 0,
                'avg_compliance' => 0,
                'avg_usage' => 0,
                'avg_quality' => 0,
                'avg_social_link' => 0,
                'avg_report_accuracy' => 0,
                'critical_count' => 0,
                'warning_count' => 0,
                'total_exceptions' => 0,
                'total_unlinked_social' => 0,
                'total_inflated_days' => 0,
            ];
        }

        $avg = fn (string $path) => round(collect($rows)->avg(function ($r) use ($path) {
            return data_get($r, $path) ?? 0;
        }), 1);

        return [
            'employees' => count($rows),
            'avg_compliance' => $avg('compliance_score'),
            'avg_usage' => round(collect($rows)->avg(fn ($r) => $r['usage']['usage_pct'] ?? 0), 1),
            'avg_quality' => round(collect($rows)->avg(fn ($r) => $r['quality']['quality_score'] ?? 0), 1),
            'avg_social_link' => round(collect($rows)->avg(fn ($r) => $r['social']['link_pct'] ?? 100), 1),
            'avg_report_accuracy' => round(collect($rows)->avg(fn ($r) => $r['report']['accuracy_pct'] ?? 50), 1),
            'critical_count' => collect($rows)->where('tone', 'critical')->count(),
            'warning_count' => collect($rows)->where('tone', 'warning')->count(),
            'total_exceptions' => collect($rows)->sum(fn ($r) => count($r['exceptions'])),
            'total_unlinked_social' => collect($rows)->sum(fn ($r) => (int) ($r['social']['unlinked_total'] ?? 0)),
            'total_inflated_days' => collect($rows)->sum(fn ($r) => (int) ($r['report']['inflated_days'] ?? 0)),
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array{code: string, title: string, detail: string, severity: string}>
     */
    private function buildInsights(array $rows): array
    {
        if ($rows === []) {
            return [];
        }

        $insights = [];
        $sorted = $rows;
        usort($sorted, fn ($a, $b) => $a['compliance_score'] <=> $b['compliance_score']);
        $worst = $sorted[0] ?? null;
        $best = $sorted[count($sorted) - 1] ?? null;

        if ($best && ($best['compliance_score'] ?? 0) >= 80) {
            $insights[] = [
                'code' => 'top_compliance',
                'title' => 'أفضل التزام CRM',
                'detail' => $best['name'].' — '.$best['compliance_score'].'%',
                'severity' => 'good',
            ];
        }

        if ($worst && ($worst['compliance_score'] ?? 100) < 65) {
            $insights[] = [
                'code' => 'low_compliance',
                'title' => 'أقل التزام CRM',
                'detail' => $worst['name'].' — '.$worst['compliance_score'].'% · يحتاج مراجعة فورية',
                'severity' => 'critical',
            ];
        }

        $lowUsage = collect($rows)->filter(fn ($r) => ($r['usage']['usage_pct'] ?? 100) < 50)->values();
        if ($lowUsage->isNotEmpty()) {
            $insights[] = [
                'code' => 'team_low_usage',
                'title' => 'موظفون لا يهتمون بالـ CRM',
                'detail' => $lowUsage->count().' موظف استخدامهم أقل من 50٪ من أيام العمل: '.$lowUsage->pluck('name')->take(5)->implode('، '),
                'severity' => 'critical',
            ];
        }

        $inflated = collect($rows)->filter(fn ($r) => ($r['report']['inflated_days'] ?? 0) > 0)->sortByDesc(fn ($r) => $r['report']['inflated_days'])->values();
        if ($inflated->isNotEmpty()) {
            $insights[] = [
                'code' => 'team_report_inflation',
                'title' => 'فجوة التقرير اليومي مقابل الواقع',
                'detail' => $inflated->count().' موظف يضخّمون الأرقام — أبرزهم '.$inflated->first()['name'],
                'severity' => 'warning',
            ];
        }

        $unlinked = collect($rows)->sum(fn ($r) => (int) ($r['social']['unlinked_total'] ?? 0));
        if ($unlinked > 0) {
            $insights[] = [
                'code' => 'team_unlinked_social',
                'title' => 'عمل سوشيال خارج CRM',
                'detail' => number_format($unlinked).' رسالة صادرة غير مربوطة بعميل في الفترة',
                'severity' => 'warning',
            ];
        }

        $noPipeline = collect($rows)->filter(fn ($r) => ($r['pipeline']['stage_changes'] ?? 0) === 0 && ($r['usage']['crm_activities'] ?? 0) > 5);
        if ($noPipeline->isNotEmpty()) {
            $insights[] = [
                'code' => 'team_stale_pipeline',
                'title' => 'نشاط بلا تقدم في الـ Pipeline',
                'detail' => $noPipeline->count().' موظف يسجّل أنشطة دون تحريك مراحل العملاء',
                'severity' => 'info',
            ];
        }

        return $insights;
    }

    private function workDaysInRange(User $employee, Carbon $from, Carbon $to): int
    {
        $count = 0;
        $cursor = $from->copy()->startOfDay();
        $end = min($to->copy()->startOfDay(), today());
        while ($cursor->lte($end)) {
            if ($this->isWorkDay($employee, $cursor)) {
                $count++;
            }
            $cursor->addDay();
        }

        return $count;
    }

    private function isWorkDay(User $employee, Carbon $date): bool
    {
        try {
            if (! $employee->isEmployedOn($date)) {
                return false;
            }
        } catch (\Throwable) {
            // ignore
        }

        try {
            if ($employee->isWeeklyOff($date)) {
                return false;
            }
        } catch (\Throwable) {
            // ignore
        }

        try {
            if ($employee->isOnApprovedLeave($date)) {
                return false;
            }
        } catch (\Throwable) {
            // ignore
        }

        return true;
    }

    private function verifiedActivities(int $userId, Carbon $from, Carbon $to): Collection
    {
        return SalesActivity::query()
            ->where('user_id', $userId)
            ->whereNotNull('sales_lead_id')
            ->whereBetween('created_at', [$from, $to])
            ->with('lead:id,name,phone,stage')
            ->orderByDesc('created_at')
            ->get();
    }

    private function stageChanges(int $userId, Carbon $from, Carbon $to): Collection
    {
        return SalesActivity::query()
            ->where('user_id', $userId)
            ->where('type', 'stage_change')
            ->whereNotNull('sales_lead_id')
            ->whereBetween('created_at', [$from, $to])
            ->with('lead:id,name,phone,stage')
            ->orderByDesc('created_at')
            ->get();
    }

    private function leadsTouched(int $userId, Carbon $from, Carbon $to): Collection
    {
        $ids = SalesActivity::query()
            ->where('user_id', $userId)
            ->whereNotNull('sales_lead_id')
            ->whereBetween('created_at', [$from, $to])
            ->pluck('sales_lead_id')
            ->unique()
            ->all();

        if ($ids === []) {
            return collect();
        }

        return SalesLead::query()
            ->whereIn('id', $ids)
            ->orderByDesc('last_contacted_at')
            ->get();
    }
}
