<?php

namespace App\Domains\Sales\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\Sales\Models\SalesLeadGroup;
use App\Domains\Messaging\Models\WhatsAppBatch;
use App\Domains\Sales\Services\SalesLeadWhatsAppBatchService;
use App\Domains\Messaging\Services\WhatsAppBatchService;
use App\Domains\Messaging\Support\WhatsAppCloudSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesGroupWhatsAppController extends Controller
{
    public function __construct(
        private SalesLeadWhatsAppBatchService $bulkService,
    ) {}

    public function store(Request $request, SalesLeadGroup $group): RedirectResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:4096',
        ], [
            'message.required' => 'نص الرسالة مطلوب',
        ]);

        if (! WhatsAppCloudSettings::usesOfficial()) {
            return back()->with('error', 'إرسال الواتساب غير مفعّل — أكمل إعداد Meta Cloud API في قسم الواتساب.');
        }

        try {
            $batch = $this->bulkService->dispatch(
                $group,
                $validated['message'],
                (int) auth()->id(),
                assigneeId: null
            );
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.whatsapp.batches.show', $batch)
            ->with('success', 'تم بدء إرسال ' . $batch->total_count . ' رسالة في الخلفية — تابع التقدّم من هذه الصفحة.');
    }

    public function showBatch(SalesLeadGroup $group, WhatsAppBatch $batch): View
    {
        $this->authorizeBatch($group, $batch);

        WhatsAppBatchService::kickstartIfStalled($batch);
        $batch->refresh()->load('creator');

        return view('sales::admin.groups.whatsapp-batch', compact('group', 'batch'));
    }

    public function statusJson(SalesLeadGroup $group, WhatsAppBatch $batch)
    {
        $this->authorizeBatch($group, $batch);

        WhatsAppBatchService::kickstartIfStalled($batch);
        $batch->refresh();

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
        ]);
    }

    private function authorizeBatch(SalesLeadGroup $group, WhatsAppBatch $batch): void
    {
        abort_unless(
            $batch->source_type === 'sales_group' && (int) $batch->source_id === (int) $group->id,
            404
        );
    }
}
