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
use App\Support\OfflineEnrollmentProvisioner;
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

        $coursePrice = (float) $offlineCourse->price;
        $paidAmount = 0;

        if ($validated['payment_type'] === 'full') {
            $paidAmount = $coursePrice;
        } elseif ($validated['payment_type'] === 'partial') {
            $paidAmount = min((float) ($validated['paid_amount'] ?? 0), $coursePrice);
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
                $coursePrice,
                $paidAmount,
                [
                    'payment_method' => $paymentMethod,
                    'wallet_id' => $walletId,
                    'payment_notes' => $validated['payment_notes'] ?? null,
                ],
                null,
                $enrollmentChannel
            );

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
            'payment_method' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $maxPayable = (float) $enrollment->remaining_amount;
        $payAmount = min((float) $validated['amount'], $maxPayable);

        if ($payAmount <= 0) {
            return back()->withErrors(['error' => 'لا يوجد مبلغ متبقي للدفع']);
        }

        DB::beginTransaction();
        try {
            $enrollment->paid_amount = (float) $enrollment->paid_amount + $payAmount;
            $enrollment->remaining_amount = max(0, (float) $enrollment->total_amount - (float) $enrollment->paid_amount);
            $enrollment->payment_status = $enrollment->remaining_amount <= 0 ? 'paid' : 'partial';
            $enrollment->save();

            $this->createPaymentRecord($enrollment, $payAmount, $validated);

            DB::commit();

            return back()->with('success', "تم تسجيل دفعة بمبلغ {$payAmount} بنجاح");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ أثناء تسجيل الدفعة']);
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
        $invoice = $invoice ?? $enrollment->invoice;
        if (!$invoice) return;

        $paymentNumber = 'OFF-PAY-' . str_pad(Payment::count() + 1, 6, '0', STR_PAD_LEFT);

        $payment = Payment::create([
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
        ]);

        $transactionNumber = 'OFF-TXN-' . str_pad(Transaction::count() + 1, 6, '0', STR_PAD_LEFT);

        Transaction::create([
            'transaction_number' => $transactionNumber,
            'user_id' => $enrollment->user_id,
            'payment_id' => $payment->id,
            'invoice_id' => $invoice->id,
            'type' => 'credit',
            'category' => 'course_payment',
            'amount' => $amount,
            'currency' => 'EGP',
            'description' => 'دفعة كورس أوفلاين: ' . ($enrollment->course->title ?? ''),
            'status' => 'completed',
            'metadata' => [
                'offline_course_id' => $enrollment->offline_course_id,
                'enrollment_id' => $enrollment->id,
                'group_id' => $enrollment->group_id,
            ],
            'created_by' => Auth::id(),
        ]);

        if ($enrollment->payment_status === 'paid' && $invoice->status !== 'paid') {
            $invoice->markAsPaid();
        }
    }
}
