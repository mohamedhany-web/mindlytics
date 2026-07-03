<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\InvestmentInquiry;
use App\Models\InvestmentPlan;
use App\Models\InvestmentPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvestmentController extends Controller
{
    public function index(): View
    {
        $policy = InvestmentPolicy::current();
        $plans = InvestmentPlan::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('is_featured')
            ->get()
            ->filter(fn (InvestmentPlan $p) => $p->isOpen())
            ->values();

        return view('public.investment.index', compact('policy', 'plans'));
    }

    public function show(string $slug): View
    {
        $plan = InvestmentPlan::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();
        $policy = InvestmentPolicy::current();

        return view('public.investment.show', compact('plan', 'policy'));
    }

    public function apply(Request $request, ?string $slug = null): RedirectResponse
    {
        $plan = null;
        if ($slug) {
            $plan = InvestmentPlan::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();
        }

        $validated = $request->validate([
            'investment_plan_id' => 'nullable|exists:investment_plans,id',
            'full_name' => 'required|string|max:200',
            'email' => 'required|email|max:200',
            'phone' => 'required|string|max:30',
            'country_code' => 'nullable|string|max:5',
            'company_name' => 'nullable|string|max:200',
            'investor_type' => 'required|in:individual,company,fund',
            'proposed_amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'experience_notes' => 'nullable|string|max:3000',
            'message' => 'nullable|string|max:3000',
            'accept_terms' => 'accepted',
        ], [
            'full_name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'phone.required' => 'رقم الهاتف مطلوب',
            'accept_terms.accepted' => 'يجب الموافقة على الشروط والأحكام',
        ]);

        $planId = $plan?->id ?? $validated['investment_plan_id'] ?? null;

        InvestmentInquiry::create([
            'investment_plan_id' => $planId,
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'country_code' => $validated['country_code'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'investor_type' => $validated['investor_type'],
            'proposed_amount' => $validated['proposed_amount'] ?? null,
            'currency' => $validated['currency'] ?? ($plan?->currency ?? 'EGP'),
            'experience_notes' => $validated['experience_notes'] ?? null,
            'message' => $validated['message'] ?? null,
            'status' => InvestmentInquiry::STATUS_PENDING,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'تم استلام طلبك بنجاح. سيتواصل معك فريق Mindlytics خلال 3–5 أيام عمل.');
    }
}
