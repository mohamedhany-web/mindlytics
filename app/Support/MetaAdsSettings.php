<?php

namespace App\Support;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

/**
 * Meta Marketing API credentials (Ad Account + access token).
 */
class MetaAdsSettings
{
    private const STORAGE_PATH = 'site/meta_ads_settings.json';

    /** @var array<string, mixed>|null */
    private static ?array $cache = null;

    /**
     * @return array<string, mixed>
     */
    private static function defaults(): array
    {
        return [
            'enabled' => false,
            'ad_account_id' => '',
            'access_token' => '',
            'api_url' => (string) config('services.meta_ads.api_url', 'https://graph.facebook.com/v21.0'),
            'default_currency' => 'EGP',
            'default_country' => 'EG',
            'page_id' => '',
            'instagram_actor_id' => '',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $disk = Storage::disk('local');
        if (! $disk->exists(self::STORAGE_PATH)) {
            self::$cache = self::defaults();

            return self::$cache;
        }

        $raw = $disk->get(self::STORAGE_PATH);
        $data = json_decode($raw, true);
        $merged = array_merge(self::defaults(), is_array($data) ? $data : []);

        if (! empty($merged['access_token'])) {
            try {
                $merged['access_token'] = Crypt::decryptString((string) $merged['access_token']);
            } catch (\Throwable) {
                $merged['access_token'] = (string) $merged['access_token'];
            }
        }

        $merged['enabled'] = filter_var($merged['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $merged['api_url'] = rtrim((string) ($merged['api_url'] ?? self::defaults()['api_url']), '/');
        $merged['ad_account_id'] = self::normalizeAdAccountId((string) ($merged['ad_account_id'] ?? ''));

        self::$cache = $merged;

        return self::$cache;
    }

    /**
     * Safe values for Blade forms (never expose full token).
     *
     * @return array<string, mixed>
     */
    public static function formValues(): array
    {
        $all = self::all();

        return [
            'enabled' => (bool) ($all['enabled'] ?? false),
            'ad_account_id' => (string) ($all['ad_account_id'] ?? ''),
            'api_url' => (string) ($all['api_url'] ?? self::defaults()['api_url']),
            'default_currency' => (string) ($all['default_currency'] ?? 'EGP'),
            'default_country' => (string) ($all['default_country'] ?? 'EG'),
            'page_id' => (string) ($all['page_id'] ?? ''),
            'instagram_actor_id' => (string) ($all['instagram_actor_id'] ?? ''),
            'has_access_token' => self::hasAccessToken(),
            'is_ready' => self::isReady(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function save(array $data): void
    {
        $current = self::all();
        $merged = array_merge($current, $data);

        if (array_key_exists('access_token', $data) && trim((string) $data['access_token']) === '') {
            $merged['access_token'] = $current['access_token'];
        }

        foreach (['ad_account_id', 'api_url', 'default_currency', 'default_country', 'page_id', 'instagram_actor_id', 'access_token'] as $key) {
            if (isset($merged[$key]) && is_string($merged[$key])) {
                $merged[$key] = trim($merged[$key]);
            }
        }

        $merged['enabled'] = filter_var($merged['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $merged['api_url'] = rtrim((string) ($merged['api_url'] ?? self::defaults()['api_url']), '/');
        $merged['ad_account_id'] = self::normalizeAdAccountId((string) ($merged['ad_account_id'] ?? ''));
        $merged['default_currency'] = strtoupper((string) ($merged['default_currency'] ?? 'EGP')) ?: 'EGP';
        $merged['default_country'] = strtoupper((string) ($merged['default_country'] ?? 'EG')) ?: 'EG';

        $toStore = $merged;
        if (! empty($toStore['access_token'])) {
            $toStore['access_token'] = Crypt::encryptString((string) $toStore['access_token']);
        } else {
            $toStore['access_token'] = '';
        }

        $disk = Storage::disk('local');
        if (! $disk->exists('site')) {
            $disk->makeDirectory('site');
        }
        $disk->put(self::STORAGE_PATH, json_encode($toStore, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        self::$cache = null;
    }

    public static function normalizeAdAccountId(string $id): string
    {
        $id = trim($id);
        if ($id === '') {
            return '';
        }
        $id = preg_replace('/^act_/i', '', $id) ?? $id;
        $id = preg_replace('/\D+/', '', $id) ?? '';

        return $id !== '' ? 'act_'.$id : '';
    }

    public static function isEnabled(): bool
    {
        return filter_var(self::all()['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    public static function hasAccessToken(): bool
    {
        return trim((string) (self::all()['access_token'] ?? '')) !== '';
    }

    public static function isReady(): bool
    {
        return self::isEnabled()
            && self::adAccountId() !== ''
            && self::hasAccessToken();
    }

    public static function adAccountId(): string
    {
        return (string) (self::all()['ad_account_id'] ?? '');
    }

    public static function accessToken(): string
    {
        return trim((string) (self::all()['access_token'] ?? ''));
    }

    public static function apiUrl(): string
    {
        return rtrim((string) (self::all()['api_url'] ?? self::defaults()['api_url']), '/');
    }

    public static function defaultCurrency(): string
    {
        return strtoupper((string) (self::all()['default_currency'] ?? 'EGP')) ?: 'EGP';
    }

    public static function defaultCountry(): string
    {
        return strtoupper((string) (self::all()['default_country'] ?? 'EG')) ?: 'EG';
    }

    public static function pageId(): string
    {
        return trim((string) (self::all()['page_id'] ?? ''));
    }

    public static function clearCache(): void
    {
        self::$cache = null;
    }
}
