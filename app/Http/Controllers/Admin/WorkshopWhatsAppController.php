<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Services\WorkshopWhatsAppBatchService;
use App\Services\WorkshopWhatsAppTemplateService;
use App\Support\WhatsAppCloudSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkshopWhatsAppController extends Controller
{
    public function __construct(
        private WorkshopWhatsAppBatchService $bulkService,
        private WorkshopWhatsAppTemplateService $templateService,
    ) {}

    public function store(Request $request, Workshop $workshop): RedirectResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:4096',
            'scope' => 'required|in:all,online,offline,phone',
            'phone' => 'nullable|string|max:30',
        ], [
            'message.required' => 'نص الرسالة مطلوب',
        ]);

        if ($validated['scope'] === 'phone' && empty(trim((string) ($validated['phone'] ?? '')))) {
            return back()->withInput()->with('error', 'يرجى إدخال رقم الهاتف عند اختيار «رقم محدد».');
        }

        if (! WhatsAppCloudSettings::usesOfficial()) {
            return back()->with('error', 'إرسال الواتساب غير مفعّل — أكمل إعداد Meta Cloud API في قسم الواتساب.');
        }

        try {
            $batch = $this->bulkService->dispatch(
                $workshop,
                $validated['message'],
                (int) auth()->id(),
                $validated['scope'],
                $validated['phone'] ?? null
            );
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.whatsapp.batches.show', $batch)
            ->with('success', 'تم بدء إرسال '.$batch->total_count.' رسالة واتساب — تابع التقدّم من هذه الصفحة.');
    }

    public function createTemplate(Request $request, Workshop $workshop): RedirectResponse
    {
        $validated = $request->validate([
            'body_text' => 'nullable|string|max:1024',
        ]);

        if (! WhatsAppCloudSettings::usesOfficial()) {
            return back()->with('error', 'إرسال الواتساب غير مفعّل — أكمل إعداد Meta Cloud API.');
        }

        $result = $this->templateService->createAndSubmitWelcomeTemplate(
            $workshop,
            $validated['body_text'] ?? null,
            (int) auth()->id()
        );

        if (! ($result['success'] ?? false)) {
            return back()->withInput()->with('error', $result['error'] ?? 'فشل إنشاء القالب');
        }

        return back()->with('success', $result['message'] ?? 'تم إنشاء قالب الترحيب.');
    }

    public function syncTemplate(Workshop $workshop): RedirectResponse
    {
        $result = $this->templateService->syncWelcomeTemplate($workshop);

        if (! ($result['success'] ?? false)) {
            return back()->with('error', $result['error'] ?? 'فشل المزامنة');
        }

        $template = $result['template'];
        $status = $template?->statusLabel() ?? '—';

        return back()->with('success', 'تمت مزامنة حالة القالب مع Meta: '.$status);
    }

    public function sendTemplate(Request $request, Workshop $workshop): RedirectResponse
    {
        $validated = $request->validate([
            'scope' => 'required|in:all,online,offline,phone',
            'phone' => 'nullable|string|max:30',
        ]);

        if ($validated['scope'] === 'phone' && empty(trim((string) ($validated['phone'] ?? '')))) {
            return back()->with('error', 'يرجى إدخال رقم الهاتف.');
        }

        if (! WhatsAppCloudSettings::usesOfficial()) {
            return back()->with('error', 'إرسال الواتساب غير مفعّل — أكمل إعداد Meta Cloud API.');
        }

        try {
            $batch = $this->templateService->dispatchWelcomeTemplate(
                $workshop,
                (int) auth()->id(),
                $validated['scope'],
                $validated['phone'] ?? null
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.whatsapp.batches.show', $batch)
            ->with('success', 'تم بدء إرسال قالب الترحيب إلى '.$batch->total_count.' مسجّل — يظهر في سجل الدفعات.');
    }
}
