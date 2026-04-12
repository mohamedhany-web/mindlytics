<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$f = $app->make(\App\Services\FawaterakService::class);
$base = 'https://app.fawaterk.com';
$b = $f->checkoutPluginBearerToken();
$pk = $f->providerKey();
$vk = $f->vendorKey();
$h = hash_hmac('sha256', 'Domain=https://127.0.0.1&ProviderKey='.$pk, $vk, false);

$sets = [
    'hash+domain+no_domain_version' => [
        'Authorization' => 'Bearer '.$b,
        'FAWATERAK-HASH-KEY' => $h,
        'FAWATERAK-DOMAIN' => 'https://127.0.0.1',
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ],
    'hash+domain+version_header' => [
        'Authorization' => 'Bearer '.$b,
        'FAWATERAK-HASH-KEY' => $h,
        'FAWATERAK-DOMAIN' => 'https://127.0.0.1',
        'FAWATERAK-VERSION' => '0',
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ],
];
foreach ($sets as $label => $hdr) {
    $r = \Illuminate\Support\Facades\Http::timeout(25)->withHeaders($hdr)->get($base.'/api/v2/getPaymentmethods');
    echo $label.': '.$r->status().PHP_EOL;
}
