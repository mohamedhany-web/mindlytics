<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesLeadGroup;
use App\Models\WhatsAppBatch;
use App\Services\SalesLeadWhatsAppBatchService;
use App\Services\WhatsAppBatchService;
use App\Support\WhatsAppCloudSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesGroupWhatsAppController extends Controller
{
    public function __construct(
        private SalesLeadWhatsAppBatchService $bulkService,
    ) {}

    public function store(Request $request, SalesLeadGroup $group): RedirectResponse
    {
        $this->authorizeGroup($group);

        $validated = $request->validate([
            'message' => 'required|string|max:4096',
        ], [
            'message.required' => 'نص الرسالة مطلوب',
        ]);

        if (! WhatsAppCloudSettings::usesOfficial()) {
            return back()->with('error', 'إرسال الواتساب غير مفعّل — تواصل مع الإدارة لإكمال إعداد Meta Cloud API.');
        }

        try {
            $batch = $this->bulkService->dispatch(
                $group,
                $validated['message'],
                (int) Auth::id(),
                assigneeId: (int) Auth::id()
            );
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('employee.sales.groups.whatsapp-batches.show', [$group, $batch])
            ->with('success', 'تم بدء إرسال ' . $batch->total_count . ' رسالة — تابع التقدّم أدناه.');
    }

    public function showBatch(SalesLeadGroup $group, WhatsAppBatch $batch): View
    {
        $this->authorizeGroup($group);
        $this->authorizeBatch($group, $batch);

        WhatsAppBatchService::kickstartIfStalled($batch);
        $batch->refresh()->load('creator');

        $items = $batch->items()->orderBy('sort_order')->paginate(50);

        return view('employee.sales.groups.whatsapp-batch', compact('group', 'batch', 'items'));
    }

    public function statusJson(SalesLeadGroup $group, WhatsAppBatch $batch)
    {
        $this->authorizeGroup($group);
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

    private function authorizeGroup(SalesLeadGroup $group): void
    {
        abort_unless($group->userHasAccess((int) Auth::id()), 403);
    }

    private function authorizeBatch(SalesLeadGroup $group, WhatsAppBatch $batch): void
    {
        abort_unless(
            $batch->source_type === 'sales_group'
            && (int) $batch->source_id === (int) $group->id
            && (int) $batch->created_by === (int) Auth::id(),
            404
        );
    }
}
