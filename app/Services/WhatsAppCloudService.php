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
        $token = WhatsAppCloudSettings::accessToken();
        $phoneId = WhatsAppCloudSettings::phoneNumberId();
        $wabaId = WhatsAppCloudSettings::businessAccountId();

        if ($token !== '' && $phoneId !== '') {
            return [
                'access_token' => $token,
                'phone_number_id' => $phoneId,
                'waba_id' => $wabaId,
            ];
        }

        $connection = WhatsAppBusinessConnection::active();
        if ($connection) {
            return [
                'access_token' => (string) $connection->access_token,
                'phone_number_id' => (string) $connection->phone_number_id,
                'waba_id' => (string) ($connection->waba_id ?? ''),
            ];
        }

        return [
            'access_token' => $token,
            'phone_number_id' => $phoneId,
            'waba_id' => $wabaId,
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
                'last_error' => $this->humanizeMetaError($test['error'] ?? 'تعذّر التحقق'),
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

        $phoneData = $test['data'] ?? [];
        $accountMode = strtoupper((string) ($phoneData['account_mode'] ?? ''));
        $warnings = [];

        if ($accountMode === 'SANDBOX') {
            $warnings[] = 'الحساب في وضع الاختبار (Sandbox) — الرسائل تصل فقط للأرقام المضافة كمستلمين تجريبيين في Meta Developer.';
        }

        if (! WhatsAppCloudSettings::webhookVerifyToken()) {
            $warnings[] = 'Webhook غير مضبوط — لن تُحدَّث حالة التسليم (فشل/وصل) في سجل الرسائل.';
        }

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
            'phone_data' => $phoneData,
            'account_mode' => $accountMode,
            'send_warnings' => $warnings,
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
                'error' => $this->humanizeMetaError($body['error']['message'] ?? ('HTTP ' . $response->status())),
                'data' => is_array($body) ? $body : [],
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $this->humanizeMetaError($e->getMessage())];
        }
    }

    public function humanizeMetaError(string $error): string
    {
        $lower = mb_strtolower($error);

        if (str_contains($lower, 'session is invalid')
            || str_contains($lower, 'user logged out')
            || str_contains($lower, 'session has expired')
            || str_contains($lower, 'error validating access token')) {
            return 'انتهت صلاحية Access Token أو أُلغي من Meta. أنشئ System User Token جديداً (دائم) من Business Settings → System Users، ثم الصقه في حقل Access Token هنا — لا تتركه فارغاً عند الحفظ.';
        }

        if (str_contains($lower, 'malformed access token')) {
            return 'Access Token غير صالح أو ناقص — انسخه كاملاً من Meta (يبدأ عادة بـ EAA). تأكد أنك لم تلصق App Secret بالخطأ.';
        }

        if (str_contains($lower, 'oauth') || str_contains($lower, 'permission')) {
            return 'التوكن لا يملك الصلاحيات المطلوبة: whatsapp_business_messaging و whatsapp_business_management.';
        }

        return $error;
    }

    /**
     * @param  array<string, mixed>|null  $errorPayload
     */
    public function humanizeSendError(?array $errorPayload, string $fallback = 'فشل إرسال الرسالة'): string
    {
        if (! is_array($errorPayload)) {
            return $this->humanizeMetaError($fallback);
        }

        $message = (string) ($errorPayload['message'] ?? $fallback);
        $code = (int) ($errorPayload['code'] ?? 0);
        $subcode = (int) ($errorPayload['error_subcode'] ?? 0);

        if ($code === 131030 || $subcode === 131030) {
            return 'رقم المستلم غير مسموح — في وضع الاختبار (Sandbox) أضف الرقم في Meta Developer → WhatsApp → API Setup → أرقام الاختبار، أو أكمل توثيق الأعمال للوضع Live.';
        }

        if ($code === 131047 || $subcode === 131047) {
            return 'لا يمكن إرسال رسالة نصية حرة — مرّ أكثر من 24 ساعة منذ آخر رسالة من العميل. استخدم قالباً معتمداً من Meta (Message Template) أو اطلب من العميل مراسلة رقم الواتساب أولاً.';
        }

        if ($code === 131026 || $subcode === 131026) {
            return 'تعذّر تسليم الرسالة — الرقم قد لا يملك واتساب، أو حظر الرقم، أو الرقم غير صحيح.';
        }

        if ($code === 131051 || $subcode === 131051) {
            return 'نوع الرسالة غير مدعوم — للرسائل التسويقية أو خارج نافذة 24 ساعة استخدم قالب Meta المعتمد.';
        }

        if ($code === 132000 || $subcode === 132000) {
            return 'معلمات قالب Meta غير صحيحة — راجع اسم القالب واللغة والمتغيرات.';
        }

        if ($code === 132001 || $subcode === 132001 || str_contains(mb_strtolower($message), 'does not exist in the translation')) {
            return 'القالب أو اللغة غير موجودين في حسابك — اختر قالباً بحالة Approved من القائمة في صفحة المحادثات، أو أنشئ قالباً في WhatsApp Manager بنفس الاسم واللغة.';
        }

        if ($code === 130472 || str_contains(mb_strtolower($message), 'experiment')) {
            return 'الرقم في تجربة Meta ولا يستقبل رسائل تجارية حالياً — جرّب رقماً آخر أو انتظر.';
        }

        return $this->humanizeMetaError($message);
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

    /**
     * @return array{success: bool, templates: array<int, array<string, string>>, error?: string}
     */
    public function listApprovedTemplates(): array
    {
        $wabaId = WhatsAppCloudSettings::businessAccountId();
        if ($wabaId === '') {
            return [
                'success' => false,
                'templates' => [],
                'error' => 'أدخل WhatsApp Business Account ID (WABA) في إعدادات الربط لجلب القوالب.',
            ];
        }

        $creds = $this->resolveCredentials();
        if ($creds['access_token'] === '') {
            return ['success' => false, 'templates' => [], 'error' => 'Access Token غير موجود'];
        }

        try {
            $response = Http::withToken($creds['access_token'])
                ->timeout(30)
                ->get("{$this->graphUrl()}/{$wabaId}/message_templates", [
                    'fields' => 'name,language,status,category',
                    'limit' => 100,
                ]);

            $body = $response->json();

            if (! $response->successful()) {
                $error = is_array($body['error'] ?? null) ? $body['error'] : [];

                return [
                    'success' => false,
                    'templates' => [],
                    'error' => $this->humanizeSendError($error, 'تعذّر جلب القوالب من Meta'),
                ];
            }

            $templates = [];
            foreach ($body['data'] ?? [] as $row) {
                if (! is_array($row)) {
                    continue;
                }

                $status = strtoupper((string) ($row['status'] ?? ''));
                if ($status !== '' && $status !== 'APPROVED') {
                    continue;
                }

                $name = (string) ($row['name'] ?? '');
                $language = (string) ($row['language'] ?? '');
                if ($name === '' || $language === '') {
                    continue;
                }

                $category = (string) ($row['category'] ?? '');
                $templates[] = [
                    'name' => $name,
                    'language' => $language,
                    'category' => $category,
                    'label' => $name . ' · ' . $language . ($category !== '' ? ' (' . $category . ')' : ''),
                ];
            }

            usort($templates, fn ($a, $b) => strcmp($a['name'] . $a['language'], $b['name'] . $b['language']));

            return ['success' => true, 'templates' => $templates];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'templates' => [],
                'error' => $this->humanizeMetaError($e->getMessage()),
            ];
        }
    }
}
