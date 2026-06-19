<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesignTaskCycle;
use App\Models\EmployeeJob;
use App\Models\ModeratorMarketingCalendarEvent;
use App\Models\ModeratorMarketingPlan;
use App\Models\ModeratorMarketingPlatform;
use App\Models\User;
use App\Services\MarketingPlanEventAutomationService;
use App\Support\MarketingPlanSettings;
use Illuminate\Http\Request;

class ModeratorMarketingPlanController extends Controller
{
    public function __construct(
        protected MarketingPlanEventAutomationService $marketingAutomation
    ) {}

    public function index(Request $request)
    {
        $query = ModeratorMarketingPlan::query()
            ->with(['moderator'])
            ->withCount(['platforms', 'calendarEvents']);

        if ($request->filled('moderator_id')) {
            $query->where('moderator_id', $request->moderator_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $q = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $request->q).'%';
            $query->where('title', 'like', $q);
        }

        $plans = $query->latest()->paginate(25)->withQueryString();

        $moderators = User::moderatorEmployees()->where('is_active', true)->orderBy('name')->get();

        $today = today();
        $stats = [
            'total' => ModeratorMarketingPlan::count(),
            'active' => ModeratorMarketingPlan::where('status', 'active')->count(),
            'moderators' => ModeratorMarketingPlan::distinct('moderator_id')->count('moderator_id'),
            'platforms' => ModeratorMarketingPlatform::count(),
            'events_today' => ModeratorMarketingCalendarEvent::whereDate('starts_at', $today)->count(),
            'pending_confirm_today' => ModeratorMarketingCalendarEvent::whereDate('starts_at', $today)
                ->where('requires_confirmation', true)
                ->whereNull('execution_confirmed_at')
                ->count(),
            'penalties_month' => ModeratorMarketingCalendarEvent::whereNotNull('execution_penalty_deduction_id')
                ->whereMonth('starts_at', $today->month)
                ->whereYear('starts_at', $today->year)
                ->count(),
        ];

        return view('admin.moderator-marketing-plans.index', compact('plans', 'moderators', 'stats'));
    }

    public function create()
    {
        $moderators = User::moderatorEmployees()->where('is_active', true)->orderBy('name')->get();

        return view('admin.moderator-marketing-plans.create', compact('moderators'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'moderator_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:10000'],
            'goals' => ['nullable', 'string', 'max:20000'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:draft,active,paused,completed'],
            'design_task_cycle_id' => ['nullable', 'exists:design_task_cycles,id'],
        ]);

        $moderator = User::query()->find($validated['moderator_id']);
        if (! $moderator?->isModeratorEmployee()) {
            return back()->withErrors(['moderator_id' => 'اختر مشرفاً صالحاً.'])->withInput();
        }

        if (! empty($validated['design_task_cycle_id'])) {
            $ok = DesignTaskCycle::query()
                ->where('id', $validated['design_task_cycle_id'])
                ->where('moderator_id', $moderator->id)
                ->exists();
            if (! $ok) {
                return back()->withErrors(['design_task_cycle_id' => 'دورة التصميم غير صالحة لهذا المشرف.'])->withInput();
            }
        }

        $plan = ModeratorMarketingPlan::create($validated);

        return redirect()->route('admin.moderator-marketing-plans.show', $plan)
            ->with('success', 'تم إنشاء خطة التسويق.');
    }

    public function show(ModeratorMarketingPlan $plan)
    {
        $plan->load([
            'moderator',
            'platforms.employeeJobs',
            'calendarEvents' => fn ($q) => $q->with(['platform', 'assignee', 'employeeTask', 'confirmedBy'])->orderBy('starts_at'),
            'designTaskCycle',
        ]);

        $cycles = DesignTaskCycle::query()->where('moderator_id', $plan->moderator_id)->orderByDesc('id')->limit(100)->get(['id', 'title', 'status']);
        $platformLabels = ModeratorMarketingPlatform::platformLabels();
        $employeeJobs = EmployeeJob::active()->orderBy('name')->get();
        $contentTypes = MarketingPlanEventAutomationService::contentTypeLabels();
        $employees = User::employees()->where('is_active', true)->orderBy('name')->get();

        return view('admin.moderator-marketing-plans.show', compact('plan', 'cycles', 'platformLabels', 'employeeJobs', 'contentTypes', 'employees'));
    }

    public function edit(ModeratorMarketingPlan $plan)
    {
        $moderators = User::moderatorEmployees()->where('is_active', true)->orderBy('name')->get();
        $cycles = DesignTaskCycle::query()->where('moderator_id', $plan->moderator_id)->orderByDesc('id')->limit(100)->get(['id', 'title', 'status']);

        return view('admin.moderator-marketing-plans.edit', compact('plan', 'moderators', 'cycles'));
    }

    public function update(Request $request, ModeratorMarketingPlan $plan)
    {
        $validated = $request->validate([
            'moderator_id' => ['required', 'exists:users,id'],
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
                ->where('moderator_id', $validated['moderator_id'])
                ->exists();
            if (! $ok) {
                return back()->withErrors(['design_task_cycle_id' => 'دورة التصميم غير صالحة.'])->withInput();
            }
        } else {
            $validated['design_task_cycle_id'] = null;
        }

        $plan->update($validated);

        return redirect()->route('admin.moderator-marketing-plans.show', $plan)->with('success', 'تم التحديث.');
    }

    public function destroy(ModeratorMarketingPlan $plan)
    {
        $plan->delete();

        return redirect()->route('admin.moderator-marketing-plans.index')->with('success', 'تم حذف الخطة.');
    }

    public function settings()
    {
        $settings = MarketingPlanSettings::all();

        return view('admin.moderator-marketing-plans.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'automation_enabled' => ['nullable', 'boolean'],
            'penalty_enabled' => ['nullable', 'boolean'],
            'penalty_amount' => ['required', 'numeric', 'min:0'],
            'auto_create_tasks' => ['nullable', 'boolean'],
            'reminder_time' => ['required', 'date_format:H:i'],
            'confirmation_deadline_time' => ['required', 'date_format:H:i'],
        ]);

        MarketingPlanSettings::save([
            'automation_enabled' => $request->boolean('automation_enabled'),
            'penalty_enabled' => $request->boolean('penalty_enabled'),
            'penalty_amount' => $validated['penalty_amount'],
            'auto_create_tasks' => $request->boolean('auto_create_tasks'),
            'reminder_time' => $validated['reminder_time'],
            'confirmation_deadline_time' => $validated['confirmation_deadline_time'],
        ]);

        return back()->with('success', 'تم حفظ إعدادات الأتمتة والغرامات.');
    }

    public function storePlatform(Request $request, ModeratorMarketingPlan $plan)
    {
        return app(\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class)
            ->storePlatform($request, $plan);
    }

    public function updatePlatform(Request $request, ModeratorMarketingPlan $plan, ModeratorMarketingPlatform $platform)
    {
        return app(\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class)
            ->updatePlatform($request, $plan, $platform);
    }

    public function destroyPlatform(ModeratorMarketingPlan $plan, ModeratorMarketingPlatform $platform)
    {
        return app(\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class)
            ->destroyPlatform($plan, $platform);
    }

    public function storeEvent(Request $request, ModeratorMarketingPlan $plan)
    {
        return app(\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class)
            ->storeEvent($request, $plan);
    }

    public function updateEvent(Request $request, ModeratorMarketingPlan $plan, ModeratorMarketingCalendarEvent $event)
    {
        return app(\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class)
            ->updateEvent($request, $plan, $event);
    }

    public function destroyEvent(ModeratorMarketingPlan $plan, ModeratorMarketingCalendarEvent $event)
    {
        return app(\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class)
            ->destroyEvent($plan, $event);
    }

    public function confirmEvent(ModeratorMarketingPlan $plan, ModeratorMarketingCalendarEvent $event)
    {
        abort_unless((int) $event->plan_id === (int) $plan->id, 404);
        $this->marketingAutomation->confirmExecution($event, auth()->user());

        return back()->with('success', 'تم تأكيد التنفيذ من الإدارة.');
    }
}
