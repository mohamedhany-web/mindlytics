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
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
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
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
     */
    public function start(): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'إعدادات الجسر غير مكتملة.'];
        }

        try {
            $response = $this->client()->timeout(90)->post($this->baseUrl() . '/api/repair');

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return ['success' => false, 'error' => $response->json('error') ?? 'فشل بدء الاتصال.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * إصلاح الجلسة بدون logout — يحافظ على الربط السابق
     *
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
     */
    public function repair(): array
    {
        return $this->start();
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
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
     */
    public function requestPairingCode(string $phone): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'إعدادات الجسر غير مكتملة.'];
        }

        try {
            $response = $this->client()
                ->timeout(90)
                ->post($this->baseUrl() . '/api/pairing-code', ['phone' => $phone]);

            $body = $response->json();

            if ($response->successful()) {
                return ['success' => true, 'data' => is_array($body) ? $body : []];
            }

            return [
                'success' => false,
                'error' => $body['error'] ?? 'فشل طلب رمز الربط.',
                'data' => is_array($body) ? $body : [],
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
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
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'إعدادات الجسر غير مكتملة.'];
        }

        try {
            $response = $this->client()->timeout(60)->post($this->baseUrl() . '/api/restart');

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return ['success' => false, 'error' => $response->json('error') ?? 'فشل إعادة تشغيل الجسر.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
