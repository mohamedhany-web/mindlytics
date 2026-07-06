<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\OfflineCourseEnrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionRefundService
{
    public function canRefund(Transaction $transaction): bool
    {
        if ($transaction->type !== 'credit') {
            return false;
        }

        if ($transaction->status === 'reversed') {
            return false;
        }

        if ($transaction->category === 'refund') {
            return false;
        }

        $metadata = is_array($transaction->metadata) ? $transaction->metadata : [];

        return empty($metadata['refunded_at']);
    }

    public function needsWalletWithdrawalSync(Transaction $transaction): bool
    {
        if (! $this->isCreditTransaction($transaction) || $transaction->category === 'refund') {
            return false;
        }

        $metadata = is_array($transaction->metadata) ? $transaction->metadata : [];
        if ($transaction->status !== 'reversed' && empty($metadata['refunded_at'])) {
            return false;
        }

        return ! $this->walletRefundCompleted($transaction);
    }

    public function walletRefundCompleted(Transaction $transaction): bool
    {
        $metadata = is_array($transaction->metadata) ? $transaction->metadata : [];

        if (! empty($metadata['wallet_refund_transaction_id'])) {
            $exists = WalletTransaction::query()
                ->whereKey($metadata['wallet_refund_transaction_id'])
                ->where('type', 'refund')
                ->exists();
            if ($exists) {
                return true;
            }
        }

        $amount = round((float) ($metadata['refund_amount'] ?? $transaction->amount), 2);

        return $this->hasExistingWalletRefund($transaction, $amount);
    }

    public function suggestedWalletId(Transaction $transaction): ?int
    {
        $deposit = $this->findLinkedWalletDeposit($transaction);

        return $this->resolveWalletId($transaction, $deposit);
    }

    public function syncWalletWithdrawalIfMissing(
        Transaction $transaction,
        ?float $amount = null,
        ?int $processedBy = null,
        ?int $walletIdOverride = null
    ): ?WalletTransaction {
        if (! $this->needsWalletWithdrawalSync($transaction)) {
            throw ValidationException::withMessages([
                'refund' => 'لا حاجة لمزامنة سحب المحفظة لهذه المعاملة.',
            ]);
        }

        $metadata = is_array($transaction->metadata) ? $transaction->metadata : [];
        $amount = round((float) ($amount ?? $metadata['refund_amount'] ?? $transaction->amount), 2);

        return DB::transaction(function () use ($transaction, $amount, $processedBy, $metadata, $walletIdOverride) {
            $transaction = Transaction::query()->lockForUpdate()->findOrFail($transaction->id);
            $processedBy = $processedBy ?? auth()->id();
            $notes = 'مزامنة سحب استرداد — معاملة: ' . ($transaction->transaction_number ?? ('#' . $transaction->id));

            $walletWithdrawal = $this->withdrawFromLinkedWallet(
                $transaction,
                $amount,
                $notes,
                $processedBy,
                $walletIdOverride
            );
            if (! $walletWithdrawal) {
                throw ValidationException::withMessages([
                    'refund' => 'لم يُعثر على محفظة مرتبطة لهذه المعاملة.',
                ]);
            }

            $transaction->update([
                'metadata' => array_merge($metadata, [
                    'wallet_refund_transaction_id' => $walletWithdrawal->id,
                    'wallet_withdrawal_synced_at' => now()->toIso8601String(),
                ]),
            ]);

            return $walletWithdrawal;
        });
    }

    /**
     * @return array{original: Transaction, refund: Transaction}
     */
    public function process(Transaction $transaction, ?float $amount = null, ?string $notes = null, ?int $processedBy = null): array
    {
        if (! $this->canRefund($transaction)) {
            throw ValidationException::withMessages([
                'refund' => 'لا يمكن استرداد هذه المعاملة (قد تكون مستردة مسبقاً أو ليست إيراداً).',
            ]);
        }

        $amount = round((float) ($amount ?? $transaction->amount), 2);
        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'مبلغ الاسترداد يجب أن يكون أكبر من صفر.',
            ]);
        }

        if ($amount > (float) $transaction->amount) {
            throw ValidationException::withMessages([
                'amount' => 'مبلغ الاسترداد لا يمكن أن يتجاوز مبلغ المعاملة الأصلية.',
            ]);
        }

        return DB::transaction(function () use ($transaction, $amount, $notes, $processedBy) {
            $transaction = Transaction::query()->lockForUpdate()->findOrFail($transaction->id);

            if (! $this->canRefund($transaction)) {
                throw ValidationException::withMessages([
                    'refund' => 'تم استرداد هذه المعاملة مسبقاً.',
                ]);
            }

            $processedBy = $processedBy ?? auth()->id();
            $refundNotes = trim((string) ($notes ?? ''));
            if ($refundNotes === '') {
                $refundNotes = 'استرداد معاملة: ' . ($transaction->transaction_number ?? ('#' . $transaction->id));
            }

            $walletWithdrawal = $this->withdrawFromLinkedWallet($transaction, $amount, $refundNotes, $processedBy);

            $originalMetadata = is_array($transaction->metadata) ? $transaction->metadata : [];
            $transaction->update([
                'status' => 'reversed',
                'metadata' => array_merge($originalMetadata, [
                    'refunded_at' => now()->toIso8601String(),
                    'refunded_by' => $processedBy,
                    'refund_amount' => $amount,
                    'wallet_refund_transaction_id' => $walletWithdrawal?->id,
                ]),
            ]);

            $refundTransaction = Transaction::create([
                'transaction_number' => $this->nextTransactionNumber(),
                'user_id' => $transaction->user_id,
                'payment_id' => $transaction->payment_id,
                'invoice_id' => $transaction->invoice_id,
                'expense_id' => $transaction->expense_id,
                'subscription_id' => $transaction->subscription_id,
                'type' => 'debit',
                'category' => 'refund',
                'amount' => $amount,
                'currency' => $transaction->currency ?? 'EGP',
                'description' => $refundNotes,
                'status' => 'completed',
                'metadata' => array_merge($originalMetadata, [
                    'original_transaction_id' => $transaction->id,
                    'refunded_at' => now()->toIso8601String(),
                    'refunded_by' => $processedBy,
                    'wallet_refund_transaction_id' => $walletWithdrawal?->id,
                ]),
                'created_by' => $processedBy,
            ]);

            if ($walletWithdrawal) {
                $walletWithdrawal->update(['transaction_id' => $refundTransaction->id]);
            }

            $this->syncPaymentAndInvoice($transaction, $amount);
            $this->syncEnrollment($transaction, $amount);

            $transaction->update([
                'metadata' => array_merge($transaction->fresh()->metadata ?? [], [
                    'refund_transaction_id' => $refundTransaction->id,
                ]),
            ]);

            return [
                'original' => $transaction->fresh(),
                'refund' => $refundTransaction,
            ];
        });
    }

    private function withdrawFromLinkedWallet(
        Transaction $transaction,
        float $amount,
        string $notes,
        ?int $processedBy,
        ?int $walletIdOverride = null
    ): ?WalletTransaction {
        if ($this->hasExistingWalletRefund($transaction, $amount)) {
            throw ValidationException::withMessages([
                'refund' => 'تم سحب مبلغ الاسترداد من المحفظة مسبقاً لهذه المعاملة.',
            ]);
        }

        $deposit = $this->findLinkedWalletDeposit($transaction);
        $walletId = $walletIdOverride ?: $this->resolveWalletId($transaction, $deposit);
        $hadDeposit = $deposit !== null;

        if (! $walletId) {
            if ($hadDeposit) {
                throw ValidationException::withMessages([
                    'refund' => 'تعذر تحديد المحفظة المرتبطة بإيداع هذه المعاملة. راجع سجل محفظة الإيداع يدوياً.',
                ]);
            }

            return null;
        }

        /** @var Wallet|null $wallet */
        $wallet = Wallet::query()->lockForUpdate()->find($walletId);
        if (! $wallet) {
            if ($hadDeposit || $this->transactionIndicatesWallet($transaction)) {
                throw ValidationException::withMessages([
                    'refund' => 'المحفظة المرتبطة بهذه المعاملة غير موجودة أو محذوفة.',
                ]);
            }

            return null;
        }

        if ((float) $wallet->balance < $amount) {
            throw ValidationException::withMessages([
                'refund' => 'رصيد المحفظة «' . ($wallet->name ?? ('#' . $wallet->id)) . '» غير كافٍ لاسترداد المبلغ. الرصيد الحالي: '
                    . number_format((float) $wallet->balance, 2) . ' ج.م',
            ]);
        }

        return $wallet->refund(
            $amount,
            $transaction->payment_id,
            null,
            $notes . ' — سحب من محفظة: ' . ($wallet->name ?? ('#' . $wallet->id)),
            $processedBy
        );
    }

    private function findLinkedWalletDeposit(Transaction $transaction): ?WalletTransaction
    {
        return WalletTransaction::query()
            ->where('type', 'deposit')
            ->where(function ($query) use ($transaction) {
                $query->where('transaction_id', $transaction->id);
                if ($transaction->payment_id) {
                    $query->orWhere('payment_id', $transaction->payment_id);
                }
            })
            ->lockForUpdate()
            ->latest('id')
            ->first();
    }

    private function resolveWalletId(Transaction $transaction, ?WalletTransaction $deposit = null): ?int
    {
        if ($deposit?->wallet_id) {
            return (int) $deposit->wallet_id;
        }

        $metadata = is_array($transaction->metadata) ? $transaction->metadata : [];

        if (! empty($metadata['wallet_id'])) {
            return (int) $metadata['wallet_id'];
        }

        if ($transaction->payment_id) {
            $walletId = Payment::query()->whereKey($transaction->payment_id)->value('wallet_id');
            if ($walletId) {
                return (int) $walletId;
            }
        }

        if (! empty($metadata['order_id'])) {
            $walletId = Order::query()->whereKey($metadata['order_id'])->value('wallet_id');
            if ($walletId) {
                return (int) $walletId;
            }
        }

        if ($transaction->payment_id) {
            $orderWalletId = Order::query()
                ->where('payment_id', $transaction->payment_id)
                ->value('wallet_id');
            if ($orderWalletId) {
                return (int) $orderWalletId;
            }
        }

        if ($transaction->invoice_id) {
            $walletId = Payment::query()
                ->where('invoice_id', $transaction->invoice_id)
                ->when($transaction->payment_id, fn ($q) => $q->where('id', $transaction->payment_id))
                ->whereNotNull('wallet_id')
                ->value('wallet_id');
            if ($walletId) {
                return (int) $walletId;
            }
        }

        return null;
    }

    private function transactionIndicatesWallet(Transaction $transaction): bool
    {
        if ($this->resolveWalletId($transaction) !== null) {
            return true;
        }

        $metadata = is_array($transaction->metadata) ? $transaction->metadata : [];

        return ! empty($metadata['wallet_id'])
            || ! empty($metadata['order_id'])
            || ($transaction->payment_id && Payment::query()->whereKey($transaction->payment_id)->whereNotNull('wallet_id')->exists());
    }

    private function hasExistingWalletRefund(Transaction $transaction, float $amount): bool
    {
        return WalletTransaction::query()
            ->where('type', 'refund')
            ->where(function ($query) use ($transaction) {
                $query->where('transaction_id', $transaction->id);
                if ($transaction->payment_id) {
                    $query->orWhere('payment_id', $transaction->payment_id);
                }
            })
            ->where('amount', $amount)
            ->exists();
    }

    private function syncPaymentAndInvoice(Transaction $transaction, float $amount): void
    {
        if ($transaction->payment_id) {
            $payment = Payment::query()->lockForUpdate()->find($transaction->payment_id);
            if ($payment && $amount >= (float) $payment->amount) {
                $payment->update(['status' => 'refunded']);
            }
        }

        if (! $transaction->invoice_id) {
            return;
        }

        /** @var Invoice|null $invoice */
        $invoice = Invoice::query()->lockForUpdate()->find($transaction->invoice_id);
        if (! $invoice) {
            return;
        }

        $paidAmount = (float) $invoice->payments()
            ->where('status', 'completed')
            ->sum('amount');

        if ($paidAmount <= 0) {
            $invoice->update([
                'status' => 'refunded',
                'paid_at' => null,
            ]);

            return;
        }

        if ($paidAmount < (float) $invoice->total_amount) {
            $invoice->update([
                'status' => 'partial',
                'paid_at' => null,
            ]);
        }
    }

    private function syncEnrollment(Transaction $transaction, float $amount): void
    {
        $metadata = is_array($transaction->metadata) ? $transaction->metadata : [];
        $enrollmentId = $metadata['enrollment_id'] ?? null;
        if (! $enrollmentId) {
            return;
        }

        $enrollment = OfflineCourseEnrollment::query()->lockForUpdate()->find($enrollmentId);
        if (! $enrollment) {
            return;
        }

        $enrollment->paid_amount = max(0, (float) $enrollment->paid_amount - $amount);
        $enrollment->updatePaymentStatus();
    }

    private function isCreditTransaction(Transaction $transaction): bool
    {
        return in_array($transaction->type, ['credit', 'income'], true);
    }

    private function nextTransactionNumber(): string
    {
        return 'TXN-' . str_pad((string) (Transaction::count() + 1), 8, '0', STR_PAD_LEFT);
    }
}
