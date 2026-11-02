<?php

namespace App\Domains\Advising\Services;

use App\Support\PlatformSettings;

/** إعدادات الإرشاد المهني (محفوظة في إعدادات المنصة تحت advising). */
final class AdvisingSettings
{
    public const DEFAULTS = [
        'enabled' => true,
        'window_days' => 14,       // الحجز متاح لكام يوم قدام
        'min_notice_hours' => 3,   // أقل وقت قبل الجلسة يسمح بالحجز
    ];

    /** @return array{enabled: bool, window_days: int, min_notice_hours: int} */
    public static function get(): array
    {
        $stored = PlatformSettings::all()['advising'] ?? [];
        $s = array_merge(self::DEFAULTS, is_array($stored) ? $stored : []);

        return [
            'enabled' => ! in_array($s['enabled'], [false, 0, '0', 'false'], true),
            'window_days' => max(1, min(60, (int) $s['window_days'])),
            'min_notice_hours' => max(0, min(72, (int) $s['min_notice_hours'])),
        ];
    }

    public static function save(array $data): void
    {
        PlatformSettings::save(['advising' => array_merge(self::get(), array_intersect_key($data, self::DEFAULTS))]);
    }
}
