<?php

namespace Tests\Feature\Architecture;

use App\Support\Architecture\ModularityChecks;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Console\Kernel;
use Tests\TestCase;

/**
 * أوامر artisan والجدولة لم تتغير بنقل الأوامر و الـ schedule إلى Providers المجالات.
 * اللقطة في tests/Snapshots/console.txt. لتحديثها عن قصد (أمر جديد أو تغيير موعد):
 * UPDATE_CONSOLE_SNAPSHOT=1 php artisan test --filter=ConsoleSnapshotTest
 */
class ConsoleSnapshotTest extends TestCase
{
    public function test_commands_and_schedule_match_snapshot(): void
    {
        $actual = ModularityChecks::consoleSnapshot($this->app->make(Kernel::class), $this->app->make(Schedule::class));
        $path = base_path('tests/Snapshots/console.txt');

        if (getenv('UPDATE_CONSOLE_SNAPSHOT') || ! is_file($path)) {
            file_put_contents($path, $actual);
            $this->markTestSkipped('Console snapshot written to tests/Snapshots/console.txt');
        }

        $this->assertSame(file_get_contents($path), $actual, 'Artisan commands or schedule changed (see tests/Snapshots/console.txt)');
    }
}
