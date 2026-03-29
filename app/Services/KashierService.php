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
     *
     * @param  string  $merchantRedirect  غير مُستخدم؛ يُحسب الرابط عبر merchantRedirectForKashier() من APP_URL أو KASHIER_MERCHANT_REDIRECT_URL
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

        $redirectUrl = $this->merchantRedirectForKashier();
        if (config('kashier.encode_merchant_redirect', false)) {
            $redirectUrl = rawurlencode($redirectUrl);
        }
        $orderIdStr = (string) $orderId;
        $payload = [
            'expireAt' => $expireAt,
            'maxFailureAttempts' => 3,
            'paymentType' => 'credit',
            'amount' => $amountFormatted,
            'currency' => $this->currency,
            // v3 body table: orderId (required). بعض الأمثلة تستخدم order — نرسل الاثنين لتوافق الوثائق.
            'orderId' => $orderIdStr,
            'order' => $orderIdStr,
            'merchantRedirect' => $redirectUrl,
            'display' => 'ar',
            'type' => 'one-time',
            'allowedMethods' => $this->allowedMethods,
            'merchantId' => $this->mid,
            'mode' => $this->mode,
            'failureRedirect' => false,
            'description' => $description ?? 'Order #' . $orderIdStr,
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

        $response = Http::acceptJson()
            ->withHeaders([
                'Authorization' => $this->secret,
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post($this->apiBaseUrl . '/v3/payment/sessions', $payload);

        if (!$response->successful()) {
            $body = $response->json();
            $message = $body['message'] ?? $body['error'] ?? $response->body();
            if (is_array($message)) {
                $message = json_encode($message, JSON_UNESCAPED_UNICODE);
            }
            Log::error('Kashier create session failed', [
                'status' => $response->status(),
                'body' => $body ?: $response->body(),
                'order_id' => $orderIdStr,
                'merchant_redirect_sent' => $redirectUrl,
                'api_base' => $this->apiBaseUrl,
            ]);
            $hint = '';
            if (is_string($message) && str_contains($message, 'merchantRedirect')) {
                $hint = ' تأكد من APP_URL (يفضّل https://) أو KASHIER_MERCHANT_REDIRECT_URL.';
            }
            throw new \RuntimeException('فشل إنشاء جلسة الدفع: ' . (string) $message . $hint);
        }

        $data = $response->json();
        if (!is_array($data)) {
            Log::error('Kashier session response not JSON', ['raw' => $response->body()]);
            throw new \RuntimeException('استجابة غير متوقعة من كاشير.');
        }

        $sessionUrl = $this->resolveSessionUrl($data);
        $sessionId = $this->resolveSessionId($data);

        if (!$sessionUrl) {
            Log::error('Kashier session response missing sessionUrl', ['response' => $data]);
            throw new \RuntimeException('لم يُرجع كاشير رابط الدفع.');
        }

        return [
            'sessionUrl' => $sessionUrl,
            'sessionId' => $sessionId,
        ];
    }

    /**
     * استخراج رابط واجهة الدفع من أشكال الاستجابة المختلفة، أو بناؤه من معرف الجلسة.
     */
    private function resolveSessionUrl(array $data): ?string
    {
        $candidates = [
            $data['sessionUrl'] ?? null,
            $data['session_url'] ?? null,
            $data['url'] ?? null,
            $data['redirectUrl'] ?? null,
            $data['link'] ?? null,
            data_get($data, 'data.sessionUrl'),
            data_get($data, 'data.session_url'),
            data_get($data, 'data.url'),
        ];
        foreach ($candidates as $url) {
            if (is_string($url) && filter_var(trim($url), FILTER_VALIDATE_URL)) {
                return trim($url);
            }
        }

        $sessionId = $this->resolveSessionId($data);
        if ($sessionId !== null && $sessionId !== '') {
            $base = rtrim((string) config('kashier.payments_session_base', 'https://payments.kashier.io'), '/');
            $built = $base . '/session/' . rawurlencode($sessionId) . '?mode=' . rawurlencode($this->mode);

            return filter_var($built, FILTER_VALIDATE_URL) ? $built : null;
        }

        return null;
    }

    private function resolveSessionId(array $data): ?string
    {
        $nested = isset($data['data']) && is_array($data['data']) ? $data['data'] : [];

        $id = $data['_id'] ?? $data['id'] ?? $data['sessionId'] ?? null;
        if ($id === null || $id === '') {
            $id = $nested['_id'] ?? $nested['id'] ?? $nested['sessionId'] ?? null;
        }

        return $id !== null && $id !== '' ? (string) $id : null;
    }

    /**
     * رابط العودة بعد الدفع لكاشير: KASHIER_MERCHANT_REDIRECT_URL إن وُجد، وإلا APP_URL + مسار الـ callback.
     */
    public function merchantRedirectForKashier(): string
    {
        $configured = trim((string) config('kashier.merchant_redirect_url', ''));
        if ($configured !== '') {
            return $this->canonicalMerchantRedirectUrl($configured);
        }

        $base = rtrim(trim((string) config('app.url', '')), '/');
        if ($base !== '') {
            $path = route('public.checkout.kashier.callback', [], false);

            return $this->canonicalMerchantRedirectUrl($base . $path);
        }

        return $this->canonicalMerchantRedirectUrl((string) url()->route('public.checkout.kashier.callback'));
    }

    /**
     * رابط مطلق https بصيغة يقبلها تحقق كاشير (نطاق ASCII، مسار موحّد، بدون منفذ 443 الصريح).
     */
    private function canonicalMerchantRedirectUrl(string $url): string
    {
        $url = trim($url);
        $url = preg_replace('/^\xEF\xBB\xBF/', '', $url) ?? $url;
        $url = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $url) ?? $url;

        if ($url === '') {
            throw new \InvalidArgumentException('رابط العودة لكاشير فارغ. ضبط APP_URL أو KASHIER_MERCHANT_REDIRECT_URL.');
        }

        if (! preg_match('#^https?://#i', $url)) {
            $base = rtrim(trim((string) config('app.url', '')), '/');
            if ($base === '') {
                throw new \InvalidArgumentException(
                    'رابط العودة نسبي أو بدون مخطط وAPP_URL غير مضبوط. مثال: APP_URL=https://mindlytics-academy.com'
                );
            }
            if (! preg_match('#^https?://#i', $base)) {
                $base = 'https://' . preg_replace('#^http://#i', '', $base);
            }
            $url = rtrim($base, '/') . '/' . ltrim($url, '/');
        }

        $url = $this->ensureHttpsScheme($url);

        $parts = parse_url($url);
        if ($parts === false || empty($parts['host'])) {
            throw new \InvalidArgumentException(
                'رابط العودة لكاشير غير صالح. تحقق من APP_URL وKASHIER_MERCHANT_REDIRECT_URL: ' . substr($url, 0, 120)
            );
        }

        $host = strtolower($parts['host']);
        if (function_exists('idn_to_ascii') && defined('INTL_IDNA_VARIANT_UTS46') && ! str_contains($host, 'xn--')) {
            $ascii = @idn_to_ascii($host, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
            if ($ascii !== false) {
                $host = $ascii;
            }
        }

        $path = $parts['path'] ?? '';
        if ($path === '' || $path === '/') {
            $path = '/checkout/kashier/callback';
        } else {
            if ($path[0] !== '/') {
                $path = '/' . $path;
            }
            $path = preg_replace('#/+#', '/', $path) ?? $path;
        }

        $port = '';
        if (! empty($parts['port'])) {
            $p = (int) $parts['port'];
            if ($p !== 443 && $p !== 80) {
                $port = ':' . $p;
            }
        }

        $query = isset($parts['query']) && $parts['query'] !== '' ? '?' . $parts['query'] : '';

        $canonical = 'https://' . $host . $port . $path . $query;

        if (! filter_var($canonical, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('رابط العودة بعد التطبيع مرفوض: ' . substr($canonical, 0, 150));
        }

        return $canonical;
    }

    private function ensureHttpsScheme(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return $url;
        }
        if (preg_match('#^http://#i', $url)) {
            return 'https://' . substr($url, 7);
        }

        return $url;
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
