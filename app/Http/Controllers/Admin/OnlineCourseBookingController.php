<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseBooking;
use App\Models\OfflineCourseGroup;
use App\Models\Wallet;
use App\Support\OfflineEnrollmentProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OnlineCourseBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = OfflineCourseBooking::with(['user', 'course', 'wallet', 'requestedGroup'])
            ->where('booking_channel', 'online');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('offline_course_id')) {
            $query->where('offline_course_id', $request->offline_course_id);
        }

        $bookings = $query->latest()->paginate(25)->withQueryString();
        $courses = OfflineCourse::orderBy('title')->get(['id', 'title']);
        $pendingCount = OfflineCourseBooking::where('status', OfflineCourseBooking::STATUS_PENDING)
            ->where('booking_channel', 'online')
            ->count();

        return view('admin.online-course-bookings.index', compact('bookings', 'courses', 'pendingCount'));
    }

    public function show(OfflineCourseBooking $offlineCourseBooking)
    {
        abort_unless($offlineCourseBooking->booking_channel === 'online', 404);

        $offlineCourseBooking->load(['user', 'course.instructor', 'wallet', 'reviewer', 'assignedGroup', 'requestedGroup']);
        if ($offlineCourseBooking->requested_group_id) {
            $groups = $offlineCourseBooking->course->groups()
                ->where('id', $offlineCourseBooking->requested_group_id)
                ->orderBy('name')
                ->get();
        } else {
            $groups = $offlineCourseBooking->course->groups()->where('is_active', true)->orderBy('name')->get();
        }

        return view('admin.online-course-bookings.show', compact('offlineCourseBooking', 'groups'));
    }

    public function approve(Request $request, OfflineCourseBooking $offlineCourseBooking)
    {
        abort_unless($offlineCourseBooking->booking_channel === 'online', 404);

        $validated = $request->validate([
            'group_id' => 'required|exists:offline_course_groups,id',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        if (! $offlineCourseBooking->isPending()) {
            return back()->withErrors(['error' => 'هذا الطلب تمت معالجته مسبقاً']);
        }

        $course = $offlineCourseBooking->course;
        $group = OfflineCourseGroup::where('id', $validated['group_id'])
            ->where('offline_course_id', $course->id)
            ->first();
        if (! $group) {
            return back()->withErrors(['error' => 'المجموعة لا تنتمي لهذا الكورس']);
        }
        if ($offlineCourseBooking->requested_group_id !== null
            && (int) $validated['group_id'] !== (int) $offlineCourseBooking->requested_group_id) {
            return back()->withErrors(['error' => 'هذا الطلب مرتبط بمجموعة محددة من رابط الحجز؛ يجب الموافقة على نفس المجموعة.']);
        }
        if (! $group->online_booking_enabled || ! filled($group->online_slug)) {
            return back()->withErrors(['error' => 'المجموعة المختارة غير مفعلة للحجز الأونلاين.']);
        }
        if (! $group->canEnroll('online')) {
            return back()->withErrors(['error' => 'المجموعة المختارة غير متاحة أو ممتلئة للأونلاين']);
        }
        if (! $course->canEnroll()) {
            return back()->withErrors(['error' => 'الكورس وصل للحد الأقصى من الطلاب']);
        }
        if ($course->enrollments()
            ->where('user_id', $offlineCourseBooking->user_id)
            ->where('enrollment_channel', 'online')
            ->exists()) {
            return back()->withErrors(['error' => 'الطالب مسجل بالفعل في هذا الكورس (أونلاين)']);
        }

        $coursePrice = (float) $course->price;
        $paidAmount = $coursePrice > 0 ? $coursePrice : 0.0;
        $paymentMethod = $offlineCourseBooking->payment_method ?: 'bank_transfer';
        $paymentNotes = 'موافقة على حجز أونلاين #' . $offlineCourseBooking->id;
        $offlineCourseBooking->loadMissing('wallet');
        if ($offlineCourseBooking->wallet) {
            $walletLabel = $offlineCourseBooking->wallet->name ?: Wallet::typeLabel($offlineCourseBooking->wallet->type);
            $paymentNotes .= ' — ' . $walletLabel;
        }

        $paymentMeta = [
            'payment_method' => $paymentMethod,
            'payment_notes' => $paymentNotes,
        ];
        if ($paidAmount > 0 && $offlineCourseBooking->wallet_id) {
            $paymentMeta['wallet_id'] = (int) $offlineCourseBooking->wallet_id;
            $paymentMeta['deposit_notes'] = 'إيداع من قبول حجز أونلاين #'.$offlineCourseBooking->id;
        }

        DB::beginTransaction();
        try {
            OfflineEnrollmentProvisioner::create(
                $course,
                $offlineCourseBooking->user_id,
                (int) $validated['group_id'],
                'active',
                $coursePrice,
                $paidAmount,
                $paymentMeta,
                $offlineCourseBooking->student_notes,
                'online'
            );

            $offlineCourseBooking->update([
                'status' => OfflineCourseBooking::STATUS_APPROVED,
                'admin_notes' => $validated['admin_notes'] ?? null,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'assigned_group_id' => (int) $validated['group_id'],
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($e->getMessage() === 'DUPLICATE_ENROLLMENT') {
                return back()->withErrors(['error' => 'الطالب لديه تسجيل أونلاين نشط بالفعل في هذا الكورس.']);
            }
            report($e);
            return back()->withErrors(['error' => 'تعذر إتمام الموافقة. حاول مرة أخرى أو راجع السجلات.']);
        }

        return redirect()->route('admin.online-course-bookings.index')
            ->with('success', 'تم قبول الحجز الأونلاين وإضافة الطالب للمجموعة وتفعيل التسجيل.');
    }

    public function reject(Request $request, OfflineCourseBooking $offlineCourseBooking)
    {
        abort_unless($offlineCourseBooking->booking_channel === 'online', 404);

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        if (! $offlineCourseBooking->isPending()) {
            return back()->withErrors(['error' => 'هذا الطلب تمت معالجته مسبقاً']);
        }

        $offlineCourseBooking->update([
            'status' => OfflineCourseBooking::STATUS_REJECTED,
            'admin_notes' => $validated['admin_notes'] ?? null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.online-course-bookings.index')
            ->with('success', 'تم رفض طلب الحجز.');
    }
}

