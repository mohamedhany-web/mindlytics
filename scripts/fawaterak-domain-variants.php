<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$f = $app->make(\App\Services\FawaterakService::class);
$secret = $f->vendorKey();
$pk = $f->providerKey();
$token = $f->checkoutPluginBearerToken();
$base = 'https://app.fawaterk.com';

$domainStringsForHash = [
    'https://127.0.0.1',
    'http://127.0.0.1',
    '127.0.0.1',
    'https://localhost',
    'localhost',
];

foreach ($domainStringsForHash as $d) {
    $queryParam = 'Domain='.$d.'&ProviderKey='.$pk;
    $hash = hash_hmac('sha256', $queryParam, $secret, false);

    $headerDomain = str_starts_with($d, 'http://') || str_starts_with($d, 'https://')
        ? $d
        : 'https://'.$d;

    $r = \Illuminate\Support\Facades\Http::timeout(12)
        ->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token,
            'FAWATERAK-HASH-KEY' => $hash,
            'FAWATERAK-DOMAIN' => $headerDomain,
            'DOMAIN-VERSION' => (string) config('fawaterak.version', '0'),
        ])
        ->get($base.'/api/v2/getPaymentmethods');

    $ok = $r->successful() ? 'OK' : 'FAIL';
    echo sprintf('hash Domain=%s | header FAWATERAK-DOMAIN=%s => %s %s', $d, $headerDomain, $r->status(), $ok).PHP_EOL;
}
