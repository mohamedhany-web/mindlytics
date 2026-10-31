<?php

namespace App\Domains\Sales\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Domains\Sales\Models\SalesPolicySection;
use App\Domains\Sales\Models\SalesPolicySettings;
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

        return view('sales::employee.policy.index', compact('settings', 'sections'));
    }
}
