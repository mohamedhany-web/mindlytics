<?php

namespace App\Services;

use App\Models\WhatsAppBusinessConnection;
use App\Support\WhatsAppCloudSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppCloudService
{
    public function graphUrl(): string
    {
        return WhatsAppCloudSettings::apiUrl();
    }

    /**
     * @return array{access_token: string, phone_number_id: string, waba_id: string}
     */
    public function resolveCredentials(): array
    {
        $connection = WhatsAppBusinessConnection::active();

        if ($connection) {
            return [
                'access_token' => (string) $connection->access_token,
                'phone_number_id' => (string) $connection->phone_number_id,
                'waba_id' => (string) ($connection->waba_id ?? ''),
            ];
        }

        return [
            'access_token' => WhatsAppCloudSettings::accessToken(),
            'phone_number_id' => WhatsAppCloudSettings::phoneNumberId(),
            'waba_id' => WhatsAppCloudSettings::businessAccountId(),
        ];
    }

    /**
     * @return array{success: bool, label?: string, can_send?: bool, last_error?: ?string, connection?: ?WhatsAppBusinessConnection}
     */
    public function connectionMeta(): array
    {
        if (! WhatsAppCloudSettings::isAppConfigured()) {
            return [
                'success' => false,
                'can_send' => false,
                'label' => 'إعدادات Meta غير مكتملة',
                'last_error' => 'أدخل App ID و App Secret في صفحة الربط',
            ];
        }

        if (! WhatsAppCloudSettings::isSendConfigured()) {
            return [
                'success' => false,
                'can_send' => false,
                'label' => 'غير مربوط',
                'last_error' => 'اضغط «ربط WhatsApp Business» أو أدخل Access Token و Phone Number ID',
            ];
        }

        $test = $this->verifyApiAccess();
        if (! ($test['success'] ?? false)) {
            return [
                'success' => false,
                'can_send' => false,
                'label' => 'فشل التحقق من الربط',
                'last_error' => $test['error'] ?? 'تعذّر التحقق',
                'connection' => WhatsAppBusinessConnection::active(),
            ];
        }

        $connection = WhatsAppBusinessConnection::active();
        $phone = $connection?->display_phone_number ?? ($test['data']['display_phone_number'] ?? null);
        $name = $connection?->verified_display_name ?? ($test['data']['verified_name'] ?? null);

        return [
            'success' => true,
            'can_send' => true,
            'label' => 'متصل — Meta Cloud API',
            'last_error' => null,
            'connection' => $connection,
            'display_phone' => $phone,
            'display_name' => $name,
            'phone_data' => $test['data'] ?? [],
        ];
    }

    /**
     * @return array{success: bool, error?: string}
     */
    public function canSendNow(): array
    {
        $meta = $this->connectionMeta();
        if ($meta['can_send'] ?? false) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'error' => ($meta['label'] ?? 'غير جاهز') . ' — ' . ($meta['last_error'] ?? 'أكمل الربط من قسم الواتساب'),
        ];
    }

    /**
     * @throws \RuntimeException
     */
    public function assertReadyForBulkSend(): void
    {
        if (! WhatsAppCloudSettings::usesOfficial()) {
            throw new \RuntimeException('إرسال الواتساب غير مفعّل — فعّل WHATSAPP_ENABLED في إعدادات الربط.');
        }

        if (! WhatsAppCloudSettings::isAppConfigured()) {
            throw new \RuntimeException('إعدادات Meta غير مكتملة — أدخل App ID و App Secret في صفحة الربط.');
        }

        $check = $this->canSendNow();
        if (! ($check['success'] ?? false)) {
            throw new \RuntimeException($check['error'] ?? 'الواتساب غير جاهز للإرسال.');
        }
    }

    public function isConnectionBlockedError(string $error): bool
    {
        $error = mb_strtolower($error);

        $needles = [
            'oauth',
            'access token',
            'expired',
            'invalid token',
            'permission',
            'not authorized',
            'غير مربوط',
            'غير مكتمل',
            'meta cloud',
            'phone number id',
            'error validating access token',
            'session has expired',
        ];

        foreach ($needles as $needle) {
            if (str_contains($error, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
     */
    public function verifyApiAccess(): array
    {
        $creds = $this->resolveCredentials();

        if ($creds['access_token'] === '' || $creds['phone_number_id'] === '') {
            return ['success' => false, 'error' => 'Access Token أو Phone Number ID غير موجود'];
        }

        try {
            $response = Http::withToken($creds['access_token'])
                ->timeout(30)
                ->get("{$this->graphUrl()}/{$creds['phone_number_id']}", [
                    'fields' => 'display_phone_number,verified_name,quality_rating,account_mode,status',
                ]);

            $body = $response->json();

            if ($response->successful()) {
                return ['success' => true, 'data' => is_array($body) ? $body : []];
            }

            return [
                'success' => false,
                'error' => $body['error']['message'] ?? ('HTTP ' . $response->status()),
                'data' => is_array($body) ? $body : [],
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
     */
    public function testConnection(?string $testPhone = null, ?string $testMessage = null): array
    {
        $verify = $this->verifyApiAccess();
        if (! ($verify['success'] ?? false)) {
            return $verify;
        }

        if ($testPhone === null || trim($testPhone) === '') {
            return [
                'success' => true,
                'message' => 'تم التحقق من الربط بنجاح — الرقم جاهز للإرسال.',
                'data' => $verify['data'] ?? [],
            ];
        }

        $message = $testMessage ?: 'اختبار ربط WhatsApp Business — Mindlytics';
        $result = app(WhatsAppService::class)->sendMessage(
            $testPhone,
            $message,
            'text',
            ['skip_ready_check' => true, 'force_official' => true]
        );

        if ($result['success'] ?? false) {
            return [
                'success' => true,
                'message' => 'تم إرسال رسالة الاختبار بنجاح.',
                'data' => array_merge($verify['data'] ?? [], ['test_send' => true]),
            ];
        }

        return [
            'success' => false,
            'error' => $result['error'] ?? 'فشل إرسال رسالة الاختبار',
            'data' => $verify['data'] ?? [],
        ];
    }

    /**
     * @return array{success: bool, access_token?: string, error?: string, data?: array<string, mixed>}
     */
    public function exchangeCodeForToken(string $code): array
    {
        if (! WhatsAppCloudSettings::isAppConfigured()) {
            return ['success' => false, 'error' => 'App ID أو App Secret غير مضبوط'];
        }

        try {
            $response = Http::timeout(45)->get("{$this->graphUrl()}/oauth/access_token", [
                'client_id' => WhatsAppCloudSettings::appId(),
                'client_secret' => WhatsAppCloudSettings::appSecret(),
                'code' => $code,
            ]);

            $body = $response->json();

            if ($response->successful() && ! empty($body['access_token'])) {
                return [
                    'success' => true,
                    'access_token' => (string) $body['access_token'],
                    'data' => is_array($body) ? $body : [],
                ];
            }

            return [
                'success' => false,
                'error' => $body['error']['message'] ?? 'فشل استبدال رمز OAuth',
                'data' => is_array($body) ? $body : [],
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function saveConnection(array $payload, int $connectedBy): WhatsAppBusinessConnection
    {
        WhatsAppBusinessConnection::query()
            ->where('status', WhatsAppBusinessConnection::STATUS_CONNECTED)
            ->update(['status' => WhatsAppBusinessConnection::STATUS_DISCONNECTED]);

        $connection = WhatsAppBusinessConnection::create([
            'business_portfolio_id' => $payload['business_portfolio_id'] ?? null,
            'waba_id' => $payload['waba_id'] ?? null,
            'phone_number_id' => (string) $payload['phone_number_id'],
            'display_phone_number' => $payload['display_phone_number'] ?? null,
            'verified_display_name' => $payload['verified_display_name'] ?? null,
            'access_token' => (string) $payload['access_token'],
            'status' => WhatsAppBusinessConnection::STATUS_CONNECTED,
            'connected_at' => now(),
            'connected_by' => $connectedBy,
            'meta' => $payload['meta'] ?? [],
        ]);

        $this->subscribeWebhooks($connection);

        return $connection->fresh();
    }

    /**
     * @return array{success: bool, error?: string}
     */
    public function subscribeWebhooks(?WhatsAppBusinessConnection $connection = null): array
    {
        $connection ??= WhatsAppBusinessConnection::active();
        if (! $connection || ! $connection->waba_id) {
            return ['success' => false, 'error' => 'WABA ID غير متوفر'];
        }

        try {
            $response = Http::withToken((string) $connection->access_token)
                ->timeout(30)
                ->post("{$this->graphUrl()}/{$connection->waba_id}/subscribed_apps");

            if ($response->successful()) {
                $connection->update(['webhook_subscribed_at' => now()]);

                return ['success' => true];
            }

            $body = $response->json();

            return [
                'success' => false,
                'error' => $body['error']['message'] ?? ('HTTP ' . $response->status()),
            ];
        } catch (\Throwable $e) {
            Log::warning('WhatsApp webhook subscribe failed', ['error' => $e->getMessage()]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array{success: bool, connection?: WhatsAppBusinessConnection, error?: string}
     */
    public function completeEmbeddedSignup(array $input, int $userId): array
    {
        $phoneNumberId = (string) ($input['phone_number_id'] ?? '');
        $wabaId = (string) ($input['waba_id'] ?? '');

        if ($phoneNumberId === '' || $wabaId === '') {
            return ['success' => false, 'error' => 'phone_number_id و waba_id مطلوبان'];
        }

        $accessToken = (string) ($input['access_token'] ?? '');
        if ($accessToken === '' && ! empty($input['code'])) {
            $exchange = $this->exchangeCodeForToken((string) $input['code']);
            if (! ($exchange['success'] ?? false)) {
                return ['success' => false, 'error' => $exchange['error'] ?? 'فشل OAuth'];
            }
            $accessToken = (string) $exchange['access_token'];
        }

        if ($accessToken === '') {
            $accessToken = WhatsAppCloudSettings::accessToken();
        }

        if ($accessToken === '') {
            return ['success' => false, 'error' => 'لم يتم الحصول على Access Token — أدخله يدوياً في الإعدادات'];
        }

        $phoneMeta = Http::withToken($accessToken)
            ->timeout(30)
            ->get("{$this->graphUrl()}/{$phoneNumberId}", [
                'fields' => 'display_phone_number,verified_name',
            ]);

        $phoneData = $phoneMeta->successful() ? ($phoneMeta->json() ?? []) : [];

        $connection = $this->saveConnection([
            'business_portfolio_id' => $input['business_id'] ?? $input['business_portfolio_id'] ?? null,
            'waba_id' => $wabaId,
            'phone_number_id' => $phoneNumberId,
            'display_phone_number' => $phoneData['display_phone_number'] ?? null,
            'verified_display_name' => $phoneData['verified_name'] ?? null,
            'access_token' => $accessToken,
            'meta' => [
                'embedded_signup' => true,
                'raw' => array_diff_key($input, array_flip(['access_token'])),
            ],
        ], $userId);

        return ['success' => true, 'connection' => $connection];
    }

    public function disconnect(): void
    {
        WhatsAppBusinessConnection::query()
            ->where('status', WhatsAppBusinessConnection::STATUS_CONNECTED)
            ->update(['status' => WhatsAppBusinessConnection::STATUS_DISCONNECTED]);
    }
}
