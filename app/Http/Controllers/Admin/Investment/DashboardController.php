<?php

namespace App\Http\Controllers\Admin\Investment;

use App\Http\Controllers\Controller;
use App\Models\InvestmentInquiry;
use App\Models\InvestmentPlan;
use App\Services\Investment\InvestmentStatsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, InvestmentStatsService $stats): View
    {
        $overview = $stats->overview();

        $recentInquiries = InvestmentInquiry::query()
            ->with('plan:id,title')
            ->latest()
            ->limit(8)
            ->get();

        $plansQuery = InvestmentPlan::query()
            ->withCount('inquiries')
            ->orderBy('sort_order')
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $plansQuery->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('slug', 'like', "%{$s}%");
            });
        }

        if ($request->status === 'active') {
            $plansQuery->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $plansQuery->where('is_active', false);
        }

        $plans = $plansQuery->paginate(12)->withQueryString();

        return view('admin.investment.dashboard.index', compact(
            'overview',
            'recentInquiries',
            'plans',
        ));
    }
}
