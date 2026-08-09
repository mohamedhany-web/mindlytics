<?php

namespace App\Support;

use App\Models\User;
use Carbon\Carbon;

/**
 * يحدد التواريخ المسموح باحتساب غرامة تلقائية عنها.
 *
 * القاعدة: لا خصم قبل تاريخ تعيين الموظف، ولا بعد إنهاء خدمته،
 * ولا قبل تاريخ سريان الخصومات الذي تحدده الإدارة.
 */
class PenaltyWindow
{
    public const SALES_DAILY_REPORT = 'sales_daily_report';

    public const EMPLOYEE_DAILY_REPORT = 'employee_daily_report';

    public const ATTENDANCE = 'attendance';

    public const SALES_DAILY_KPI = 'sales_daily_kpi';

    public static function effectiveFrom(string $subsystem): ?Carbon
    {
        $raw = match ($subsystem) {
            self::SALES_DAILY_REPORT => SalesDailyReportSettings::all()['penalty_effective_from'] ?? null,
            self::EMPLOYEE_DAILY_REPORT => EmployeeDailyReportSettings::all()['penalty_effective_from'] ?? null,
            self::ATTENDANCE => EmployeeAttendanceSettings::all()['penalty_effective_from'] ?? null,
            self::SALES_DAILY_KPI => config('sales_kpi.daily_kpi_penalty.penalty_effective_from'),
            default => null,
        };

        if (blank($raw)) {
            return null;
        }

        try {
            return Carbon::parse((string) $raw)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    public static function isChargeable(string $subsystem, User $employee, Carbon $date): bool
    {
        $day = $date->copy()->startOfDay();

        if (! $employee->isEmployedOn($day)) {
            return false;
        }

        $effectiveFrom = self::effectiveFrom($subsystem);

        return ! ($effectiveFrom && $day->lt($effectiveFrom));
    }

    /**
     * أقرب تاريخ يجوز البدء منه عند احتساب غرامات بأثر رجعي لموظف بعينه.
     */
    public static function earliestChargeableDate(string $subsystem, User $employee, Carbon $requestedFrom): Carbon
    {
        $from = $requestedFrom->copy()->startOfDay();

        if ($employee->hire_date) {
            $hireDate = Carbon::parse($employee->hire_date)->startOfDay();
            if ($from->lt($hireDate)) {
                $from = $hireDate;
            }
        }

        $effectiveFrom = self::effectiveFrom($subsystem);
        if ($effectiveFrom && $from->lt($effectiveFrom)) {
            $from = $effectiveFrom;
        }

        return $from;
    }
}
