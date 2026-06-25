<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppBatch;
use App\Models\Workshop;
use App\Services\WhatsAppBatchService;
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

        return view('admin.whatsapp.batches.show', compact('batch', 'items', 'filter', 'workshop', 'salesGroup'));
    }

    public function statusJson(WhatsAppBatch $batch)
    {
        WhatsAppBatchService::kickstartIfStalled($batch);
        $batch->refresh();

        $recentItems = $batch->items()
            ->whereIn('status', ['sent', 'failed'])
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get(['id', 'recipient_name', 'phone', 'status', 'error_message', 'sent_at', 'updated_at']);

        return response()->json([
            'id' => $batch->id,
            'status' => $batch->status,
            'status_label' => $batch->statusLabel(),
            'total' => $batch->total_count,
            'sent' => $batch->sent_count,
            'failed' => $batch->failed_count,
            'pending' => $batch->pendingCount(),
            'progress' => $batch->progressPercent(),
            'finished' => $batch->isFinished(),
            'started_at' => optional($batch->started_at)->toIso8601String(),
            'completed_at' => optional($batch->completed_at)->toIso8601String(),
            'recent' => $recentItems->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->recipient_name,
                'phone' => $item->phone,
                'status' => $item->status,
                'status_label' => $item->statusLabel(),
                'error' => $item->error_message,
                'sent_at' => optional($item->sent_at)->format('Y-m-d H:i'),
            ]),
        ]);
    }

    public function retry(WhatsAppBatch $batch)
    {
        if ($batch->pendingCount() === 0 && $batch->isFinished()) {
            return back()->with('error', 'لا توجد رسائل معلّقة في هذه الدفعة.');
        }

        $this->batchService->retryBatch($batch);

        return back()->with('success', 'تم إعادة تشغيل الإرسال — انتظر بضع ثوانٍ ثم حدّث الصفحة.');
    }
}
