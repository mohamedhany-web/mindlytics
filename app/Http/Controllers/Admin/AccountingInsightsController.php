<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\OfflineCourseEnrollment;
use App\Support\AccountingAnalytics;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AccountingInsightsController extends Controller
{
    public function index(): View
    {
        return view('admin.accounting.insights', [
            'initialPayload' => $this->buildPayload(Carbon::now()),
        ]);
    }

    public function metrics(): JsonResponse
    {
        try {
            return response()->json($this->buildPayload(Carbon::now()));
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'تعذر حساب المؤشرات: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * @return array<string, mixed>
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

        $revenueToday = AccountingAnalytics::revenueBetween($todayStart, $todayEnd);
        $revenueYesterday = AccountingAnalytics::revenueBetween($yesterdayStart, $yesterdayEnd);
        $revenueMonth = AccountingAnalytics::revenueBetween($monthStart, $monthEnd);
        $revenuePrevMonth = AccountingAnalytics::revenueBetween($prevMonthStart, $prevMonthEnd);

        $expensesToday = AccountingAnalytics::expensesBetween($todayStart, $todayEnd);
        $expensesYesterday = AccountingAnalytics::expensesBetween($yesterdayStart, $yesterdayEnd);
        $expensesMonth = AccountingAnalytics::expensesBetween($monthStart, $monthEnd);
        $expensesPrevMonth = AccountingAnalytics::expensesBetween($prevMonthStart, $prevMonthEnd);

        $expensesMonthRevenue = AccountingAnalytics::expensesBetween($monthStart, $monthEnd, AccountingAnalytics::FUNDING_REVENUE);
        $expensesMonthPocket = AccountingAnalytics::expensesBetween($monthStart, $monthEnd, AccountingAnalytics::FUNDING_OUT_OF_POCKET);

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

        $dailyRaw = AccountingAnalytics::dailySeries(
            $now->copy()->subDays(13)->startOfDay(),
            $now->copy()->endOfDay()
        );

        $daily = [
            'labels' => $dailyRaw['labels'],
            'revenue' => $dailyRaw['revenue'],
            'expenses' => array_map(fn ($i) => round(($dailyRaw['expenses_revenue'][$i] ?? 0) + ($dailyRaw['expenses_pocket'][$i] ?? 0), 2), array_keys($dailyRaw['labels'])),
            'expenses_revenue' => $dailyRaw['expenses_revenue'],
            'expenses_pocket' => $dailyRaw['expenses_pocket'],
            'net' => $dailyRaw['net'],
        ];

        $realtime = AccountingAnalytics::realtimeCashflowSeries(
            $now->copy()->subHours(6)->startOfMinute(),
            $now->copy()->endOfMinute(),
            5
        );

        $breakEvenMonth = AccountingAnalytics::breakEvenAnalysis($monthStart, $monthEnd);
        $breakEvenAllTime = AccountingAnalytics::breakEvenAnalysis($monthStart, $monthEnd, true);
        $receivables = AccountingAnalytics::receivablesSnapshot();

        $expensesByFundingMonth = Expense::query()
            ->approved()
            ->whereBetween('expense_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->selectRaw('funding_source, COUNT(*) as count, COALESCE(SUM(amount),0) as total')
            ->groupBy('funding_source')
            ->get()
            ->keyBy('funding_source');

        $expensesByCategoryMonth = Expense::query()
            ->approved()
            ->whereBetween('expense_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->selectRaw('category, COUNT(*) as count, COALESCE(SUM(amount),0) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $snapshot = [
            'as_of' => $now->format('Y-m-d H:i:s'),
            'revenue_today' => $revenueToday,
            'expenses_today' => $expensesToday,
            'expenses_today_revenue' => AccountingAnalytics::expensesBetween($todayStart, $todayEnd, AccountingAnalytics::FUNDING_REVENUE),
            'expenses_today_pocket' => AccountingAnalytics::expensesBetween($todayStart, $todayEnd, AccountingAnalytics::FUNDING_OUT_OF_POCKET),
            'net_today' => $netToday,
            'revenue_month' => $revenueMonth,
            'expenses_month' => $expensesMonth,
            'expenses_month_revenue' => $expensesMonthRevenue,
            'expenses_month_pocket' => $expensesMonthPocket,
            'net_month' => $netMonth,
            'operational_net_month' => round($revenueMonth - $expensesMonthRevenue, 2),
            'pending_invoices_amount' => (float) Invoice::whereIn('status', ['pending', 'overdue'])->sum('total_amount'),
            'offline_outstanding' => (float) OfflineCourseEnrollment::query()->sum('remaining_amount'),
        ];

        $health = $this->healthFromBreakEven($breakEvenMonth, $netMonth, $trend['net_month_pct']);

        return [
            'snapshot' => $snapshot,
            'trend' => $trend,
            'daily' => $daily,
            'realtime' => $realtime,
            'health' => $health,
            'break_even_month' => $breakEvenMonth,
            'break_even_all_time' => $breakEvenAllTime,
            'receivables' => $receivables,
            'expenses_by_funding' => $expensesByFundingMonth,
            'expenses_by_category' => $expensesByCategoryMonth,
        ];
    }

    private function pctDelta(float $prev, float $cur): ?float
    {
        if (abs($prev) < 0.00001) {
            return $cur === 0.0 ? 0.0 : null;
        }

        return round((($cur - $prev) / $prev) * 100, 1);
    }

    /**
     * @return array{label: string, tone: 'good'|'warn'|'bad', detail?: string}
     */
    private function healthFromBreakEven(array $breakEven, float $netMonth, ?float $netMonthPct): array
    {
        if ($breakEven['reached_full_safety'] ?? false) {
            return [
                'label' => $breakEven['label'],
                'tone' => 'good',
                'detail' => $breakEven['detail'] ?? '',
            ];
        }

        if ($breakEven['reached_operational_breakeven'] ?? false) {
            return [
                'label' => $breakEven['label'],
                'tone' => ($breakEven['expenses_out_of_pocket'] ?? 0) > 0 ? 'warn' : 'good',
                'detail' => $breakEven['detail'] ?? '',
            ];
        }

        if ($netMonth < 0) {
            return [
                'label' => 'خسارة هذا الشهر — لم تصل لبر الأمان',
                'tone' => 'bad',
                'detail' => $breakEven['detail'] ?? '',
            ];
        }

        if ($netMonthPct !== null && $netMonthPct < -15) {
            return ['label' => 'ربح لكن في تراجع', 'tone' => 'warn', 'detail' => ''];
        }

        return ['label' => 'ربح جزئي — راقب المصروفات', 'tone' => 'warn', 'detail' => ''];
    }
}
