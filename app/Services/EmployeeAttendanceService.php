<?php

namespace App\Services;

use App\Models\EmployeeAttendanceRecord;
use App\Models\EmployeeWorkUnlock;
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

    public function activeUnlock(User $user, ?Carbon $now = null): ?EmployeeWorkUnlock
    {
        $now = $now ?? now();

        return EmployeeWorkUnlock::query()
            ->with('unlockedBy:id,name')
            ->where('user_id', $user->id)
            ->whereDate('work_date', $now->toDateString())
            ->active($now)
            ->orderByDesc('id')
            ->first();
    }

    /**
     * فتح النظام لموظف خارج موعده / في يوم راحته (بواسطة مدير المبيعات).
     */
    public function unlockForManager(
        User $employee,
        User $manager,
        string $reason,
        string $durationKey = 'end_of_day',
    ): EmployeeWorkUnlock {
        if (! $employee->isSubjectToWorkSchedule()) {
            throw ValidationException::withMessages([
                'employee' => 'هذا الموظف غير خاضع لنظام مواعيد العمل.',
            ]);
        }

        $reason = trim($reason);
        if (mb_strlen($reason) < 5) {
            throw ValidationException::withMessages([
                'reason' => 'اكتب سبباً واضحاً لفتح النظام (5 أحرف على الأقل).',
            ]);
        }

        $now = now();
        $expiresAt = $this->resolveUnlockExpiry($now, $durationKey);
        $labels = EmployeeWorkUnlock::durationOptions();

        return DB::transaction(function () use ($employee, $manager, $reason, $durationKey, $labels, $now, $expiresAt) {
            EmployeeWorkUnlock::query()
                ->where('user_id', $employee->id)
                ->whereDate('work_date', $now->toDateString())
                ->whereNull('revoked_at')
                ->where('expires_at', '>', $now)
                ->update([
                    'revoked_at' => $now,
                    'revoked_by' => $manager->id,
                    'revoke_reason' => 'استُبدل بفتح جديد',
                ]);

            $schedule = $this->resolveSchedule($employee);
            if ($schedule) {
                $record = $this->ensureTodayRecord($employee, $schedule, $now);
                if (in_array($record->status, ['off_day', 'on_leave', 'absent'], true) && ! $record->clock_in_at) {
                    $fromStatus = $record->status;
                    $record->update([
                        'status' => 'pending',
                        'metadata' => array_merge($record->metadata ?? [], [
                            'opened_by_manager' => true,
                            'opened_from_status' => $fromStatus,
                            'opened_at' => $now->toIso8601String(),
                        ]),
                    ]);
                }
            }

            return EmployeeWorkUnlock::query()->create([
                'user_id' => $employee->id,
                'unlocked_by' => $manager->id,
                'work_date' => $now->toDateString(),
                'starts_at' => $now,
                'expires_at' => $expiresAt,
                'reason' => $reason,
                'duration_label' => $labels[$durationKey] ?? $durationKey,
                'metadata' => [
                    'duration_key' => $durationKey,
                ],
            ]);
        });
    }

    public function revokeUnlock(EmployeeWorkUnlock $unlock, User $manager, ?string $reason = null): EmployeeWorkUnlock
    {
        if ($unlock->revoked_at || $unlock->expires_at->lte(now())) {
            throw ValidationException::withMessages([
                'unlock' => 'هذا الفتح غير نشط حالياً.',
            ]);
        }

        $unlock->update([
            'revoked_at' => now(),
            'revoked_by' => $manager->id,
            'revoke_reason' => $reason ?: 'تم الإلغاء بواسطة المدير',
        ]);

        return $unlock->fresh();
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
        $unlock = $this->activeUnlock($user, $now);
        $unlockMeta = $unlock ? [
            'unlock' => [
                'id' => $unlock->id,
                'reason' => $unlock->reason,
                'expires_at' => $unlock->expires_at->toIso8601String(),
                'expires_at_human' => $unlock->expires_at->format('H:i'),
                'duration_label' => $unlock->duration_label,
                'manager_name' => $unlock->unlockedBy?->name,
            ],
        ] : [];

        if ($unlock && $record->isCompleted()) {
            return $this->state(array_merge([
                'mode' => 'manager_unlocked',
                'can_access' => true,
                'can_clock_in' => false,
                'can_clock_out' => false,
                'schedule' => $schedule,
                'record' => $record,
                'worked_seconds' => ($record->worked_minutes ?? 0) * 60,
                'required_seconds' => $record->required_minutes * 60,
                'message' => 'تم فتح النظام بواسطة المدير حتى '.$unlock->expires_at->format('H:i').'.',
            ], $unlockMeta));
        }

        if (in_array($record->status, ['on_leave', 'off_day'], true) && ! $unlock) {
            return $this->state([
                'mode' => $record->status,
                'can_access' => false,
                'schedule' => $schedule,
                'record' => $record,
                'message' => $record->status === 'on_leave' ? 'أنت في إجازة معتمدة اليوم.' : 'يوم راحتك الأسبوعية.',
                'work_mode' => $user->work_mode ?? User::WORK_MODE_ONLINE,
            ]);
        }

        if ($record->isAwaitingManagerApproval() && ! $unlock) {
            return $this->state([
                'mode' => 'pending_manager_approval',
                'can_access' => false,
                'can_clock_in' => false,
                'schedule' => $schedule,
                'record' => $record,
                'message' => 'تم إرسال طلب الحضور — بانتظار موافقة مدير المبيعات (تأكيد تواجدك في المكتب).',
                'work_mode' => $user->work_mode ?? User::WORK_MODE_ONLINE,
                'attendance_requested_at' => $record->attendance_requested_at?->toIso8601String(),
            ]);
        }

        if ($record->attendance_approval_status === EmployeeAttendanceRecord::APPROVAL_REJECTED && ! $record->clock_in_at && ! $unlock) {
            return $this->state([
                'mode' => 'attendance_rejected',
                'can_access' => false,
                'can_clock_in' => true,
                'schedule' => $schedule,
                'record' => $record,
                'message' => 'رُفض طلب الحضور'.($record->approval_rejection_reason ? ': '.$record->approval_rejection_reason : '').' — يمكنك إعادة الطلب.',
                'work_mode' => $user->work_mode ?? User::WORK_MODE_ONLINE,
            ]);
        }

        if ($record->isCompleted() && ! $unlock) {
            return $this->state([
                'mode' => 'completed',
                'can_access' => false,
                'schedule' => $schedule,
                'record' => $record,
                'worked_seconds' => ($record->worked_minutes ?? 0) * 60,
                'required_seconds' => $record->required_minutes * 60,
                'message' => 'تم إنهاء يوم العمل. نراك غداً!',
                'work_mode' => $user->work_mode ?? User::WORK_MODE_ONLINE,
            ]);
        }

        $window = $this->scheduleWindow($schedule, $now);
        $secondsUntilOpen = $now->lt($window['access_starts_at'])
            ? (int) $now->diffInSeconds($window['access_starts_at'])
            : 0;

        if ($now->lt($window['access_starts_at']) && ! $unlock) {
            return $this->state([
                'mode' => 'locked_before_shift',
                'can_access' => false,
                'schedule' => $schedule,
                'record' => $record,
                'seconds_until_open' => $secondsUntilOpen,
                'shift_starts_at' => $window['shift_starts_at']->toIso8601String(),
                'shift_ends_at' => $window['shift_ends_at']->toIso8601String(),
                'message' => 'النظام يفتح عند بدء موعد العمل.',
                'work_mode' => $user->work_mode ?? User::WORK_MODE_ONLINE,
            ]);
        }

        if ($now->gt($window['shift_ends_at']) && ! $record->clock_in_at && ! $unlock) {
            return $this->state([
                'mode' => 'missed_shift',
                'can_access' => false,
                'schedule' => $schedule,
                'record' => $record,
                'message' => 'انتهى موعد العمل دون تسجيل حضور.',
                'work_mode' => $user->work_mode ?? User::WORK_MODE_ONLINE,
            ]);
        }

        if (! $record->clock_in_at) {
            $isOffline = $user->isOfflineWorker();
            $message = $unlock
                ? 'تم فتح النظام بواسطة المدير — سجّل حضورك لبدء العمل.'
                : ($isOffline
                    ? 'اطلب الحضور — المدير يؤكد تواجدك في المكتب ثم يفتح النظام.'
                    : 'سجّل حضورك لبدء يوم العمل.');

            return $this->state(array_merge([
                'mode' => $unlock ? 'manager_unlocked' : 'awaiting_clock_in',
                'can_access' => false,
                'can_clock_in' => true,
                'schedule' => $schedule,
                'record' => $record,
                'seconds_until_open' => 0,
                'shift_starts_at' => $window['shift_starts_at']->toIso8601String(),
                'shift_ends_at' => $window['shift_ends_at']->toIso8601String(),
                'message' => $message,
                'work_mode' => $user->work_mode ?? User::WORK_MODE_ONLINE,
                'requires_manager_approval' => $isOffline && ! $unlock,
            ], $unlockMeta));
        }

        $workedSeconds = (int) $record->clock_in_at->diffInSeconds($now);
        $requiredSeconds = $record->required_minutes * 60;
        $canClockOut = $workedSeconds >= $requiredSeconds || $now->gte($window['shift_ends_at']) || (bool) $unlock;

        return $this->state(array_merge([
            'mode' => $unlock ? 'manager_unlocked_working' : 'working',
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
            'message' => $unlock
                ? 'نظام مفتوح بتصريح المدير حتى '.$unlock->expires_at->format('H:i').'.'
                : ($canClockOut ? 'يمكنك إنهاء يوم العمل الآن.' : 'استمر في العمل حتى إكمال الساعات المطلوبة.'),
            'work_mode' => $user->work_mode ?? User::WORK_MODE_ONLINE,
        ], $unlockMeta));
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

            $unlock = $state['unlock'] ?? null;
            $requiresApproval = $user->isOfflineWorker() && ! $unlock;

            if ($requiresApproval) {
                $record->update([
                    'attendance_approval_status' => EmployeeAttendanceRecord::APPROVAL_PENDING,
                    'attendance_requested_at' => $now,
                    'approval_rejection_reason' => null,
                    'clock_in_ip' => $ip,
                    'status' => 'pending',
                    'metadata' => array_merge($record->metadata ?? [], [
                        'attendance_request_ip' => $ip,
                        'attendance_requested_via' => 'employee_clock_in',
                    ]),
                ]);

                return $record->fresh();
            }

            $window = $this->scheduleWindow($state['schedule'], $now);
            $isLate = ! $unlock && $now->gt($window['shift_starts_at']->copy()->addMinutes((int) $state['schedule']->grace_minutes));

            $metadata = $record->metadata ?? [];
            if ($unlock) {
                $metadata['clock_in_via_manager_unlock'] = true;
                $metadata['unlock_id'] = $unlock['id'] ?? null;
            }

            $record->update([
                'clock_in_at' => $now,
                'clock_in_ip' => $ip,
                'status' => 'active',
                'is_late' => $isLate,
                'attendance_approval_status' => EmployeeAttendanceRecord::APPROVAL_NOT_REQUIRED,
                'attendance_requested_at' => $now,
                'metadata' => $metadata,
            ]);

            $record = $record->fresh(['user']);
            if ($isLate) {
                app(EmployeeAttendancePenaltyService::class)->applyLatePenalty($record);
            }

            $user->forceFill([
                'presence_last_seen_at' => $now,
                'presence_status' => 'online',
            ])->save();

            return $record->fresh();
        });
    }

    /**
     * موافقة مدير المبيعات على طلب حضور موظف أوفلاين.
     *
     * @param  'on_time'|'excused_late'|'confirmed_late'  $latenessDecision
     */
    public function approveAttendanceRequest(
        EmployeeAttendanceRecord $record,
        User $manager,
        string $latenessDecision,
        ?string $ip = null,
    ): EmployeeAttendanceRecord {
        if (! in_array($latenessDecision, [
            EmployeeAttendanceRecord::LATENESS_ON_TIME,
            EmployeeAttendanceRecord::LATENESS_EXCUSED,
            EmployeeAttendanceRecord::LATENESS_CONFIRMED,
        ], true)) {
            throw ValidationException::withMessages([
                'lateness_decision' => 'قرار التأخير غير صالح.',
            ]);
        }

        return DB::transaction(function () use ($record, $manager, $latenessDecision, $ip) {
            $record = EmployeeAttendanceRecord::query()->whereKey($record->id)->lockForUpdate()->firstOrFail();

            if ($record->clock_in_at) {
                throw ValidationException::withMessages(['attendance' => 'تم تثبيت الحضور مسبقاً.']);
            }

            if ($record->attendance_approval_status !== EmployeeAttendanceRecord::APPROVAL_PENDING) {
                throw ValidationException::withMessages(['attendance' => 'لا يوجد طلب حضور معلّق لهذا اليوم.']);
            }

            $clockInAt = $record->attendance_requested_at ?? now();
            $markLate = $latenessDecision !== EmployeeAttendanceRecord::LATENESS_ON_TIME;
            $waivePenalty = $latenessDecision !== EmployeeAttendanceRecord::LATENESS_CONFIRMED;

            $record->update([
                'clock_in_at' => $clockInAt,
                'clock_in_ip' => $ip ?: $record->clock_in_ip,
                'status' => 'active',
                'is_late' => $markLate,
                'attendance_approval_status' => EmployeeAttendanceRecord::APPROVAL_APPROVED,
                'approved_by' => $manager->id,
                'approved_at' => now(),
                'manager_lateness_decision' => $latenessDecision,
                'late_penalty_waived' => $waivePenalty && $markLate,
                'approval_rejection_reason' => null,
                'metadata' => array_merge($record->metadata ?? [], [
                    'approved_by_manager' => $manager->id,
                    'approved_at' => now()->toIso8601String(),
                    'lateness_decision' => $latenessDecision,
                ]),
            ]);

            $record = $record->fresh(['user']);

            if ($latenessDecision === EmployeeAttendanceRecord::LATENESS_CONFIRMED) {
                app(EmployeeAttendancePenaltyService::class)->applyLatePenalty($record);
            }

            $record->user?->forceFill([
                'presence_last_seen_at' => now(),
                'presence_status' => 'online',
            ])->save();

            return $record->fresh();
        });
    }

    public function rejectAttendanceRequest(
        EmployeeAttendanceRecord $record,
        User $manager,
        string $reason,
    ): EmployeeAttendanceRecord {
        $reason = trim($reason);
        if (mb_strlen($reason) < 3) {
            throw ValidationException::withMessages([
                'reason' => 'اكتب سبب الرفض (3 أحرف على الأقل).',
            ]);
        }

        return DB::transaction(function () use ($record, $manager, $reason) {
            $record = EmployeeAttendanceRecord::query()->whereKey($record->id)->lockForUpdate()->firstOrFail();

            if ($record->clock_in_at || $record->attendance_approval_status !== EmployeeAttendanceRecord::APPROVAL_PENDING) {
                throw ValidationException::withMessages(['attendance' => 'لا يوجد طلب حضور معلّق.']);
            }

            $record->update([
                'attendance_approval_status' => EmployeeAttendanceRecord::APPROVAL_REJECTED,
                'approved_by' => $manager->id,
                'approved_at' => now(),
                'approval_rejection_reason' => $reason,
                'attendance_requested_at' => null,
                'metadata' => array_merge($record->metadata ?? [], [
                    'rejected_by_manager' => $manager->id,
                    'rejected_at' => now()->toIso8601String(),
                    'rejection_reason' => $reason,
                ]),
            ]);

            return $record->fresh();
        });
    }

    /**
     * إعفاء خصم التأخير لموظف أونلاين (أو بعد التأكيد).
     */
    public function waiveLatePenalty(EmployeeAttendanceRecord $record, User $manager, ?string $note = null): EmployeeAttendanceRecord
    {
        return DB::transaction(function () use ($record, $manager, $note) {
            $record = EmployeeAttendanceRecord::query()->whereKey($record->id)->lockForUpdate()->firstOrFail();

            if (! $record->clock_in_at) {
                throw ValidationException::withMessages(['attendance' => 'لا يوجد حضور مثبت.']);
            }

            $record->update([
                'late_penalty_waived' => true,
                'manager_lateness_decision' => EmployeeAttendanceRecord::LATENESS_EXCUSED,
                'approved_by' => $record->approved_by ?: $manager->id,
                'approved_at' => $record->approved_at ?: now(),
                'metadata' => array_merge($record->metadata ?? [], [
                    'late_waived_by' => $manager->id,
                    'late_waived_at' => now()->toIso8601String(),
                    'late_waive_note' => $note,
                ]),
            ]);

            app(EmployeeAttendancePenaltyService::class)->revokeLatePenalty($record);

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
            return $this->syncDayStatusFromEmployeeFile($user, $existing, $now);
        }

        $window = $this->scheduleWindow($schedule, $now);
        $base = [
            'user_id' => $user->id,
            'work_schedule_id' => $schedule->id,
            'work_date' => $now->toDateString(),
            'scheduled_start' => $window['shift_starts_at'],
            'scheduled_end' => $window['shift_ends_at'],
            'required_minutes' => (int) round((float) $schedule->required_hours * 60),
        ];

        // يوم الراحة من ملف الموظف (أوفلاين بأيام محددة أو weekly_off_day)
        if ($user->isAttendanceOffDay($now)) {
            return EmployeeAttendanceRecord::create($base + ['status' => 'off_day']);
        }

        if ($user->isOnApprovedLeave($now)) {
            return EmployeeAttendanceRecord::create($base + ['status' => 'on_leave']);
        }

        return EmployeeAttendanceRecord::create($base + ['status' => 'pending']);
    }

    /**
     * مزامنة حالة يوم الحضور مع يوم الإجازة/الإجازة المعتمدة من ملف الموظف.
     * لا تُغيَّر السجلات التي بدأ فيها الحضور أو اكتملت، ولا أثناء فتح المدير النشط.
     */
    public function syncDayStatusFromEmployeeFile(
        User $user,
        EmployeeAttendanceRecord $record,
        ?Carbon $now = null
    ): EmployeeAttendanceRecord {
        $now = $now ?? now();

        if ($record->clock_in_at || in_array($record->status, ['active', 'completed'], true)) {
            return $record;
        }

        if ($record->isAwaitingManagerApproval()) {
            return $record;
        }

        if (! in_array($record->status, ['pending', 'off_day', 'on_leave', 'absent'], true)) {
            return $record;
        }

        $desired = $this->desiredDayStatus($user, $now);

        // فتح المدير يبقي اليوم قابلاً للعمل حتى لو كان يوم راحة
        if (in_array($desired, ['off_day', 'on_leave'], true) && $this->activeUnlock($user, $now)) {
            if ($record->status === 'pending') {
                return $record;
            }
            $desired = 'pending';
        }

        if ($record->status === $desired) {
            return $record;
        }

        $record->update([
            'status' => $desired,
            'metadata' => array_merge($record->metadata ?? [], [
                'synced_from_employee_file_at' => $now->toIso8601String(),
                'synced_weekly_off_day' => $user->weekly_off_day,
            ]),
        ]);

        return $record->fresh();
    }

    /**
     * بعد تحديث ملف الموظف: أعد حساب حالة اليوم الحالي إن وُجد سجل.
     */
    public function resyncTodayAfterEmployeeUpdate(User $user): ?EmployeeAttendanceRecord
    {
        if (! $user->isSubjectToWorkSchedule()) {
            return null;
        }

        $schedule = $this->resolveSchedule($user);
        if (! $schedule) {
            return null;
        }

        return $this->ensureTodayRecord($user, $schedule, now());
    }

    private function desiredDayStatus(User $user, Carbon $now): string
    {
        if ($user->isAttendanceOffDay($now)) {
            return 'off_day';
        }

        if ($user->isOnApprovedLeave($now)) {
            return 'on_leave';
        }

        return 'pending';
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

    private function resolveUnlockExpiry(Carbon $now, string $durationKey): Carbon
    {
        return match ($durationKey) {
            '2h' => $now->copy()->addHours(2),
            '4h' => $now->copy()->addHours(4),
            '8h' => $now->copy()->addHours(8),
            default => $now->copy()->endOfDay(),
        };
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
            'unlock' => null,
            'work_mode' => User::WORK_MODE_ONLINE,
            'requires_manager_approval' => false,
            'attendance_requested_at' => null,
        ], $data);
    }
}
