<?php

namespace App\Services;

use App\Models\EmployeeAttendanceRecord;
use App\Models\EmployeePresenceDaily;
use App\Models\EmployeePresenceViolation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EmployeePresenceService
{
    public function __construct(
        private EmployeeAttendanceService $attendance,
    ) {}

    public function isEnabled(): bool
    {
        return (bool) config('employee_presence.enabled', true);
    }

    public function shouldTrack(User $user): bool
    {
        return $this->isEnabled()
            && $user->isSubjectToWorkSchedule();
    }

    /**
     * @return array{success: bool, status: string, message?: string, redirect?: string}
     */
    public function heartbeat(User $user, Request $request): array
    {
        if (! $this->shouldTrack($user)) {
            return ['success' => true, 'status' => 'exempt'];
        }

        $record = $this->activeAttendanceRecord($user);
        if (! $record) {
            return [
                'success' => false,
                'status' => 'not_on_shift',
                'message' => 'يجب تسجيل الحضور أولاً.',
            ];
        }

        $now = now();
        $previousSeen = $user->presence_last_seen_at;
        $gapSeconds = $previousSeen ? (int) $previousSeen->diffInSeconds($now) : 0;

        $this->closeOpenViolationOnReturn($user, $now);

        $daily = $this->touchDaily($user, $record, $now, $gapSeconds);

        $user->forceFill([
            'presence_last_seen_at' => $now,
            'presence_status' => 'online',
        ])->save();

        return [
            'success' => true,
            'status' => 'online',
            'heartbeat_interval' => $this->heartbeatInterval(),
            'away_threshold' => $this->awayThreshold(),
            'offline_threshold' => $this->offlineThreshold(),
            'violation_count_today' => $daily->violation_count,
            'session_active' => $this->hasActiveSession($user),
        ];
    }

    public function memberPresenceStatus(User $user, ?Carbon $now = null): array
    {
        $now = $now ?? now();
        $record = $this->todayAttendanceRecord($user);
        $sessionActive = $this->hasActiveSession($user);
        $lastSeen = $user->presence_last_seen_at;
        $secondsSinceSeen = $lastSeen ? (int) $lastSeen->diffInSeconds($now) : null;

        if (! $record || ! $record->clock_in_at) {
            return $this->presencePayload($user, 'not_clocked_in', $lastSeen, $secondsSinceSeen, $sessionActive, $record);
        }

        if ($record->isCompleted()) {
            return $this->presencePayload($user, 'shift_completed', $lastSeen, $secondsSinceSeen, $sessionActive, $record);
        }

        if (! $sessionActive && $record->isActive()) {
            return $this->presencePayload($user, 'logged_out', $lastSeen, $secondsSinceSeen, false, $record);
        }

        $status = $this->resolveLiveStatus($secondsSinceSeen, $sessionActive);

        return $this->presencePayload($user, $status, $lastSeen, $secondsSinceSeen, $sessionActive, $record);
    }

    /** @param list<int> $memberIds */
    public function teamPresenceBoard(array $memberIds, ?Carbon $now = null): Collection
    {
        $now = $now ?? now();

        return User::query()
            ->whereIn('id', $memberIds)
            ->orderBy('name')
            ->get()
            ->map(fn (User $user) => $this->memberPresenceStatus($user, $now));
    }

    public function scanOfflineEmployees(?Carbon $now = null): int
    {
        if (! $this->isEnabled()) {
            return 0;
        }

        $now = $now ?? now();
        $offlineCutoff = $now->copy()->subSeconds($this->offlineThreshold());
        $created = 0;

        $records = EmployeeAttendanceRecord::query()
            ->whereDate('work_date', $now->toDateString())
            ->whereNotNull('clock_in_at')
            ->whereNull('clock_out_at')
            ->whereIn('status', ['active', 'late'])
            ->whereHas('user', fn ($q) => $q->where('is_employee', true))
            ->with('user')
            ->get();

        foreach ($records as $record) {
            $user = $record->user;
            if (! $user || ! $user->isSubjectToWorkSchedule()) {
                continue;
            }

            $lastSeen = $user->presence_last_seen_at;
            if (! $lastSeen || $lastSeen->lt($offlineCutoff)) {
                if ($this->openViolationIfNeeded($user, $record, $now)) {
                    $created++;
                }
                $user->forceFill(['presence_status' => 'offline'])->save();
            } elseif ($lastSeen->lt($now->copy()->subSeconds($this->awayThreshold()))) {
                $user->forceFill(['presence_status' => 'away'])->save();
            }
        }

        return $created;
    }

    public function closeViolationsOnClockOut(User $user): void
    {
        $this->closeOpenViolation($user, now());
        $user->forceFill([
            'presence_status' => 'offline',
        ])->save();
    }

    /** @return array<string, mixed> */
    private function presencePayload(
        User $user,
        string $status,
        ?Carbon $lastSeen,
        ?int $secondsSinceSeen,
        bool $sessionActive,
        ?EmployeeAttendanceRecord $record
    ): array {
        $daily = EmployeePresenceDaily::query()
            ->where('user_id', $user->id)
            ->whereDate('work_date', today())
            ->first();

        $openViolation = EmployeePresenceViolation::query()
            ->where('user_id', $user->id)
            ->whereDate('work_date', today())
            ->open()
            ->first();

        return [
            'user_id' => $user->id,
            'name' => $user->name,
            'status' => $status,
            'status_label' => $this->statusLabel($status),
            'status_color' => $this->statusColor($status),
            'last_seen_at' => $lastSeen?->toIso8601String(),
            'last_seen_human' => $lastSeen?->diffForHumans(),
            'seconds_since_seen' => $secondsSinceSeen,
            'session_active' => $sessionActive,
            'clock_in_at' => $record?->clock_in_at?->format('H:i'),
            'clock_out_at' => $record?->clock_out_at?->format('H:i'),
            'violations_today' => $daily?->violation_count ?? 0,
            'offline_minutes_today' => (int) round(($daily?->offline_seconds ?? 0) / 60),
            'open_violation' => $openViolation ? [
                'id' => $openViolation->id,
                'started_at' => $openViolation->started_at->format('H:i'),
                'duration_seconds' => (int) $openViolation->started_at->diffInSeconds(now()),
            ] : null,
        ];
    }

    private function resolveLiveStatus(?int $secondsSinceSeen, bool $sessionActive): string
    {
        if (! $sessionActive) {
            return 'logged_out';
        }

        if ($secondsSinceSeen === null) {
            return 'offline';
        }

        if ($secondsSinceSeen >= $this->offlineThreshold()) {
            return 'offline';
        }

        if ($secondsSinceSeen >= $this->awayThreshold()) {
            return 'away';
        }

        return 'online';
    }

    public function statusLabel(string $status): string
    {
        return match ($status) {
            'online' => 'متصل — النظام مفتوح',
            'away' => 'بعيد — بدون نشاط',
            'offline' => 'غير متصل — مخالفة',
            'logged_out' => 'خارج النظام (جلسة منتهية)',
            'not_clocked_in' => 'لم يسجّل حضور',
            'shift_completed' => 'أنهى الدوام',
            default => $status,
        };
    }

    public function statusColor(string $status): string
    {
        return match ($status) {
            'online' => 'emerald',
            'away' => 'amber',
            'offline', 'logged_out' => 'rose',
            'not_clocked_in' => 'slate',
            'shift_completed' => 'blue',
            default => 'gray',
        };
    }

    private function touchDaily(User $user, EmployeeAttendanceRecord $record, Carbon $now, int $gapSeconds): EmployeePresenceDaily
    {
        $daily = EmployeePresenceDaily::firstOrNew([
            'user_id' => $user->id,
            'work_date' => $now->toDateString(),
        ]);

        if (! $daily->exists) {
            $daily->first_seen_at = $now;
            $daily->employee_attendance_record_id = $record->id;
        }

        $daily->last_seen_at = $now;
        $daily->heartbeat_count = (int) $daily->heartbeat_count + 1;

        if ($gapSeconds > 0 && $gapSeconds < $this->offlineThreshold()) {
            $daily->online_seconds = (int) $daily->online_seconds + min($gapSeconds, $this->heartbeatInterval() * 2);
        } elseif ($gapSeconds >= $this->awayThreshold() && $gapSeconds < $this->offlineThreshold()) {
            $daily->away_seconds = (int) $daily->away_seconds + ($gapSeconds - $this->awayThreshold());
        }

        $daily->save();

        return $daily;
    }

    private function closeOpenViolationOnReturn(User $user, Carbon $now): void
    {
        $open = EmployeePresenceViolation::query()
            ->where('user_id', $user->id)
            ->whereDate('work_date', $now->toDateString())
            ->open()
            ->first();

        if (! $open) {
            return;
        }

        $duration = (int) $open->started_at->diffInSeconds($now);
        $open->update([
            'ended_at' => $now,
            'duration_seconds' => $duration,
            'status' => EmployeePresenceViolation::STATUS_CLOSED,
        ]);

        $daily = EmployeePresenceDaily::query()
            ->where('user_id', $user->id)
            ->whereDate('work_date', $now->toDateString())
            ->first();

        if ($daily && $duration >= $this->violationMinSeconds()) {
            $daily->increment('offline_seconds', $duration);
        }
    }

    private function openViolationIfNeeded(User $user, EmployeeAttendanceRecord $record, Carbon $now): bool
    {
        $exists = EmployeePresenceViolation::query()
            ->where('user_id', $user->id)
            ->whereDate('work_date', $now->toDateString())
            ->open()
            ->exists();

        if ($exists) {
            return false;
        }

        $startedAt = $user->presence_last_seen_at
            ? $user->presence_last_seen_at->copy()->addSeconds($this->awayThreshold())
            : $now->copy()->subSeconds($this->offlineThreshold());

        $reason = $this->hasActiveSession($user)
            ? EmployeePresenceViolation::REASON_NO_HEARTBEAT
            : EmployeePresenceViolation::REASON_SESSION_EXPIRED;

        EmployeePresenceViolation::create([
            'user_id' => $user->id,
            'work_date' => $now->toDateString(),
            'employee_attendance_record_id' => $record->id,
            'started_at' => $startedAt,
            'reason' => $reason,
            'status' => EmployeePresenceViolation::STATUS_OPEN,
        ]);

        $daily = EmployeePresenceDaily::firstOrCreate(
            ['user_id' => $user->id, 'work_date' => $now->toDateString()],
            ['employee_attendance_record_id' => $record->id, 'first_seen_at' => $startedAt]
        );
        $daily->increment('violation_count');

        return true;
    }

    private function closeOpenViolation(User $user, Carbon $now): void
    {
        $open = EmployeePresenceViolation::query()
            ->where('user_id', $user->id)
            ->whereDate('work_date', $now->toDateString())
            ->open()
            ->first();

        if ($open) {
            $duration = (int) $open->started_at->diffInSeconds($now);
            $open->update([
                'ended_at' => $now,
                'duration_seconds' => $duration,
                'status' => EmployeePresenceViolation::STATUS_CLOSED,
            ]);

            if ($duration >= $this->violationMinSeconds()) {
                EmployeePresenceDaily::query()
                    ->where('user_id', $user->id)
                    ->whereDate('work_date', $now->toDateString())
                    ->increment('offline_seconds', $duration);
            }
        }
    }

    private function activeAttendanceRecord(User $user): ?EmployeeAttendanceRecord
    {
        return EmployeeAttendanceRecord::query()
            ->where('user_id', $user->id)
            ->whereDate('work_date', today())
            ->whereNotNull('clock_in_at')
            ->whereNull('clock_out_at')
            ->whereIn('status', ['active', 'late'])
            ->first();
    }

    private function todayAttendanceRecord(User $user): ?EmployeeAttendanceRecord
    {
        return EmployeeAttendanceRecord::query()
            ->where('user_id', $user->id)
            ->whereDate('work_date', today())
            ->first();
    }

    public function hasActiveSession(User $user): bool
    {
        if (! DB::getSchemaBuilder()->hasTable('sessions')) {
            return $user->presence_last_seen_at && $user->presence_last_seen_at->gt(now()->subMinutes((int) config('session.lifetime', 120)));
        }

        $lifetime = (int) config('session.lifetime', 120) * 60;

        return DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('last_activity', '>=', now()->timestamp - $lifetime)
            ->exists();
    }

    public function heartbeatInterval(): int
    {
        return max(30, (int) config('employee_presence.heartbeat_interval_seconds', 45));
    }

    public function awayThreshold(): int
    {
        return max(60, (int) config('employee_presence.away_threshold_seconds', 120));
    }

    public function offlineThreshold(): int
    {
        return max(120, (int) config('employee_presence.offline_threshold_seconds', 300));
    }

    public function violationMinSeconds(): int
    {
        return max(60, (int) config('employee_presence.violation_min_seconds', 180));
    }
}
