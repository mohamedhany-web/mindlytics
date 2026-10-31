<?php

namespace App\Domains\Sales\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Domains\Sales\Models\SalesKpiTarget;
use App\Domains\Sales\Services\SalesDailyResultService;
use App\Domains\Sales\Services\SalesKpiService;
use Illuminate\Support\Facades\Auth;

class SalesKpiController extends Controller
{
    public function index(SalesKpiService $kpi, SalesDailyResultService $dailyResults)
    {
        $user = Auth::user();
        $report = $kpi->buildReport($user);
        $sosToday = $dailyResults->comparisonFor($user, today());
        $yearMonth = now()->format('Y-m');
        $hasCustomTargets = SalesKpiTarget::query()
            ->where('user_id', $user->id)
            ->where('year_month', $yearMonth)
            ->exists();

        return view('sales::employee.kpi.index', compact('report', 'sosToday', 'hasCustomTargets', 'yearMonth'));
    }
}
