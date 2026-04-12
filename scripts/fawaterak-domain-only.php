<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$f = $app->make(\App\Services\FawaterakService::class);
$base = 'https://app.fawaterk.com';
$b = $f->checkoutPluginBearerToken();
$dom = 'https://127.0.0.1';

$r = \Illuminate\Support\Facades\Http::timeout(25)->withHeaders([
    'Accept' => 'application/json',
    'Content-Type' => 'application/json',
    'Authorization' => 'Bearer '.$b,
    'FAWATERAK-DOMAIN' => $dom,
    'DOMAIN-VERSION' => (string) config('fawaterak.version', '0'),
])->get($base.'/api/v2/getPaymentmethods');

echo 'Bearer + DOMAIN (no HASH): HTTP '.$r->status().PHP_EOL;
echo substr($r->body(), 0, 300).PHP_EOL;
