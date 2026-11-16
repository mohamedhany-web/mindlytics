<?php

namespace App\Domains\Messaging\Jobs;

use App\Domains\Marketing\Models\AdvertisingCampaign;
use App\Domains\Messaging\Models\MetaCampaignLeadSyncRun;
use App\Domains\Messaging\Services\MetaCampaignLeadSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SyncMetaCampaignLeadsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 900;

    public int $tries = 1;

    public function __construct(public int $runId)
    {
        // طابور مستقل حتى لا يتأخر خلف جوبز قديمة على default
        $this->onQueue('meta');
    }

    public function handle(MetaCampaignLeadSyncService $sync): void
    {
        $run = MetaCampaignLeadSyncRun::query()->find($this->runId);
        if (! $run) {
            return;
        }

        $lock = Cache::lock('meta_campaign_lead_sync:'.$run->meta_campaign_id, 950);
        if (! $lock->get()) {
            $run->update([
                'status' => MetaCampaignLeadSyncRun::STATUS_FAILED,
                'error' => 'سحب آخر لنفس الحملة ما زال قيد التنفيذ.',
                'finished_at' => now(),
                'message' => 'تم تخطي المهمة لأن سحباً آخر يعمل حالياً.',
            ]);

            return;
        }

        try {
            $run->update([
                'status' => MetaCampaignLeadSyncRun::STATUS_RUNNING,
                'started_at' => now(),
                'error' => null,
                'message' => 'بدأ السحب… ستظهر الليدز أول بأول في الصفحة.',
            ]);

            $local = $run->advertising_campaign_id
                ? AdvertisingCampaign::query()->find($run->advertising_campaign_id)
                : AdvertisingCampaign::query()->where('meta_campaign_id', $run->meta_campaign_id)->first();

            $progress = function (array $p) use ($run): void {
                $fresh = MetaCampaignLeadSyncRun::query()->find($run->id);
                if (! $fresh || $fresh->status !== MetaCampaignLeadSyncRun::STATUS_RUNNING) {
                    return;
                }

                $fresh->forceFill([
                    'created_count' => (int) ($p['created'] ?? $fresh->created_count),
                    'updated_count' => (int) ($p['updated'] ?? $fresh->updated_count),
                    'skipped_count' => (int) ($p['skipped'] ?? $fresh->skipped_count),
                    'conversations_linked' => (int) ($p['conversations_linked'] ?? $fresh->conversations_linked),
                    'import_batch' => $p['import_batch'] ?? $fresh->import_batch,
                    'message' => (string) ($p['message'] ?? $fresh->message),
                    'diagnostics' => array_merge($fresh->diagnostics ?? [], [
                        'phase' => $p['phase'] ?? null,
                        'live' => true,
                    ]),
                ])->save();
            };

            $result = $sync->syncByMetaCampaignId(
                (string) $run->meta_campaign_id,
                $local,
                $run->meta_campaign_name,
                $run->requested_by ? (int) $run->requested_by : null,
                $progress,
            );

            $run->refresh();
            $run->update([
                'status' => ($result['success'] ?? false)
                    ? MetaCampaignLeadSyncRun::STATUS_COMPLETED
                    : MetaCampaignLeadSyncRun::STATUS_FAILED,
                'import_batch' => $result['diagnostics']['import_batch']
                    ?? data_get($result, 'import_batch')
                    ?? $run->import_batch,
                'created_count' => (int) ($result['created'] ?? 0),
                'updated_count' => (int) ($result['updated'] ?? 0),
                'skipped_count' => (int) ($result['skipped'] ?? 0),
                'conversations_linked' => (int) ($result['conversations_linked'] ?? 0),
                'message' => (string) ($result['message'] ?? ''),
                'diagnostics' => $result['diagnostics'] ?? null,
                'error' => ($result['success'] ?? false) ? null : (string) ($result['message'] ?? 'فشل السحب'),
                'advertising_campaign_id' => $result['advertising_campaign_id'] ?? $run->advertising_campaign_id,
                'finished_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('SyncMetaCampaignLeadsJob failed', [
                'run_id' => $this->runId,
                'error' => $e->getMessage(),
            ]);
            $run->update([
                'status' => MetaCampaignLeadSyncRun::STATUS_FAILED,
                'error' => $e->getMessage(),
                'message' => 'فشل سحب ليدز الكامبين: '.$e->getMessage(),
                'finished_at' => now(),
            ]);
        } finally {
            optional($lock)->release();
        }
    }
}
