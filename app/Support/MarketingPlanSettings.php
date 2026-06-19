<?php

namespace App\Support;

class MarketingPlanSettings
{
    private const FILE = 'marketing_plan_settings.json';

    public static function all(): array
    {
        $defaults = config('marketing_plan', []);
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

    public static function automationEnabled(): bool
    {
        return (bool) self::all()['automation_enabled'];
    }

    public static function penaltyEnabled(): bool
    {
        return (bool) self::all()['penalty_enabled'];
    }

    public static function penaltyAmount(): float
    {
        return (float) self::all()['penalty_amount'];
    }

    public static function autoCreateTasks(): bool
    {
        return (bool) self::all()['auto_create_tasks'];
    }

    public static function reminderTime(): string
    {
        return (string) (self::all()['reminder_time'] ?? '10:00');
    }

    public static function confirmationDeadlineTime(): string
    {
        return (string) (self::all()['confirmation_deadline_time'] ?? '22:00');
    }
}
