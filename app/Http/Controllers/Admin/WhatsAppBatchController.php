<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWhatsAppBatchJob;
use App\Models\WhatsAppBatch;
use App\Models\WhatsAppBatchItem;
use App\Models\Workshop;
use App\Services\WhatsAppBatchService;
use App\Services\WhatsAppBridgeService;
use Illuminate\Http\Request;

class WhatsAppBatchController extends Controller
{
    public function __construct(
        private WhatsAppBatchService $batchService
    ) {}

    public function index(Request $request)
    {
        $batches = WhatsAppBatch::with('creator')
            ->when($request->source_type, fn ($q, $t) => $q->where('source_type', $t))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.whatsapp.batches.index', compact('batches'));
    }

    public function show(WhatsAppBatch $batch, Request $request)
    {
        $batch->load('creator');

        WhatsAppBatchService::kickstartIfStalled($batch);
        $batch->refresh();

        $filter = $request->get('filter', 'all');

        $itemsQuery = $batch->items()->orderBy('sort_order');

        if (in_array($filter, ['sent', 'failed', 'pending'], true)) {
            if ($filter === 'pending') {
                $itemsQuery->whereIn('status', ['pending', 'processing']);
            } else {
                $itemsQuery->where('status', $filter);
            }
        }

        $items = $itemsQuery->paginate(50)->withQueryString();

        $workshop = null;
        $salesGroup = null;
        if ($batch->source_type === 'workshop' && $batch->source_id) {
            $workshop = Workshop::find($batch->source_id);
        }
        if ($batch->source_type === 'sales_group' && $batch->source_id) {
            $salesGroup = \App\Models\SalesLeadGroup::find($batch->source_id);
        }

        $bridgeStatus = app(WhatsAppBridgeService::class)->getStatus();
        $bridgeMeta = app(WhatsAppBridgeService::class)->connectionMeta(
            $bridgeStatus['data'] ?? [],
            (bool) ($bridgeStatus['success'] ?? false)
        );

        return view('admin.whatsapp.batches.show', compact('batch', 'items', 'filter', 'workshop', 'salesGroup', 'bridgeMeta'));
    }

    public function statusJson(WhatsAppBatch $batch)
    {
        WhatsAppBatchService::kickstartIfStalled($batch);
        $batch->refresh();

        return response()->json($this->batchStatusPayload($batch));
    }

    public function process(WhatsAppBatch $batch)
    {
        if ($batch->isFinished()) {
            return response()->json($this->batchStatusPayload($batch));
        }

        if ($batch->status === 'paused' || $batch->isPausedForBridge()) {
            $ready = app(WhatsAppBridgeService::class)->ensureReadyForSend();
            if (! ($ready['success'] ?? false)) {
                return response()->json(array_merge(
                    ['ok' => false, 'bridge_blocked' => true, 'error' => $ready['error'] ?? 'الواتساب غير متصل'],
                    $this->batchStatusPayload($batch)
                ));
            }

            WhatsAppBatchService::resumeIfBridgeReady($batch->fresh());
            $batch->refresh();
        }

        try {
            ProcessWhatsAppBatchJob::dispatchSync($batch->id);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok' => false,
                'error' => 'تعذّر معالجة الدفعة: ' . $e->getMessage(),
            ], 500);
        }

        $batch->refresh();

        return response()->json(array_merge(['ok' => true], $this->batchStatusPayload($batch)));
    }

    /**
     * @return array<string, mixed>
     */
    private function batchStatusPayload(WhatsAppBatch $batch): array
    {
        $recentItems = $batch->items()
            ->whereIn('status', ['sent', 'failed'])
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get(['id', 'recipient_name', 'phone', 'status', 'error_message', 'sent_at', 'updated_at']);

        $bridgePayload = $this->bridgeStatusPayload();

        return [
            'id' => $batch->id,
            'status' => $batch->status,
            'status_label' => $batch->statusLabel(),
            'total' => $batch->total_count,
            'sent' => $batch->sent_count,
            'failed' => $batch->failed_count,
            'pending' => $batch->pendingCount(),
            'progress' => $batch->progressPercent(),
            'finished' => $batch->isFinished(),
            'paused_for_bridge' => $batch->isPausedForBridge(),
            'bridge_blocked_reason' => $batch->meta['bridge_blocked_reason'] ?? null,
            'started_at' => optional($batch->started_at)->toIso8601String(),
            'completed_at' => optional($batch->completed_at)->toIso8601String(),
            'bridge' => $bridgePayload,
            'recent' => $recentItems->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->recipient_name,
                'phone' => $item->phone,
                'status' => $item->status,
                'status_label' => $item->statusLabel(),
                'error' => $item->error_message,
                'sent_at' => optional($item->sent_at)->format('Y-m-d H:i'),
            ]),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function bridgeStatusPayload(): array
    {
        $bridge = app(WhatsAppBridgeService::class);
        $status = $bridge->getStatus();
        $meta = $bridge->connectionMeta($status['data'] ?? [], (bool) ($status['success'] ?? false));

        return [
            'can_send' => (bool) ($meta['can_send'] ?? false),
            'label' => (string) ($meta['label'] ?? 'غير معروف'),
            'last_error' => $meta['last_error'] ?? null,
            'connect_url' => route('admin.whatsapp.index'),
        ];
    }

    public function retry(WhatsAppBatch $batch)
    {
        if ($batch->status === 'cancelled') {
            return back()->with('error', 'هذه الدفعة موقوفة.');
        }

        $failedCount = (int) $batch->items()->where('status', 'failed')->count();
        $pendingCount = $batch->pendingCount();

        if ($failedCount === 0 && $pendingCount === 0) {
            return back()->with('error', 'لا توجد رسائل فاشلة أو معلّقة لإعادة الإرسال.');
        }

        try {
            $result = $this->batchService->retryBatch($batch);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($result['failed_reset'] > 0) {
            return back()->with(
                'success',
                'تمت إعادة جدولة ' . $result['failed_reset'] . ' رسالة فاشلة للإرسال — الرسائل المرسلة مسبقاً لن تُعاد.'
            );
        }

        return back()->with('success', 'تم إعادة تشغيل الإرسال للرسائل المعلّقة — انتظر بضع ثوانٍ.');
    }

    public function retryItem(WhatsAppBatch $batch, WhatsAppBatchItem $item)
    {
        if ($batch->status === 'cancelled') {
            return back()->with('error', 'هذه الدفعة موقوفة.');
        }

        try {
            $this->batchService->retryItem($batch, $item);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $label = $item->recipient_name ?: $item->phone;

        return back()->with('success', 'تمت إعادة جدولة رسالة «' . $label . '» للإرسال — باقي الرسائل لم تُمس.');
    }

    public function cancel(WhatsAppBatch $batch)
    {
        if ($batch->isFinished()) {
            return back()->with('error', 'الدفعة منتهية بالفعل — لا يمكن إيقافها.');
        }

        $stopped = $this->batchService->cancelBatch($batch);

        if ($stopped === 0 && $batch->fresh()->status !== 'cancelled') {
            return back()->with('error', 'تعذّر إيقاف الدفعة.');
        }

        return back()->with('success', 'تم إيقاف الإرسال — ' . $stopped . ' رسالة متبقية لن تُرسل.');
    }
}
