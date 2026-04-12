<?php

/**
 * مصفوفة طلبات ضد live API (لا تطبع الأسرار).
 * تشغيل: php scripts/fawaterak-live-matrix.php
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$f = $app->make(\App\Services\FawaterakService::class);
$base = 'https://app.fawaterk.com';
$bearer = $f->checkoutPluginBearerToken();
$pk = $f->providerKey();
$secret = $f->iframeHmacSecret();
$version = (string) config('fawaterak.version', '0');

$scenarios = [
    ['label' => 'Bearer only', 'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer '.$bearer,
    ]],
];

foreach (['http://127.0.0.1', 'https://127.0.0.1', 'http://localhost', 'https://localhost'] as $dom) {
    $qp = 'Domain='.$dom.'&ProviderKey='.$pk;
    $hash = hash_hmac('sha256', $qp, $secret, false);
    $scenarios[] = [
        'label' => 'HASH+DOMAIN '.$dom,
        'headers' => [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$bearer,
            'FAWATERAK-HASH-KEY' => $hash,
            'FAWATERAK-DOMAIN' => $dom,
            'DOMAIN-VERSION' => $version,
        ],
    ];
}

// تجارب صيغة التوقيع (عند فشل كل الـ Domain+HASH أعلاه)
$dom = 'https://127.0.0.1';
$altStrings = [
    'std' => 'Domain='.$dom.'&ProviderKey='.$pk,
    'rev' => 'ProviderKey='.$pk.'&Domain='.$dom,
    'pk_only' => 'ProviderKey='.$pk,
];
$pkNumeric = preg_match('/^FAWATERAK\.(\d+)$/i', $pk, $m) ? $m[1] : '';
$altSecrets = [
    'vendor' => $f->vendorKey(),
    'iframe_svc' => $secret,
    'provider_as_secret' => $pk,
];
if ($pkNumeric !== '') {
    $altSecrets['provider_numeric'] = $pkNumeric;
}
$foundAlt = false;
foreach ($altStrings as $ak => $qs) {
    foreach ($altSecrets as $sk => $sec) {
        if ($sec === '') {
            continue;
        }
        $hash = hash_hmac('sha256', $qs, $sec, false);
        $resp = \Illuminate\Support\Facades\Http::timeout(25)->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$bearer,
            'FAWATERAK-HASH-KEY' => $hash,
            'FAWATERAK-DOMAIN' => $dom,
            'DOMAIN-VERSION' => $version,
        ])->get($base.'/api/v2/getPaymentmethods');
        if ($resp->successful()) {
            echo 'ALT OK '.$ak.' secret='.$sk.' HTTP '.$resp->status().PHP_EOL;
            $foundAlt = true;
        }
    }
}
if (! $foundAlt) {
    echo 'ALT matrix: no combination returned HTTP 2xx'.PHP_EOL;
}

// صيغ مخرجات HMAC
$qsStd = 'Domain=https://127.0.0.1&ProviderKey='.$pk;
$hBin = hash_hmac('sha256', $qsStd, $f->vendorKey(), true);
$encodings = [
    'hex_lower' => hash_hmac('sha256', $qsStd, $f->vendorKey(), false),
    'hex_upper' => strtoupper(hash_hmac('sha256', $qsStd, $f->vendorKey(), false)),
    'base64' => base64_encode($hBin),
];
foreach ($encodings as $ek => $hv) {
    $resp = \Illuminate\Support\Facades\Http::timeout(25)->withHeaders([
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer '.$bearer,
        'FAWATERAK-HASH-KEY' => $hv,
        'FAWATERAK-DOMAIN' => 'https://127.0.0.1',
        'DOMAIN-VERSION' => $version,
    ])->get($base.'/api/v2/getPaymentmethods');
    echo 'Encoding '.$ek.': HTTP '.$resp->status().PHP_EOL;
}

foreach ($scenarios as $s) {
    $resp = \Illuminate\Support\Facades\Http::timeout(25)->withHeaders($s['headers'])->get($base.'/api/v2/getPaymentmethods');
    $snippet = substr(preg_replace('/\s+/', ' ', $resp->body()), 0, 200);
    echo $s['label'].': HTTP '.$resp->status().' — '.$snippet.PHP_EOL;
}

// مقارنة استضافة API
$staging = 'https://staging.fawaterk.com';
$hashStd = hash_hmac('sha256', 'Domain=https://127.0.0.1&ProviderKey='.$pk, $f->vendorKey(), false);
foreach ([$base => 'live host', $staging => 'staging host'] as $u => $lbl) {
    $resp = \Illuminate\Support\Facades\Http::timeout(25)->withHeaders([
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer '.$bearer,
        'FAWATERAK-HASH-KEY' => $hashStd,
        'FAWATERAK-DOMAIN' => 'https://127.0.0.1',
        'DOMAIN-VERSION' => $version,
    ])->get($u.'/api/v2/getPaymentmethods');
    echo $lbl.' same hash: HTTP '.$resp->status().PHP_EOL;
}
