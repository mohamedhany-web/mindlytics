<?php

namespace App\Support;

use App\Models\Invoice;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseEnrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * إنشاء تسجيل أوفلاين مع فاتورة/دفعة (مشترك بين لوحة التحكم والحجز العام).
 */
class OfflineEnrollmentProvisioner
{
    /**
     * @param  array{payment_method?: string, payment_notes?: string}  $paymentMeta
     */
    public static function create(
        OfflineCourse $course,
        int $userId,
        int $groupId,
        string $enrollmentStatus,
        float $coursePrice,
        float $paidAmount,
        array $paymentMeta,
        ?string $enrollmentNotes = null,
        string $enrollmentChannel = 'offline',
        float $discountAmount = 0,
        ?float $listPrice = null,
    ): OfflineCourseEnrollment {
        $alreadyActive = OfflineCourseEnrollment::query()
            ->where('user_id', $userId)
            ->where('offline_course_id', $course->id)
            ->where('enrollment_channel', $enrollmentChannel)
            ->where('status', 'active')
            ->exists();
        if ($alreadyActive) {
            throw new \RuntimeException('DUPLICATE_ENROLLMENT');
        }

        if ($coursePrice <= 0) {
            $remainingAmount = 0;
            $paymentStatus = 'paid';
        } else {
            $remainingAmount = max(0, $coursePrice - $paidAmount);
            $paymentStatus = 'unpaid';
            if ($paidAmount >= $coursePrice) {
                $paymentStatus = 'paid';
            } elseif ($paidAmount > 0) {
                $paymentStatus = 'partial';
            }
        }

        $enrollment = OfflineCourseEnrollment::create([
            'user_id' => $userId,
            'offline_course_id' => $course->id,
            'group_id' => $groupId,
            'enrollment_channel' => $enrollmentChannel,
            'status' => $enrollmentStatus,
            'enrolled_at' => now(),
            'total_amount' => $coursePrice,
            'discount_amount' => max(0, $discountAmount),
            'paid_amount' => $paidAmount,
            'remaining_amount' => $remainingAmount,
            'payment_status' => $paymentStatus,
            'payment_method' => $paymentMeta['payment_method'] ?? null,
            'payment_notes' => $paymentMeta['payment_notes'] ?? null,
            'notes' => $enrollmentNotes,
        ]);

        $course->incrementStudents();
        $group = $course->groups()->find($groupId);
        if ($group) {
            if ($enrollmentChannel === 'online') {
                $group->increment('current_students_online');
            } else {
                $group->increment('current_students');
            }
        }

        if ($paidAmount > 0) {
            self::createFinancialRecords(
                $enrollment,
                $course,
                $paidAmount,
                $coursePrice,
                $paymentMeta,
                max(0, $discountAmount),
                $listPrice ?? $coursePrice
            );
        }

        return $enrollment;
    }

    /**
     * @param  array{payment_method?: string, payment_notes?: string}  $data
     */
    private static function createFinancialRecords(
        OfflineCourseEnrollment $enrollment,
        OfflineCourse $course,
        float $paidAmount,
        float $totalAmount,
        array $data,
        float $discountAmount = 0,
        ?float $listPrice = null,
    ): void {
        $listPrice = $listPrice ?? $totalAmount;
        $invoiceNumber = 'OFF-INV-' . str_pad((string) $enrollment->id, 6, '0', STR_PAD_LEFT);

        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'user_id' => $enrollment->user_id,
            'type' => 'offline_course',
            'description' => "تسجيل في كورس أوفلاين: {$course->title}",
            'subtotal' => $listPrice,
            'tax_amount' => 0,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'status' => $paidAmount >= $totalAmount ? 'paid' : 'pending',
            'due_date' => now()->addDays(30),
            'paid_at' => $paidAmount >= $totalAmount ? now() : null,
            'items' => [[
                'description' => "كورس أوفلاين: {$course->title}",
                'quantity' => 1,
                'unit_price' => $listPrice,
                'total' => $totalAmount,
            ]],
        ]);

        $enrollment->update(['invoice_id' => $invoice->id]);

        $payment = self::createPaymentRecord($enrollment, $course, $paidAmount, $data, $invoice);

        // Ensure offline/online approved bookings appear in student's "orders".
        Order::updateOrCreate(
            ['invoice_id' => $invoice->id],
            [
                'user_id' => $enrollment->user_id,
                'advanced_course_id' => null,
                'academic_year_id' => null,
                'coupon_id' => null,
                'original_amount' => $listPrice,
                'discount_amount' => $discountAmount,
                'amount' => $totalAmount,
                'payment_method' => $data['payment_method'] ?? 'bank_transfer',
                'wallet_id' => isset($data['wallet_id']) && (int) $data['wallet_id'] > 0 ? (int) $data['wallet_id'] : null,
                'payment_proof' => null,
                'payment_id' => $payment?->id,
                'status' => Order::STATUS_APPROVED,
                'notes' => trim(($data['payment_notes'] ?? $data['notes'] ?? '') . "\n" . "طلب كورس " . ($enrollment->enrollment_channel === 'online' ? 'أونلاين' : 'أوفلاين') . ": " . ($course->title ?? '')),
                'approved_at' => now(),
                'approved_by' => Auth::id(),
            ]
        );
    }

    /**
     * @param  array{payment_method?: string, payment_notes?: string, notes?: string, wallet_id?: int|null, deposit_notes?: string|null}  $data
     */
    private static function createPaymentRecord(
        OfflineCourseEnrollment $enrollment,
        OfflineCourse $course,
        float $amount,
        array $data,
        ?Invoice $invoice = null
    ): ?Payment {
        $invoice = $invoice ?? $enrollment->invoice;
        if (! $invoice) {
            return null;
        }

        $paymentNumber = 'OFF-PAY-' . str_pad((string) (Payment::count() + 1), 6, '0', STR_PAD_LEFT);

        $walletId = isset($data['wallet_id']) ? (int) $data['wallet_id'] : null;
        if ($walletId <= 0) {
            $walletId = null;
        }

        $paymentAttrs = [
            'payment_number' => $paymentNumber,
            'invoice_id' => $invoice->id,
            'user_id' => $enrollment->user_id,
            'payment_method' => $data['payment_method'] ?? 'cash',
            'amount' => $amount,
            'currency' => 'EGP',
            'status' => 'completed',
            'notes' => $data['payment_notes'] ?? $data['notes'] ?? null,
            'paid_at' => now(),
            'processed_by' => Auth::id(),
        ];
        if ($walletId !== null) {
            $paymentAttrs['wallet_id'] = $walletId;
        }

        $payment = Payment::create($paymentAttrs);

        $wallet = null;
        if ($walletId !== null && $amount > 0) {
            $wallet = Wallet::find($walletId);
            if ($wallet) {
                try {
                    $depositDescription = $data['deposit_notes'] ?? $data['payment_notes'] ?? $data['notes'] ?? '';
                    if ($depositDescription === '') {
                        $depositDescription = 'إيداع كورس أوفلاين — فاتورة: ' . $invoice->invoice_number;
                    } else {
                        $depositDescription .= ' — فاتورة: ' . $invoice->invoice_number;
                    }
                    $wallet->deposit(
                        $amount,
                        $payment->id,
                        null,
                        $depositDescription
                    );
                } catch (\Throwable $e) {
                    Log::warning('Wallet deposit skipped during offline enrollment payment: '.$e->getMessage(), [
                        'wallet_id' => $walletId,
                        'payment_id' => $payment->id,
                        'enrollment_id' => $enrollment->id,
                    ]);
                }
            }
        }

        $transactionNumber = 'OFF-TXN-' . str_pad((string) (Transaction::count() + 1), 6, '0', STR_PAD_LEFT);

        $txDescription = 'دفعة كورس أوفلاين: '.($course->title ?? '');
        if ($wallet) {
            $txDescription .= ' — محفظة: '.($wallet->name ?? (string) $wallet->id);
        }

        $metadata = [
            'offline_course_id' => $enrollment->offline_course_id,
            'enrollment_id' => $enrollment->id,
            'group_id' => $enrollment->group_id,
        ];
        if ($walletId !== null) {
            $metadata['wallet_id'] = $walletId;
        }

        $transaction = Transaction::create([
            'transaction_number' => $transactionNumber,
            'user_id' => $enrollment->user_id,
            'payment_id' => $payment->id,
            'invoice_id' => $invoice->id,
            'type' => 'credit',
            'category' => 'course_payment',
            'amount' => $amount,
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

        if ($enrollment->payment_status === 'paid' && $invoice->status !== 'paid') {
            $invoice->markAsPaid();
        }

        return $payment;
    }
}
