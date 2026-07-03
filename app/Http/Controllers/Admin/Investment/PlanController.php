<?php

namespace App\Http\Controllers\Admin\Investment;

use App\Http\Controllers\Controller;
use App\Models\InvestmentPlan;
use App\Services\Investment\InvestmentPlanService;
use App\Services\Investment\InvestmentStatsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(Request $request, InvestmentStatsService $stats): View
    {
        $query = InvestmentPlan::query()
            ->withCount('inquiries')
            ->orderBy('sort_order')
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('slug', 'like', "%{$s}%");
            });
        }

        if ($request->filled('plan_type')) {
            $query->where('plan_type', $request->plan_type);
        }

        $plans = $query->paginate(20)->withQueryString();
        $overview = $stats->overview();

        return view('admin.investment.plans.index', compact('plans', 'overview'));
    }

    public function create(): View
    {
        return view('admin.investment.plans.create', [
            'planTypes' => InvestmentPlan::planTypeLabels(),
            'returnModels' => InvestmentPlan::returnModelLabels(),
            'riskLevels' => InvestmentPlan::riskLevelLabels(),
        ]);
    }

    public function store(Request $request, InvestmentPlanService $service): RedirectResponse
    {
        $validated = $this->validatePlan($request);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);

        $plan = $service->create($validated, auth()->id());

        return redirect()->route('admin.investment.plans.show', $plan)
            ->with('success', 'تم إنشاء الخطة الاستثمارية بنجاح.');
    }

    public function show(InvestmentPlan $plan): View
    {
        $plan->loadCount([
            'inquiries',
            'inquiries as pending_count' => fn ($q) => $q->where('status', 'pending'),
            'inquiries as approved_count' => fn ($q) => $q->where('status', 'approved'),
        ]);

        $inquiries = $plan->inquiries()->latest()->paginate(20);

        return view('admin.investment.plans.show', compact('plan', 'inquiries'));
    }

    public function edit(InvestmentPlan $plan): View
    {
        return view('admin.investment.plans.edit', [
            'plan' => $plan,
            'planTypes' => InvestmentPlan::planTypeLabels(),
            'returnModels' => InvestmentPlan::returnModelLabels(),
            'riskLevels' => InvestmentPlan::riskLevelLabels(),
        ]);
    }

    public function update(Request $request, InvestmentPlan $plan, InvestmentPlanService $service): RedirectResponse
    {
        $validated = $this->validatePlan($request, $plan->id);
        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['is_featured'] = $request->boolean('is_featured', false);

        $service->update($plan, $validated);

        return redirect()->route('admin.investment.plans.show', $plan)
            ->with('success', 'تم تحديث الخطة الاستثمارية.');
    }

    public function destroy(InvestmentPlan $plan): RedirectResponse
    {
        $plan->delete();

        return redirect()->route('admin.investment.plans.index')
            ->with('success', 'تم حذف الخطة.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePlan(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title' => 'required|string|max:200',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:10000',
            'plan_type' => 'required|in:equity,revenue_share,partnership,fixed_return,strategic',
            'min_investment' => 'required|numeric|min:0',
            'max_investment' => 'nullable|numeric|min:0',
            'target_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'duration_months' => 'nullable|integer|min:1|max:600',
            'expected_return_min' => 'nullable|numeric|min:0|max:1000',
            'expected_return_max' => 'nullable|numeric|min:0|max:1000',
            'return_model' => 'required|in:profit_share,fixed_annual,equity_stake,revenue_share,custom',
            'risk_level' => 'required|in:low,medium,high',
            'eligibility_criteria' => 'nullable|string|max:5000',
            'benefits' => 'nullable|string|max:5000',
            'terms_summary' => 'nullable|string|max:5000',
            'legal_notes' => 'nullable|string|max:5000',
            'process_steps' => 'nullable|array',
            'process_steps.*.title' => 'nullable|string|max:200',
            'process_steps.*.description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ], [
            'title.required' => 'عنوان الخطة مطلوب',
            'min_investment.required' => 'الحد الأدنى للاستثمار مطلوب',
        ]);
    }
}
