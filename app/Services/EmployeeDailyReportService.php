<?php

namespace App\Services;

use App\Models\EmployeeDailyReport;
use App\Models\EmployeeSalaryDeduction;
use App\Models\Notification;
use App\Models\User;
use App\Support\EmployeeDailyReportSettings;
use App\Support\PenaltyWindow;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EmployeeDailyReportService
{
    public function employeeRequiresReport(User $employee, Carbon $date): bool
    {
        if (! EmployeeDailyReportSettings::enabled()) {
            return false;
        }

        if (! $employee->isEmployee() || ! $employee->is_active) {
            return false;
        }

        if (EmployeeDailyReportSettings::all()['exclude_sales_employees'] && $employee->isSalesEmployee()) {
            return false;
        }

        return $employee->requiresDailyReportOn($date);
    }

    public function saveReport(User $user, Carbon $date, array $payload, bool $submit): EmployeeDailyReport
    {
        $report = EmployeeDailyReport::firstOrNew([
            'user_id' => $user->id,
            'report_date' => $date->toDateString(),
        ]);

        $report->fill([
            'summary' => $payload['summary'] ?? null,
            'tasks_done' => $payload['tasks_done'] ?? null,
            'tomorrow_plan' => $payload['tomorrow_plan'] ?? null,
            'blockers' => $payload['blockers'] ?? null,
            'hours_worked' => $payload['hours_worked'] ?? null,
        ]);

        if ($submit) {
            $report->status = EmployeeDailyReport::STATUS_SUBMITTED;
            $report->submitted_at = now();
        } else {
            $report->status = EmployeeDailyReport::STATUS_DRAFT;
        }

        $report->save();

        return $report->fresh();
    }

    public function submissionRateForMonth(Collection $employees, Carbon $month): array
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $workDays = 0;
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            if ($employees->first() && $this->employeeRequiresReport($employees->first(), $d)) {
                $workDays++;
            }
        }

        $rows = [];
        foreach ($employees as $emp) {
            $required = 0;
            $submitted = 0;
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                if (! $this->employeeRequiresReport($emp, $d)) {
                    continue;
                }
                $required++;
                if (EmployeeDailyReport::forUser($emp->id)
                    ->whereDate('report_date', $d)
                    ->where('status', EmployeeDailyReport::STATUS_SUBMITTED)
                    ->exists()) {
                    $submitted++;
                }
            }
            $rows[] = [
                'employee' => $emp,
                'required' => $required,
                'submitted' => $submitted,
                'rate' => $required > 0 ? round($submitted / $required * 100, 1) : null,
            ];
        }

        return $rows;
    }

    public function applyPenaltyForDate(User $employee, Carbon $date): ?EmployeeSalaryDeduction
    {
        if (! EmployeeDailyReportSettings::enabled() || ! EmployeeDailyReportSettings::penaltyEnabled()) {
            return null;
        }

        if (! $this->employeeRequiresReport($employee, $date)) {
            return null;
        }

        if (! PenaltyWindow::isChargeable(PenaltyWindow::EMPLOYEE_DAILY_REPORT, $employee, $date)) {
            return null;
        }

        $report = EmployeeDailyReport::forUser($employee->id)
            ->whereDate('report_date', $date)
            ->first();

        if ($report?->isSubmitted()) {
            return null;
        }

        if ($report?->penalty_waived_at) {
            return null;
        }

        if ($report?->auto_deduction_id) {
            return $report->autoDeduction;
        }

        $deduction = EmployeeSalaryDeduction::createWithAutoDeductionNumber([
            'employee_id' => $employee->id,
            'title' => 'غرامة — تقرير يومي لم يُرسل',
            'description' => 'لم يُسلّم التقرير اليومي لتاريخ '.$date->format('Y-m-d'),
            'amount' => EmployeeDailyReportSettings::penaltyAmount(),
            'type' => 'penalty',
            'deduction_date' => $date->toDateString(),
            'status' => 'applied',
            'created_by' => null,
        ]);

        if ($report) {
            $report->update(['auto_deduction_id' => $deduction->id]);
        } else {
            EmployeeDailyReport::create([
                'user_id' => $employee->id,
                'report_date' => $date->toDateString(),
                'status' => EmployeeDailyReport::STATUS_DRAFT,
                'auto_deduction_id' => $deduction->id,
            ]);
        }

        Notification::create([
            'user_id' => $employee->id,
            'sender_id' => null,
            'title' => 'غرامة — تقرير يومي',
            'message' => 'لم تُسلّم تقريرك اليومي لتاريخ '.$date->format('Y-m-d').'. خصم '.number_format((float) $deduction->amount, 2).' ج.م.',
            'type' => 'warning',
            'priority' => 'high',
            'audience' => 'employee',
            'action_url' => route('employee.daily-reports.edit'),
            'action_text' => 'إرسال التقرير',
            'data' => ['kind' => 'employee_daily_report_penalty'],
        ]);

        return $deduction;
    }

    public function isPenaltyDueForDate(Carbon $date): bool
    {
        if ($date->isFuture()) {
            return false;
        }

        if ($date->isToday()) {
            return now()->greaterThan($date->copy()->endOfDay());
        }

        return true;
    }

    /**
     * @param  iterable<int, User>|null  $employees
     */
    public function applyDuePenaltiesInRange(Carbon $from, Carbon $to, ?iterable $employees = null): int
    {
        if (! EmployeeDailyReportSettings::enabled() || ! EmployeeDailyReportSettings::penaltyEnabled()) {
            return 0;
        }

        $this->ensureLinkedAutoDeductionsApplied();

        $employees = $employees ?? User::employees()->where('is_active', true)->get();
        $count = 0;
        $end = $to->copy()->startOfDay();

        foreach ($employees as $employee) {
            $cursor = PenaltyWindow::earliestChargeableDate(
                PenaltyWindow::EMPLOYEE_DAILY_REPORT,
                $employee,
                $from
            );

            while ($cursor->lte($end)) {
                if ($this->isPenaltyDueForDate($cursor) && $this->applyPenaltyForDate($employee, $cursor)) {
                    $count++;
                }
                $cursor->addDay();
            }
        }

        return $count;
    }

    private function ensureLinkedAutoDeductionsApplied(): void
    {
        $ids = EmployeeDailyReport::query()
            ->whereNotNull('auto_deduction_id')
            ->pluck('auto_deduction_id');

        if ($ids->isEmpty()) {
            return;
        }

        EmployeeSalaryDeduction::query()
            ->whereIn('id', $ids)
            ->where('status', 'pending')
            ->update(['status' => 'applied']);
    }

    public function sendReminder(User $employee): void
    {
        $exists = Notification::query()
            ->where('user_id', $employee->id)
            ->where('title', 'تذكير: التقرير اليومي')
            ->whereDate('created_at', today())
            ->exists();

        if ($exists) {
            return;
        }

        Notification::create([
            'user_id' => $employee->id,
            'sender_id' => null,
            'title' => 'تذكير: التقرير اليومي',
            'message' => 'لم تُسلّم تقرير اليوم بعد. يُطبَّق خصم تلقائي إذا لم يُرسل قبل نهاية اليوم.',
            'type' => 'reminder',
            'priority' => 'high',
            'audience' => 'employee',
            'action_url' => route('employee.daily-reports.edit'),
            'action_text' => 'إرسال الآن',
            'data' => ['kind' => 'employee_daily_report_reminder'],
        ]);
    }
}
