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
                'error' => $body['error'] ?? 'فشل إرسال الرسالة.',
                'data' => is_array($body) ? $body : [],
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
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
