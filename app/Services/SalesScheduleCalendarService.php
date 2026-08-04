<?php

namespace App\Services;

use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SalesScheduleCalendarService
{
    public function __construct(
        private EmployeeAttendanceService $attendance
    ) {}

    /**
     * @param  Collection<int, User>|iterable<User>  $reps
     * @return array{
     *   week_start: Carbon,
     *   week_end: Carbon,
     *   days: list<Carbon>,
     *   rows: list<array{user: User, days: list<array<string, mixed>>}>
     * }
     */
    public function buildWeek(iterable $reps, Carbon $weekStart): array
    {
        $weekStart = $weekStart->copy()->startOfDay();
        $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $days[] = $weekStart->copy()->addDays($i);
        }

        $reps = collect($reps)->values();
        $repIds = $reps->pluck('id')->map(fn ($id) => (int) $id)->all();

        $leavesByUser = LeaveRequest::query()
            ->whereIn('employee_id', $repIds)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $weekEnd->toDateString())
            ->whereDate('end_date', '>=', $weekStart->toDateString())
            ->get()
            ->groupBy('employee_id');

        $rows = [];
        foreach ($reps as $user) {
            /** @var User $user */
            $schedule = $this->attendance->resolveSchedule($user);
            $dayCells = [];

            foreach ($days as $day) {
                $leave = $this->leaveForDay($leavesByUser->get($user->id, collect()), $day);
                $onLeave = $leave !== null;
                $attendanceOff = $user->isAttendanceOffDay($day);
                $weeklyOff = $user->isWeeklyOff($day);

                $status = 'working';
                $statusLabel = 'عمل';
                if ($onLeave) {
                    $status = 'on_leave';
                    $statusLabel = 'إجازة'.($leave->type_label ? ' · '.$leave->type_label : '');
                } elseif ($attendanceOff) {
                    $status = $weeklyOff ? 'weekly_off' : 'off';
                    $statusLabel = $weeklyOff ? 'إجازة أسبوعية' : 'يوم راحة';
                }

                $shiftStart = null;
                $shiftEnd = null;
                if ($schedule) {
                    $window = $this->attendance->scheduleWindowForUser($user, $schedule, $day);
                    $shiftStart = $window['shift_starts_at']->format('H:i');
                    $shiftEnd = $window['shift_ends_at']->format('H:i');
                }

                $dayCells[] = [
                    'date' => $day->toDateString(),
                    'day_label' => $day->copy()->locale('ar')->translatedFormat('D'),
                    'status' => $status,
                    'status_label' => $statusLabel,
                    'is_working' => $status === 'working',
                    'on_leave' => $onLeave,
                    'weekly_off' => $weeklyOff,
                    'shift_start' => $shiftStart,
                    'shift_end' => $shiftEnd,
                    'mode' => $status === 'working' ? $user->attendanceModeFor($day) : null,
                    'schedule_name' => $schedule?->name,
                ];
            }

            $rows[] = [
                'user' => $user,
                'days' => $dayCells,
            ];
        }

        return [
            'week_start' => $weekStart,
            'week_end' => $weekStart->copy()->addDays(6),
            'days' => $days,
            'rows' => $rows,
        ];
    }

    public function resolveWeekStart(?string $weekParam): Carbon
    {
        if ($weekParam && preg_match('/^\d{4}-\d{2}-\d{2}$/', $weekParam)) {
            return Carbon::parse($weekParam)->startOfWeek(Carbon::SATURDAY);
        }

        return now()->startOfWeek(Carbon::SATURDAY);
    }

    /**
     * @param  Collection<int, LeaveRequest>  $leaves
     */
    private function leaveForDay(Collection $leaves, Carbon $day): ?LeaveRequest
    {
        $date = $day->toDateString();
        foreach ($leaves as $leave) {
            if ($leave->start_date->toDateString() <= $date && $leave->end_date->toDateString() >= $date) {
                return $leave;
            }
        }

        return null;
    }
}
