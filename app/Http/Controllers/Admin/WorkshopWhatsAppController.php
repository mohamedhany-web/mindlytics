<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Services\WorkshopWhatsAppBatchService;
use App\Support\WhatsAppBridgeSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkshopWhatsAppController extends Controller
{
    public function __construct(
        private WorkshopWhatsAppBatchService $bulkService,
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

        if (! WhatsAppBridgeSettings::usesBridge()) {
            return back()->with('error', 'إرسال الواتساب التلقائي غير مفعّل — راجع إعدادات Bridge في قسم الواتساب، أو استخدم «فتح روابط يدوياً».');
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
            ->with('success', 'تم بدء إرسال ' . $batch->total_count . ' رسالة واتساب — تابع التقدّم من هذه الصفحة. يُرسل تدريجياً لتقليل خطر الحظر.');
    }
}
