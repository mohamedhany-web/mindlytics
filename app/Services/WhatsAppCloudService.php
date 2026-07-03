<?php

namespace App\Services;

use App\Models\WhatsAppBusinessConnection;
use App\Models\WhatsAppConversationMessage;
use App\Support\WhatsAppCloudSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

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
            $warnings[] = 'Webhook غير مضبوط — لن تظهر ردود العملاء في المحادثات ولن تُحدَّث حالة التسليم.';
        }

        $webhook = $this->webhookDiagnostics();
        foreach ($webhook['issues'] as $issue) {
            $warnings[] = $issue;
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
            'webhook' => $webhook,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function webhookDiagnostics(): array
    {
        $webhookUrl = WhatsAppCloudSettings::webhookUrl();
        $hasVerifyToken = WhatsAppCloudSettings::webhookVerifyToken() !== '';
        $isLocalUrl = (bool) preg_match('#^https?://(localhost|127\\.0\\.0\\.1)([:/]|$)#i', $webhookUrl);

        $inboundCount = 0;
        if (Schema::hasTable('whatsapp_conversation_messages')) {
            $inboundCount = (int) WhatsAppConversationMessage::query()
                ->where('direction', WhatsAppConversationMessage::DIRECTION_INBOUND)
                ->count();
        }

        $stored = WhatsAppCloudSettings::webhookStatus();
        $lastWebhook = Cache::get('whatsapp:webhook:last_received_at') ?? $stored['last_received_at'];
        $lastInbound = Cache::get('whatsapp:webhook:last_inbound_at') ?? $stored['last_inbound_at'];

        $meta = WhatsAppCloudSettings::isSendConfigured()
            ? $this->fetchMetaWebhookStatus()
            : ['success' => false, 'errors' => ['أكمل Access Token و WABA ID أولاً']];

        $issues = [];
        $tips = [];

        if ($isLocalUrl) {
            $issues[] = 'رابط Webhook في النظام يشير إلى localhost — اضبط APP_URL أو WHATSAPP_WEBHOOK_BASE_URL على https://mindlytics-academy.com';
        }
        if (! $hasVerifyToken) {
            $issues[] = 'Verify Token غير محفوظ في إعدادات الربط — يجب أن يطابق ما في Meta حرفياً.';
        }

        if (($meta['messages_subscribed'] ?? null) === false) {
            $issues[] = 'حقل messages غير مشترك في Meta — من صفحة Webhooks فعّل الاشتراك لحقل messages (ليس فقط حفظ Callback URL).';
        }
        if (($meta['callback_matches'] ?? null) === false && ! empty($meta['callback_url'])) {
            $issues[] = 'Callback URL في Meta (' . $meta['callback_url'] . ') يختلف عن رابط النظام (' . $webhookUrl . ').';
        }
        if (($meta['waba_app_subscribed'] ?? null) === false) {
            $issues[] = 'التطبيق غير مشترك في حساب الواتساب (WABA) — احفظ الإعدادات مرة أخرى أو اضغط «إعادة اشتراك Webhook».';
        }
        if (($meta['active'] ?? null) === false) {
            $issues[] = 'اشتراك Webhook في Meta غير نشط (active=false).';
        }

        foreach ($meta['errors'] ?? [] as $apiError) {
            $tips[] = $apiError;
        }

        $tips[] = 'إذا كان خيار «Attach a client certificate to Webhook requests» مفعّلاً في Meta — عطّله إلا إذا ضبطت mTLS على السيرفر.';

        if ($inboundCount === 0 && ! $lastInbound) {
            if (($meta['messages_subscribed'] ?? null) === true && ($meta['waba_app_subscribed'] ?? null) === true) {
                $issues[] = 'Meta مضبوط والاشتراك سليم، لكن لم تُسجَّل رسائل واردة بعد — اطلب من عميل إرسال رسالة نصية لرقم الواتساب الرسمي ثم راقب السجلات.';
            } elseif (! $lastWebhook) {
                $issues[] = 'لم يصل أي طلب Webhook من Meta للسيرفر بعد — راجع اشتراك messages وتحقق أن الرقم الذي يرد عليه العميل هو نفس رقم الحساب المربوط.';
            } elseif ($lastWebhook) {
                $issues[] = 'وصل Webhook لكن بلا رسائل واردة — غالباً اشتراك message_status فقط بدون messages.';
            }
        }

        $receiving = $inboundCount > 0 || $lastInbound !== null;

        return [
            'webhook_url' => $webhookUrl,
            'is_local_url' => $isLocalUrl,
            'has_verify_token' => $hasVerifyToken,
            'last_webhook_at' => $lastWebhook,
            'last_inbound_at' => $lastInbound,
            'inbound_message_count' => $inboundCount,
            'receiving_replies' => $receiving,
            'meta' => $meta,
            'issues' => array_values(array_unique($issues)),
            'tips' => array_values(array_unique($tips)),
            'ok' => $receiving && $issues === [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function fetchMetaWebhookStatus(): array
    {
        $appId = WhatsAppCloudSettings::appId();
        $wabaId = WhatsAppCloudSettings::businessAccountId();
        $expectedUrl = WhatsAppCloudSettings::webhookUrl();
        $creds = $this->resolveCredentials();

        $result = [
            'success' => false,
            'callback_url' => null,
            'callback_matches' => null,
            'messages_subscribed' => null,
            'message_status_subscribed' => null,
            'active' => null,
            'subscribed_fields' => [],
            'waba_app_subscribed' => null,
            'waba_subscribed_app_ids' => [],
            'errors' => [],
        ];

        if ($creds['access_token'] === '') {
            $result['errors'][] = 'Access Token غير موجود';

            return $result;
        }

        if ($appId !== '') {
            try {
                $response = Http::withToken($creds['access_token'])
                    ->timeout(25)
                    ->get("{$this->graphUrl()}/{$appId}/subscriptions");

                if ($response->successful()) {
                    foreach ($response->json('data') ?? [] as $row) {
                        if (! is_array($row) || ($row['object'] ?? '') !== 'whatsapp_business_account') {
                            continue;
                        }

                        $fields = is_array($row['fields'] ?? null) ? $row['fields'] : [];
                        $result['subscribed_fields'] = $fields;
                        $result['messages_subscribed'] = in_array('messages', $fields, true);
                        $result['message_status_subscribed'] = in_array('message_status', $fields, true);
                        $result['callback_url'] = (string) ($row['callback_url'] ?? '');
                        $result['active'] = (bool) ($row['active'] ?? false);
                        $result['callback_matches'] = rtrim($result['callback_url'], '/') === rtrim($expectedUrl, '/');
                    }
                } else {
                    $body = $response->json() ?? [];
                    $error = is_array($body['error'] ?? null) ? $body['error'] : [];
                    $result['errors'][] = 'تعذّر قراءة اشتراكات التطبيق: ' . $this->humanizeMetaError((string) ($error['message'] ?? 'HTTP ' . $response->status()));
                }
            } catch (\Throwable $e) {
                $result['errors'][] = 'تعذّر الاتصال بـ Meta للتحقق من Webhook: ' . $e->getMessage();
            }
        }

        if ($wabaId !== '') {
            try {
                $response = Http::withToken($creds['access_token'])
                    ->timeout(25)
                    ->get("{$this->graphUrl()}/{$wabaId}/subscribed_apps");

                if ($response->successful()) {
                    $ids = [];
                    foreach ($response->json('data') ?? [] as $row) {
                        $id = (string) ($row['whatsapp_business_api_data']['id'] ?? $row['id'] ?? '');
                        if ($id !== '') {
                            $ids[] = $id;
                        }
                    }
                    $result['waba_subscribed_app_ids'] = $ids;
                    $result['waba_app_subscribed'] = $appId === ''
                        ? $ids !== []
                        : in_array($appId, $ids, true);
                } else {
                    $body = $response->json() ?? [];
                    $error = is_array($body['error'] ?? null) ? $body['error'] : [];
                    $result['errors'][] = 'تعذّر قراءة subscribed_apps للـ WABA: ' . $this->humanizeMetaError((string) ($error['message'] ?? 'HTTP ' . $response->status()));
                }
            } catch (\Throwable $e) {
                $result['errors'][] = 'تعذّر التحقق من اشتراك WABA: ' . $e->getMessage();
            }
        } else {
            $result['errors'][] = 'WABA ID غير مضبوط في الإعدادات';
        }

        $result['success'] = ($result['errors'] === [])
            && ($result['messages_subscribed'] !== false)
            && ($result['waba_app_subscribed'] !== false);

        return $result;
    }

    /**
     * اشتراك تطبيق Meta في أحداث WABA (رسائل واردة + حالة التسليم).
     *
     * @return array{success: bool, error?: string}
     */
    public function ensureWebhookSubscription(): array
    {
        $wabaId = WhatsAppCloudSettings::businessAccountId();
        if ($wabaId === '') {
            return ['success' => false, 'error' => 'WABA ID غير مضبوط.'];
        }

        $creds = $this->resolveCredentials();
        if ($creds['access_token'] === '') {
            return ['success' => false, 'error' => 'Access Token غير موجود.'];
        }

        $payload = [];
        $webhookUrl = WhatsAppCloudSettings::webhookUrl();
        $verifyToken = WhatsAppCloudSettings::webhookVerifyToken();
        if ($verifyToken !== '' && ! preg_match('#^https?://(localhost|127\\.0\\.0\\.1)#i', $webhookUrl)) {
            $payload['override_callback_uri'] = $webhookUrl;
            $payload['verify_token'] = $verifyToken;
        }

        try {
            $response = Http::withToken($creds['access_token'])
                ->timeout(30)
                ->post("{$this->graphUrl()}/{$wabaId}/subscribed_apps", $payload);

            if ($response->successful()) {
                $connection = WhatsAppBusinessConnection::active();
                if ($connection) {
                    $connection->update(['webhook_subscribed_at' => now()]);
                }

                return ['success' => true];
            }

            $body = $response->json() ?? [];
            $error = is_array($body['error'] ?? null) ? $body['error'] : [];

            return [
                'success' => false,
                'error' => $this->humanizeMetaError((string) ($error['message'] ?? 'فشل اشتراك Webhook')),
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $this->humanizeMetaError($e->getMessage())];
        }
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
        $fromDb = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('whatsapp_meta_templates')) {
            $fromDb = \App\Models\WhatsAppMetaTemplate::query()
                ->where('status', \App\Models\WhatsAppMetaTemplate::STATUS_APPROVED)
                ->orderBy('name')
                ->get()
                ->map(fn ($t) => [
                    'name' => $t->name,
                    'language' => $t->language,
                    'category' => $t->category,
                    'label' => $t->displayLabel() . ' (' . $t->categoryLabel() . ')',
                    'source' => 'database',
                ])
                ->all();
        }

        if ($fromDb !== []) {
            return ['success' => true, 'templates' => $fromDb];
        }

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

    /**
     * @return array{success: bool, templates: array<int, array<string, mixed>>, error?: string}
     */
    public function fetchAllMessageTemplates(): array
    {
        $wabaId = WhatsAppCloudSettings::businessAccountId();
        if ($wabaId === '') {
            return [
                'success' => false,
                'templates' => [],
                'error' => 'أدخل WhatsApp Business Account ID (WABA) في إعدادات الربط.',
            ];
        }

        $creds = $this->resolveCredentials();
        if ($creds['access_token'] === '') {
            return ['success' => false, 'templates' => [], 'error' => 'Access Token غير موجود'];
        }

        $all = [];
        $url = "{$this->graphUrl()}/{$wabaId}/message_templates";
        $params = [
            'fields' => 'id,name,language,status,category,components,rejected_reason,quality_score',
            'limit' => 100,
        ];

        try {
            for ($page = 0; $page < 20; $page++) {
                $response = Http::withToken($creds['access_token'])
                    ->timeout(45)
                    ->get($url, $params);

                $body = $response->json();

                if (! $response->successful()) {
                    $error = is_array($body['error'] ?? null) ? $body['error'] : [];

                    return [
                        'success' => false,
                        'templates' => [],
                        'error' => $this->humanizeSendError($error, 'تعذّر جلب القوالب من Meta'),
                    ];
                }

                foreach ($body['data'] ?? [] as $row) {
                    if (is_array($row)) {
                        $all[] = $row;
                    }
                }

                $next = $body['paging']['next'] ?? null;
                if (! is_string($next) || $next === '') {
                    break;
                }

                $url = $next;
                $params = [];
            }

            return ['success' => true, 'templates' => $all];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'templates' => [],
                'error' => $this->humanizeMetaError($e->getMessage()),
            ];
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $components
     * @return array{success: bool, id?: string, error?: string}
     */
    public function createMessageTemplate(
        string $name,
        string $language,
        string $category,
        array $components
    ): array {
        $wabaId = WhatsAppCloudSettings::businessAccountId();
        if ($wabaId === '') {
            return ['success' => false, 'error' => 'WABA ID غير مضبوط في الإعدادات.'];
        }

        $creds = $this->resolveCredentials();
        if ($creds['access_token'] === '') {
            return ['success' => false, 'error' => 'Access Token غير موجود'];
        }

        try {
            $response = Http::withToken($creds['access_token'])
                ->timeout(60)
                ->post("{$this->graphUrl()}/{$wabaId}/message_templates", [
                    'name' => $name,
                    'language' => $language,
                    'category' => strtoupper($category),
                    'components' => $components,
                ]);

            $body = $response->json() ?? [];

            if ($response->successful()) {
                return [
                    'success' => true,
                    'id' => (string) ($body['id'] ?? ''),
                ];
            }

            $error = is_array($body['error'] ?? null) ? $body['error'] : [];

            return [
                'success' => false,
                'error' => $this->humanizeSendError($error, (string) ($error['message'] ?? 'فشل إنشاء القالب في Meta')),
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $this->humanizeMetaError($e->getMessage())];
        }
    }

    /**
     * @return array{success: bool, error?: string}
     */
    public function deleteMessageTemplate(string $name, ?string $language = null): array
    {
        $wabaId = WhatsAppCloudSettings::businessAccountId();
        if ($wabaId === '') {
            return ['success' => false, 'error' => 'WABA ID غير مضبوط.'];
        }

        $creds = $this->resolveCredentials();
        if ($creds['access_token'] === '') {
            return ['success' => false, 'error' => 'Access Token غير موجود'];
        }

        try {
            $query = ['name' => $name];
            if ($language) {
                $query['language'] = $language;
            }

            $response = Http::withToken($creds['access_token'])
                ->timeout(30)
                ->delete("{$this->graphUrl()}/{$wabaId}/message_templates", $query);

            if ($response->successful()) {
                return ['success' => true];
            }

            $body = $response->json() ?? [];
            $error = is_array($body['error'] ?? null) ? $body['error'] : [];

            return [
                'success' => false,
                'error' => $this->humanizeSendError($error, 'فشل حذف القالب من Meta'),
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $this->humanizeMetaError($e->getMessage())];
        }
    }
}
