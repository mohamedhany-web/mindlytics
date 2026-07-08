<?php

namespace App\Services;

use App\Models\EmployeeAgreement;
use App\Models\EmployeeAttendanceRecord;
use App\Models\EmployeeSalaryDeduction;
use App\Models\Notification;
use App\Models\User;
use App\Support\EmployeeAttendanceSettings;
use Carbon\Carbon;

class EmployeeAttendancePenaltyService
{
    public function __construct(
        private EmployeeAttendanceService $attendance,
    ) {}

    public function applyLatePenalty(EmployeeAttendanceRecord $record): ?EmployeeSalaryDeduction
    {
        if (! EmployeeAttendanceSettings::latePenaltyEnabled() || ! $record->is_late || $record->late_deduction_id) {
            return null;
        }

        $deduction = $this->createDeduction(
            $record->user ?? $record->user()->first(),
            Carbon::parse($record->work_date),
            EmployeeAttendanceSettings::latePenaltyAmount(),
            (string) config('employee_attendance.late_penalty_title', 'غرامة تأخير حضور'),
            'تأخر عن موعد الدوام — '.$record->work_date->format('Y-m-d')
        );

        $record->update(['late_deduction_id' => $deduction->id]);

        $this->notifyEmployee($record->user, $deduction, 'تأخير حضور');

        return $deduction;
    }

    public function applyAbsencePenalty(User $employee, Carbon $date): ?EmployeeSalaryDeduction
    {
        if (! EmployeeAttendanceSettings::absencePenaltyEnabled()) {
            return null;
        }

        $schedule = $this->attendance->resolveSchedule($employee);
        if (! $schedule) {
            return null;
        }

        $record = $this->attendance->ensureTodayRecord($employee, $schedule, $date->copy());

        if (in_array($record->status, ['on_leave', 'off_day'], true)) {
            return null;
        }

        if ($record->clock_in_at || $record->absence_deduction_id) {
            return null;
        }

        $window = $this->attendance->scheduleWindow($schedule, $date);
        if (now()->lt($window['shift_ends_at']) && $date->isToday()) {
            return null;
        }

        $deduction = $this->createDeduction(
            $employee,
            Carbon::parse($record->work_date),
            EmployeeAttendanceSettings::absencePenaltyAmount(),
            (string) config('employee_attendance.absence_penalty_title', 'غرامة غياب'),
            'غياب دون تسجيل حضور — '.$record->work_date->format('Y-m-d')
        );

        $record->update([
            'absence_deduction_id' => $deduction->id,
            'status' => 'absent',
        ]);

        $this->notifyEmployee($employee, $deduction, 'غياب');

        return $deduction;
    }

    public function applyIncompletePenalty(EmployeeAttendanceRecord $record): ?EmployeeSalaryDeduction
    {
        if (! EmployeeAttendanceSettings::incompletePenaltyEnabled()) {
            return null;
        }

        if ($record->status !== 'incomplete' || $record->incomplete_deduction_id) {
            return null;
        }

        $deduction = $this->createDeduction(
            $record->user ?? $record->user()->first(),
            Carbon::parse($record->work_date),
            EmployeeAttendanceSettings::incompletePenaltyAmount(),
            (string) config('employee_attendance.incomplete_penalty_title', 'غرامة عدم إكمال ساعات العمل'),
            'لم تُكمل ساعات العمل المطلوبة — '.$record->work_date->format('Y-m-d')
            .' (عملت '.number_format(($record->worked_minutes ?? 0) / 60, 2).' س من '
            .number_format($record->required_minutes / 60, 2).' س)'
        );

        $record->update(['incomplete_deduction_id' => $deduction->id]);

        $this->notifyEmployee($record->user, $deduction, 'عدم إكمال الساعات');

        return $deduction;
    }

    /**
     * @return array{late: int, absence: int, incomplete: int, presence: int}
     */
    public function processDate(Carbon $date): array
    {
        $counts = ['late' => 0, 'absence' => 0, 'incomplete' => 0, 'presence' => 0];

        $employees = User::employees()
            ->where('is_active', true)
            ->with('employeeJob')
            ->get()
            ->filter(fn (User $employee) => $employee->isSubjectToWorkSchedule());

        foreach ($employees as $employee) {
            if ($deduction = $this->applyAbsencePenalty($employee, $date)) {
                $counts['absence']++;
            }

            $record = EmployeeAttendanceRecord::query()
                ->where('user_id', $employee->id)
                ->whereDate('work_date', $date->toDateString())
                ->first();

            if (! $record) {
                continue;
            }

            if ($record->is_late && ! $record->late_deduction_id && $record->clock_in_at) {
                if ($this->applyLatePenalty($record)) {
                    $counts['late']++;
                }
            }

            if ($record->status === 'incomplete' && ! $record->incomplete_deduction_id) {
                if ($this->applyIncompletePenalty($record->fresh())) {
                    $counts['incomplete']++;
                }
            }

            if ($this->applyPresencePenalty($record->fresh())) {
                $counts['presence']++;
            }
        }

        return $counts;
    }

    public function applyPresencePenalty(EmployeeAttendanceRecord $record): ?EmployeeSalaryDeduction
    {
        if (! (bool) config('employee_presence.presence_penalty_enabled', true)) {
            return null;
        }

        if ($record->presence_deduction_id || ! $record->clock_in_at) {
            return null;
        }

        $daily = \App\Models\EmployeePresenceDaily::query()
            ->where('user_id', $record->user_id)
            ->whereDate('work_date', $record->work_date)
            ->first();

        $offlineMinutes = (int) round(($daily?->offline_seconds ?? 0) / 60);
        $minMinutes = (int) config('employee_presence.presence_penalty_min_offline_minutes', 15);

        if ($offlineMinutes < $minMinutes) {
            return null;
        }

        $deduction = $this->createDeduction(
            $record->user ?? $record->user()->first(),
            Carbon::parse($record->work_date),
            (float) config('employee_presence.presence_penalty_amount', 35),
            (string) config('employee_presence.presence_penalty_title', 'غرامة انقطاع عن النظام أثناء الدوام'),
            'انقطاع عن النظام '.$offlineMinutes.' دقيقة أثناء الدوام — '.$record->work_date->format('Y-m-d')
        );

        $record->update(['presence_deduction_id' => $deduction->id]);
        $this->notifyEmployee($record->user, $deduction, 'انقطاع عن النظام');

        return $deduction;
    }

    private function createDeduction(User $employee, Carbon $date, float $amount, string $title, string $description): EmployeeSalaryDeduction
    {
        $settings = EmployeeAttendanceSettings::all();
        $agreement = EmployeeAgreement::query()
            ->where('employee_id', $employee->id)
            ->where('status', 'active')
            ->first();

        $type = in_array($settings['penalty_type'] ?? 'penalty', ['tax', 'insurance', 'loan', 'penalty', 'other'], true)
            ? $settings['penalty_type']
            : 'penalty';

        $status = in_array($settings['penalty_status'] ?? 'applied', ['pending', 'applied', 'cancelled'], true)
            ? $settings['penalty_status']
            : 'applied';

        return EmployeeSalaryDeduction::createWithAutoDeductionNumber([
            'employee_id' => $employee->id,
            'agreement_id' => $agreement?->id,
            'title' => $title,
            'description' => $description,
            'amount' => $amount,
            'type' => $type,
            'deduction_date' => $date->toDateString(),
            'status' => $status,
            'notes' => 'خصم تلقائي — حضور وانصراف',
            'created_by' => null,
        ]);
    }

    private function notifyEmployee(User $employee, EmployeeSalaryDeduction $deduction, string $reason): void
    {
        if (! (bool) config('employee_attendance.notify_employee', true)) {
            return;
        }

        Notification::create([
            'user_id' => $employee->id,
            'sender_id' => null,
            'title' => 'خصم راتب — '.$reason,
            'message' => $deduction->title.' — '.number_format((float) $deduction->amount, 2).' ج.م. '.$deduction->description,
            'type' => 'warning',
            'priority' => 'high',
            'audience' => 'employee',
            'action_url' => route('employee.accounting.index'),
            'action_text' => 'حساباتي',
        ]);
    }
}
