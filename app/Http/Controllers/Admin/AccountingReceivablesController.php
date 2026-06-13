<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingDebt;
use App\Models\AccountingDebtRepayment;
use App\Models\Expense;
use App\Models\InstallmentPayment;
use App\Models\Invoice;
use App\Models\OfflineCourseEnrollment;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\WithdrawalRequest;
use App\Support\AccountingAnalytics;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AccountingReceivablesController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->get('period', 'month');
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        if ($period === 'all') {
            $start = Carbon::parse('2020-01-01');
            $end = Carbon::now();
        } elseif ($period === 'year') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();
        }

        $snapshot = AccountingAnalytics::receivablesSnapshot();
        $breakEven = AccountingAnalytics::breakEvenAnalysis($start, $end);
        $breakEvenAll = AccountingAnalytics::breakEvenAnalysis($start, $end, true);

        $manualDebtsPayable = AccountingDebt::with(['wallet', 'creator'])
            ->where('direction', AccountingDebt::DIRECTION_PAYABLE)
            ->whereIn('status', ['active', 'partial'])
            ->orderByDesc('debt_date')
            ->get();

        $manualDebtsReceivable = AccountingDebt::with(['wallet', 'creator'])
            ->where('direction', AccountingDebt::DIRECTION_RECEIVABLE)
            ->whereIn('status', ['active', 'partial'])
            ->orderByDesc('debt_date')
            ->get();

        $debtStats = [
            'payable_count' => $manualDebtsPayable->count(),
            'payable_remaining' => (float) $manualDebtsPayable->sum('remaining_amount'),
            'receivable_count' => $manualDebtsReceivable->count(),
            'receivable_remaining' => (float) $manualDebtsReceivable->sum('remaining_amount'),
        ];

        $wallets = Wallet::academyWallets()->where('is_active', true)->orderBy('name')->get();

        $pendingInvoices = Invoice::with('user')
            ->whereIn('status', ['pending', 'overdue'])
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        $offlineDebts = OfflineCourseEnrollment::with(['student', 'course'])
            ->where('remaining_amount', '>', 0)
            ->orderByDesc('remaining_amount')
            ->limit(15)
            ->get();

        $pocketExpensesTotal = (float) Expense::approved()->outOfPocket()->sum('amount');
        $pocketExpensesMonth = (float) Expense::approved()->outOfPocket()
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');

        return view('admin.accounting.receivables', compact(
            'snapshot',
            'breakEven',
            'breakEvenAll',
            'manualDebtsPayable',
            'manualDebtsReceivable',
            'debtStats',
            'wallets',
            'pendingInvoices',
            'offlineDebts',
            'pocketExpensesTotal',
            'pocketExpensesMonth',
            'period',
            'start',
            'end'
        ));
    }

    public function storeDebt(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'direction' => 'required|in:payable,receivable',
            'party_name' => 'required|string|max:255',
            'party_phone' => 'nullable|string|max:50',
            'party_relation' => 'nullable|string|max:100',
            'title' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'debt_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:debt_date',
            'wallet_id' => 'nullable|exists:wallets,id',
            'deposit_to_wallet' => 'nullable|boolean',
            'notes' => 'nullable|string|max:2000',
        ], [
            'party_name.required' => 'اسم الطرف (من جايب الفلوس) مطلوب',
            'amount.required' => 'مبلغ الدين مطلوب',
        ]);

        $amount = round((float) $validated['amount'], 2);
        $walletId = isset($validated['wallet_id']) ? (int) $validated['wallet_id'] : null;
        if ($walletId <= 0) {
            $walletId = null;
        }

        $depositToWallet = $request->boolean('deposit_to_wallet')
            && $validated['direction'] === AccountingDebt::DIRECTION_PAYABLE
            && $walletId !== null;

        DB::beginTransaction();
        try {
            $debtNumber = 'DEB-'.str_pad((string) (AccountingDebt::count() + 1), 6, '0', STR_PAD_LEFT);

            $debt = AccountingDebt::create([
                'debt_number' => $debtNumber,
                'direction' => $validated['direction'],
                'party_name' => $validated['party_name'],
                'party_phone' => $validated['party_phone'] ?? null,
                'party_relation' => $validated['party_relation'] ?? null,
                'title' => $validated['title'] ?? null,
                'amount' => $amount,
                'paid_amount' => 0,
                'remaining_amount' => $amount,
                'wallet_id' => $walletId,
                'deposited_to_wallet' => false,
                'debt_date' => $validated['debt_date'],
                'due_date' => $validated['due_date'] ?? null,
                'status' => 'active',
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            if ($depositToWallet) {
                $wallet = Wallet::academyWallets()->where('is_active', true)->whereKey($walletId)->firstOrFail();
                $depositNote = 'إيداع دين مستلف من: '.$validated['party_name'].' — '.$debtNumber;
                if (! empty($validated['notes'])) {
                    $depositNote .= ' — '.$validated['notes'];
                }
                $wallet->deposit($amount, null, null, $depositNote);
                $debt->update(['deposited_to_wallet' => true]);
            }

            DB::commit();

            return back()->with('success', 'تم تسجيل الدين بنجاح'.($depositToWallet ? ' وإيداع المبلغ في المحفظة.' : '.'));
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->withErrors(['error' => 'تعذر تسجيل الدين: '.$e->getMessage()])->withInput();
        }
    }

    public function addRepayment(Request $request, AccountingDebt $accountingDebt): RedirectResponse
    {
        $debt = $accountingDebt;
        if ($debt->status === 'cancelled' || $debt->status === 'settled') {
            return back()->withErrors(['error' => 'لا يمكن تسجيل سداد على دين مغلق.']);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'paid_at' => 'required|date',
            'wallet_id' => 'nullable|exists:wallets,id',
            'withdraw_from_wallet' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $payAmount = min(round((float) $validated['amount'], 2), (float) $debt->remaining_amount);
        if ($payAmount <= 0) {
            return back()->withErrors(['error' => 'لا يوجد مبلغ متبقي للسداد.']);
        }

        $walletId = isset($validated['wallet_id']) ? (int) $validated['wallet_id'] : null;
        if ($walletId <= 0) {
            $walletId = null;
        }

        $withdrawFromWallet = $request->boolean('withdraw_from_wallet')
            && $debt->isPayable()
            && $walletId !== null;

        DB::beginTransaction();
        try {
            if ($withdrawFromWallet) {
                $wallet = Wallet::academyWallets()->where('is_active', true)->whereKey($walletId)->firstOrFail();
                if ((float) $wallet->balance < $payAmount) {
                    throw new \RuntimeException('رصيد المحفظة غير كافٍ للسداد.');
                }
                $wallet->withdraw($payAmount, 'سداد دين لـ '.$debt->party_name.' — '.$debt->debt_number);
            }

            AccountingDebtRepayment::create([
                'accounting_debt_id' => $debt->id,
                'amount' => $payAmount,
                'paid_at' => $validated['paid_at'],
                'wallet_id' => $walletId,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $debt->paid_amount = round((float) $debt->paid_amount + $payAmount, 2);
            $debt->recalculateStatus();

            DB::commit();

            return back()->with('success', 'تم تسجيل سداد بمبلغ '.number_format($payAmount, 2).' ج.م');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->withErrors(['error' => 'تعذر تسجيل السداد: '.$e->getMessage()]);
        }
    }

    public function cancelDebt(AccountingDebt $accountingDebt): RedirectResponse
    {
        $debt = $accountingDebt;
        if ($debt->deposited_to_wallet && (float) $debt->paid_amount <= 0) {
            return back()->withErrors(['error' => 'لا يمكن إلغاء دين تم إيداع مبلغه في محفظة دون معالجة يدوية للرصيد.']);
        }

        $debt->update(['status' => 'cancelled', 'remaining_amount' => 0]);

        return back()->with('success', 'تم إلغاء الدين.');
    }
}
