<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppMetaTemplate;
use App\Services\WhatsAppCloudService;
use App\Services\WhatsAppTemplateAccessService;
use App\Services\WhatsAppTemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppTemplateController extends Controller
{
    public function index(Request $request, WhatsAppTemplateAccessService $access): View
    {
        $query = WhatsAppMetaTemplate::query()->with('creator:id,name')->latest();

        if ($access->isRestricted()) {
            $query->withCount('assignedUsers');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', strtoupper((string) $request->category));
        }

        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('body_text', 'like', "%{$s}%");
            });
        }

        $templates = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => WhatsAppMetaTemplate::count(),
            'approved' => WhatsAppMetaTemplate::where('status', 'approved')->count(),
            'pending' => WhatsAppMetaTemplate::where('status', 'pending')->count(),
            'rejected' => WhatsAppMetaTemplate::where('status', 'rejected')->count(),
            'draft' => WhatsAppMetaTemplate::where('status', 'draft')->count(),
        ];

        $connectionMeta = app(WhatsAppCloudService::class)->connectionMeta();

        return view('admin.whatsapp.templates.index', compact(
            'templates',
            'stats',
            'connectionMeta',
        ) + [
            'templateAccessMode' => $access->mode(),
            'templateAccessLabels' => $access->modeLabels(),
            'salesStaff' => $access->salesStaffForAssignment(),
        ]);
    }

    public function create(): View
    {
        return view('admin.whatsapp.templates.create', [
            'connectionMeta' => app(WhatsAppCloudService::class)->connectionMeta(),
        ]);
    }

    public function store(Request $request, WhatsAppTemplateService $service): RedirectResponse
    {
        $validated = $service->validateDraftFromRequest($request);

        try {
            $template = $service->createDraft($validated, auth()->id());
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['name' => $e->getMessage()]);
        }

        if ($request->boolean('submit_now')) {
            $submit = $service->submitToMeta($template);
            if (! ($submit['success'] ?? false)) {
                return redirect()
                    ->route('admin.whatsapp.templates.show', $template)
                    ->with('error', $submit['error'] ?? 'فشل الإرسال إلى Meta — القالب محفوظ كمسودة.');
            }

            return redirect()
                ->route('admin.whatsapp.templates.show', $template)
                ->with('success', 'تم إرسال القالب إلى Meta للمراجعة.');
        }

        return redirect()
            ->route('admin.whatsapp.templates.show', $template)
            ->with('success', 'تم حفظ القالب كمسودة.');
    }

    public function show(WhatsAppMetaTemplate $template, WhatsAppTemplateAccessService $access): View
    {
        $template->load(['creator:id,name', 'assignedUsers:id,name,email']);

        return view('admin.whatsapp.templates.show', [
            'template' => $template,
            'connectionMeta' => app(WhatsAppCloudService::class)->connectionMeta(),
            'templateAccessMode' => $access->mode(),
            'salesStaff' => $access->salesStaffForAssignment(),
        ]);
    }

    public function updateAccessMode(Request $request, WhatsAppTemplateAccessService $access): RedirectResponse
    {
        $validated = $request->validate([
            'template_access_mode' => 'required|in:all,restricted',
        ]);

        $access->setMode($validated['template_access_mode']);

        $label = $access->modeLabels()[$validated['template_access_mode']] ?? $validated['template_access_mode'];

        return back()->with('success', 'تم تحديث صلاحيات القوالب: '.$label);
    }

    public function updateAccess(Request $request, WhatsAppMetaTemplate $template, WhatsAppTemplateAccessService $access): RedirectResponse
    {
        if (! $access->isRestricted()) {
            return back()->with('error', 'فعّل وضع «قوالب محددة لكل موظف» أولاً من صفحة القوالب.');
        }

        $validated = $request->validate([
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $access->syncTemplateAssignments($template, $validated['user_ids'] ?? []);

        return back()->with('success', 'تم حفظ الموظفين المصرّح لهم باستخدام هذا القالب.');
    }

    public function edit(WhatsAppMetaTemplate $template): View|RedirectResponse
    {
        if (! $template->isEditable()) {
            return redirect()
                ->route('admin.whatsapp.templates.show', $template)
                ->with('error', 'لا يمكن تعديل قالب بحالة: ' . $template->statusLabel());
        }

        return view('admin.whatsapp.templates.edit', [
            'template' => $template,
            'connectionMeta' => app(WhatsAppCloudService::class)->connectionMeta(),
        ]);
    }

    public function update(Request $request, WhatsAppMetaTemplate $template, WhatsAppTemplateService $service): RedirectResponse
    {
        $validated = $service->validateDraftFromRequest($request);

        try {
            $service->updateDraft($template, $validated);
        } catch (\RuntimeException|\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        if ($request->boolean('submit_now')) {
            $submit = $service->submitToMeta($template->fresh());
            if (! ($submit['success'] ?? false)) {
                return back()->with('error', $submit['error'] ?? 'فشل الإرسال إلى Meta.');
            }

            return redirect()
                ->route('admin.whatsapp.templates.show', $template)
                ->with('success', 'تم تحديث القالب وإرساله إلى Meta للمراجعة.');
        }

        return redirect()
            ->route('admin.whatsapp.templates.show', $template)
            ->with('success', 'تم تحديث القالب.');
    }

    public function submit(WhatsAppMetaTemplate $template, WhatsAppTemplateService $service): RedirectResponse
    {
        $result = $service->submitToMeta($template);

        if (! ($result['success'] ?? false)) {
            return back()->with('error', $result['error'] ?? 'فشل الإرسال إلى Meta.');
        }

        return back()->with('success', 'تم إرسال القالب إلى Meta — الحالة: قيد المراجعة.');
    }

    public function sync(WhatsAppTemplateService $service): RedirectResponse
    {
        $result = $service->syncFromMeta();

        if (! ($result['success'] ?? false)) {
            return back()->with('error', $result['error'] ?? 'فشلت المزامنة مع Meta.');
        }

        return back()->with('success', 'تمت مزامنة ' . ($result['synced'] ?? 0) . ' قالباً من Meta.');
    }

    public function destroy(WhatsAppMetaTemplate $template, WhatsAppTemplateService $service): RedirectResponse
    {
        $deleteFromMeta = request()->boolean('delete_from_meta', true);
        $result = $service->deleteTemplate($template, $deleteFromMeta);

        if (! ($result['success'] ?? false)) {
            return back()->with('error', $result['error'] ?? 'فشل حذف القالب.');
        }

        return redirect()
            ->route('admin.whatsapp.templates.index')
            ->with('success', 'تم حذف القالب.');
    }

}
