<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfflineCourse;
use App\Models\User;
use App\Models\OfflineLocation;
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

        return view('admin.offline-courses.create', compact('instructors', 'locations'));
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
        ]);

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
            'instructor',
            'locationModel',
            'groups.instructor',
            'enrollments.student',
            'activities',
            'instructorAgreements'
        ]);

        $stats = [
            'total_students' => $offlineCourse->enrollments()->count(),
            'active_students' => $offlineCourse->enrollments()->where('status', 'active')->count(),
            'total_groups' => $offlineCourse->groups()->count(),
            'total_activities' => $offlineCourse->activities()->count(),
            'completed_activities' => $offlineCourse->activities()->where('status', 'completed')->count(),
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

        return view('admin.offline-courses.edit', compact('offlineCourse', 'instructors', 'locations'));
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
        ]);

        $offlineCourse->update($validated);

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
