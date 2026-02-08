<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class AccountingReportsController extends Controller
{
    public function index(Request $request)
    {
        // تحديد الفترة الزمنية
        $period = $request->get('period', 'month'); // day, week, month, year, all
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // حساب التواريخ حسب الفترة
        $dates = $this->calculateDateRange($period, $startDate, $endDate);
        $startDate = $dates['start'];
        $endDate = $dates['end'];

        // إحصائيات عامة
        $stats = $this->getGeneralStats($startDate, $endDate);

        // تقارير الإيرادات
        $revenueReports = $this->getRevenueReports($startDate, $endDate);

        // تقارير المصروفات
        $expenseReports = $this->getExpenseReports($startDate, $endDate);

        // تقارير الفواتير
        $invoiceReports = $this->getInvoiceReports($startDate, $endDate);

        // تقارير المدفوعات
        $paymentReports = $this->getPaymentReports($startDate, $endDate);

        // تقارير المعاملات
        $transactionReports = $this->getTransactionReports($startDate, $endDate);

        // البيانات الشهرية (لرسم بياني)
        $monthlyData = $this->getMonthlyData($startDate, $endDate);

        // البيانات اليومية (آخر 30 يوم)
        $dailyData = $this->getDailyData();

        // بيانات تفصيلية للجداول
        $detailedInvoices = Invoice::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $detailedPayments = Payment::with(['user', 'invoice'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $detailedTransactions = Transaction::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('admin.accounting.reports', compact(
            'stats',
            'revenueReports',
            'expenseReports',
            'invoiceReports',
            'paymentReports',
            'transactionReports',
            'monthlyData',
            'dailyData',
            'detailedInvoices',
            'detailedPayments',
            'detailedTransactions',
            'period',
            'startDate',
            'endDate'
        ));
    }

    private function calculateDateRange($period, $startDate = null, $endDate = null)
    {
        if ($startDate && $endDate) {
            return [
                'start' => Carbon::parse($startDate)->startOfDay(),
                'end' => Carbon::parse($endDate)->endOfDay()
            ];
        }

        switch ($period) {
            case 'day':
                return [
                    'start' => Carbon::today()->startOfDay(),
                    'end' => Carbon::today()->endOfDay()
                ];
            case 'week':
                return [
                    'start' => Carbon::now()->startOfWeek()->startOfDay(),
                    'end' => Carbon::now()->endOfWeek()->endOfDay()
                ];
            case 'month':
                return [
                    'start' => Carbon::now()->startOfMonth()->startOfDay(),
                    'end' => Carbon::now()->endOfMonth()->endOfDay()
                ];
            case 'year':
                return [
                    'start' => Carbon::now()->startOfYear()->startOfDay(),
                    'end' => Carbon::now()->endOfYear()->endOfDay()
                ];
            case 'all':
            default:
                return [
                    'start' => Carbon::parse('2020-01-01')->startOfDay(),
                    'end' => Carbon::now()->endOfDay()
                ];
        }
    }

    private function getGeneralStats($startDate, $endDate)
    {
        // إجمالي الإيرادات (من المدفوعات المكتملة)
        $totalRevenue = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('amount');

        // إجمالي الإيرادات من المعاملات (نوع credit = دائن = إيراد)
        $totalRevenueFromTransactions = Transaction::where('type', 'credit')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $totalRevenue = $totalRevenue + $totalRevenueFromTransactions;

        // إجمالي المصروفات
        $totalExpenses = Expense::where('status', 'approved')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        // إجمالي المصروفات من المعاملات (نوع debit = مدين = مصروف)
        $totalExpensesFromTransactions = Transaction::where('type', 'debit')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $totalExpenses = $totalExpenses + $totalExpensesFromTransactions;

        // الربح الصافي
        $netProfit = $totalRevenue - $totalExpenses;

        // عدد الفواتير
        $totalInvoices = Invoice::whereBetween('created_at', [$startDate, $endDate])->count();
        $paidInvoices = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->count();
        $pendingInvoices = Invoice::where('status', 'pending')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // عدد المدفوعات
        $totalPayments = Payment::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedPayments = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->count();

        // عدد المعاملات
        $totalTransactions = Transaction::whereBetween('created_at', [$startDate, $endDate])->count();

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
        ];
    }

    private function getRevenueReports($startDate, $endDate)
    {
        // الإيرادات من المدفوعات
        $revenueFromPayments = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->select(
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count'),
                'payment_method'
            )
            ->groupBy('payment_method')
            ->get();

        // الإيرادات من المعاملات
        $revenueFromTransactions = Transaction::where('type', 'credit')
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

            $monthRevenue += Transaction::where('type', 'credit')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
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

    private function getDailyData()
    {
        $days = [];
        $revenues = [];
        $expenses = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $dayRevenue = Payment::where('status', 'completed')
                ->whereBetween('paid_at', [$dayStart, $dayEnd])
                ->sum('amount');

            $dayRevenue += Transaction::where('type', 'credit')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->sum('amount');

            $dayExpense = Expense::where('status', 'approved')
                ->whereBetween('expense_date', [$dayStart, $dayEnd])
                ->sum('amount');

            $dayExpense += Transaction::where('type', 'debit')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->sum('amount');

            $days[] = $date->format('Y-m-d');
            $revenues[] = $dayRevenue;
            $expenses[] = $dayExpense;
        }

        return [
            'days' => $days,
            'revenues' => $revenues,
            'expenses' => $expenses,
        ];
    }

    public function export(Request $request)
    {
        // تحديد الفترة الزمنية
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $type = $request->get('type', 'all'); // all, invoices, payments, transactions, expenses

        // حساب التواريخ حسب الفترة
        $dates = $this->calculateDateRange($period, $startDate, $endDate);
        $startDate = $dates['start'];
        $endDate = $dates['end'];

        // إنشاء ملف Excel
        $filename = 'التقارير_المحاسبية_' . $startDate->format('Y-m-d') . '_' . $endDate->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        // إضافة BOM للـ UTF-8 لضمان عرض العربية بشكل صحيح
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        return Response::stream(function() use ($type, $startDate, $endDate, $output) {
            $this->generateExcelContent($type, $startDate, $endDate, $output);
        }, 200, $headers);
    }

    private function generateExcelContent($type, $startDate, $endDate, $output)
    {
        // معلومات التقرير
        fputcsv($output, ['تقارير محاسبية شاملة - Mindlytics'], ';');
        fputcsv($output, ['من تاريخ: ' . $startDate->format('Y-m-d') . ' إلى تاريخ: ' . $endDate->format('Y-m-d')], ';');
        fputcsv($output, ['تاريخ التصدير: ' . now()->format('Y-m-d H:i:s')], ';');
        fputcsv($output, []); // سطر فارغ

        if ($type == 'all' || $type == 'summary') {
            // الملخص المالي
            fputcsv($output, ['=== الملخص المالي ==='], ';');
            $stats = $this->getGeneralStats($startDate, $endDate);
            fputcsv($output, ['إجمالي الإيرادات', number_format($stats['total_revenue'], 2) . ' ج.م'], ';');
            fputcsv($output, ['إجمالي المصروفات', number_format($stats['total_expenses'], 2) . ' ج.م'], ';');
            fputcsv($output, ['الربح الصافي', number_format($stats['net_profit'], 2) . ' ج.م'], ';');
            fputcsv($output, ['نسبة الربحية', $stats['total_revenue'] > 0 ? number_format(($stats['net_profit'] / $stats['total_revenue']) * 100, 2) . '%' : '0%'], ';');
            fputcsv($output, []); // سطر فارغ
        }

        if ($type == 'all' || $type == 'invoices') {
            // تفاصيل الفواتير
            fputcsv($output, ['=== تفاصيل الفواتير ==='], ';');
            fputcsv($output, ['رقم الفاتورة', 'العميل', 'النوع', 'المبلغ الفرعي', 'الضريبة', 'الخصم', 'المبلغ الإجمالي', 'الحالة', 'تاريخ الاستحقاق', 'تاريخ الإنشاء'], ';');
            
            $invoices = Invoice::with('user')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($invoices as $invoice) {
                fputcsv($output, [
                    $invoice->invoice_number,
                    $invoice->user->name ?? 'غير معروف',
                    $invoice->type,
                    number_format($invoice->subtotal, 2),
                    number_format($invoice->tax_amount ?? 0, 2),
                    number_format($invoice->discount_amount ?? 0, 2),
                    number_format($invoice->total_amount, 2),
                    $invoice->status,
                    $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '-',
                    $invoice->created_at->format('Y-m-d H:i:s'),
                ], ';');
            }
            fputcsv($output, []); // سطر فارغ
        }

        if ($type == 'all' || $type == 'payments') {
            // تفاصيل المدفوعات
            fputcsv($output, ['=== تفاصيل المدفوعات ==='], ';');
            fputcsv($output, ['رقم الدفعة', 'العميل', 'رقم الفاتورة', 'المبلغ', 'طريقة الدفع', 'الحالة', 'تاريخ الدفع', 'رقم المرجع', 'تاريخ الإنشاء'], ';');
            
            $payments = Payment::with(['user', 'invoice'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($payments as $payment) {
                fputcsv($output, [
                    $payment->payment_number,
                    $payment->user->name ?? 'غير معروف',
                    $payment->invoice->invoice_number ?? '-',
                    number_format($payment->amount, 2),
                    $payment->payment_method,
                    $payment->status,
                    $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i:s') : '-',
                    $payment->reference_number ?? '-',
                    $payment->created_at->format('Y-m-d H:i:s'),
                ], ';');
            }
            fputcsv($output, []); // سطر فارغ
        }

        if ($type == 'all' || $type == 'transactions') {
            // تفاصيل المعاملات
            fputcsv($output, ['=== تفاصيل المعاملات المالية ==='], ';');
            fputcsv($output, ['رقم المعاملة', 'العميل', 'النوع', 'الفئة', 'المبلغ', 'الحالة', 'الوصف', 'تاريخ الإنشاء'], ';');
            
            $transactions = Transaction::with('user')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($transactions as $transaction) {
                fputcsv($output, [
                    $transaction->transaction_number ?? 'N/A',
                    $transaction->user->name ?? 'غير معروف',
                    $transaction->type == 'credit' ? 'إيراد' : 'مصروف',
                    $transaction->category ?? 'غير محدد',
                    number_format($transaction->amount, 2),
                    $transaction->status,
                    $transaction->description ?? '-',
                    $transaction->created_at->format('Y-m-d H:i:s'),
                ], ';');
            }
            fputcsv($output, []); // سطر فارغ
        }

        if ($type == 'all' || $type == 'expenses') {
            // تفاصيل المصروفات
            fputcsv($output, ['=== تفاصيل المصروفات ==='], ';');
            fputcsv($output, ['رقم المصروف', 'العنوان', 'الفئة', 'المبلغ', 'طريقة الدفع', 'الحالة', 'تاريخ المصروف', 'تاريخ الإنشاء'], ';');
            
            $expenses = Expense::whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($expenses as $expense) {
                fputcsv($output, [
                    $expense->expense_number ?? 'N/A',
                    $expense->title ?? '-',
                    \App\Models\Expense::categoryLabel($expense->category),
                    number_format($expense->amount, 2),
                    $expense->payment_method ?? '-',
                    $expense->status,
                    $expense->expense_date ? $expense->expense_date->format('Y-m-d') : '-',
                    $expense->created_at->format('Y-m-d H:i:s'),
                ], ';');
            }
            fputcsv($output, []); // سطر فارغ
        }

        // تقارير الإيرادات
        if ($type == 'all' || $type == 'revenue') {
            fputcsv($output, ['=== تقرير الإيرادات حسب طريقة الدفع ==='], ';');
            fputcsv($output, ['طريقة الدفع', 'عدد المدفوعات', 'إجمالي المبلغ'], ';');
            
            $revenueReports = $this->getRevenueReports($startDate, $endDate);
            foreach ($revenueReports['from_payments'] as $item) {
                fputcsv($output, [
                    $item->payment_method,
                    $item->count,
                    number_format($item->total, 2) . ' ج.م',
                ], ';');
            }
            fputcsv($output, []); // سطر فارغ
        }

        // تقارير المصروفات
        if ($type == 'all' || $type == 'expense') {
            fputcsv($output, ['=== تقرير المصروفات حسب الفئة ==='], ';');
            fputcsv($output, ['الفئة', 'عدد المصروفات', 'إجمالي المبلغ'], ';');
            
            $expenseReports = $this->getExpenseReports($startDate, $endDate);
            foreach ($expenseReports['from_expenses'] as $item) {
                fputcsv($output, [
                    \App\Models\Expense::categoryLabel($item->category),
                    $item->count,
                    number_format($item->total, 2) . ' ج.م',
                ], ';');
            }
            fputcsv($output, []); // سطر فارغ
        }

        fclose($output);
    }
}

