<?php

namespace App\Support;

/**
 * Web analytics tags (GTM / GA4 / Clarity / Meta Pixel).
 * Stored in JSON; env values in config/analytics.php are defaults / fallback.
 */
class MarketingWebAnalyticsSettings
{
    private const FILE = 'site/marketing_web_analytics.json';

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'enabled' => (bool) config('analytics.enabled', true),
            'gtm_container_id' => (string) (config('analytics.gtm_container_id') ?? ''),
            'ga4_measurement_id' => (string) (config('analytics.ga4_measurement_id') ?? ''),
            'clarity_project_id' => (string) (config('analytics.clarity_project_id') ?? ''),
            'meta_pixel_id' => (string) (config('analytics.meta_pixel_id') ?? ''),
            'meta_pixel_enabled' => (bool) config('analytics.meta_pixel_enabled', true),
            'currency' => (string) config('analytics.currency', 'EGP'),
            'item_brand' => (string) config('analytics.item_brand', 'Mindlytics'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        $path = storage_path('app/'.self::FILE);
        $defaults = self::defaults();

        if (! is_readable($path)) {
            return $defaults;
        }

        $json = json_decode((string) file_get_contents($path), true);
        if (! is_array($json)) {
            return $defaults;
        }

        return array_merge($defaults, $json);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function save(array $data): void
    {
        $dir = dirname(storage_path('app/'.self::FILE));
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $merged = array_merge(self::all(), $data);
        file_put_contents(
            storage_path('app/'.self::FILE),
            json_encode($merged, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
    }

    public static function enabled(): bool
    {
        return (bool) (self::all()['enabled'] ?? true);
    }

    public static function gtmContainerId(): string
    {
        return trim((string) (self::all()['gtm_container_id'] ?? ''));
    }

    public static function ga4MeasurementId(): string
    {
        return trim((string) (self::all()['ga4_measurement_id'] ?? ''));
    }

    public static function clarityProjectId(): string
    {
        return trim((string) (self::all()['clarity_project_id'] ?? ''));
    }

    public static function metaPixelId(): string
    {
        return trim((string) (self::all()['meta_pixel_id'] ?? ''));
    }

    public static function metaPixelEnabled(): bool
    {
        return (bool) (self::all()['meta_pixel_enabled'] ?? true)
            && self::metaPixelId() !== '';
    }

    public static function currency(): string
    {
        $c = trim((string) (self::all()['currency'] ?? 'EGP'));

        return $c !== '' ? strtoupper($c) : 'EGP';
    }

    public static function itemBrand(): string
    {
        $b = trim((string) (self::all()['item_brand'] ?? 'Mindlytics'));

        return $b !== '' ? $b : 'Mindlytics';
    }

    /**
     * Snapshot for Blade tracking tags.
     *
     * @return array{
     *     enabled: bool,
     *     gtm_container_id: string,
     *     ga4_measurement_id: string,
     *     clarity_project_id: string,
     *     meta_pixel_id: string,
     *     meta_pixel_enabled: bool,
     *     currency: string,
     *     item_brand: string
     * }
     */
    public static function forTracking(): array
    {
        return [
            'enabled' => self::enabled(),
            'gtm_container_id' => self::gtmContainerId(),
            'ga4_measurement_id' => self::ga4MeasurementId(),
            'clarity_project_id' => self::clarityProjectId(),
            'meta_pixel_id' => self::metaPixelId(),
            'meta_pixel_enabled' => self::metaPixelEnabled(),
            'currency' => self::currency(),
            'item_brand' => self::itemBrand(),
        ];
    }
}
