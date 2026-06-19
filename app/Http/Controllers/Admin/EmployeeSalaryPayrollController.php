<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeSalaryPayment;
use App\Models\Wallet;
use App\Services\EmployeePayrollService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeSalaryPayrollController extends Controller
{
    public function __construct(
        protected EmployeePayrollService $payroll
    ) {}

    public function index(Request $request)
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $month = max(1, min(12, $month));
        $year = max(2020, min(2100, $year));

        $rows = $this->payroll->previewPeriod($month, $year);

        $payments = EmployeeSalaryPayment::query()
            ->with(['employee.employeeJob', 'expense', 'wallet'])
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->orderBy('id')
            ->get()
            ->keyBy('employee_id');

        $stats = [
            'employees' => $rows->count(),
            'generated' => $payments->count(),
            'pending' => $payments->whereIn('status', ['pending', 'overdue'])->count(),
            'paid' => $payments->where('status', 'paid')->count(),
            'total_net_pending' => $payments->whereIn('status', ['pending', 'overdue'])->sum('net_salary'),
            'total_net_paid' => $payments->where('status', 'paid')->sum('net_salary'),
            'total_net_preview' => $rows->sum('net_salary'),
        ];

        $wallets = Wallet::academyWallets()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.employee-salaries.index', compact('rows', 'payments', 'stats', 'month', 'year', 'wallets'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
        ]);

        $result = $this->payroll->generateForPeriod(
            (int) $validated['month'],
            (int) $validated['year'],
            auth()->id()
        );

        return redirect()
            ->route('admin.employee-salaries.index', ['month' => $validated['month'], 'year' => $validated['year']])
            ->with('success', 'تم إنشاء '.$result['created'].' دفعة/دفعات. تم تخطي '.$result['skipped'].'.');
    }

    public function export(Request $request): StreamedResponse
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $rows = $this->payroll->previewPeriod($month, $year);
        $filename = 'employee-payroll-'.$year.'-'.str_pad((string) $month, 2, '0', STR_PAD_LEFT).'.csv';

        return response()->streamDownload(function () use ($rows, $month, $year) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['الموظف', 'الكود', 'التوصيف', 'الأساسي', 'الخصومات', 'الإضافات (أوفر تايم)', 'الصافي', 'حالة الدفعة', 'الشهر', 'السنة']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row['employee_name'],
                    $row['employee_code'] ?? '',
                    $row['job_name'] ?? '',
                    number_format((float) $row['base_salary'], 2, '.', ''),
                    number_format((float) $row['total_deductions'], 2, '.', ''),
                    number_format((float) $row['total_additions'], 2, '.', ''),
                    number_format((float) $row['net_salary'], 2, '.', ''),
                    $row['existing_payment_status'] ?? 'غير منشأ',
                    $month,
                    $year,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function pay(EmployeeSalaryPayment $payment)
    {
        abort_unless(in_array($payment->status, ['pending', 'overdue'], true), 404);

        $payment->load(['employee.employeeJob', 'agreement']);

        $wallets = Wallet::academyWallets()
            ->where('is_active', true)
            ->whereNotNull('type')
            ->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer'])
            ->orderBy('name')
            ->get();

        return view('admin.employee-salaries.pay', compact('payment', 'wallets'));
    }

    public function markPaid(Request $request, EmployeeSalaryPayment $payment)
    {
        $validated = $request->validate([
            'wallet_id' => ['required', 'exists:wallets,id'],
            'transfer_receipt' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $expense = $this->payroll->payPayment(
                $payment,
                (int) $validated['wallet_id'],
                $request->file('transfer_receipt'),
                $validated['notes'] ?? null,
                auth()->id()
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['wallet_id' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('admin.employee-salaries.index', [
                'month' => $payment->period_month ?? now()->month,
                'year' => $payment->period_year ?? now()->year,
            ])
            ->with('success', 'تم الدفع وتسجيل المصروف '.$expense->expense_number.' وخصم المبلغ من المحفظة.');
    }

    public function payBatch(Request $request)
    {
        $validated = $request->validate([
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'wallet_id' => ['required', 'exists:wallets,id'],
            'payment_ids' => ['required', 'array', 'min:1'],
            'payment_ids.*' => ['integer', 'exists:employee_salary_payments,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $payments = EmployeeSalaryPayment::query()
            ->whereIn('id', $validated['payment_ids'])
            ->where('period_month', $validated['month'])
            ->where('period_year', $validated['year'])
            ->whereIn('status', ['pending', 'overdue'])
            ->get();

        if ($payments->isEmpty()) {
            return back()->with('error', 'لا توجد دفعات قابلة للدفع.');
        }

        $paid = 0;
        $errors = [];

        foreach ($payments as $payment) {
            try {
                $this->payroll->payPayment(
                    $payment,
                    (int) $validated['wallet_id'],
                    null,
                    $validated['notes'] ?? null,
                    auth()->id()
                );
                $paid++;
            } catch (\RuntimeException $e) {
                $errors[] = ($payment->employee?->name ?? '#'.$payment->employee_id).': '.$e->getMessage();
                break;
            }
        }

        $redirect = redirect()->route('admin.employee-salaries.index', [
            'month' => $validated['month'],
            'year' => $validated['year'],
        ]);

        if ($paid > 0) {
            $redirect = $redirect->with('success', 'تم دفع '.$paid.' راتب/رواتب وتسجيلها في المصروفات.');
        }

        if ($errors !== []) {
            $redirect = $redirect->with('error', implode(' | ', $errors));
        }

        return $redirect;
    }
}
