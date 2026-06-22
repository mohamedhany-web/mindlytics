<?php

namespace App\Services;

use App\Models\EmployeeAgreement;
use App\Models\EmployeeSalaryAddition;
use App\Models\EmployeeSalaryDeduction;
use App\Models\EmployeeSalaryPayment;
use App\Models\Expense;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Support\AccountingAnalytics;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EmployeePayrollService
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function previewPeriod(int $month, int $year): Collection
    {
        $from = Carbon::create($year, $month, 1)->startOfDay();
        $to = Carbon::create($year, $month, 1)->endOfMonth()->startOfDay();
        if ($to->gt(now())) {
            $to = now()->copy()->startOfDay();
        }

        if ($from->lte($to)) {
            app(SalesDailyReportService::class)->applyDuePenaltiesInRange($from, $to);
            app(EmployeeDailyReportService::class)->applyDuePenaltiesInRange($from, $to);
        }

        return $this->activeEmployeesWithAgreement()
            ->map(fn (User $employee) => $this->buildPayrollRow($employee, $month, $year));
    }

    /**
     * @return array{created: int, skipped: int}
     */
    public function generateForPeriod(int $month, int $year, ?int $createdBy = null): array
    {
        $created = 0;
        $skipped = 0;

        foreach ($this->previewPeriod($month, $year) as $row) {
            if ($row['existing_payment_id']) {
                $skipped++;

                continue;
            }

            if ($row['base_salary'] <= 0) {
                $skipped++;

                continue;
            }

            EmployeeSalaryPayment::create([
                'employee_id' => $row['employee_id'],
                'agreement_id' => $row['agreement_id'],
                'payment_number' => EmployeeSalaryPayment::generatePaymentNumber(),
                'base_salary' => $row['base_salary'],
                'total_deductions' => $row['total_deductions'],
                'total_additions' => $row['total_additions'],
                'net_salary' => $row['net_salary'],
                'period_month' => $month,
                'period_year' => $year,
                'payment_date' => Carbon::create($year, $month)->endOfMonth(),
                'status' => 'pending',
                'notes' => 'مسير رواتب '.$month.'/'.$year,
                'created_by' => $createdBy,
            ]);
            $created++;
        }

        return compact('created', 'skipped');
    }

    public function payPayment(
        EmployeeSalaryPayment $payment,
        int $walletId,
        ?UploadedFile $receipt = null,
        ?string $notes = null,
        ?int $paidBy = null
    ): Expense {
        if (! in_array($payment->status, ['pending', 'overdue'], true)) {
            throw new \RuntimeException('هذه الدفعة ليست قابلة للدفع.');
        }

        return DB::transaction(function () use ($payment, $walletId, $receipt, $notes, $paidBy) {
            $wallet = Wallet::query()->lockForUpdate()->findOrFail($walletId);

            if ($wallet->balance < $payment->net_salary) {
                throw new \RuntimeException('رصيد المحفظة غير كافٍ. الرصيد: '.number_format((float) $wallet->balance, 2).' ج.م');
            }

            $employee = $payment->employee;
            $periodLabel = $payment->period_month && $payment->period_year
                ? sprintf('%02d/%d', $payment->period_month, $payment->period_year)
                : $payment->payment_date?->format('Y-m');

            $receiptPath = null;
            if ($receipt) {
                $receiptPath = $receipt->store('receipts/employee-salary-payments', 'public');
            }

            $expenseNumber = 'EXP-'.str_pad((string) (Expense::count() + 1), 8, '0', STR_PAD_LEFT);
            $paymentMethod = match ($wallet->type) {
                'vodafone_cash', 'instapay' => 'wallet',
                'bank_transfer' => 'bank_transfer',
                default => 'wallet',
            };

            $expense = Expense::create([
                'expense_number' => $expenseNumber,
                'title' => 'راتب موظف — '.($employee?->name ?? '#'.$payment->employee_id),
                'description' => 'دفعة راتب '.$payment->payment_number.' — فترة '.$periodLabel
                    .' | أساسي: '.number_format((float) $payment->base_salary, 2)
                    .' | خصومات: '.number_format((float) $payment->total_deductions, 2)
                    .' | إضافات: '.number_format((float) ($payment->total_additions ?? 0), 2)
                    .' | صافي: '.number_format((float) $payment->net_salary, 2),
                'category' => 'salaries',
                'amount' => $payment->net_salary,
                'currency' => 'EGP',
                'expense_date' => now()->toDateString(),
                'payment_method' => $paymentMethod,
                'funding_source' => AccountingAnalytics::inferFundingSource($wallet->id, $paymentMethod),
                'wallet_id' => $wallet->id,
                'reference_number' => $payment->payment_number,
                'attachment' => $receiptPath,
                'status' => 'approved',
                'approved_by' => $paidBy,
                'approved_at' => now(),
                'notes' => $notes,
                'metadata' => [
                    'kind' => 'employee_salary_payment',
                    'employee_salary_payment_id' => $payment->id,
                    'employee_id' => $payment->employee_id,
                    'period_month' => $payment->period_month,
                    'period_year' => $payment->period_year,
                ],
                'created_by' => $paidBy,
            ]);

            $wallet->withdraw(
                (float) $payment->net_salary,
                'راتب موظف — '.$payment->payment_number.' — '.$expense->expense_number
            );

            $transaction = Transaction::create([
                'transaction_number' => 'TXN-'.str_pad((string) (Transaction::count() + 1), 8, '0', STR_PAD_LEFT),
                'user_id' => $paidBy,
                'expense_id' => $expense->id,
                'type' => 'debit',
                'category' => 'expense',
                'amount' => $payment->net_salary,
                'currency' => 'EGP',
                'description' => 'مصروف راتب: '.$expense->title.' — '.$expense->expense_number,
                'status' => 'completed',
                'metadata' => [
                    'expense_id' => $expense->id,
                    'employee_salary_payment_id' => $payment->id,
                    'wallet_id' => $wallet->id,
                ],
                'created_by' => $paidBy,
            ]);

            $expense->update(['transaction_id' => $transaction->id]);

            $walletTransaction = WalletTransaction::query()
                ->where('wallet_id', $wallet->id)
                ->where('type', 'withdrawal')
                ->where('amount', $payment->net_salary)
                ->latest()
                ->first();

            if ($walletTransaction) {
                $walletTransaction->update(['transaction_id' => $transaction->id]);
            }

            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'wallet_id' => $wallet->id,
                'expense_id' => $expense->id,
                'transfer_receipt_path' => $receiptPath ?? $payment->transfer_receipt_path,
                'notes' => $notes
                    ? trim(($payment->notes ?? '')."\n".'[دفع] '.$notes)
                    : $payment->notes,
            ]);

            return $expense;
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function buildPayrollRow(User $employee, int $month, int $year): array
    {
        $agreement = EmployeeAgreement::query()
            ->where('employee_id', $employee->id)
            ->where('status', 'active')
            ->orderByDesc('start_date')
            ->first();

        $baseSalary = (float) ($agreement?->salary ?? $employee->salary ?? 0);

        $totalDeductions = (float) EmployeeSalaryDeduction::query()
            ->where('employee_id', $employee->id)
            ->where('status', 'applied')
            ->whereYear('deduction_date', $year)
            ->whereMonth('deduction_date', $month)
            ->sum('amount');

        $totalAdditions = (float) EmployeeSalaryAddition::query()
            ->where('employee_id', $employee->id)
            ->where('status', 'applied')
            ->whereYear('addition_date', $year)
            ->whereMonth('addition_date', $month)
            ->sum('amount');

        $netSalary = max(0, $baseSalary - $totalDeductions + $totalAdditions);

        $existing = EmployeeSalaryPayment::query()
            ->where('employee_id', $employee->id)
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->whereNotIn('status', ['cancelled'])
            ->first();

        return [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'employee_code' => $employee->employee_code,
            'job_name' => $employee->employeeJob?->name,
            'agreement_id' => $agreement?->id,
            'base_salary' => $baseSalary,
            'total_deductions' => $totalDeductions,
            'total_additions' => $totalAdditions,
            'net_salary' => $netSalary,
            'existing_payment_id' => $existing?->id,
            'existing_payment_status' => $existing?->status,
            'existing_payment' => $existing,
        ];
    }

    /**
     * @return Collection<int, User>
     */
    private function activeEmployeesWithAgreement(): Collection
    {
        return User::query()
            ->employees()
            ->where('is_active', true)
            ->with('employeeJob')
            ->where(function ($q) {
                $q->whereHas('employeeAgreements', fn ($a) => $a->where('status', 'active'))
                    ->orWhere('salary', '>', 0);
            })
            ->orderBy('name')
            ->get();
    }
}
