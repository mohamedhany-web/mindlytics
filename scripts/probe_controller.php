<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "new InvoiceController...\n";
$c = new \App\Http\Controllers\Admin\InvoiceController();
echo "ok\n";

echo "app(InvoiceController)...\n";
$c2 = $app->make(\App\Http\Controllers\Admin\InvoiceController::class);
echo "ok2\n";
