<?php

namespace App\Services;

use App\Models\EmployeeAttendanceRecord;
use App\Models\EmployeePresenceDaily;
use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\SalesShiftSwapRequest;
use App\Models\SalesTeam;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * تجميع داشبورد مدير المبيعات من CRM / SOS / Presence / Attendance / Shifts.
 */
class SalesManagerHubService
{
    public function __construct(
        private SalesDailyResultService $dailyResults,
        private SalesKpiService $kpi,
        private SalesManagerOpsBoardService $ops,
        private SalesShiftScheduleService $shifts,
        private EmployeePresenceService $presence,
    ) {}

    /**
     * @param  list<int>  $memberIds
     * @return array<string, mixed>
     */
    public function build(SalesTeam $team, array $memberIds, ?Carbon $date = null, ?int $compareA = null, ?int $compareB = null): array
    {
        $date = ($date ?? today())->copy()->startOfDay();
        $from = $date->copy()->startOfDay();
        $to = $date->copy()->endOfDay();
        $monthStart = $date->copy()->startOfMonth();
        $monthEnd = $date->copy()->endOfMonth()->endOfDay();

        $members = User::query()
            ->whereIn('id', $memberIds)
            ->where('is_active', true)
            ->with(['workSchedule', 'employeeJob'])
            ->orderBy('name')
            ->get()
            ->keyBy('id');

        $opsBoard = $this->ops->build($memberIds, $date);
        $opsRows = collect($opsBoard['rows'])->keyBy('user_id');
        $shiftLive = $this->shifts->buildTeamLivePanel($memberIds);
        $shiftBoard = $this->shifts->buildWeekBoard(null, null, null, $memberIds);

        $ranking = $this->buildRanking($members, $from, $to, $monthStart, $monthEnd, $opsRows);
        $kpis = $this->buildTeamKpis($memberIds, $ranking, $opsBoard['stats'] ?? [], $from, $to, $monthStart, $monthEnd);
        $liveStatus = $this->buildLiveStatus($members, $opsRows, $shiftLive, $from, $to);
        $pipeline = $this->buildPipeline($memberIds);
        $tasks = $this->buildTasks($memberIds);
        $timeline = $this->buildTeamTimeline($memberIds, $from, $to, 40);
        $alerts = $this->buildAlerts($ranking, $opsRows, $tasks, $memberIds, $from, $to);
        $approvals = $this->buildApprovals($memberIds, $opsBoard);
        $attendance = $this->buildAttendanceSummary($memberIds, $date, $opsBoard);
        $analytics = $this->buildWeekAnalytics($memberIds, $date);
        $leaderboard = $this->buildLeaderboard($ranking);
        $compare = $this->buildCompare($ranking, $compareA, $compareB);

        $pendingShiftSwaps = (int) ($approvals['shift_swaps'] ?? 0);
        $memberShiftToday = [];
        foreach ($memberIds as $mid) {
            $memberShiftToday[$mid] = $this->shifts->memberShiftToday($mid);
        }

        return [
            'date' => $date,
            'team' => $team,
            'members' => $members->values(),
            'kpis' => $kpis,
            'live_status' => $liveStatus,
            'ranking' => $ranking,
            'pipeline' => $pipeline,
            'tasks' => $tasks,
            'timeline' => $timeline,
            'alerts' => $alerts,
            'approvals' => $approvals,
            'attendance' => $attendance,
            'analytics' => $analytics,
            'leaderboard' => $leaderboard,
            'compare' => $compare,
            'shift_live' => $shiftLive,
            'shift_board' => $shiftBoard,
            'pending_shift_swaps' => $pendingShiftSwaps,
            'member_shift_today' => $memberShiftToday,
            'ops_stats' => $opsBoard['stats'] ?? [],
        ];
    }

    /**
     * ملخص يوم موظف واحد (لصفحة الملف).
     *
     * @return array<string, mixed>
     */
    public function employeeToday(User $employee, ?Carbon $date = null): array
    {
        $date = ($date ?? today())->copy()->startOfDay();
        $from = $date->copy()->startOfDay();
        $to = $date->copy()->endOfDay();

        $sos = $this->dailyResults->comparisonFor($employee, $date);
        $metrics = $sos['metrics'] ?? [];
        $month = $this->kpi->metricsForPeriod($employee->id, $date->copy()->startOfMonth(), $to);

        $activities = SalesActivity::query()
            ->with('lead:id,name')
            ->where('user_id', $employee->id)
            ->whereBetween('created_at', [$from, $to])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(fn (SalesActivity $a) => $this->mapActivity($a));

        $avgCallSeconds = (int) SalesActivity::query()
            ->where('user_id', $employee->id)
            ->where('type', 'call')
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('duration_seconds')
            ->avg('duration_seconds');

        $followupsPending = SalesLead::query()
            ->where('assigned_to', $employee->id)
            ->openPipeline()
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '>=', now())
            ->count();

        $followupsOverdue = SalesLead::query()
            ->where('assigned_to', $employee->id)
            ->openPipeline()
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<', now())
            ->count();

        return [
            'date' => $date,
            'metrics' => [
                'calls' => (int) ($metrics['call_attempts_daily'] ?? 0),
                'answered' => (int) ($metrics['calls_answered_daily'] ?? 0),
                'qualified' => (int) ($metrics['qualified_conversations_daily'] ?? 0),
                'meetings' => (int) ($metrics['discovery_sessions_daily'] ?? 0),
                'proposals' => (int) ($metrics['proposals_daily'] ?? 0),
                'closed' => (int) ($metrics['paid_enrollments_daily'] ?? 0),
                'revenue' => (float) ($month['revenue_closed'] ?? 0),
                'avg_call_seconds' => $avgCallSeconds,
                'avg_response_minutes' => $month['avg_response_minutes'] ?? null,
                'followups_pending' => $followupsPending,
                'followups_overdue' => $followupsOverdue,
            ],
            'sos' => $sos,
            'activities' => $activities,
            'shift' => $this->shifts->memberShiftToday($employee->id),
        ];
    }

    /**
     * @param  Collection<int, User>  $members
     * @param  Collection<int, array<string, mixed>>  $opsRows
     * @return list<array<string, mixed>>
     */
    private function buildRanking(Collection $members, Carbon $from, Carbon $to, Carbon $monthStart, Carbon $monthEnd, Collection $opsRows): array
    {
        $rows = [];
        foreach ($members as $member) {
            $sos = $this->dailyResults->comparisonFor($member, $from);
            $m = $sos['metrics'] ?? [];
            $monthMetrics = $this->kpi->metricsForPeriod($member->id, $monthStart, min($to, $monthEnd));
            $ops = $opsRows->get($member->id) ?? [];

            $targetPct = (float) ($sos['overall_pct'] ?? 0);
            $stars = $this->starsFromPct($targetPct);

            $rows[] = [
                'user_id' => (int) $member->id,
                'name' => $member->name,
                'calls' => (int) ($m['call_attempts_daily'] ?? 0),
                'answered' => (int) ($m['calls_answered_daily'] ?? 0),
                'qualified' => (int) ($m['qualified_conversations_daily'] ?? 0),
                'meetings' => (int) ($m['discovery_sessions_daily'] ?? 0),
                'proposals' => (int) ($m['proposals_daily'] ?? 0),
                'deals' => (int) ($m['paid_enrollments_daily'] ?? 0),
                'revenue' => (float) ($monthMetrics['revenue_closed'] ?? 0),
                'revenue_today' => $this->revenueForPeriod((int) $member->id, $from, $to),
                'won_month' => (int) ($monthMetrics['won_closed'] ?? 0),
                'lost_month' => (int) ($monthMetrics['lost_closed'] ?? 0),
                'conversion_pct' => $monthMetrics['conversion_pct'] ?? null,
                'avg_response_minutes' => $monthMetrics['avg_response_minutes'] ?? null,
                'target_pct' => $targetPct,
                'status' => $sos['status'] ?? 'behind',
                'status_label' => $sos['status_label'] ?? '',
                'score' => $targetPct,
                'stars' => $stars,
                'presence_status' => $ops['presence_status'] ?? 'offline',
                'presence_label' => $ops['presence_label'] ?? 'غير متصل',
                'presence_color' => $ops['presence_color'] ?? 'slate',
                'calls_today_ops' => (int) ($ops['calls_today'] ?? 0),
                'last_activity_at' => null,
            ];
        }

        usort($rows, function ($a, $b) {
            if ($b['score'] !== $a['score']) {
                return $b['score'] <=> $a['score'];
            }

            return $b['deals'] <=> $a['deals'];
        });

        foreach ($rows as $i => &$row) {
            $row['rank'] = $i + 1;
        }
        unset($row);

        return $rows;
    }

    /**
     * @param  list<array<string, mixed>>  $ranking
     * @param  array<string, int>  $opsStats
     * @return array<string, mixed>
     */
    private function buildTeamKpis(array $memberIds, array $ranking, array $opsStats, Carbon $from, Carbon $to, Carbon $monthStart, Carbon $monthEnd): array
    {
        $sum = fn (string $key) => (int) collect($ranking)->sum($key);
        $sumF = fn (string $key) => (float) collect($ranking)->sum($key);

        $wonToday = SalesLead::query()
            ->whereIn('assigned_to', $memberIds)
            ->where('stage', SalesLead::WON_STAGE)
            ->whereNotNull('closed_at')
            ->whereBetween('closed_at', [$from, $to])
            ->count();

        $lostToday = SalesLead::query()
            ->whereIn('assigned_to', $memberIds)
            ->where('stage', 'lost')
            ->whereNotNull('closed_at')
            ->whereBetween('closed_at', [$from, $to])
            ->count();

        $revenueToday = (float) SalesLead::query()
            ->whereIn('assigned_to', $memberIds)
            ->where('stage', SalesLead::WON_STAGE)
            ->whereNotNull('closed_at')
            ->whereBetween('closed_at', [$from, $to])
            ->sum('expected_value');

        $revenueMonth = (float) SalesLead::query()
            ->whereIn('assigned_to', $memberIds)
            ->where('stage', SalesLead::WON_STAGE)
            ->whereNotNull('closed_at')
            ->whereBetween('closed_at', [$monthStart, $monthEnd])
            ->sum('expected_value');

        $newMonth = SalesLead::query()
            ->whereIn('assigned_to', $memberIds)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        $wonMonth = SalesLead::query()
            ->whereIn('assigned_to', $memberIds)
            ->where('stage', SalesLead::WON_STAGE)
            ->whereNotNull('closed_at')
            ->whereBetween('closed_at', [$monthStart, $monthEnd])
            ->count();

        $conversion = $newMonth > 0 ? round($wonMonth / $newMonth * 100, 1) : null;

        $responseVals = collect($ranking)
            ->pluck('avg_response_minutes')
            ->filter(fn ($v) => $v !== null)
            ->values();
        $avgResponse = $responseVals->isNotEmpty() ? round((float) $responseVals->avg(), 1) : null;

        $avgTarget = count($ranking) > 0
            ? round(collect($ranking)->avg('target_pct'), 1)
            : 0.0;

        return [
            'team_members' => count($memberIds),
            'online_now' => (int) ($opsStats['online_presence'] ?? 0),
            'calls_today' => $sum('calls'),
            'answered_today' => $sum('answered'),
            'qualified_today' => $sum('qualified'),
            'meetings_today' => $sum('meetings'),
            'proposals_today' => $sum('proposals'),
            'won_today' => $wonToday,
            'lost_today' => $lostToday,
            'deals_today' => $sum('deals'),
            'revenue_today' => $revenueToday,
            'revenue_month' => $revenueMonth,
            'target_pct' => $avgTarget,
            'conversion_pct' => $conversion,
            'avg_response_minutes' => $avgResponse,
            'working_now' => (int) ($opsStats['working'] ?? 0),
        ];
    }

    /**
     * @param  Collection<int, User>  $members
     * @param  Collection<int, array<string, mixed>>  $opsRows
     * @param  array<string, mixed>  $shiftLive
     * @return list<array<string, mixed>>
     */
    private function buildLiveStatus(Collection $members, Collection $opsRows, array $shiftLive, Carbon $from, Carbon $to): array
    {
        $recentByUser = SalesActivity::query()
            ->with('lead:id,name')
            ->whereIn('user_id', $members->keys()->all())
            ->whereBetween('created_at', [$from, $to])
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('user_id');

        $activeShift = collect($shiftLive['active_now'] ?? [])->keyBy('user_id');

        $rows = [];
        foreach ($members as $member) {
            $ops = $opsRows->get($member->id) ?? [];
            $events = ($recentByUser->get($member->id) ?? collect())
                ->take(5)
                ->map(fn (SalesActivity $a) => $this->mapActivity($a))
                ->values()
                ->all();

            $status = $ops['presence_status'] ?? 'offline';
            $lastType = $events[0]['type'] ?? null;
            $displayStatus = match (true) {
                $lastType === 'meeting' && $status === 'online' => 'meeting',
                $lastType === 'call' && $status === 'online' => 'on_call',
                default => $status,
            };

            $rows[] = [
                'user_id' => (int) $member->id,
                'name' => $member->name,
                'presence_status' => $status,
                'display_status' => $displayStatus,
                'display_label' => $this->liveStatusLabel($displayStatus),
                'presence_label' => $ops['presence_label'] ?? 'غير متصل',
                'presence_color' => $ops['presence_color'] ?? 'slate',
                'last_seen_human' => $ops['last_seen_human'] ?? null,
                'shift_channels' => $activeShift->get($member->id)['channels_label'] ?? null,
                'events' => $events,
            ];
        }

        usort($rows, function ($a, $b) {
            $order = ['on_call' => 0, 'meeting' => 1, 'online' => 2, 'away' => 3, 'offline' => 4];

            return ($order[$a['display_status']] ?? 9) <=> ($order[$b['display_status']] ?? 9);
        });

        return $rows;
    }

    private function liveStatusLabel(string $status): string
    {
        return match ($status) {
            'on_call' => 'في مكالمة (آخر نشاط)',
            'meeting' => 'اجتماع (آخر نشاط)',
            'online' => 'متصل',
            'away' => 'بعيد',
            'offline' => 'غير متصل',
            default => $status,
        };
    }

    /**
     * @param  list<int>  $memberIds
     * @return array<string, int>
     */
    private function buildPipeline(array $memberIds): array
    {
        $counts = SalesLead::query()
            ->whereIn('assigned_to', $memberIds)
            ->selectRaw('stage, count(*) as c')
            ->groupBy('stage')
            ->pluck('c', 'stage')
            ->all();

        $map = [
            'new' => ['new_lead'],
            'contacted' => ['first_contact', 'no_answer', 'connected'],
            'qualified' => ['qualification', 'interested'],
            'meeting' => ['follow_up_scheduled', 'objection'],
            'proposal' => ['offer_sent', 'payment_pending'],
            'negotiation' => ['payment_received'],
            'won' => [SalesLead::WON_STAGE, 'upsell'],
            'lost' => ['lost', 'dormant'],
        ];

        $out = [];
        foreach ($map as $key => $stages) {
            $out[$key] = 0;
            foreach ($stages as $stage) {
                $out[$key] += (int) ($counts[$stage] ?? 0);
            }
        }

        $out['raw'] = $counts;

        return $out;
    }

    /**
     * @param  list<int>  $memberIds
     * @return array<string, mixed>
     */
    private function buildTasks(array $memberIds): array
    {
        $open = SalesLead::query()
            ->whereIn('assigned_to', $memberIds)
            ->openPipeline()
            ->whereNotNull('next_follow_up_at');

        $today = (clone $open)->whereDate('next_follow_up_at', today())->count();
        $overdue = (clone $open)->where('next_follow_up_at', '<', now())->count();
        $pending = (clone $open)->where('next_follow_up_at', '>=', now())->count();

        $completedToday = SalesActivity::query()
            ->whereIn('user_id', $memberIds)
            ->where('type', 'follow_up')
            ->whereDate('created_at', today())
            ->count();

        return [
            'followups_today' => $today,
            'completed_today' => $completedToday,
            'pending' => $pending,
            'overdue' => $overdue,
        ];
    }

    /**
     * @param  list<int>  $memberIds
     * @return list<array<string, mixed>>
     */
    private function buildTeamTimeline(array $memberIds, Carbon $from, Carbon $to, int $limit = 40): array
    {
        return SalesActivity::query()
            ->with(['user:id,name', 'lead:id,name'])
            ->whereIn('user_id', $memberIds)
            ->whereBetween('created_at', [$from, $to])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn (SalesActivity $a) => array_merge($this->mapActivity($a), [
                'user_name' => $a->user->name ?? '—',
            ]))
            ->all();
    }

    /**
     * @param  list<array<string, mixed>>  $ranking
     * @param  Collection<int, array<string, mixed>>  $opsRows
     * @param  array<string, mixed>  $tasks
     * @param  list<int>  $memberIds
     * @return list<array<string, mixed>>
     */
    private function buildAlerts(array $ranking, Collection $opsRows, array $tasks, array $memberIds, Carbon $from, Carbon $to): array
    {
        $alerts = [];
        $twoHoursAgo = now()->subHours(2);

        foreach ($ranking as $row) {
            $uid = (int) $row['user_id'];
            $ops = $opsRows->get($uid) ?? [];

            $lastCall = SalesActivity::query()
                ->where('user_id', $uid)
                ->where('type', 'call')
                ->whereBetween('created_at', [$from, $to])
                ->max('created_at');

            $isWorking = in_array($ops['attendance_filter'] ?? '', ['working', 'late'], true);
            if ($isWorking && ($row['calls'] === 0 || ($lastCall && Carbon::parse($lastCall)->lt($twoHoursAgo)))) {
                $alerts[] = [
                    'level' => 'danger',
                    'user_id' => $uid,
                    'user_name' => $row['name'],
                    'message' => $row['calls'] === 0
                        ? "{$row['name']}: لا مكالمات مسجّلة اليوم أثناء الدوام"
                        : "{$row['name']}: لا مكالمات منذ أكثر من ساعتين",
                ];
            }

            if (($ops['pending_approval'] ?? false) && ($ops['is_late'] ?? false)) {
                $alerts[] = [
                    'level' => 'warning',
                    'user_id' => $uid,
                    'user_name' => $row['name'],
                    'message' => "{$row['name']}: متأخر وبانتظار اعتماد الحضور",
                ];
            }

            if (($row['avg_response_minutes'] ?? null) !== null && $row['avg_response_minutes'] > 30) {
                $alerts[] = [
                    'level' => 'warning',
                    'user_id' => $uid,
                    'user_name' => $row['name'],
                    'message' => "{$row['name']}: متوسط زمن الرد مرتفع ({$row['avg_response_minutes']} د)",
                ];
            }

            if (($row['conversion_pct'] ?? null) !== null && $row['conversion_pct'] < 10 && ($row['won_month'] + $row['lost_month']) >= 3) {
                $alerts[] = [
                    'level' => 'warning',
                    'user_id' => $uid,
                    'user_name' => $row['name'],
                    'message' => "{$row['name']}: Conversion منخفض هذا الشهر ({$row['conversion_pct']}%)",
                ];
            }
        }

        $stale = SalesLead::query()
            ->whereIn('assigned_to', $memberIds)
            ->openPipeline()
            ->where(function ($q) {
                $d = SalesLead::STALE_CONTACT_DAYS;
                $q->where(function ($q2) use ($d) {
                    $q2->whereNull('last_contacted_at')
                        ->where('created_at', '<', now()->subDays($d));
                })->orWhere('last_contacted_at', '<', now()->subDays($d));
            })
            ->count();

        if ($stale > 0) {
            $alerts[] = [
                'level' => 'warning',
                'user_id' => null,
                'user_name' => null,
                'message' => "{$stale} عميل راكد بلا تواصل (≥ ".SalesLead::STALE_CONTACT_DAYS.' أيام)',
            ];
        }

        if (($tasks['overdue'] ?? 0) > 0) {
            $alerts[] = [
                'level' => 'danger',
                'user_id' => null,
                'user_name' => null,
                'message' => "{$tasks['overdue']} متابعة متأخرة على الفريق",
            ];
        }

        return array_slice($alerts, 0, 12);
    }

    /**
     * @param  list<int>  $memberIds
     * @param  array<string, mixed>  $opsBoard
     * @return array<string, int>
     */
    private function buildApprovals(array $memberIds, array $opsBoard): array
    {
        $attendancePending = (int) ($opsBoard['stats']['pending_approval'] ?? 0);

        $swaps = 0;
        if (Schema::hasTable('sales_shift_swap_requests')) {
            $swaps = SalesShiftSwapRequest::query()
                ->where('status', SalesShiftSwapRequest::STATUS_PENDING)
                ->where(function ($q) use ($memberIds) {
                    $q->whereIn('requester_id', $memberIds)->orWhereIn('partner_id', $memberIds);
                })
                ->count();
        }

        $waQueue = 0;
        try {
            $waQueue = app(WhatsAppQueueService::class)->pendingCount();
        } catch (\Throwable) {
            $waQueue = 0;
        }

        return [
            'attendance' => $attendancePending,
            'shift_swaps' => $swaps,
            'whatsapp_queue' => $waQueue,
            'total' => $attendancePending + $swaps + $waQueue,
        ];
    }

    /**
     * @param  list<int>  $memberIds
     * @param  array<string, mixed>  $opsBoard
     * @return array<string, mixed>
     */
    private function buildAttendanceSummary(array $memberIds, Carbon $date, array $opsBoard): array
    {
        $records = EmployeeAttendanceRecord::query()
            ->whereIn('user_id', $memberIds)
            ->whereDate('work_date', $date->toDateString())
            ->get();

        $worked = (int) $records->sum(fn (EmployeeAttendanceRecord $r) => (int) ($r->worked_minutes ?? 0));
        if ($worked === 0) {
            foreach ($records as $r) {
                if ($r->clock_in_at && ! $r->clock_out_at) {
                    $worked += max(0, (int) $r->clock_in_at->diffInMinutes(now()));
                }
            }
        }

        $presence = Schema::hasTable('employee_presence_daily')
            ? EmployeePresenceDaily::query()
                ->whereIn('user_id', $memberIds)
                ->whereDate('work_date', $date->toDateString())
                ->get()
            : collect();

        $online = (int) $presence->sum('online_seconds');
        $away = (int) $presence->sum('away_seconds');
        $offline = (int) $presence->sum('offline_seconds');

        return [
            'working_minutes' => $worked,
            'working_label' => $this->minutesLabel($worked),
            'idle_minutes' => (int) round(($away + $offline) / 60),
            'idle_label' => $this->minutesLabel((int) round(($away + $offline) / 60)),
            'productive_minutes' => (int) round($online / 60),
            'productive_label' => $this->minutesLabel((int) round($online / 60)),
            'working_count' => (int) ($opsBoard['stats']['working'] ?? 0),
            'not_clocked_in' => (int) ($opsBoard['stats']['not_clocked_in'] ?? 0),
            'pending_approval' => (int) ($opsBoard['stats']['pending_approval'] ?? 0),
        ];
    }

    /**
     * @param  list<int>  $memberIds
     * @return array{labels: list<string>, calls: list<int>, meetings: list<int>, revenue: list<float>}
     */
    private function buildWeekAnalytics(array $memberIds, Carbon $date): array
    {
        $start = $date->copy()->startOfWeek(Carbon::SATURDAY);
        $labels = [];
        $calls = [];
        $meetings = [];
        $revenue = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i);
            $labels[] = $day->copy()->locale('ar')->translatedFormat('D d');
            $from = $day->copy()->startOfDay();
            $to = $day->copy()->endOfDay();

            $calls[] = SalesActivity::query()
                ->whereIn('user_id', $memberIds)
                ->where('type', 'call')
                ->whereBetween('created_at', [$from, $to])
                ->count();

            $meetings[] = SalesActivity::query()
                ->whereIn('user_id', $memberIds)
                ->where('type', 'meeting')
                ->whereBetween('created_at', [$from, $to])
                ->count();

            $revenue[] = (float) SalesLead::query()
                ->whereIn('assigned_to', $memberIds)
                ->where('stage', SalesLead::WON_STAGE)
                ->whereNotNull('closed_at')
                ->whereBetween('closed_at', [$from, $to])
                ->sum('expected_value');
        }

        return compact('labels', 'calls', 'meetings', 'revenue');
    }

    /**
     * @param  list<array<string, mixed>>  $ranking
     * @return array<string, mixed>
     */
    private function buildLeaderboard(array $ranking): array
    {
        $dayTop = $ranking[0] ?? null;
        $monthTop = collect($ranking)->sortByDesc('revenue')->first();

        return [
            'day' => $dayTop,
            'month' => $monthTop,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $ranking
     * @return array<string, mixed>|null
     */
    private function buildCompare(array $ranking, ?int $a, ?int $b): ?array
    {
        if (! $a || ! $b || $a === $b) {
            return null;
        }

        $byId = collect($ranking)->keyBy('user_id');
        $left = $byId->get($a);
        $right = $byId->get($b);
        if (! $left || ! $right) {
            return null;
        }

        return [
            'a' => $left,
            'b' => $right,
            'metrics' => [
                ['key' => 'calls', 'label' => 'مكالمات', 'a' => $left['calls'], 'b' => $right['calls']],
                ['key' => 'meetings', 'label' => 'اجتماعات', 'a' => $left['meetings'], 'b' => $right['meetings']],
                ['key' => 'qualified', 'label' => 'مؤهل', 'a' => $left['qualified'], 'b' => $right['qualified']],
                ['key' => 'deals', 'label' => 'صفقات', 'a' => $left['deals'], 'b' => $right['deals']],
                ['key' => 'revenue', 'label' => 'إيراد الشهر', 'a' => $left['revenue'], 'b' => $right['revenue']],
                ['key' => 'conversion_pct', 'label' => 'Conversion %', 'a' => $left['conversion_pct'], 'b' => $right['conversion_pct']],
                ['key' => 'avg_response_minutes', 'label' => 'متوسط الرد (د)', 'a' => $left['avg_response_minutes'], 'b' => $right['avg_response_minutes']],
                ['key' => 'target_pct', 'label' => 'تحقيق التارجت %', 'a' => $left['target_pct'], 'b' => $right['target_pct']],
            ],
        ];
    }

    private function mapActivity(SalesActivity $a): array
    {
        return [
            'id' => $a->id,
            'type' => $a->type,
            'type_label' => SalesActivity::typeLabel($a->type),
            'title' => $a->title ?: SalesActivity::typeLabel($a->type),
            'lead_name' => $a->lead->name ?? null,
            'outcome' => $a->outcome,
            'time' => $a->created_at?->format('H:i'),
            'at' => $a->created_at?->toDateTimeString(),
        ];
    }

    private function revenueForPeriod(int $userId, Carbon $from, Carbon $to): float
    {
        return (float) SalesLead::query()
            ->where('assigned_to', $userId)
            ->where('stage', SalesLead::WON_STAGE)
            ->whereNotNull('closed_at')
            ->whereBetween('closed_at', [$from, $to])
            ->sum('expected_value');
    }

    private function starsFromPct(float $pct): int
    {
        return match (true) {
            $pct >= 100 => 5,
            $pct >= 80 => 4,
            $pct >= 60 => 3,
            $pct >= 40 => 2,
            $pct > 0 => 1,
            default => 0,
        };
    }

    private function minutesLabel(int $minutes): string
    {
        $h = intdiv(max(0, $minutes), 60);
        $m = max(0, $minutes) % 60;

        return "{$h}س {$m}د";
    }
}
