<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class SalesDailyReportSettings
{
    private const STORAGE_PATH = 'site/sales_daily_report_settings.json';

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        $config = config('sales_daily_report');

        return is_array($config) ? $config : [];
    }

    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        $defaults = self::defaults();

        try {
            $disk = Storage::disk('public');
            if (! $disk->exists(self::STORAGE_PATH)) {
                return $defaults;
            }
            $raw = $disk->get(self::STORAGE_PATH);
            $data = json_decode($raw, true);

            return array_merge($defaults, is_array($data) ? $data : []);
        } catch (\Throwable) {
            return $defaults;
        }
    }

    public static function enabled(): bool
    {
        return (bool) (self::all()['enabled'] ?? true);
    }

    public static function penaltyEnabled(): bool
    {
        return (bool) (self::all()['penalty_enabled'] ?? true);
    }

    public static function penaltyAmount(): float
    {
        return max(0.01, (float) (self::all()['penalty_amount'] ?? 50));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function save(array $data): void
    {
        $disk = Storage::disk('public');
        if (! $disk->exists('site')) {
            $disk->makeDirectory('site');
        }
        $merged = array_merge(self::all(), $data);
        $disk->put(self::STORAGE_PATH, json_encode($merged, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
}
