<?php

namespace App\Services;

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
     * قيمة Domain في سلسلة الـ HMAC (مع ProviderKey).
     *
     * الوثائق الرسمية: «Each Domain must be in the HTTPS protocol and without "/" at the end»
     * والصيغة: Domain=YOUR_WEBSITE_DOMAIN&ProviderKey=… حيث YOUR_WEBSITE_DOMAIN يُسجَّل في لوحة فواتيرك
     * (Integrations → Fawaterak → IFrame domains) بنفس الشكل: https://hostname بدون منفذ وبدون شرطة أخيرة.
     *
     * @param  string|null  $browserHostname  اختياري: window.location.hostname عند التطابق مع APP_URL أو loopback في local/testing
     */
    public function hashDomain(?string $browserHostname = null): string
    {
        $override = trim((string) config('fawaterak.iframe_domain', ''));
        if ($override !== '') {
            return rtrim($override, '/');
        }

        $root = rtrim(trim((string) config('app.url', '')), '/');
        $appHost = strtolower((string) (parse_url($root, PHP_URL_HOST) ?: ''));
        $browserHost = $this->normalizeCheckoutHostname($browserHostname);

        if ($browserHost !== null) {
            $loopback = ['127.0.0.1', 'localhost', '::1'];
            $bothLoopback = in_array($browserHost, $loopback, true)
                && in_array($appHost, $loopback, true);

            if ($browserHost === $appHost
                || (app()->environment(['local', 'testing']) && $bothLoopback)) {
                return 'https://'.$browserHost;
            }
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

        return 'https://'.$appHost;
    }

    /**
     * HMAC-SHA256 حسب وثائق فواتيرك (iframe): hash_hmac('sha256', "Domain=…&ProviderKey=…", FAWATERAK_VENDOR_KEY, false).
     */
    public function generateHashKey(?string $browserHostname = null): string
    {
        $secretKey = $this->iframeHmacSecret();
        $domain = $this->hashDomain($browserHostname);
        $providerKey = $this->providerKey();
        $queryParam = 'Domain='.$domain.'&ProviderKey='.$providerKey;

        return hash_hmac('sha256', $queryParam, $secretKey, false);
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
