<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\InstallmentAgreement;
use App\Models\InstallmentPayment;
use App\Models\Invoice;
use App\Models\OfflineCourseEnrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\WithdrawalRequest;
use App\Support\AccountingAnalytics;
use App\Services\AccountingComprehensiveExcelExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountingReportsController extends Controller
{
    public function index(Request $request)
    {
        $filter = $this->resolvePeriodAndDates($request);
        $period = $filter['period'];
        $startDate = $filter['startDate'];
        $endDate = $filter['endDate'];
        $periodLabel = $filter['periodLabel'];

        $stats = $this->getGeneralStats($startDate, $endDate);
        $revenueReports = $this->getRevenueReports($startDate, $endDate);
        $expenseReports = $this->getExpenseReports($startDate, $endDate);
        $invoiceReports = $this->getInvoiceReports($startDate, $endDate);
        $paymentReports = $this->getPaymentReports($startDate, $endDate);
        $transactionReports = $this->getTransactionReports($startDate, $endDate);
        $monthlyData = $this->getMonthlyData($startDate, $endDate);
        $dailyData = $this->getDailyData($startDate, $endDate);
        $detailedReport = $this->getDetailedFinancialReport($startDate, $endDate);

        return view('admin.accounting.reports', compact(
            'stats',
            'revenueReports',
            'expenseReports',
            'invoiceReports',
            'paymentReports',
            'transactionReports',
            'monthlyData',
            'dailyData',
            'detailedReport',
            'period',
            'startDate',
            'endDate',
            'periodLabel',
            'filter'
        ));
    }

    public function financialAnalysis(Request $request)
    {
        $filter = $this->resolvePeriodAndDates($request);
        $period = $filter['period'];
        $startDate = $filter['startDate'];
        $endDate = $filter['endDate'];
        $periodLabel = $filter['periodLabel'];

        $report = AccountingAnalytics::comprehensiveReport($startDate, $endDate);

        return view('admin.accounting.financial-analysis', compact(
            'report',
            'period',
            'startDate',
            'endDate',
            'periodLabel',
            'filter'
        ));
    }

    public function exportFinancialAnalysis(Request $request)
    {
        $filter = $this->resolvePeriodAndDates($request);
        $startDate = $filter['startDate'];
        $endDate = $filter['endDate'];

        $report = AccountingAnalytics::comprehensiveReport($startDate, $endDate);

        return app(AccountingComprehensiveExcelExportService::class)
            ->download($report, $startDate, $endDate);
    }

    public function invoices(Request $request)
    {
        $filter = $this->resolvePeriodAndDates($request);
        $period = $filter['period'];
        $startDate = $filter['startDate'];
        $endDate = $filter['endDate'];
        $stats = $this->getGeneralStats($startDate, $endDate);
        $items = Invoice::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('admin.accounting.reports-invoices', compact('stats', 'items', 'startDate', 'endDate', 'period', 'filter'));
    }

    public function payments(Request $request)
    {
        $filter = $this->resolvePeriodAndDates($request);
        $period = $filter['period'];
        $startDate = $filter['startDate'];
        $endDate = $filter['endDate'];
        $stats = $this->getGeneralStats($startDate, $endDate);
        $items = Payment::with(['user', 'invoice'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('admin.accounting.reports-payments', compact('stats', 'items', 'startDate', 'endDate', 'period', 'filter'));
    }

    public function transactions(Request $request)
    {
        $filter = $this->resolvePeriodAndDates($request);
        $period = $filter['period'];
        $startDate = $filter['startDate'];
        $endDate = $filter['endDate'];
        $stats = $this->getGeneralStats($startDate, $endDate);
        $items = Transaction::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('admin.accounting.reports-transactions', compact('stats', 'items', 'startDate', 'endDate', 'period', 'filter'));
    }

    public function expenses(Request $request)
    {
        $filter = $this->resolvePeriodAndDates($request);
        $period = $filter['period'];
        $startDate = $filter['startDate'];
        $endDate = $filter['endDate'];
        $stats = $this->getGeneralStats($startDate, $endDate);
        $items = Expense::query()
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('admin.accounting.reports-expenses', compact('stats', 'items', 'startDate', 'endDate', 'period', 'filter'));
    }

    public function wallets(Request $request)
    {
        $filter = $this->resolvePeriodAndDates($request);
        $period = $filter['period'];
        $startDate = $filter['startDate'];
        $endDate = $filter['endDate'];
        $stats = $this->getGeneralStats($startDate, $endDate);
        $items = Wallet::academyWallets()
            ->withCount('transactions')
            ->orderBy('balance', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('admin.accounting.reports-wallets', compact('stats', 'items', 'startDate', 'endDate', 'period', 'filter'));
    }

    public function orders(Request $request)
    {
        $filter = $this->resolvePeriodAndDates($request);
        $period = $filter['period'];
        $startDate = $filter['startDate'];
        $endDate = $filter['endDate'];
        $stats = $this->getGeneralStats($startDate, $endDate);
        $items = Order::with(['user', 'course', 'learningPath'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('admin.accounting.reports-orders', compact('stats', 'items', 'startDate', 'endDate', 'period', 'filter'));
    }

    /**
     * @return array{
     *     period: string,
     *     startDate: Carbon,
     *     endDate: Carbon,
     *     filterStart: string,
     *     filterEnd: string,
     *     periodLabel: string
     * }
     */
    private function resolvePeriodAndDates(Request $request): array
    {
        $period = $request->get('period', 'month');
        $inputStart = $request->get('start_date');
        $inputEnd = $request->get('end_date');

        if (! in_array($period, ['day', 'week', 'month', 'year', 'all', 'custom'], true)) {
            $period = 'month';
        }

        if ($period === 'custom') {
            if ($inputStart && $inputEnd) {
                $startDate = Carbon::parse($inputStart)->startOfDay();
                $endDate = Carbon::parse($inputEnd)->endOfDay();
                if ($startDate->gt($endDate)) {
                    [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
                }
            } else {
                $startDate = Carbon::now()->startOfMonth()->startOfDay();
                $endDate = Carbon::now()->endOfMonth()->endOfDay();
            }

            return [
                'period' => 'custom',
                'startDate' => $startDate,
                'endDate' => $endDate,
                'filterStart' => $startDate->format('Y-m-d'),
                'filterEnd' => $endDate->format('Y-m-d'),
                'periodLabel' => 'فترة مخصصة — '.$startDate->format('Y-m-d').' → '.$endDate->format('Y-m-d'),
            ];
        }

        $dates = $this->calculatePresetDateRange($period);
        $startDate = $dates['start'];
        $endDate = $dates['end'];

        return [
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filterStart' => $period === 'custom' ? ($inputStart ?: $startDate->format('Y-m-d')) : '',
            'filterEnd' => $period === 'custom' ? ($inputEnd ?: $endDate->format('Y-m-d')) : '',
            'periodLabel' => $this->periodLabel($period, $startDate, $endDate),
        ];
    }

    private function periodLabel(string $period, Carbon $startDate, Carbon $endDate): string
    {
        return match ($period) {
            'day' => 'اليوم — '.$startDate->format('Y-m-d'),
            'week' => 'هذا الأسبوع — '.$startDate->format('Y-m-d').' → '.$endDate->format('Y-m-d'),
            'month' => 'هذا الشهر — '.$startDate->format('Y-m'),
            'year' => 'هذه السنة — '.$startDate->format('Y'),
            'all' => 'كل الفترات — من '.$startDate->format('Y-m-d').' إلى '.$endDate->format('Y-m-d'),
            default => $startDate->format('Y-m-d').' → '.$endDate->format('Y-m-d'),
        };
    }

    private function calculatePresetDateRange(string $period): array
    {
        switch ($period) {
            case 'day':
                return [
                    'start' => Carbon::today()->startOfDay(),
                    'end' => Carbon::today()->endOfDay(),
                ];
            case 'week':
                return [
                    'start' => Carbon::now()->startOfWeek()->startOfDay(),
                    'end' => Carbon::now()->endOfWeek()->endOfDay(),
                ];
            case 'year':
                return [
                    'start' => Carbon::now()->startOfYear()->startOfDay(),
                    'end' => Carbon::now()->endOfYear()->endOfDay(),
                ];
            case 'all':
                return [
                    'start' => Carbon::parse('2020-01-01')->startOfDay(),
                    'end' => Carbon::now()->endOfDay(),
                ];
            case 'month':
            default:
                return [
                    'start' => Carbon::now()->startOfMonth()->startOfDay(),
                    'end' => Carbon::now()->endOfMonth()->endOfDay(),
                ];
        }
    }

    /** @deprecated Use resolvePeriodAndDates() */
    private function calculateDateRange($period, $startDate = null, $endDate = null)
    {
        if ($period === 'custom' && $startDate && $endDate) {
            return [
                'start' => Carbon::parse($startDate)->startOfDay(),
                'end' => Carbon::parse($endDate)->endOfDay(),
            ];
        }

        return $this->calculatePresetDateRange((string) $period);
    }

    private function getGeneralStats($startDate, $endDate)
    {
        // إجمالي الإيرادات من المدفوعات المكتملة (مصدر موحّد لتجنب الازدواج مع المعاملات)
        $totalRevenue = (float) Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('amount');

        // إجمالي المصروفات المعتمدة
        $totalExpenses = (float) Expense::where('status', 'approved')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        // مصروفات إضافية من معاملات مدينة غير مرتبطة بمصروف مسجّل
        $totalExpensesFromTransactions = (float) Transaction::where('type', 'debit')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotIn('category', ['expense', 'expense_payment'])
            ->sum('amount');

        $totalExpenses += $totalExpensesFromTransactions;

        // الربح الصافي
        $netProfit = $totalRevenue - $totalExpenses;

        // عدد الفواتير
        $totalInvoices = Invoice::whereBetween('created_at', [$startDate, $endDate])->count();
        $paidInvoices = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->count();
        $pendingInvoices = Invoice::whereIn('status', ['pending', 'overdue'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $invoiceAmounts = [
            'invoiced_total' => (float) Invoice::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount'),
            'collected_total' => (float) Payment::where('status', 'completed')
                ->whereBetween('paid_at', [$startDate, $endDate])
                ->sum('amount'),
            'discount_total' => (float) Invoice::whereBetween('created_at', [$startDate, $endDate])->sum('discount_amount'),
            'outstanding_total' => (float) Invoice::whereIn('status', ['pending', 'overdue'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total_amount'),
        ];

        // عدد المدفوعات
        $totalPayments = Payment::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedPayments = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->count();

        // عدد المعاملات
        $totalTransactions = Transaction::whereBetween('created_at', [$startDate, $endDate])->count();

        // محافظ المنصة (إجماليات دون ربط بفترة — حالة حاليّة)
        $walletStats = [
            'total_wallets' => Wallet::academyWallets()->count(),
            'active_wallets' => Wallet::academyWallets()->where('is_active', true)->count(),
            'total_balance' => (float) Wallet::academyWallets()->sum('balance'),
            'pending_balance' => (float) Wallet::academyWallets()->sum('pending_balance'),
        ];

        // الطلبات (كورسات ومسارات)
        $ordersTotal = Order::whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $ordersApproved = Order::where('status', Order::STATUS_APPROVED)
            ->whereBetween('approved_at', [$startDate, $endDate])
            ->sum('amount');
        $orderStats = [
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'approved_orders' => Order::where('status', Order::STATUS_APPROVED)->whereBetween('approved_at', [$startDate, $endDate])->count(),
            'pending_orders' => Order::where('status', Order::STATUS_PENDING)->whereBetween('created_at', [$startDate, $endDate])->count(),
            'rejected_orders' => Order::where('status', Order::STATUS_REJECTED)->whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_amount' => $ordersTotal,
            'approved_amount' => $ordersApproved,
        ];

        $academyStats = [
            'subscriptions_new_period' => Subscription::whereBetween('created_at', [$startDate, $endDate])->count(),
            'subscriptions_value_period' => (float) Subscription::whereBetween('created_at', [$startDate, $endDate])->sum('price'),
            'withdrawals_completed_period' => (float) WithdrawalRequest::where('status', WithdrawalRequest::STATUS_COMPLETED)
                ->whereBetween('processed_at', [$startDate, $endDate])
                ->sum('amount'),
            'withdrawals_pending_amount' => (float) WithdrawalRequest::where('status', WithdrawalRequest::STATUS_PENDING)->sum('amount'),
            'installment_agreements_active' => InstallmentAgreement::whereIn('status', [
                InstallmentAgreement::STATUS_ACTIVE,
                InstallmentAgreement::STATUS_OVERDUE,
            ])->count(),
            'installment_contracts_value_period' => (float) InstallmentAgreement::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount'),
            'installment_pending_scheduled' => (float) InstallmentPayment::where('status', InstallmentPayment::STATUS_PENDING)->sum('amount'),
            'offline_collected_period' => (float) OfflineCourseEnrollment::whereBetween('enrolled_at', [$startDate, $endDate])->sum('paid_amount'),
            'offline_outstanding_total' => (float) OfflineCourseEnrollment::query()->sum('remaining_amount'),
        ];

        return [
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'total_invoices' => $totalInvoices,
            'paid_invoices' => $paidInvoices,
            'pending_invoices' => $pendingInvoices,
            'total_payments' => $totalPayments,
            'completed_payments' => $completedPayments,
            'total_transactions' => $totalTransactions,
            'wallet_stats' => $walletStats,
            'order_stats' => $orderStats,
            'academy_stats' => $academyStats,
            'invoice_amounts' => $invoiceAmounts,
        ];
    }

    private function getDetailedFinancialReport(Carbon $startDate, Carbon $endDate): array
    {
        $invoiceByStatus = Invoice::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'status',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('SUM(discount_amount) as discount_amount'),
                DB::raw('SUM(subtotal) as subtotal')
            )
            ->groupBy('status')
            ->get();

        $invoiceByType = Invoice::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'type',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total_amount')
            )
            ->groupBy('type')
            ->get();

        $paymentsByStatus = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'status',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('status')
            ->get();

        $paymentsByMethod = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('payment_method')
            ->get();

        $transactionsByType = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'type',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('type')
            ->get();

        $recentPayments = Payment::with(['user', 'invoice'])
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->orderByDesc('paid_at')
            ->limit(15)
            ->get();

        $recentExpenses = Expense::query()
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderByDesc('expense_date')
            ->limit(15)
            ->get();

        $recentInvoices = Invoice::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        $profitLoss = [
            'revenue' => (float) Payment::where('status', 'completed')
                ->whereBetween('paid_at', [$startDate, $endDate])
                ->sum('amount'),
            'expenses' => (float) Expense::where('status', 'approved')
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->sum('amount'),
            'expenses_from_revenue' => AccountingAnalytics::expensesBetween($startDate, $endDate, AccountingAnalytics::FUNDING_REVENUE),
            'expenses_out_of_pocket' => AccountingAnalytics::expensesBetween($startDate, $endDate, AccountingAnalytics::FUNDING_OUT_OF_POCKET),
            'withdrawals' => (float) WithdrawalRequest::where('status', WithdrawalRequest::STATUS_COMPLETED)
                ->whereBetween('processed_at', [$startDate, $endDate])
                ->sum('amount'),
        ];
        $profitLoss['net'] = $profitLoss['revenue'] - $profitLoss['expenses'] - $profitLoss['withdrawals'];
        $profitLoss['operational_net'] = $profitLoss['revenue'] - $profitLoss['expenses_from_revenue'];
        $profitLoss['break_even'] = AccountingAnalytics::breakEvenAnalysis($startDate, $endDate);

        return [
            'invoice_by_status' => $invoiceByStatus,
            'invoice_by_type' => $invoiceByType,
            'payments_by_status' => $paymentsByStatus,
            'payments_by_method' => $paymentsByMethod,
            'transactions_by_type' => $transactionsByType,
            'recent_payments' => $recentPayments,
            'recent_expenses' => $recentExpenses,
            'recent_invoices' => $recentInvoices,
            'profit_loss' => $profitLoss,
        ];
    }

    private function getRevenueReports($startDate, $endDate)
    {
        $revenueFromPayments = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->select(
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count'),
                'payment_method'
            )
            ->groupBy('payment_method')
            ->get();

        $revenueFromTransactions = Transaction::where('type', 'credit')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('payment_id')
            ->select(
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count'),
                'category'
            )
            ->groupBy('category')
            ->get();

        return [
            'from_payments' => $revenueFromPayments,
            'from_transactions' => $revenueFromTransactions,
        ];
    }

    private function getExpenseReports($startDate, $endDate)
    {
        // المصروفات من جدول المصروفات
        $expenses = Expense::where('status', 'approved')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->select(
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count'),
                'category'
            )
            ->groupBy('category')
            ->get();

        // المصروفات من المعاملات
        $expensesFromTransactions = Transaction::where('type', 'debit')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count'),
                'category'
            )
            ->groupBy('category')
            ->get();

        return [
            'from_expenses' => $expenses,
            'from_transactions' => $expensesFromTransactions,
        ];
    }

    private function getInvoiceReports($startDate, $endDate)
    {
        return Invoice::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('COUNT(*) as count'),
                'status',
                'type'
            )
            ->groupBy('status', 'type')
            ->get();
    }

    private function getPaymentReports($startDate, $endDate)
    {
        return Payment::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count'),
                'status',
                'payment_method'
            )
            ->groupBy('status', 'payment_method')
            ->get();
    }

    private function getTransactionReports($startDate, $endDate)
    {
        return Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count'),
                'type',
                'status',
                'category'
            )
            ->groupBy('type', 'status', 'category')
            ->get();
    }

    private function getMonthlyData($startDate, $endDate)
    {
        $months = [];
        $revenues = [];
        $expenses = [];

        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current->lte($end)) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            $monthRevenue = Payment::where('status', 'completed')
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->sum('amount');

            $monthExpense = Expense::where('status', 'approved')
                ->whereBetween('expense_date', [$monthStart, $monthEnd])
                ->sum('amount');

            $monthExpense += Transaction::where('type', 'debit')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');

            $months[] = $current->format('Y-m');
            $revenues[] = $monthRevenue;
            $expenses[] = $monthExpense;

            $current->addMonth();
        }

        return [
            'months' => $months,
            'revenues' => $revenues,
            'expenses' => $expenses,
        ];
    }

    private function getDailyData($startDate = null, $endDate = null)
    {
        $days = [];
        $revenues = [];
        $expenses = [];

        $end = $endDate ? Carbon::parse($endDate)->copy()->endOfDay() : Carbon::now()->endOfDay();
        $start = $startDate ? Carbon::parse($startDate)->copy()->startOfDay() : Carbon::now()->subDays(29)->startOfDay();

        if ($start->diffInDays($end) > 90) {
            $start = $end->copy()->subDays(89)->startOfDay();
        }

        $current = $start->copy();
        while ($current->lte($end)) {
            $dayStart = $current->copy()->startOfDay();
            $dayEnd = $current->copy()->endOfDay();

            $dayRevenue = Payment::where('status', 'completed')
                ->whereBetween('paid_at', [$dayStart, $dayEnd])
                ->sum('amount');

            $dayExpense = Expense::where('status', 'approved')
                ->whereBetween('expense_date', [$dayStart, $dayEnd])
                ->sum('amount');

            $days[] = $current->format('Y-m-d');
            $revenues[] = (float) $dayRevenue;
            $expenses[] = (float) $dayExpense;

            $current->addDay();
        }

        return [
            'days' => $days,
            'revenues' => $revenues,
            'expenses' => $expenses,
        ];
    }

    public function export(Request $request)
    {
        $filter = $this->resolvePeriodAndDates($request);
        $period = $filter['period'];
        $startDate = $filter['startDate'];
        $endDate = $filter['endDate'];
        $type = $request->get('type', 'all');

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Mindlytics')
            ->setTitle('التقارير المالية - المنصة')
            ->setSubject('تقارير محاسبية شاملة');

        $headerStyle = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ];
        $headerFont = [];
        $border = ['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]]];

        $sheetIndex = 0;

        // ورقة الملخص
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('الملخص المالي');
        $sheet->setRightToLeft(true);
        $stats = $this->getGeneralStats($startDate, $endDate);
        $this->writeSummarySheet($sheet, $stats, $startDate, $endDate, $headerStyle, $headerFont, $border);

        if ($type === 'all') {
            $sheetIndex++;
            $this->addInvoicesSheet($spreadsheet, $sheetIndex, $startDate, $endDate, $headerStyle, $headerFont, $border);
            $sheetIndex++;
            $this->addPaymentsSheet($spreadsheet, $sheetIndex, $startDate, $endDate, $headerStyle, $headerFont, $border);
            $sheetIndex++;
            $this->addTransactionsSheet($spreadsheet, $sheetIndex, $startDate, $endDate, $headerStyle, $headerFont, $border);
            $sheetIndex++;
            $this->addExpensesSheet($spreadsheet, $sheetIndex, $startDate, $endDate, $headerStyle, $headerFont, $border);
            $sheetIndex++;
            $this->addWalletsSheet($spreadsheet, $sheetIndex, $headerStyle, $headerFont, $border);
            $sheetIndex++;
            $this->addOrdersSheet($spreadsheet, $sheetIndex, $startDate, $endDate, $headerStyle, $headerFont, $border);
            $sheetIndex++;
            $this->addSubscriptionsSheet($spreadsheet, $sheetIndex, $startDate, $endDate, $headerStyle, $headerFont, $border);
            $sheetIndex++;
            $this->addWithdrawalsSheet($spreadsheet, $sheetIndex, $startDate, $endDate, $headerStyle, $headerFont, $border);
            $sheetIndex++;
            $this->addInstallmentsSheet($spreadsheet, $sheetIndex, $startDate, $endDate, $headerStyle, $headerFont, $border);
            $sheetIndex++;
            $this->addOfflineEnrollmentsSheet($spreadsheet, $sheetIndex, $startDate, $endDate, $headerStyle, $headerFont, $border);
            $sheetIndex++;
            $this->addChartOfAccountsSheet($spreadsheet, $sheetIndex, $headerStyle, $headerFont, $border);
        } elseif (in_array($type, ['invoices', 'payments', 'transactions', 'expenses', 'wallets', 'orders', 'subscriptions', 'withdrawals', 'installments', 'offline_enrollments', 'chart'], true)) {
            if ($type === 'invoices') {
                $this->addInvoicesSheet($spreadsheet, 1, $startDate, $endDate, $headerStyle, $headerFont, $border);
            }
            if ($type === 'payments') {
                $this->addPaymentsSheet($spreadsheet, 1, $startDate, $endDate, $headerStyle, $headerFont, $border);
            }
            if ($type === 'transactions') {
                $this->addTransactionsSheet($spreadsheet, 1, $startDate, $endDate, $headerStyle, $headerFont, $border);
            }
            if ($type === 'expenses') {
                $this->addExpensesSheet($spreadsheet, 1, $startDate, $endDate, $headerStyle, $headerFont, $border);
            }
            if ($type === 'wallets') {
                $this->addWalletsSheet($spreadsheet, 1, $headerStyle, $headerFont, $border);
            }
            if ($type === 'orders') {
                $this->addOrdersSheet($spreadsheet, 1, $startDate, $endDate, $headerStyle, $headerFont, $border);
            }
            if ($type === 'subscriptions') {
                $this->addSubscriptionsSheet($spreadsheet, 1, $startDate, $endDate, $headerStyle, $headerFont, $border);
            }
            if ($type === 'withdrawals') {
                $this->addWithdrawalsSheet($spreadsheet, 1, $startDate, $endDate, $headerStyle, $headerFont, $border);
            }
            if ($type === 'installments') {
                $this->addInstallmentsSheet($spreadsheet, 1, $startDate, $endDate, $headerStyle, $headerFont, $border);
            }
            if ($type === 'offline_enrollments') {
                $this->addOfflineEnrollmentsSheet($spreadsheet, 1, $startDate, $endDate, $headerStyle, $headerFont, $border);
            }
            if ($type === 'chart') {
                $this->addChartOfAccountsSheet($spreadsheet, 1, $headerStyle, $headerFont, $border);
            }
        }

        $filenameAscii = 'Mindlytics_accounting_' . $startDate->format('Y-m-d') . '_' . $endDate->format('Y-m-d') . '.xlsx';
        $filenameUtf8 = 'تقارير_مالية_Mindlytics_' . $startDate->format('Y-m-d') . '_' . $endDate->format('Y-m-d') . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filenameAscii . '"; filename*=UTF-8\'\'' . rawurlencode($filenameUtf8),
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function writeSummarySheet($sheet, $stats, $startDate, $endDate, $headerStyle, $headerFont, $border)
    {
        $sheet->setCellValue('A1', 'تقارير مالية شاملة - منصة Mindlytics');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->setCellValue('A2', 'الفترة: من ' . $startDate->format('Y-m-d') . ' إلى ' . $endDate->format('Y-m-d'));
        $sheet->mergeCells('A2:D2');
        $sheet->setCellValue('A3', 'تاريخ التصدير: ' . now()->format('Y-m-d H:i'));
        $sheet->mergeCells('A3:D3');
        $row = 5;
        $sheet->setCellValue('A' . $row, 'البند');
        $sheet->setCellValue('B' . $row, 'القيمة');
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($headerStyle);
        $row++;
        $items = [
            ['إجمالي الإيرادات', number_format($stats['total_revenue'], 2) . ' ج.م'],
            ['إجمالي المصروفات', number_format($stats['total_expenses'], 2) . ' ج.م'],
            ['الربح الصافي', number_format($stats['net_profit'], 2) . ' ج.م'],
            ['عدد الفواتير', $stats['total_invoices']],
            ['فواتير مدفوعة', $stats['paid_invoices']],
            ['فواتير معلقة', $stats['pending_invoices']],
            ['عدد المدفوعات', $stats['total_payments']],
            ['مدفوعات مكتملة', $stats['completed_payments']],
            ['عدد المعاملات', $stats['total_transactions']],
            ['عدد محافظ المنصة', $stats['wallet_stats']['total_wallets']],
            ['محافظ نشطة', $stats['wallet_stats']['active_wallets']],
            ['إجمالي أرصدة المحافظ', number_format($stats['wallet_stats']['total_balance'], 2) . ' ج.م'],
            ['الرصيد المعلق للمحافظ', number_format($stats['wallet_stats']['pending_balance'], 2) . ' ج.م'],
            ['عدد الطلبات (الفترة)', $stats['order_stats']['total_orders']],
            ['طلبات معتمدة', $stats['order_stats']['approved_orders']],
            ['طلبات معلقة', $stats['order_stats']['pending_orders']],
            ['إجمالي مبالغ الطلبات', number_format($stats['order_stats']['total_amount'], 2) . ' ج.م'],
            ['مبالغ الطلبات المعتمدة', number_format($stats['order_stats']['approved_amount'], 2) . ' ج.م'],
            ['—', '—'],
            ['اشتراكات جديدة (الفترة)', $stats['academy_stats']['subscriptions_new_period'] ?? 0],
            ['قيمة اشتراكات جديدة (الفترة)', number_format($stats['academy_stats']['subscriptions_value_period'] ?? 0, 2) . ' ج.م'],
            ['سحوبات مكتملة (الفترة)', number_format($stats['academy_stats']['withdrawals_completed_period'] ?? 0, 2) . ' ج.م'],
            ['سحوبات معلقة (مبلغ حالي)', number_format($stats['academy_stats']['withdrawals_pending_amount'] ?? 0, 2) . ' ج.م'],
            ['اتفاقيات تقسيط نشطة/متأخرة', $stats['academy_stats']['installment_agreements_active'] ?? 0],
            ['قيمة عقود تقسيط (إنشاء في الفترة)', number_format($stats['academy_stats']['installment_contracts_value_period'] ?? 0, 2) . ' ج.م'],
            ['مجموع أقساط مستحقة (جدول)', number_format($stats['academy_stats']['installment_pending_scheduled'] ?? 0, 2) . ' ج.م'],
            ['تحصيلات تسجيل أوفلاين (الفترة)', number_format($stats['academy_stats']['offline_collected_period'] ?? 0, 2) . ' ج.م'],
            ['متبقي أوفلاين (إجمالي)', number_format($stats['academy_stats']['offline_outstanding_total'] ?? 0, 2) . ' ج.م'],
        ];
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, $item[0]);
            $sheet->setCellValue('B' . $row, $item[1]);
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($border);
            $row++;
        }
        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(22);
    }

    private function addInvoicesSheet($spreadsheet, $index, $startDate, $endDate, $headerStyle, $headerFont, $border)
    {
        $sheet = $spreadsheet->createSheet($index);
        $sheet->setTitle('الفواتير');
        $sheet->setRightToLeft(true);
        $headers = ['رقم الفاتورة', 'العميل', 'النوع', 'المبلغ الفرعي', 'الضريبة', 'الخصم', 'المبلغ الإجمالي', 'الحالة', 'تاريخ الاستحقاق', 'تاريخ الإنشاء'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }
        $invoices = Invoice::with('user')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $row = 2;
        foreach ($invoices as $inv) {
            $sheet->setCellValue('A' . $row, $inv->invoice_number);
            $sheet->setCellValue('B' . $row, $inv->user->name ?? 'غير معروف');
            $sheet->setCellValue('C' . $row, $inv->type ?? '-');
            $sheet->setCellValue('D' . $row, (float) $inv->subtotal);
            $sheet->setCellValue('E' . $row, (float) ($inv->tax_amount ?? 0));
            $sheet->setCellValue('F' . $row, (float) ($inv->discount_amount ?? 0));
            $sheet->setCellValue('G' . $row, (float) $inv->total_amount);
            $sheet->setCellValue('H' . $row, $inv->status);
            $sheet->setCellValue('I' . $row, $inv->due_date ? $inv->due_date->format('Y-m-d') : '-');
            $sheet->setCellValue('J' . $row, $inv->created_at->format('Y-m-d H:i'));
            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($border);
            $row++;
        }
        for ($c = 0; $c < 10; $c++) $sheet->getColumnDimensionByColumn($c + 1)->setAutoSize(true);
    }

    private function addPaymentsSheet($spreadsheet, $index, $startDate, $endDate, $headerStyle, $headerFont, $border)
    {
        $sheet = $spreadsheet->createSheet($index);
        $sheet->setTitle('المدفوعات');
        $sheet->setRightToLeft(true);
        $headers = ['رقم الدفعة', 'العميل', 'رقم الفاتورة', 'المبلغ', 'طريقة الدفع', 'الحالة', 'تاريخ الدفع', 'مرجع', 'تاريخ الإنشاء'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }
        $payments = Payment::with(['user', 'invoice'])->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $row = 2;
        foreach ($payments as $p) {
            $sheet->setCellValue('A' . $row, $p->payment_number);
            $sheet->setCellValue('B' . $row, $p->user->name ?? 'غير معروف');
            $sheet->setCellValue('C' . $row, $p->invoice->invoice_number ?? '-');
            $sheet->setCellValue('D' . $row, (float) $p->amount);
            $sheet->setCellValue('E' . $row, $p->payment_method ?? '-');
            $sheet->setCellValue('F' . $row, $p->status);
            $sheet->setCellValue('G' . $row, $p->paid_at ? $p->paid_at->format('Y-m-d H:i') : '-');
            $sheet->setCellValue('H' . $row, $p->reference_number ?? '-');
            $sheet->setCellValue('I' . $row, $p->created_at->format('Y-m-d H:i'));
            $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray($border);
            $row++;
        }
        for ($c = 0; $c < 9; $c++) $sheet->getColumnDimensionByColumn($c + 1)->setAutoSize(true);
    }

    private function addTransactionsSheet($spreadsheet, $index, $startDate, $endDate, $headerStyle, $headerFont, $border)
    {
        $sheet = $spreadsheet->createSheet($index);
        $sheet->setTitle('المعاملات');
        $sheet->setRightToLeft(true);
        $headers = ['رقم المعاملة', 'العميل', 'النوع', 'الفئة', 'المبلغ', 'الحالة', 'الوصف', 'التاريخ'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }
        $transactions = Transaction::with('user')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $row = 2;
        foreach ($transactions as $t) {
            $sheet->setCellValue('A' . $row, $t->transaction_number ?? 'N/A');
            $sheet->setCellValue('B' . $row, $t->user->name ?? 'غير معروف');
            $sheet->setCellValue('C' . $row, $t->type === 'credit' ? 'إيراد' : 'مصروف');
            $sheet->setCellValue('D' . $row, $t->category ?? '-');
            $sheet->setCellValue('E' . $row, (float) $t->amount);
            $sheet->setCellValue('F' . $row, $t->status);
            $sheet->setCellValue('G' . $row, $t->description ?? '-');
            $sheet->setCellValue('H' . $row, $t->created_at->format('Y-m-d H:i'));
            $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($border);
            $row++;
        }
        for ($c = 0; $c < 8; $c++) $sheet->getColumnDimensionByColumn($c + 1)->setAutoSize(true);
    }

    private function addExpensesSheet($spreadsheet, $index, $startDate, $endDate, $headerStyle, $headerFont, $border)
    {
        $sheet = $spreadsheet->createSheet($index);
        $sheet->setTitle('المصروفات');
        $sheet->setRightToLeft(true);
        $headers = ['رقم المصروف', 'العنوان', 'الفئة', 'المبلغ', 'طريقة الدفع', 'الحالة', 'تاريخ المصروف', 'التاريخ'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->orderBy('expense_date', 'desc')->get();
        $row = 2;
        foreach ($expenses as $e) {
            $sheet->setCellValue('A' . $row, $e->expense_number ?? 'N/A');
            $sheet->setCellValue('B' . $row, $e->title ?? '-');
            $sheet->setCellValue('C' . $row, \App\Models\Expense::categoryLabel($e->category));
            $sheet->setCellValue('D' . $row, (float) $e->amount);
            $sheet->setCellValue('E' . $row, $e->payment_method ?? '-');
            $sheet->setCellValue('F' . $row, $e->status);
            $sheet->setCellValue('G' . $row, $e->expense_date ? $e->expense_date->format('Y-m-d') : '-');
            $sheet->setCellValue('H' . $row, $e->created_at->format('Y-m-d H:i'));
            $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($border);
            $row++;
        }
        for ($c = 0; $c < 8; $c++) $sheet->getColumnDimensionByColumn($c + 1)->setAutoSize(true);
    }

    private function addWalletsSheet($spreadsheet, $index, $headerStyle, $headerFont, $border)
    {
        $sheet = $spreadsheet->createSheet($index);
        $sheet->setTitle('المحافظ');
        $sheet->setRightToLeft(true);
        $headers = ['اسم المحفظة', 'النوع', 'رقم الحساب', 'البنك', 'صاحب الحساب', 'الرصيد', 'الرصيد المعلق', 'نشطة', 'عدد المعاملات', 'تاريخ التحديث'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }
        $wallets = Wallet::academyWallets()->withCount('transactions')->orderBy('balance', 'desc')->get();
        $row = 2;
        foreach ($wallets as $w) {
            $sheet->setCellValue('A' . $row, $w->name ?? '-');
            $sheet->setCellValue('B' . $row, Wallet::typeLabel($w->type ?? ''));
            $sheet->setCellValue('C' . $row, $w->account_number ?? '-');
            $sheet->setCellValue('D' . $row, $w->bank_name ?? '-');
            $sheet->setCellValue('E' . $row, $w->account_holder ?? '-');
            $sheet->setCellValue('F' . $row, (float) $w->balance);
            $sheet->setCellValue('G' . $row, (float) ($w->pending_balance ?? 0));
            $sheet->setCellValue('H' . $row, $w->is_active ? 'نعم' : 'لا');
            $sheet->setCellValue('I' . $row, $w->transactions_count ?? 0);
            $sheet->setCellValue('J' . $row, $w->updated_at->format('Y-m-d H:i'));
            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($border);
            $row++;
        }
        for ($c = 0; $c < 10; $c++) $sheet->getColumnDimensionByColumn($c + 1)->setAutoSize(true);
    }

    private function addOrdersSheet($spreadsheet, $index, $startDate, $endDate, $headerStyle, $headerFont, $border)
    {
        $sheet = $spreadsheet->createSheet($index);
        $sheet->setTitle('الطلبات');
        $sheet->setRightToLeft(true);
        $headers = ['رقم الطلب', 'العميل', 'نوع الطلب', 'المنتج', 'المبلغ', 'طريقة الدفع', 'الحالة', 'تاريخ الطلب', 'تاريخ الموافقة'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }
        $orders = Order::with(['user', 'course', 'learningPath'])->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $row = 2;
        foreach ($orders as $o) {
            $product = $o->course ? $o->course->title : ($o->learningPath ? $o->learningPath->name : '—');
            $sheet->setCellValue('A' . $row, $o->id);
            $sheet->setCellValue('B' . $row, $o->user->name ?? 'غير معروف');
            $sheet->setCellValue('C' . $row, $o->advanced_course_id ? 'كورس' : 'مسار');
            $sheet->setCellValue('D' . $row, $product);
            $sheet->setCellValue('E' . $row, (float) $o->amount);
            $sheet->setCellValue('F' . $row, $o->payment_method ?? '-');
            $sheet->setCellValue('G' . $row, $o->status === Order::STATUS_APPROVED ? 'معتمد' : ($o->status === Order::STATUS_PENDING ? 'معلق' : 'مرفوض'));
            $sheet->setCellValue('H' . $row, $o->created_at->format('Y-m-d H:i'));
            $sheet->setCellValue('I' . $row, $o->approved_at ? $o->approved_at->format('Y-m-d H:i') : '-');
            $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray($border);
            $row++;
        }
        for ($c = 0; $c < 9; $c++) {
            $sheet->getColumnDimensionByColumn($c + 1)->setAutoSize(true);
        }
    }

    private function addSubscriptionsSheet(
        Spreadsheet $spreadsheet,
        int $index,
        Carbon $startDate,
        Carbon $endDate,
        array $headerStyle,
        array $headerFont,
        array $border
    ): void {
        $sheet = $spreadsheet->createSheet($index);
        $sheet->setTitle('الاشتراكات');
        $sheet->setRightToLeft(true);
        $headers = ['المعرف', 'الطالب', 'نوع الاشتراك', 'الخطة', 'السعر', 'الحالة', 'بداية', 'نهاية', 'تاريخ الإنشاء'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }
        $rows = Subscription::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
        $row = 2;
        foreach ($rows as $s) {
            $sheet->setCellValue('A' . $row, $s->id);
            $sheet->setCellValue('B' . $row, $s->user->name ?? '—');
            $sheet->setCellValue('C' . $row, Subscription::typeLabel($s->subscription_type));
            $sheet->setCellValue('D' . $row, $s->plan_name ?? '—');
            $sheet->setCellValue('E' . $row, (float) $s->price);
            $sheet->setCellValue('F' . $row, $s->status ?? '—');
            $sheet->setCellValue('G' . $row, $s->start_date ? $s->start_date->format('Y-m-d') : '—');
            $sheet->setCellValue('H' . $row, $s->end_date ? $s->end_date->format('Y-m-d') : '—');
            $sheet->setCellValue('I' . $row, $s->created_at->format('Y-m-d H:i'));
            $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray($border);
            $row++;
        }
        $sheet->getStyle('E2:E' . max(1, $row - 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        for ($c = 0; $c < 9; $c++) {
            $sheet->getColumnDimensionByColumn($c + 1)->setAutoSize(true);
        }
    }

    private function addWithdrawalsSheet(
        Spreadsheet $spreadsheet,
        int $index,
        Carbon $startDate,
        Carbon $endDate,
        array $headerStyle,
        array $headerFont,
        array $border
    ): void {
        $sheet = $spreadsheet->createSheet($index);
        $sheet->setTitle('السحوبات');
        $sheet->setRightToLeft(true);
        $headers = ['رقم الطلب', 'المدرب', 'المبلغ', 'الحالة', 'طريقة الدفع', 'البنك', 'تاريخ الإنشاء', 'تاريخ المعالجة'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }
        $rows = WithdrawalRequest::with('instructor')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
        $row = 2;
        foreach ($rows as $w) {
            $sheet->setCellValue('A' . $row, $w->request_number ?? $w->id);
            $sheet->setCellValue('B' . $row, $w->instructor->name ?? '—');
            $sheet->setCellValue('C' . $row, (float) $w->amount);
            $sheet->setCellValue('D' . $row, $w->status_label ?? $w->status);
            $sheet->setCellValue('E' . $row, $w->payment_method_label ?? $w->payment_method);
            $sheet->setCellValue('F' . $row, $w->bank_name ?? '—');
            $sheet->setCellValue('G' . $row, $w->created_at->format('Y-m-d H:i'));
            $sheet->setCellValue('H' . $row, $w->processed_at ? $w->processed_at->format('Y-m-d H:i') : '—');
            $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($border);
            $row++;
        }
        $sheet->getStyle('C2:C' . max(1, $row - 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        for ($c = 0; $c < 8; $c++) {
            $sheet->getColumnDimensionByColumn($c + 1)->setAutoSize(true);
        }
    }

    private function addInstallmentsSheet(
        Spreadsheet $spreadsheet,
        int $index,
        Carbon $startDate,
        Carbon $endDate,
        array $headerStyle,
        array $headerFont,
        array $border
    ): void {
        $sheet = $spreadsheet->createSheet($index);
        $sheet->setTitle('التقسيط');
        $sheet->setRightToLeft(true);
        $headers = ['رقم الاتفاقية', 'الطالب', 'إجمالي العقد', 'المقدم', 'عدد الأقساط', 'الحالة', 'بداية', 'تاريخ الإنشاء'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }
        $rows = InstallmentAgreement::with('student')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
        $row = 2;
        foreach ($rows as $a) {
            $sheet->setCellValue('A' . $row, $a->id);
            $sheet->setCellValue('B' . $row, $a->student->name ?? '—');
            $sheet->setCellValue('C' . $row, (float) $a->total_amount);
            $sheet->setCellValue('D' . $row, (float) $a->deposit_amount);
            $sheet->setCellValue('E' . $row, (int) $a->installments_count);
            $sheet->setCellValue('F' . $row, $a->status);
            $sheet->setCellValue('G' . $row, $a->start_date ? $a->start_date->format('Y-m-d') : '—');
            $sheet->setCellValue('H' . $row, $a->created_at->format('Y-m-d H:i'));
            $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($border);
            $row++;
        }
        $sheet->getStyle('C2:D' . max(1, $row - 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        for ($c = 0; $c < 8; $c++) {
            $sheet->getColumnDimensionByColumn($c + 1)->setAutoSize(true);
        }
    }

    private function addOfflineEnrollmentsSheet(
        Spreadsheet $spreadsheet,
        int $index,
        Carbon $startDate,
        Carbon $endDate,
        array $headerStyle,
        array $headerFont,
        array $border
    ): void {
        $sheet = $spreadsheet->createSheet($index);
        $sheet->setTitle('تسجيل أوفلاين');
        $sheet->setRightToLeft(true);
        $headers = ['المعرف', 'الطالب', 'الكورس', 'القناة', 'إجمالي', 'مدفوع', 'متبقي', 'حالة السداد', 'تاريخ التسجيل'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }
        $rows = OfflineCourseEnrollment::with(['student', 'course'])
            ->whereBetween('enrolled_at', [$startDate, $endDate])
            ->orderBy('enrolled_at', 'desc')
            ->get();
        $row = 2;
        foreach ($rows as $e) {
            $sheet->setCellValue('A' . $row, $e->id);
            $sheet->setCellValue('B' . $row, $e->student->name ?? '—');
            $sheet->setCellValue('C' . $row, $e->course->title ?? '—');
            $sheet->setCellValue('D' . $row, $e->enrollment_channel ?? '—');
            $sheet->setCellValue('E' . $row, (float) $e->total_amount);
            $sheet->setCellValue('F' . $row, (float) $e->paid_amount);
            $sheet->setCellValue('G' . $row, (float) $e->remaining_amount);
            $sheet->setCellValue('H' . $row, $e->payment_status ?? '—');
            $sheet->setCellValue('I' . $row, $e->enrolled_at ? $e->enrolled_at->format('Y-m-d H:i') : '—');
            $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray($border);
            $row++;
        }
        $sheet->getStyle('E2:G' . max(1, $row - 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        for ($c = 0; $c < 9; $c++) {
            $sheet->getColumnDimensionByColumn($c + 1)->setAutoSize(true);
        }
    }

    private function addChartOfAccountsSheet(
        Spreadsheet $spreadsheet,
        int $index,
        array $headerStyle,
        array $headerFont,
        array $border
    ): void {
        $sheet = $spreadsheet->createSheet($index);
        $sheet->setTitle('شجرة الحسابات');
        $sheet->setRightToLeft(true);
        $headers = ['المستوى', 'الكود', 'اسم الحساب', 'النوع', 'مصدر في النظام'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }
        $flat = [];
        foreach (config('accounting_chart.roots', []) as $root) {
            $flat[] = ['level' => 0, 'code' => $root['code'] ?? '', 'name' => $root['name'] ?? '', 'type' => $root['type'] ?? '', 'source' => ''];
            $flat = array_merge($flat, $this->flattenChartChildren($root['children'] ?? [], 1));
        }
        $row = 2;
        foreach ($flat as $item) {
            $sheet->setCellValue('A' . $row, $item['level']);
            $sheet->setCellValue('B' . $row, $item['code']);
            $sheet->setCellValue('C' . $row, $item['name']);
            $sheet->setCellValue('D' . $row, $item['type']);
            $sheet->setCellValue('E' . $row, $item['source'] ?? '');
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($border);
            $row++;
        }
        foreach (range('A', 'E') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
    }

    /**
     * @return array<int, array{level: int, code: string, name: string, type: string, source: string}>
     */
    private function flattenChartChildren(array $nodes, int $level): array
    {
        $out = [];
        foreach ($nodes as $node) {
            $isGroup = ! empty($node['children']);
            $name = ($node['name'] ?? '');
            if ($isGroup) {
                $out[] = [
                    'level' => $level,
                    'code' => $node['code'] ?? '',
                    'name' => $name,
                    'type' => $node['type'] ?? '',
                    'source' => '',
                ];
                $out = array_merge($out, $this->flattenChartChildren($node['children'], $level + 1));
            } else {
                $out[] = [
                    'level' => $level,
                    'code' => $node['code'] ?? '',
                    'name' => $name,
                    'type' => $node['type'] ?? '',
                    'source' => $node['source'] ?? '',
                ];
            }
        }

        return $out;
    }

}

