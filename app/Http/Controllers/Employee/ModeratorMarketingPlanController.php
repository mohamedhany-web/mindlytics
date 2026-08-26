<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\DesignTaskCycle;
use App\Models\EmployeeJob;
use App\Models\ModeratorMarketingCalendarEvent;
use App\Models\ModeratorMarketingPlan;
use App\Models\ModeratorMarketingPlatform;
use App\Services\MarketingPlanEventAutomationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModeratorMarketingPlanController extends Controller
{
    public function __construct(
        protected MarketingPlanEventAutomationService $marketingAutomation
    ) {}

    private function assertOwnPlan(ModeratorMarketingPlan $plan): void
    {
        $user = Auth::user();
        abort_unless($user && $user->canManageModeratorResource((int) $plan->moderator_id), 403);
    }

    private function planModeratorId(ModeratorMarketingPlan $plan): int
    {
        $user = Auth::user();
        if ($user?->isAdmin() || $user?->isBusinessDeveloper()) {
            return (int) $plan->moderator_id;
        }

        return (int) $user->id;
    }

    public function index()
    {
        $user = Auth::user();
        $moderatorId = Auth::id();

        $plansQuery = ModeratorMarketingPlan::query()
            ->when(! $user->isBusinessDeveloper(), fn ($q) => $q->where('moderator_id', $moderatorId))
            ->withCount(['platforms', 'calendarEvents'])
            ->when($user->isBusinessDeveloper(), fn ($q) => $q->with('moderator:id,name'))
            ->latest();

        $plans = $plansQuery->paginate(15);

        $statsQuery = ModeratorMarketingPlan::query()
            ->when(! $user->isBusinessDeveloper(), fn ($q) => $q->where('moderator_id', $moderatorId));

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'active' => (clone $statsQuery)->where('status', 'active')->count(),
            'platforms' => ModeratorMarketingPlatform::query()->when(! $user->isBusinessDeveloper(), function ($q) use ($moderatorId) {
                $q->whereHas('plan', fn ($plan) => $plan->where('moderator_id', $moderatorId));
            })->count(),
            'events' => \App\Models\ModeratorMarketingCalendarEvent::query()->when(! $user->isBusinessDeveloper(), function ($q) use ($moderatorId) {
                $q->whereHas('plan', fn ($plan) => $plan->where('moderator_id', $moderatorId));
            })->count(),
        ];

        $isBusinessDeveloper = $user->isBusinessDeveloper();

        return view('employee.marketing-plans.index', compact('plans', 'stats', 'isBusinessDeveloper'));
    }

    public function create()
    {
        $cycles = DesignTaskCycle::query()
            ->when(! Auth::user()->isBusinessDeveloper(), fn ($q) => $q->where('moderator_id', Auth::id()))
            ->orderByDesc('id')
            ->limit(100)
            ->get(['id', 'title', 'status']);

        return view('employee.marketing-plans.create', compact('cycles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:10000'],
            'goals' => ['nullable', 'string', 'max:20000'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:draft,active,paused,completed'],
            'design_task_cycle_id' => ['nullable', 'exists:design_task_cycles,id'],
        ]);

        if (! empty($validated['design_task_cycle_id'])) {
            $cycleQuery = DesignTaskCycle::query()->where('id', $validated['design_task_cycle_id']);
            if (! Auth::user()->isBusinessDeveloper()) {
                $cycleQuery->where('moderator_id', Auth::id());
            }
            if (! $cycleQuery->exists()) {
                return back()->withErrors(['design_task_cycle_id' => 'دورة التصميم غير صالحة.'])->withInput();
            }
        }

        $validated['moderator_id'] = Auth::id();
        $plan = ModeratorMarketingPlan::create($validated);

        return redirect()
            ->route('employee.marketing-plans.show', $plan)
            ->with('success', 'تم إنشاء خطة التسويق.');
    }

    public function show(ModeratorMarketingPlan $marketing_plan)
    {
        $this->assertOwnPlan($marketing_plan);
        $plan = $marketing_plan->load([
            'moderator:id,name',
            'platforms.employeeJobs',
            'calendarEvents' => fn ($q) => $q->with(['platform', 'assignee', 'employeeTask'])->orderBy('starts_at'),
            'designTaskCycle',
        ]);

        $cycles = DesignTaskCycle::query()
            ->where('moderator_id', $this->planModeratorId($marketing_plan))
            ->orderByDesc('id')
            ->limit(100)
            ->get(['id', 'title', 'status']);

        $platformLabels = ModeratorMarketingPlatform::platformLabels();
        $employeeJobs = EmployeeJob::active()->orderBy('name')->get();
        $contentTypes = MarketingPlanEventAutomationService::contentTypeLabels();
        $isBusinessDeveloper = Auth::user()?->isBusinessDeveloper() ?? false;

        $executionStats = [
            'total' => $plan->calendarEvents->count(),
            'published' => $plan->calendarEvents->where('status', 'published')->count(),
            'scheduled' => $plan->calendarEvents->where('status', 'scheduled')->count(),
            'overdue_confirm' => $plan->calendarEvents
                ->filter(fn ($e) => $e->requires_confirmation
                    && ! $e->execution_confirmed_at
                    && $e->starts_at
                    && $e->starts_at->isPast()
                    && ! in_array($e->status, ['skipped', 'published'], true))
                ->count(),
        ];

        return view('employee.marketing-plans.show', compact(
            'plan',
            'cycles',
            'platformLabels',
            'employeeJobs',
            'contentTypes',
            'isBusinessDeveloper',
            'executionStats',
        ));
    }

    public function edit(ModeratorMarketingPlan $marketing_plan)
    {
        $this->assertOwnPlan($marketing_plan);
        $plan = $marketing_plan;
        $cycles = DesignTaskCycle::query()
            ->where('moderator_id', $this->planModeratorId($marketing_plan))
            ->orderByDesc('id')
            ->limit(100)
            ->get(['id', 'title', 'status']);

        return view('employee.marketing-plans.edit', compact('plan', 'cycles'));
    }

    public function update(Request $request, ModeratorMarketingPlan $marketing_plan)
    {
        $this->assertOwnPlan($marketing_plan);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:10000'],
            'goals' => ['nullable', 'string', 'max:20000'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:draft,active,paused,completed'],
            'design_task_cycle_id' => ['nullable', 'exists:design_task_cycles,id'],
        ]);

        if (! empty($validated['design_task_cycle_id'])) {
            $ok = DesignTaskCycle::query()
                ->where('id', $validated['design_task_cycle_id'])
                ->where('moderator_id', $this->planModeratorId($marketing_plan))
                ->exists();
            if (! $ok) {
                return back()->withErrors(['design_task_cycle_id' => 'دورة التصميم غير صالحة.'])->withInput();
            }
        } else {
            $validated['design_task_cycle_id'] = null;
        }

        $marketing_plan->update($validated);

        return redirect()
            ->route('employee.marketing-plans.show', $marketing_plan)
            ->with('success', 'تم تحديث الخطة.');
    }

    public function destroy(ModeratorMarketingPlan $marketing_plan)
    {
        $this->assertOwnPlan($marketing_plan);
        $marketing_plan->delete();

        return redirect()
            ->route('employee.marketing-plans.index')
            ->with('success', 'تم حذف الخطة.');
    }

    public function storePlatform(Request $request, ModeratorMarketingPlan $marketing_plan)
    {
        $this->assertOwnPlan($marketing_plan);

        $validated = $request->validate([
            'platform_key' => ['nullable', 'string', 'max:40'],
            'platform_keys' => ['nullable', 'array', 'min:1'],
            'platform_keys.*' => ['string', 'max:40'],
            'custom_label' => ['nullable', 'string', 'max:120'],
            'profile_url' => ['nullable', 'url', 'max:500'],
            'strategy_notes' => ['nullable', 'string', 'max:10000'],
            'cadence_notes' => ['nullable', 'string', 'max:10000'],
            'color_hex' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'employee_job_ids' => ['nullable', 'array'],
            'employee_job_ids.*' => ['integer', 'exists:employee_jobs,id'],
        ]);

        $keys = array_keys(ModeratorMarketingPlatform::platformLabels());
        $platformKeys = [];
        if (! empty($validated['platform_keys']) && is_array($validated['platform_keys'])) {
            $platformKeys = array_values(array_filter(array_map('strtolower', $validated['platform_keys'])));
        } elseif (! empty($validated['platform_key'])) {
            $platformKeys = [strtolower((string) $validated['platform_key'])];
        }

        if ($platformKeys === []) {
            return back()->withErrors(['platform_key' => 'اختر منصة واحدة على الأقل.'])->withInput();
        }

        foreach ($platformKeys as $pk) {
            if (! in_array($pk, $keys, true)) {
                return back()->withErrors(['platform_key' => 'منصة غير معروفة.'])->withInput();
            }
        }

        // "other" لا يدعم الإضافة المتعددة (يحتاج اسم مخصص واحد)
        if (in_array('other', $platformKeys, true)) {
            if (count($platformKeys) > 1) {
                return back()->withErrors(['platform_key' => 'لا يمكن اختيار "أخرى" مع منصات متعددة. اختر "أخرى" وحدها.'])->withInput();
            }
            $custom = trim((string) ($validated['custom_label'] ?? ''));
            if ($custom === '') {
                return back()->withErrors(['custom_label' => 'اكتب اسم المنصة عند اختيار "أخرى".'])->withInput();
            }
        } else {
            $validated['custom_label'] = null;
        }

        $maxSort = (int) $marketing_plan->platforms()->max('sort_order');
        $color = $validated['color_hex'] ?? '#6366f1';
        $jobIds = array_map('intval', $validated['employee_job_ids'] ?? []);

        $created = 0;
        foreach ($platformKeys as $pk) {
            // لا تكرر نفس المنصة داخل نفس الخطة
            $exists = ModeratorMarketingPlatform::query()
                ->where('plan_id', $marketing_plan->id)
                ->where('platform_key', $pk)
                ->exists();
            if ($exists) {
                continue;
            }

            $maxSort++;
            $platform = ModeratorMarketingPlatform::create([
                'plan_id' => $marketing_plan->id,
                'platform_key' => $pk,
                'custom_label' => $validated['custom_label'] ?? null,
                'profile_url' => $validated['profile_url'] ?? null,
                'strategy_notes' => $validated['strategy_notes'] ?? null,
                'cadence_notes' => $validated['cadence_notes'] ?? null,
                'color_hex' => $color,
                'sort_order' => $maxSort,
            ]);
            if ($jobIds !== []) {
                $platform->employeeJobs()->sync($jobIds);
            }
            $created++;
        }

        if ($created <= 0) {
            return back()->with('success', 'لم يتم إضافة منصات جديدة (قد تكون مضافة مسبقاً).');
        }

        return back()->with('success', 'تمت إضافة '.$created.' منصة/منصات.');
    }

    public function updatePlatform(Request $request, ModeratorMarketingPlan $marketing_plan, ModeratorMarketingPlatform $platform)
    {
        $this->assertOwnPlan($marketing_plan);
        abort_unless((int) $platform->plan_id === (int) $marketing_plan->id, 404);

        $validated = $request->validate([
            'platform_key' => ['required', 'string', 'max:40'],
            'custom_label' => ['nullable', 'string', 'max:120'],
            'profile_url' => ['nullable', 'url', 'max:500'],
            'strategy_notes' => ['nullable', 'string', 'max:10000'],
            'cadence_notes' => ['nullable', 'string', 'max:10000'],
            'color_hex' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'employee_job_ids' => ['nullable', 'array'],
            'employee_job_ids.*' => ['integer', 'exists:employee_jobs,id'],
        ]);

        $keys = array_keys(ModeratorMarketingPlatform::platformLabels());
        if (! in_array($validated['platform_key'], $keys, true)) {
            return back()->withErrors(['platform_key' => 'منصة غير معروفة.'])->withInput();
        }
        if ($validated['platform_key'] !== 'other') {
            $validated['custom_label'] = null;
        }

        $jobIds = array_map('intval', $request->input('employee_job_ids', []));
        unset($validated['employee_job_ids']);

        $platform->update($validated);
        $platform->employeeJobs()->sync($jobIds);

        return back()->with('success', 'تم تحديث المنصة.');
    }

    public function destroyPlatform(ModeratorMarketingPlan $marketing_plan, ModeratorMarketingPlatform $platform)
    {
        $this->assertOwnPlan($marketing_plan);
        abort_unless((int) $platform->plan_id === (int) $marketing_plan->id, 404);
        $platform->delete();

        return back()->with('success', 'تم حذف المنصة.');
    }

    public function storeEvent(Request $request, ModeratorMarketingPlan $marketing_plan)
    {
        $this->assertOwnPlan($marketing_plan);

        $validated = $request->validate([
            'platform_id' => ['nullable', 'exists:moderator_mkt_platforms,id'],
            'platform_ids' => ['nullable', 'array', 'min:1'],
            'platform_ids.*' => ['integer', 'exists:moderator_mkt_platforms,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:10000'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['required', 'in:idea,draft,scheduled,published,skipped'],
            'content_type' => ['required', 'in:'.implode(',', array_keys(MarketingPlanEventAutomationService::contentTypeLabels()))],
            'requires_confirmation' => ['nullable', 'boolean'],
            'assigned_employee_id' => ['nullable', 'exists:users,id'],
            'design_task_cycle_id' => ['nullable', 'exists:design_task_cycles,id'],
        ]);

        $platformIds = [];
        if (! empty($validated['platform_ids']) && is_array($validated['platform_ids'])) {
            $platformIds = array_values(array_unique(array_map('intval', $validated['platform_ids'])));
        } elseif (! empty($validated['platform_id'])) {
            $platformIds = [(int) $validated['platform_id']];
        }

        if ($platformIds !== []) {
            $okCount = ModeratorMarketingPlatform::query()
                ->whereIn('id', $platformIds)
                ->where('plan_id', $marketing_plan->id)
                ->count();
            if ($okCount !== count($platformIds)) {
                return back()->withErrors(['platform_ids' => 'يوجد منصة/منصات غير تابعة لهذه الخطة.'])->withInput();
            }
        }

        if (! empty($validated['design_task_cycle_id'])) {
            $ok = DesignTaskCycle::query()
                ->where('id', $validated['design_task_cycle_id'])
                ->where('moderator_id', $this->planModeratorId($marketing_plan))
                ->exists();
            if (! $ok) {
                return back()->withErrors(['design_task_cycle_id' => 'دورة التصميم غير صالحة.'])->withInput();
            }
        }

        $base = [
            'plan_id' => $marketing_plan->id,
            'title' => $validated['title'],
            'body' => $validated['body'] ?? null,
            'content_type' => $validated['content_type'],
            'assigned_employee_id' => $validated['assigned_employee_id'] ?? null,
            'requires_confirmation' => $request->boolean('requires_confirmation', true),
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'] ?? null,
            'status' => $validated['status'],
            'design_task_cycle_id' => $validated['design_task_cycle_id'] ?? null,
        ];

        if ($platformIds === []) {
            $ev = ModeratorMarketingCalendarEvent::create($base + ['platform_id' => null]);
            $this->marketingAutomation->afterEventSaved($ev);

            return back()->with('success', 'تمت إضافة الحدث للتقويم وربط المسؤول تلقائياً.');
        }

        $created = 0;
        foreach ($platformIds as $pid) {
            $ev = ModeratorMarketingCalendarEvent::create($base + ['platform_id' => $pid]);
            $this->marketingAutomation->afterEventSaved($ev);
            $created++;
        }

        return back()->with('success', 'تمت إضافة الحدث لعدد '.$created.' منصة/منصات مع التوجيه التلقائي.');
    }

    public function updateEvent(Request $request, ModeratorMarketingPlan $marketing_plan, ModeratorMarketingCalendarEvent $event)
    {
        $this->assertOwnPlan($marketing_plan);
        abort_unless((int) $event->plan_id === (int) $marketing_plan->id, 404);

        $validated = $request->validate([
            'platform_id' => ['nullable', 'exists:moderator_mkt_platforms,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:10000'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['required', 'in:idea,draft,scheduled,published,skipped'],
            'content_type' => ['required', 'in:'.implode(',', array_keys(MarketingPlanEventAutomationService::contentTypeLabels()))],
            'requires_confirmation' => ['nullable', 'boolean'],
            'assigned_employee_id' => ['nullable', 'exists:users,id'],
            'design_task_cycle_id' => ['nullable', 'exists:design_task_cycles,id'],
        ]);

        if (! empty($validated['platform_id'])) {
            $p = ModeratorMarketingPlatform::query()
                ->where('id', $validated['platform_id'])
                ->where('plan_id', $marketing_plan->id)
                ->exists();
            if (! $p) {
                return back()->withErrors(['platform_id' => 'المنصة غير تابعة لهذه الخطة.'])->withInput();
            }
        } else {
            $validated['platform_id'] = null;
        }

        if (! empty($validated['design_task_cycle_id'])) {
            $ok = DesignTaskCycle::query()
                ->where('id', $validated['design_task_cycle_id'])
                ->where('moderator_id', $this->planModeratorId($marketing_plan))
                ->exists();
            if (! $ok) {
                return back()->withErrors(['design_task_cycle_id' => 'دورة التصميم غير صالحة.'])->withInput();
            }
        } else {
            $validated['design_task_cycle_id'] = null;
        }

        $validated['requires_confirmation'] = $request->boolean('requires_confirmation', true);

        $event->update($validated);
        $this->marketingAutomation->afterEventSaved($event->fresh());

        return back()->with('success', 'تم تحديث الحدث.');
    }

    public function destroyEvent(ModeratorMarketingPlan $marketing_plan, ModeratorMarketingCalendarEvent $event)
    {
        $this->assertOwnPlan($marketing_plan);
        abort_unless((int) $event->plan_id === (int) $marketing_plan->id, 404);
        $event->delete();

        return back()->with('success', 'تم حذف الحدث.');
    }
}
