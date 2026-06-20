<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class PlatformSettings
{
    private const STORAGE_PATH = 'site/platform_settings.json';

    /**
     * @return array{platform_payment_mode: string}
     */
    private static function defaults(): array
    {
        return [
            'platform_payment_mode' => 'kashier',
            'fawaterak_gateway_enabled' => true,
            /** عمولة بوابة الدفع: none | percent | fixed */
            'gateway_fee_mode' => 'none',
            'gateway_fee_percent' => '0',
            'gateway_fee_fixed' => '0',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        $disk = Storage::disk('public');
        if (! $disk->exists(self::STORAGE_PATH)) {
            return self::defaults();
        }
        $raw = $disk->get(self::STORAGE_PATH);
        $data = json_decode($raw, true);

        return array_merge(self::defaults(), is_array($data) ? $data : []);
    }

    public static function paymentMode(): string
    {
        $m = (string) (self::all()['platform_payment_mode'] ?? 'kashier');
        if ($m === 'paymob') {
            $m = 'fawaterak';
        }

        return in_array($m, ['manual', 'kashier', 'fawaterak'], true) ? $m : 'kashier';
    }

    public static function isManualPayment(): bool
    {
        return self::paymentMode() === 'manual';
    }

    public static function isKashierPayment(): bool
    {
        return self::paymentMode() === 'kashier';
    }

    public static function isFawaterakPayment(): bool
    {
        return self::paymentMode() === 'fawaterak';
    }

    /**
     * @return array{
     *     hero_title: string,
     *     hero_subtitle: string,
     *     address: string,
     *     phone: string,
     *     email: string,
     *     whatsapp: string,
     *     hours: array<int, array{label: string, value: string, closed: bool}>
     * }
     */
    public static function contactPage(): array
    {
        $defaults = [
            'hero_title' => 'تواصل معنا',
            'hero_subtitle' => 'نحن هنا للإجابة على استفساراتك ومساعدتك في رحلتك التعليمية',
            'address' => '',
            'phone' => '01044610507',
            'email' => 'info@mindlytics-academy.com',
            'whatsapp' => '201044610507',
            'hours' => [
                ['label' => 'الأحد - الخميس', 'value' => '9:00 ص - 6:00 م', 'closed' => false],
                ['label' => 'الجمعة', 'value' => 'مغلق', 'closed' => true],
                ['label' => 'السبت', 'value' => '10:00 ص - 2:00 م', 'closed' => false],
            ],
        ];

        $stored = self::all()['contact_page'] ?? [];
        if (! is_array($stored)) {
            return $defaults;
        }

        $merged = array_merge($defaults, $stored);
        if (isset($stored['hours']) && is_array($stored['hours']) && $stored['hours'] !== []) {
            $merged['hours'] = self::normalizeContactHours($stored['hours']);
        }

        return $merged;
    }

    /**
     * @param  array<int, mixed>  $rows
     * @return array<int, array{label: string, value: string, closed: bool}>
     */
    public static function normalizeContactHours(array $rows): array
    {
        $out = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $label = trim((string) ($row['label'] ?? ''));
            if ($label === '') {
                continue;
            }
            $out[] = [
                'label' => $label,
                'value' => trim((string) ($row['value'] ?? '')),
                'closed' => filter_var($row['closed'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ];
        }

        return $out;
    }

    public static function phoneDigits(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?: '';
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
        if (isset($merged['platform_payment_mode'])) {
            $m = (string) $merged['platform_payment_mode'];
            if ($m === 'paymob') {
                $m = 'fawaterak';
                $merged['platform_payment_mode'] = 'fawaterak';
            }
            if (! in_array($m, ['manual', 'kashier', 'fawaterak'], true)) {
                $merged['platform_payment_mode'] = 'kashier';
            }
        }
        $disk->put(self::STORAGE_PATH, json_encode($merged, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
}
