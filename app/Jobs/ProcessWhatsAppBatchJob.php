<?php

namespace App\Jobs;

use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\WhatsAppBatch;
use App\Models\WhatsAppBatchItem;
use App\Models\WorkshopRegistration;
use App\Services\WhatsAppBatchService;
use App\Services\WhatsAppBridgeService;
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
            $deadline = time() + max(30, (int) config('whatsapp.batch_job_max_seconds', 240));
            $maxMessages = max(1, (int) config('whatsapp.batch_max_messages_per_job', 12));
            $processed = 0;
            $hasMore = true;

            while ($hasMore && $processed < $maxMessages && time() < $deadline) {
                $chunkProcessed = $this->processChunk($whatsapp);
                $processed += $chunkProcessed;

                if ($chunkProcessed === 0) {
                    $hasMore = false;
                    break;
                }

                $batch = WhatsAppBatch::find($this->batchId);
                if (! $batch || $batch->pendingCount() === 0) {
                    $hasMore = false;
                    break;
                }
            }

            $batch = WhatsAppBatch::find($this->batchId);
            if ($batch && ! $batch->isFinished() && $batch->pendingCount() > 0) {
                WhatsAppBatchService::dispatchBatch($this->batchId);
            }
        } finally {
            $lock->release();
        }
    }

    /**
     * @return int عدد العناصر التي عُولجت في هذا الجزء
     */
    private function processChunk(WhatsAppService $whatsapp): int
    {
        $batch = WhatsAppBatch::find($this->batchId);

        if (! $batch) {
            return 0;
        }

        if ($batch->status === 'cancelled' || WhatsAppBatchService::isCancelled($batch->id)) {
            return 0;
        }

        if ($batch->status === 'completed' && $batch->pendingCount() === 0) {
            return 0;
        }

        $this->releaseStaleItems($batch);

        $batch->update([
            'status' => 'processing',
            'started_at' => $batch->started_at ?? now(),
            'completed_at' => null,
        ]);

        $chunkSize = max(1, (int) config('whatsapp.batch_chunk_size', 3));

        $items = $batch->items()
            ->where('status', 'pending')
            ->orderBy('sort_order')
            ->limit($chunkSize)
            ->get();

        if ($items->isEmpty()) {
            $this->finalizeBatchIfDone($batch);

            return 0;
        }

        $processed = 0;
        $bridgeReady = null;

        foreach ($items as $item) {
            $batch->refresh();
            if ($batch->status === 'cancelled' || WhatsAppBatchService::isCancelled($batch->id)) {
                return $processed;
            }

            $claimed = WhatsAppBatchItem::query()
                ->where('id', $item->id)
                ->where('status', 'pending')
                ->update(['status' => 'processing']);

            if ($claimed === 0) {
                continue;
            }

            $item->refresh();
            $processed++;

            if ($bridgeReady === null) {
                $gate = app(WhatsAppBridgeService::class)->canSendNow();
                $bridgeReady = (bool) ($gate['success'] ?? false);
                if (! $bridgeReady) {
                    $this->handleSendFailure($item, $batch, (string) ($gate['error'] ?? 'الواتساب غير جاهز للإرسال.'));
                    continue;
                }
            }

            $result = $this->sendWithRetries($whatsapp, $item, $batch, skipReadyCheck: true);

            if ($result['success'] ?? false) {
                $item->update([
                    'status' => 'sent',
                    'whatsapp_message_id' => $result['message_id'] ?? null,
                    'sent_at' => now(),
                    'error_message' => null,
                    'send_attempts' => 0,
                ]);
                $batch->increment('sent_count');

                if ($item->workshop_registration_id) {
                    WorkshopRegistration::where('id', $item->workshop_registration_id)
                        ->update(['whatsapp_link_sent_at' => now()]);
                }

                $this->recordSalesLeadActivity($batch, $item);
            } else {
                $this->handleSendFailure($item, $batch, (string) ($result['error'] ?? 'فشل الإرسال'));
            }

            Cache::put('wa_batch_activity_' . $batch->id, now()->timestamp, now()->addHours(6));
        }

        $batch->refresh();
        $this->finalizeBatchIfDone($batch);

        if ($batch->pendingCount() > 0) {
            Log::info('WhatsApp batch chunk done — more pending', [
                'batch_id' => $batch->id,
                'sent' => $batch->sent_count,
                'failed' => $batch->failed_count,
                'remaining' => $batch->pendingCount(),
            ]);
        }

        return $processed;
    }

    private function finalizeBatchIfDone(WhatsAppBatch $batch): void
    {
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
        }
    }

    private function handleSendFailure(WhatsAppBatchItem $item, WhatsAppBatch $batch, string $error): void
    {
        $attempts = (int) ($item->send_attempts ?? 0) + 1;
        $maxItemAttempts = max(1, (int) config('whatsapp.batch_item_max_attempts', 6));
        $transient = $this->isTransientWhatsAppError($error);

        if ($transient && $attempts < $maxItemAttempts) {
            $item->update([
                'status' => 'pending',
                'send_attempts' => $attempts,
                'error_message' => $error,
            ]);

            Log::warning('WhatsApp batch item deferred (transient)', [
                'batch_id' => $batch->id,
                'item_id' => $item->id,
                'attempt' => $attempts,
                'error' => $error,
            ]);

            sleep(min(30, 8 + ($attempts * 3)));

            return;
        }

        $item->update([
            'status' => 'failed',
            'send_attempts' => $attempts,
            'error_message' => $error,
        ]);
        $batch->increment('failed_count');
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

    /**
     * @return array{success?: bool, message_id?: int, error?: string}
     */
    private function sendWithRetries(WhatsAppService $whatsapp, WhatsAppBatchItem $item, WhatsAppBatch $batch, bool $skipReadyCheck = false): array
    {
        $maxAttempts = max(1, (int) config('whatsapp.batch_max_attempts', 3));
        $lastResult = ['success' => false, 'error' => 'فشل الإرسال'];

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $lastResult = $whatsapp->sendMessage(
                $item->phone,
                $item->message,
                $item->message_type,
                [
                    'user_id' => $item->user_id ?? $batch->created_by,
                    'batch_id' => $batch->id,
                    'skip_ready_check' => $skipReadyCheck,
                ]
            );

            if ($lastResult['success'] ?? false) {
                return $lastResult;
            }

            $error = (string) ($lastResult['error'] ?? '');
            if ($attempt >= $maxAttempts || ! $this->isTransientWhatsAppError($error)) {
                return $lastResult;
            }

            Log::info('WhatsApp batch item retry', [
                'batch_id' => $batch->id,
                'item_id' => $item->id,
                'attempt' => $attempt + 1,
                'error' => $error,
            ]);

            sleep(min(45, 10 * $attempt));
        }

        return $lastResult;
    }

    private function isTransientWhatsAppError(string $error): bool
    {
        $error = mb_strtolower($error);

        $needles = [
            'not connected',
            'غير متصل',
            'غير جاهز',
            'لا يمكن الإرسال',
            'bridge',
            'timeout',
            'timed out',
            'econnreset',
            'connection refused',
            'could not resolve',
            'curl error',
            '502',
            '503',
            '504',
            'protocol',
            'degraded',
            'chrome',
            'session',
            'try again',
            'انتظر',
            'execution context',
            'target closed',
            'disconnected',
        ];

        foreach ($needles as $needle) {
            if (str_contains($error, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function recordSalesLeadActivity(WhatsAppBatch $batch, WhatsAppBatchItem $item): void
    {
        if ($batch->source_type !== 'sales_group' || ! $item->sales_lead_id) {
            return;
        }

        $lead = SalesLead::find($item->sales_lead_id);
        if (! $lead) {
            return;
        }

        SalesActivity::create([
            'sales_lead_id' => $lead->id,
            'user_id' => $batch->created_by,
            'type' => 'whatsapp',
            'title' => 'واتساب جماعي — دفعة #' . $batch->id,
            'body' => mb_substr($item->message, 0, 500),
        ]);

        $lead->touchLastContactFromActivity('whatsapp');
    }
}
