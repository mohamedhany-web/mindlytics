<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\InstallmentPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InstallmentPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage.installments');
    }

    public function index(Request $request): View
    {
        $plansQuery = InstallmentPlan::with('course')
            ->withCount('agreements')
            ->when($request->boolean('active_only'), fn ($query) => $query->where('is_active', true))
            ->latest();

        $plans = $plansQuery
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => InstallmentPlan::count(),
            'active' => InstallmentPlan::where('is_active', true)->count(),
            'inactive' => InstallmentPlan::where('is_active', false)->count(),
            'auto_generate' => InstallmentPlan::where('auto_generate_on_enrollment', true)->count(),
            'total_amount' => (float) InstallmentPlan::sum('total_amount'),
            'total_deposit' => (float) InstallmentPlan::sum('deposit_amount'),
            'average_installments' => round((float) InstallmentPlan::avg('installments_count'), 1),
        ];

        $currentMonthRange = [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ];

        $monthlyNew = InstallmentPlan::whereBetween('created_at', $currentMonthRange)->count();
        $monthlyAmount = (float) InstallmentPlan::whereBetween('created_at', $currentMonthRange)->sum('total_amount');

        $frequencyBreakdown = InstallmentPlan::selectRaw('frequency_unit, COUNT(*) as plans_count, SUM(total_amount) as total_amount')
            ->groupBy('frequency_unit')
            ->get();

        $recentPlans = InstallmentPlan::with('course')
            ->latest()
            ->take(6)
            ->get();

        $highValuePlans = InstallmentPlan::with('course')
            ->orderByDesc('total_amount')
            ->take(6)
            ->get();

        $unitLabels = $this->frequencyUnits();

        return view('admin.installments.plans.index', compact(
            'plans',
            'stats',
            'monthlyNew',
            'monthlyAmount',
            'frequencyBreakdown',
            'recentPlans',
            'highValuePlans',
            'unitLabels'
        ));
    }

    public function create(): View
    {
        $courses = AdvancedCourse::orderBy('title')->get(['id', 'title', 'price']);
        $frequencyUnits = $this->frequencyUnits();

        return view('admin.installments.plans.create', compact('courses', 'frequencyUnits'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        if (! $data['total_amount'] && $data['advanced_course_id']) {
            $course = AdvancedCourse::find($data['advanced_course_id']);
            $data['total_amount'] = $course?->price ?? 0;
        }

        if (($data['total_amount'] ?? 0) < ($data['deposit_amount'] ?? 0)) {
            return back()->withErrors(['deposit_amount' => 'قيمة الدفعة المقدمة أكبر من إجمالي المبلغ.'])->withInput();
        }

        $plan = InstallmentPlan::create($data);

        Log::info('Installment plan created', ['plan_id' => $plan->id]);

        return redirect()->route('admin.installments.plans.show', $plan)->with('success', 'تم إنشاء خطة التقسيط بنجاح.');
    }

    public function show(InstallmentPlan $plan): View
    {
        $plan->load(['course', 'agreements.student', 'agreements.course']);
        $frequencyUnits = $this->frequencyUnits();

        return view('admin.installments.plans.show', compact('plan', 'frequencyUnits'));
    }

    public function edit(InstallmentPlan $plan): View
    {
        $courses = AdvancedCourse::orderBy('title')->get(['id', 'title', 'price']);
        $frequencyUnits = $this->frequencyUnits();

        return view('admin.installments.plans.edit', compact('plan', 'courses', 'frequencyUnits'));
    }

    public function update(Request $request, InstallmentPlan $plan): RedirectResponse
    {
        $data = $this->validatedData($request, $plan->id);

        if (! $data['total_amount'] && $data['advanced_course_id']) {
            $course = AdvancedCourse::find($data['advanced_course_id']);
            $data['total_amount'] = $course?->price ?? 0;
        }

        if (($data['total_amount'] ?? 0) < ($data['deposit_amount'] ?? 0)) {
            return back()->withErrors(['deposit_amount' => 'قيمة الدفعة المقدمة أكبر من إجمالي المبلغ.'])->withInput();
        }

        $plan->update($data);

        return redirect()->route('admin.installments.plans.show', $plan)->with('success', 'تم تحديث خطة التقسيط بنجاح.');
    }

    public function destroy(InstallmentPlan $plan): RedirectResponse
    {
        if ($plan->agreements()->exists()) {
            return back()->with('error', 'لا يمكن حذف الخطة لارتباطها باتفاقيات نشطة. يمكنك تعطيلها بدلاً من ذلك.');
        }

        $plan->delete();

        return redirect()->route('admin.installments.plans.index')->with('success', 'تم حذف خطة التقسيط.');
    }

    protected function validatedData(Request $request, ?int $planId = null): array
    {
        $frequencyUnits = implode(',', array_keys($this->frequencyUnits()));

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'advanced_course_id' => ['nullable', 'exists:advanced_courses,id'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'installments_count' => ['required', 'integer', 'min:1', 'max:36'],
            'frequency_unit' => ["required", "in:$frequencyUnits"],
            'frequency_interval' => ['required', 'integer', 'min:1', 'max:12'],
            'grace_period_days' => ['nullable', 'integer', 'min:0', 'max:30'],
            'auto_generate_on_enrollment' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ], [], [
            'name' => 'اسم الخطة',
            'advanced_course_id' => 'الكورس',
            'total_amount' => 'إجمالي المبلغ',
            'deposit_amount' => 'الدفعة المقدمة',
            'installments_count' => 'عدد الأقساط',
            'frequency_unit' => 'دورية السداد',
            'frequency_interval' => 'الفاصل الزمني',
        ]);

        $validated['auto_generate_on_enrollment'] = $request->boolean('auto_generate_on_enrollment');
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;
        $validated['grace_period_days'] = $validated['grace_period_days'] ?? 0;
        $validated['deposit_amount'] = $validated['deposit_amount'] ?? 0;

        return $validated;
    }

    protected function frequencyUnits(): array
    {
        return [
            'month' => 'شهري',
            'week' => 'أسبوعي',
            'day' => 'يومي',
            'year' => 'سنوي',
        ];
    }
}
