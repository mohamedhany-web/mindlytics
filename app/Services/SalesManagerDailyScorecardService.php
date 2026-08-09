<?php

namespace App\Services;

use App\Models\EmployeeAttendanceRecord;
use App\Models\MetaSocialMessage;
use App\Models\SalesActivity;
use App\Models\SalesDailyReport;
use App\Models\SalesLead;
use App\Models\SalesManagerDailyReview;
use App\Models\SalesTeam;
use App\Models\User;
use App\Models\WhatsAppConversationMessage;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * مركز رقابة يومي لمدير المبيعات — يحاسب على نشاط CRM موثّق فقط.
 */
class SalesManagerDailyScorecardService
{
    public function __construct(
        private SalesDailyResultService $dailyResults,
        private EmployeeAttendanceService $attendance,
        private EmployeePresenceService $presence,
    ) {}

    /**
     * @param  list<int>  $memberIds
     * @return array{
     *   date: Carbon,
     *   team: ?SalesTeam,
     *   rows: list<array<string, mixed>>,
     *   summary: array<string, mixed>,
     *   exceptions: list<array<string, mixed>>,
     *   reviews: Collection<int, SalesManagerDailyReview>
     * }
     */
    public function buildForTeam(SalesTeam $team, array $memberIds, Carbon $date, ?int $employeeId = null, ?string $channel = null): array
    {
        $date = $date->copy()->startOfDay();
        $from = $date->copy()->startOfDay();
        $to = $date->copy()->endOfDay();

        $members = User::query()
            ->whereIn('id', $memberIds)
            ->where('is_active', true)
            ->with('employeeJob:id,code,name')
            ->orderBy('name')
            ->get();

        if ($employeeId) {
            $members = $members->where('id', $employeeId)->values();
        }

        $reviews = SalesManagerDailyReview::query()
            ->whereIn('employee_id', $members->pluck('id'))
            ->whereDate('work_date', $date->toDateString())
            ->get()
            ->keyBy('employee_id');

        $rows = [];
        $exceptions = [];

        foreach ($members as $member) {
            $row = $this->buildEmployeeDay($member, $from, $to, $team, $reviews->get($member->id));
            if ($channel && ! $this->rowMatchesChannel($row, $channel)) {
                continue;
            }
            $rows[] = $row;
            foreach ($row['exceptions'] as $ex) {
                $exceptions[] = array_merge($ex, [
                    'employee_id' => $member->id,
                    'employee_name' => $member->name,
                ]);
            }
        }

        usort($rows, fn ($a, $b) => $b['verified_score'] <=> $a['verified_score']);

        return [
            'date' => $date,
            'team' => $team,
            'rows' => $rows,
            'summary' => $this->summarize($rows),
            'exceptions' => $exceptions,
            'reviews' => $reviews,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function buildEmployeeDay(User $employee, Carbon $from, Carbon $to, ?SalesTeam $team = null, ?SalesManagerDailyReview $review = null): array
    {
        $date = $from->copy()->startOfDay();
        $targets = array_merge(
            config('sales_manager_scorecard.daily_targets', []),
            $this->dailyResults->targetsFor($employee, $date)
        );

        $sos = $this->dailyResults->metricsFor($employee->id, $from, $to);
        $channels = $this->channelMetrics($employee->id, $from, $to);
        $cold = $this->coldMetrics($employee->id, $from, $to);
        $crm = $this->crmDisciplineMetrics($employee->id, $from, $to);
        $attendance = $this->attendanceMetrics($employee, $date);
        $dailyReport = SalesDailyReport::forUser($employee->id)
            ->whereDate('report_date', $date->toDateString())
            ->first();

        $financial = $this->financialMetrics($employee->id, $from, $to);
        $exceptions = $this->collectExceptions($employee->id, $from, $to, $channels, $cold, $crm);

        $pillars = $this->scorePillars($sos, $channels, $cold, $crm, $attendance, $dailyReport, $financial, $targets);
        $weights = config('sales_manager_scorecard.weights', []);
        $verifiedScore = 0.0;
        foreach ($weights as $key => $wgt) {
            $verifiedScore += ($pillars[$key]['score'] ?? 0) * (float) $wgt;
        }
        $verifiedScore = round($verifiedScore, 1);

        $alerts = config('sales_manager_scorecard.alerts', []);
        $tone = $verifiedScore >= 85 ? 'excellent'
            : ($verifiedScore >= (float) ($alerts['warning_below'] ?? 65) ? 'good'
                : ($verifiedScore >= (float) ($alerts['critical_below'] ?? 45) ? 'warning' : 'critical'));

        $snapshot = [
            'verified_score' => $verifiedScore,
            'pillars' => $pillars,
            'sos' => $sos,
            'channels' => $channels,
            'cold' => $cold,
            'crm' => $crm,
            'attendance' => $attendance,
            'financial' => $financial,
            'daily_report_submitted' => (bool) ($dailyReport?->isSubmitted()),
            'targets' => $targets,
            'computed_at' => now()->toIso8601String(),
        ];

        // إذا معتمد: اعرض الـ snapshot المحفوظ ولا تعيد الحساب الحي
        if ($review?->isApproved() && is_array($review->score_snapshot)) {
            $snap = $review->score_snapshot;
            $verifiedScore = (float) ($review->verified_score ?? $snap['verified_score'] ?? $verifiedScore);
            $pillars = $snap['pillars'] ?? $pillars;
            $sos = $snap['sos'] ?? $sos;
            $channels = $snap['channels'] ?? $channels;
            $cold = $snap['cold'] ?? $cold;
            $crm = $snap['crm'] ?? $crm;
            $attendance = $snap['attendance'] ?? $attendance;
            $financial = $snap['financial'] ?? $financial;
            $tone = $verifiedScore >= 85 ? 'excellent'
                : ($verifiedScore >= 65 ? 'good' : ($verifiedScore >= 45 ? 'warning' : 'critical'));
        }

        $activities = $this->verifiedActivities($employee->id, $from, $to)->take(40)->values();
        $leadsTouched = $this->leadsTouched($employee->id, $from, $to)->take(40)->values();

        return [
            'user' => $employee,
            'employee_id' => $employee->id,
            'name' => $employee->name,
            'job_title' => $employee->employeeJob?->name ?? 'مبيعات',
            'verified_score' => $verifiedScore,
            'tone' => $tone,
            'pillars' => $pillars,
            'sos' => $sos,
            'channels' => $channels,
            'cold' => $cold,
            'crm' => $crm,
            'attendance' => $attendance,
            'financial' => $financial,
            'daily_report' => $dailyReport,
            'daily_report_submitted' => (bool) ($dailyReport?->isSubmitted()),
            'exceptions' => $exceptions,
            'missed_points' => $this->missedPoints($pillars, $targets, $sos, $channels, $cold, $crm, $attendance, $dailyReport),
            'review' => $review,
            'snapshot' => $snapshot,
            'activities' => $activities,
            'leads_touched' => $leadsTouched,
            'suggested_recommendation' => $this->suggestRecommendation($verifiedScore, $exceptions, $attendance),
        ];
    }

    /**
     * @return array<string, int|float>
     */
    private function channelMetrics(int $userId, Carbon $from, Carbon $to): array
    {
        $verifiedActs = SalesActivity::query()
            ->where('user_id', $userId)
            ->whereNotNull('sales_lead_id')
            ->whereBetween('created_at', [$from, $to]);

        $whatsappLinked = (clone $verifiedActs)->where('type', 'whatsapp')->count();
        $followups = (clone $verifiedActs)->where('type', 'follow_up')->count();
        $callsNoOutcome = (clone $verifiedActs)->where('type', 'call')
            ->where(function ($q) {
                $q->whereNull('outcome')->orWhere('outcome', '');
            })->count();
        $callAttempts = (clone $verifiedActs)->where('type', 'call')->count();
        $callDuration = (int) (clone $verifiedActs)->where('type', 'call')->sum('duration_seconds');

        $waOutboundLinked = 0;
        $waOutboundUnlinked = 0;
        if (Schema::hasTable('whatsapp_conversation_messages')) {
            $waOutboundLinked = WhatsAppConversationMessage::query()
                ->where('sent_by_user_id', $userId)
                ->where('direction', 'outbound')
                ->whereBetween('created_at', [$from, $to])
                ->whereHas('conversation', fn ($q) => $q->whereNotNull('sales_lead_id'))
                ->count();

            $waOutboundUnlinked = WhatsAppConversationMessage::query()
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

            // نشاط CRM من نوع note المرتبط بـ Meta يُحتسب ضمن linked social فقط إذا وُجد lead
            $metaCrmNotes = SalesActivity::query()
                ->where('user_id', $userId)
                ->whereNotNull('sales_lead_id')
                ->whereBetween('created_at', [$from, $to])
                ->where('type', 'note')
                ->where(function ($q) {
                    $q->where('body', 'like', '%meta%')
                        ->orWhere('title', 'like', '%Messenger%')
                        ->orWhere('title', 'like', '%Instagram%')
                        ->orWhere('meta->channel', 'meta_social');
                })
                ->count();
            $metaLinked = max($metaLinked, $metaCrmNotes);
        }

        return [
            'whatsapp_crm' => $whatsappLinked,
            'whatsapp_outbound_linked' => $waOutboundLinked,
            'whatsapp_outbound_unlinked' => $waOutboundUnlinked,
            'meta_outbound_linked' => $metaLinked,
            'meta_outbound_unlinked' => $metaUnlinked,
            'followups' => $followups,
            'call_attempts' => $callAttempts,
            'calls_no_outcome' => $callsNoOutcome,
            'call_duration_seconds' => $callDuration,
            'social_linked_total' => $whatsappLinked + $metaLinked,
        ];
    }

    /**
     * @return array<string, int|float|null>
     */
    private function coldMetrics(int $userId, Carbon $from, Carbon $to): array
    {
        // المستلمة/المسندة في اليوم (إنشاء ضمن اليوم كأفضل تقريب متاح)
        $assignedToday = SalesLead::query()
            ->where('assigned_to', $userId)
            ->whereNotNull('import_batch')
            ->where('import_batch', '!=', '')
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $openCold = SalesLead::query()
            ->where('assigned_to', $userId)
            ->whereNotNull('import_batch')
            ->where('import_batch', '!=', '')
            ->openPipeline()
            ->count();

        $workedLeadIds = SalesActivity::query()
            ->where('user_id', $userId)
            ->whereNotNull('sales_lead_id')
            ->whereBetween('created_at', [$from, $to])
            ->pluck('sales_lead_id')
            ->unique();

        $coldWorked = SalesLead::query()
            ->where('assigned_to', $userId)
            ->whereNotNull('import_batch')
            ->where('import_batch', '!=', '')
            ->whereIn('id', $workedLeadIds)
            ->count();

        $pool = max($assignedToday, min($openCold, 30));
        $workedPct = $pool > 0 ? round($coldWorked / $pool * 100, 1) : ($coldWorked > 0 ? 100.0 : null);

        return [
            'assigned_today' => $assignedToday,
            'open_cold' => $openCold,
            'worked_today' => $coldWorked,
            'worked_pct' => $workedPct,
            'pool_for_pct' => $pool,
        ];
    }

    /**
     * @return array<string, int|float|null>
     */
    private function crmDisciplineMetrics(int $userId, Carbon $from, Carbon $to): array
    {
        $totalActs = SalesActivity::query()
            ->where('user_id', $userId)
            ->whereNotNull('sales_lead_id')
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $overdue = SalesLead::query()
            ->forAssignee($userId)
            ->openPipeline()
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<', now())
            ->count();

        $stale = SalesLead::query()
            ->forAssignee($userId)
            ->openPipeline()
            ->where(function ($q) {
                $d = SalesLead::STALE_CONTACT_DAYS;
                $q->where(function ($q2) use ($d) {
                    $q2->whereNull('last_contacted_at')
                        ->where('created_at', '<', now()->subDays($d));
                })->orWhere('last_contacted_at', '<', now()->subDays($d));
            })
            ->count();

        $calls = SalesActivity::query()
            ->where('user_id', $userId)
            ->where('type', 'call')
            ->whereNotNull('sales_lead_id')
            ->whereBetween('created_at', [$from, $to]);
        $callCount = (clone $calls)->count();
        $withOutcome = Schema::hasColumn('sales_activities', 'outcome')
            ? (clone $calls)->whereNotNull('outcome')->where('outcome', '!=', '')->count()
            : $callCount;
        $outcomePct = $callCount > 0 ? round($withOutcome / $callCount * 100, 1) : 100.0;

        return [
            'crm_activities' => $totalActs,
            'overdue_followups' => $overdue,
            'stale_open_leads' => $stale,
            'calls_with_outcome_pct' => $outcomePct,
            'calls_total' => $callCount,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function attendanceMetrics(User $employee, Carbon $date): array
    {
        $record = EmployeeAttendanceRecord::query()
            ->where('user_id', $employee->id)
            ->whereDate('work_date', $date->toDateString())
            ->first();

        $stateMode = null;
        try {
            $stateMode = $this->attendance->getState($employee)['mode'] ?? null;
        } catch (\Throwable) {
            $stateMode = null;
        }

        $presence = null;
        try {
            $board = $this->presence->teamPresenceBoard([$employee->id]);
            $presence = $board->firstWhere('user_id', $employee->id);
        } catch (\Throwable) {
            $presence = null;
        }

        try {
            $isOff = $employee->isAttendanceOffDay($date) || $employee->isAttendanceExcused($date);
        } catch (\Throwable) {
            $isOff = $employee->isWeeklyOff($date);
        }
        $clockedIn = (bool) ($record?->clock_in_at);
        $isLate = (bool) ($record?->is_late && ! $record?->late_penalty_waived);
        $isAbsent = ! $isOff && ! $clockedIn && $date->lt(today());
        $status = $isOff ? 'off' : ($record?->status ?? ($stateMode ?? 'unknown'));

        return [
            'is_off' => $isOff,
            'clocked_in' => $clockedIn,
            'is_late' => $isLate,
            'is_absent' => $isAbsent,
            'status' => $status,
            'worked_minutes' => (int) ($record?->worked_minutes ?? 0),
            'required_minutes' => (int) ($record?->required_minutes ?? 0),
            'presence_status' => $presence['status'] ?? null,
            'offline_minutes' => isset($presence['offline_seconds'])
                ? (int) round(((int) $presence['offline_seconds']) / 60)
                : 0,
        ];
    }

    /**
     * @return array{crm_declared_paid: int, crm_declared_revenue: float, finance_verified_paid: int, finance_verified_revenue: float}
     */
    private function financialMetrics(int $userId, Carbon $from, Carbon $to): array
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

        $financeVerified = $paidLeads->filter(fn ($l) => filled($l->payment_txn_ref) || filled($l->won_confirmed_at))->count();
        $financeRevenue = (float) $paidLeads
            ->filter(fn ($l) => filled($l->payment_txn_ref) || filled($l->won_confirmed_at))
            ->sum(fn ($l) => (float) ($l->payment_amount ?: $l->expected_value ?: 0));

        return [
            'crm_declared_paid' => $crmDeclared,
            'crm_declared_revenue' => $crmRevenue,
            'finance_verified_paid' => $financeVerified,
            'finance_verified_revenue' => $financeRevenue,
        ];
    }

    /**
     * @param  array<string, mixed>  $sos
     * @param  array<string, mixed>  $channels
     * @param  array<string, mixed>  $cold
     * @param  array<string, mixed>  $crm
     * @param  array<string, mixed>  $attendance
     * @param  array<string, mixed>  $financial
     * @param  array<string, mixed>  $targets
     * @return array<string, array{score: float, label: string, details: list<string>}>
     */
    private function scorePillars(
        array $sos,
        array $channels,
        array $cold,
        array $crm,
        array $attendance,
        ?SalesDailyReport $dailyReport,
        array $financial,
        array $targets,
    ): array {
        $results = $this->mean([
            $this->up((float) ($sos['paid_enrollments_daily'] ?? 0), (float) ($targets['paid_enrollments_daily'] ?? $targets['paid_enrollments'] ?? 1)),
            $this->up((float) ($sos['qualified_conversations_daily'] ?? 0), (float) ($targets['qualified_conversations_daily'] ?? $targets['qualified'] ?? 8)),
            $this->up((float) ($sos['proposals_daily'] ?? 0), (float) ($targets['proposals_daily'] ?? 5)),
            $this->up((float) ($financial['finance_verified_paid'] ?? 0), max(1.0, (float) ($targets['paid_enrollments'] ?? 1))),
        ]);

        $activity = $this->mean([
            $this->up((float) ($sos['call_attempts_daily'] ?? 0), (float) ($targets['call_attempts_daily'] ?? $targets['call_attempts'] ?? 50)),
            $this->up((float) ($sos['calls_answered_daily'] ?? 0), (float) ($targets['calls_answered_daily'] ?? $targets['calls_answered'] ?? 20)),
            $this->up((float) ($sos['discovery_sessions_daily'] ?? 0), (float) ($targets['discovery_sessions_daily'] ?? $targets['meetings'] ?? 3)),
            $this->up((float) ($channels['followups'] ?? 0), (float) ($targets['followups'] ?? 10)),
            $this->up((float) ($channels['social_linked_total'] ?? 0), (float) ($targets['whatsapp_linked'] ?? 15)),
            $cold['worked_pct'] !== null
                ? $this->up((float) $cold['worked_pct'], (float) ($targets['cold_worked_pct'] ?? 70))
                : 70.0,
        ]);

        $answered = (float) ($sos['calls_answered_daily'] ?? 0);
        $attempts = max(1.0, (float) ($sos['call_attempts_daily'] ?? 0));
        $answerRate = $attempts > 0 ? ($answered / $attempts * 100) : 0.0;
        $quality = $this->mean([
            $this->up($answerRate, 40.0),
            $this->up((float) ($crm['calls_with_outcome_pct'] ?? 100), (float) ($targets['calls_with_outcome_pct'] ?? 90)),
            $this->up((float) ($sos['qualified_conversations_daily'] ?? 0), max(1.0, $answered * 0.4)),
        ]);

        $crmDiscipline = $this->mean([
            $this->up((float) ($crm['crm_activities'] ?? 0), (float) ($targets['crm_activities'] ?? 20)),
            $this->down((float) ($crm['overdue_followups'] ?? 0), (float) ($targets['overdue_followups_max'] ?? 2)),
            $this->down((float) ($crm['stale_open_leads'] ?? 0), 5.0),
        ]);

        if ($attendance['is_off'] ?? false) {
            $attScore = 100.0;
            $attDetails = ['يوم راحة / إجازة'];
        } else {
            $attParts = [];
            if ($attendance['is_absent'] ?? false) {
                $attParts[] = 0.0;
            } elseif ($attendance['clocked_in'] ?? false) {
                $attParts[] = ($attendance['is_late'] ?? false) ? 70.0 : 100.0;
            } else {
                // اليوم الحالي قبل انتهاء الدوام
                $attParts[] = 80.0;
            }
            $attParts[] = ($dailyReport?->isSubmitted()) ? 100.0 : 0.0;
            $offline = (int) ($attendance['offline_minutes'] ?? 0);
            $attParts[] = $offline >= 30 ? 40.0 : ($offline >= 15 ? 70.0 : 100.0);
            $attScore = $this->mean($attParts);
            $attDetails = [
                ($attendance['clocked_in'] ?? false) ? 'حضور مسجّل' : 'لم يُسجّل حضور',
                ($dailyReport?->isSubmitted()) ? 'تقرير يومي مسلّم' : 'تقرير يومي غير مسلّم',
            ];
        }

        return [
            'results' => [
                'score' => round($results, 1),
                'label' => 'النتائج 35٪ — تسجيلات، مؤهل، عروض، دفع مؤكد',
                'details' => [
                    'مدفوع CRM: '.($sos['paid_enrollments_daily'] ?? 0),
                    'دفع مؤكد: '.($financial['finance_verified_paid'] ?? 0),
                    'مؤهل: '.($sos['qualified_conversations_daily'] ?? 0),
                ],
            ],
            'activity' => [
                'score' => round($activity, 1),
                'label' => 'النشاط الموثّق 25٪ — مكالمات، متابعات، سوشيال مرتبط، كولد',
                'details' => [
                    'محاولات: '.($sos['call_attempts_daily'] ?? 0),
                    'واتساب/سوشيال مرتبط: '.($channels['social_linked_total'] ?? 0),
                    'كولد تم العمل عليه: '.($cold['worked_today'] ?? 0),
                ],
            ],
            'quality' => [
                'score' => round($quality, 1),
                'label' => 'الجودة 15٪ — نسبة الرد، outcome، تأهيل',
                'details' => [
                    'نسبة رد: '.round($answerRate, 1).'%',
                    'مكالمات بـ outcome: '.($crm['calls_with_outcome_pct'] ?? 0).'%',
                ],
            ],
            'crm_discipline' => [
                'score' => round($crmDiscipline, 1),
                'label' => 'التزام CRM 15٪ — أنشطة، متابعات، تحديث بيانات',
                'details' => [
                    'أنشطة: '.($crm['crm_activities'] ?? 0),
                    'متابعات متأخرة: '.($crm['overdue_followups'] ?? 0),
                    'راكد: '.($crm['stale_open_leads'] ?? 0),
                ],
            ],
            'attendance' => [
                'score' => round($attScore, 1),
                'label' => 'حضور وتقرير يومي 10٪',
                'details' => $attDetails,
            ],
        ];
    }

    /**
     * @return list<array{code: string, label: string, count: int}>
     */
    private function collectExceptions(int $userId, Carbon $from, Carbon $to, array $channels, array $cold, array $crm): array
    {
        $list = [];

        if (($channels['whatsapp_outbound_unlinked'] ?? 0) > 0) {
            $list[] = [
                'code' => 'wa_unlinked',
                'label' => 'رسائل واتساب صادرة غير مرتبطة بعميل CRM',
                'count' => (int) $channels['whatsapp_outbound_unlinked'],
            ];
        }
        if (($channels['meta_outbound_unlinked'] ?? 0) > 0) {
            $list[] = [
                'code' => 'meta_unlinked',
                'label' => 'ردود سوشيال غير مرتبطة بعميل CRM',
                'count' => (int) $channels['meta_outbound_unlinked'],
            ];
        }
        if (($channels['calls_no_outcome'] ?? 0) > 0) {
            $list[] = [
                'code' => 'call_no_outcome',
                'label' => 'مكالمات بدون نتيجة (outcome)',
                'count' => (int) $channels['calls_no_outcome'],
            ];
        }
        if (($cold['assigned_today'] ?? 0) > 0 && ($cold['worked_today'] ?? 0) === 0) {
            $list[] = [
                'code' => 'cold_idle',
                'label' => 'بيانات كولد مستلمة اليوم بلا أي نشاط CRM',
                'count' => (int) $cold['assigned_today'],
            ];
        }
        if (($crm['overdue_followups'] ?? 0) > 0) {
            $list[] = [
                'code' => 'overdue_followups',
                'label' => 'متابعات متأخرة في الأنبوب',
                'count' => (int) $crm['overdue_followups'],
            ];
        }

        $unconfirmedPaid = SalesLead::query()
            ->where('assigned_to', $userId)
            ->whereIn('stage', SalesLead::PAID_STAGES)
            ->whereBetween('closed_at', [$from, $to])
            ->whereNull('won_confirmed_at')
            ->where(function ($q) {
                $q->whereNull('payment_txn_ref')->orWhere('payment_txn_ref', '');
            })
            ->count();
        if ($unconfirmedPaid > 0) {
            $list[] = [
                'code' => 'payment_unverified',
                'label' => 'مدفوعات معلَنة في CRM بدون تأكيد',
                'count' => $unconfirmedPaid,
            ];
        }

        return $list;
    }

    /**
     * @return list<string>
     */
    private function missedPoints(
        array $pillars,
        array $targets,
        array $sos,
        array $channels,
        array $cold,
        array $crm,
        array $attendance,
        ?SalesDailyReport $dailyReport,
    ): array {
        $missed = [];
        foreach ($pillars as $key => $p) {
            if (($p['score'] ?? 100) < 70) {
                $missed[] = ($p['label'] ?? $key).' — '.$p['score'].'/100';
            }
        }
        if (! ($dailyReport?->isSubmitted()) && ! ($attendance['is_off'] ?? false)) {
            $missed[] = 'التقرير اليومي غير مسلّم';
        }
        if (($channels['whatsapp_outbound_unlinked'] ?? 0) > 0) {
            $missed[] = 'نشاط واتساب غير مرتبط لا يدخل الدرجة';
        }
        if (($sos['call_attempts_daily'] ?? 0) < ($targets['call_attempts_daily'] ?? $targets['call_attempts'] ?? 50) * 0.5) {
            $missed[] = 'محاولات الاتصال أقل من نصف الهدف';
        }

        return $missed;
    }

    private function suggestRecommendation(float $score, array $exceptions, array $attendance): string
    {
        if ($attendance['is_absent'] ?? false) {
            return 'warning';
        }
        if ($score >= 90) {
            return 'bonus';
        }
        if ($score >= 80) {
            return 'praise';
        }
        if ($score < 45 || count($exceptions) >= 3) {
            return 'deduction';
        }
        if ($score < 65) {
            return 'coaching';
        }

        return 'none';
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array<string, mixed>
     */
    private function summarize(array $rows): array
    {
        $n = count($rows);

        return [
            'members' => $n,
            'avg_score' => $n ? round(collect($rows)->avg('verified_score'), 1) : 0.0,
            'below_target' => collect($rows)->filter(fn ($r) => $r['verified_score'] < 65)->count(),
            'critical' => collect($rows)->filter(fn ($r) => $r['verified_score'] < 45)->count(),
            'call_attempts' => (int) collect($rows)->sum(fn ($r) => $r['sos']['call_attempts_daily'] ?? 0),
            'calls_answered' => (int) collect($rows)->sum(fn ($r) => $r['sos']['calls_answered_daily'] ?? 0),
            'paid' => (int) collect($rows)->sum(fn ($r) => $r['sos']['paid_enrollments_daily'] ?? 0),
            'finance_verified_paid' => (int) collect($rows)->sum(fn ($r) => $r['financial']['finance_verified_paid'] ?? 0),
            'wa_unlinked' => (int) collect($rows)->sum(fn ($r) => $r['channels']['whatsapp_outbound_unlinked'] ?? 0),
            'meta_unlinked' => (int) collect($rows)->sum(fn ($r) => $r['channels']['meta_outbound_unlinked'] ?? 0),
            'reports_submitted' => collect($rows)->filter(fn ($r) => $r['daily_report_submitted'])->count(),
            'exceptions_total' => (int) collect($rows)->sum(fn ($r) => count($r['exceptions'])),
            'approved_reviews' => collect($rows)->filter(fn ($r) => $r['review']?->isApproved())->count(),
        ];
    }

    private function rowMatchesChannel(array $row, string $channel): bool
    {
        return match ($channel) {
            'calls' => ($row['sos']['call_attempts_daily'] ?? 0) > 0,
            'whatsapp' => (($row['channels']['whatsapp_crm'] ?? 0) + ($row['channels']['whatsapp_outbound_linked'] ?? 0) + ($row['channels']['whatsapp_outbound_unlinked'] ?? 0)) > 0,
            'social' => (($row['channels']['meta_outbound_linked'] ?? 0) + ($row['channels']['meta_outbound_unlinked'] ?? 0)) > 0,
            'cold' => (($row['cold']['assigned_today'] ?? 0) + ($row['cold']['worked_today'] ?? 0) + ($row['cold']['open_cold'] ?? 0)) > 0,
            'exceptions' => count($row['exceptions']) > 0,
            default => true,
        };
    }

    private function verifiedActivities(int $userId, Carbon $from, Carbon $to): Collection
    {
        return SalesActivity::query()
            ->where('user_id', $userId)
            ->whereNotNull('sales_lead_id')
            ->whereBetween('created_at', [$from, $to])
            ->with(['lead' => fn ($q) => $q->withTrashed()->select('id', 'name', 'phone', 'stage', 'import_batch')])
            ->orderByDesc('created_at')
            ->limit(80)
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
            ->values();

        return SalesLead::query()
            ->withTrashed()
            ->whereIn('id', $ids)
            ->get(['id', 'name', 'phone', 'stage', 'source', 'import_batch', 'expected_value', 'assigned_to']);
    }

    private function up(float $actual, float $target): float
    {
        if ($target <= 0) {
            return $actual > 0 ? 100.0 : 50.0;
        }

        return min(100.0, max(0.0, $actual / $target * 100));
    }

    private function down(float $actual, float $maxAcceptable): float
    {
        if ($maxAcceptable <= 0) {
            return $actual <= 0 ? 100.0 : 0.0;
        }
        if ($actual <= $maxAcceptable) {
            return 100.0;
        }

        return min(100.0, max(0.0, $maxAcceptable / max($actual, 0.01) * 100));
    }

    /**
     * @param  list<float>  $vals
     */
    private function mean(array $vals): float
    {
        $vals = array_values(array_filter($vals, fn ($v) => is_numeric($v)));
        if ($vals === []) {
            return 50.0;
        }

        return array_sum($vals) / count($vals);
    }

    /**
     * حفظ/تحديث مراجعة المدير بدون إنشاء خصم مالي.
     *
     * @param  array<string, mixed>  $data
     */
    public function saveReview(
        User $manager,
        SalesTeam $team,
        User $employee,
        Carbon $date,
        array $employeeRow,
        array $data,
    ): SalesManagerDailyReview {
        $status = $data['status'] ?? SalesManagerDailyReview::STATUS_REVIEWED;
        if (! in_array($status, [
            SalesManagerDailyReview::STATUS_DRAFT,
            SalesManagerDailyReview::STATUS_REVIEWED,
            SalesManagerDailyReview::STATUS_APPROVED,
        ], true)) {
            $status = SalesManagerDailyReview::STATUS_REVIEWED;
        }

        $recommendation = $data['recommendation'] ?? ($employeeRow['suggested_recommendation'] ?? 'none');
        $allowed = array_keys(config('sales_manager_scorecard.recommendations', []));
        if (! in_array($recommendation, $allowed, true)) {
            $recommendation = 'none';
        }

        $payload = [
            'sales_team_id' => $team->id,
            'manager_id' => $manager->id,
            'status' => $status,
            'verified_score' => $employeeRow['verified_score'],
            'score_snapshot' => $employeeRow['snapshot'],
            'recommendation' => $recommendation,
            'proposed_deduction_amount' => $recommendation === 'deduction'
                ? ($data['proposed_deduction_amount'] ?? null)
                : null,
            'manager_notes' => $data['manager_notes'] ?? null,
            'reviewed_at' => now(),
            'approved_at' => $status === SalesManagerDailyReview::STATUS_APPROVED ? now() : null,
        ];

        return SalesManagerDailyReview::query()->updateOrCreate(
            [
                'employee_id' => $employee->id,
                'work_date' => $date->toDateString(),
            ],
            $payload
        );
    }
}
