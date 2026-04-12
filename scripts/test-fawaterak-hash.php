<?php

/**
 * تشغيل: php scripts/test-fawaterak-hash.php
 * يتحقق من اشتقاق نطاق الـ HMAC لمطابقة ترويسة الإضافة (بدون طباعة أسرار).
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$f = $app->make(\App\Services\FawaterakService::class);

echo 'APP_URL: '.config('app.url').PHP_EOL;
echo 'APP_ENV: '.config('app.env').PHP_EOL;
echo 'hashDomain(null): '.$f->hashDomain(null).PHP_EOL;
echo "hashDomain('127.0.0.1'): ".$f->hashDomain('127.0.0.1').PHP_EOL;
echo "hashDomain('localhost'): ".$f->hashDomain('localhost').PHP_EOL;
echo 'hashKey len (null): '.strlen($f->generateHashKey(null)).PHP_EOL;
echo "hashKey len (127): ".strlen($f->generateHashKey('127.0.0.1')).PHP_EOL;

if (getenv('FAWATERAK_PROBE') === '1') {
    $base = $f->envType() === 'live' ? 'https://app.fawaterk.com' : 'https://staging.fawaterk.com';
    $hostForHeader = $f->hashDomain('127.0.0.1');
    $resp = \Illuminate\Support\Facades\Http::timeout(20)
        ->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$f->checkoutPluginBearerToken(),
            'FAWATERAK-HASH-KEY' => $f->generateHashKey('127.0.0.1'),
            'FAWATERAK-DOMAIN' => $hostForHeader,
            'DOMAIN-VERSION' => (string) config('fawaterak.version', '0'),
        ])
        ->get($base.'/api/v2/getPaymentmethods');

    echo PHP_EOL.'--- API probe (FAWATERAK_ENV='.$f->envType().') ---'.PHP_EOL;
    echo 'GET '.$base.'/api/v2/getPaymentmethods'.PHP_EOL;
    echo 'FAWATERAK-DOMAIN: '.$hostForHeader.PHP_EOL;
    echo 'HTTP '.$resp->status().PHP_EOL;
    echo 'Body (first 350 chars): '.substr($resp->body(), 0, 350).PHP_EOL;
}
