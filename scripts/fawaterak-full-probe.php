<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$f = $app->make(\App\Services\FawaterakService::class);
$base = 'https://app.fawaterk.com';
$host = $f->hashDomain('127.0.0.1');

$r = \Illuminate\Support\Facades\Http::timeout(15)
    ->withHeaders([
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer '.$f->checkoutPluginBearerToken(),
        'FAWATERAK-HASH-KEY' => $f->generateHashKey('127.0.0.1'),
        'FAWATERAK-DOMAIN' => $host,
        'DOMAIN-VERSION' => (string) config('fawaterak.version', '0'),
    ])
    ->get($base.'/api/v2/getPaymentmethods');

echo 'live + iframe headers'.PHP_EOL;
echo 'FAWATERAK-DOMAIN: '.$host.PHP_EOL;
echo 'HTTP '.$r->status().PHP_EOL;
echo substr($r->body(), 0, 400).PHP_EOL;
