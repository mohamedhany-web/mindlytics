<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\OfflineLocation;
use App\Models\PlaceInvoice;
use App\Models\PlaceMonthlySettlement;
use App\Models\PlaceDailyExpense;
use App\Models\PlaceUsageLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlaceSettlementService
{
    public function getOrCreateOpenSettlement(OfflineLocation $location, ?string $periodMonth = null): PlaceMonthlySettlement
    {
        $period = $periodMonth ?? now()->format('Y-m');

        $existing = PlaceMonthlySettlement::query()
            ->where('offline_location_id', $location->id)
            ->where('period_month', $period)
            ->first();

        if ($existing) {
            if ($existing->isLocked()) {
                throw ValidationException::withMessages([
                    'period' => 'شهر ' . $period . ' مقفل بالفعل. ابدأ شهراً جديداً.',
                ]);
            }

            return $existing;
        }

        return PlaceMonthlySettlement::create([
            'settlement_number' => PlaceMonthlySettlement::generateNumber(),
            'offline_location_id' => $location->id,
            'period_month' => $period,
            'hourly_rate' => $location->hourly_rate ?? 0,
            'currency' => 'EGP',
            'status' => PlaceMonthlySettlement::STATUS_OPEN,
            'wallet_id' => $location->default_wallet_id,
        ]);
    }

    public function recalculateSettlement(PlaceMonthlySettlement $settlement): PlaceMonthlySettlement
    {
        if ($settlement->isLocked()) {
            return $settlement;
        }

        $year = (int) substr($settlement->period_month, 0, 4);
        $month = (int) substr($settlement->period_month, 5, 2);

        $approvedHours = PlaceUsageLog::query()
            ->where('offline_location_id', $settlement->offline_location_id)
            ->where('status', PlaceUsageLog::STATUS_APPROVED)
            ->whereYear('usage_date', $year)
            ->whereMonth('usage_date', $month)
            ->sum('hours');

        $approvedExpenses = PlaceDailyExpense::query()
            ->where('offline_location_id', $settlement->offline_location_id)
            ->where('status', PlaceDailyExpense::STATUS_APPROVED)
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month)
            ->get(['amount', 'quantity']);

        $totalExpenses = round($approvedExpenses->sum(fn (PlaceDailyExpense $e) => $e->lineTotal()), 2);

        $rate = $settlement->hourly_rate ?: ($settlement->location?->hourly_rate ?? 0);
        $hoursAmount = round((float) $approvedHours * (float) $rate, 2);
        $total = round($hoursAmount + $totalExpenses, 2);

        $settlement->update([
            'total_hours' => $approvedHours,
            'hourly_rate' => $rate,
            'total_expenses' => $totalExpenses,
            'total_amount' => $total,
        ]);

        return $settlement->fresh();
    }

    public function submitForReview(PlaceMonthlySettlement $settlement, User $user): PlaceMonthlySettlement
    {
        if ($settlement->isLocked()) {
            throw ValidationException::withMessages(['settlement' => 'المخالصة مقفلة.']);
        }

        $settlement = $this->recalculateSettlement($settlement);

        if ((float) $settlement->total_hours <= 0 && (float) $settlement->total_expenses <= 0) {
            throw ValidationException::withMessages(['hours' => 'لا توجد ساعات أو مصاريف معتمدة لهذا الشهر.']);
        }

        $settlement->update([
            'status' => PlaceMonthlySettlement::STATUS_SUBMITTED,
            'submitted_by' => $user->id,
            'submitted_at' => now(),
        ]);

        return $settlement->fresh();
    }

    public function approveSettlement(PlaceMonthlySettlement $settlement, User $admin, ?int $walletId = null): PlaceMonthlySettlement
    {
        if ($settlement->status !== PlaceMonthlySettlement::STATUS_SUBMITTED) {
            throw ValidationException::withMessages(['settlement' => 'يجب أن تكون المخالصة مُرسلة للمراجعة أولاً.']);
        }

        $settlement = $this->recalculateSettlement($settlement);
        $walletId = $walletId ?: $settlement->wallet_id ?: $settlement->location?->default_wallet_id;

        return DB::transaction(function () use ($settlement, $admin, $walletId) {
            $settlement->update([
                'status' => PlaceMonthlySettlement::STATUS_APPROVED,
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'wallet_id' => $walletId,
            ]);

            $expense = $this->createExpenseFromSettlement($settlement->fresh(), $admin);
            $invoice = $this->createInvoiceFromSettlement($settlement->fresh());

            $settlement->update([
                'expense_id' => $expense->id,
                'place_invoice_id' => $invoice->id,
            ]);

            return $settlement->fresh(['expense', 'invoice', 'location']);
        });
    }

    public function closeMonth(PlaceMonthlySettlement $settlement, User $admin): PlaceMonthlySettlement
    {
        if (! in_array($settlement->status, [PlaceMonthlySettlement::STATUS_APPROVED, PlaceMonthlySettlement::STATUS_PAID], true)) {
            throw ValidationException::withMessages(['settlement' => 'يجب الموافقة على المخالصة قبل إقفال الشهر.']);
        }

        $settlement->update([
            'status' => PlaceMonthlySettlement::STATUS_CLOSED,
            'closed_by' => $admin->id,
            'closed_at' => now(),
        ]);

        return $settlement->fresh();
    }

    public function markPaidAfterExpenseApproval(PlaceMonthlySettlement $settlement): void
    {
        $settlement->update(['status' => PlaceMonthlySettlement::STATUS_PAID]);

        if ($settlement->invoice) {
            $settlement->invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        }
    }

    protected function createExpenseFromSettlement(PlaceMonthlySettlement $settlement, User $admin): Expense
    {
        $location = $settlement->location;
        $periodLabel = Carbon::createFromFormat('Y-m', $settlement->period_month)->locale('ar')->translatedFormat('F Y');

        $expenseNumber = 'EXP-' . str_pad((string) (Expense::count() + 1), 8, '0', STR_PAD_LEFT);

        return Expense::create([
            'expense_number' => $expenseNumber,
            'title' => 'إيجار مكان: ' . ($location?->name ?? '') . ' — ' . $periodLabel,
            'description' => 'مخالصة شهرية — ' . $settlement->total_hours . ' ساعة × ' . number_format((float) $settlement->hourly_rate, 2) . ' ج.م'
                . ((float) $settlement->total_expenses > 0 ? ' + مصاريف يومية ' . number_format((float) $settlement->total_expenses, 2) . ' ج.م' : ''),
            'category' => 'operational',
            'amount' => $settlement->total_amount,
            'currency' => $settlement->currency,
            'expense_date' => now()->toDateString(),
            'payment_method' => 'wallet',
            'funding_source' => 'revenue',
            'wallet_id' => $settlement->wallet_id,
            'offline_location_id' => $settlement->offline_location_id,
            'place_monthly_settlement_id' => $settlement->id,
            'status' => 'pending',
            'created_by' => $admin->id,
            'metadata' => [
                'source' => 'place_monthly_settlement',
                'settlement_id' => $settlement->id,
                'settlement_number' => $settlement->settlement_number,
                'period_month' => $settlement->period_month,
            ],
        ]);
    }

    protected function createInvoiceFromSettlement(PlaceMonthlySettlement $settlement): PlaceInvoice
    {
        $location = $settlement->location;

        return PlaceInvoice::create([
            'invoice_number' => PlaceInvoice::generateNumber(),
            'offline_location_id' => $settlement->offline_location_id,
            'place_monthly_settlement_id' => $settlement->id,
            'amount' => $settlement->total_amount,
            'currency' => $settlement->currency,
            'period_month' => $settlement->period_month,
            'status' => 'issued',
            'issued_at' => now(),
            'line_items' => array_values(array_filter([
                (float) $settlement->total_hours > 0 ? [
                    'description' => 'إيجار ' . ($location?->name ?? 'المكان') . ' — ' . $settlement->period_month,
                    'hours' => (float) $settlement->total_hours,
                    'rate' => (float) $settlement->hourly_rate,
                    'amount' => round((float) $settlement->total_hours * (float) $settlement->hourly_rate, 2),
                ] : null,
                (float) $settlement->total_expenses > 0 ? [
                    'description' => 'مصاريف يومية (أكل، مشروبات، إلخ) — ' . $settlement->period_month,
                    'amount' => (float) $settlement->total_expenses,
                ] : null,
            ])),
            'notes' => 'فاتورة صادرة للمكان الإداري بعد اعتماد المخالصة الشهرية.',
        ]);
    }
}
