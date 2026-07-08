<?php

namespace App\Support;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class WhatsAppCloudSettings
{
    private const STORAGE_PATH = 'site/whatsapp_cloud_settings.json';

    /** @var array<string, mixed>|null */
    private static ?array $cache = null;

    /**
     * @return array<string, mixed>
     */
    private static function defaults(): array
    {
        return [
            'service_type' => 'official',
            'enabled' => false,
            'app_id' => '',
            'app_secret' => '',
            'api_url' => 'https://graph.facebook.com/v21.0',
            'webhook_verify_token' => '',
            'access_token' => '',
            'phone_number_id' => '',
            'business_account_id' => '',
            'display_phone_number' => '',
            'verified_display_name' => '',
            'template_access_mode' => 'all',
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

        foreach (['app_secret', 'access_token'] as $secretKey) {
            if (! empty($merged[$secretKey])) {
                try {
                    $merged[$secretKey] = Crypt::decryptString((string) $merged[$secretKey]);
                } catch (\Throwable) {
                    $merged[$secretKey] = (string) $merged[$secretKey];
                }
            }
        }

        $merged['enabled'] = filter_var($merged['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $merged['api_url'] = rtrim((string) ($merged['api_url'] ?? self::defaults()['api_url']), '/');

        self::$cache = $merged;

        return self::$cache;
    }

    /**
     * قيم النموذج — بدون إظهار الأسرار كاملة.
     *
     * @return array<string, mixed>
     */
    public static function formValues(): array
    {
        $all = self::all();

        return [
            'service_type' => (string) ($all['service_type'] ?? 'official'),
            'enabled' => (bool) ($all['enabled'] ?? false),
            'app_id' => (string) ($all['app_id'] ?? ''),
            'api_url' => (string) ($all['api_url'] ?? self::defaults()['api_url']),
            'webhook_verify_token' => (string) ($all['webhook_verify_token'] ?? ''),
            'phone_number_id' => (string) ($all['phone_number_id'] ?? ''),
            'business_account_id' => (string) ($all['business_account_id'] ?? ''),
            'display_phone_number' => (string) ($all['display_phone_number'] ?? ''),
            'verified_display_name' => (string) ($all['verified_display_name'] ?? ''),
            'has_app_secret' => self::hasAppSecret(),
            'has_access_token' => self::hasAccessToken(),
            'webhook_url' => self::webhookUrl(),
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

        if (array_key_exists('access_token', $data) && trim((string) $data['access_token']) === '') {
            $merged['access_token'] = $current['access_token'];
        }

        foreach (['app_secret', 'access_token', 'app_id', 'phone_number_id', 'business_account_id', 'webhook_verify_token'] as $trimKey) {
            if (isset($merged[$trimKey]) && is_string($merged[$trimKey])) {
                $merged[$trimKey] = trim($merged[$trimKey]);
            }
        }

        $merged['service_type'] = 'official';
        $merged['enabled'] = filter_var($merged['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $merged['api_url'] = rtrim((string) ($merged['api_url'] ?? self::defaults()['api_url']), '/');
        unset($merged['embedded_signup_config_id']);

        $toStore = $merged;
        foreach (['app_secret', 'access_token'] as $secretKey) {
            if (! empty($toStore[$secretKey])) {
                $toStore[$secretKey] = Crypt::encryptString((string) $toStore[$secretKey]);
            } else {
                $toStore[$secretKey] = '';
            }
        }

        $disk = Storage::disk('local');
        if (! $disk->exists('site')) {
            $disk->makeDirectory('site');
        }

        $disk->put(self::STORAGE_PATH, json_encode($toStore, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        self::$cache = null;
    }

    public static function hasAppSecret(): bool
    {
        return trim((string) (self::all()['app_secret'] ?? '')) !== '';
    }

    public static function hasAccessToken(): bool
    {
        if (trim((string) (self::all()['access_token'] ?? '')) !== '') {
            return true;
        }

        $connection = \App\Models\WhatsAppBusinessConnection::active();

        return (bool) $connection?->access_token;
    }

    public static function isEnabled(): bool
    {
        return filter_var(self::all()['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    public static function serviceType(): string
    {
        return 'official';
    }

    public static function usesOfficial(): bool
    {
        return self::isEnabled();
    }

    public static function isAppConfigured(): bool
    {
        return self::appId() !== '' && self::hasAppSecret();
    }

    public static function isSendConfigured(): bool
    {
        if (\App\Models\WhatsAppBusinessConnection::active()) {
            return true;
        }

        return self::accessToken() !== '' && self::phoneNumberId() !== '';
    }

    public static function appId(): string
    {
        return (string) (self::all()['app_id'] ?? '');
    }

    public static function appSecret(): string
    {
        return (string) (self::all()['app_secret'] ?? '');
    }

    public static function apiUrl(): string
    {
        return rtrim((string) (self::all()['api_url'] ?? self::defaults()['api_url']), '/');
    }

    public static function displayPhoneNumber(): string
    {
        $connection = \App\Models\WhatsAppBusinessConnection::active();
        if ($connection?->display_phone_number) {
            return (string) $connection->display_phone_number;
        }

        return (string) (self::all()['display_phone_number'] ?? '');
    }

    public static function verifiedDisplayName(): string
    {
        $connection = \App\Models\WhatsAppBusinessConnection::active();
        if ($connection?->verified_display_name) {
            return (string) $connection->verified_display_name;
        }

        return (string) (self::all()['verified_display_name'] ?? '');
    }

    public static function accessToken(): string
    {
        $fromSettings = trim((string) (self::all()['access_token'] ?? ''));
        if ($fromSettings !== '') {
            return $fromSettings;
        }

        $connection = \App\Models\WhatsAppBusinessConnection::active();

        return (string) ($connection?->access_token ?? '');
    }

    public static function phoneNumberId(): string
    {
        $fromSettings = trim((string) (self::all()['phone_number_id'] ?? ''));
        if ($fromSettings !== '') {
            return $fromSettings;
        }

        $connection = \App\Models\WhatsAppBusinessConnection::active();

        return (string) ($connection?->phone_number_id ?? '');
    }

    public static function businessAccountId(): string
    {
        $fromSettings = trim((string) (self::all()['business_account_id'] ?? ''));
        if ($fromSettings !== '') {
            return $fromSettings;
        }

        $connection = \App\Models\WhatsAppBusinessConnection::active();

        return (string) ($connection?->waba_id ?? '');
    }

    public static function webhookVerifyToken(): string
    {
        $fromSettings = trim((string) (self::all()['webhook_verify_token'] ?? ''));
        if ($fromSettings !== '') {
            return $fromSettings;
        }

        return trim((string) config('whatsapp.webhook_verify_token', ''));
    }

    public static function webhookUrl(): string
    {
        $base = trim((string) config('whatsapp.webhook_base_url', ''));
        if ($base === '') {
            $base = (string) config('app.url');
        }

        return rtrim($base, '/') . '/webhooks/whatsapp';
    }

    public static function graphVersion(): string
    {
        if (preg_match('#/v(\d+\.\d+)$#', self::apiUrl(), $m)) {
            return 'v' . $m[1];
        }

        return 'v21.0';
    }

    private const WEBHOOK_STATUS_PATH = 'site/whatsapp_webhook_status.json';

    public static function recordWebhookHit(string $type = 'webhook'): void
    {
        $disk = Storage::disk('local');
        if (! $disk->exists('site')) {
            $disk->makeDirectory('site');
        }

        $data = [];
        if ($disk->exists(self::WEBHOOK_STATUS_PATH)) {
            $decoded = json_decode((string) $disk->get(self::WEBHOOK_STATUS_PATH), true);
            $data = is_array($decoded) ? $decoded : [];
        }

        $data[$type === 'inbound' ? 'last_inbound_at' : 'last_received_at'] = now()->toIso8601String();
        $disk->put(self::WEBHOOK_STATUS_PATH, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    /**
     * @return array{last_received_at: ?string, last_inbound_at: ?string}
     */
    public static function webhookStatus(): array
    {
        $disk = Storage::disk('local');
        if (! $disk->exists(self::WEBHOOK_STATUS_PATH)) {
            return ['last_received_at' => null, 'last_inbound_at' => null];
        }

        $decoded = json_decode((string) $disk->get(self::WEBHOOK_STATUS_PATH), true);

        return [
            'last_received_at' => is_array($decoded) ? ($decoded['last_received_at'] ?? null) : null,
            'last_inbound_at' => is_array($decoded) ? ($decoded['last_inbound_at'] ?? null) : null,
        ];
    }

    public static function templateAccessMode(): string
    {
        $mode = (string) (self::all()['template_access_mode'] ?? self::defaults()['template_access_mode']);

        return in_array($mode, ['all', 'restricted'], true) ? $mode : 'all';
    }

    public static function setTemplateAccessMode(string $mode): void
    {
        self::save([
            'template_access_mode' => in_array($mode, ['all', 'restricted'], true) ? $mode : 'all',
        ]);
    }
}
