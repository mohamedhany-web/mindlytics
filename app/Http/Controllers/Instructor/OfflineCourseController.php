<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfflineCourseController extends Controller
{
    /**
     * قائمة كورسات الأوفلاين المعينة للمدرب
     */
    public function index(Request $request)
    {
        $instructor = Auth::user();

        $query = OfflineCourse::where('instructor_id', $instructor->id)
            ->with(['locationModel'])
            ->withCount(['groups', 'enrollments']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $courses = $query->orderBy('start_date', 'desc')->paginate(12);

        $stats = [
            'total' => OfflineCourse::where('instructor_id', $instructor->id)->count(),
            'active' => OfflineCourse::where('instructor_id', $instructor->id)->where('status', 'active')->count(),
            'draft' => OfflineCourse::where('instructor_id', $instructor->id)->where('status', 'draft')->count(),
            'completed' => OfflineCourse::where('instructor_id', $instructor->id)->where('status', 'completed')->count(),
            'total_students' => OfflineCourseEnrollment::whereHas('course', function ($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id);
            })->where('status', 'active')->count(),
        ];

        return view('instructor.offline-courses.index', compact('courses', 'stats'));
    }

    /**
     * عرض تفاصيل كورس أوفلاين واحد (للمدرب المعين فقط)
     */
    public function show(OfflineCourse $offlineCourse)
    {
        $instructor = Auth::user();
        if ($offlineCourse->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بعرض هذا الكورس');
        }

        $offlineCourse->load(['locationModel', 'instructor', 'groups', 'enrollments']);

        $stats = [
            'total_students' => $offlineCourse->enrollments()->count(),
            'active_students' => $offlineCourse->enrollments()->where('status', 'active')->count(),
            'total_groups' => $offlineCourse->groups()->count(),
            'total_activities' => $offlineCourse->activities()->count(),
        ];

        return view('instructor.offline-courses.show', compact('offlineCourse', 'stats'));
    }
}
