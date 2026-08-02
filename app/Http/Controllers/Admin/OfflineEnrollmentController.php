<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseEnrollment;
use App\Models\OfflineCourseGroup;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Log;
use App\Support\OfflineEnrollmentProvisioner;
use App\Services\AutoInstallmentAgreementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfflineEnrollmentController extends Controller
{
    public function index(Request $request, OfflineCourse $offlineCourse)
    {
        $channel = $request->query('channel', 'offline');
        if (! in_array($channel, ['offline', 'online'], true)) {
            $channel = 'offline';
        }

        $enrollments = $offlineCourse->enrollments()
            ->where('enrollment_channel', $channel)
            ->with(['student', 'group', 'invoice'])
            ->latest('enrolled_at')
            ->paginate(20)
            ->withQueryString();

        $students = User::where('role', 'student')
            ->where('is_active', true)
            ->whereDoesntHave('offlineEnrollments', function ($q) use ($offlineCourse, $channel) {
                $q->where('offline_course_id', $offlineCourse->id)
                    ->where('enrollment_channel', $channel);
            })
            ->get();

        $groups = $offlineCourse->groups()
            ->where('is_active', true)
            ->withCount([
                'enrollments as offline_enrollments_count' => function ($q) {
                    $q->where('enrollment_channel', 'offline');
                },
                'enrollments as online_enrollments_count' => function ($q) {
                    $q->where('enrollment_channel', 'online');
                },
            ])
            ->get();
        $wallets = Wallet::academyWallets()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $channelCounts = [
            'offline' => $offlineCourse->enrollments()->where('enrollment_channel', 'offline')->count(),
            'online' => $offlineCourse->enrollments()->where('enrollment_channel', 'online')->count(),
        ];

        return view('admin.offline-courses.enrollments.index', compact(
            'offlineCourse',
            'enrollments',
            'students',
            'groups',
            'wallets',
            'channel',
            'channelCounts'
        ));
    }

    public function store(Request $request, OfflineCourse $offlineCourse)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'group_id' => 'required|exists:offline_course_groups,id',
            'enrollment_channel' => 'required|in:offline,online',
            'status' => 'required|in:pending,active',
            'payment_type' => 'required|in:full,partial,free',
            'paid_amount' => 'required_if:payment_type,partial|nullable|numeric|min:0',
            'apply_discount' => 'nullable|boolean',
            'discount_type' => 'nullable|in:fixed,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|required_if:payment_type,full,partial|in:cash,wallet',
            'wallet_id' => 'nullable|required_if:payment_method,wallet|exists:wallets,id',
            'payment_notes' => 'nullable|string',
        ]);

        $enrollmentChannel = $validated['enrollment_channel'];

        $existing = OfflineCourseEnrollment::where('user_id', $validated['user_id'])
            ->where('offline_course_id', $offlineCourse->id)
            ->where('enrollment_channel', $enrollmentChannel)
            ->exists();

        if ($existing) {
            return back()->withErrors(['error' => 'الطالب مسجل بالفعل في هذا الكورس على نفس القناة (أوفلاين أو أونلاين).']);
        }

        $group = OfflineCourseGroup::where('id', $validated['group_id'])
            ->where('offline_course_id', $offlineCourse->id)
            ->firstOrFail();

        if (! $group->canEnroll($enrollmentChannel)) {
            $msg = $enrollmentChannel === 'online'
                ? 'سعة الأونلاين للمجموعة ممتلئة أو المجموعة غير متاحة. زِد «سعة الأونلاين» أو اختر مجموعة أخرى.'
                : 'سعة الحضور بالمركز للمجموعة ممتلئة أو المجموعة غير متاحة.';

            return back()->withErrors(['error' => $msg]);
        }

        $listPrice = (float) $offlineCourse->price;
        $discountAmount = 0;
        $finalAmount = $listPrice;
        $paidAmount = 0;
        $workshopActivation = null;

        if ($validated['payment_type'] === 'free') {
            $finalAmount = 0;
            $paidAmount = 0;
        } else {
            $discountAmount = $this->resolveEnrollmentDiscount($request, $listPrice);

            if ($discountAmount <= 0) {
                $student = User::find($validated['user_id']);
                if ($student) {
                    $promoResult = app(\App\Services\WorkshopPromoService::class)
                        ->calculateOfflineDiscount($student, $offlineCourse, $listPrice);
                    if ($promoResult['discount'] > 0) {
                        $discountAmount = $promoResult['discount'];
                        $workshopActivation = $promoResult['activation'];
                    }
                }
            }

            $finalAmount = max(0, round($listPrice - $discountAmount, 2));

            if ($validated['payment_type'] === 'full') {
                $paidAmount = $finalAmount;
            } elseif ($validated['payment_type'] === 'partial') {
                $paidAmount = min((float) ($validated['paid_amount'] ?? 0), $finalAmount);
            }
        }

        $paymentMethod = $validated['payment_method'] ?? 'cash';
        $walletId = isset($validated['wallet_id']) ? (int) $validated['wallet_id'] : null;
        if ($paidAmount > 0 && $paymentMethod === 'wallet') {
            $wallet = Wallet::academyWallets()
                ->where('is_active', true)
                ->whereKey($walletId)
                ->first();
            if (! $wallet) {
                return back()->withErrors(['wallet_id' => 'اختر محفظة أكاديمية نشطة وصحيحة'])->withInput();
            }
        } else {
            $walletId = null;
        }

        DB::beginTransaction();
        try {
            OfflineEnrollmentProvisioner::create(
                $offlineCourse,
                (int) $validated['user_id'],
                (int) $validated['group_id'],
                $validated['status'],
                $finalAmount,
                $paidAmount,
                [
                    'payment_method' => $paymentMethod,
                    'wallet_id' => $walletId,
                    'payment_notes' => $validated['payment_notes'] ?? null,
                ],
                null,
                $enrollmentChannel,
                $discountAmount,
                $listPrice
            );

            $createdEnrollment = OfflineCourseEnrollment::query()
                ->where('user_id', (int) $validated['user_id'])
                ->where('offline_course_id', $offlineCourse->id)
                ->where('group_id', (int) $validated['group_id'])
                ->where('enrollment_channel', $enrollmentChannel)
                ->latest('id')
                ->first();

            if ($createdEnrollment && (float) $createdEnrollment->remaining_amount > 0 && (float) $createdEnrollment->paid_amount > 0) {
                app(AutoInstallmentAgreementService::class)->ensureFromOfflineEnrollment($createdEnrollment, auth()->id());
            }

            if ($workshopActivation && $discountAmount > 0) {
                app(\App\Services\WorkshopPromoService::class)->markUsed(
                    $workshopActivation,
                    'offline_course',
                    $offlineCourse->id
                );
            }

            DB::commit();

            return redirect()->route('admin.offline-courses.enrollments.index', [
                'offlineCourse' => $offlineCourse,
                'channel' => $enrollmentChannel,
            ])->with('success', 'تم تسجيل الطالب بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Offline enrollment error', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'حدث خطأ أثناء التسجيل: ' . $e->getMessage()]);
        }
    }

    public function updateStatus(Request $request, OfflineCourse $offlineCourse, OfflineCourseEnrollment $enrollment)
    {
        abort_unless((int) $enrollment->offline_course_id === (int) $offlineCourse->id, 404);

        $validated = $request->validate([
            'status' => 'required|in:pending,active,completed,suspended,cancelled',
        ]);

        $enrollment->update($validated);

        return back()->with('success', 'تم تحديث حالة التسجيل بنجاح');
    }

    /**
     * تسجيل دفعة إضافية
     */
    public function addPayment(Request $request, OfflineCourse $offlineCourse, OfflineCourseEnrollment $enrollment)
    {
        abort_unless((int) $enrollment->offline_course_id === (int) $offlineCourse->id, 404);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,wallet',
            'wallet_id' => 'nullable|required_if:payment_method,wallet|exists:wallets,id',
            'notes' => 'nullable|string',
        ]);

        $maxPayable = (float) $enrollment->remaining_amount;
        $payAmount = min((float) $validated['amount'], $maxPayable);

        if ($payAmount <= 0) {
            return back()->withErrors(['error' => 'لا يوجد مبلغ متبقي للدفع']);
        }

        $paymentMethod = $validated['payment_method'];
        $walletId = isset($validated['wallet_id']) ? (int) $validated['wallet_id'] : null;
        $wallet = null;
        if ($paymentMethod === 'wallet') {
            $wallet = Wallet::academyWallets()
                ->where('is_active', true)
                ->whereKey($walletId)
                ->first();
            if (! $wallet) {
                return back()->withErrors(['wallet_id' => 'اختر محفظة أكاديمية نشطة وصحيحة'])->withInput();
            }
        } else {
            $walletId = null;
        }

        $paymentMeta = [
            'payment_method' => $paymentMethod,
            'wallet_id' => $walletId,
            'notes' => $validated['notes'] ?? null,
            'deposit_notes' => 'إيداع دفعة إضافية — تسجيل كورس '.((($enrollment->enrollment_channel ?? 'offline') === 'online') ? 'أونلاين' : 'أوفلاين').' #'.$enrollment->id,
        ];
        if ($wallet) {
            $walletLabel = $wallet->name ?: Wallet::typeLabel($wallet->type);
            $paymentMeta['notes'] = trim(($validated['notes'] ?? '').' — '.$walletLabel);
        }

        DB::beginTransaction();
        try {
            $enrollment->paid_amount = (float) $enrollment->paid_amount + $payAmount;
            $enrollment->remaining_amount = max(0, (float) $enrollment->total_amount - (float) $enrollment->paid_amount);
            $enrollment->payment_status = $enrollment->remaining_amount <= 0 ? 'paid' : 'partial';
            $enrollment->save();

            $this->createPaymentRecord($enrollment, $payAmount, $paymentMeta);
            if ((float) $enrollment->remaining_amount > 0 && (float) $enrollment->paid_amount > 0) {
                app(AutoInstallmentAgreementService::class)->ensureFromOfflineEnrollment($enrollment, auth()->id());
            }

            DB::commit();

            return back()->with('success', "تم تسجيل دفعة بمبلغ {$payAmount} بنجاح");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Offline enrollment addPayment failed: '.$e->getMessage(), [
                'enrollment_id' => $enrollment->id,
                'amount' => $payAmount ?? null,
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء تسجيل الدفعة: '.$e->getMessage()]);
        }
    }

    public function destroy(OfflineCourse $offlineCourse, OfflineCourseEnrollment $enrollment)
    {
        abort_unless((int) $enrollment->offline_course_id === (int) $offlineCourse->id, 404);

        DB::beginTransaction();
        try {
            $offlineCourse->decrementStudents();
            if ($enrollment->group_id) {
                $group = $offlineCourse->groups()->find($enrollment->group_id);
                if ($group) {
                    if (($enrollment->enrollment_channel ?? 'offline') === 'online') {
                        $group->decrement('current_students_online');
                    } else {
                        $group->decrement('current_students');
                    }
                }
            }

            $enrollment->delete();
            DB::commit();

            return back()->with('success', 'تم حذف التسجيل بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ أثناء الحذف']);
        }
    }

    private function createPaymentRecord(OfflineCourseEnrollment $enrollment, float $amount, array $data, ?Invoice $invoice = null): void
    {
        $enrollment->loadMissing('course', 'invoice');
        $invoice = $invoice ?? $enrollment->invoice;

        // تسجيلات الأونلاين غالباً تُنشأ بدون فاتورة (paid=0) — أنشئ فاتورة عند أول دفعة
        if (! $invoice) {
            $invoice = $this->ensureEnrollmentInvoice($enrollment);
        }

        $channelLabel = ($enrollment->enrollment_channel ?? 'offline') === 'online' ? 'أونلاين' : 'أوفلاين';
        $courseTitle = $enrollment->course->title ?? '';

        $paymentNumber = 'OFF-PAY-' . str_pad(Payment::count() + 1, 6, '0', STR_PAD_LEFT);

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
                        $depositDescription = 'إيداع كورس '.$channelLabel.' — فاتورة: '.$invoice->invoice_number;
                    } else {
                        $depositDescription .= ' — فاتورة: '.$invoice->invoice_number;
                    }
                    $wallet->deposit(
                        $amount,
                        $payment->id,
                        null,
                        $depositDescription
                    );
                } catch (\Throwable $e) {
                    Log::warning('Wallet deposit skipped during offline enrollment additional payment: '.$e->getMessage(), [
                        'wallet_id' => $walletId,
                        'payment_id' => $payment->id,
                        'enrollment_id' => $enrollment->id,
                    ]);
                }
            }
        }

        $transactionNumber = 'OFF-TXN-' . str_pad(Transaction::count() + 1, 6, '0', STR_PAD_LEFT);

        $txDescription = 'دفعة كورس '.$channelLabel.': '.$courseTitle;
        if ($wallet) {
            $txDescription .= ' — محفظة: '.($wallet->name ?? (string) $wallet->id);
        }

        $metadata = [
            'offline_course_id' => $enrollment->offline_course_id,
            'enrollment_id' => $enrollment->id,
            'group_id' => $enrollment->group_id,
            'enrollment_channel' => $enrollment->enrollment_channel ?? 'offline',
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
    }

    /**
     * إنشاء فاتورة معلقة للتسجيل إن لم تكن موجودة (شائع في تسجيلات الأونلاين غير المدفوعة).
     */
    private function ensureEnrollmentInvoice(OfflineCourseEnrollment $enrollment): Invoice
    {
        $enrollment->loadMissing('course');
        $course = $enrollment->course;
        $channelLabel = ($enrollment->enrollment_channel ?? 'offline') === 'online' ? 'أونلاين' : 'أوفلاين';
        $title = $course->title ?? '';
        $total = (float) ($enrollment->total_amount ?? 0);
        if ($total <= 0) {
            throw new \RuntimeException('لا يمكن إنشاء فاتورة لتسجيل بمبلغ صفر');
        }

        $listPrice = $total + (float) ($enrollment->discount_amount ?? 0);
        $invoiceNumber = 'OFF-INV-'.str_pad((string) $enrollment->id, 6, '0', STR_PAD_LEFT);

        $invoice = Invoice::query()->where('invoice_number', $invoiceNumber)->first();
        if (! $invoice) {
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $enrollment->user_id,
                'type' => 'offline_course',
                'description' => "تسجيل في كورس {$channelLabel}: {$title}",
                'subtotal' => $listPrice,
                'tax_amount' => 0,
                'discount_amount' => (float) ($enrollment->discount_amount ?? 0),
                'total_amount' => $total,
                'status' => 'pending',
                'due_date' => now()->addDays(30),
                'paid_at' => null,
                'items' => [[
                    'description' => "كورس {$channelLabel}: {$title}",
                    'quantity' => 1,
                    'unit_price' => $listPrice,
                    'total' => $total,
                ]],
            ]);
        }

        if ((int) ($enrollment->invoice_id ?? 0) !== (int) $invoice->id) {
            $enrollment->update(['invoice_id' => $invoice->id]);
            $enrollment->setRelation('invoice', $invoice);
        }

        return $invoice;
    }

    private function resolveEnrollmentDiscount(Request $request, float $listPrice): float
    {
        if ($listPrice <= 0 || ! $request->boolean('apply_discount')) {
            return 0;
        }

        $type = $request->input('discount_type', 'fixed');
        $value = (float) $request->input('discount_value', 0);

        if ($value <= 0) {
            return 0;
        }

        if ($type === 'percent') {
            if ($value > 100) {
                return $listPrice;
            }

            return min($listPrice, round($listPrice * $value / 100, 2));
        }

        return min($listPrice, round($value, 2));
    }
}
