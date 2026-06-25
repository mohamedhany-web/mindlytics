<?php

namespace App\Jobs;

use App\Models\WhatsAppBatch;
use App\Models\WhatsAppBatchItem;
use App\Models\WorkshopRegistration;
use App\Services\WhatsAppBatchService;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessWhatsAppBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public int $tries = 1;

    public function __construct(public int $batchId)
    {
        $this->onQueue((string) config('whatsapp.queue', 'whatsapp'));
    }

    public function handle(WhatsAppService $whatsapp): void
    {
        $lock = Cache::lock('wa_process_batch_' . $this->batchId, 600);

        if (! $lock->get()) {
            Log::info('WhatsApp batch skipped — already processing', ['batch_id' => $this->batchId]);

            return;
        }

        try {
            $hasMore = $this->processChunk($whatsapp);

            if ($hasMore) {
                WhatsAppBatchService::dispatchBatch($this->batchId);
            }
        } finally {
            $lock->release();
        }
    }

    private function processChunk(WhatsAppService $whatsapp): bool
    {
        $batch = WhatsAppBatch::find($this->batchId);

        if (! $batch) {
            return false;
        }

        if ($batch->status === 'completed' && $batch->pendingCount() === 0) {
            return false;
        }

        $this->releaseStaleItems($batch);

        $batch->update([
            'status' => 'processing',
            'started_at' => $batch->started_at ?? now(),
            'completed_at' => null,
        ]);

        $chunkSize = max(1, (int) config('whatsapp.batch_chunk_size', 1));

        $items = $batch->items()
            ->where('status', 'pending')
            ->orderBy('sort_order')
            ->limit($chunkSize)
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
                    'user_id' => $item->user_id ?? $batch->created_by,
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

            Cache::put('wa_batch_activity_' . $batch->id, now()->timestamp, now()->addHours(6));
        }

        $batch->refresh();
        $remaining = $batch->items()->whereIn('status', ['pending', 'processing'])->count();

        if ($remaining === 0) {
            $batch->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            Log::info('WhatsApp batch completed', [
                'batch_id' => $batch->id,
                'sent' => $batch->sent_count,
                'failed' => $batch->failed_count,
            ]);

            return false;
        }

        Log::info('WhatsApp batch chunk done — more pending', [
            'batch_id' => $batch->id,
            'sent' => $batch->sent_count,
            'failed' => $batch->failed_count,
            'remaining' => $remaining,
        ]);

        return true;
    }

    private function releaseStaleItems(WhatsAppBatch $batch): void
    {
        $staleMinutes = max(1, (int) config('whatsapp.batch_stale_minutes', 10));

        WhatsAppBatchItem::query()
            ->where('batch_id', $batch->id)
            ->where('status', 'processing')
            ->where('updated_at', '<', now()->subMinutes($staleMinutes))
            ->update(['status' => 'pending']);
    }
}
