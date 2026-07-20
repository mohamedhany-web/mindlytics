<?php

/**
 * Smoke test: guest checkout page + quick-register JSON.
 * Usage: php scripts/test-checkout-quick-register.php
 */

use App\Models\AdvancedCourse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$course = AdvancedCourse::query()
    ->where('is_active', true)
    ->publicCatalog()
    ->where(function ($q) {
        $q->where('is_free', false)->orWhere('price', '>', 0);
    })
    ->orderByDesc('id')
    ->first();

if (! $course) {
    fwrite(STDERR, "FAIL: no paid public course found\n");
    exit(1);
}

echo "Using course #{$course->id} — {$course->title}\n";

// 1) Guest can open checkout
$get = Illuminate\Http\Request::create('/course/'.$course->id.'/checkout', 'GET');
$getResponse = $kernel->handle($get);
$status = $getResponse->getStatusCode();
$body = $getResponse->getContent();
$kernel->terminate($get, $getResponse);

if ($status !== 200) {
    fwrite(STDERR, "FAIL: GET checkout returned {$status}\n");
    exit(1);
}
if (! str_contains($body, 'أنشئ حسابك لإتمام الشراء')) {
    fwrite(STDERR, "FAIL: quick-register UI missing on checkout page\n");
    exit(1);
}
if (! str_contains($body, 'checkoutQuickRegister')) {
    fwrite(STDERR, "FAIL: checkoutQuickRegister JS missing\n");
    exit(1);
}
echo "OK: guest checkout page renders quick-register UI\n";

// Extract CSRF from cookie/session via a follow-up request with session
$cookieJar = [];
foreach ($getResponse->headers->getCookies() as $cookie) {
    $cookieJar[$cookie->getName()] = $cookie->getValue();
}

$sessionCookieName = null;
foreach (array_keys($cookieJar) as $name) {
    if (str_starts_with($name, 'laravel_session') || $name === config('session.cookie')) {
        $sessionCookieName = $name;
        break;
    }
}

// Bootstrap app properly for CSRF via acting as HTTP with session
$email = 'quickreg_'.Str::lower(Str::random(8)).'@example.test';
$phoneNational = '1'.substr((string) random_int(100000000, 999999999), 0, 9);

$postData = [
    'name' => 'Quick Reg Test',
    'country_code' => '+20',
    'phone' => $phoneNational,
    'email' => $email,
    'password' => 'Password123!',
    'password_confirmation' => 'Password123!',
];

// Use Laravel HTTP kernel with session from previous response
$cookieHeader = [];
foreach ($cookieJar as $n => $v) {
    $cookieHeader[] = $n.'='.$v;
}

// Get CSRF token from rendered page
preg_match('/name="csrf-token"\s+content="([^"]+)"/', $body, $m);
$csrf = $m[1] ?? null;
if (! $csrf) {
    fwrite(STDERR, "FAIL: csrf-token meta not found\n");
    exit(1);
}

$post = Illuminate\Http\Request::create(
    '/course/'.$course->id.'/checkout/quick-register',
    'POST',
    [],
    $cookieJar,
    [],
    [
        'HTTP_ACCEPT' => 'application/json',
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_CSRF_TOKEN' => $csrf,
        'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
    ],
    json_encode($postData)
);

$postResponse = $kernel->handle($post);
$postStatus = $postResponse->getStatusCode();
$postBody = $postResponse->getContent();
$kernel->terminate($post, $postResponse);

$json = json_decode($postBody, true);
echo "quick-register HTTP {$postStatus}: {$postBody}\n";

if ($postStatus !== 200 || empty($json['success'])) {
    // Phone validation may fail depending on country config — try SA if EG fails
    if ($postStatus === 422) {
        echo "WARN: first attempt validation failed, trying alternate phone format...\n";
        $email2 = 'quickreg_'.Str::lower(Str::random(8)).'@example.test';
        $postData2 = [
            'name' => 'Quick Reg Test',
            'country_code' => config('phone_countries.countries.0.dial_code', '+966'),
            'phone' => '512345678',
            'email' => $email2,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];
        // Re-fetch page for fresh CSRF/session
        $get2 = Illuminate\Http\Request::create('/course/'.$course->id.'/checkout', 'GET');
        $get2Response = $kernel->handle($get2);
        $body2 = $get2Response->getContent();
        $cookies2 = [];
        foreach ($get2Response->headers->getCookies() as $cookie) {
            $cookies2[$cookie->getName()] = $cookie->getValue();
        }
        $kernel->terminate($get2, $get2Response);
        preg_match('/name="csrf-token"\s+content="([^"]+)"/', $body2, $m2);
        $csrf2 = $m2[1] ?? $csrf;

        // Prefer default country dial from config
        $countries = config('phone_countries.countries', []);
        $default = collect($countries)->firstWhere('code', config('phone_countries.default_country', 'SA')) ?? ($countries[0] ?? null);
        if ($default) {
            $postData2['country_code'] = $default['dial_code'];
            // Use example digits if available
            $example = preg_replace('/\D/', '', (string) ($default['example'] ?? $default['placeholder'] ?? '512345678'));
            $postData2['phone'] = $example !== '' ? $example : '512345678';
        }

        $post2 = Illuminate\Http\Request::create(
            '/course/'.$course->id.'/checkout/quick-register',
            'POST',
            [],
            $cookies2,
            [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_CSRF_TOKEN' => $csrf2,
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
            ],
            json_encode($postData2)
        );
        $post2Response = $kernel->handle($post2);
        $postStatus = $post2Response->getStatusCode();
        $postBody = $post2Response->getContent();
        $kernel->terminate($post2, $post2Response);
        $json = json_decode($postBody, true);
        echo "retry HTTP {$postStatus}: {$postBody}\n";
        $email = $postData2['email'];
    }
}

if ($postStatus !== 200 || empty($json['success'])) {
    fwrite(STDERR, "FAIL: quick-register did not succeed\n");
    exit(1);
}

if (empty($json['csrf_token'])) {
    fwrite(STDERR, "FAIL: csrf_token missing from quick-register response\n");
    exit(1);
}

$user = User::where('email', $email)->first();
if (! $user || $user->role !== 'student') {
    fwrite(STDERR, "FAIL: student user not created\n");
    exit(1);
}

echo "OK: quick-register created student #{$user->id} and returned csrf_token\n";

// Cleanup test user
$user->delete();
echo "OK: cleaned up test user\n";
echo "ALL CHECKS PASSED\n";
