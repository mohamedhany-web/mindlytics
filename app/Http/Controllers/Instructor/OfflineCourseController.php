<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseEnrollment;
use App\Models\OfflineGroupSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfflineCourseController extends Controller
{
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

    public function show(OfflineCourse $offlineCourse)
    {
        $instructor = Auth::user();
        if ($offlineCourse->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بعرض هذا الكورس');
        }

        $offlineCourse->load(['locationModel', 'instructor', 'groups.sessions', 'enrollments.student']);

        $stats = [
            'total_students' => $offlineCourse->enrollments()->count(),
            'active_students' => $offlineCourse->enrollments()->where('status', 'active')->count(),
            'total_groups' => $offlineCourse->groups()->count(),
            'total_activities' => $offlineCourse->activities()->count(),
        ];

        return view('instructor.offline-courses.show', compact('offlineCourse', 'stats'));
    }

    /**
     * تقويم المدرب - جلسات الأوفلاين
     */
    public function calendar()
    {
        return view('instructor.calendar');
    }

    /**
     * API - جلب جلسات المدرب للتقويم
     */
    public function calendarEvents(Request $request)
    {
        $instructor = Auth::user();
        $start = $request->get('start');
        $end = $request->get('end');

        $query = OfflineGroupSession::where('instructor_id', $instructor->id)
            ->with(['group.course']);

        if ($start) {
            $query->where('session_date', '>=', $start);
        }
        if ($end) {
            $query->where('session_date', '<=', $end);
        }

        $sessions = $query->ordered()->get();

        $events = $sessions->map(function ($session) {
            $course = $session->group?->course;
            $colors = [
                'scheduled' => '#3B82F6',
                'completed' => '#10B981',
                'cancelled' => '#EF4444',
            ];

            return [
                'id' => $session->id,
                'title' => ($session->title ?: ($course?->title ?? 'جلسة')) . ' - ' . ($session->group?->name ?? ''),
                'start' => $session->session_date->format('Y-m-d') . 'T' . $session->start_time,
                'end' => $session->session_date->format('Y-m-d') . 'T' . $session->end_time,
                'color' => $colors[$session->status] ?? '#6B7280',
                'extendedProps' => [
                    'course' => $course?->title,
                    'group' => $session->group?->name,
                    'location' => $session->location ?? $session->group?->location,
                    'status' => $session->status,
                    'duration' => $session->duration_minutes . ' دقيقة',
                    'notes' => $session->notes,
                ],
            ];
        });

        return response()->json($events);
    }
}
