<?php

namespace App\Support;

class EmployeeAttendanceSettings
{
    private const FILE = 'employee_attendance_settings.json';

    public static function all(): array
    {
        $defaults = config('employee_attendance', []);
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

    public static function penaltiesEnabled(): bool
    {
        return (bool) self::all()['penalties_enabled'];
    }

    public static function latePenaltyEnabled(): bool
    {
        return self::penaltiesEnabled() && (bool) self::all()['late_penalty_enabled'];
    }

    public static function absencePenaltyEnabled(): bool
    {
        return self::penaltiesEnabled() && (bool) self::all()['absence_penalty_enabled'];
    }

    public static function incompletePenaltyEnabled(): bool
    {
        return self::penaltiesEnabled() && (bool) self::all()['incomplete_penalty_enabled'];
    }

    public static function latePenaltyAmount(): float
    {
        return (float) self::all()['late_penalty_amount'];
    }

    public static function absencePenaltyAmount(): float
    {
        return (float) self::all()['absence_penalty_amount'];
    }

    public static function incompletePenaltyAmount(): float
    {
        return (float) self::all()['incomplete_penalty_amount'];
    }
}
