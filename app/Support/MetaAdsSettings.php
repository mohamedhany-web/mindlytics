<?php

namespace App\Support;

use App\Models\MetaSocialConnection;
use App\Models\MetaSocialPage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

/**
 * Meta Ads preferences. Auth reuses Meta Social OAuth connection by default.
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
            'enabled' => true,
            'ad_account_id' => '',
            /** Optional override — leave empty to use Meta Social connection token. */
            'access_token' => '',
            'api_url' => '',
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

        $merged['enabled'] = filter_var($merged['enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $merged['ad_account_id'] = self::normalizeAdAccountId((string) ($merged['ad_account_id'] ?? ''));

        self::$cache = $merged;

        return self::$cache;
    }

    /**
     * @return array<string, mixed>
     */
    public static function formValues(): array
    {
        $all = self::all();
        $social = self::metaSocialConnectionSummary();

        return [
            'enabled' => (bool) ($all['enabled'] ?? true),
            'ad_account_id' => (string) ($all['ad_account_id'] ?? ''),
            'default_currency' => (string) ($all['default_currency'] ?? 'EGP'),
            'default_country' => (string) ($all['default_country'] ?? 'EG'),
            'page_id' => self::pageId(),
            'instagram_actor_id' => (string) ($all['instagram_actor_id'] ?? ''),
            'has_override_token' => trim((string) ($all['access_token'] ?? '')) !== '',
            'token_source' => self::tokenSource(),
            'api_url' => self::apiUrl(),
            'is_ready' => self::isReady(),
            'has_access_token' => self::hasAccessToken(),
            'meta_social' => $social,
        ];
    }

    /**
     * @return array{connected: bool, user_name: ?string, label: string}
     */
    public static function metaSocialConnectionSummary(): array
    {
        try {
            $connection = MetaSocialConnection::active();
            if ($connection && trim((string) $connection->user_access_token) !== '') {
                return [
                    'connected' => true,
                    'user_name' => $connection->meta_user_name,
                    'label' => 'متصل عبر السوشيال ميديا — '.($connection->meta_user_name ?: 'Meta'),
                ];
            }
        } catch (\Throwable) {
            //
        }

        return [
            'connected' => false,
            'user_name' => null,
            'label' => 'لا يوجد ربط Meta Social نشط',
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

        $merged['enabled'] = filter_var($merged['enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
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
        return filter_var(self::all()['enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
    }

    public static function hasAccessToken(): bool
    {
        return self::accessToken() !== '';
    }

    /**
     * @return 'meta_social'|'override'|'none'
     */
    public static function tokenSource(): string
    {
        try {
            $connection = MetaSocialConnection::active();
            if ($connection && trim((string) $connection->user_access_token) !== '') {
                return 'meta_social';
            }
        } catch (\Throwable) {
            //
        }

        if (trim((string) (self::all()['access_token'] ?? '')) !== '') {
            return 'override';
        }

        $envToken = trim((string) config('services.meta_ads.access_token', ''));

        return $envToken !== '' ? 'override' : 'none';
    }

    public static function isReady(): bool
    {
        return self::isEnabled()
            && self::adAccountId() !== ''
            && self::hasAccessToken();
    }

    public static function adAccountId(): string
    {
        $fromSettings = (string) (self::all()['ad_account_id'] ?? '');
        if ($fromSettings !== '') {
            return $fromSettings;
        }

        return self::normalizeAdAccountId((string) config('services.meta_ads.ad_account_id', ''));
    }

    /**
     * Prefer Meta Social OAuth user token; optional Meta Ads override / env as fallback.
     */
    public static function accessToken(): string
    {
        try {
            $connection = MetaSocialConnection::active();
            if ($connection) {
                $token = trim((string) $connection->user_access_token);
                if ($token !== '') {
                    return $token;
                }
            }
        } catch (\Throwable) {
            //
        }

        $override = trim((string) (self::all()['access_token'] ?? ''));
        if ($override !== '') {
            return $override;
        }

        return trim((string) config('services.meta_ads.access_token', ''));
    }

    public static function apiUrl(): string
    {
        $custom = trim((string) (self::all()['api_url'] ?? ''));
        if ($custom !== '') {
            return rtrim($custom, '/');
        }

        try {
            $social = MetaSocialSettings::apiUrl();
            if ($social !== '') {
                return rtrim($social, '/');
            }
        } catch (\Throwable) {
            //
        }

        return rtrim((string) config('services.meta_ads.api_url', 'https://graph.facebook.com/v21.0'), '/');
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
        $saved = trim((string) (self::all()['page_id'] ?? ''));
        if ($saved !== '') {
            return $saved;
        }

        try {
            $page = MetaSocialPage::query()->where('is_active', true)->orderByDesc('id')->first();
            if ($page && trim((string) $page->page_id) !== '') {
                return trim((string) $page->page_id);
            }
        } catch (\Throwable) {
            //
        }

        return '';
    }

    public static function clearCache(): void
    {
        self::$cache = null;
    }
}
