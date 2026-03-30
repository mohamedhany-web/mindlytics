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

class OfflineCourseBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = OfflineCourseBooking::with(['user', 'course', 'wallet', 'requestedGroup']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('offline_course_id')) {
            $query->where('offline_course_id', $request->offline_course_id);
        }

        $bookings = $query->latest()->paginate(25)->withQueryString();
        $courses = OfflineCourse::orderBy('title')->get(['id', 'title']);
        $pendingCount = OfflineCourseBooking::where('status', OfflineCourseBooking::STATUS_PENDING)->count();

        return view('admin.offline-course-bookings.index', compact('bookings', 'courses', 'pendingCount'));
    }

    public function show(OfflineCourseBooking $offlineCourseBooking)
    {
        $offlineCourseBooking->load(['user', 'course.instructor', 'wallet', 'reviewer', 'assignedGroup', 'requestedGroup']);

        if ($offlineCourseBooking->requested_group_id) {
            $groups = $offlineCourseBooking->course->groups()
                ->where('id', $offlineCourseBooking->requested_group_id)
                ->orderBy('name')
                ->get();
        } else {
            $groups = $offlineCourseBooking->course->groups()
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        return view('admin.offline-course-bookings.show', compact('offlineCourseBooking', 'groups'));
    }

    public function approve(Request $request, OfflineCourseBooking $offlineCourseBooking)
    {
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

        if (! $group->canEnroll()) {
            return back()->withErrors(['error' => 'المجموعة المختارة غير متاحة أو ممتلئة']);
        }

        if (! $course->canEnroll()) {
            return back()->withErrors(['error' => 'الكورس وصل للحد الأقصى من الطلاب']);
        }

        if ($course->enrollments()->where('user_id', $offlineCourseBooking->user_id)->exists()) {
            return back()->withErrors(['error' => 'الطالب مسجل بالفعل في هذا الكورس']);
        }

        $coursePrice = (float) $course->price;
        $paidAmount = $coursePrice > 0 ? $coursePrice : 0.0;
        $paymentMethod = $offlineCourseBooking->payment_method ?: 'bank_transfer';

        $paymentNotes = 'موافقة على حجز أوفلاين #' . $offlineCourseBooking->id;
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
            $paymentMeta['deposit_notes'] = 'إيداع من قبول حجز أوفلاين #'.$offlineCourseBooking->id;
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
                $offlineCourseBooking->student_notes
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
            report($e);

            return back()->withErrors(['error' => 'تعذر إتمام الموافقة. حاول مرة أخرى أو راجع السجلات.']);
        }

        return redirect()->route('admin.offline-course-bookings.index')
            ->with('success', 'تم قبول الحجز وإضافة الطالب للمجموعة وتفعيل التسجيل.');
    }

    public function reject(Request $request, OfflineCourseBooking $offlineCourseBooking)
    {
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

        return redirect()->route('admin.offline-course-bookings.index')
            ->with('success', 'تم رفض طلب الحجز.');
    }
}
