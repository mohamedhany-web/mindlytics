<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesShiftPlan;
use App\Models\SalesShiftSegment;
use App\Models\User;
use App\Services\SalesShiftPlanImporter;
use App\Services\SalesShiftScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesShiftController extends Controller
{
    public function index(Request $request, SalesShiftScheduleService $shifts): View
    {
        $plan = $shifts->activePlan();
        $weekStart = $shifts->resolveWeekStart($request->query('week'));
        $board = $plan ? $shifts->buildWeekBoard($plan, $weekStart) : null;

        return view('admin.sales.shifts.index', [
            'plan' => $plan,
            'board' => $board,
            'weekStart' => $weekStart,
            'salesReps' => User::salesEmployees()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function importDemo(SalesShiftPlanImporter $importer): RedirectResponse
    {
        $importer->importDefaultPlan(true);

        return redirect()->route('admin.sales.shifts.index')->with('success', 'تم استيراد جدول الشيفتات الافتراضي وتفعيله.');
    }

    public function updatePlan(Request $request, SalesShiftPlan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
            'work_start_hour' => 'required|integer|min:0|max:23',
            'work_end_hour' => 'required|integer|min:13|max:30',
            'takeover_grace_minutes' => 'required|integer|min:1|max:60',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->boolean('is_active')) {
            SalesShiftPlan::query()->where('id', '!=', $plan->id)->update(['is_active' => false]);
        }

        $plan->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'work_start_hour' => $validated['work_start_hour'],
            'work_end_hour' => $validated['work_end_hour'],
            'takeover_grace_minutes' => $validated['takeover_grace_minutes'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'تم تحديث إعدادات الخطة.');
    }

    public function storeSegment(Request $request, SalesShiftPlan $plan): RedirectResponse
    {
        $validated = $this->validateSegment($request);
        $plan->segments()->create($validated);

        return back()->with('success', 'تمت إضافة segment.');
    }

    public function updateSegment(Request $request, SalesShiftSegment $segment): RedirectResponse
    {
        $validated = $this->validateSegment($request);
        $segment->update($validated);

        return back()->with('success', 'تم تحديث segment.');
    }

    public function destroySegment(SalesShiftSegment $segment): RedirectResponse
    {
        $segment->delete();

        return back()->with('success', 'تم حذف segment.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateSegment(Request $request): array
    {
        $validated = $request->validate([
            'sales_shift_plan_id' => 'required|exists:sales_shift_plans,id',
            'day_of_week' => 'required|integer|min:0|max:6',
            'user_id' => 'required|exists:users,id',
            'start_hour' => 'required|integer|min:0|max:30',
            'end_hour' => 'required|integer|min:1|max:30|gt:start_hour',
            'mode' => 'required|in:normal,home',
            'channels' => 'required|array|min:1',
            'channels.*' => 'string|in:'.implode(',', array_keys(config('sales_shifts.channels', []))),
            'location_badge' => 'nullable|string|max:40',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['channels'] = array_values(array_unique($validated['channels']));
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        return $validated;
    }
}
