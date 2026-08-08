<?php

namespace App\Services;

use App\Models\SalesShiftChannelEvent;
use App\Models\SalesShiftEmployeeProfile;
use App\Models\SalesShiftPlan;
use App\Models\SalesShiftSegment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class SalesShiftScheduleService
{
    public function tablesReady(): bool
    {
        return Schema::hasTable('sales_shift_plans') && Schema::hasTable('sales_shift_segments');
    }

    public function activePlan(): ?SalesShiftPlan
    {
        if (! $this->tablesReady()) {
            return null;
        }

        return SalesShiftPlan::activePlan();
    }

    public function resolveWeekStart(?string $weekParam): Carbon
    {
        if ($weekParam && preg_match('/^\d{4}-\d{2}-\d{2}$/', $weekParam)) {
            return Carbon::parse($weekParam)->startOfWeek(Carbon::SATURDAY);
        }

        return now()->startOfWeek(Carbon::SATURDAY);
    }

    public function dayOfWeekIndex(Carbon $date): int
    {
        return (int) $date->dayOfWeek; // Carbon: 0=Sun … 6=Sat — we need Sat=0
    }

    /** 0=السبت … 6=الجمعة */
    public function salesDayIndex(Carbon $date): int
    {
        // Carbon Saturday=6, we want Saturday=0
        return ($this->dayOfWeekIndex($date) + 1) % 7;
    }

    public function formatHourLabel(int $hour): string
    {
        if ($hour === 12) {
            return '12 ظ';
        }
        if ($hour < 12) {
            return $hour.' ص';
        }
        if ($hour < 24) {
            return ($hour - 12).' م';
        }

        $h = $hour - 24;

        return ($h === 0 ? 12 : $h).' ص';
    }

    public function currentWorkHour(?Carbon $at = null): int
    {
        $at = $at ?? now();
        $h = (int) $at->format('G');
        $start = (int) config('sales_shifts.work_day_start_hour', 10);

        return $h < $start ? $h + 24 : $h;
    }

    /**
     * @param  list<int>|null  $memberIds  تقييد العرض لفريق معيّن (مدير المبيعات)
     * @return array<string, mixed>|null
     */
    public function buildWeekBoard(
        ?SalesShiftPlan $plan = null,
        ?Carbon $weekStart = null,
        ?int $forUserId = null,
        ?array $memberIds = null,
    ): ?array {
        $plan = $plan ?? $this->activePlan();
        if (! $plan) {
            return null;
        }

        $weekStart = ($weekStart ?? $this->resolveWeekStart(null))->copy()->startOfDay();
        $workStart = (int) ($plan->work_start_hour ?? config('sales_shifts.work_day_start_hour', 10));
        $workEnd = (int) ($plan->work_end_hour ?? config('sales_shifts.work_day_end_hour', 26));
        $span = max(1, $workEnd - $workStart);

        $segmentsQ = $plan->segments()->with('user:id,name');
        if ($memberIds !== null) {
            $segmentsQ->whereIn('user_id', $memberIds);
        }
        $segments = $segmentsQ->get();
        $profiles = $this->profilesKeyed();

        $allRepIds = $memberIds ?? User::salesEmployees()->where('is_active', true)->pluck('id')->map(fn ($id) => (int) $id)->all();
        $days = [];
        $peopleStats = [];

        foreach ($allRepIds as $rid) {
            $peopleStats[$rid] = ['hours' => 0, 'days' => 0, 'nights' => 0];
        }

        for ($d = 0; $d < 7; $d++) {
            $date = $weekStart->copy()->addDays($d);
            $daySegments = $segments->where('day_of_week', $d)->values();

            if ($forUserId && ! $daySegments->contains(fn ($s) => (int) $s->user_id === $forUserId)) {
                // still show day if user has weekly off
            }

            $byUser = $daySegments->groupBy('user_id');
            $lanes = [];

            foreach ($byUser as $userId => $userSegs) {
                $user = $userSegs->first()->user;
                if (! $user) {
                    continue;
                }
                $uid = (int) $userId;
                $profile = $profiles[$uid] ?? null;
                $segRows = [];

                foreach ($userSegs->sortBy([['start_hour', 'asc'], ['sort_order', 'asc']]) as $seg) {
                    $left = (($seg->start_hour - $workStart) / $span) * 100;
                    $width = (($seg->end_hour - $seg->start_hour) / $span) * 100;
                    $segRows[] = [
                        'id' => $seg->id,
                        'start_hour' => $seg->start_hour,
                        'end_hour' => $seg->end_hour,
                        'start_label' => $this->formatHourLabel($seg->start_hour),
                        'end_label' => $this->formatHourLabel($seg->end_hour),
                        'mode' => $seg->mode,
                        'channels' => $seg->channels ?? [],
                        'channels_label' => $seg->channelsLabel(),
                        'location_badge' => $seg->location_badge,
                        'left_pct' => round(max(0, $left), 4),
                        'width_pct' => round(max(0, $width), 4),
                        'is_home' => $seg->mode === SalesShiftSegment::MODE_HOME,
                        'is_night' => $seg->end_hour > 21,
                    ];

                    if (isset($peopleStats[$uid])) {
                        $peopleStats[$uid]['hours'] += ($seg->end_hour - $seg->start_hour);
                        if ($seg->end_hour > 21) {
                            $peopleStats[$uid]['nights']++;
                        }
                    }
                }

                if ($segRows !== []) {
                    $peopleStats[$uid]['days']++;
                }

                $lanes[] = [
                    'user_id' => $uid,
                    'user_name' => $user->name,
                    'color' => $profile?->color_hex ?? '#0EA5E9',
                    'base_channels' => $profile?->base_channels_label,
                    'from_hour' => $userSegs->min('start_hour'),
                    'to_hour' => $userSegs->max('end_hour'),
                    'from_label' => $this->formatHourLabel((int) $userSegs->min('start_hour')),
                    'to_label' => $this->formatHourLabel((int) $userSegs->max('end_hour')),
                    'segments' => $segRows,
                ];
            }

            usort($lanes, fn ($a, $b) => ($a['from_hour'] ?? 99) <=> ($b['from_hour'] ?? 99));

            $offToday = [];
            foreach ($allRepIds as $rid) {
                $user = User::query()->find($rid);
                if (! $user) {
                    continue;
                }
                $hasSegments = $byUser->has($rid);
                if ($user->isWeeklyOff($date) || ! $hasSegments) {
                    $offToday[] = ['user_id' => $rid, 'name' => $user->name, 'reason' => $user->isWeeklyOff($date) ? 'weekly_off' : 'no_shift'];
                }
            }

            $locationBadge = $daySegments->first()?->location_badge;
            if ($locationBadge === 'from_office') {
                $locationBadge = 'من المقر';
            }

            $days[] = [
                'day_of_week' => $d,
                'date' => $date,
                'date_str' => $date->toDateString(),
                'name' => config('sales_shifts.day_names.'.$d, $date->copy()->locale('ar')->translatedFormat('l')),
                'location_badge' => $locationBadge,
                'off_today' => $offToday,
                'lanes' => $lanes,
                'is_today' => $date->isToday(),
            ];
        }

        $peopleSummary = $this->buildPeopleSummary($allRepIds, $profiles, $peopleStats);

        return [
            'plan' => $plan,
            'week_start' => $weekStart,
            'week_end' => $weekStart->copy()->addDays(6),
            'prev_week' => $weekStart->copy()->subWeek()->toDateString(),
            'next_week' => $weekStart->copy()->addWeek()->toDateString(),
            'work_start_hour' => $workStart,
            'work_end_hour' => $workEnd,
            'days' => $days,
            'people_summary' => $peopleSummary,
            'rules' => $plan->rules ?? config('sales_shifts.rules', []),
            'ownership_now' => $this->channelOwnershipNow($plan),
            'my_today' => $forUserId ? $this->employeeTodaySnapshot($plan, $forUserId) : null,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function employeeTodaySnapshot(SalesShiftPlan $plan, int $userId, ?Carbon $at = null): ?array
    {
        $at = $at ?? now();
        $d = $this->salesDayIndex($at);
        $hour = $this->currentWorkHour($at);
        $segments = $plan->segments()
            ->where('day_of_week', $d)
            ->where('user_id', $userId)
            ->orderBy('start_hour')
            ->get();

        if ($segments->isEmpty()) {
            return [
                'is_working_today' => false,
                'day_name' => config('sales_shifts.day_names.'.$d),
                'message' => 'لا يوجد شيفت مسجّل لك اليوم',
            ];
        }

        $current = $segments->first(fn ($s) => $this->segmentCoversHour($s, $hour));
        $next = $segments->first(fn ($s) => $s->start_hour > $hour);

        return [
            'is_working_today' => true,
            'day_name' => config('sales_shifts.day_names.'.$d),
            'segments_today' => $segments->map(fn ($s) => [
                'start_label' => $this->formatHourLabel($s->start_hour),
                'end_label' => $this->formatHourLabel($s->end_hour),
                'channels_label' => $s->channelsLabel(),
                'mode' => $s->mode,
                'is_current' => $this->segmentCoversHour($s, $hour),
            ])->values()->all(),
            'current' => $current ? [
                'channels_label' => $current->channelsLabel(),
                'end_label' => $this->formatHourLabel($current->end_hour),
                'mode' => $current->mode,
            ] : null,
            'next' => $next ? [
                'channels_label' => $next->channelsLabel(),
                'start_label' => $this->formatHourLabel($next->start_hour),
            ] : null,
        ];
    }

    /**
     * @return array<string, array{owner_id: int, owner_name: string, channels: list<string>, segment_id: int|null, can_takeover: bool}>
     */
    public function channelOwnershipNow(?SalesShiftPlan $plan = null, ?Carbon $at = null): array
    {
        $plan = $plan ?? $this->activePlan();
        if (! $plan) {
            return [];
        }

        $at = $at ?? now();
        $d = $this->salesDayIndex($at);
        $hour = $this->currentWorkHour($at);
        $active = $plan->segments()
            ->where('day_of_week', $d)
            ->with('user:id,name')
            ->get()
            ->filter(fn ($s) => $this->segmentCoversHour($s, $hour));

        $result = [];
        foreach (array_keys(config('sales_shifts.channels', [])) as $code) {
            if ($code === 'all') {
                continue;
            }
            $ownerSeg = $active->first(fn ($s) => $s->coversChannel($code))
                ?? $active->first(fn ($s) => $s->coversChannel('all'));

            if (! $ownerSeg?->user) {
                continue;
            }

            $grace = (int) ($plan->takeover_grace_minutes ?? config('sales_shifts.takeover_grace_minutes', 10));
            $canTakeover = $this->ownerMissedGrace($ownerSeg->user_id, $code, $grace, $at);

            $result[$code] = [
                'owner_id' => $ownerSeg->user_id,
                'owner_name' => $ownerSeg->user->name,
                'channels' => $ownerSeg->channels ?? [],
                'segment_id' => $ownerSeg->id,
                'can_takeover' => $canTakeover,
            ];
        }

        return $result;
    }

    public function canUserRespondOnChannel(User $user, string $channelCode, ?Carbon $at = null): array
    {
        $ownership = $this->channelOwnershipNow(null, $at);
        $info = $ownership[$channelCode] ?? null;

        if (! $info) {
            return ['allowed' => true, 'reason' => 'no_owner'];
        }

        if ((int) $info['owner_id'] === (int) $user->id) {
            return ['allowed' => true, 'reason' => 'owner'];
        }

        if ($info['can_takeover']) {
            return ['allowed' => true, 'reason' => 'takeover_grace'];
        }

        return [
            'allowed' => false,
            'reason' => 'not_owner',
            'owner_name' => $info['owner_name'],
            'grace_minutes' => (int) config('sales_shifts.takeover_grace_minutes', 10),
        ];
    }

    public function recordChannelResponse(
        User $actor,
        string $channelCode,
        ?int $ownerUserId = null,
        string $eventType = 'outbound_reply',
        ?string $sourceType = null,
        ?int $sourceId = null,
        ?Carbon $at = null,
    ): void {
        if (! Schema::hasTable('sales_shift_channel_events')) {
            return;
        }

        SalesShiftChannelEvent::create([
            'channel_code' => $channelCode,
            'owner_user_id' => $ownerUserId,
            'actor_user_id' => $actor->id,
            'event_type' => $eventType,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'occurred_at' => ($at ?? now()),
        ]);
    }

    private function segmentCoversHour(SalesShiftSegment $seg, int $hour): bool
    {
        return $hour >= $seg->start_hour && $hour < $seg->end_hour;
    }

    private function ownerMissedGrace(int $ownerId, string $channelCode, int $graceMinutes, Carbon $at): bool
    {
        if (! Schema::hasTable('sales_shift_channel_events')) {
            return false;
        }

        $lastOwnerReply = SalesShiftChannelEvent::query()
            ->where('channel_code', $channelCode)
            ->where('actor_user_id', $ownerId)
            ->where('event_type', 'outbound_reply')
            ->where('occurred_at', '>=', $at->copy()->subHours(4))
            ->orderByDesc('occurred_at')
            ->first();

        if (! $lastOwnerReply) {
            return $at->copy()->subMinutes($graceMinutes)->isPast();
        }

        return $lastOwnerReply->occurred_at->lt($at->copy()->subMinutes($graceMinutes));
    }

    /**
     * @return array<int, SalesShiftEmployeeProfile>
     */
    private function profilesKeyed(): array
    {
        if (! Schema::hasTable('sales_shift_employee_profiles')) {
            return [];
        }

        return SalesShiftEmployeeProfile::query()
            ->get()
            ->keyBy('user_id')
            ->all();
    }

    /**
     * @param  list<int>  $repIds
     * @param  array<int, SalesShiftEmployeeProfile>  $profiles
     * @param  array<int, array{hours: int, days: int, nights: int}>  $stats
     * @return list<array<string, mixed>>
     */
    private function buildPeopleSummary(array $repIds, array $profiles, array $stats): array
    {
        $rows = [];
        foreach ($repIds as $rid) {
            $user = User::query()->find($rid);
            if (! $user) {
                continue;
            }
            $profile = $profiles[$rid] ?? null;
            $stat = $stats[$rid] ?? ['hours' => 0, 'days' => 0, 'nights' => 0];
            $rows[] = [
                'user_id' => $rid,
                'name' => $user->name,
                'color' => $profile?->color_hex ?? '#0EA5E9',
                'base_channels' => $profile?->base_channels_label ?? '—',
                'weekly_off' => $user->weeklyOffDayLabel() ?? '—',
                'work_days' => $stat['days'],
                'total_hours' => $stat['hours'],
                'night_shifts' => $stat['nights'],
            ];
        }

        usort($rows, fn ($a, $b) => (($profiles[$a['user_id']] ?? null)?->display_order ?? 99) <=> (($profiles[$b['user_id']] ?? null)?->display_order ?? 99));

        return $rows;
    }

    /**
     * لوحة حية لمدير المبيعات — من على الشيفت الآن + القنوات.
     *
     * @param  list<int>  $memberIds
     * @return array<string, mixed>
     */
    public function buildTeamLivePanel(array $memberIds, ?SalesShiftPlan $plan = null, ?Carbon $at = null): array
    {
        $plan = $plan ?? $this->activePlan();
        $at = $at ?? now();
        $hour = $this->currentWorkHour($at);
        $dow = $this->salesDayIndex($at);

        if (! $plan || $memberIds === []) {
            return [
                'active_now' => [],
                'off_today' => [],
                'not_on_shift' => [],
                'ownership' => [],
                'hour_label' => $this->formatHourLabel($hour),
            ];
        }

        $segments = $plan->segments()
            ->where('day_of_week', $dow)
            ->whereIn('user_id', $memberIds)
            ->with('user:id,name')
            ->orderBy('start_hour')
            ->get();

        $activeNow = [];
        $activeUserIds = [];

        foreach ($segments as $seg) {
            if (! $this->segmentCoversHour($seg, $hour)) {
                continue;
            }
            $uid = (int) $seg->user_id;
            $activeUserIds[] = $uid;
            $activeNow[] = [
                'user_id' => $uid,
                'user_name' => $seg->user->name ?? '—',
                'channels_label' => $seg->channelsLabel(),
                'end_label' => $this->formatHourLabel($seg->end_hour),
                'mode' => $seg->mode,
                'segment_id' => $seg->id,
            ];
        }

        $offToday = [];
        $notOnShift = [];
        foreach ($memberIds as $rid) {
            $user = User::query()->find($rid);
            if (! $user) {
                continue;
            }
            if ($user->isWeeklyOff($at)) {
                $offToday[] = ['user_id' => $rid, 'name' => $user->name, 'reason' => 'weekly_off'];
            } elseif (! in_array($rid, $activeUserIds, true)) {
                $hasDaySegments = $segments->contains(fn ($s) => (int) $s->user_id === $rid);
                if (! $hasDaySegments) {
                    $offToday[] = ['user_id' => $rid, 'name' => $user->name, 'reason' => 'no_shift'];
                } else {
                    $notOnShift[] = ['user_id' => $rid, 'name' => $user->name];
                }
            }
        }

        $ownership = $this->channelOwnershipNow($plan, $at);
        $ownership = array_filter($ownership, fn ($o) => in_array((int) ($o['owner_id'] ?? 0), $memberIds, true));

        return [
            'active_now' => $activeNow,
            'off_today' => $offToday,
            'not_on_shift' => $notOnShift,
            'ownership' => $ownership,
            'hour_label' => $this->formatHourLabel($hour),
            'day_name' => config('sales_shifts.day_names.'.$dow, ''),
        ];
    }

    /**
     * @param  list<int>  $memberIds
     */
    public function memberShiftToday(int $userId, ?SalesShiftPlan $plan = null, ?Carbon $at = null): ?array
    {
        $plan = $plan ?? $this->activePlan();
        if (! $plan) {
            return null;
        }

        return $this->employeeTodaySnapshot($plan, $userId, $at);
    }

    /**
     * هل للموظف شيفت من المقر (غير من البيت) في هذا التاريخ؟
     */
    public function isOfficeShiftDay(int $userId, Carbon $date, ?SalesShiftPlan $plan = null): bool
    {
        $plan = $plan ?? $this->activePlan();
        if (! $plan) {
            return false;
        }

        return $plan->segments()
            ->where('day_of_week', $this->salesDayIndex($date))
            ->where('user_id', $userId)
            ->where('mode', '!=', SalesShiftSegment::MODE_HOME)
            ->exists();
    }

    /**
     * قائمة أعضاء الفريق المجدولين من المقر في يوم معيّن (يستبعد mode=home).
     *
     * @param  list<int>  $memberIds
     * @return array{
     *   plan: ?SalesShiftPlan,
     *   date: Carbon,
     *   day_index: int,
     *   day_name: string,
     *   members: list<array<string, mixed>>,
     *   empty_reason: ?string
     * }
     */
    public function officeRosterForDate(Carbon $date, array $memberIds): array
    {
        $date = $date->copy()->startOfDay();
        $dayIndex = $this->salesDayIndex($date);
        $plan = $this->activePlan();

        $base = [
            'plan' => $plan,
            'date' => $date,
            'day_index' => $dayIndex,
            'day_name' => (string) config('sales_shifts.day_names.'.$dayIndex, $date->copy()->locale('ar')->translatedFormat('l')),
            'members' => [],
            'empty_reason' => null,
        ];

        if (! $plan) {
            $base['empty_reason'] = 'no_plan';

            return $base;
        }

        $memberIds = array_values(array_unique(array_map('intval', $memberIds)));
        if ($memberIds === []) {
            $base['empty_reason'] = 'no_members';

            return $base;
        }

        $segments = $plan->segments()
            ->with('user:id,name')
            ->where('day_of_week', $dayIndex)
            ->whereIn('user_id', $memberIds)
            ->where('mode', '!=', SalesShiftSegment::MODE_HOME)
            ->orderBy('start_hour')
            ->get();

        if ($segments->isEmpty()) {
            $base['empty_reason'] = 'no_office_shifts';

            return $base;
        }

        $byUser = $segments->groupBy(fn ($s) => (int) $s->user_id);
        $records = \App\Models\EmployeeAttendanceRecord::query()
            ->whereIn('user_id', $byUser->keys()->all())
            ->whereDate('work_date', $date->toDateString())
            ->get()
            ->keyBy(fn ($r) => (int) $r->user_id);

        $members = [];
        foreach ($byUser as $userId => $userSegs) {
            /** @var Collection<int, SalesShiftSegment> $userSegs */
            $user = $userSegs->first()?->user;
            $startHour = (int) $userSegs->min('start_hour');
            $endHour = (int) $userSegs->max('end_hour');
            $badge = $userSegs->first(fn ($s) => filled($s->location_badge))?->location_badge
                ?? $userSegs->first()?->location_badge;
            if ($badge === 'from_office') {
                $badge = 'من المقر';
            }

            $record = $records->get((int) $userId);
            $managerConfirmed = $record
                && $record->attendance_approval_status === \App\Models\EmployeeAttendanceRecord::APPROVAL_APPROVED
                && filled($record->approved_by);
            $isLate = (bool) ($record?->is_late);
            $pendingRequest = $record?->isAwaitingManagerApproval() ?? false;

            $statusKey = 'awaiting';
            if ($managerConfirmed && $isLate) {
                $statusKey = 'confirmed_late';
            } elseif ($managerConfirmed) {
                $statusKey = 'confirmed_on_time';
            } elseif ($pendingRequest) {
                $statusKey = 'pending_request';
            } elseif ($record?->clock_in_at) {
                $statusKey = 'clocked_unconfirmed';
            }

            $members[] = [
                'user_id' => (int) $userId,
                'name' => (string) ($user?->name ?? '—'),
                'start_hour' => $startHour,
                'end_hour' => $endHour,
                'start_label' => $this->formatHourLabel($startHour),
                'end_label' => $this->formatHourLabel($endHour),
                'location_badge' => $badge,
                'channels_label' => $userSegs->map(fn ($s) => $s->channelsLabel())->unique()->implode(' · '),
                'segments_count' => $userSegs->count(),
                'record' => $record,
                'status_key' => $statusKey,
                'manager_confirmed' => $managerConfirmed,
                'is_late' => $isLate,
                'clock_in_at' => $record?->clock_in_at,
                'deduction_id' => $record?->late_deduction_id,
            ];
        }

        usort($members, fn ($a, $b) => [$a['start_hour'], $a['name']] <=> [$b['start_hour'], $b['name']]);

        $base['members'] = $members;

        return $base;
    }
}
