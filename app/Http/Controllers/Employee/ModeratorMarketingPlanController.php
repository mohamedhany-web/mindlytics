<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\DesignTaskCycle;
use App\Models\ModeratorMarketingCalendarEvent;
use App\Models\ModeratorMarketingPlan;
use App\Models\ModeratorMarketingPlatform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModeratorMarketingPlanController extends Controller
{
    private function assertOwnPlan(ModeratorMarketingPlan $plan): void
    {
        abort_unless((int) $plan->moderator_id === (int) Auth::id(), 403);
    }

    public function index()
    {
        $plans = ModeratorMarketingPlan::query()
            ->where('moderator_id', Auth::id())
            ->withCount(['platforms', 'calendarEvents'])
            ->latest()
            ->paginate(15);

        return view('employee.marketing-plans.index', compact('plans'));
    }

    public function create()
    {
        $cycles = DesignTaskCycle::query()
            ->where('moderator_id', Auth::id())
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
            $ok = DesignTaskCycle::query()
                ->where('id', $validated['design_task_cycle_id'])
                ->where('moderator_id', Auth::id())
                ->exists();
            if (! $ok) {
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
            'platforms',
            'calendarEvents' => fn ($q) => $q->with('platform')->orderBy('starts_at'),
            'designTaskCycle',
        ]);

        $cycles = DesignTaskCycle::query()
            ->where('moderator_id', Auth::id())
            ->orderByDesc('id')
            ->limit(100)
            ->get(['id', 'title', 'status']);

        $platformLabels = ModeratorMarketingPlatform::platformLabels();

        return view('employee.marketing-plans.show', compact('plan', 'cycles', 'platformLabels'));
    }

    public function edit(ModeratorMarketingPlan $marketing_plan)
    {
        $this->assertOwnPlan($marketing_plan);
        $plan = $marketing_plan;
        $cycles = DesignTaskCycle::query()
            ->where('moderator_id', Auth::id())
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
                ->where('moderator_id', Auth::id())
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
            'platform_key' => ['required', 'string', 'max:40'],
            'custom_label' => ['nullable', 'string', 'max:120'],
            'profile_url' => ['nullable', 'url', 'max:500'],
            'strategy_notes' => ['nullable', 'string', 'max:10000'],
            'cadence_notes' => ['nullable', 'string', 'max:10000'],
            'color_hex' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $keys = array_keys(ModeratorMarketingPlatform::platformLabels());
        if (! in_array($validated['platform_key'], $keys, true)) {
            return back()->withErrors(['platform_key' => 'منصة غير معروفة.'])->withInput();
        }
        if ($validated['platform_key'] !== 'other') {
            $validated['custom_label'] = null;
        }

        $validated['plan_id'] = $marketing_plan->id;
        $validated['sort_order'] = (int) $marketing_plan->platforms()->max('sort_order') + 1;
        $validated['color_hex'] = $validated['color_hex'] ?? '#6366f1';

        ModeratorMarketingPlatform::create($validated);

        return back()->with('success', 'تمت إضافة المنصة.');
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
        ]);

        $keys = array_keys(ModeratorMarketingPlatform::platformLabels());
        if (! in_array($validated['platform_key'], $keys, true)) {
            return back()->withErrors(['platform_key' => 'منصة غير معروفة.'])->withInput();
        }
        if ($validated['platform_key'] !== 'other') {
            $validated['custom_label'] = null;
        }

        $platform->update($validated);

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
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:10000'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['required', 'in:idea,draft,scheduled,published,skipped'],
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
        }

        if (! empty($validated['design_task_cycle_id'])) {
            $ok = DesignTaskCycle::query()
                ->where('id', $validated['design_task_cycle_id'])
                ->where('moderator_id', Auth::id())
                ->exists();
            if (! $ok) {
                return back()->withErrors(['design_task_cycle_id' => 'دورة التصميم غير صالحة.'])->withInput();
            }
        }

        $validated['plan_id'] = $marketing_plan->id;
        ModeratorMarketingCalendarEvent::create($validated);

        return back()->with('success', 'تمت إضافة الحدث للتقويم.');
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
                ->where('moderator_id', Auth::id())
                ->exists();
            if (! $ok) {
                return back()->withErrors(['design_task_cycle_id' => 'دورة التصميم غير صالحة.'])->withInput();
            }
        } else {
            $validated['design_task_cycle_id'] = null;
        }

        $event->update($validated);

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
