<?php

namespace App\Support;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class MetaSocialSettings
{
    private const STORAGE_PATH = 'site/meta_social_settings.json';

    /** @var array<string, mixed>|null */
    private static ?array $cache = null;

    /**
     * @return array<string, mixed>
     */
    private static function defaults(): array
    {
        return [
            'enabled' => false,
            'app_id' => '',
            'app_secret' => '',
            'api_url' => 'https://graph.facebook.com/v21.0',
            'webhook_verify_token' => '',
            'oauth_scopes' => implode(',', self::defaultOAuthScopes()),
        ];
    }

    /**
     * @return list<string>
     */
    public static function defaultOAuthScopes(): array
    {
        return [
            'pages_show_list',
            'pages_read_engagement',
            'pages_manage_metadata',
            'pages_messaging',
            'instagram_basic',
            'instagram_manage_messages',
            'business_management',
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

        if (! empty($merged['app_secret'])) {
            try {
                $merged['app_secret'] = Crypt::decryptString((string) $merged['app_secret']);
            } catch (\Throwable) {
                $merged['app_secret'] = (string) $merged['app_secret'];
            }
        }

        $merged['enabled'] = filter_var($merged['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $merged['api_url'] = rtrim((string) ($merged['api_url'] ?? self::defaults()['api_url']), '/');

        self::$cache = $merged;

        return self::$cache;
    }

    /**
     * @return array<string, mixed>
     */
    public static function formValues(): array
    {
        $all = self::all();

        return [
            'enabled' => (bool) ($all['enabled'] ?? false),
            'app_id' => (string) ($all['app_id'] ?? ''),
            'api_url' => (string) ($all['api_url'] ?? self::defaults()['api_url']),
            'webhook_verify_token' => (string) ($all['webhook_verify_token'] ?? ''),
            'oauth_scopes' => (string) ($all['oauth_scopes'] ?? implode(',', self::defaultOAuthScopes())),
            'has_app_secret' => self::hasAppSecret(),
            'webhook_url' => self::webhookUrl(),
            'oauth_redirect_url' => self::oauthRedirectUrl(),
            'graph_version' => self::graphVersion(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function save(array $data): void
    {
        $current = self::all();
        $merged = array_merge($current, $data);

        if (array_key_exists('app_secret', $data) && trim((string) $data['app_secret']) === '') {
            $merged['app_secret'] = $current['app_secret'];
        }

        foreach (['app_secret', 'app_id', 'webhook_verify_token', 'oauth_scopes'] as $trimKey) {
            if (isset($merged[$trimKey]) && is_string($merged[$trimKey])) {
                $merged[$trimKey] = trim($merged[$trimKey]);
            }
        }

        $merged['enabled'] = filter_var($merged['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $merged['api_url'] = rtrim((string) ($merged['api_url'] ?? self::defaults()['api_url']), '/');

        $toStore = $merged;
        if (! empty($toStore['app_secret'])) {
            $toStore['app_secret'] = Crypt::encryptString((string) $toStore['app_secret']);
        } else {
            $toStore['app_secret'] = '';
        }

        $disk = Storage::disk('local');
        if (! $disk->exists('site')) {
            $disk->makeDirectory('site');
        }

        $disk->put(self::STORAGE_PATH, json_encode($toStore, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        self::$cache = null;
    }

    public static function isEnabled(): bool
    {
        return filter_var(self::all()['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    public static function isAppConfigured(): bool
    {
        return self::appId() !== '' && self::hasAppSecret();
    }

    public static function hasAppSecret(): bool
    {
        return trim((string) (self::all()['app_secret'] ?? '')) !== '';
    }

    public static function appId(): string
    {
        $fromSettings = trim((string) (self::all()['app_id'] ?? ''));
        if ($fromSettings !== '') {
            return $fromSettings;
        }

        return trim((string) config('services.meta_social.app_id', ''));
    }

    public static function appSecret(): string
    {
        $fromSettings = trim((string) (self::all()['app_secret'] ?? ''));
        if ($fromSettings !== '') {
            return $fromSettings;
        }

        return trim((string) config('services.meta_social.app_secret', ''));
    }

    public static function apiUrl(): string
    {
        return rtrim((string) (self::all()['api_url'] ?? self::defaults()['api_url']), '/');
    }

    public static function webhookVerifyToken(): string
    {
        $fromSettings = trim((string) (self::all()['webhook_verify_token'] ?? ''));
        if ($fromSettings !== '') {
            return $fromSettings;
        }

        return trim((string) config('services.meta_social.webhook_verify_token', ''));
    }

    public static function oauthScopes(): string
    {
        $scopes = trim((string) (self::all()['oauth_scopes'] ?? ''));

        return $scopes !== '' ? $scopes : implode(',', self::defaultOAuthScopes());
    }

    public static function publicBaseUrl(): string
    {
        $oauthBase = trim((string) config('services.meta_social.oauth_base_url', ''));
        if ($oauthBase !== '') {
            return rtrim($oauthBase, '/');
        }

        $webhookBase = trim((string) config('services.meta_social.webhook_base_url', ''));
        if ($webhookBase !== '') {
            return rtrim($webhookBase, '/');
        }

        return rtrim((string) config('app.url'), '/');
    }

    public static function webhookUrl(): string
    {
        return self::publicBaseUrl() . '/webhooks/meta-social';
    }

    public static function oauthRedirectUrl(): string
    {
        return self::publicBaseUrl() . '/admin/meta-social/oauth/callback';
    }

    public static function graphVersion(): string
    {
        if (preg_match('#/v(\d+\.\d+)$#', self::apiUrl(), $m)) {
            return 'v' . $m[1];
        }

        return 'v21.0';
    }

    public static function recordWebhookHit(): void
    {
        $disk = Storage::disk('local');
        if (! $disk->exists('site')) {
            $disk->makeDirectory('site');
        }

        $path = 'site/meta_social_webhook_status.json';
        $data = [];
        if ($disk->exists($path)) {
            $decoded = json_decode((string) $disk->get($path), true);
            $data = is_array($decoded) ? $decoded : [];
        }

        $data['last_received_at'] = now()->toIso8601String();
        $disk->put($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    /**
     * @return array{last_received_at: ?string}
     */
    public static function webhookStatus(): array
    {
        $disk = Storage::disk('local');
        $path = 'site/meta_social_webhook_status.json';
        if (! $disk->exists($path)) {
            return ['last_received_at' => null];
        }

        $decoded = json_decode((string) $disk->get($path), true);

        return [
            'last_received_at' => is_array($decoded) ? ($decoded['last_received_at'] ?? null) : null,
        ];
    }
}
