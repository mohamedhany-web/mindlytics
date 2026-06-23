<?php

namespace App\Services;

use App\Jobs\ProcessWhatsAppBatchJob;
use App\Models\WhatsAppBatch;
use App\Models\WhatsAppBatchItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class WhatsAppBatchService
{
    public static function isReady(): bool
    {
        static $ready = null;

        if ($ready !== null) {
            return $ready;
        }

        try {
            $batchTable = (new WhatsAppBatch)->getTable();
            $itemTable = (new WhatsAppBatchItem)->getTable();
            $ready = Schema::hasTable($batchTable) && Schema::hasTable($itemTable);
        } catch (\Throwable) {
            $ready = false;
        }

        return $ready;
    }

    public function latestForWorkshop(int $workshopId): ?WhatsAppBatch
    {
        if (! self::isReady()) {
            return null;
        }

        return WhatsAppBatch::query()
            ->where('source_type', 'workshop')
            ->where('source_id', $workshopId)
            ->latest()
            ->first();
    }

    /**
     * @param  Collection<int, array{recipient_name: string, phone: string, message: string, message_type?: string, workshop_registration_id?: int|null, user_id?: int|null}>  $items
     */
    public function createAndDispatch(
        string $sourceType,
        ?int $sourceId,
        string $title,
        ?string $messageTemplate,
        Collection $items,
        int $createdBy,
        array $meta = []
    ): WhatsAppBatch {
        if (! self::isReady()) {
            throw new \RuntimeException('جداول دفعات الواتساب غير موجودة. نفّذ php artisan migrate على السيرفر.');
        }

        $batch = WhatsAppBatch::create([
            'title' => $title,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'message_template' => $messageTemplate,
            'status' => 'pending',
            'total_count' => $items->count(),
            'created_by' => $createdBy,
            'meta' => $meta,
        ]);

        $order = 0;
        foreach ($items as $item) {
            WhatsAppBatchItem::create([
                'batch_id' => $batch->id,
                'recipient_name' => $item['recipient_name'] ?? null,
                'phone' => $item['phone'],
                'message' => $item['message'],
                'message_type' => $item['message_type'] ?? 'text',
                'workshop_registration_id' => $item['workshop_registration_id'] ?? null,
                'user_id' => $item['user_id'] ?? null,
                'sort_order' => $order++,
                'status' => 'pending',
            ]);
        }

        ProcessWhatsAppBatchJob::dispatchAfterResponse($batch->id);

        return $batch;
    }

    public function retryBatch(WhatsAppBatch $batch): void
    {
        WhatsAppBatchItem::query()
            ->where('batch_id', $batch->id)
            ->where('status', 'processing')
            ->update(['status' => 'pending']);

        $pendingCount = $batch->items()->where('status', 'pending')->count();

        if ($pendingCount === 0) {
            return;
        }

        $batch->update([
            'status' => 'pending',
            'completed_at' => null,
        ]);

        ProcessWhatsAppBatchJob::dispatchAfterResponse($batch->id);
    }
}
