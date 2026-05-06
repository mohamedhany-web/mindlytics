<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class AccountingInsightsController extends Controller
{
    public function index(): View
    {
        $payload = $this->buildPayload(Carbon::now());

        return view('admin.accounting.insights', [
            'initialPayload' => $payload,
        ]);
    }

    public function metrics(Request $request): JsonResponse
    {
        return response()->json($this->buildPayload(Carbon::now()));
    }

    /**
     * @return array{snapshot: array<string,mixed>, trend: array<string,mixed>, daily: array<string,mixed>, realtime: array<string,mixed>, health: array<string,mixed>}
     */
    private function buildPayload(Carbon $now): array
    {
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();

        $yesterdayStart = $now->copy()->subDay()->startOfDay();
        $yesterdayEnd = $now->copy()->subDay()->endOfDay();

        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $prevMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $prevMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();

        $revenueToday = $this->revenueBetween($todayStart, $todayEnd);
        $revenueYesterday = $this->revenueBetween($yesterdayStart, $yesterdayEnd);
        $revenueMonth = $this->revenueBetween($monthStart, $monthEnd);
        $revenuePrevMonth = $this->revenueBetween($prevMonthStart, $prevMonthEnd);

        $expensesToday = $this->expensesBetween($todayStart, $todayEnd);
        $expensesYesterday = $this->expensesBetween($yesterdayStart, $yesterdayEnd);
        $expensesMonth = $this->expensesBetween($monthStart, $monthEnd);
        $expensesPrevMonth = $this->expensesBetween($prevMonthStart, $prevMonthEnd);

        $netToday = round($revenueToday - $expensesToday, 2);
        $netYesterday = round($revenueYesterday - $expensesYesterday, 2);
        $netMonth = round($revenueMonth - $expensesMonth, 2);
        $netPrevMonth = round($revenuePrevMonth - $expensesPrevMonth, 2);

        $trend = [
            'revenue_today_pct' => $this->pctDelta($revenueYesterday, $revenueToday),
            'expenses_today_pct' => $this->pctDelta($expensesYesterday, $expensesToday),
            'net_today_pct' => $this->pctDelta($netYesterday, $netToday),
            'revenue_month_pct' => $this->pctDelta($revenuePrevMonth, $revenueMonth),
            'expenses_month_pct' => $this->pctDelta($expensesPrevMonth, $expensesMonth),
            'net_month_pct' => $this->pctDelta($netPrevMonth, $netMonth),
        ];

        $seriesDays = 14;
        $daily = $this->dailySeries($now->copy()->subDays($seriesDays - 1)->startOfDay(), $now->copy()->endOfDay());

        $realtime = $this->realtimeCashflowSeries($now->copy()->subHours(6)->startOfMinute(), $now->copy()->endOfMinute(), 5);

        $cashIn = $this->cashInBetween($monthStart, $monthEnd);
        $cashOut = $this->cashOutBetween($monthStart, $monthEnd);

        $snapshot = [
            'as_of' => $now->format('Y-m-d H:i:s'),
            'revenue_today' => $revenueToday,
            'expenses_today' => $expensesToday,
            'net_today' => $netToday,
            'revenue_month' => $revenueMonth,
            'expenses_month' => $expensesMonth,
            'net_month' => $netMonth,
            'cash_in_month' => $cashIn,
            'cash_out_month' => $cashOut,
        ];

        $health = $this->healthLabel($netMonth, $trend['net_month_pct']);

        return compact('snapshot', 'trend', 'daily', 'realtime', 'health');
    }

    private function revenueBetween(Carbon $start, Carbon $end): float
    {
        return (float) Payment::query()
            ->where('status', 'completed')
            ->whereBetween('paid_at', [$start, $end])
            ->sum('amount');
    }

    private function expensesBetween(Carbon $start, Carbon $end): float
    {
        return (float) Expense::query()
            ->approved()
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');
    }

    private function cashInBetween(Carbon $start, Carbon $end): float
    {
        return (float) Transaction::query()
            ->where('type', 'credit')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');
    }

    private function cashOutBetween(Carbon $start, Carbon $end): float
    {
        return (float) Transaction::query()
            ->where('type', 'debit')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');
    }

    private function pctDelta(float $prev, float $cur): ?float
    {
        if (abs($prev) < 0.00001) {
            return $cur === 0.0 ? 0.0 : null;
        }

        return round((($cur - $prev) / $prev) * 100, 1);
    }

    /**
     * @return array{labels: list<string>, revenue: list<float>, expenses: list<float>, net: list<float>}
     */
    private function dailySeries(Carbon $start, Carbon $end): array
    {
        $labels = [];
        $rev = [];
        $exp = [];
        $net = [];

        $cursor = $start->copy()->startOfDay();
        while ($cursor->lte($end)) {
            $dStart = $cursor->copy()->startOfDay();
            $dEnd = $cursor->copy()->endOfDay();

            $r = $this->revenueBetween($dStart, $dEnd);
            $e = $this->expensesBetween($dStart, $dEnd);
            $n = round($r - $e, 2);

            $labels[] = $cursor->format('m/d');
            $rev[] = round($r, 2);
            $exp[] = round($e, 2);
            $net[] = $n;

            $cursor->addDay();
        }

        return [
            'labels' => $labels,
            'revenue' => $rev,
            'expenses' => $exp,
            'net' => $net,
        ];
    }

    /**
     * Series لحظي من المعاملات (Cash In/Out/Net) على فواصل دقائق.
     *
     * @return array{labels: list<string>, cash_in: list<float>, cash_out: list<float>, net: list<float>, bucket_minutes: int}
     */
    private function realtimeCashflowSeries(Carbon $start, Carbon $end, int $bucketMinutes = 5): array
    {
        $bucketMinutes = max(1, min(60, (int) $bucketMinutes));

        // MySQL: bucket time label HH:MM where MM floored to bucketMinutes
        $labelExpr = "CONCAT(LPAD(HOUR(created_at),2,'0'),':',LPAD(FLOOR(MINUTE(created_at)/{$bucketMinutes})*{$bucketMinutes},2,'0'))";

        $rows = Transaction::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw("$labelExpr as t, type, COALESCE(SUM(amount),0) as total")
            ->groupBy('t', 'type')
            ->orderBy('t')
            ->get();

        $mapIn = [];
        $mapOut = [];
        foreach ($rows as $r) {
            $t = (string) ($r->t ?? '');
            if ($t === '') continue;
            $total = (float) ($r->total ?? 0);
            if (($r->type ?? '') === 'credit') {
                $mapIn[$t] = ($mapIn[$t] ?? 0) + $total;
            } elseif (($r->type ?? '') === 'debit') {
                $mapOut[$t] = ($mapOut[$t] ?? 0) + $total;
            }
        }

        $labels = [];
        $cashIn = [];
        $cashOut = [];
        $net = [];

        $cursor = $start->copy();
        $cursor->second = 0;
        while ($cursor->lte($end)) {
            $label = $cursor->format('H:') . str_pad((string) (floor(((int) $cursor->format('i')) / $bucketMinutes) * $bucketMinutes), 2, '0', STR_PAD_LEFT);

            $in = (float) ($mapIn[$label] ?? 0);
            $out = (float) ($mapOut[$label] ?? 0);
            $labels[] = $label;
            $cashIn[] = round($in, 2);
            $cashOut[] = round($out, 2);
            $net[] = round($in - $out, 2);

            $cursor->addMinutes($bucketMinutes);
        }

        return [
            'labels' => $labels,
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'net' => $net,
            'bucket_minutes' => $bucketMinutes,
        ];
    }

    /**
     * @return array{label: string, tone: 'good'|'warn'|'bad'}
     */
    private function healthLabel(float $netMonth, ?float $netMonthPct): array
    {
        if ($netMonth < 0) {
            return ['label' => 'خسارة هذا الشهر', 'tone' => 'bad'];
        }

        if ($netMonthPct !== null && $netMonthPct < -15) {
            return ['label' => 'ربح لكن في تراجع', 'tone' => 'warn'];
        }

        return ['label' => 'ربح واتجاه صحي', 'tone' => 'good'];
    }
}

