<?php

namespace App\Jobs;

use App\Models\WhatsAppBatch;
use App\Models\WhatsAppBatchItem;
use App\Models\WorkshopRegistration;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * يُنفَّذ مرة واحدة عبر dispatchAfterResponse (بدون Queue) لتجنّب الإرسال المكرر.
 */
class ProcessWhatsAppBatchJob
{
    use Dispatchable;

    public function __construct(public int $batchId) {}

    public function handle(WhatsAppService $whatsapp): void
    {
        $lock = Cache::lock('wa_process_batch_' . $this->batchId, 7200);

        if (! $lock->get()) {
            Log::info('WhatsApp batch skipped — already processing', ['batch_id' => $this->batchId]);

            return;
        }

        try {
            $this->processBatch($whatsapp);
        } finally {
            $lock->release();
        }
    }

    private function processBatch(WhatsAppService $whatsapp): void
    {
        $batch = WhatsAppBatch::find($this->batchId);

        if (! $batch) {
            return;
        }

        if ($batch->status === 'completed' && $batch->pendingCount() === 0) {
            return;
        }

        $batch->update([
            'status' => 'processing',
            'started_at' => $batch->started_at ?? now(),
            'completed_at' => null,
        ]);

        $items = $batch->items()
            ->where('status', 'pending')
            ->orderBy('sort_order')
            ->get();

        foreach ($items as $item) {
            $claimed = WhatsAppBatchItem::query()
                ->where('id', $item->id)
                ->where('status', 'pending')
                ->update(['status' => 'processing']);

            if ($claimed === 0) {
                continue;
            }

            $item->refresh();

            $result = $whatsapp->sendMessage(
                $item->phone,
                $item->message,
                $item->message_type,
                [
                    'user_id' => $batch->created_by,
                    'batch_id' => $batch->id,
                ]
            );

            if ($result['success'] ?? false) {
                $item->update([
                    'status' => 'sent',
                    'whatsapp_message_id' => $result['message_id'] ?? null,
                    'sent_at' => now(),
                    'error_message' => null,
                ]);
                $batch->increment('sent_count');

                if ($item->workshop_registration_id) {
                    WorkshopRegistration::where('id', $item->workshop_registration_id)
                        ->update(['whatsapp_link_sent_at' => now()]);
                }
            } else {
                $item->update([
                    'status' => 'failed',
                    'error_message' => $result['error'] ?? 'فشل الإرسال',
                ]);
                $batch->increment('failed_count');
            }
        }

        $batch->refresh();
        $batch->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        Log::info('WhatsApp batch completed', [
            'batch_id' => $batch->id,
            'sent' => $batch->sent_count,
            'failed' => $batch->failed_count,
        ]);
    }
}
