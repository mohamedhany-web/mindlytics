<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KashierService
{
    protected string $mid;

    protected string $apiKey;

    protected string $secret;

    protected string $apiBaseUrl;

    protected string $currency;

    protected string $allowedMethods;

    protected string $mode;

    public function __construct()
    {
        $this->mode = config('kashier.mode', 'test');
        $config = config("kashier.{$this->mode}", config('kashier.test'));
        $this->mid = $config['mid'] ?? '';
        $this->apiKey = $config['api_key'] ?? '';
        $this->secret = $config['secret'] ?? '';
        $this->apiBaseUrl = rtrim($config['api_base_url'] ?? 'https://api.kashier.io', '/');
        $this->currency = config('kashier.currency', 'EGP');
        $this->allowedMethods = config('kashier.allowed_methods', 'card,wallet,bank_installments');
    }

    /**
     * إنشاء جلسة دفع عبر Kashier API v3 وإرجاع رابط التوجيه
     */
    public function createPaymentSession(
        string $orderId,
        float $amount,
        string $merchantRedirect,
        ?string $customerEmail = null,
        ?string $customerReference = null,
        ?string $description = null
    ): array {
        $amountFormatted = number_format((float) $amount, 2, '.', '');
        $expireAt = now()->addHours(2)->utc()->format('Y-m-d\TH:i:s.v\Z');

        $redirectUrl = trim($merchantRedirect);
        if (!filter_var($redirectUrl, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('merchantRedirect must be a valid URL. Got: ' . substr($redirectUrl, 0, 80));
        }
        // في بيئة الإنتاج نفرض HTTPS، لكن في البيئة المحلية نسمح بـ http://127.0.0.1 أو localhost للتجربة
        if (!app()->environment('local') && str_starts_with(strtolower($redirectUrl), 'http://')) {
            throw new \RuntimeException(
                'بوابة كاشير تقبل فقط روابط HTTPS. للتجربة المحلية يمكنك استخدام http://127.0.0.1 فقط أو ضبط رابط HTTPS في KASHIER_MERCHANT_REDIRECT_URL.'
            );
        }
        $payload = [
            'expireAt' => $expireAt,
            'maxFailureAttempts' => 3,
            'paymentType' => 'credit',
            'amount' => $amountFormatted,
            'currency' => $this->currency,
            'order' => $orderId,
            'merchantRedirect' => $redirectUrl,
            'display' => 'ar',
            'type' => 'one-time',
            'allowedMethods' => $this->allowedMethods,
            'redirectMethod' => null,
            'merchantId' => $this->mid,
            'mode' => $this->mode,
            'failureRedirect' => false,
            'description' => $description ?? 'Order #' . $orderId,
            'manualCapture' => false,
            'saveCard' => 'optional',
            'retrieveSavedCard' => false,
            'interactionSource' => 'ECOMMERCE',
            'enable3DS' => true,
        ];

        if ($customerEmail || $customerReference) {
            $payload['customer'] = array_filter([
                'email' => $customerEmail,
                'reference' => $customerReference,
            ]);
        }

        $response = Http::withHeaders([
            'Authorization' => $this->secret,
            'api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiBaseUrl . '/v3/payment/sessions', $payload);

        if (!$response->successful()) {
            $body = $response->json();
            $message = $body['message'] ?? $body['error'] ?? $response->body();
            if (is_array($message)) {
                $message = json_encode($message, JSON_UNESCAPED_UNICODE);
            }
            Log::error('Kashier create session failed', [
                'status' => $response->status(),
                'body' => $body,
                'order_id' => $orderId,
                'merchant_redirect_sent' => $redirectUrl,
            ]);
            throw new \RuntimeException('فشل إنشاء جلسة الدفع: ' . (string) $message);
        }

        $data = $response->json();
        $sessionUrl = $data['sessionUrl'] ?? $data['url'] ?? $data['redirectUrl'] ?? $data['link'] ?? null;

        if (!$sessionUrl) {
            Log::error('Kashier session response missing redirect URL', ['response' => $data]);
            throw new \RuntimeException('لم يُرجع كاشير رابط الدفع.');
        }

        return [
            'sessionUrl' => $sessionUrl,
            'sessionId' => $data['id'] ?? $data['sessionId'] ?? null,
        ];
    }

    /**
     * الحصول على رابط صفحة الدفع (يُنشئ جلسة عبر API v3 ثم يُرجع الرابط)
     */
    public function getHppUrl(
        string $orderId,
        float $amount,
        string $callbackUrl,
        ?string $currency = null,
        ?string $customerEmail = null,
        ?string $customerReference = null,
        ?string $description = null
    ): string {
        $result = $this->createPaymentSession(
            $orderId,
            $amount,
            $callbackUrl,
            $customerEmail,
            $customerReference,
            $description
        );
        return $result['sessionUrl'];
    }

    /**
     * التحقق من توقيع الـ callback القادم من كاشير
     */
    public function validateCallback(array $query): bool
    {
        if (empty($query['signature'])) {
            Log::warning('Kashier callback: missing signature');

            return false;
        }

        $queryString = implode('&', [
            'paymentStatus=' . ($query['paymentStatus'] ?? ''),
            'cardDataToken=' . ($query['cardDataToken'] ?? ''),
            'maskedCard=' . ($query['maskedCard'] ?? ''),
            'merchantOrderId=' . ($query['merchantOrderId'] ?? ''),
            'orderId=' . ($query['orderId'] ?? ''),
            'cardBrand=' . ($query['cardBrand'] ?? ''),
            'orderReference=' . ($query['orderReference'] ?? ''),
            'transactionId=' . ($query['transactionId'] ?? ''),
            'amount=' . ($query['amount'] ?? ''),
            'currency=' . ($query['currency'] ?? ''),
        ]);

        $signature = hash_hmac('sha256', $queryString, $this->secret);

        return hash_equals($signature, $query['signature']);
    }

    /**
     * هل الدفع ناجح حسب الاستجابة
     */
    public function isPaymentSuccess(array $query): bool
    {
        return strtoupper((string) ($query['paymentStatus'] ?? '')) === 'SUCCESS';
    }

    public function getMid(): string
    {
        return $this->mid;
    }
}
