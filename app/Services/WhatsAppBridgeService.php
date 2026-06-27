<?php

namespace App\Services;

use App\Support\WhatsAppBridgeSettings;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppBridgeService
{
    public function isConfigured(): bool
    {
        return WhatsAppBridgeSettings::isBridgeConfigured();
    }

    public function baseUrl(): string
    {
        return WhatsAppBridgeSettings::bridgeUrl();
    }

    public function token(): string
    {
        return WhatsAppBridgeSettings::bridgeToken();
    }

    protected function client(): PendingRequest
    {
        return Http::withToken($this->token())
            ->acceptJson()
            ->timeout(30);
    }

    /**
     * @return array{success: bool, data?: array<string, mixed>, error?: string, restarting?: bool}
     */
    public function getStatus(): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'إعدادات الجسر غير مكتملة (الرابط أو التوكن).'];
        }

        try {
            $response = $this->client()->get($this->baseUrl() . '/api/status');

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return [
                'success' => false,
                'error' => $response->json('error') ?? 'فشل الاتصال بالجسر: HTTP ' . $response->status(),
            ];
        } catch (\Throwable $e) {
            Log::warning('WhatsApp bridge status error', ['error' => $e->getMessage()]);

            return ['success' => false, 'error' => 'تعذّر الاتصال بسيرفر الواتساب: ' . $e->getMessage()];
        }
    }

    /**
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
     */
    public function getQr(): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'إعدادات الجسر غير مكتملة.'];
        }

        try {
            $response = $this->client()->get($this->baseUrl() . '/api/qr');

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return [
                'success' => false,
                'error' => $response->json('error') ?? 'QR غير متاح حالياً.',
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array{success: bool, data?: array<string, mixed>, error?: string, restarting?: bool}
     */
    public function start(): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'إعدادات الجسر غير مكتملة.'];
        }

        $status = $this->getStatus();
        $lastError = (string) ($status['data']['last_error'] ?? '');

        if ($this->isBrowserLockError($lastError)) {
            return $this->forceRepair();
        }

        try {
            $response = $this->client()->timeout(120)->post($this->baseUrl() . '/api/repair');

            if ($response->successful()) {
                $data = $response->json();
                if (! empty($data['restarting'])) {
                    return ['success' => true, 'data' => $data, 'restarting' => true];
                }

                return ['success' => true, 'data' => $data];
            }

            return ['success' => false, 'error' => $response->json('error') ?? 'فشل بدء الاتصال.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array{success: bool, data?: array<string, mixed>, error?: string, restarting?: bool}
     */
    public function repair(): array
    {
        return $this->start();
    }

    /**
     * @return array{success: bool, data?: array<string, mixed>, error?: string, restarting?: bool}
     */
    public function forceRepair(): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'إعدادات الجسر غير مكتملة.'];
        }

        try {
            $response = $this->client()->timeout(45)->post($this->baseUrl() . '/api/force-repair');

            if ($response->status() === 404) {
                return $this->legacyRepair();
            }

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json(), 'restarting' => true];
            }

            return ['success' => false, 'error' => $response->json('error') ?? 'فشل الإصلاح القوي.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array{success: bool, data?: array<string, mixed>, error?: string, restarting?: bool}
     */
    private function legacyRepair(): array
    {
        try {
            $response = $this->client()->timeout(120)->post($this->baseUrl() . '/api/repair');

            if ($response->successful()) {
                $data = $response->json();

                return ['success' => true, 'data' => $data, 'restarting' => ! empty($data['restarting'])];
            }

            return ['success' => false, 'error' => $response->json('error') ?? 'فشل الإصلاح.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{can_send: bool, status: string, label: string, badge_class: string, last_error: ?string}
     */
    public function connectionMeta(array $data, bool $reachable = true): array
    {
        if (! $reachable) {
            return [
                'can_send' => false,
                'status' => 'unreachable',
                'label' => 'الجسر غير متاح',
                'badge_class' => 'bg-rose-100 text-rose-800 border-rose-200',
                'last_error' => null,
            ];
        }

        $status = (string) ($data['status'] ?? 'unknown');
        $legacySnapshot = ! array_key_exists('connected', $data);

        if ($legacySnapshot) {
            $connected = in_array($status, ['ready', 'degraded'], true)
                && (bool) ($data['phone'] ?? null);
            $sendReady = $connected;
        } else {
            $connected = (bool) ($data['connected'] ?? false);
            $sendReady = $data['send_ready'] ?? null;
            if ($sendReady === null) {
                $sendReady = in_array($status, ['ready', 'degraded'], true) && $connected;
            } else {
                $sendReady = (bool) $sendReady;
            }
        }

        $lastError = $data['last_error'] ?? null;
        $sessionPresent = $connected && in_array($status, ['ready', 'degraded'], true);

        if ($sendReady || $sessionPresent) {
            return [
                'can_send' => true,
                'status' => 'ready',
                'label' => $sendReady ? 'متصل وجاهز للإرسال' : 'متصل',
                'badge_class' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                'last_error' => null,
            ];
        }

        $labels = [
            'ready' => 'الجلسة متصلة لكن غير جاهزة للإرسال',
            'degraded' => 'جلسة غير مستقرة',
            'qr' => 'بانتظار QR',
            'pairing' => 'بانتظار رمز الربط',
            'authenticated' => 'تمت المصادقة',
            'disconnected' => 'غير متصل',
            'error' => 'خطأ في الجسر',
            'auth_failure' => 'فشل المصادقة',
        ];

        return [
            'can_send' => false,
            'status' => $status,
            'label' => $labels[$status] ?? $status,
            'badge_class' => 'bg-rose-100 text-rose-800 border-rose-200',
            'last_error' => is_string($lastError) ? $lastError : null,
        ];
    }

    public function translateError(string $error): string
    {
        $error = trim($error);
        if ($error === '') {
            return 'فشل إرسال الرسالة عبر جسر الواتساب.';
        }

        if (preg_match('/WhatsApp is not connected/i', $error)) {
            if (preg_match('/Status:\s*(\w+)/i', $error, $m)) {
                $status = $m[1];
                $map = [
                    'disconnected' => 'الواتساب غير متصل — افتح لوحة الاتصال وأعد الربط.',
                    'qr' => 'الواتساب بانتظار مسح QR — أكمل الربط أولاً.',
                    'pairing' => 'الواتساب بانتظار رمز الربط — أكمل الربط أولاً.',
                    'error' => 'خطأ في جلسة الواتساب — اضغط «إصلاح الاتصال» في لوحة الواتساب.',
                    'degraded' => 'جلسة الواتساب غير مستقرة — انتظر 30 ثانية أو أعد إصلاح الاتصال.',
                    'authenticated' => 'تمت المصادقة لكن الجلسة لم تكتمل — انتظر قليلاً أو أعد الربط.',
                ];

                return $map[$status] ?? 'الواتساب غير جاهز للإرسال (الحالة: ' . $status . ').';
            }

            return 'الواتساب غير متصل — افتح لوحة الاتصال وأعد الربط قبل الإرسال.';
        }

        if (str_contains(strtolower($error), 'browser is already running')) {
            return 'Chrome عالق على VPS — اضغط «إصلاح قوي» في لوحة الواتساب أو أعد تشغيل Bridge.';
        }

        return $error;
    }

    /**
     * فحص سريع: هل الجسر جاهز للإرسال؟ (نفس منطق لوحة الواتساب — بدون إصلاح تلقائي)
     *
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
     */
    public function canSendNow(): array
    {
        $status = $this->getStatus();
        if (! ($status['success'] ?? false)) {
            return $status;
        }

        $data = $status['data'] ?? [];
        $meta = $this->connectionMeta($data, true);

        if ($meta['can_send']) {
            return ['success' => true, 'data' => $data];
        }

        return [
            'success' => false,
            'error' => $this->formatSendBlockedError($meta),
            'data' => $data,
        ];
    }

    /**
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
     */
    public function ensureReadyForSend(): array
    {
        $check = $this->canSendNow();
        if ($check['success'] ?? false) {
            return $check;
        }

        $data = $check['data'] ?? [];
        $bridgeStatus = (string) ($data['status'] ?? '');
        $meta = $this->connectionMeta($data, true);
        $hasConnectedField = array_key_exists('connected', $data);
        $sendReadyField = array_key_exists('send_ready', $data) ? (bool) $data['send_ready'] : null;

        $shouldRepair = (! $hasConnectedField && in_array($bridgeStatus, ['ready', 'degraded'], true) && ! ($data['phone'] ?? null))
            || ($hasConnectedField && ! ($data['connected'] ?? false)
                && in_array($bridgeStatus, ['disconnected', 'error', 'auth_failure', 'unknown'], true))
            || ($sendReadyField === false && in_array($bridgeStatus, ['ready', 'degraded'], true));

        if ($shouldRepair) {
            $repair = $this->start();
            if ($repair['success'] ?? false) {
                sleep(! empty($repair['restarting']) ? 12 : 4);
                $status = $this->getStatus();
                $data = $status['data'] ?? [];
                $meta = $this->connectionMeta($data, true);
                if ($meta['can_send']) {
                    return ['success' => true, 'data' => $data];
                }
            }
        }

        return [
            'success' => false,
            'error' => $this->formatSendBlockedError($meta),
            'data' => $data,
        ];
    }

    /**
     * @param  array{label?: string, last_error?: ?string}  $meta
     */
    private function formatSendBlockedError(array $meta): string
    {
        $label = (string) ($meta['label'] ?? 'الواتساب غير جاهز للإرسال');
        if (in_array($label, ['ready', 'degraded', 'unknown'], true)) {
            $label = 'الواتساب غير جاهز للإرسال';
        }
        $detail = ! empty($meta['last_error']) ? ' (' . $meta['last_error'] . ')' : '';

        return $label . ' — لا يمكن الإرسال الآن.' . $detail;
    }

    /**
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
     */
    public function sendMessage(string $phone, string $message, bool $simulateTyping = true): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'إعدادات الجسر غير مكتملة.'];
        }

        try {
            $timeout = (int) config('whatsapp.bridge_timeout', 180);
            $response = $this->client()
                ->timeout($timeout)
                ->post($this->baseUrl() . '/api/send', [
                    'phone' => $phone,
                    'message' => $message,
                    'simulate_typing' => $simulateTyping,
                ]);

            $body = $response->json();

            if ($response->successful() && ($body['success'] ?? false)) {
                return ['success' => true, 'data' => $body];
            }

            return [
                'success' => false,
                'error' => $this->translateError($body['error'] ?? 'فشل إرسال الرسالة.'),
                'data' => is_array($body) ? $body : [],
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $this->translateError($e->getMessage())];
        }
    }

    /**
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
     */
    public function getPairingCode(): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'إعدادات الجسر غير مكتملة.'];
        }

        try {
            $response = $this->client()->get($this->baseUrl() . '/api/pairing-code');

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return [
                'success' => false,
                'error' => $response->json('error') ?? 'رمز الربط غير متاح حالياً.',
                'data' => is_array($response->json()) ? $response->json() : [],
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array{success: bool, data?: array<string, mixed>, error?: string, restarting?: bool}
     */
    public function requestPairingCode(string $phone): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'إعدادات الجسر غير مكتملة.'];
        }

        $status = $this->getStatus();
        $lastError = (string) ($status['data']['last_error'] ?? '');

        if ($this->isBrowserLockError($lastError)) {
            $force = $this->forceRepair();
            if ($force['success'] ?? false) {
                return [
                    'success' => false,
                    'restarting' => true,
                    'error' => 'تم إعادة تشغيل Bridge — انتظر 15 ثانية ثم أعد طلب رمز الربط.',
                    'data' => $force['data'] ?? [],
                ];
            }
        }

        return $this->postPairingCode($phone);
    }

    /**
     * @return array{success: bool, data?: array<string, mixed>, error?: string, restarting?: bool}
     */
    private function postPairingCode(string $phone, bool $isRetry = false): array
    {
        try {
            $response = $this->client()
                ->timeout(120)
                ->post($this->baseUrl() . '/api/pairing-code', ['phone' => $phone]);

            $body = $response->json() ?? [];
            $lastError = (string) ($body['last_error'] ?? $body['error'] ?? '');

            if ($response->successful() && ($body['success'] ?? false) && $lastError === '') {
                return ['success' => true, 'data' => is_array($body) ? $body : []];
            }

            if (! empty($body['restarting'])) {
                return [
                    'success' => false,
                    'restarting' => true,
                    'error' => 'Bridge يُعاد تشغيله — انتظر 15 ثانية ثم أعد المحاولة.',
                    'data' => is_array($body) ? $body : [],
                ];
            }

            if (! $isRetry && $this->isBrowserLockError($lastError)) {
                Log::info('WhatsApp pairing browser lock — force repair', ['error' => $lastError]);
                $force = $this->forceRepair();
                if ($force['success'] ?? false) {
                    return [
                        'success' => false,
                        'restarting' => true,
                        'error' => 'تم إعادة تشغيل Bridge — انتظر 15 ثانية ثم أعد طلب رمز الربط.',
                        'data' => $force['data'] ?? [],
                    ];
                }
            }

            return [
                'success' => false,
                'error' => $lastError !== '' ? $lastError : 'فشل طلب رمز الربط.',
                'data' => is_array($body) ? $body : [],
            ];
        } catch (\Throwable $e) {
            if (! $isRetry && $this->isBrowserLockError($e->getMessage())) {
                $force = $this->forceRepair();
                if ($force['success'] ?? false) {
                    return [
                        'success' => false,
                        'restarting' => true,
                        'error' => 'تم إعادة تشغيل Bridge — انتظر 15 ثانية ثم أعد طلب رمز الربط.',
                    ];
                }
            }

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function isBrowserLockError(string $message): bool
    {
        $message = strtolower($message);

        return str_contains($message, 'browser is already running')
            || str_contains($message, 'userdataDir')
            || str_contains($message, 'userdata dir');
    }

    /**
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
     */
    public function switchToQrMode(): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'إعدادات الجسر غير مكتملة.'];
        }

        try {
            $response = $this->client()->timeout(60)->post($this->baseUrl() . '/api/qr-mode');

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return ['success' => false, 'error' => $response->json('error') ?? 'فشل التحويل لوضع QR.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
     */
    public function logout(): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'إعدادات الجسر غير مكتملة.'];
        }

        try {
            $response = $this->client()->post($this->baseUrl() . '/api/logout');

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return ['success' => false, 'error' => $response->json('error') ?? 'فشل تسجيل الخروج.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
     */
    public function restart(): array
    {
        return $this->forceRepair();
    }
}
