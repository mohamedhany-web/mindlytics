<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesPolicySection;
use App\Models\SalesPolicySettings;
use Illuminate\View\View;

class SalesPolicyController extends Controller
{
    public function index(): View
    {
        $settings = SalesPolicySettings::current();
        $sections = SalesPolicySection::query()
            ->active()
            ->ordered()
            ->with(['activeRules'])
            ->get()
            ->filter(fn ($s) => $s->activeRules->isNotEmpty());

        return view('employee.sales.policy.index', compact('settings', 'sections'));
    }
}
