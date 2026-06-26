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
                'sales_lead_id' => $item['sales_lead_id'] ?? null,
                'user_id' => $item['user_id'] ?? null,
                'sort_order' => $order++,
                'status' => 'pending',
            ]);
        }

        self::dispatchBatch($batch->id, afterResponse: true);

        return $batch;
    }

    public function retryBatch(WhatsAppBatch $batch): void
    {
        if ($batch->status === 'cancelled') {
            throw new \RuntimeException('هذه الدفعة موقوفة — لا يمكن إعادة التشغيل تلقائياً.');
        }

        WhatsAppBatchItem::query()
            ->where('batch_id', $batch->id)
            ->where('status', 'processing')
            ->update(['status' => 'pending']);

        $pendingCount = $batch->items()->whereIn('status', ['pending', 'processing'])->count();

        if ($pendingCount === 0) {
            return;
        }

        $batch->update([
            'status' => 'pending',
            'completed_at' => null,
        ]);

        self::dispatchBatch($batch->id);
    }

    public function cancelBatch(WhatsAppBatch $batch): int
    {
        if ($batch->isFinished()) {
            return 0;
        }

        Cache::put('wa_batch_cancelled_' . $batch->id, true, now()->addDays(7));

        $count = WhatsAppBatchItem::query()
            ->where('batch_id', $batch->id)
            ->whereIn('status', ['pending', 'processing'])
            ->update([
                'status' => 'cancelled',
                'error_message' => 'تم إيقاف الإرسال يدوياً',
            ]);

        $batch->update([
            'status' => 'cancelled',
            'completed_at' => now(),
        ]);

        return $count;
    }

    public static function isCancelled(int $batchId): bool
    {
        return (bool) Cache::get('wa_batch_cancelled_' . $batchId);
    }

    /**
     * جدولة معالجة الدفعة.
     * - أول تشغيل: بعد إرسال الرد للمتصفح (لا يعلّق صفحة الإدارة).
     * - التشغيلات التالية: مباشرة في الطابور.
     */
    public static function dispatchBatch(int $batchId, bool $afterResponse = false): void
    {
        $batch = WhatsAppBatch::find($batchId);
        if (! $batch || $batch->status === 'cancelled' || self::isCancelled($batchId)) {
            return;
        }

        Cache::put('wa_batch_dispatched_' . $batchId, now()->timestamp, now()->addHours(6));

        if ($afterResponse && config('whatsapp.dispatch_after_response', true)) {
            ProcessWhatsAppBatchJob::dispatchAfterResponse($batchId);

            return;
        }

        ProcessWhatsAppBatchJob::dispatch($batchId);
    }

    /**
     * إعادة تشغيل دفعة عالقة (pending أو processing بدون نشاط حديث).
     */
    public static function kickstartIfStalled(WhatsAppBatch $batch): bool
    {
        if ($batch->isFinished()) {
            return false;
        }

        $pending = $batch->items()->whereIn('status', ['pending', 'processing'])->count();

        if ($pending === 0) {
            $batch->update([
                'status' => 'completed',
                'completed_at' => $batch->completed_at ?? now(),
            ]);

            return false;
        }

        WhatsAppBatchItem::query()
            ->where('batch_id', $batch->id)
            ->where('status', 'processing')
            ->where('updated_at', '<', now()->subMinutes(max(1, (int) config('whatsapp.batch_stale_minutes', 10))))
            ->update(['status' => 'pending']);

        $lastActivity = Cache::get('wa_batch_activity_' . $batch->id);
        $recentDispatch = Cache::get('wa_batch_dispatched_' . $batch->id);

        if ($batch->status === 'pending' && $recentDispatch && $recentDispatch >= now()->subSeconds(60)->timestamp) {
            return false;
        }

        $stalled = $batch->status === 'pending'
            || ($batch->status === 'processing' && ($lastActivity === null || $lastActivity < now()->subMinutes(2)->timestamp));

        if (! $stalled) {
            return false;
        }

        self::dispatchBatch($batch->id);

        return true;
    }
}
