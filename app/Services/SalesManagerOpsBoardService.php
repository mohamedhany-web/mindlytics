<?php

namespace App\Services;

use App\Models\EmployeeAttendanceRecord;
use App\Models\SalesActivity;
use App\Models\SalesDailyReport;
use App\Models\SalesLead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SalesManagerOpsBoardService
{
    public function __construct(
        private EmployeeAttendanceService $attendance,
        private EmployeePresenceService $presence,
    ) {}

    /**
     * @param  list<int>  $memberIds
     * @return array{rows: Collection<int, array<string, mixed>>, stats: array<string, int>, pending_approvals: Collection}
     */
    public function build(array $memberIds, Carbon $date, array $filters = []): array
    {
        $members = User::query()
            ->whereIn('id', $memberIds)
            ->with(['workSchedule', 'employeeJob'])
            ->orderBy('name')
            ->get();

        $presenceBoard = $this->presence->teamPresenceBoard($memberIds)->keyBy('user_id');

        $records = EmployeeAttendanceRecord::query()
            ->whereIn('user_id', $memberIds)
            ->whereDate('work_date', $date->toDateString())
            ->get()
            ->keyBy('user_id');

        $activityCounts = SalesActivity::query()
            ->whereIn('user_id', $memberIds)
            ->whereDate('created_at', $date->toDateString())
            ->selectRaw('user_id, count(*) as total, sum(case when type = "call" then 1 else 0 end) as calls')
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $overdueFollowUps = SalesLead::query()
            ->whereIn('assigned_to', $memberIds)
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<', now())
            ->whereNotIn('stage', array_merge(SalesLead::CLOSED_STAGES, SalesLead::WON_LIKE_STAGES))
            ->selectRaw('assigned_to, count(*) as overdue')
            ->groupBy('assigned_to')
            ->pluck('overdue', 'assigned_to');

        $reports = SalesDailyReport::query()
            ->whereIn('user_id', $memberIds)
            ->whereDate('report_date', $date->toDateString())
            ->get()
            ->keyBy('user_id');

        $lastLeadTouches = SalesActivity::query()
            ->with('lead:id,name,stage')
            ->whereIn('user_id', $memberIds)
            ->whereDate('created_at', $date->toDateString())
            ->whereNotNull('sales_lead_id')
            ->orderByDesc('id')
            ->get()
            ->groupBy('user_id')
            ->map(fn (Collection $items) => $items->first());

        $rows = $members->map(function (User $member) use (
            $presenceBoard,
            $records,
            $activityCounts,
            $overdueFollowUps,
            $reports,
            $lastLeadTouches,
            $date
        ) {
            $record = $records->get($member->id);
            $state = $this->attendance->getState($member);
            $presence = $presenceBoard->get($member->id);
            $acts = $activityCounts->get($member->id);
            $lastTouch = $lastLeadTouches->get($member->id);
            $report = $reports->get($member->id);

            $attendanceFilterKey = $this->attendanceFilterKey($state, $record);

            return [
                'user_id' => $member->id,
                'name' => $member->name,
                'work_mode' => $member->work_mode ?? User::WORK_MODE_ONLINE,
                'work_mode_label' => $member->workModeLabel(),
                'is_offline' => $member->requiresManagerApprovalFor($date),
                'day_attendance_mode' => $member->isAttendanceOffDay($date)
                    ? 'off'
                    : $member->attendanceModeFor($date),
                'attendance_mode' => $state['mode'] ?? null,
                'attendance_filter' => $attendanceFilterKey,
                'attendance_message' => $state['message'] ?? '',
                'clock_in_at' => $record?->clock_in_at?->format('H:i'),
                'clock_out_at' => $record?->clock_out_at?->format('H:i'),
                'requested_at' => $record?->attendance_requested_at?->format('H:i'),
                'is_late' => (bool) ($record?->is_late),
                'late_waived' => (bool) ($record?->late_penalty_waived),
                'pending_approval' => $record?->isAwaitingManagerApproval() ?? false,
                'record_id' => $record?->id,
                'presence_status' => $presence['status'] ?? 'offline',
                'presence_label' => $presence['status_label'] ?? 'غير متصل',
                'presence_color' => $presence['status_color'] ?? 'slate',
                'last_seen_human' => $presence['last_seen_human'] ?? null,
                'activities_today' => (int) ($acts->total ?? 0),
                'calls_today' => (int) ($acts->calls ?? 0),
                'overdue_follow_ups' => (int) ($overdueFollowUps[$member->id] ?? 0),
                'last_lead_name' => $lastTouch?->lead?->name,
                'last_lead_stage' => $lastTouch?->lead?->stage,
                'daily_report_status' => $report?->status ?? 'missing',
                'scheduled_start' => $record?->scheduled_start?->format('H:i')
                    ?? $member->workSchedule?->start_time,
            ];
        });

        if (! empty($filters['employee_id'])) {
            $eid = (int) $filters['employee_id'];
            $rows = $rows->where('user_id', $eid)->values();
        }

        if (! empty($filters['work_mode']) && in_array($filters['work_mode'], ['online', 'offline', 'hybrid'], true)) {
            $rows = $rows->where('work_mode', $filters['work_mode'])->values();
        }

        if (! empty($filters['attendance'])) {
            $rows = $rows->where('attendance_filter', $filters['attendance'])->values();
        }

        if (! empty($filters['presence'])) {
            $rows = $rows->where('presence_status', $filters['presence'])->values();
        }

        $pendingApprovals = $records
            ->filter(fn (EmployeeAttendanceRecord $r) => $r->isAwaitingManagerApproval())
            ->values();

        $stats = [
            'total' => $rows->count(),
            'online_presence' => $rows->where('presence_status', 'online')->count(),
            'working' => $rows->whereIn('attendance_filter', ['working', 'late'])->count(),
            'pending_approval' => $rows->where('pending_approval', true)->count(),
            'not_clocked_in' => $rows->where('attendance_filter', 'not_clocked_in')->count(),
            'offline_workers' => $rows->where('is_offline', true)->count(),
        ];

        return compact('rows', 'stats', 'pendingApprovals');
    }

    private function attendanceFilterKey(array $state, ?EmployeeAttendanceRecord $record): string
    {
        $mode = $state['mode'] ?? '';

        if ($record?->isAwaitingManagerApproval() || $mode === 'pending_manager_approval') {
            return 'pending_approval';
        }

        if ($record?->clock_out_at || in_array($mode, ['completed'], true)) {
            return 'completed';
        }

        if ($record?->clock_in_at || in_array($mode, ['working', 'manager_unlocked_working'], true)) {
            return $record?->is_late ? 'late' : 'working';
        }

        if (in_array($mode, ['off_day', 'on_leave'], true)) {
            return $mode;
        }

        return 'not_clocked_in';
    }
}
