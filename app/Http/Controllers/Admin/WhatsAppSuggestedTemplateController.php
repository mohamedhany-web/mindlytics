<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppSuggestedTemplate;
use App\Services\WhatsAppSuggestedTemplateService;
use App\Services\WhatsAppTemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppSuggestedTemplateController extends Controller
{
    public function edit(WhatsAppSuggestedTemplate $suggestedTemplate): View
    {
        $suggestedTemplate->load('metaTemplate');

        return view('admin.whatsapp.templates.suggested-edit', [
            'suggested' => $suggestedTemplate,
        ]);
    }

    public function update(Request $request, WhatsAppSuggestedTemplate $suggestedTemplate, WhatsAppSuggestedTemplateService $service): RedirectResponse
    {
        $validated = $service->validateFromRequest($request->all());

        if ($validated['title'] === '' || $validated['body'] === '') {
            return back()->withInput()->with('error', 'العنوان ونص الرسالة مطلوبان.');
        }

        $suggestedTemplate->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->boolean('sync_meta_draft') && $suggestedTemplate->metaTemplate?->isEditable()) {
            try {
                $service->createOrUpdateMetaDraft($suggestedTemplate->fresh(), auth()->id());
            } catch (\Throwable $e) {
                return back()->withInput()->with('error', 'تم حفظ القالب لكن فشل تحديث مسودة Meta: '.$e->getMessage());
            }
        }

        return redirect()
            ->route('admin.whatsapp.templates.suggested.edit', $suggestedTemplate)
            ->with('success', 'تم حفظ القالب المقترح.');
    }

    public function createMetaDraft(WhatsAppSuggestedTemplate $suggestedTemplate, WhatsAppSuggestedTemplateService $service): RedirectResponse
    {
        try {
            $draft = $service->createOrUpdateMetaDraft($suggestedTemplate, auth()->id());
        } catch (\Throwable $e) {
            return back()->with('error', 'تعذّر إنشاء مسودة Meta: '.$e->getMessage());
        }

        return redirect()
            ->route('admin.whatsapp.templates.edit', $draft)
            ->with('success', 'تم إنشاء/تحديث مسودة Meta — راجعها ثم أرسلها للاعتماد.');
    }

    public function submitMeta(WhatsAppSuggestedTemplate $suggestedTemplate, WhatsAppSuggestedTemplateService $suggestedService, WhatsAppTemplateService $templateService): RedirectResponse
    {
        try {
            $draft = $suggestedTemplate->metaTemplate && $suggestedTemplate->metaTemplate->isEditable()
                ? $suggestedTemplate->metaTemplate
                : $suggestedService->createOrUpdateMetaDraft($suggestedTemplate, auth()->id());
        } catch (\Throwable $e) {
            return back()->with('error', 'تعذّر تجهيز مسودة Meta: '.$e->getMessage());
        }

        $result = $templateService->submitToMeta($draft->fresh());

        if (! ($result['success'] ?? false)) {
            return redirect()
                ->route('admin.whatsapp.templates.edit', $draft)
                ->with('error', $result['error'] ?? 'فشل الإرسال إلى Meta.');
        }

        return redirect()
            ->route('admin.whatsapp.templates.show', $draft)
            ->with('success', 'تم إرسال القالب إلى Meta للمراجعة.');
    }
}
