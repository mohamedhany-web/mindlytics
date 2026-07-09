<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppMetaTemplate;
use App\Models\WhatsAppSuggestedTemplate;
use App\Models\Workshop;
use Database\Seeders\WhatsAppSuggestedTemplatesSeeder;
use App\Services\WhatsAppCloudService;
use App\Services\WhatsAppTemplateAccessService;
use App\Services\WhatsAppTemplateService;
use App\Services\WorkshopWhatsAppTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class WhatsAppTemplateController extends Controller
{
    public function index(Request $request, WhatsAppTemplateAccessService $access): View
    {
        $query = WhatsAppMetaTemplate::query()->with('creator:id,name')->latest();

        if ($access->isRestricted() && $access->hasAssignmentTable()) {
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

        $activeTab = in_array($request->query('tab'), ['meta', 'suggested'], true)
            ? (string) $request->query('tab')
            : 'meta';

        $suggestedReady = Schema::hasTable('whatsapp_suggested_templates');
        $suggestedTemplates = collect();
        $suggestedStats = ['total' => 0, 'ar' => 0, 'en' => 0];

        if ($suggestedReady) {
            $suggestedQuery = WhatsAppSuggestedTemplate::query()
                ->with('metaTemplate:id,name,language,status')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id');

            if ($request->filled('suggested_category')) {
                $suggestedQuery->where('category', (string) $request->suggested_category);
            }

            if ($request->filled('suggested_language')) {
                $suggestedQuery->where('language', (string) $request->suggested_language);
            }

            if ($request->filled('suggested_search')) {
                $s = trim((string) $request->suggested_search);
                $suggestedQuery->where(function ($q) use ($s) {
                    $q->where('title', 'like', "%{$s}%")
                        ->orWhere('body', 'like', "%{$s}%")
                        ->orWhere('help', 'like', "%{$s}%");
                });
            }

            $suggestedTemplates = $suggestedQuery->get();
            $suggestedStats = [
                'total' => WhatsAppSuggestedTemplate::where('is_active', true)->count(),
                'ar' => WhatsAppSuggestedTemplate::where('is_active', true)->where('language', 'ar')->count(),
                'en' => WhatsAppSuggestedTemplate::where('is_active', true)->where('language', 'en')->count(),
            ];
        }

        if ($activeTab === 'meta' && ($stats['total'] ?? 0) === 0 && ($suggestedStats['total'] ?? 0) > 0) {
            $activeTab = 'suggested';
        }

        return view('admin.whatsapp.templates.index', compact(
            'templates',
            'stats',
            'connectionMeta',
            'activeTab',
            'suggestedReady',
            'suggestedTemplates',
            'suggestedStats',
        ) + [
            'templateAccessMode' => $access->mode(),
            'templateAccessLabels' => $access->modeLabels(),
            'salesStaff' => $access->salesStaffForAssignment(),
        ]);
    }

    public function seedSuggestedLibrary(): RedirectResponse
    {
        if (! Schema::hasTable('whatsapp_suggested_templates')) {
            return back()->with('error', 'جدول المكتبة غير موجود — نفّذ php artisan migrate أولاً.');
        }

        Artisan::call('db:seed', [
            '--class' => WhatsAppSuggestedTemplatesSeeder::class,
            '--force' => true,
        ]);

        $count = WhatsAppSuggestedTemplate::where('is_active', true)->count();

        return redirect()
            ->route('admin.whatsapp.templates.index', ['tab' => 'suggested'])
            ->with('success', "تم تحميل مكتبة القوالب المقترحة ({$count} قالباً).");
    }

    public function create(): View
    {
        $workshops = collect();
        if (Schema::hasTable('workshops')) {
            $columns = ['id', 'title'];
            if (Schema::hasColumn('workshops', 'whatsapp_group_link')) {
                $columns[] = 'whatsapp_group_link';
            }
            if (Schema::hasColumn('workshops', 'starts_at')) {
                $columns[] = 'starts_at';
            }

            $workshops = Workshop::query()
                ->orderByDesc('starts_at')
                ->orderByDesc('id')
                ->get($columns);
        }

        return view('admin.whatsapp.templates.create', [
            'connectionMeta' => app(WhatsAppCloudService::class)->connectionMeta(),
            'workshops' => $workshops,
            'workshopVariableLabels' => app(WorkshopWhatsAppTemplateService::class)->workshopVariableLabels(),
            'initialWorkshopId' => (int) request('workshop_id', 0) ?: null,
        ]);
    }

    public function workshopPreset(Workshop $workshop, WorkshopWhatsAppTemplateService $workshopTemplates): JsonResponse
    {
        return response()->json($workshopTemplates->formPreset($workshop));
    }

    public function store(Request $request, WhatsAppTemplateService $service, WorkshopWhatsAppTemplateService $workshopTemplates): RedirectResponse
    {
        $validated = $service->validateDraftFromRequest($request);

        $workshop = null;
        $workshopId = (int) $request->input('workshop_id');
        if ($workshopId > 0 && Schema::hasTable('workshops')) {
            $workshop = Workshop::query()->find($workshopId);
        }

        if ($request->input('template_mode') === 'workshop' && ! $workshop) {
            return back()->withInput()->withErrors(['workshop_id' => 'اختر ورشة عند إنشاء قالب مرتبط بورشة.']);
        }

        if ($workshop) {
            $validated['name'] = $workshopTemplates->templateNameFor($workshop);
            $validated['body_text'] = $workshopTemplates->normalizeBodyForMeta((string) ($validated['body_text'] ?? ''));
            $validated['buttons'] = $workshopTemplates->enrichButtonExamplesForWorkshop($workshop, $validated['buttons'] ?? []);
            $validated['language'] = $validated['language'] ?? 'ar_EG';
            $groupLink = trim((string) $workshop->whatsapp_group_link);
            if ($groupLink !== '') {
                $validated['group_invite_example'] = $workshopTemplates->groupLinkDynamicPart($groupLink);
            }
        }

        try {
            $template = $service->createDraft($validated, auth()->id());
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['name' => $e->getMessage()]);
        }

        if ($workshop && Schema::hasColumn('workshops', 'welcome_meta_template_id')) {
            $workshopTemplates->linkTemplateToWorkshop($workshop, $template);
        }

        $linkedMsg = $workshop ? ' وتم ربطه بورشة «'.$workshop->title.'».' : '';

        if ($request->boolean('submit_now')) {
            $submit = $service->submitToMeta($template);
            if (! ($submit['success'] ?? false)) {
                return redirect()
                    ->route('admin.whatsapp.templates.show', $template)
                    ->with('error', $submit['error'] ?? 'فشل الإرسال إلى Meta — القالب محفوظ كمسودة.');
            }

            return redirect()
                ->route('admin.whatsapp.templates.show', $template)
                ->with('success', 'تم إرسال القالب إلى Meta للمراجعة'.$linkedMsg);
        }

        return redirect()
            ->route('admin.whatsapp.templates.show', $template)
            ->with('success', 'تم حفظ القالب كمسودة'.$linkedMsg);
    }

    public function show(WhatsAppMetaTemplate $template, WhatsAppTemplateAccessService $access): View
    {
        $template->load(['creator:id,name']);

        if ($access->hasAssignmentTable()) {
            $template->load(['assignedUsers:id,name,email']);
        } else {
            $template->setRelation('assignedUsers', collect());
        }

        return view('admin.whatsapp.templates.show', [
            'template' => $template,
            'templateAccessMode' => $access->hasAssignmentTable() ? $access->mode() : WhatsAppTemplateAccessService::MODE_ALL,
            'salesStaff' => $access->hasAssignmentTable() ? $access->salesStaffForAssignment() : collect(),
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
