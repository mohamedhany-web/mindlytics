<?php

namespace App\Support;

class EmployeeDailyReportSettings
{
    private const FILE = 'employee_daily_report_settings.json';

    public static function all(): array
    {
        $defaults = config('employee_daily_report', []);
        $path = storage_path('app/'.self::FILE);

        if (! is_readable($path)) {
            return $defaults;
        }

        $json = json_decode((string) file_get_contents($path), true);

        return is_array($json) ? array_merge($defaults, $json) : $defaults;
    }

    public static function save(array $data): void
    {
        $merged = array_merge(self::all(), $data);
        file_put_contents(
            storage_path('app/'.self::FILE),
            json_encode($merged, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
    }

    public static function enabled(): bool
    {
        return (bool) self::all()['enabled'];
    }

    public static function penaltyEnabled(): bool
    {
        return (bool) self::all()['penalty_enabled'];
    }

    public static function penaltyAmount(): float
    {
        return (float) self::all()['penalty_amount'];
    }
}
