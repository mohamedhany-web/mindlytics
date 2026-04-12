<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$token = trim((string) config('fawaterak.vendor_key'));

foreach (['https://staging.fawaterk.com', 'https://app.fawaterk.com'] as $base) {
    $r = \Illuminate\Support\Facades\Http::timeout(15)
        ->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])
        ->get($base.'/api/v2/getPaymentmethods');

    echo $base.' Bearer-only: HTTP '.$r->status().' — '.substr(str_replace(["\n", "\r"], ' ', $r->body()), 0, 150).PHP_EOL;
}
