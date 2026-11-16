<?php

namespace App\Domains\Messaging\Services;

use App\Domains\Marketing\Models\AdvertisingCampaign;
use App\Domains\Messaging\Jobs\SyncMetaCampaignLeadsJob;
use App\Domains\Messaging\Models\MetaCampaignLeadSyncRun;

class MetaCampaignLeadSyncDispatcher
{
    /**
     * @return array{success: bool, message: string, run: MetaCampaignLeadSyncRun}
     */
    public function queue(
        string $metaCampaignId,
        ?string $metaCampaignName = null,
        ?AdvertisingCampaign $local = null,
        ?int $actorUserId = null,
    ): array {
        $metaCampaignId = trim($metaCampaignId);
        if ($metaCampaignId === '') {
            return [
                'success' => false,
                'message' => 'معرّف حملة ميتا غير صالح.',
                'run' => new MetaCampaignLeadSyncRun,
            ];
        }

        $active = MetaCampaignLeadSyncRun::query()
            ->where('meta_campaign_id', $metaCampaignId)
            ->whereIn('status', [
                MetaCampaignLeadSyncRun::STATUS_QUEUED,
                MetaCampaignLeadSyncRun::STATUS_RUNNING,
            ])
            ->latest('id')
            ->first();

        if ($active) {
            $startedAt = $active->started_at ?: $active->created_at;
            $stale = $startedAt && $startedAt->lt(now()->subMinutes(20));
            if ($stale) {
                $active->update([
                    'status' => MetaCampaignLeadSyncRun::STATUS_FAILED,
                    'error' => 'انتهت مهلة السحب السابق دون اكتمال.',
                    'message' => 'تم اعتبار السحب السابق متوقفاً وإعادة الجدولة.',
                    'finished_at' => now(),
                ]);
            } else {
                return [
                    'success' => true,
                    'message' => 'سحب الليدز لهذه الحملة قيد التنفيذ بالفعل — تابع الحالة من صفحة ليدز الحملة.',
                    'run' => $active,
                ];
            }
        }

        $local ??= AdvertisingCampaign::query()->where('meta_campaign_id', $metaCampaignId)->first();

        $run = MetaCampaignLeadSyncRun::query()->create([
            'meta_campaign_id' => $metaCampaignId,
            'meta_campaign_name' => $metaCampaignName ?: ($local?->meta_campaign_name ?: $local?->name),
            'advertising_campaign_id' => $local?->id,
            'requested_by' => $actorUserId,
            'status' => MetaCampaignLeadSyncRun::STATUS_QUEUED,
            'message' => 'تمت إضافة المهمة إلى الطابور.',
        ]);

        SyncMetaCampaignLeadsJob::dispatch($run->id);

        return [
            'success' => true,
            'message' => 'تم جدولة سحب ليدز الحملة في الخلفية. يمكنك متابعة النتائج من صفحة ليدز الحملة دون انتظار الصفحة.',
            'run' => $run,
        ];
    }
}
