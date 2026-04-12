<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$v = (string) config('fawaterak.vendor_key', '');
$p = (string) config('fawaterak.provider_key', '');
echo 'vendor_key len='.strlen($v).' last_ord='.(strlen($v) ? ord(substr($v, -1)) : 'n/a').PHP_EOL;
echo 'provider_key len='.strlen($p).PHP_EOL;
