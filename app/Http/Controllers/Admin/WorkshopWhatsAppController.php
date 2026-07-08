<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Services\WhatsAppCloudService;
use App\Services\WhatsAppTemplateService;
use App\Services\WorkshopWhatsAppBatchService;
use App\Services\WorkshopWhatsAppTemplateService;
use App\Support\WhatsAppCloudSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkshopWhatsAppController extends Controller
{
    public function __construct(
        private WorkshopWhatsAppBatchService $bulkService,
        private WorkshopWhatsAppTemplateService $templateService,
        private WhatsAppTemplateService $metaTemplates,
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

    public function createTemplateForm(Workshop $workshop): View|RedirectResponse
    {
        $linked = $this->templateService->linkedTemplate($workshop);
        if ($linked && ! $linked->isEditable()) {
            return redirect()
                ->route('admin.workshops.show', $workshop)
                ->with('info', 'قالب الورشة بحالة «'.$linked->statusLabel().'» — لا يمكن تعديله. يمكنك المزامنة أو الإرسال من صفحة الورشة.');
        }

        $defaultButtons = $this->templateService->defaultWelcomeButtons($workshop);

        return view('admin.whatsapp.templates.workshop-create', [
            'workshop' => $workshop,
            'template' => $linked,
            'connectionMeta' => app(WhatsAppCloudService::class)->connectionMeta(),
            'workshopVariableLabels' => $this->templateService->workshopVariableLabels(),
            'defaultTemplateName' => $this->templateService->templateNameFor($workshop),
            'defaultBody' => old('body_text', $linked?->body_text ?: $this->templateService->defaultWelcomeBody()),
            'defaultButtons' => old('buttons', $linked?->buttons ?: $defaultButtons),
        ]);
    }

    public function createTemplate(Request $request, Workshop $workshop): RedirectResponse
    {
        if (! WhatsAppCloudSettings::usesOfficial()) {
            return back()->with('error', 'إرسال الواتساب غير مفعّل — أكمل إعداد Meta Cloud API.');
        }

        $validated = $this->metaTemplates->validateDraftFromRequest($request);

        $result = $this->templateService->createAndSubmitWelcomeTemplate(
            $workshop,
            array_merge($validated, ['submit_now' => $request->boolean('submit_now')]),
            (int) auth()->id()
        );

        if (! ($result['success'] ?? false)) {
            return back()->withInput()->with('error', $result['error'] ?? 'فشل إنشاء القالب');
        }

        return redirect()
            ->route('admin.workshops.show', $workshop)
            ->with('success', $result['message'] ?? 'تم إنشاء قالب الترحيب.');
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
        if (is_array($request->input('group_invite_code'))) {
            $request->merge([
                'group_invite_code' => $this->metaTemplates->scalarToString($request->input('group_invite_code')),
            ]);
        }

        $validated = $request->validate([
            'scope' => 'required|in:all,online,offline,phone',
            'phone' => 'nullable|string|max:30',
            'template_name' => 'required|string|max:512',
            'template_language' => 'required|string|max:20',
            'group_invite_code' => 'nullable|string|max:200',
        ]);

        if ($validated['scope'] === 'phone' && empty(trim((string) ($validated['phone'] ?? '')))) {
            return back()->with('error', 'يرجى إدخال رقم الهاتف.');
        }

        if (! WhatsAppCloudSettings::usesOfficial()) {
            return back()->with('error', 'إرسال الواتساب غير مفعّل — أكمل إعداد Meta Cloud API.');
        }

        $template = $this->templateService->resolveSendableTemplate(
            $validated['template_name'],
            $validated['template_language']
        );

        if (! $template) {
            return back()->with('error', 'القالب المختار غير معتمد أو غير موجود.');
        }

        $inviteOverride = trim((string) ($validated['group_invite_code'] ?? ''));
        if ($missing = $this->templateService->missingGroupInviteForSend($workshop, $template, $inviteOverride ?: null)) {
            return back()
                ->with('error', $missing)
                ->with('show_group_invite_modal', true);
        }

        $variableOverrides = $this->templateService->buildSendVariableOverrides(
            $workshop,
            $template,
            $inviteOverride !== '' ? $inviteOverride : null
        );

        try {
            $sample = $this->bulkService->registrationsForWorkshop($workshop, $validated['scope'], $validated['phone'] ?? null)->first();
            if ($sample) {
                $this->templateService->validateTemplateForRegistration($workshop, $template, $sample, $variableOverrides);
            }

            $batch = $this->templateService->dispatchTemplate(
                $workshop,
                $template,
                (int) auth()->id(),
                $validated['scope'],
                $validated['phone'] ?? null,
                $variableOverrides
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.whatsapp.batches.show', $batch)
            ->with('success', 'تم بدء إرسال القالب إلى '.$batch->total_count.' مسجّل.');
    }
}
