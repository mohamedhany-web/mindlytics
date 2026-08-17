<?php

namespace App\Support;

use App\Models\Expense;
use App\Models\InstallmentPayment;
use App\Models\Invoice;
use App\Models\OfflineCourseEnrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\StudentCourseEnrollment;
use App\Models\Wallet;
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
                'founder_injections' => 0.0,
                'manual_debts_payable' => $manualPayable,
                'total' => $withdrawalsPending + $manualPayable,
            ],
            'equity' => [
                'founder_capital' => $pocketExpensesTotal,
            ],
        ];
    }

    /**
     * نسبة التغيّر الأفقي (Horizontal). تُرجع null عند عدم إمكانية القياس (n.m.).
     */
    public static function pctChange(?float $prior, float $current): ?float
    {
        if ($prior === null) {
            return null;
        }

        if (abs($prior) < 0.0000001) {
            return abs($current) < 0.0000001 ? 0.0 : null;
        }

        return round((($current - $prior) / abs($prior)) * 100, 1);
    }

    /**
     * التحليل الرأسي (common-size) كنسبة من قاعدة (مبيعات أو أصول).
     */
    public static function verticalPct(float $amount, float $base): ?float
    {
        if (abs($base) < 0.0000001) {
            return null;
        }

        return round(($amount / $base) * 100, 1);
    }

    /**
     * قائمة الدخل وفق إطار التقارير المالية:
     * صافي المبيعات → تكلفة الخدمة → مجمل الربح → مصروفات البيع والتشغيل (شاملة عمولات البوابات) → صافي الدخل.
     *
     * @return array<string, mixed>
     */
    public static function incomeStatement(Carbon $start, Carbon $end): array
    {
        $netSales = round(self::revenueBetween($start, $end), 2);
        $gatewayFees = round((float) Payment::query()
            ->where('status', 'completed')
            ->whereBetween('paid_at', [$start, $end])
            ->sum('gateway_fee_amount'), 2);

        $byCategory = Expense::query()
            ->approved()
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('category, COALESCE(SUM(amount), 0) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->map(fn ($v) => (float) $v)
            ->all();

        $salaries = round((float) ($byCategory['salaries'] ?? 0), 2);
        $marketing = round((float) ($byCategory['marketing'] ?? 0), 2);
        $operational = round((float) ($byCategory['operational'] ?? 0), 2);
        $utilities = round((float) ($byCategory['utilities'] ?? 0), 2);
        $equipment = round((float) ($byCategory['equipment'] ?? 0), 2);
        $maintenance = round((float) ($byCategory['maintenance'] ?? 0), 2);
        $other = round((float) ($byCategory['other'] ?? 0), 2);

        $classified = ['salaries', 'marketing', 'operational', 'utilities', 'equipment', 'maintenance', 'other'];
        foreach ($byCategory as $category => $amount) {
            if ($category === '' || $category === null) {
                $other = round($other + (float) $amount, 2);

                continue;
            }
            if (! in_array((string) $category, $classified, true)) {
                $other = round($other + (float) $amount, 2);
            }
        }

        $instructorCost = round((float) WithdrawalRequest::query()
            ->where('status', WithdrawalRequest::STATUS_COMPLETED)
            ->whereBetween('processed_at', [$start, $end])
            ->sum('amount'), 2);

        $expensesRecorded = round($salaries + $marketing + $operational + $utilities + $equipment + $maintenance + $other, 2);
        $cogs = round($salaries + $instructorCost, 2);
        $selling = round($marketing + $gatewayFees, 2);
        $opex = round($operational + $utilities + $equipment + $maintenance + $other, 2);
        $grossProfit = round($netSales - $cogs, 2);
        $operatingProfit = round($grossProfit - $selling - $opex, 2);
        $netIncome = $operatingProfit;

        $lines = [
            ['key' => 'net_sales', 'label' => 'صافي المبيعات (تحصيلات)', 'amount' => $netSales, 'nature' => 'income', 'emphasis' => false],
            ['key' => 'cogs', 'label' => 'تكلفة الخدمة المباشرة', 'amount' => $cogs, 'nature' => 'cost', 'emphasis' => false],
            ['key' => 'salaries', 'label' => 'رواتب', 'amount' => $salaries, 'nature' => 'cost', 'indent' => 1],
            ['key' => 'instructor_withdrawals', 'label' => 'سحوبات مدربين مكتملة', 'amount' => $instructorCost, 'nature' => 'cost', 'indent' => 1],
            ['key' => 'gross_profit', 'label' => 'مجمل الربح', 'amount' => $grossProfit, 'nature' => 'income', 'emphasis' => true],
            ['key' => 'selling', 'label' => 'مصروفات البيع', 'amount' => $selling, 'nature' => 'cost', 'emphasis' => false],
            ['key' => 'marketing', 'label' => 'تسويق', 'amount' => $marketing, 'nature' => 'cost', 'indent' => 1],
            ['key' => 'gateway_fees', 'label' => 'عمولات بوابات الدفع', 'amount' => $gatewayFees, 'nature' => 'cost', 'indent' => 1],
            ['key' => 'opex', 'label' => 'مصروفات تشغيلية', 'amount' => $opex, 'nature' => 'cost', 'emphasis' => false],
            ['key' => 'operational', 'label' => 'تشغيلي', 'amount' => $operational, 'nature' => 'cost', 'indent' => 1],
            ['key' => 'utilities', 'label' => 'مرافق', 'amount' => $utilities, 'nature' => 'cost', 'indent' => 1],
            ['key' => 'equipment', 'label' => 'معدات', 'amount' => $equipment, 'nature' => 'cost', 'indent' => 1],
            ['key' => 'maintenance', 'label' => 'صيانة', 'amount' => $maintenance, 'nature' => 'cost', 'indent' => 1],
            ['key' => 'other', 'label' => 'أخرى', 'amount' => $other, 'nature' => 'cost', 'indent' => 1],
            ['key' => 'operating_profit', 'label' => 'الربح التشغيلي (EBIT)', 'amount' => $operatingProfit, 'nature' => 'income', 'emphasis' => true],
            ['key' => 'ebitda', 'label' => 'EBITDA (≈ EBIT — لا إهلاك منفصل)', 'amount' => $operatingProfit, 'nature' => 'income', 'emphasis' => false],
            ['key' => 'net_income', 'label' => 'صافي الدخل', 'amount' => $netIncome, 'nature' => 'income', 'emphasis' => true],
        ];

        return [
            'net_sales' => $netSales,
            'cogs' => $cogs,
            'salaries' => $salaries,
            'instructor_withdrawals' => $instructorCost,
            'gross_profit' => $grossProfit,
            'selling' => $selling,
            'marketing' => $marketing,
            'gateway_fees' => $gatewayFees,
            'opex' => $opex,
            'operational' => $operational,
            'utilities' => $utilities,
            'equipment' => $equipment,
            'maintenance' => $maintenance,
            'other' => $other,
            'operating_profit' => $operatingProfit,
            'ebitda' => $operatingProfit,
            'net_income' => $netIncome,
            'expenses_recorded' => $expensesRecorded,
            'lines' => $lines,
        ];
    }

    /**
     * لقطة المركز المالي (أصول متداولة / خصوم متداولة / رأس مال المؤسسين).
     * تمويل الجيب يُصنَّف حقوق ملكية وليس دائناً.
     *
     * @return array<string, mixed>
     */
    public static function positionSnapshot(): array
    {
        $cash = round((float) Wallet::academyWallets()->sum('balance'), 2);
        $snap = self::receivablesSnapshot();
        $receivables = round((float) ($snap['receivables']['total'] ?? 0), 2);
        $currentAssets = round($cash + $receivables, 2);
        $withdrawalsPending = round((float) ($snap['payables']['withdrawals_pending'] ?? 0), 2);
        $manualPayables = round((float) ($snap['payables']['manual_debts_payable'] ?? 0), 2);
        $currentLiabilities = round((float) ($snap['payables']['total'] ?? 0), 2);
        $founderCapital = round((float) ($snap['equity']['founder_capital'] ?? 0), 2);
        $nwc = round($currentAssets - $currentLiabilities, 2);
        $nonCurrentAssets = 0.0;
        $nonCurrentLiabilities = 0.0;
        $totalAssets = round($nonCurrentAssets + $currentAssets, 2);
        $totalLiabilities = round($nonCurrentLiabilities + $currentLiabilities, 2);
        $accumulatedSurplus = round($nwc - $founderCapital, 2);
        $totalEquity = $nwc;
        $totalLiabilitiesAndEquity = round($totalLiabilities + $totalEquity, 2);

        $lines = [
            ['key' => 'non_current_assets', 'label' => 'إجمالي الأصول غير المتداولة', 'amount' => $nonCurrentAssets, 'group' => 'asset', 'emphasis' => false],
            ['key' => 'cash', 'label' => 'النقدية وما في حكمها (محافظ الأكاديمية)', 'amount' => $cash, 'group' => 'asset'],
            ['key' => 'receivables', 'label' => 'الذمم المدينة', 'amount' => $receivables, 'group' => 'asset'],
            ['key' => 'current_assets', 'label' => 'إجمالي الأصول المتداولة', 'amount' => $currentAssets, 'group' => 'asset', 'emphasis' => true],
            ['key' => 'total_assets', 'label' => 'إجمالي الأصول', 'amount' => $totalAssets, 'group' => 'asset', 'emphasis' => true],
            ['key' => 'founder_capital', 'label' => 'رأس مال المؤسسين (تمويل من الجيب)', 'amount' => $founderCapital, 'group' => 'equity'],
            ['key' => 'accumulated_surplus', 'label' => 'فائض / (عجز) متراكم', 'amount' => $accumulatedSurplus, 'group' => 'equity'],
            ['key' => 'total_equity', 'label' => 'إجمالي حقوق الملكية', 'amount' => $totalEquity, 'group' => 'equity', 'emphasis' => true],
            ['key' => 'non_current_liabilities', 'label' => 'إجمالي الخصوم غير المتداولة', 'amount' => $nonCurrentLiabilities, 'group' => 'liability'],
            ['key' => 'withdrawals_pending', 'label' => 'سحوبات مدربين معلّقة', 'amount' => $withdrawalsPending, 'group' => 'liability'],
            ['key' => 'manual_payables', 'label' => 'ديون مستلفة مسجّلة', 'amount' => $manualPayables, 'group' => 'liability'],
            ['key' => 'current_liabilities', 'label' => 'إجمالي الخصوم المتداولة', 'amount' => $currentLiabilities, 'group' => 'liability', 'emphasis' => true],
            ['key' => 'total_liabilities', 'label' => 'إجمالي الخصوم', 'amount' => $totalLiabilities, 'group' => 'liability', 'emphasis' => true],
            ['key' => 'total_liabilities_and_equity', 'label' => 'إجمالي الخصوم وحقوق الملكية', 'amount' => $totalLiabilitiesAndEquity, 'group' => 'total', 'emphasis' => true],
            ['key' => 'net_working_capital', 'label' => 'صافي رأس المال العامل', 'amount' => $nwc, 'group' => 'equity', 'emphasis' => true],
        ];

        $vertical = [];
        foreach ($lines as $line) {
            $vertical[] = [
                'key' => $line['key'],
                'label' => $line['label'],
                'amount' => $line['amount'],
                'group' => $line['group'],
                'emphasis' => (bool) ($line['emphasis'] ?? false),
                'pct_of_assets' => self::verticalPct((float) $line['amount'], $totalAssets),
            ];
        }

        return [
            'cash' => $cash,
            'receivables' => $receivables,
            'non_current_assets' => $nonCurrentAssets,
            'current_assets' => $currentAssets,
            'total_assets' => $totalAssets,
            'withdrawals_pending' => $withdrawalsPending,
            'manual_payables' => $manualPayables,
            'non_current_liabilities' => $nonCurrentLiabilities,
            'current_liabilities' => $currentLiabilities,
            'total_liabilities' => $totalLiabilities,
            'founder_capital' => $founderCapital,
            'accumulated_surplus' => $accumulatedSurplus,
            'total_equity' => $totalEquity,
            'net_working_capital' => $nwc,
            'implied_equity' => $nwc,
            'total_liabilities_and_equity' => $totalLiabilitiesAndEquity,
            'balances' => abs($totalAssets - $totalLiabilitiesAndEquity) < 0.02,
            'lines' => $lines,
            'vertical' => $vertical,
            'snapshot_note' => 'المركز المالي لقطة بتاريخ التقرير (الأرصدة الحالية). لا تُحفظ نسخ تاريخية للميزانية؛ لذلك التحليل الأفقي للمركز غير متاح دون فقد بيانات.',
        ];
    }

    /**
     * قائمة التدفقات النقدية (الطريقة المباشرة) — أنشطة تشغيل / استثمار / تمويل.
     * مصروف الجيب يظهر تشغيلياً ويُعاد تمويله في النشاط التمويلي حتى لا تُحرَّك أرصدة المحافظ.
     *
     * @param  array<string, mixed>  $income
     * @return array<string, mixed>
     */
    public static function cashFlowStatement(array $income, Carbon $start, Carbon $end): array
    {
        $collections = round((float) ($income['net_sales'] ?? 0), 2);
        $equipment = round((float) ($income['equipment'] ?? 0), 2);
        $operatingPaid = round((float) ($income['expenses_recorded'] ?? 0) - $equipment, 2);
        $withdrawals = round((float) ($income['instructor_withdrawals'] ?? 0), 2);
        $fees = round((float) ($income['gateway_fees'] ?? 0), 2);
        $pocket = round(self::expensesBetween($start, $end, self::FUNDING_OUT_OF_POCKET), 2);

        $cfo = round($collections - $operatingPaid - $withdrawals - $fees, 2);
        $cfi = round(-$equipment, 2);
        $cff = $pocket;
        $netChange = round($cfo + $cfi + $cff, 2);

        $lines = [
            ['key' => 'collections', 'label' => 'متحصلات من العملاء (تحصيلات)', 'amount' => $collections, 'section' => 'operating'],
            ['key' => 'operating_paid', 'label' => 'مدفوعات تشغيل وبيع (بدون معدات)', 'amount' => -$operatingPaid, 'section' => 'operating'],
            ['key' => 'instructor_withdrawals', 'label' => 'مدفوعات للمدربين (سحوبات مكتملة)', 'amount' => -$withdrawals, 'section' => 'operating'],
            ['key' => 'gateway_fees', 'label' => 'عمولات بوابات الدفع', 'amount' => -$fees, 'section' => 'operating'],
            ['key' => 'cfo', 'label' => 'صافي التدفق من الأنشطة التشغيلية', 'amount' => $cfo, 'section' => 'operating', 'emphasis' => true],
            ['key' => 'equipment', 'label' => 'شراء معدات وتجهيزات', 'amount' => $cfi, 'section' => 'investing'],
            ['key' => 'cfi', 'label' => 'صافي التدفق من الأنشطة الاستثمارية', 'amount' => $cfi, 'section' => 'investing', 'emphasis' => true],
            ['key' => 'founder_contribution', 'label' => 'مساهمات المؤسسين (تمويل من الجيب)', 'amount' => $cff, 'section' => 'financing'],
            ['key' => 'cff', 'label' => 'صافي التدفق من الأنشطة التمويلية', 'amount' => $cff, 'section' => 'financing', 'emphasis' => true],
            ['key' => 'net_change', 'label' => 'صافي التغيّر في النقدية', 'amount' => $netChange, 'section' => 'total', 'emphasis' => true],
        ];

        return [
            'collections' => $collections,
            'operating_paid' => $operatingPaid,
            'instructor_withdrawals' => $withdrawals,
            'gateway_fees' => $fees,
            'equipment' => $equipment,
            'founder_contribution' => $pocket,
            'cfo' => $cfo,
            'cfi' => $cfi,
            'cff' => $cff,
            'net_change' => $netChange,
            'lines' => $lines,
        ];
    }

    /**
     * حزمة القوائم: الفترة الحالية مقابل فترة سابقة بنفس الطول + تحليل أفقي/رأسي + النسب.
     *
     * @return array<string, mixed>
     */
    public static function statementsPack(Carbon $start, Carbon $end): array
    {
        $seconds = max(0, $end->getTimestamp() - $start->getTimestamp());
        $priorEnd = $start->copy()->subSecond();
        $priorStart = $priorEnd->copy()->subSeconds($seconds);

        $current = self::incomeStatement($start, $end);
        $prior = self::incomeStatement($priorStart, $priorEnd);
        $position = self::positionSnapshot();
        $cashCurrent = self::cashFlowStatement($current, $start, $end);
        $cashPrior = self::cashFlowStatement($prior, $priorStart, $priorEnd);
        $incomeCompare = self::compareIncomeLines($current, $prior);
        $cashCompare = self::compareKeyedLines($cashCurrent['lines'], $cashPrior, $cashCurrent['collections'], $cashPrior['collections']);
        $ratios = self::ratioPack($current, $prior, $position, $cashCurrent, $cashPrior, $start, $end);
        $dupont = self::dupontAnalysis($current, $prior, $position);
        $ccc = self::cashConversionCycle($current, $prior, $position, $start, $end);
        $executive = self::executiveBrief($current, $prior, $position, $cashCurrent, $ratios, $start, $end);
        $recommendations = self::recommendations($current, $position, $ratios, $ccc, $cashCurrent);

        return [
            'basis' => 'تقارير إدارية على أساس التحصيل النقدي من بيانات النظام الحالية — لا تُنقل ولا تُحذف أي قيود. ليست دفتر أستاذ عام (GL).',
            'entity' => config('app.name', 'Mindlytics'),
            'currency' => 'EGP',
            'current_period' => [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
            ],
            'prior_period' => [
                'start' => $priorStart->format('Y-m-d'),
                'end' => $priorEnd->format('Y-m-d'),
            ],
            'current' => $current,
            'prior' => $prior,
            'income_compare' => $incomeCompare,
            'position' => $position,
            'cash_flow' => $cashCurrent,
            'cash_flow_prior' => $cashPrior,
            'cash_compare' => $cashCompare,
            'ratios' => $ratios,
            'dupont' => $dupont,
            'ccc' => $ccc,
            'executive' => $executive,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * @param  array<string, mixed>  $current
     * @param  array<string, mixed>  $prior
     * @return list<array<string, mixed>>
     */
    public static function compareIncomeLines(array $current, array $prior): array
    {
        return self::compareKeyedLines(
            $current['lines'] ?? [],
            $prior,
            (float) ($current['net_sales'] ?? 0),
            (float) ($prior['net_sales'] ?? 0)
        );
    }

    /**
     * @param  list<array<string, mixed>>  $lines
     * @param  array<string, mixed>  $priorKeyed
     * @return list<array<string, mixed>>
     */
    public static function compareKeyedLines(array $lines, array $priorKeyed, float $baseCurrent, float $basePrior): array
    {
        $priorByKey = [];
        foreach ($priorKeyed['lines'] ?? [] as $line) {
            $priorByKey[$line['key']] = (float) $line['amount'];
        }

        $rows = [];
        foreach ($lines as $line) {
            $key = $line['key'];
            $curAmt = (float) $line['amount'];
            $priorAmt = $priorByKey[$key] ?? (float) ($priorKeyed[$key] ?? 0);
            $change = round($curAmt - $priorAmt, 2);

            $rows[] = [
                'key' => $key,
                'label' => $line['label'],
                'nature' => $line['nature'] ?? (($curAmt < 0) ? 'cost' : 'income'),
                'indent' => (int) ($line['indent'] ?? 0),
                'emphasis' => (bool) ($line['emphasis'] ?? false),
                'section' => $line['section'] ?? ($line['group'] ?? null),
                'current' => $curAmt,
                'prior' => $priorAmt,
                'change' => $change,
                'change_pct' => self::pctChange($priorAmt, $curAmt),
                'vertical_current' => self::verticalPct($curAmt, $baseCurrent),
                'vertical_prior' => self::verticalPct($priorAmt, $basePrior),
            ];
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $current
     * @param  array<string, mixed>  $prior
     * @param  array<string, mixed>  $position
     * @param  array<string, mixed>  $cashCurrent
     * @param  array<string, mixed>  $cashPrior
     * @return array<string, list<array<string, mixed>>>
     */
    public static function ratioPack(array $current, array $prior, array $position, array $cashCurrent, array $cashPrior, Carbon $start, Carbon $end): array
    {
        $periodDays = max(1, (int) round(($end->getTimestamp() - $start->getTimestamp()) / 86400));

        $row = function (
            string $key,
            string $label,
            ?float $cur,
            ?float $prev,
            string $unit,
            string $betterWhen,
            string $benchmark,
            string $note
        ): array {
            return [
                'key' => $key,
                'label' => $label,
                'current' => $cur,
                'prior' => $prev,
                'change_pct' => ($cur !== null && $prev !== null) ? self::pctChange($prev, $cur) : null,
                'unit' => $unit,
                'better_when' => $betterWhen,
                'benchmark' => $benchmark,
                'note' => $note,
            ];
        };

        $margin = function (float $num, float $den): ?float {
            if (abs($den) < 0.0000001) {
                return null;
            }

            return round(($num / $den) * 100, 1);
        };

        $multiple = function (float $num, float $den): ?float {
            if (abs($den) < 0.0000001) {
                return null;
            }

            return round($num / $den, 2);
        };

        $salesC = (float) $current['net_sales'];
        $salesP = (float) $prior['net_sales'];
        $ca = (float) $position['current_assets'];
        $cl = (float) $position['current_liabilities'];
        $cash = (float) $position['cash'];
        $ar = (float) $position['receivables'];
        $nwc = (float) $position['net_working_capital'];
        $equity = (float) ($position['total_equity'] ?? $position['implied_equity'] ?? 0);
        $ebitdaC = (float) ($current['ebitda'] ?? $current['operating_profit']);
        $ebitdaP = (float) ($prior['ebitda'] ?? $prior['operating_profit']);
        $cogsC = (float) $current['cogs'];
        $cogsP = (float) $prior['cogs'];

        $dso = abs($salesC) < 0.0000001 ? null : round($periodDays * ($ar / $salesC), 1);
        $dsoPrior = abs($salesP) < 0.0000001 ? null : round($periodDays * ($ar / $salesP), 1);

        return [
            'profitability' => [
                $row('gpm', 'هامش مجمل الربح', $margin((float) $current['gross_profit'], $salesC), $margin((float) $prior['gross_profit'], $salesP), '%', 'up', '20–40%', 'مجمل الربح ÷ صافي المبيعات'),
                $row('ebitda', 'هامش EBITDA', $margin($ebitdaC, $salesC), $margin($ebitdaP, $salesP), '%', 'up', '15–25%', '≈ هامش EBIT — لا يُفصَل إهلاك'),
                $row('ebit', 'هامش EBIT (تشغيلي)', $margin((float) $current['operating_profit'], $salesC), $margin((float) $prior['operating_profit'], $salesP), '%', 'up', '10–22%', 'الربح التشغيلي ÷ صافي المبيعات'),
                $row('pre_tax', 'هامش ما قبل الضريبة', $margin((float) $current['net_income'], $salesC), $margin((float) $prior['net_income'], $salesP), '%', 'up', '8–20%', 'لا ضريبة منفصلة — يساوي هامش الصافي'),
                $row('npm', 'هامش صافي الربح', $margin((float) $current['net_income'], $salesC), $margin((float) $prior['net_income'], $salesP), '%', 'up', '8–20%', 'صافي الدخل ÷ صافي المبيعات'),
                $row('cfm', 'هامش التدفق النقدي', $margin((float) $cashCurrent['cfo'], $salesC), $margin((float) $cashPrior['cfo'], $salesP), '%', 'up', '10–20%', 'صافي التدفق التشغيلي ÷ المبيعات'),
                $row('roa', 'العائد على الأصول (ROA)', $margin((float) $current['net_income'], $ca), null, '%', 'up', '10–20%', 'صافي الدخل ÷ إجمالي الأصول (لقطة حالية)'),
                $row('roe', 'العائد على حقوق الملكية (ROE)', $margin((float) $current['net_income'], $equity), null, '%', 'up', '15–40%', 'صافي الدخل ÷ حقوق الملكية'),
                $row('roic', 'العائد على رأس المال المستثمر (ROIC)', $margin((float) $current['operating_profit'], $nwc), null, '%', 'up', 'أعلى من تكلفة رأس المال', 'EBIT ÷ صافي رأس المال العامل'),
                $row('wacc', 'المتوسط المرجح لتكلفة رأس المال (WACC)', null, null, '%', 'down', '< ROIC', 'غير قابل للحساب — لا تكلفة دين/ملكية مفصح عنها'),
            ],
            'liquidity' => [
                $row('current_ratio', 'النسبة الجارية', $multiple($ca, $cl), null, '×', 'up', '1.5–3.0', 'الأصول المتداولة ÷ الخصوم المتداولة'),
                $row('quick_ratio', 'النسبة السريعة (اختبار الحمض)', $multiple($cash + $ar, $cl), null, '×', 'up', '≥ 1.0', 'بدون مخزون — تعادل الجارية'),
                $row('cash_ratio', 'نسبة النقدية', $multiple($cash, $cl), null, '×', 'up', '≈ 1.0', 'النقدية ÷ الخصوم المتداولة'),
                $row('ebitda_interest', 'EBITDA إلى الفائدة', null, null, '×', 'up', '> 3.0', 'غير قابل للحساب — لا فائدة مفصح عنها'),
                $row('nwc_revenue', 'رأس المال العامل / الإيراد', $margin($nwc, $salesC), null, '%', 'up', 'أعلى = أقوى', 'صافي رأس المال العامل ÷ مبيعات الفترة'),
            ],
            'solvency' => [
                $row('assets_equity', 'إجمالي الأصول إلى حقوق الملكية', $multiple($ca, $equity), null, '×', 'down', 'أقل = أأمن', 'إجمالي الأصول ÷ حقوق الملكية'),
                $row('debt_equity', 'الدين إلى حقوق الملكية', $multiple($cl, $equity), null, '×', 'down', '< 1.0', 'إجمالي الخصوم ÷ حقوق الملكية'),
                $row('debt_assets', 'الدين إلى الأصول', $margin($cl, $ca), null, '%', 'down', '< 50%', 'إجمالي الخصوم ÷ إجمالي الأصول'),
                $row('debt_capital', 'الدين إلى رأس المال', $margin($cl, $ca), null, '%', 'down', '< 50%', 'يساوي الدين/الأصول لأن رأس المال = الأصول'),
                $row('debt_tnw', 'الدين إلى صافي القيمة الملموسة', $multiple($cl, $equity), null, '×', 'down', '< 1.0', 'لا أصول غير ملموسة — يساوي الدين/الملكية'),
                $row('debt_ebitda', 'إجمالي الدين إلى EBITDA', $multiple($cl, $ebitdaC), null, '×', 'down', '< 3.0', 'الخصوم ÷ EBITDA'),
            ],
            'efficiency' => [
                $row('inventory_turnover', 'دوران المخزون', null, null, '×', 'up', 'أعلى أفضل', 'غير منطبق — الأكاديمية بلا مخزون سلعي'),
                $row('ar_turnover', 'دوران الذمم المدينة', $multiple($salesC, $ar), $multiple($salesP, $ar), '×', 'up', 'أعلى أفضل', 'المبيعات ÷ الذمم المدينة'),
                $row('ap_turnover', 'دوران الذمم الدائنة', $multiple($cogsC, $cl), $multiple($cogsP, $cl), '×', 'up', 'حسب السياق', 'تكلفة الخدمة ÷ الخصوم المتداولة'),
                $row('asset_turnover', 'دوران إجمالي الأصول', $multiple($salesC, $ca), $multiple($salesP, $ca), '×', 'up', 'أعلى أفضل', 'المبيعات ÷ إجمالي الأصول'),
            ],
            'coverage' => [
                $row('assets_coverage', 'تغطية الأصول', $multiple($ca, $cl), null, '×', 'up', '> 1.0', 'الأصول الملموسة ÷ إجمالي الالتزامات'),
                $row('interest_coverage', 'تغطية الفائدة', null, null, '×', 'up', '> 1.0', 'غير قابل للحساب — لا مصروف فائدة مفصح'),
                $row('dscr', 'تغطية خدمة الدين (DSCR)', null, null, '×', 'up', '> 1.0', 'غير منطبق — لا جدول خدمة دين منفصل'),
                $row('cash_coverage', 'التغطية النقدية للفائدة', null, null, '×', 'up', '> 1.0', 'غير قابل للحساب — لا فائدة مفصح عنها'),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $current
     * @param  array<string, mixed>  $prior
     * @param  array<string, mixed>  $position
     * @return array<string, mixed>
     */
    public static function dupontAnalysis(array $current, array $prior, array $position): array
    {
        $assets = (float) ($position['total_assets'] ?? $position['current_assets'] ?? 0);
        $equity = (float) ($position['total_equity'] ?? 0);
        $salesC = (float) $current['net_sales'];
        $salesP = (float) $prior['net_sales'];
        $niC = (float) $current['net_income'];
        $niP = (float) $prior['net_income'];

        $npm = abs($salesC) < 0.0000001 ? 0.0 : $niC / $salesC;
        $npmP = abs($salesP) < 0.0000001 ? 0.0 : $niP / $salesP;
        $at = abs($assets) < 0.0000001 ? 0.0 : $salesC / $assets;
        $atP = abs($assets) < 0.0000001 ? 0.0 : $salesP / $assets;
        $em = abs($equity) < 0.0000001 ? 0.0 : $assets / $equity;
        $roe = $npm * $at * $em;
        $roeP = $npmP * $atP * $em;

        return [
            'note' => 'ROE = هامش صافي الربح × دوران الأصول × مضاعف الملكية. الأصول والملكية لقطة حالية.',
            'current' => [
                'npm' => round($npm * 100, 1),
                'asset_turnover' => round($at, 2),
                'equity_multiplier' => round($em, 2),
                'roe' => round($roe * 100, 1),
            ],
            'prior' => [
                'npm' => round($npmP * 100, 1),
                'asset_turnover' => round($atP, 2),
                'equity_multiplier' => round($em, 2),
                'roe' => round($roeP * 100, 1),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $current
     * @param  array<string, mixed>  $prior
     * @param  array<string, mixed>  $position
     * @return array<string, mixed>
     */
    public static function cashConversionCycle(array $current, array $prior, array $position, Carbon $start, Carbon $end): array
    {
        $periodDays = max(1, (int) round(($end->getTimestamp() - $start->getTimestamp()) / 86400));
        $salesC = (float) $current['net_sales'];
        $salesP = (float) $prior['net_sales'];
        $cogsC = (float) $current['cogs'];
        $cogsP = (float) $prior['cogs'];
        $ar = (float) $position['receivables'];
        $cl = (float) $position['current_liabilities'];

        $dso = abs($salesC) < 0.0000001 ? null : round($periodDays * ($ar / $salesC), 1);
        $dsoP = abs($salesP) < 0.0000001 ? null : round($periodDays * ($ar / $salesP), 1);
        $dpo = abs($cogsC) < 0.0000001 ? null : round($periodDays * ($cl / $cogsC), 1);
        $dpoP = abs($cogsP) < 0.0000001 ? null : round($periodDays * ($cl / $cogsP), 1);
        $dio = 0.0;
        $ccc = ($dso === null || $dpo === null) ? null : round($dio + $dso - $dpo, 1);
        $cccP = ($dsoP === null || $dpoP === null) ? null : round($dio + $dsoP - $dpoP, 1);

        return [
            'note' => 'CCC = DIO + DSO − DPO. DIO = 0 لأن الأكاديمية بلا مخزون سلعي. الأرصدة لقطة حالية.',
            'components' => [
                ['key' => 'dio', 'label' => 'أيام المخزون (DIO)', 'current' => $dio, 'prior' => $dio, 'benchmark' => 'غير منطبق', 'note' => 'لا مخزون'],
                ['key' => 'dso', 'label' => 'أيام التحصيل (DSO)', 'current' => $dso, 'prior' => $dsoP, 'benchmark' => 'أقل أفضل', 'note' => 'الذمم ÷ (المبيعات ÷ الأيام)'],
                ['key' => 'dpo', 'label' => 'أيام السداد (DPO)', 'current' => $dpo, 'prior' => $dpoP, 'benchmark' => 'أعلى أفضل', 'note' => 'الخصوم ÷ (تكلفة الخدمة ÷ الأيام)'],
                ['key' => 'ccc', 'label' => 'دورة التحويل النقدي (CCC)', 'current' => $ccc, 'prior' => $cccP, 'benchmark' => 'أقصر أفضل', 'note' => 'DIO + DSO − DPO'],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $current
     * @param  array<string, mixed>  $prior
     * @param  array<string, mixed>  $position
     * @param  array<string, mixed>  $cash
     * @param  array<string, mixed>  $ratios
     * @return array<string, mixed>
     */
    public static function executiveBrief(array $current, array $prior, array $position, array $cash, array $ratios, Carbon $start, Carbon $end): array
    {
        $salesChg = self::pctChange((float) $prior['net_sales'], (float) $current['net_sales']);
        $niChg = self::pctChange((float) $prior['net_income'], (float) $current['net_income']);
        $opChg = self::pctChange((float) $prior['operating_profit'], (float) $current['operating_profit']);
        $gpm = self::findRatio($ratios, 'profitability', 'gpm');
        $npm = self::findRatio($ratios, 'profitability', 'npm');
        $currentRatio = self::findRatio($ratios, 'liquidity', 'current_ratio');

        $salesTxt = $salesChg === null ? 'تغيرت المبيعات بشكل غير قابل للقياس مقابل الفترة السابقة' : (
            ($salesChg >= 0 ? 'ارتفعت' : 'انخفضت').' المبيعات '.self::formatSignedPct($salesChg)
        );
        $profitTxt = $niChg === null ? 'صافي الدخل غير قابل للمقارنة أفقياً' : (
            ($niChg >= 0 ? 'ونما صافي الدخل ' : 'وتراجع صافي الدخل ').self::formatSignedPct($niChg)
        );

        $purpose = 'تقديم تقييم موجز وجاهز للقرار عن الأداء والمركز المالي لـ '.config('app.name', 'Mindlytics').' للفترة '.$start->format('Y-m-d').' إلى '.$end->format('Y-m-d').'، مقارنةً بفترة سابقة بنفس الطول. الأرقام من سجلات النظام الحالية دون نقل أو حذف أي بيانات.';

        $findings = $salesTxt.' '.$profitTxt.'. '
            .'هامش المجمل '.self::formatRatioPlain($gpm).' وهامش الصافي '.self::formatRatioPlain($npm).'. '
            .'الميزانية متوازنة: أصول '.number_format((float) $position['total_assets'], 2).' ج.م مقابل خصوم وحقوق ملكية '.number_format((float) $position['total_liabilities_and_equity'], 2).' ج.م. '
            .'السيولة: النسبة الجارية '.self::formatRatioPlain($currentRatio).'، والنقدية '.number_format((float) $position['cash'], 2).' ج.م. '
            .'صافي التدفق التشغيلي '.number_format((float) $cash['cfo'], 2).' ج.م.';

        $drivers = 'الربح التشغيلي '.($opChg === null ? 'غير مقارن' : self::formatSignedPct($opChg)).' مقابل الفترة السابقة. '
            .'تكلفة الخدمة '.number_format((float) $current['cogs'], 2).' ج.م وعمولات البوابات '.number_format((float) $current['gateway_fees'], 2).' ج.م. '
            .'تمويل المؤسسين مصنّف حقوق ملكية ('.number_format((float) $position['founder_capital'], 2).' ج.م) وليس التزاماً.';

        return [
            'to' => 'الإدارة التنفيذية / مجلس الإدارة',
            'from' => 'النظام المحاسبي — '.config('app.name', 'Mindlytics'),
            'date' => now()->format('Y-m-d'),
            'subject' => 'ملخص تنفيذي — التحليل المالي للفترة',
            'purpose' => $purpose,
            'findings' => $findings,
            'drivers' => $drivers,
        ];
    }

    /**
     * @param  array<string, mixed>  $current
     * @param  array<string, mixed>  $position
     * @param  array<string, mixed>  $ratios
     * @param  array<string, mixed>  $ccc
     * @param  array<string, mixed>  $cash
     * @return list<string>
     */
    public static function recommendations(array $current, array $position, array $ratios, array $ccc, array $cash): array
    {
        $gpm = self::findRatio($ratios, 'profitability', 'gpm');
        $cashRatio = self::findRatio($ratios, 'liquidity', 'cash_ratio');
        $dso = $ccc['components'][1]['current'] ?? null;
        $de = self::findRatio($ratios, 'solvency', 'debt_equity');
        $roic = self::findRatio($ratios, 'profitability', 'roic');

        $items = [];
        $gpmVal = $gpm['current'] ?? null;
        if ($gpmVal !== null && $gpmVal >= 20) {
            $items[] = 'الحفاظ على مكاسب الهامش عبر ضبط تكلفة الخدمة (رواتب وسحوبات المدربين) وحماية نسبة تكلفة المبيعات.';
        } else {
            $items[] = 'رفع مجمل الربح عبر مراجعة تكلفة الخدمة وأسعار البرامج، مع الإبقاء على جودة التشغيل.';
        }

        $items[] = ($dso !== null && $dso > 21)
            ? 'تشديد إدارة رأس المال العامل: أيام التحصيل حالياً '.number_format((float) $dso, 1).' يوماً — تسريع تحصيل الذمم يقصر دورة التحويل النقدي.'
            : 'الاستمرار في تحصيل الذمم بسرعة، ومراجعة أيام السداد للموردين والمدربين دون الإضرار بالعلاقات.';

        $cashVal = $cashRatio['current'] ?? null;
        if ($cashVal !== null && $cashVal >= 1) {
            $items[] = 'توظيف فائض السيولة في استثمارات انتقائية مولّدة للعائد بدلاً من الإبقاء على أرصدة نقدية كبيرة عاطلة، مع الحفاظ على المرونة المالية.';
        } else {
            $items[] = 'بناء هامش سيولة نقدية يغطي الالتزامات قصيرة الأجل، دون تقييد التشغيل.';
        }

        $items[] = ($roic['current'] ?? null) !== null
            ? 'تحسين كفاءة رأس المال (ROIC '.number_format((float) $roic['current'], 1).'%) بالتأكد أن التوسع والمصروفات الرأسمالية تضيف عائداً موجباً.'
            : 'قياس العائد على أي توسع جديد قبل الالتزام به.';

        $deVal = $de['current'] ?? null;
        $items[] = ($deVal !== null && $deVal < 1)
            ? 'الإبقاء على الرفع المالي محافظاً، وتمويل التوسع أساساً من التدفق الداخلي وحقوق الملكية.'
            : 'خفض الاعتماد على الالتزامات قصيرة الأجل وتمويل النمو من التشغيل وحقوق الملكية.';

        $items[] = 'تعزيز الإفصاح المالي: فصل مصروف الفائدة واستحقاقات الدين إن وُجدت، لتفعيل نسب التغطية القائمة على الفائدة في الفترات القادمة.';

        return $items;
    }

    /**
     * @param  array<string, mixed>  $ratios
     * @return array<string, mixed>|null
     */
    public static function findRatio(array $ratios, string $group, string $key): ?array
    {
        foreach ($ratios[$group] ?? [] as $row) {
            if (($row['key'] ?? '') === $key) {
                return $row;
            }
        }

        return null;
    }

    public static function formatSignedPct(?float $pct): string
    {
        if ($pct === null) {
            return 'n.m.';
        }

        $sign = $pct > 0 ? '+' : '';

        return $sign.number_format($pct, 1).'%';
    }

    /**
     * @param  array<string, mixed>|null  $ratio
     */
    public static function formatRatioPlain(?array $ratio): string
    {
        if (! $ratio || $ratio['current'] === null) {
            return 'n.m.';
        }
        $unit = $ratio['unit'] ?? '';
        $val = (float) $ratio['current'];
        if ($unit === '%') {
            return number_format($val, 1).'%';
        }
        if ($unit === '×') {
            return number_format($val, 2).'×';
        }

        return number_format($val, 2);
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

    public static function invoiceTypeLabels(): array
    {
        return [
            'course' => 'كورسات مسجّلة',
            'offline_course' => 'جروبات (لايف)',
            'learning_path' => 'مسارات تعليمية',
            'subscription' => 'اشتراكات',
            'membership' => 'عضويات',
            'installment' => 'أقساط',
            'other' => 'أخرى / عام',
        ];
    }

    public static function invoiceTypeLabel(?string $type): string
    {
        return self::invoiceTypeLabels()[$type] ?? ($type ?: 'غير مصنّف');
    }

    public static function revenueSourceLabels(): array
    {
        return [
            'recorded_course' => 'كورسات مسجّلة',
            'live_online_group' => 'جروبات أونلاين',
            'live_offline_group' => 'جروبات أوفلاين',
            'live_group_unknown' => 'جروبات (قناة غير محددة)',
            'learning_path' => 'مسارات تعليمية',
            'subscription' => 'اشتراكات',
            'membership' => 'عضويات',
            'installment' => 'أقساط',
            'other' => 'أخرى / عام',
        ];
    }

    public static function revenueSourceLabel(?string $type): string
    {
        return self::revenueSourceLabels()[$type] ?? ($type ?: 'غير مصنّف');
    }

    public static function enrollmentChannelLabel(?string $channel): string
    {
        return match (strtolower((string) $channel)) {
            'online' => 'أونلاين (لايف)',
            'offline' => 'أوفلاين (حضور)',
            default => $channel ? (string) $channel : 'غير محدد',
        };
    }

    public static function paymentMethodLabel(?string $method): string
    {
        return match (strtolower((string) $method)) {
            'online' => 'أونلاين (بوابة)',
            'cash' => 'نقدي',
            'bank_transfer', 'bank' => 'تحويل بنكي',
            'wallet' => 'محفظة',
            'manual' => 'يدوي / إداري',
            'installment' => 'تقسيط',
            'other' => 'أخرى',
            default => $method ? (string) $method : '—',
        };
    }

    public static function isOnlineCollection(Payment $payment): bool
    {
        return $payment->status === 'completed'
            && strtolower((string) $payment->payment_method) === 'online'
            && filled($payment->payment_gateway)
            && strtolower((string) $payment->payment_gateway) !== 'manual';
    }

    /**
     * @return array{
     *     channel: string,
     *     channel_label: string,
     *     sub_channel: string,
     *     sub_channel_label: string
     * }
     */
    public static function classifyCollectionChannel(Payment $payment): array
    {
        if (self::isOnlineCollection($payment)) {
            return [
                'channel' => 'online',
                'channel_label' => 'تحصيل أونلاين',
                'sub_channel' => (string) ($payment->payment_gateway ?? 'other'),
                'sub_channel_label' => Payment::gatewayLabel($payment->payment_gateway),
            ];
        }

        $method = strtolower((string) ($payment->payment_method ?? 'other'));

        return [
            'channel' => 'offline',
            'channel_label' => 'تحصيل أوفلاين / يدوي',
            'sub_channel' => $method ?: 'other',
            'sub_channel_label' => self::paymentMethodLabel($payment->payment_method),
        ];
    }

    /**
     * @return array{
     *     revenue_type: string,
     *     revenue_type_label: string,
     *     invoice_type: string|null,
     *     product_name: string,
     *     group_name: string|null,
     *     enrollment_channel: string|null,
     *     enrollment_channel_label: string|null,
     *     client_name: string,
     *     invoice_number: string|null,
     *     description: string
     * }
     */
    public static function revenueSourceForPayment(
        Payment $payment,
        ?OfflineCourseEnrollment $offlineEnrollment = null,
        ?StudentCourseEnrollment $recordedEnrollment = null
    ): array {
        $invoice = $payment->invoice;
        $order = $payment->order;
        $invoiceType = $invoice?->type;

        if (! $invoiceType && $order) {
            $invoiceType = $order->advanced_course_id ? 'course' : ($order->academic_year_id ? 'learning_path' : 'other');
        }
        $invoiceType = $invoiceType ?: 'other';

        $channel = $offlineEnrollment?->enrollment_channel;
        if (! $channel && $invoiceType === 'offline_course') {
            $channel = self::inferLiveChannelFromText(
                (string) ($invoice?->description ?? ''),
                $invoice?->items
            );
            if (! $channel && $offlineEnrollment?->course?->online_only) {
                $channel = 'online';
            }
        }

        $revenueType = match ($invoiceType) {
            'course' => 'recorded_course',
            'offline_course' => match ($channel) {
                'online' => 'live_online_group',
                'offline' => 'live_offline_group',
                default => 'live_group_unknown',
            },
            'learning_path' => 'learning_path',
            'subscription' => 'subscription',
            'membership' => 'membership',
            'installment' => 'installment',
            default => 'other',
        };

        $productName = '—';
        $groupName = $offlineEnrollment?->group?->name;

        if ($offlineEnrollment?->course) {
            $productName = (string) $offlineEnrollment->course->title;
            if ($groupName) {
                $productName .= ' — '.$groupName;
            }
        } elseif ($recordedEnrollment?->course) {
            $productName = (string) $recordedEnrollment->course->title;
        } elseif ($order?->course) {
            $productName = (string) $order->course->title;
        } elseif ($order?->learningPath) {
            $productName = (string) ($order->learningPath->name ?? $order->learningPath->title ?? 'مسار تعليمي');
        } elseif ($invoice) {
            $productName = trim((string) ($invoice->description ?? ''));
            if ($productName === '' || $productName === '-') {
                $items = $invoice->items;
                if (is_array($items) && ! empty($items[0]['name'] ?? $items[0]['description'] ?? null)) {
                    $productName = (string) ($items[0]['name'] ?? $items[0]['description']);
                }
            }
        }

        if ($productName === '' || $productName === '-') {
            $productName = self::revenueSourceLabel($revenueType);
        }

        $clientName = $invoice
            ? $invoice->clientDisplayName()
            : (string) ($payment->user?->name ?? '—');

        return [
            'revenue_type' => $revenueType,
            'revenue_type_label' => self::revenueSourceLabel($revenueType),
            'invoice_type' => $invoiceType,
            'product_name' => $productName,
            'group_name' => $groupName,
            'enrollment_channel' => $channel,
            'enrollment_channel_label' => $channel ? self::enrollmentChannelLabel($channel) : null,
            'client_name' => $clientName,
            'invoice_number' => $invoice?->invoice_number,
            'description' => (string) ($invoice?->description ?? $payment->notes ?? '—'),
        ];
    }

    /**
     * @param  mixed  $items
     */
    public static function inferLiveChannelFromText(string $text, mixed $items = null): ?string
    {
        $blob = $text;
        if (is_array($items)) {
            foreach ($items as $item) {
                if (is_array($item)) {
                    $blob .= ' '.($item['description'] ?? '').' '.($item['name'] ?? '');
                }
            }
        }

        if (preg_match('/أونلاين|اونلاين|online/iu', $blob)) {
            return 'online';
        }
        if (preg_match('/أوفلاين|اوفلاين|offline|حضور/iu', $blob)) {
            return 'offline';
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public static function comprehensiveReport(Carbon $start, Carbon $end): array
    {
        $breakEven = self::breakEvenAnalysis($start, $end);
        $daily = self::dailySeries($start, $end);

        $expenses = Expense::query()
            ->approved()
            ->with(['offlineLocation', 'wallet', 'createdBy'])
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('expense_date')
            ->get();

        $payments = Payment::query()
            ->with(['invoice', 'user', 'order.course', 'order.learningPath', 'branch'])
            ->where('status', 'completed')
            ->whereBetween('paid_at', [$start, $end])
            ->orderBy('paid_at')
            ->get();

        $invoiceIds = $payments->pluck('invoice_id')->filter()->unique()->values();
        $paymentIds = $payments->pluck('id')->filter()->unique()->values();

        $offlineByInvoice = OfflineCourseEnrollment::query()
            ->with(['course', 'group'])
            ->whereIn('invoice_id', $invoiceIds)
            ->get()
            ->keyBy('invoice_id');

        $recordedByInvoice = StudentCourseEnrollment::query()
            ->with('course')
            ->whereIn('invoice_id', $invoiceIds)
            ->get()
            ->keyBy('invoice_id');

        $recordedByPayment = StudentCourseEnrollment::query()
            ->with('course')
            ->whereIn('payment_id', $paymentIds)
            ->get()
            ->keyBy('payment_id');

        $paymentRows = [];
        $revenueByType = [];
        $revenueByProduct = [];
        $revenueByTypeAndChannel = [];
        $collectionsOnline = ['total' => 0.0, 'count' => 0, 'by_gateway' => []];
        $collectionsOffline = ['total' => 0.0, 'count' => 0, 'by_method' => []];
        $groupCollections = ['total' => 0.0, 'count' => 0, 'by_channel' => []];
        $productMix = [
            'recorded_course' => ['total' => 0.0, 'count' => 0],
            'live_online_group' => ['total' => 0.0, 'count' => 0],
            'live_offline_group' => ['total' => 0.0, 'count' => 0],
        ];

        foreach ($payments as $payment) {
            $amount = (float) $payment->amount;
            $offlineEnrollment = $payment->invoice_id
                ? $offlineByInvoice->get($payment->invoice_id)
                : null;
            $recordedEnrollment = $payment->invoice_id
                ? $recordedByInvoice->get($payment->invoice_id)
                : null;
            if (! $recordedEnrollment) {
                $recordedEnrollment = $recordedByPayment->get($payment->id);
            }

            $source = self::revenueSourceForPayment($payment, $offlineEnrollment, $recordedEnrollment);
            $collection = self::classifyCollectionChannel($payment);

            $typeKey = $source['revenue_type'];
            $productKey = $typeKey.'|'.$source['product_name'];
            $typeChannelKey = $typeKey.'|'.$collection['channel'];

            $revenueByType[$typeKey] = ($revenueByType[$typeKey] ?? ['label' => $source['revenue_type_label'], 'total' => 0.0, 'count' => 0]);
            $revenueByType[$typeKey]['total'] += $amount;
            $revenueByType[$typeKey]['count']++;

            $revenueByProduct[$productKey] = ($revenueByProduct[$productKey] ?? [
                'type' => $typeKey,
                'type_label' => $source['revenue_type_label'],
                'product_name' => $source['product_name'],
                'total' => 0.0,
                'count' => 0,
                'online' => 0.0,
                'offline' => 0.0,
            ]);
            $revenueByProduct[$productKey]['total'] += $amount;
            $revenueByProduct[$productKey]['count']++;
            $revenueByProduct[$productKey][$collection['channel']] += $amount;

            $revenueByTypeAndChannel[$typeChannelKey] = ($revenueByTypeAndChannel[$typeChannelKey] ?? [
                'type' => $typeKey,
                'type_label' => $source['revenue_type_label'],
                'channel' => $collection['channel'],
                'channel_label' => $collection['channel_label'],
                'total' => 0.0,
                'count' => 0,
            ]);
            $revenueByTypeAndChannel[$typeChannelKey]['total'] += $amount;
            $revenueByTypeAndChannel[$typeChannelKey]['count']++;

            if ($collection['channel'] === 'online') {
                $collectionsOnline['total'] += $amount;
                $collectionsOnline['count']++;
                $gw = $collection['sub_channel'];
                $collectionsOnline['by_gateway'][$gw] = ($collectionsOnline['by_gateway'][$gw] ?? [
                    'label' => $collection['sub_channel_label'],
                    'total' => 0.0,
                    'count' => 0,
                ]);
                $collectionsOnline['by_gateway'][$gw]['total'] += $amount;
                $collectionsOnline['by_gateway'][$gw]['count']++;
            } else {
                $collectionsOffline['total'] += $amount;
                $collectionsOffline['count']++;
                $method = $collection['sub_channel'];
                $collectionsOffline['by_method'][$method] = ($collectionsOffline['by_method'][$method] ?? [
                    'label' => $collection['sub_channel_label'],
                    'total' => 0.0,
                    'count' => 0,
                ]);
                $collectionsOffline['by_method'][$method]['total'] += $amount;
                $collectionsOffline['by_method'][$method]['count']++;
            }

            if (isset($productMix[$typeKey])) {
                $productMix[$typeKey]['total'] += $amount;
                $productMix[$typeKey]['count']++;
            }

            if (in_array($typeKey, ['live_online_group', 'live_offline_group', 'live_group_unknown'], true)) {
                $groupCollections['total'] += $amount;
                $groupCollections['count']++;
                $enrollChannel = $source['enrollment_channel'] ?: 'unknown';
                $groupCollections['by_channel'][$enrollChannel] = ($groupCollections['by_channel'][$enrollChannel] ?? [
                    'label' => self::enrollmentChannelLabel($enrollChannel === 'unknown' ? null : $enrollChannel),
                    'total' => 0.0,
                    'count' => 0,
                ]);
                $groupCollections['by_channel'][$enrollChannel]['total'] += $amount;
                $groupCollections['by_channel'][$enrollChannel]['count']++;
            }

            $paymentRows[] = array_merge($source, $collection, [
                'payment_id' => $payment->id,
                'payment_number' => $payment->payment_number,
                'amount' => $amount,
                'gateway_fee' => (float) ($payment->gateway_fee_amount ?? 0),
                'net_amount' => round($amount - (float) ($payment->gateway_fee_amount ?? 0), 2),
                'paid_at' => $payment->paid_at?->format('Y-m-d H:i'),
                'branch' => $payment->branch?->name,
                'reference' => $payment->reference_number ?? $payment->transaction_id,
            ]);
        }

        $totalRevenue = round((float) $payments->sum('amount'), 2);
        $totalExpenses = round((float) $expenses->sum('amount'), 2);

        $expensesByCategory = [];
        $expensesByFunding = [];
        $expenseRows = [];

        foreach ($expenses as $expense) {
            $amount = (float) $expense->amount;
            $category = $expense->category ?: 'other';
            $funding = $expense->funding_source ?: 'unknown';

            $expensesByCategory[$category] = ($expensesByCategory[$category] ?? [
                'label' => Expense::categoryLabel($category),
                'total' => 0.0,
                'count' => 0,
            ]);
            $expensesByCategory[$category]['total'] += $amount;
            $expensesByCategory[$category]['count']++;

            $expensesByFunding[$funding] = ($expensesByFunding[$funding] ?? [
                'label' => self::fundingSourceLabel($funding),
                'total' => 0.0,
                'count' => 0,
            ]);
            $expensesByFunding[$funding]['total'] += $amount;
            $expensesByFunding[$funding]['count']++;

            $expenseRows[] = [
                'expense_number' => $expense->expense_number,
                'title' => $expense->title,
                'category' => Expense::categoryLabel($expense->category),
                'amount' => $amount,
                'funding_source' => self::fundingSourceLabel($expense->funding_source),
                'payment_method' => self::paymentMethodLabel($expense->payment_method),
                'location' => $expense->offlineLocation?->name,
                'expense_date' => $expense->expense_date?->format('Y-m-d'),
                'created_by' => $expense->createdBy?->name,
            ];
        }

        uasort($revenueByType, fn ($a, $b) => $b['total'] <=> $a['total']);
        uasort($revenueByProduct, fn ($a, $b) => $b['total'] <=> $a['total']);

        $monthly = self::monthlySeries($start, $end);
        $statements = self::statementsPack($start, $end);

        return [
            'period' => [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
                'label' => $start->format('Y-m-d').' → '.$end->format('Y-m-d'),
            ],
            'statements' => $statements,
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_expenses' => $totalExpenses,
                'gross_profit' => $statements['current']['gross_profit'],
                'operating_profit' => $statements['current']['operating_profit'],
                'net_profit' => $statements['current']['net_income'],
                'payments_count' => $payments->count(),
                'expenses_count' => $expenses->count(),
                'online_collections' => round($collectionsOnline['total'], 2),
                'offline_collections' => round($collectionsOffline['total'], 2),
                'online_pct' => $totalRevenue > 0 ? round(($collectionsOnline['total'] / $totalRevenue) * 100, 1) : 0,
                'offline_pct' => $totalRevenue > 0 ? round(($collectionsOffline['total'] / $totalRevenue) * 100, 1) : 0,
                'gateway_fees' => round((float) $payments->sum('gateway_fee_amount'), 2),
                'recorded_course' => round($productMix['recorded_course']['total'], 2),
                'recorded_course_count' => $productMix['recorded_course']['count'],
                'live_online_group' => round($productMix['live_online_group']['total'], 2),
                'live_online_group_count' => $productMix['live_online_group']['count'],
                'live_offline_group' => round($productMix['live_offline_group']['total'], 2),
                'live_offline_group_count' => $productMix['live_offline_group']['count'],
            ],
            'break_even' => $breakEven,
            'revenue_by_type' => array_values($revenueByType),
            'revenue_by_product' => array_values($revenueByProduct),
            'revenue_by_type_channel' => array_values($revenueByTypeAndChannel),
            'collections' => [
                'online' => $collectionsOnline,
                'offline' => $collectionsOffline,
                'groups' => $groupCollections,
                'offline_courses' => $groupCollections,
            ],
            'expenses_by_category' => array_values($expensesByCategory),
            'expenses_by_funding' => array_values($expensesByFunding),
            'payment_rows' => $paymentRows,
            'expense_rows' => $expenseRows,
            'daily' => $daily,
            'monthly' => $monthly,
        ];
    }

    /**
     * @return array{labels: list<string>, revenue: list<float>, expenses: list<float>, net: list<float>}
     */
    public static function monthlySeries(Carbon $start, Carbon $end): array
    {
        $labels = [];
        $revenue = [];
        $expenses = [];
        $net = [];

        $cursor = $start->copy()->startOfMonth();
        $endMonth = $end->copy()->endOfMonth();

        while ($cursor->lte($endMonth)) {
            $mStart = $cursor->copy()->startOfMonth()->max($start);
            $mEnd = $cursor->copy()->endOfMonth()->min($end);

            $is = self::incomeStatement($mStart, $mEnd);

            $labels[] = $cursor->format('Y-m');
            $revenue[] = $is['net_sales'];
            $expenses[] = $is['expenses_recorded'];
            $net[] = $is['net_income'];

            $cursor->addMonth();
        }

        return compact('labels', 'revenue', 'expenses', 'net');
    }
}
