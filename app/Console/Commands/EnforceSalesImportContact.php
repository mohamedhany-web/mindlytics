<?php

namespace App\Console\Commands;

use App\Models\SalesLead;
use App\Models\User;
use App\Services\SalesNotificationService;
use Illuminate\Console\Command;

class EnforceSalesImportContact extends Command
{
    protected $signature = 'sales:enforce-import-contact {--days=2 : Days without contact before alert}';

    protected $description = 'Alert reps and admins when imported leads have no contact after N days';

    public function handle(SalesNotificationService $notifications): int
    {
        $days = max(1, (int) $this->option('days'));
        $cutoff = now()->subDays($days);

        $leads = SalesLead::query()
            ->whereNotNull('import_batch')
            ->openPipeline()
            ->where('created_at', '<=', $cutoff)
            ->where(function ($q) use ($cutoff) {
                $q->whereNull('last_contacted_at')
                    ->orWhere('last_contacted_at', '<', $cutoff);
            })
            ->get(['id', 'assigned_to', 'import_batch', 'name']);

        if ($leads->isEmpty()) {
            $this->info('No stale imported leads.');

            return self::SUCCESS;
        }

        $byRep = $leads->groupBy('assigned_to');
        $sent = 0;

        foreach ($byRep as $repId => $repLeads) {
            $rep = User::query()->find($repId);
            if (! $rep || ! $rep->isSalesEmployee()) {
                continue;
            }

            $batchCounts = $repLeads->groupBy('import_batch')->map->count();
            $notifications->notifyImportBatchStale($rep, $batchCounts->sum(), $batchCounts->keys()->first(), $days);
            $sent++;
        }

        $this->info("Import contact enforcement alerts sent to {$sent} rep(s) for {$leads->count()} lead(s).");

        return self::SUCCESS;
    }
}
