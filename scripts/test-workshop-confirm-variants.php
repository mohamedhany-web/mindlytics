<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use App\Services\WorkshopAttendanceService;

$workshopId = (int) ($argv[1] ?? 1);
$workshop = Workshop::findOrFail($workshopId);
$service = app(WorkshopAttendanceService::class);
$failed = 0;

echo "Extended variant tests for workshop #{$workshopId}\n\n";

$registrations = $workshop->registrations()->orderBy('id')->get();

foreach ($registrations as $reg) {
    $phones = array_unique(array_filter([
        $reg->phone,
        preg_replace('/[^0-9]/', '', $reg->phone ?? ''),
        (function ($p) {
            $d = preg_replace('/[^0-9]/', '', $p ?? '');
            if (str_starts_with($d, '20')) {
                return '0'.substr($d, 2);
            }
            if (str_starts_with($d, '01')) {
                return '2'.$d;
            }

            return $d;
        })($reg->phone),
        '+'.preg_replace('/[^0-9]/', '', $reg->phone ?? ''),
    ]));

    $names = array_unique([
        $reg->name,
        mb_strtolower($reg->name, 'UTF-8'),
        strtoupper($reg->name),
        preg_replace('/\s+/', '  ', trim($reg->name)),
    ]);

    foreach ($phones as $phone) {
        foreach ($names as $name) {
            $found = $service->findRegistration($workshop, $name, (string) $phone);
            $ok = $found && (int) $found->id === (int) $reg->id;
            echo ($ok ? 'OK  ' : 'FAIL')." #{$reg->id} name=[{$name}] phone=[{$phone}]\n";
            if (! $ok) {
                $failed++;
            }
        }
    }
}

$result = $service->confirmByNameAndPhone($workshop, 'Unknown Person', '01999999999');
echo ($result['status'] === 'not_found' ? 'OK  ' : 'FAIL')." unknown registration => {$result['status']}\n";
if ($result['status'] !== 'not_found') {
    $failed++;
}

echo "\n".($failed === 0 ? 'All variant tests passed.' : "Failed: {$failed}")."\n";
exit($failed === 0 ? 0 : 1);
