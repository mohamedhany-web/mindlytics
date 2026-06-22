<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class WhatsAppBridgeSettings
{
    private const STORAGE_PATH = 'site/whatsapp_settings.json';

    /**
     * @return array<string, mixed>
     */
    private static function defaults(): array
    {
        return [
            'service_type' => (string) config('services.whatsapp.type', 'wwebjs'),
            'bridge_url' => rtrim((string) config('services.whatsapp.local_api_url', 'https://wa-api.mindlytics-academy.com'), '/'),
            'bridge_token' => (string) config('services.whatsapp.bridge_token', ''),
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

        $merged = array_merge(self::defaults(), is_array($data) ? $data : []);

        // لو الإعدادات المحفوظة ناقصة، استخدم قيم config/.env
        if (($merged['bridge_url'] ?? '') === '') {
            $merged['bridge_url'] = self::defaults()['bridge_url'];
        }
        if (($merged['bridge_token'] ?? '') === '') {
            $merged['bridge_token'] = self::defaults()['bridge_token'];
        }
        if (($merged['service_type'] ?? 'disabled') === 'disabled' && config('services.whatsapp.enabled')) {
            $merged['service_type'] = self::defaults()['service_type'];
        }

        return $merged;
    }

    public static function serviceType(): string
    {
        $type = (string) (self::all()['service_type'] ?? 'disabled');

        return in_array($type, ['disabled', 'wwebjs', 'local', 'official', 'custom'], true) ? $type : 'disabled';
    }

    public static function bridgeUrl(): string
    {
        return rtrim((string) (self::all()['bridge_url'] ?? ''), '/');
    }

    public static function bridgeToken(): string
    {
        return (string) (self::all()['bridge_token'] ?? '');
    }

    public static function isBridgeConfigured(): bool
    {
        return self::bridgeUrl() !== '' && self::bridgeToken() !== '';
    }

    public static function usesBridge(): bool
    {
        return in_array(self::serviceType(), ['wwebjs', 'local'], true) && self::isBridgeConfigured();
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

        if (isset($merged['service_type'])) {
            $type = (string) $merged['service_type'];
            if (! in_array($type, ['disabled', 'wwebjs', 'local', 'official', 'custom'], true)) {
                $merged['service_type'] = 'disabled';
            }
        }

        if (isset($merged['bridge_url'])) {
            $merged['bridge_url'] = rtrim((string) $merged['bridge_url'], '/');
        }

        $disk->put(self::STORAGE_PATH, json_encode($merged, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
}
