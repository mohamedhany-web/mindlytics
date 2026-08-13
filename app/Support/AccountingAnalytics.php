<?php

namespace App\Support;

use App\Models\Expense;
use App\Models\InstallmentPayment;
use App\Models\Invoice;
use App\Models\OfflineCourseEnrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\StudentCourseEnrollment;
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

        return [
            'period' => [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
                'label' => $start->format('Y-m-d').' → '.$end->format('Y-m-d'),
            ],
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_expenses' => $totalExpenses,
                'net_profit' => round($totalRevenue - $totalExpenses, 2),
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

            $r = self::revenueBetween($mStart, $mEnd);
            $e = self::expensesBetween($mStart, $mEnd);

            $labels[] = $cursor->format('Y-m');
            $revenue[] = round($r, 2);
            $expenses[] = round($e, 2);
            $net[] = round($r - $e, 2);

            $cursor->addMonth();
        }

        return compact('labels', 'revenue', 'expenses', 'net');
    }
}
