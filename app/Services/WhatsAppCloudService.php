<?php

namespace App\Services;

use App\Models\WhatsAppBusinessConnection;
use App\Support\WhatsAppCloudSettings;
use Illuminate\Support\Facades\Http;

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
                'phone_number_id' => WhatsAppCloudSettings::phoneNumberId(),
                'business_account_id' => WhatsAppCloudSettings::businessAccountId(),
                'display_phone' => WhatsAppCloudSettings::displayPhoneNumber(),
                'display_name' => WhatsAppCloudSettings::verifiedDisplayName(),
            ];
        }

        if (! WhatsAppCloudSettings::isSendConfigured()) {
            return [
                'success' => false,
                'can_send' => false,
                'label' => 'غير مربوط',
                'last_error' => 'أدخل Access Token و Phone Number ID ثم فعّل الإرسال',
                'phone_number_id' => WhatsAppCloudSettings::phoneNumberId(),
                'business_account_id' => WhatsAppCloudSettings::businessAccountId(),
                'display_phone' => WhatsAppCloudSettings::displayPhoneNumber(),
                'display_name' => WhatsAppCloudSettings::verifiedDisplayName(),
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
        $phone = $connection?->display_phone_number
            ?? ($test['data']['display_phone_number'] ?? null)
            ?? (WhatsAppCloudSettings::displayPhoneNumber() ?: null);
        $name = $connection?->verified_display_name
            ?? ($test['data']['verified_name'] ?? null)
            ?? (WhatsAppCloudSettings::verifiedDisplayName() ?: null);

        return [
            'success' => true,
            'can_send' => true,
            'label' => 'متصل — Meta Cloud API',
            'last_error' => null,
            'connection' => $connection,
            'display_phone' => $phone,
            'display_name' => $name,
            'phone_number_id' => WhatsAppCloudSettings::phoneNumberId(),
            'business_account_id' => WhatsAppCloudSettings::businessAccountId(),
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

    public function disconnect(): void
    {
        WhatsAppBusinessConnection::query()
            ->where('status', WhatsAppBusinessConnection::STATUS_CONNECTED)
            ->update(['status' => WhatsAppBusinessConnection::STATUS_DISCONNECTED]);
    }
}
