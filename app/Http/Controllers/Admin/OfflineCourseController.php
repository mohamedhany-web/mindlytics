<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseBooking;
use App\Models\Branch;
use App\Models\User;
use App\Models\OfflineLocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfflineCourseController extends Controller
{
    /**
     * عرض قائمة الكورسات الأوفلاين
     */
    public function index(Request $request)
    {
        $query = OfflineCourse::with(['instructor', 'locationModel']);

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('instructor', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب المدرب
        if ($request->filled('instructor_id')) {
            $query->where('instructor_id', $request->instructor_id);
        }

        $courses = $query->latest()->paginate(20);

        // البيانات المساعدة
        $instructors = User::where('role', 'instructor')->where('is_active', true)->get();
        $stats = [
            'total' => OfflineCourse::count(),
            'active' => OfflineCourse::where('status', 'active')->count(),
            'draft' => OfflineCourse::where('status', 'draft')->count(),
            'completed' => OfflineCourse::where('status', 'completed')->count(),
        ];

        return view('admin.offline-courses.index', compact('courses', 'instructors', 'stats'));
    }

    /**
     * عرض صفحة إنشاء كورس أوفلاين
     */
    public function create()
    {
        $instructors = User::where('role', 'instructor')->where('is_active', true)->get();
        $locations = OfflineLocation::where('is_active', true)->get();
        $branches = Branch::query()->whereNull('deleted_at')->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug']);
        $defaultBranchId = Branch::defaultAssignableId();

        return view('admin.offline-courses.create', compact('instructors', 'locations', 'branches', 'defaultBranchId'));
    }

    /**
     * حفظ كورس أوفلاين جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructor_id' => 'required|exists:users,id',
            'location_id' => 'nullable|exists:offline_locations,id',
            'location' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'duration_hours' => 'nullable|integer|min:0',
            'sessions_count' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'max_students' => 'required|integer|min:1',
            'status' => 'required|in:draft,active,completed,cancelled',
            'notes' => 'nullable|string',
            'public_booking_enabled' => 'sometimes|boolean',
            'student_online_portal_enabled' => 'sometimes|boolean',
            'online_only' => 'sometimes|boolean',
            'booking_opens_at' => 'nullable|date',
            'booking_closes_at' => 'nullable|date|after_or_equal:booking_opens_at',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $validated['public_booking_enabled'] = $request->boolean('public_booking_enabled');
        $validated['student_online_portal_enabled'] = $request->boolean('student_online_portal_enabled');
        $validated['online_only'] = $request->boolean('online_only');
        foreach (['booking_opens_at', 'booking_closes_at'] as $k) {
            if (empty($validated[$k] ?? null)) {
                $validated[$k] = null;
            }
        }
        if (! empty($validated['booking_closes_at'])) {
            $dt = Carbon::parse($validated['booking_closes_at']);
            if ($dt->format('H:i:s') === '00:00:00') {
                $validated['booking_closes_at'] = $dt->copy()->endOfDay()->format('Y-m-d H:i:s');
            }
        }

        $validated['branch_id'] = $request->filled('branch_id') ? (int) $request->input('branch_id') : null;

        $course = OfflineCourse::create($validated);

        return redirect()->route('admin.offline-courses.show', $course)
                        ->with('success', 'تم إنشاء الكورس الأوفلاين بنجاح');
    }

    /**
     * عرض تفاصيل كورس أوفلاين
     */
    public function show(OfflineCourse $offlineCourse)
    {
        $offlineCourse->load([
            'branch',
            'instructor',
            'locationModel',
            'groups.instructor',
            'groups.sessions',
            'enrollments.student',
            'enrollments.group',
            'activities',
            'instructorAgreements'
        ]);

        $totalSessions = $offlineCourse->groups->sum(fn($g) => $g->sessions->count());
        $totalRevenue = $offlineCourse->enrollments->sum('paid_amount');
        $totalRemaining = $offlineCourse->enrollments->sum('remaining_amount');

        $stats = [
            'total_students' => $offlineCourse->enrollments()->count(),
            'active_students' => $offlineCourse->enrollments()->where('status', 'active')->count(),
            'students_offline_channel' => $offlineCourse->enrollments()->where('enrollment_channel', 'offline')->count(),
            'students_online_channel' => $offlineCourse->enrollments()->where('enrollment_channel', 'online')->count(),
            'active_offline_channel' => $offlineCourse->enrollments()->where('enrollment_channel', 'offline')->where('status', 'active')->count(),
            'active_online_channel' => $offlineCourse->enrollments()->where('enrollment_channel', 'online')->where('status', 'active')->count(),
            'pending_offline_bookings' => OfflineCourseBooking::where('offline_course_id', $offlineCourse->id)
                ->where('booking_channel', 'offline')
                ->where('status', OfflineCourseBooking::STATUS_PENDING)
                ->count(),
            'pending_online_bookings' => OfflineCourseBooking::where('offline_course_id', $offlineCourse->id)
                ->where('booking_channel', 'online')
                ->where('status', OfflineCourseBooking::STATUS_PENDING)
                ->count(),
            'total_groups' => $offlineCourse->groups()->count(),
            'total_activities' => $offlineCourse->activities()->count(),
            'completed_activities' => $offlineCourse->activities()->where('status', 'completed')->count(),
            'total_sessions' => $totalSessions,
            'total_revenue' => $totalRevenue,
            'total_remaining' => $totalRemaining,
        ];

        return view('admin.offline-courses.show', compact('offlineCourse', 'stats'));
    }

    /**
     * عرض صفحة تعديل كورس أوفلاين
     */
    public function edit(OfflineCourse $offlineCourse)
    {
        $instructors = User::where('role', 'instructor')->where('is_active', true)->get();
        $locations = OfflineLocation::where('is_active', true)->get();
        $branches = Branch::query()->whereNull('deleted_at')->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug']);

        return view('admin.offline-courses.edit', compact('offlineCourse', 'instructors', 'locations', 'branches'));
    }

    /**
     * تحديث كورس أوفلاين
     */
    public function update(Request $request, OfflineCourse $offlineCourse)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructor_id' => 'required|exists:users,id',
            'location_id' => 'nullable|exists:offline_locations,id',
            'location' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'duration_hours' => 'nullable|integer|min:0',
            'sessions_count' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'max_students' => 'required|integer|min:1',
            'status' => 'required|in:draft,active,completed,cancelled',
            'notes' => 'nullable|string',
            'public_booking_enabled' => 'sometimes|boolean',
            'student_online_portal_enabled' => 'sometimes|boolean',
            'online_only' => 'sometimes|boolean',
            'booking_opens_at' => 'nullable|date',
            'booking_closes_at' => 'nullable|date|after_or_equal:booking_opens_at',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $validated['public_booking_enabled'] = $request->boolean('public_booking_enabled');
        $validated['student_online_portal_enabled'] = $request->boolean('student_online_portal_enabled');
        $validated['online_only'] = $request->boolean('online_only');
        if ($validated['online_only']) {
            $validated['public_booking_enabled'] = false;
            $validated['student_online_portal_enabled'] = true;
        }
        foreach (['booking_opens_at', 'booking_closes_at'] as $k) {
            if (empty($validated[$k] ?? null)) {
                $validated[$k] = null;
            }
        }
        if (! empty($validated['booking_closes_at'])) {
            $dt = Carbon::parse($validated['booking_closes_at']);
            if ($dt->format('H:i:s') === '00:00:00') {
                $validated['booking_closes_at'] = $dt->copy()->endOfDay()->format('Y-m-d H:i:s');
            }
        }

        $validated['branch_id'] = $request->filled('branch_id') ? (int) $request->input('branch_id') : null;

        $offlineCourse->update($validated);

        if ($validated['online_only']) {
            $offlineCourse->groups()->update([
                'public_booking_enabled' => false,
                'public_slug' => null,
            ]);
        }

        return redirect()->route('admin.offline-courses.show', $offlineCourse)
                        ->with('success', 'تم تحديث الكورس الأوفلاين بنجاح');
    }

    /**
     * حذف كورس أوفلاين
     */
    public function destroy(OfflineCourse $offlineCourse)
    {
        $offlineCourse->delete();

        return redirect()->route('admin.offline-courses.index')
                        ->with('success', 'تم حذف الكورس الأوفلاين بنجاح');
    }
}
