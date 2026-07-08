<?php

namespace App\Console\Commands;

use App\Services\EmployeePresenceService;
use Illuminate\Console\Command;

class CheckEmployeePresence extends Command
{
    protected $signature = 'employees:check-presence';

    protected $description = 'Detect sales employees offline during active shift and log presence violations';

    public function handle(EmployeePresenceService $presence): int
    {
        if (! $presence->isEnabled()) {
            $this->info('Employee presence monitoring is disabled.');

            return self::SUCCESS;
        }

        $created = $presence->scanOfflineEmployees();
        $this->info("Presence scan complete — new violations: {$created}");

        return self::SUCCESS;
    }
}
