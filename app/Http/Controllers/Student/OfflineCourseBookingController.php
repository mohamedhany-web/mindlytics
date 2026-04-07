<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseBooking;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OfflineCourseBookingController extends Controller
{
    public function create(OfflineCourse $offlineCourse)
    {
        $user = Auth::user();

        if (! $offlineCourse->isPublicBookingWindowOpen()) {
            abort(404);
        }

        if ($user->offlineEnrollments()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('enrollment_channel', 'offline')
            ->where('status', 'active')
            ->exists()) {
            return redirect()->route('student.offline-courses.index')
                ->with('info', 'أنت مسجل بالفعل في هذا الكورس.');
        }

        if ($user->offlineCourseBookings()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('status', OfflineCourseBooking::STATUS_PENDING)
            ->exists()) {
            return redirect()->route('student.offline-courses.index')
                ->with('info', 'لديك طلب حجز قيد المراجعة لهذا الكورس.');
        }

        $wallets = Wallet::academyWallets()
            ->where('is_active', true)
            ->whereNotNull('type')
            ->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer'])
            ->where(function ($query) {
                $query->whereNotNull('account_number')
                    ->orWhereNotNull('name');
            })
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('student.offline-booking.form', compact('offlineCourse', 'wallets'));
    }

    public function store(Request $request, OfflineCourse $offlineCourse)
    {
        $user = Auth::user();

        if (! $offlineCourse->isPublicBookingWindowOpen()) {
            abort(404);
        }

        if ($user->offlineEnrollments()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('enrollment_channel', 'offline')
            ->where('status', 'active')
            ->exists()) {
            return back()->withErrors(['error' => 'أنت مسجل بالفعل في هذا الكورس']);
        }

        if ($user->offlineCourseBookings()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('status', OfflineCourseBooking::STATUS_PENDING)
            ->exists()) {
            return back()->withErrors(['error' => 'لديك طلب حجز قيد المراجعة لهذا الكورس']);
        }

        $coursePrice = (float) $offlineCourse->price;

        $walletChannelsExist = Wallet::academyWallets()
            ->where('is_active', true)
            ->whereNotNull('type')
            ->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer'])
            ->where(function ($query) {
                $query->whereNotNull('account_number')
                    ->orWhereNotNull('name');
            })
            ->exists();

        $allowedMethods = $walletChannelsExist ? ['bank_transfer', 'wallet'] : ['bank_transfer'];

        $rules = [
            'payment_method' => ['required', Rule::in($allowedMethods)],
            'wallet_id' => [
                'nullable',
                'required_if:payment_method,wallet',
                Rule::exists('wallets', 'id')->where('is_active', true)->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer']),
            ],
            'transfer_name' => ['required', 'string', 'max:255'],
            'student_notes' => 'nullable|string|max:2000',
        ];

        if ($coursePrice > 0) {
            $rules['payment_proof'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        } else {
            $rules['payment_proof'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';
        }

        $validated = $request->validate($rules, [
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'wallet_id.required_if' => 'يجب اختيار المحفظة أو قناة التحويل',
            'transfer_name.required' => 'الاسم مطلوب',
            'payment_proof.required' => 'صورة إيصال التحويل مطلوبة لهذا الكورس',
        ]);

        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('offline-booking-proofs', 'public');
        }

        OfflineCourseBooking::create([
            'user_id' => $user->id,
            'offline_course_id' => $offlineCourse->id,
            'requested_group_id' => null,
            'wallet_id' => $validated['wallet_id'] ?? null,
            'payment_method' => $validated['payment_method'],
            'payment_proof' => $proofPath,
            'transfer_name' => $validated['transfer_name'],
            'booking_channel' => 'offline',
            'student_notes' => $validated['student_notes'] ?? null,
            'status' => OfflineCourseBooking::STATUS_PENDING,
        ]);

        return redirect()->route('student.offline-courses.index')
            ->with('success', 'تم إرسال طلب الحجز وسيتم المراجعة قريباً.');
    }
}
