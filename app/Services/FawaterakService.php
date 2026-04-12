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

    public function providerKey(): string
    {
        return trim((string) config('fawaterak.provider_key', ''));
    }

    /**
     * نطاق الموقع كما في لوحة فواتيرك (HTTPS، بدون / في النهاية).
     */
    public function hashDomain(): string
    {
        $override = trim((string) config('fawaterak.iframe_domain', ''));
        if ($override !== '') {
            return rtrim($override, '/');
        }

        $root = rtrim((string) config('app.url', ''), '/');
        if ($root === '') {
            return '';
        }
        if (! str_starts_with($root, 'http://') && ! str_starts_with($root, 'https://')) {
            $root = 'https://'.$root;
        }

        return $root;
    }

    /**
     * HMAC-SHA256 حسب وثائق فواتيرك (iframe).
     */
    public function generateHashKey(): string
    {
        $secretKey = $this->vendorKey();
        $domain = $this->hashDomain();
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
