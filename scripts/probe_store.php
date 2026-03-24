<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

$admin = User::query()->where('role', 'super_admin')->first();
$student = User::query()->where('role', 'student')->where('is_active', 1)->first();
Auth::login($admin);
RateLimiter::clear('create-invoice:' . $admin->id);

$request = Request::create('/admin/invoices', 'POST', [
    'user_id' => (string) $student->id,
    'type' => 'course',
    'description' => '',
    'subtotal' => '100',
    'tax_amount' => '0',
    'discount_amount' => '0',
    'due_date' => now()->addDays(7)->format('Y-m-d'),
]);

$before = \App\Models\Invoice::query()->count();
echo "invoices before={$before}\n";
echo "calling store...\n";
$c = new \App\Http\Controllers\Admin\InvoiceController();
$response = $c->store($request);
$after = \App\Models\Invoice::query()->count();
echo "done status " . $response->getStatusCode() . "\n";
echo "url " . $response->getTargetUrl() . "\n";
echo "invoices after={$after}\n";
if ($response->getSession()) {
    $e = $response->getSession()->get('errors');
    if ($e) {
        echo "validation errors: " . json_encode($e->toArray(), JSON_UNESCAPED_UNICODE) . "\n";
    }
    if ($response->getSession()->has('error')) {
        echo "flash error: " . $response->getSession()->get('error') . "\n";
    }
}
