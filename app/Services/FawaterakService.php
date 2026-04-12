<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

class FawaterakService
{
    public function integrationMode(): string
    {
        return strtolower(trim((string) config('fawaterak.integration', 'iframe')));
    }

    public function isIframeMode(): bool
    {
        return $this->integrationMode() === 'iframe';
    }

    public function envType(): string
    {
        $e = strtolower(trim((string) config('fawaterak.env', 'test')));

        return $e === 'live' ? 'live' : 'test';
    }

    public function apiBaseUrl(): string
    {
        return $this->envType() === 'live'
            ? 'https://app.fawaterk.com'
            : 'https://staging.fawaterk.com';
    }

    public function isConfigured(): bool
    {
        if (! $this->isIframeMode()) {
            return false;
        }

        return $this->vendorKey() !== '' && $this->providerKey() !== '';
    }

    public function vendorKey(): string
    {
        return trim((string) config('fawaterak.vendor_key', ''));
    }

    /**
     * سر HMAC لسلسلة Domain+ProviderKey (iframe). إن وُجد FAWATERAK_HMAC_SECRET يُستخدم، وإلا Vendor Key.
     */
    public function iframeHmacSecret(): string
    {
        $s = trim((string) config('fawaterak.hmac_secret', ''));

        return $s !== '' ? $s : $this->vendorKey();
    }

    public function providerKey(): string
    {
        return trim((string) config('fawaterak.provider_key', ''));
    }

    /**
     * رمز Bearer لـ pluginConfig.token (طلبات الإضافة إلى API فواتيرك).
     * FAWATERAK_PLUGIN_BEARER_TOKEN إن وُجد، وإلا FAWATERAK_VENDOR_KEY — منفصل عن الـ HMAC (hashKey).
     */
    public function checkoutPluginBearerToken(): string
    {
        $bearer = trim((string) config('fawaterak.plugin_bearer_token', ''));

        return $bearer !== '' ? $bearer : $this->vendorKey();
    }

    /**
     * hostname من المتصفح بعد التحقق البسيط (لا يُستخدم إلا لمطابقة ترويسة الإضافة).
     */
    public function normalizeCheckoutHostname(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }
        $h = strtolower(trim($raw));
        if ($h === '' || strlen($h) > 253) {
            return null;
        }
        if (! preg_match('/^[a-z0-9.\-:]+$/i', $h)) {
            return null;
        }

        return $h;
    }

    /**
     * @return 'http'|'https'|null
     */
    public function normalizeBrowserScheme(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }
        $s = strtolower(trim($raw));
        if ($s === '' || $s === ':') {
            return null;
        }
        if (str_ends_with($s, ':')) {
            $s = rtrim($s, ':');
        }
        if ($s === 'http') {
            return 'http';
        }
        if ($s === 'https') {
            return 'https';
        }

        return null;
    }

    /**
     * قيمة Domain في سلسلة الـ HMAC (مع ProviderKey).
     *
     * يجب أن تطابق ترويسة FAWATERAK-DOMAIN التي تبنيها إضافة فواتيرك (بروتوكول الصفحة + hostname، بدون منفذ).
     * عند توفر hostname موثوق من المتصفح يُفضَّل ذلك على FAWATERAK_IFRAME_DOMAIN لتفادي تعارض http/https.
     *
     * الوثائق تطلب تسجيل نطاقات iframe بصيغة HTTPS في اللوحة؛ للتطوير على http محلياً قد تحتاج تسجيل
     * نفس الأصل الذي يظهر في المتصفح أو استخدام HTTPS محلي.
     *
     * @param  string|null  $browserHostname  من صفحة الدفع (window.location.hostname)
     * @param  string|null  $browserScheme    من صفحة الدفع (window.location.protocol) أو ناتج getScheme()
     */
    public function hashDomain(?string $browserHostname = null, ?string $browserScheme = null): string
    {
        $root = rtrim(trim((string) config('app.url', '')), '/');
        $appHost = strtolower((string) (parse_url($root, PHP_URL_HOST) ?: ''));
        $browserHost = $this->normalizeCheckoutHostname($browserHostname);
        $sch = $this->normalizeBrowserScheme($browserScheme);

        if ($browserHost !== null) {
            $loopback = ['127.0.0.1', 'localhost', '::1'];
            $bothLoopback = in_array($browserHost, $loopback, true)
                && in_array($appHost, $loopback, true);

            if ($browserHost === $appHost
                || (app()->environment(['local', 'testing']) && $bothLoopback)) {
                $scheme = $sch ?? 'https';

                return $scheme.'://'.$browserHost;
            }
        }

        $override = trim((string) config('fawaterak.iframe_domain', ''));
        if ($override !== '') {
            return rtrim($override, '/');
        }

        $appScheme = strtolower((string) (parse_url($root, PHP_URL_SCHEME) ?: ''));
        if ($appScheme !== 'http' && $appScheme !== 'https') {
            $appScheme = 'https';
        }

        if ($appHost === '') {
            if ($root === '') {
                return '';
            }
            if (! str_starts_with($root, 'http://') && ! str_starts_with($root, 'https://')) {
                return 'https://'.$root;
            }

            return $root;
        }

        return $appScheme.'://'.$appHost;
    }

    /**
     * HMAC-SHA256 حسب وثائق فواتيرك (iframe): hash_hmac('sha256', "Domain=…&ProviderKey=…", secret, false).
     */
    public function generateHashKey(?string $browserHostname = null, ?string $browserScheme = null): string
    {
        $secretKey = $this->iframeHmacSecret();
        $domain = $this->hashDomain($browserHostname, $browserScheme);
        $providerKey = $this->providerKey();
        $queryParam = 'Domain='.$domain.'&ProviderKey='.$providerKey;

        return hash_hmac('sha256', $queryParam, $secretKey, false);
    }

    /**
     * نفس طلب الإضافة تقريباً: التحقق من قبول التوقيع قبل فتح الـ iframe.
     */
    public function preflightGetPaymentMethods(?string $browserHostname, ?string $browserScheme): \Illuminate\Http\Client\Response
    {
        $domain = $this->hashDomain($browserHostname, $browserScheme);

        return Http::timeout(25)->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->checkoutPluginBearerToken(),
            'FAWATERAK-HASH-KEY' => $this->generateHashKey($browserHostname, $browserScheme),
            'FAWATERAK-DOMAIN' => $domain,
            'DOMAIN-VERSION' => (string) config('fawaterak.version', '0'),
        ])->get($this->apiBaseUrl().'/api/v2/getPaymentmethods');
    }

    public function remotePluginUrl(): string
    {
        $env = $this->envType();
        $urls = config('fawaterak.plugin_urls', []);

        return (string) ($urls[$env] ?? $urls['test'] ?? 'https://app.fawaterk.com/fawaterkPlugin/fawaterkPlugin.min.js');
    }

    /**
     * رابط السكربت عبر بروكسي الموقع (CSP / Safari).
     */
    public function proxiedPluginScriptUrl(): string
    {
        return URL::route('public.fawaterk.plugin', [], true);
    }
}
