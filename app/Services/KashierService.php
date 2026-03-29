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

        $plainRedirect = $this->merchantRedirectForKashier();
        $orderIdStr = (string) $orderId;

        $preferEncodedFirst = (bool) config('kashier.encode_merchant_redirect', false);
        $variants = $preferEncodedFirst
            ? [rawurlencode($plainRedirect), $plainRedirect]
            : [$plainRedirect, rawurlencode($plainRedirect)];
        $variants = array_values(array_unique($variants));

        $response = null;
        $lastMessage = '';
        $lastBody = null;
        $sentRedirect = '';

        foreach ($variants as $idx => $merchantRedirectValue) {
            $payload = $this->buildPaymentSessionPayload(
                $orderIdStr,
                $amountFormatted,
                $expireAt,
                $merchantRedirectValue,
                $description,
                $customerEmail,
                $customerReference
            );
            $sentRedirect = $merchantRedirectValue;

            $response = Http::acceptJson()
                ->withHeaders([
                    'Authorization' => $this->secret,
                    'api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiBaseUrl . '/v3/payment/sessions', $payload);

            if ($response->successful()) {
                if ($idx > 0) {
                    Log::info('Kashier session created after merchantRedirect variant retry', [
                        'attempt' => $idx + 1,
                        'plain_redirect' => $plainRedirect,
                    ]);
                }
                break;
            }

            $lastBody = $response->json();
            $lastMessage = $lastBody['message'] ?? $lastBody['error'] ?? $response->body();
            if (is_array($lastMessage)) {
                $lastMessage = json_encode($lastMessage, JSON_UNESCAPED_UNICODE);
            }
            $lastMessage = (string) $lastMessage;

            $retryable = str_contains(strtolower($lastMessage), 'merchantredirect')
                && $idx === 0
                && count($variants) > 1;

            if (!$retryable) {
                break;
            }

            Log::warning('Kashier merchantRedirect rejected; retrying with alternate encoding', [
                'first_variant' => 'plain_or_config',
                'plain_redirect' => $plainRedirect,
                'message' => $lastMessage,
            ]);
        }

        if (!$response->successful()) {
            Log::error('Kashier create session failed', [
                'status' => $response->status(),
                'body' => $lastBody ?: $response->body(),
                'order_id' => $orderIdStr,
                'merchant_redirect_last_sent' => $sentRedirect,
                'merchant_redirect_plain' => $plainRedirect,
                'api_base' => $this->apiBaseUrl,
            ]);
            $hint = '';
            if (str_contains(strtolower($lastMessage), 'merchantredirect')) {
                $hint = ' جرّب KASHIER_MERCHANT_REDIRECT_URL=https://نطاقك/checkout/kashier/callback ثم php artisan config:clear. سجّل نفس الرابط في لوحة كاشير إن طُلب. يمكن تعيين KASHIER_ENCODE_MERCHANT_REDIRECT=true لإرسال الرابط مرمّزاً (RFC3986).';
            }
            throw new \RuntimeException('فشل إنشاء جلسة الدفع: ' . $lastMessage . $hint);
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

    private function buildPaymentSessionPayload(
        string $orderIdStr,
        string $amountFormatted,
        string $expireAt,
        string $merchantRedirect,
        ?string $description,
        ?string $customerEmail,
        ?string $customerReference
    ): array {
        $payload = [
            'expireAt' => $expireAt,
            'maxFailureAttempts' => 3,
            'paymentType' => 'credit',
            'amount' => $amountFormatted,
            'currency' => $this->currency,
            'orderId' => $orderIdStr,
            'merchantRedirect' => $merchantRedirect,
            'display' => 'ar',
            'type' => 'one-time',
            'allowedMethods' => $this->allowedMethods,
            'merchantId' => $this->mid,
            'mode' => $this->mode,
            'failureRedirect' => 'FALSE',
            'description' => $this->truncateKashierDescription($description ?? 'Order #' . $orderIdStr),
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

        return $payload;
    }

    /** وصف الطلب في كاشير: أقل من 120 حرفاً */
    private function truncateKashierDescription(string $text): string
    {
        $text = trim($text);
        if (function_exists('mb_strlen') && mb_strlen($text) > 120) {
            return mb_substr($text, 0, 117) . '...';
        }
        if (strlen($text) > 120) {
            return substr($text, 0, 117) . '...';
        }

        return $text;
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
