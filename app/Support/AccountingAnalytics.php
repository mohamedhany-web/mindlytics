<?php

namespace App\Support;

use App\Models\Expense;
use App\Models\InstallmentPayment;
use App\Models\Invoice;
use App\Models\OfflineCourseEnrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\WithdrawalRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AccountingAnalytics
{
    public const FUNDING_REVENUE = 'revenue';

    public const FUNDING_OUT_OF_POCKET = 'out_of_pocket';

    public static function fundingSourceLabels(): array
    {
        return [
            self::FUNDING_REVENUE => 'من إيرادات الكورسات / المحفظة',
            self::FUNDING_OUT_OF_POCKET => 'من جيب الشركة (تمويل ذاتي)',
        ];
    }

    public static function fundingSourceLabel(?string $source): string
    {
        return self::fundingSourceLabels()[$source] ?? 'غير محدد';
    }

    public static function inferFundingSource(?int $walletId, string $paymentMethod, ?string $requested = null): string
    {
        if (in_array($requested, [self::FUNDING_REVENUE, self::FUNDING_OUT_OF_POCKET], true)) {
            return $requested;
        }

        if ($walletId) {
            return self::FUNDING_REVENUE;
        }

        if (in_array($paymentMethod, ['cash', 'other'], true)) {
            return self::FUNDING_OUT_OF_POCKET;
        }

        return self::FUNDING_REVENUE;
    }

    public static function revenueBetween(Carbon $start, Carbon $end): float
    {
        return (float) Payment::query()
            ->where('status', 'completed')
            ->whereBetween('paid_at', [$start, $end])
            ->sum('amount');
    }

    public static function expensesBetween(Carbon $start, Carbon $end, ?string $fundingSource = null): float
    {
        $q = Expense::query()
            ->approved()
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()]);

        if ($fundingSource) {
            $q->where('funding_source', $fundingSource);
        }

        return (float) $q->sum('amount');
    }

    /**
     * @return array{
     *     revenue: float,
     *     expenses_from_revenue: float,
     *     expenses_out_of_pocket: float,
     *     expenses_total: float,
     *     operational_net: float,
     *     true_net: float,
     *     reached_operational_breakeven: bool,
     *     reached_full_safety: bool,
     *     gap_to_breakeven: float,
     *     pocket_ratio_pct: float|null,
     *     label: string,
     *     tone: string,
     *     detail: string
     * }
     */
    public static function breakEvenAnalysis(Carbon $start, Carbon $end, bool $allTime = false): array
    {
        if ($allTime) {
            $start = Carbon::parse('2020-01-01')->startOfDay();
            $end = Carbon::now()->endOfDay();
        }

        $revenue = self::revenueBetween($start, $end);
        $fromRevenue = self::expensesBetween($start, $end, self::FUNDING_REVENUE);
        $outOfPocket = self::expensesBetween($start, $end, self::FUNDING_OUT_OF_POCKET);
        $totalExpenses = $fromRevenue + $outOfPocket;
        $operationalNet = round($revenue - $fromRevenue, 2);
        $trueNet = round($revenue - $totalExpenses, 2);

        $reachedOperational = $operationalNet >= 0;
        $reachedFull = $trueNet >= 0;
        $gap = round(max(0, $fromRevenue - $revenue), 2);
        $pocketRatio = $totalExpenses > 0 ? round(($outOfPocket / $totalExpenses) * 100, 1) : null;

        if ($reachedFull && $outOfPocket <= 0) {
            $label = 'وصلت لبر الأمان';
            $tone = 'good';
            $detail = 'الإيرادات تغطي كل المصروفات دون الحاجة لتمويل من جيب الشركة.';
        } elseif ($reachedOperational && $outOfPocket > 0) {
            $label = 'تشغيلياً آمن — مع تمويل ذاتي';
            $tone = 'warn';
            $detail = 'إيرادات الكورسات تغطي مصروفاتها، لكن هناك '.number_format($outOfPocket, 2).' ج.م دُفعت من جيب الشركة.';
        } elseif ($reachedOperational) {
            $label = 'وصلت لبر الأمان التشغيلي';
            $tone = 'good';
            $detail = 'إيرادات الفترة تغطي المصروفات الممولة من الإيراد.';
        } else {
            $label = 'لم تصل لبر الأمان بعد';
            $tone = 'bad';
            $detail = 'ينقص '.number_format($gap, 2).' ج.م إيراد لتغطية مصروفات التشغيل في هذه الفترة.';
        }

        return [
            'revenue' => $revenue,
            'expenses_from_revenue' => $fromRevenue,
            'expenses_out_of_pocket' => $outOfPocket,
            'expenses_total' => $totalExpenses,
            'operational_net' => $operationalNet,
            'true_net' => $trueNet,
            'reached_operational_breakeven' => $reachedOperational,
            'reached_full_safety' => $reachedFull,
            'gap_to_breakeven' => $gap,
            'pocket_ratio_pct' => $pocketRatio,
            'label' => $label,
            'tone' => $tone,
            'detail' => $detail,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function receivablesSnapshot(): array
    {
        $pendingInvoices = (float) Invoice::whereIn('status', ['pending', 'overdue'])->sum('total_amount');
        $pendingInvoicesCount = Invoice::whereIn('status', ['pending', 'overdue'])->count();

        $offlineRemaining = (float) OfflineCourseEnrollment::query()->sum('remaining_amount');
        $offlineCount = OfflineCourseEnrollment::query()->where('remaining_amount', '>', 0)->count();

        $installmentPending = (float) InstallmentPayment::where('status', InstallmentPayment::STATUS_PENDING)->sum('amount');
        $installmentOverdue = (float) InstallmentPayment::where('status', InstallmentPayment::STATUS_OVERDUE)->sum('amount');

        $ordersPending = (float) Order::where('status', Order::STATUS_PENDING)->sum('amount');
        $ordersPendingCount = Order::where('status', Order::STATUS_PENDING)->count();

        $manualPayable = 0.0;
        $manualReceivable = 0.0;
        if (
            class_exists(\App\Models\AccountingDebt::class)
            && \Illuminate\Support\Facades\Schema::hasTable('accounting_debts')
        ) {
            $manualPayable = (float) \App\Models\AccountingDebt::query()
                ->where('direction', \App\Models\AccountingDebt::DIRECTION_PAYABLE)
                ->whereIn('status', ['active', 'partial'])
                ->sum('remaining_amount');
            $manualReceivable = (float) \App\Models\AccountingDebt::query()
                ->where('direction', \App\Models\AccountingDebt::DIRECTION_RECEIVABLE)
                ->whereIn('status', ['active', 'partial'])
                ->sum('remaining_amount');
        }

        $totalReceivable = $pendingInvoices + $offlineRemaining + $installmentPending + $installmentOverdue + $manualReceivable;

        $withdrawalsPending = (float) WithdrawalRequest::where('status', WithdrawalRequest::STATUS_PENDING)->sum('amount');
        $withdrawalsCount = WithdrawalRequest::where('status', WithdrawalRequest::STATUS_PENDING)->count();

        $pocketExpensesTotal = (float) Expense::approved()
            ->where('funding_source', self::FUNDING_OUT_OF_POCKET)
            ->sum('amount');

        return [
            'receivables' => [
                'invoices_amount' => $pendingInvoices,
                'invoices_count' => $pendingInvoicesCount,
                'offline_remaining' => $offlineRemaining,
                'offline_count' => $offlineCount,
                'installments_pending' => $installmentPending,
                'installments_overdue' => $installmentOverdue,
                'orders_pending' => $ordersPending,
                'orders_pending_count' => $ordersPendingCount,
                'manual_debts_receivable' => $manualReceivable,
                'total' => $totalReceivable,
            ],
            'payables' => [
                'withdrawals_pending' => $withdrawalsPending,
                'withdrawals_count' => $withdrawalsCount,
                'founder_injections' => $pocketExpensesTotal,
                'manual_debts_payable' => $manualPayable,
                'total' => $withdrawalsPending + $pocketExpensesTotal + $manualPayable,
            ],
        ];
    }

    /**
     * @return array{labels: list<string>, revenue: list<float>, expenses_revenue: list<float>, expenses_pocket: list<float>, net: list<float>}
     */
    public static function dailySeries(Carbon $start, Carbon $end): array
    {
        $labels = [];
        $rev = [];
        $expRev = [];
        $expPocket = [];
        $net = [];

        $cursor = $start->copy()->startOfDay();
        while ($cursor->lte($end)) {
            $dStart = $cursor->copy()->startOfDay();
            $dEnd = $cursor->copy()->endOfDay();

            $r = self::revenueBetween($dStart, $dEnd);
            $eRev = self::expensesBetween($dStart, $dEnd, self::FUNDING_REVENUE);
            $ePocket = self::expensesBetween($dStart, $dEnd, self::FUNDING_OUT_OF_POCKET);

            $labels[] = $cursor->format('m/d');
            $rev[] = round($r, 2);
            $expRev[] = round($eRev, 2);
            $expPocket[] = round($ePocket, 2);
            $net[] = round($r - $eRev - $ePocket, 2);

            $cursor->addDay();
        }

        return compact('labels', 'rev', 'expRev', 'expPocket', 'net') + [
            'revenue' => $rev,
            'expenses_revenue' => $expRev,
            'expenses_pocket' => $expPocket,
        ];
    }

    /**
     * @return array{labels: list<string>, cash_in: list<float>, cash_out: list<float>, net: list<float>, bucket_minutes: int}
     */
    public static function realtimeCashflowSeries(Carbon $start, Carbon $end, int $bucketMinutes = 5): array
    {
        $bucketMinutes = max(1, min(60, $bucketMinutes));
        $mapIn = [];
        $mapOut = [];

        $rows = DB::table('transactions')
            ->whereBetween('created_at', [$start, $end])
            ->select(['created_at', 'type', 'amount'])
            ->orderBy('created_at')
            ->get();

        foreach ($rows as $row) {
            $cursor = Carbon::parse($row->created_at)->second(0);
            $minuteBucket = (int) (floor((int) $cursor->format('i') / $bucketMinutes) * $bucketMinutes);
            $label = sprintf('%02d:%02d', (int) $cursor->format('H'), $minuteBucket);
            $amount = (float) ($row->amount ?? 0);

            if (($row->type ?? '') === 'credit') {
                $mapIn[$label] = ($mapIn[$label] ?? 0) + $amount;
            } elseif (($row->type ?? '') === 'debit') {
                $mapOut[$label] = ($mapOut[$label] ?? 0) + $amount;
            }
        }

        $labels = [];
        $cashIn = [];
        $cashOut = [];
        $net = [];

        $cursor = $start->copy()->second(0);
        while ($cursor->lte($end)) {
            $minuteBucket = (int) (floor((int) $cursor->format('i') / $bucketMinutes) * $bucketMinutes);
            $label = sprintf('%02d:%02d', (int) $cursor->format('H'), $minuteBucket);

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
}
