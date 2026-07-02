<?php

namespace App\Support;

use App\Models\AdvancedCourse;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\StudentCourseEnrollment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OnlineEnrollmentProvisioner
{
    /**
     * @return array{original_price: float, discount_amount: float, final_price: float}
     */
    public static function resolvePricing(AdvancedCourse $course, ?float $finalPrice = null, ?float $discountAmount = null, ?float $originalPrice = null): array
    {
        $original = $originalPrice ?? (float) ($course->price ?? 0);
        $original = max(0, $original);

        $discount = max(0, (float) ($discountAmount ?? 0));

        if ($finalPrice !== null && $finalPrice >= 0) {
            $final = (float) $finalPrice;
            if ($discount <= 0 && $original > $final) {
                $discount = round($original - $final, 2);
            }
        } elseif ($discount > 0 && $original > 0) {
            $final = max(0, round($original - $discount, 2));
        } elseif ($course->courseDiscountAmount() > 0) {
            $discount = $course->courseDiscountAmount();
            $final = $course->effectivePrice();
        } else {
            $final = $original;
            $discount = 0;
        }

        if ($original > 0 && $final > $original) {
            $original = $final;
            $discount = 0;
        }

        return [
            'original_price' => round($original, 2),
            'discount_amount' => round($discount, 2),
            'final_price' => round($final, 2),
        ];
    }

    /**
     * @param  array{payment_method?: string, payment_notes?: string, wallet_id?: int|null, deposit_notes?: string|null}  $paymentMeta
     */
    public static function attachFinancialRecords(
        StudentCourseEnrollment $enrollment,
        AdvancedCourse $course,
        User $student,
        array $pricing,
        array $paymentMeta = [],
        bool $replaceExisting = false,
    ): ?Invoice {
        if ($pricing['final_price'] <= 0) {
            $enrollment->update([
                'original_price' => $pricing['original_price'],
                'discount_amount' => $pricing['discount_amount'],
                'final_price' => $pricing['final_price'],
                'payment_method' => $paymentMeta['payment_method'] ?? null,
            ]);

            return null;
        }

        return DB::transaction(function () use ($enrollment, $course, $student, $pricing, $paymentMeta, $replaceExisting) {
            if ($replaceExisting && $enrollment->invoice_id) {
                $existing = Invoice::query()->find($enrollment->invoice_id);
                if ($existing) {
                    Payment::query()->where('invoice_id', $existing->id)->delete();
                    $existing->delete();
                }
            }

            $invoiceNumber = 'ONL-INV-' . str_pad((string) $enrollment->id, 6, '0', STR_PAD_LEFT);
            $discount = $pricing['discount_amount'];
            $original = $pricing['original_price'];
            $final = $pricing['final_price'];
            $paymentMethod = $paymentMeta['payment_method'] ?? 'cash';

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $student->id,
                'type' => 'online_course',
                'description' => 'تفعيل كورس أونلاين: ' . $course->title,
                'subtotal' => $original,
                'tax_amount' => 0,
                'discount_amount' => $discount,
                'total_amount' => $final,
                'status' => 'paid',
                'due_date' => now(),
                'paid_at' => now(),
                'notes' => $paymentMeta['payment_notes'] ?? 'تفعيل من لوحة التحكم',
                'items' => [[
                    'description' => $course->title,
                    'quantity' => 1,
                    'unit_price' => $original,
                    'discount' => $discount,
                    'total' => $final,
                ]],
            ]);

            $walletId = isset($paymentMeta['wallet_id']) ? (int) $paymentMeta['wallet_id'] : null;
            if ($walletId <= 0) {
                $walletId = null;
            }

            $paymentNumber = 'ONL-PAY-' . str_pad((string) $enrollment->id, 6, '0', STR_PAD_LEFT);
            $paymentAttrs = [
                'payment_number' => $paymentNumber,
                'invoice_id' => $invoice->id,
                'user_id' => $student->id,
                'payment_method' => $paymentMethod,
                'amount' => $final,
                'currency' => 'EGP',
                'status' => 'completed',
                'paid_at' => now(),
                'processed_by' => Auth::id(),
                'notes' => $paymentMeta['payment_notes'] ?? 'دفعة تفعيل كورس أونلاين',
            ];
            if ($walletId !== null) {
                $paymentAttrs['wallet_id'] = $walletId;
            }

            $payment = Payment::create($paymentAttrs);

            $wallet = null;
            if ($paymentMethod === 'wallet' && $walletId !== null && $final > 0) {
                $wallet = Wallet::academyWallets()->where('is_active', true)->find($walletId);
                if ($wallet) {
                    $depositDescription = $paymentMeta['deposit_notes'] ?? $paymentMeta['payment_notes'] ?? '';
                    if ($depositDescription === '') {
                        $depositDescription = 'إيداع كورس أونلاين — فاتورة: ' . $invoice->invoice_number;
                    } else {
                        $depositDescription .= ' — فاتورة: ' . $invoice->invoice_number;
                    }
                    $wallet->deposit($final, $payment->id, null, $depositDescription);
                }
            }

            $txDescription = 'دفعة كورس أونلاين: ' . ($course->title ?? '');
            if ($wallet) {
                $txDescription .= ' — محفظة: ' . ($wallet->name ?? Wallet::typeLabel($wallet->type));
            }

            $metadata = [
                'advanced_course_id' => $course->id,
                'enrollment_id' => $enrollment->id,
                'payment_method' => $paymentMethod,
            ];
            if ($walletId !== null) {
                $metadata['wallet_id'] = $walletId;
            }

            $transaction = Transaction::create([
                'transaction_number' => 'ONL-TXN-' . str_pad((string) (Transaction::count() + 1), 6, '0', STR_PAD_LEFT),
                'user_id' => $student->id,
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'type' => 'credit',
                'category' => 'course_payment',
                'amount' => $final,
                'currency' => 'EGP',
                'description' => $txDescription,
                'status' => 'completed',
                'metadata' => $metadata,
                'created_by' => Auth::id(),
            ]);

            if ($wallet) {
                $walletTransaction = WalletTransaction::where('wallet_id', $wallet->id)
                    ->where('payment_id', $payment->id)
                    ->where('type', 'deposit')
                    ->latest()
                    ->first();
                if ($walletTransaction) {
                    $walletTransaction->update(['transaction_id' => $transaction->id]);
                }
            }

            if (! $invoice->isPaid()) {
                $invoice->markAsPaid();
            }

            $enrollment->update([
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'original_price' => $original,
                'discount_amount' => $discount,
                'final_price' => $final,
                'payment_method' => $paymentMethod,
            ]);

            return $invoice;
        });
    }
}
