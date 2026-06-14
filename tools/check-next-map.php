<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::first();
Auth::login($user);

$controller = app(App\Http\Controllers\Student\MyCourseController::class);
$html = $controller->learn(23, Illuminate\Http\Request::create('/my-courses/23/learn', 'GET'))->render();

if (preg_match('/id="learn-next-item-map">([^<]+)</', $html, $m)) {
    echo "next-item-map:\n";
    echo json_encode(json_decode($m[1], true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}
