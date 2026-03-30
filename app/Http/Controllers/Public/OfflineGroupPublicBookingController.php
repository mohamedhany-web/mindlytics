<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\OfflineCourseBooking;
use App\Models\OfflineCourseGroup;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OfflineGroupPublicBookingController extends Controller
{
    public function show(string $slug)
    {
        $group = OfflineCourseGroup::query()
            ->where('public_slug', $slug)
            ->where('public_booking_enabled', true)
            ->with(['course.instructor', 'course.locationModel', 'instructor', 'locationModel'])
            ->firstOrFail();

        $course = $group->course;
        if (! $course || ! $course->is_active || $course->status !== 'active') {
            abort(404);
        }

        // الجدول الزمني فقط (لا يشترط «تفعيل الحجز العام» على الكورس — ذلك لكتالوج الطلاب فقط)
        $courseScheduleOpen = $course->isOfflineBookingScheduleOpen();
        $scheduleBlockReason = null;
        if ($course->booking_opens_at && now()->lt($course->booking_opens_at)) {
            $scheduleBlockReason = 'not_started';
        } elseif (($closesEff = $course->bookingClosesAtEffective()) && now()->gt($closesEff)) {
            $scheduleBlockReason = 'ended';
        }
        $groupHasRoom = $group->is_active
            && $group->status === 'active'
            && $group->effectiveAvailableSeats() > 0;

        $canBook = $courseScheduleOpen && $groupHasRoom && filled($group->public_slug);

        $wallets = Wallet::where('is_active', true)
            ->whereNotNull('type')
            ->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer'])
            ->where(function ($query) {
                $query->whereNotNull('account_number')
                    ->orWhereNotNull('name');
            })
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $effectiveRemaining = $group->effectiveAvailableSeats();
        $walletChannelsExist = $wallets->isNotEmpty();

        return view('public.offline-group-checkout', compact(
            'group',
            'course',
            'canBook',
            'courseScheduleOpen',
            'scheduleBlockReason',
            'groupHasRoom',
            'effectiveRemaining',
            'wallets',
            'walletChannelsExist'
        ));
    }

    public function store(Request $request, string $slug)
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'))->with('info', 'يرجى تسجيل الدخول لإرسال طلب الحجز');
        }

        $group = OfflineCourseGroup::query()
            ->where('public_slug', $slug)
            ->where('public_booking_enabled', true)
            ->with('course')
            ->firstOrFail();

        $course = $group->course;

        if (! $course || ! $course->is_active || $course->status !== 'active') {
            abort(404);
        }

        if (! $course->isOfflineBookingScheduleOpen()) {
            return back()->withErrors(['error' => 'فترة الحجز لهذا الكورس غير مفتوحة حالياً (التواريخ أو حالة الكورس).'])->withInput();
        }

        $user = Auth::user();

        $walletChannelsExist = Wallet::where('is_active', true)
            ->whereNotNull('type')
            ->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer'])
            ->where(function ($query) {
                $query->whereNotNull('account_number')
                    ->orWhereNotNull('name');
            })
            ->exists();

        $allowedMethods = $walletChannelsExist ? ['bank_transfer', 'wallet'] : ['bank_transfer'];
        $coursePrice = (float) $course->price;

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

        try {
            DB::transaction(function () use ($request, $group, $user, $course, $coursePrice, $validated) {
                /** @var OfflineCourseGroup $locked */
                $locked = OfflineCourseGroup::whereKey($group->id)->lockForUpdate()->firstOrFail();

                if (! $locked->public_booking_enabled || ! filled($locked->public_slug)) {
                    throw new \RuntimeException('UNAVAILABLE');
                }

                if (! $locked->is_active || $locked->status !== 'active' || $locked->effectiveAvailableSeats() <= 0) {
                    throw new \RuntimeException('FULL');
                }

                if ($user->offlineEnrollments()->where('offline_course_id', $course->id)->where('status', 'active')->exists()) {
                    throw new \RuntimeException('ENROLLED');
                }

                if ($user->offlineCourseBookings()
                    ->where('offline_course_id', $course->id)
                    ->where('status', OfflineCourseBooking::STATUS_PENDING)
                    ->exists()) {
                    throw new \RuntimeException('PENDING');
                }

                $proofPath = null;
                if ($request->hasFile('payment_proof')) {
                    $proofPath = $request->file('payment_proof')->store('offline-booking-proofs', 'public');
                }

                OfflineCourseBooking::create([
                    'user_id' => $user->id,
                    'offline_course_id' => $course->id,
                    'requested_group_id' => $locked->id,
                    'wallet_id' => $validated['wallet_id'] ?? null,
                    'payment_method' => $validated['payment_method'],
                    'payment_proof' => $proofPath,
                    'transfer_name' => $validated['transfer_name'],
                    'student_notes' => $validated['student_notes'] ?? null,
                    'status' => OfflineCourseBooking::STATUS_PENDING,
                ]);
            });
        } catch (\RuntimeException $e) {
            $messages = [
                'FULL' => 'تم اكتمال عدد هذه المجموعة ولا يمكن استقبال حجوزات جديدة حالياً.',
                'UNAVAILABLE' => 'صفحة الحجز غير متاحة.',
                'ENROLLED' => 'أنت مسجل بالفعل في هذا الكورس.',
                'PENDING' => 'لديك طلب حجز قيد المراجعة لهذا الكورس.',
            ];

            return back()->withErrors(['error' => $messages[$e->getMessage()] ?? 'تعذر إتمام الطلب.'])->withInput();
        }

        return redirect()->route('public.offline-groups.show', $slug)
            ->with('success', 'تم إرسال طلب الحجز لهذه المجموعة. سيتم المراجعة وتفعيل التسجيل بعد الموافقة.');
    }
}
