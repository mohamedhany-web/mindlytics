<?php

namespace App\Jobs;

use App\Models\WhatsAppBatch;
use App\Models\WorkshopRegistration;
use App\Services\WhatsAppPacingService;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWhatsAppBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 7200;

    public int $tries = 1;

    public function __construct(public int $batchId) {}

    public function handle(WhatsAppService $whatsapp, WhatsAppPacingService $pacing): void
    {
        $batch = WhatsAppBatch::find($this->batchId);

        if (! $batch || $batch->isFinished()) {
            return;
        }

        $batch->update([
            'status' => 'processing',
            'started_at' => $batch->started_at ?? now(),
        ]);

        $items = $batch->items()
            ->where('status', 'pending')
            ->orderBy('sort_order')
            ->get();

        foreach ($items as $item) {
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

    public function failed(\Throwable $exception): void
    {
        $batch = WhatsAppBatch::find($this->batchId);

        if (! $batch) {
            return;
        }

        $batch->update([
            'status' => 'completed',
            'completed_at' => now(),
            'meta' => array_merge($batch->meta ?? [], [
                'job_error' => $exception->getMessage(),
            ]),
        ]);

        Log::error('WhatsApp batch job failed', [
            'batch_id' => $this->batchId,
            'error' => $exception->getMessage(),
        ]);
    }
}
