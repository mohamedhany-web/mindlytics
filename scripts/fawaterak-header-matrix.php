<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$f = $app->make(\App\Services\FawaterakService::class);
$base = 'https://app.fawaterk.com/api/v2/getPaymentmethods';
$token = $f->checkoutPluginBearerToken();
$hash = $f->generateHashKey('127.0.0.1');
$dom = 'https://127.0.0.1';
$ver = (string) config('fawaterak.version', '0');

$matrix = [
    'bearer only' => [
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer '.$token,
    ],
    '+DOMAIN' => [
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer '.$token,
        'FAWATERAK-DOMAIN' => $dom,
    ],
    '+HASH' => [
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer '.$token,
        'FAWATERAK-HASH-KEY' => $hash,
    ],
    '+HASH+DOMAIN' => [
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer '.$token,
        'FAWATERAK-HASH-KEY' => $hash,
        'FAWATERAK-DOMAIN' => $dom,
    ],
    'full' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer '.$token,
        'FAWATERAK-HASH-KEY' => $hash,
        'FAWATERAK-DOMAIN' => $dom,
        'DOMAIN-VERSION' => $ver,
    ],
];

foreach ($matrix as $label => $headers) {
    $r = \Illuminate\Support\Facades\Http::timeout(12)->withHeaders($headers)->get($base);
    $snippet = substr(str_replace(["\n", "\r"], '', $r->body()), 0, 100);
    echo $label.': HTTP '.$r->status().' '.$snippet.PHP_EOL;
}
