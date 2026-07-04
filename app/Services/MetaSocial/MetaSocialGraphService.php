<?php

namespace App\Services\MetaSocial;

use App\Models\MetaSocialConnection;
use App\Models\MetaSocialPage;
use App\Support\MetaSocialSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaSocialGraphService
{
    public function graphUrl(): string
    {
        return MetaSocialSettings::apiUrl();
    }

    /**
     * @return array{success: bool, label?: string, can_use?: bool, last_error?: ?string, connection?: ?MetaSocialConnection}
     */
    public function connectionMeta(): array
    {
        if (! MetaSocialSettings::isAppConfigured()) {
            return [
                'success' => false,
                'can_use' => false,
                'label' => 'Meta App غير مكتمل',
                'last_error' => 'أدخل App ID و App Secret في الإعدادات',
            ];
        }

        $connection = MetaSocialConnection::active();
        $pagesCount = MetaSocialPage::query()->where('is_active', true)->count();

        if (! $connection) {
            return [
                'success' => false,
                'can_use' => false,
                'label' => 'غير مربوط — سجّل الدخول عبر Meta',
                'last_error' => null,
                'pages_count' => $pagesCount,
            ];
        }

        $test = $this->debugToken((string) $connection->user_access_token);

        return [
            'success' => (bool) ($test['valid'] ?? false),
            'can_use' => (bool) ($test['valid'] ?? false) && $pagesCount > 0,
            'label' => ($test['valid'] ?? false)
                ? 'متصل — ' . ($connection->meta_user_name ?: 'Meta Business')
                : 'انتهت صلاحية الربط',
            'last_error' => ($test['valid'] ?? false) ? null : ($test['error'] ?? 'Token غير صالح'),
            'connection' => $connection,
            'pages_count' => $pagesCount,
            'meta_user_name' => $connection->meta_user_name,
            'token_expires_at' => $connection->token_expires_at?->toIso8601String(),
        ];
    }

    public function oauthLoginUrl(string $state): string
    {
        $params = http_build_query([
            'client_id' => MetaSocialSettings::appId(),
            'redirect_uri' => MetaSocialSettings::oauthRedirectUrl(),
            'state' => $state,
            'scope' => MetaSocialSettings::oauthScopes(),
            'response_type' => 'code',
        ]);

        return 'https://www.facebook.com/' . MetaSocialSettings::graphVersion() . '/dialog/oauth?' . $params;
    }

    /**
     * @return array{success: bool, access_token?: string, expires_in?: int, error?: string}
     */
    public function exchangeCodeForToken(string $code): array
    {
        try {
            $response = Http::timeout(30)->get("{$this->graphUrl()}/oauth/access_token", [
                'client_id' => MetaSocialSettings::appId(),
                'client_secret' => MetaSocialSettings::appSecret(),
                'redirect_uri' => MetaSocialSettings::oauthRedirectUrl(),
                'code' => $code,
            ]);

            if (! $response->successful()) {
                return ['success' => false, 'error' => $this->graphErrorMessage($response->json() ?? [], 'فشل تبادل الرمز')];
            }

            $body = $response->json();

            return [
                'success' => true,
                'access_token' => (string) ($body['access_token'] ?? ''),
                'expires_in' => (int) ($body['expires_in'] ?? 0),
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array{success: bool, access_token?: string, expires_in?: int, error?: string}
     */
    public function exchangeForLongLivedToken(string $shortLivedToken): array
    {
        try {
            $response = Http::timeout(30)->get("{$this->graphUrl()}/oauth/access_token", [
                'grant_type' => 'fb_exchange_token',
                'client_id' => MetaSocialSettings::appId(),
                'client_secret' => MetaSocialSettings::appSecret(),
                'fb_exchange_token' => $shortLivedToken,
            ]);

            if (! $response->successful()) {
                return ['success' => false, 'error' => $this->graphErrorMessage($response->json() ?? [], 'فشل Long-Lived Token')];
            }

            $body = $response->json();

            return [
                'success' => true,
                'access_token' => (string) ($body['access_token'] ?? ''),
                'expires_in' => (int) ($body['expires_in'] ?? 5184000),
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array{success: bool, id?: string, name?: string, error?: string}
     */
    public function fetchMe(string $accessToken): array
    {
        try {
            $response = Http::timeout(20)->get("{$this->graphUrl()}/me", [
                'fields' => 'id,name',
                'access_token' => $accessToken,
            ]);

            if (! $response->successful()) {
                return ['success' => false, 'error' => $this->graphErrorMessage($response->json() ?? [], 'تعذّر قراءة الحساب')];
            }

            $body = $response->json();

            return [
                'success' => true,
                'id' => (string) ($body['id'] ?? ''),
                'name' => (string) ($body['name'] ?? ''),
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array{success: bool, pages?: list<array<string, mixed>>, error?: string}
     */
    public function fetchManagedPages(string $userAccessToken): array
    {
        try {
            $fields = implode(',', [
                'id',
                'name',
                'username',
                'access_token',
                'category',
                'picture{url}',
                'instagram_business_account{id,username,profile_picture_url}',
            ]);

            $response = Http::timeout(45)->get("{$this->graphUrl()}/me/accounts", [
                'fields' => $fields,
                'limit' => 100,
                'access_token' => $userAccessToken,
            ]);

            if (! $response->successful()) {
                return ['success' => false, 'error' => $this->graphErrorMessage($response->json() ?? [], 'تعذّر جلب الصفحات')];
            }

            $pages = $response->json('data') ?? [];

            return ['success' => true, 'pages' => is_array($pages) ? $pages : []];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array{success: bool, error?: string}
     */
    public function subscribePageWebhook(MetaSocialPage $page): array
    {
        $token = (string) $page->page_access_token;
        if ($token === '') {
            return ['success' => false, 'error' => 'Page Access Token مفقود'];
        }

        $fields = [
            'messages',
            'messaging_postbacks',
            'message_reads',
            'message_deliveries',
            'messaging_optins',
            'messaging_referrals',
        ];

        try {
            $response = Http::timeout(30)->post("{$this->graphUrl()}/{$page->page_id}/subscribed_apps", [
                'subscribed_fields' => implode(',', $fields),
                'access_token' => $token,
            ]);

            if ($response->successful() && ($response->json('success') ?? false)) {
                $page->update(['webhook_subscribed_at' => now()]);

                return ['success' => true];
            }

            return ['success' => false, 'error' => $this->graphErrorMessage($response->json() ?? [], 'فشل اشتراك Webhook')];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array{success: bool, error?: string}
     */
    public function syncAppWebhookSubscription(): array
    {
        $appToken = $this->resolveAppAccessToken();
        $appId = MetaSocialSettings::appId();
        $callbackUrl = MetaSocialSettings::webhookUrl();
        $verifyToken = MetaSocialSettings::webhookVerifyToken();

        if ($appToken === null || $appId === '') {
            return ['success' => false, 'error' => 'App ID و App Secret مطلوبان'];
        }

        if ($verifyToken === '') {
            return ['success' => false, 'error' => 'Webhook Verify Token مطلوب'];
        }

        if (preg_match('#^https?://(localhost|127\\.0\\.0\\.1)#i', $callbackUrl)) {
            return ['success' => false, 'error' => 'Callback URL يجب أن يكون نطاقاً عاماً'];
        }

        $fields = ['messages', 'messaging_postbacks', 'message_reads', 'message_deliveries'];

        try {
            $response = Http::asForm()->timeout(30)->post("{$this->graphUrl()}/{$appId}/subscriptions", [
                'access_token' => $appToken,
                'object' => 'page',
                'callback_url' => $callbackUrl,
                'verify_token' => $verifyToken,
                'fields' => implode(',', $fields),
            ]);

            if ($response->successful()) {
                return ['success' => true];
            }

            return ['success' => false, 'error' => $this->graphErrorMessage($response->json() ?? [], 'فشل مزامنة Webhook')];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array{success: bool, message_id?: string, error?: string}
     */
    public function sendTextMessage(MetaSocialPage $page, string $recipientId, string $text, string $platform = 'messenger'): array
    {
        $token = (string) $page->page_access_token;
        if ($token === '') {
            return ['success' => false, 'error' => 'Page Access Token مفقود'];
        }

        $payload = [
            'messaging_type' => 'RESPONSE',
            'recipient' => ['id' => $recipientId],
            'message' => ['text' => mb_substr($text, 0, 2000)],
        ];

        try {
            $response = Http::timeout(30)->post("{$this->graphUrl()}/{$page->page_id}/messages", array_merge($payload, [
                'access_token' => $token,
            ]));

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message_id' => (string) ($response->json('message_id') ?? ''),
                ];
            }

            return ['success' => false, 'error' => $this->graphErrorMessage($response->json() ?? [], 'فشل الإرسال')];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array{valid: bool, error?: string, expires_at?: ?int}
     */
    public function debugToken(string $accessToken): array
    {
        if ($accessToken === '') {
            return ['valid' => false, 'error' => 'Token فارغ'];
        }

        $appToken = $this->resolveAppAccessToken();
        if ($appToken === null) {
            return ['valid' => true];
        }

        try {
            $response = Http::timeout(20)->get("{$this->graphUrl()}/debug_token", [
                'input_token' => $accessToken,
                'access_token' => $appToken,
            ]);

            if (! $response->successful()) {
                return ['valid' => false, 'error' => $this->graphErrorMessage($response->json() ?? [], 'Token غير صالح')];
            }

            $data = $response->json('data') ?? [];

            return [
                'valid' => (bool) ($data['is_valid'] ?? false),
                'expires_at' => isset($data['expires_at']) ? (int) $data['expires_at'] : null,
                'error' => ($data['is_valid'] ?? false) ? null : ($data['error']['message'] ?? 'Token غير صالح'),
            ];
        } catch (\Throwable $e) {
            return ['valid' => false, 'error' => $e->getMessage()];
        }
    }

    public function resolveAppAccessToken(): ?string
    {
        $appId = MetaSocialSettings::appId();
        $secret = MetaSocialSettings::appSecret();
        if ($appId === '' || $secret === '') {
            return null;
        }

        return $appId . '|' . $secret;
    }

    /**
     * @param  array<string, mixed>  $body
     */
    public function graphErrorMessage(array $body, string $fallback): string
    {
        $error = $body['error'] ?? null;
        if (is_array($error)) {
            return (string) ($error['message'] ?? $error['error_user_msg'] ?? $fallback);
        }

        return $fallback;
    }
}
