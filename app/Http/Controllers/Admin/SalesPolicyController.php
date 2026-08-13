<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesPolicyRule;
use App\Models\SalesPolicySection;
use App\Models\SalesPolicySettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesPolicyController extends Controller
{
    public function index(): View
    {
        $settings = SalesPolicySettings::current();
        $sections = SalesPolicySection::query()
            ->with('rules')
            ->ordered()
            ->get();

        return view('admin.sales.policy.index', compact('settings', 'sections'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'version' => ['required', 'string', 'max:20'],
            'effective_date' => ['nullable', 'date'],
            'document_title' => ['required', 'string', 'max:255'],
            'document_title_en' => ['nullable', 'string', 'max:255'],
            'intro_content' => ['nullable', 'string'],
            'acknowledgement_content' => ['nullable', 'string'],
        ]);

        SalesPolicySettings::current()->update($validated);

        return back()->with('success', 'تم تحديث بيانات الدليل.');
    }

    public function storeSection(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'rules_range' => ['nullable', 'string', 'max:32'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        SalesPolicySection::create([
            ...$validated,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => true,
        ]);

        return back()->with('success', 'تم إضافة القسم.');
    }

    public function updateSection(Request $request, SalesPolicySection $section): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'rules_range' => ['nullable', 'string', 'max:32'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $section->update([
            ...$validated,
            'sort_order' => (int) ($validated['sort_order'] ?? $section->sort_order),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'تم تحديث القسم.');
    }

    public function destroySection(SalesPolicySection $section): RedirectResponse
    {
        $section->delete();

        return back()->with('success', 'تم حذف القسم وقواعده.');
    }

    public function createRule(): View
    {
        return view('admin.sales.policy.rule-form', [
            'rule' => new SalesPolicyRule(['is_active' => true]),
            'sections' => SalesPolicySection::ordered()->get(),
        ]);
    }

    public function storeRule(Request $request): RedirectResponse
    {
        $validated = $this->validateRule($request);
        SalesPolicyRule::create($validated);

        return redirect()->route('admin.sales.policy.index')->with('success', 'تم إضافة القاعدة.');
    }

    public function editRule(SalesPolicyRule $rule): View
    {
        return view('admin.sales.policy.rule-form', [
            'rule' => $rule,
            'sections' => SalesPolicySection::ordered()->get(),
        ]);
    }

    public function updateRule(Request $request, SalesPolicyRule $rule): RedirectResponse
    {
        $rule->update($this->validateRule($request));

        return redirect()->route('admin.sales.policy.index')->with('success', 'تم تحديث القاعدة.');
    }

    public function destroyRule(SalesPolicyRule $rule): RedirectResponse
    {
        $rule->delete();

        return back()->with('success', 'تم حذف القاعدة.');
    }

    /** @return array<string, mixed> */
    private function validateRule(Request $request): array
    {
        $validated = $request->validate([
            'section_id' => ['required', 'integer', 'exists:sales_policy_sections,id'],
            'rule_number' => ['nullable', 'string', 'max:32'],
            'title' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active', true);

        return $validated;
    }
}
