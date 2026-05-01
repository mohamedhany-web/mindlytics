<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\FawaterakService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class FawaterkPluginController extends Controller
{
    public function __invoke(FawaterakService $fawaterak): Response
    {
        if (! $fawaterak->isConfigured()) {
            abort(503, 'Fawaterak plugin not configured');
        }

        $url = $fawaterak->remotePluginUrl();
        $bearer = trim((string) config('fawaterak.plugin_bearer_token', ''));
        $token = $bearer !== '' ? $bearer : $fawaterak->vendorKey();

        $cacheKey = 'fawaterk_plugin_js_'.$fawaterak->envType().'_'.md5($url);

        try {
            $body = Cache::remember($cacheKey, 6 * 3600, function () use ($url, $token) {
                try {
                    $client = Http::timeout(45);
                    $response = $client->withHeaders(['Authorization' => 'Bearer '.$token])->get($url);
                    if (! $response->successful()) {
                        // بعض إعدادات فواتيرك قد لا تتطلب Authorization لتحميل السكربت
                        $response = $client->get($url);
                    }
                } catch (\Throwable $e) {
                    Log::warning('Fawaterk plugin fetch failed', ['message' => $e->getMessage(), 'url' => $url]);

                    throw $e;
                }

                if (! $response->successful()) {
                    Log::warning('Fawaterk plugin HTTP error', ['status' => $response->status(), 'url' => $url]);

                    throw new \RuntimeException('Fawaterk plugin HTTP '.$response->status());
                }

                $content = $response->body();
                if ($content === '') {
                    throw new \RuntimeException('Fawaterk plugin empty body');
                }
                // sanity check: يجب أن يحتوي السكربت على الدالة الأساسية
                if (! str_contains($content, 'fawaterkCheckout')) {
                    Log::warning('Fawaterk plugin unexpected body', [
                        'url' => $url,
                        'sample' => substr(preg_replace('/\s+/', ' ', $content), 0, 180),
                    ]);
                    throw new \RuntimeException('Fawaterk plugin invalid javascript body');
                }

                return $content;
            });
        } catch (\Throwable $e) {
            abort(502, 'Could not load Fawaterak checkout script');
        }

        return response($body, 200, [
            'Content-Type' => 'application/javascript; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
