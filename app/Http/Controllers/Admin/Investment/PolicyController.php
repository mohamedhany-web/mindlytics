<?php

namespace App\Http\Controllers\Admin\Investment;

use App\Http\Controllers\Controller;
use App\Models\InvestmentPolicy;
use App\Support\PlatformSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PolicyController extends Controller
{
    public function edit(): View
    {
        $policy = InvestmentPolicy::current();
        $platformContact = PlatformSettings::contactPage();

        return view('admin.investment.policies.edit', compact('policy', 'platformContact'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'overview' => 'nullable|string|max:20000',
            'eligibility_rules' => 'nullable|string|max:20000',
            'legal_framework' => 'nullable|string|max:20000',
            'terms_conditions' => 'nullable|string|max:20000',
            'privacy_notice' => 'nullable|string|max:20000',
            'process_description' => 'nullable|string|max:20000',
            'disclaimer' => 'nullable|string|max:10000',
            'contact_email' => 'nullable|email|max:200',
            'contact_phone' => 'nullable|string|max:30',
        ]);

        $policy = InvestmentPolicy::current();
        $policy->update(array_merge($validated, ['updated_by' => auth()->id()]));

        return back()->with('success', 'تم حفظ الإطار القانوني والسياسات.');
    }
}
