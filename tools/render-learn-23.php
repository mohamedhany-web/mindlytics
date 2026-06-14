<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('role', 'student')->first() ?? App\Models\User::first();
Auth::login($user);

$controller = app(App\Http\Controllers\Student\MyCourseController::class);
$request = Illuminate\Http\Request::create('/my-courses/23/learn', 'GET');
$response = $controller->learn(23, $request);
$html = $response->render();

file_put_contents(__DIR__ . '/learn-23.html', $html);

if (preg_match('/id="learn-lectures-data">([^<]+)</', $html, $m)) {
    $json = json_decode($m[1], true);
    echo "lectures-data keys: " . (is_array($json) ? count($json) : 'not array') . "\n";
    if (is_array($json)) {
        echo "is_list=" . (array_is_list($json) ? 'yes' : 'no') . "\n";
        foreach (array_slice($json, 0, 3, true) as $k => $v) {
            echo "  key=$k id=" . ($v['id'] ?? '?') . "\n";
        }
    }
}

preg_match_all('/data-item-type="lecture"/', $html, $matches);
echo "sidebar lecture items: " . count($matches[0]) . "\n";

preg_match_all('/function courseFocusMode/', $html, $fm);
echo "courseFocusMode defined: " . count($fm[0]) . "\n";

echo "html length: " . strlen($html) . "\n";
