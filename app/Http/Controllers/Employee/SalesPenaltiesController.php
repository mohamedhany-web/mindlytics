<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\SalesDailyKpiPenaltyService;
use App\Services\SalesDailyResultService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesPenaltiesController extends Controller
{
    public function index(
        Request $request,
        SalesDailyKpiPenaltyService $penalties,
        SalesDailyResultService $dailyResults,
    ): View {
        $user = Auth::user();
        abort_unless($user && $user->isSalesStaff(), 403);

        $month = $request->get('month', now()->format('Y-m'));
        if (! preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = now()->format('Y-m');
        }

        $from = Carbon::createFromFormat('Y-m-d', $month.'-01')->startOfMonth();
        $to = $from->copy()->endOfMonth();
        if ($from->isCurrentMonth()) {
            $to = now();
        }

        $hub = $penalties->employeeDeductionsHub($user, $from, $to);
        $sosToday = $dailyResults->comparisonFor($user, today());
        $threshold = $penalties->thresholdPct();

        return view('employee.sales.penalties.index', [
            'hub' => $hub,
            'sosToday' => $sosToday,
            'month' => $month,
            'threshold' => $threshold,
            'penaltyEnabled' => $penalties->enabled(),
            'chargeable' => $penalties->chargeableMetrics(),
        ]);
    }
}
