<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use App\Services\WorkshopAttendanceService;
use Illuminate\Support\Facades\DB;

$workshopId = (int) ($argv[1] ?? 4);
$reset = in_array('--reset', $argv, true);

$workshop = Workshop::find($workshopId);
if (! $workshop) {
    fwrite(STDERR, "Workshop {$workshopId} not found\n");
    exit(1);
}

echo "Workshop #{$workshop->id}: {$workshop->title}\n";
echo "Slug: {$workshop->slug}\n";
echo "Confirm URL: ".url('/workshops/'.$workshop->slug.'/confirm')."\n\n";

$service = app(WorkshopAttendanceService::class);
$registrations = $workshop->registrations()->orderBy('id')->get();

if ($registrations->isEmpty()) {
    echo "No registrations — creating test fixtures...\n";
    $fixtures = [
        ['name' => 'أحمد محمد', 'phone' => '01012345678'],
        ['name' => 'Sara Ali', 'phone' => '+20 101 234 5679'],
        ['name' => 'محمد  حسن', 'phone' => '201012345680'],
        ['name' => 'Test User', 'phone' => '01112223344'],
    ];
    foreach ($fixtures as $f) {
        WorkshopRegistration::create([
            'workshop_id' => $workshop->id,
            'name' => $f['name'],
            'phone' => $f['phone'],
            'attendance_mode' => $workshop->mode === 'offline' ? 'offline' : 'online',
            'status' => 'confirmed',
            'checkin_token' => (string) Illuminate\Support\Str::uuid(),
        ]);
    }
    $registrations = $workshop->registrations()->orderBy('id')->get();
}

if ($reset) {
    echo "Resetting checked_in_at for all registrations...\n";
    WorkshopRegistration::where('workshop_id', $workshop->id)->update(['checked_in_at' => null]);
    $registrations = $workshop->registrations()->orderBy('id')->get();
}

$passed = 0;
$failed = 0;
$issues = [];

DB::beginTransaction();

try {
    foreach ($registrations as $reg) {
        $reg->checked_in_at = null;
        $reg->save();

        $phoneVariants = [
            $reg->phone,
            preg_replace('/[^0-9]/', '', $reg->phone ?? ''),
            str_starts_with(preg_replace('/[^0-9]/', '', $reg->phone ?? ''), '20')
                ? '0'.substr(preg_replace('/[^0-9]/', '', $reg->phone), 2)
                : null,
        ];

        $found = false;
        $lastResult = null;

        foreach (array_filter(array_unique($phoneVariants)) as $phoneInput) {
            $foundReg = $service->findRegistration($workshop, $reg->name, (string) $phoneInput);
            if ($foundReg && (int) $foundReg->id === (int) $reg->id) {
                $found = true;
                $result = $service->confirmByNameAndPhone($workshop, $reg->name, (string) $phoneInput);
                $lastResult = $result;
                break;
            }
        }

        if (! $found) {
            $failed++;
            $issues[] = "#{$reg->id} {$reg->name} ({$reg->phone}): findRegistration failed";
            echo "FAIL  #{$reg->id} {$reg->name} — not matched\n";
            continue;
        }

        if ($lastResult['status'] !== 'success') {
            $failed++;
            $issues[] = "#{$reg->id} expected success, got {$lastResult['status']}: {$lastResult['message']}";
            echo "FAIL  #{$reg->id} {$reg->name} — confirm status: {$lastResult['status']}\n";
            continue;
        }

        $reg->refresh();
        if (! $reg->checked_in_at) {
            $failed++;
            $issues[] = "#{$reg->id} checked_in_at not set";
            echo "FAIL  #{$reg->id} {$reg->name} — checked_in_at missing\n";
            continue;
        }

        // already confirmed path
        $again = $service->confirmByNameAndPhone($workshop, $reg->name, (string) $reg->phone);
        if ($again['status'] !== 'already') {
            $failed++;
            $issues[] = "#{$reg->id} second confirm should be 'already', got {$again['status']}";
            echo "FAIL  #{$reg->id} {$reg->name} — duplicate confirm: {$again['status']}\n";
            continue;
        }

        // wrong name should not match (unless only one phone candidate)
        $wrong = $service->findRegistration($workshop, 'اسم غير موجود xyz', (string) $reg->phone);
        if ($wrong && (int) $wrong->id === (int) $reg->id && $registrations->where('phone', $reg->phone)->count() === 1) {
            // single registration with this phone — expected to match by phone only; skip
        } elseif ($wrong && (int) $wrong->id === (int) $reg->id) {
            $failed++;
            $issues[] = "#{$reg->id} wrong name matched same registration";
            echo "FAIL  #{$reg->id} wrong name matched\n";
            continue;
        }

        $passed++;
        echo "OK    #{$reg->id} {$reg->name} ({$reg->phone})\n";
    }

    // HTTP smoke test
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $getRequest = Illuminate\Http\Request::create('/workshops/'.$workshop->slug.'/confirm', 'GET');
    $getResponse = $kernel->handle($getRequest);
    $getStatus = $getResponse->getStatusCode();
    echo "\nGET /workshops/{$workshop->slug}/confirm => HTTP {$getStatus}\n";
    if ($getStatus !== 200) {
        $failed++;
        $issues[] = "GET confirm page returned {$getStatus}";
    }

    $sample = $registrations->first();
    if ($sample) {
        $sample->checked_in_at = null;
        $sample->save();

        $postRequest = Illuminate\Http\Request::create('/workshops/'.$workshop->slug.'/confirm', 'POST', [
            'name' => $sample->name,
            'phone' => $sample->phone,
        ]);
        $postRequest->headers->set('Accept', 'text/html');
        $postRequest->setLaravelSession($app->make('session')->driver());
        $postRequest->session()->start();
        $postRequest->session()->token();
        $postRequest->merge(['_token' => $postRequest->session()->token()]);
        $postResponse = $kernel->handle($postRequest);
        $postStatus = $postResponse->getStatusCode();
        echo "POST confirm ({$sample->name}) => HTTP {$postStatus}\n";
        if (! in_array($postStatus, [200, 302], true)) {
            $failed++;
            $issues[] = "POST confirm returned {$postStatus}";
        }
        $sample->refresh();
        if (! in_array($postStatus, [200, 302], true) || ! $sample->checked_in_at) {
            if (! $sample->checked_in_at) {
                $failed++;
                $issues[] = 'POST confirm did not set checked_in_at';
                echo "FAIL  POST did not set checked_in_at\n";
            }
        } else {
            echo "OK    POST set checked_in_at\n";
        }
    }

    DB::rollBack();
    echo "\n(DB rolled back — no permanent changes)\n";
} catch (Throwable $e) {
    DB::rollBack();
    throw $e;
}

echo "\n=== Summary ===\n";
echo "Passed registration tests: {$passed}/{$registrations->count()}\n";
echo "Failed: {$failed}\n";

if ($issues !== []) {
    echo "\nIssues:\n";
    foreach ($issues as $issue) {
        echo " - {$issue}\n";
    }
    exit(1);
}

echo "All tests passed.\n";
exit(0);
