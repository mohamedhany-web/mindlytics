<?php

namespace App\Services;

use App\Models\EmployeeAttendanceRecord;
use App\Models\User;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EmployeeAttendanceService
{
    public function resolveSchedule(User $user): ?WorkSchedule
    {
        if (! $user->isEmployee()) {
            return null;
        }

        $schedule = $user->workSchedule;
        if ($schedule?->is_active) {
            return $schedule;
        }

        return WorkSchedule::query()->where('is_active', true)->orderBy('id')->first();
    }

    public function getState(User $user, ?Carbon $now = null): array
    {
        $now = $now ?? now();

        if (! $user->isEmployee()) {
            return $this->state(['mode' => 'not_employee', 'can_access' => true]);
        }

        if (! $user->isSubjectToWorkSchedule()) {
            return $this->state([
                'mode' => 'exempt',
                'can_access' => true,
                'message' => '',
            ]);
        }

        $schedule = $this->resolveSchedule($user);
        if (! $schedule) {
            return $this->state([
                'mode' => 'no_schedule',
                'can_access' => true,
                'message' => 'لم يُحدَّد موعد عمل — تواصل مع الإدارة.',
            ]);
        }

        $record = $this->ensureTodayRecord($user, $schedule, $now);

        if (in_array($record->status, ['on_leave', 'off_day'], true)) {
            return $this->state([
                'mode' => $record->status,
                'can_access' => false,
                'schedule' => $schedule,
                'record' => $record,
                'message' => $record->status === 'on_leave' ? 'أنت في إجازة معتمدة اليوم.' : 'يوم راحتك الأسبوعية.',
            ]);
        }

        if ($record->isCompleted()) {
            return $this->state([
                'mode' => 'completed',
                'can_access' => false,
                'schedule' => $schedule,
                'record' => $record,
                'worked_seconds' => ($record->worked_minutes ?? 0) * 60,
                'required_seconds' => $record->required_minutes * 60,
                'message' => 'تم إنهاء يوم العمل. نراك غداً!',
            ]);
        }

        $window = $this->scheduleWindow($schedule, $now);
        $secondsUntilOpen = $now->lt($window['access_starts_at'])
            ? (int) $now->diffInSeconds($window['access_starts_at'])
            : 0;
        if ($now->lt($window['access_starts_at'])) {
            return $this->state([
                'mode' => 'locked_before_shift',
                'can_access' => false,
                'schedule' => $schedule,
                'record' => $record,
                'seconds_until_open' => $secondsUntilOpen,
                'shift_starts_at' => $window['shift_starts_at']->toIso8601String(),
                'shift_ends_at' => $window['shift_ends_at']->toIso8601String(),
                'message' => 'النظام يفتح عند بدء موعد العمل.',
            ]);
        }

        if ($now->gt($window['shift_ends_at']) && ! $record->clock_in_at) {
            return $this->state([
                'mode' => 'missed_shift',
                'can_access' => false,
                'schedule' => $schedule,
                'record' => $record,
                'message' => 'انتهى موعد العمل دون تسجيل حضور.',
            ]);
        }

        if (! $record->clock_in_at) {
            return $this->state([
                'mode' => 'awaiting_clock_in',
                'can_access' => false,
                'can_clock_in' => true,
                'schedule' => $schedule,
                'record' => $record,
                'seconds_until_open' => 0,
                'shift_starts_at' => $window['shift_starts_at']->toIso8601String(),
                'shift_ends_at' => $window['shift_ends_at']->toIso8601String(),
                'message' => 'سجّل حضورك لبدء يوم العمل.',
            ]);
        }

        $workedSeconds = (int) $record->clock_in_at->diffInSeconds($now);
        $requiredSeconds = $record->required_minutes * 60;
        $canClockOut = $workedSeconds >= $requiredSeconds || $now->gte($window['shift_ends_at']);

        return $this->state([
            'mode' => 'working',
            'can_access' => true,
            'can_clock_in' => false,
            'can_clock_out' => $canClockOut,
            'schedule' => $schedule,
            'record' => $record,
            'worked_seconds' => $workedSeconds,
            'required_seconds' => $requiredSeconds,
            'shift_starts_at' => $window['shift_starts_at']->toIso8601String(),
            'shift_ends_at' => $window['shift_ends_at']->toIso8601String(),
            'clock_in_at' => $record->clock_in_at->toIso8601String(),
            'message' => $canClockOut ? 'يمكنك إنهاء يوم العمل الآن.' : 'استمر في العمل حتى إكمال الساعات المطلوبة.',
        ]);
    }

    public function clockIn(User $user, ?string $ip = null): EmployeeAttendanceRecord
    {
        $now = now();
        $state = $this->getState($user, $now);

        if (empty($state['can_clock_in'])) {
            throw ValidationException::withMessages([
                'attendance' => $state['message'] ?? 'لا يمكن تسجيل الحضور الآن.',
            ]);
        }

        return DB::transaction(function () use ($user, $now, $ip, $state) {
            /** @var EmployeeAttendanceRecord $record */
            $record = EmployeeAttendanceRecord::query()
                ->where('user_id', $user->id)
                ->whereDate('work_date', $now->toDateString())
                ->lockForUpdate()
                ->firstOrFail();

            if ($record->clock_in_at) {
                throw ValidationException::withMessages(['attendance' => 'تم تسجيل الحضور مسبقاً.']);
            }

            $window = $this->scheduleWindow($state['schedule'], $now);
            $isLate = $now->gt($window['shift_starts_at']->copy()->addMinutes((int) $state['schedule']->grace_minutes));

            $record->update([
                'clock_in_at' => $now,
                'clock_in_ip' => $ip,
                'status' => 'active',
                'is_late' => $isLate,
            ]);

            $record = $record->fresh(['user']);
            app(EmployeeAttendancePenaltyService::class)->applyLatePenalty($record);

            $user->forceFill([
                'presence_last_seen_at' => $now,
                'presence_status' => 'online',
            ])->save();

            return $record->fresh();
        });
    }

    public function clockOut(User $user, ?string $ip = null): EmployeeAttendanceRecord
    {
        $now = now();
        $state = $this->getState($user, $now);

        if (empty($state['can_clock_out'])) {
            throw ValidationException::withMessages([
                'attendance' => 'لم تُكمل ساعات العمل المطلوبة بعد.',
            ]);
        }

        return DB::transaction(function () use ($user, $now, $ip) {
            $record = EmployeeAttendanceRecord::query()
                ->where('user_id', $user->id)
                ->whereDate('work_date', $now->toDateString())
                ->lockForUpdate()
                ->firstOrFail();

            if (! $record->clock_in_at || $record->clock_out_at) {
                throw ValidationException::withMessages(['attendance' => 'لا يوجد حضور نشط لإنهائه.']);
            }

            $workedMinutes = (int) $record->clock_in_at->diffInMinutes($now);
            $status = $workedMinutes >= $record->required_minutes ? 'completed' : 'incomplete';
            if ($record->is_late && $status === 'completed') {
                $status = 'late';
            }

            $record->update([
                'clock_out_at' => $now,
                'clock_out_ip' => $ip,
                'worked_minutes' => $workedMinutes,
                'status' => $status,
            ]);

            $record = $record->fresh(['user']);
            app(EmployeeAttendancePenaltyService::class)->applyIncompletePenalty($record);
            app(EmployeePresenceService::class)->closeViolationsOnClockOut($user);

            return $record->fresh();
        });
    }

    public function ensureTodayRecord(User $user, WorkSchedule $schedule, Carbon $now): EmployeeAttendanceRecord
    {
        $existing = EmployeeAttendanceRecord::query()
            ->where('user_id', $user->id)
            ->whereDate('work_date', $now->toDateString())
            ->first();

        if ($existing) {
            return $existing;
        }

        $window = $this->scheduleWindow($schedule, $now);
        $workDays = $schedule->work_days ?? WorkSchedule::defaultWorkDays();

        if ($user->isWeeklyOff($now)) {
            return EmployeeAttendanceRecord::create([
                'user_id' => $user->id,
                'work_schedule_id' => $schedule->id,
                'work_date' => $now->toDateString(),
                'scheduled_start' => $window['shift_starts_at'],
                'scheduled_end' => $window['shift_ends_at'],
                'required_minutes' => (int) round((float) $schedule->required_hours * 60),
                'status' => 'off_day',
            ]);
        }

        if ($user->isOnApprovedLeave($now)) {
            return EmployeeAttendanceRecord::create([
                'user_id' => $user->id,
                'work_schedule_id' => $schedule->id,
                'work_date' => $now->toDateString(),
                'scheduled_start' => $window['shift_starts_at'],
                'scheduled_end' => $window['shift_ends_at'],
                'required_minutes' => (int) round((float) $schedule->required_hours * 60),
                'status' => 'on_leave',
            ]);
        }

        if (! in_array($now->dayOfWeek, $workDays, true)) {
            return EmployeeAttendanceRecord::create([
                'user_id' => $user->id,
                'work_schedule_id' => $schedule->id,
                'work_date' => $now->toDateString(),
                'scheduled_start' => $window['shift_starts_at'],
                'scheduled_end' => $window['shift_ends_at'],
                'required_minutes' => (int) round((float) $schedule->required_hours * 60),
                'status' => 'off_day',
            ]);
        }

        return EmployeeAttendanceRecord::create([
            'user_id' => $user->id,
            'work_schedule_id' => $schedule->id,
            'work_date' => $now->toDateString(),
            'scheduled_start' => $window['shift_starts_at'],
            'scheduled_end' => $window['shift_ends_at'],
            'required_minutes' => (int) round((float) $schedule->required_hours * 60),
            'status' => 'pending',
        ]);
    }

    /** @return array{shift_starts_at: Carbon, shift_ends_at: Carbon, access_starts_at: Carbon} */
    public function scheduleWindow(WorkSchedule $schedule, Carbon $date): array
    {
        $start = $this->parseTimeOnDate($schedule->start_time, $date);
        $end = $this->parseTimeOnDate($schedule->end_time, $date);
        if ($end->lte($start)) {
            $end->addDay();
        }

        $accessStarts = $start->copy()->subMinutes((int) ($schedule->early_access_minutes ?? 0));

        return [
            'shift_starts_at' => $start,
            'shift_ends_at' => $end,
            'access_starts_at' => $accessStarts,
        ];
    }

    private function parseTimeOnDate(mixed $time, Carbon $date): Carbon
    {
        $timeStr = is_string($time) ? substr($time, 0, 8) : $time?->format('H:i:s');

        return $date->copy()->setTimeFromTimeString($timeStr ?? '09:00:00');
    }

    private function state(array $data): array
    {
        return array_merge([
            'mode' => 'unknown',
            'can_access' => false,
            'can_clock_in' => false,
            'can_clock_out' => false,
            'schedule' => null,
            'record' => null,
            'worked_seconds' => 0,
            'required_seconds' => 0,
            'seconds_until_open' => 0,
            'shift_starts_at' => null,
            'shift_ends_at' => null,
            'clock_in_at' => null,
            'message' => '',
        ], $data);
    }
}
