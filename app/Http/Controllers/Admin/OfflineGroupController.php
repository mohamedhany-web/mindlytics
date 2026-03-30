<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseGroup;
use App\Models\OfflineGroupSession;
use App\Models\OfflineLocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class OfflineGroupController extends Controller
{
    public function index(OfflineCourse $offlineCourse)
    {
        $groups = $offlineCourse->groups()
            ->with(['instructor', 'locationModel', 'sessions' => fn($q) => $q->ordered()])
            ->withCount(['sessions', 'enrollments'])
            ->latest()
            ->get();

        $instructors = User::where('role', 'instructor')->where('is_active', true)->get();
        $locations = OfflineLocation::where('is_active', true)->get();

        return view('admin.offline-courses.groups.index', compact('offlineCourse', 'groups', 'instructors', 'locations'));
    }

    public function store(Request $request, OfflineCourse $offlineCourse)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructor_id' => 'required|exists:users,id',
            'max_students' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'location_id' => 'nullable|exists:offline_locations,id',
            'class_time' => 'nullable',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'session_duration_hours' => 'nullable|numeric|min:0.5|max:24',
            'public_booking_enabled' => 'sometimes|boolean',
            'public_slug' => ['nullable', 'string', 'max:120', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('offline_course_groups', 'public_slug')],
        ]);

        $validated['offline_course_id'] = $offlineCourse->id;
        $validated['status'] = 'active';
        $validated['public_booking_enabled'] = $request->boolean('public_booking_enabled');
        if ($validated['public_booking_enabled'] && empty($validated['public_slug'] ?? '')) {
            $validated['public_slug'] = OfflineCourseGroup::generateUniquePublicSlug($validated['name']);
        }
        if (! $validated['public_booking_enabled']) {
            $validated['public_slug'] = null;
        }

        $group = OfflineCourseGroup::create($validated);

        return redirect()->route('admin.offline-courses.groups.index', $offlineCourse)
                        ->with('success', 'تم إنشاء المجموعة بنجاح');
    }

    public function update(Request $request, OfflineCourse $offlineCourse, OfflineCourseGroup $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructor_id' => 'required|exists:users,id',
            'max_students' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'location_id' => 'nullable|exists:offline_locations,id',
            'class_time' => 'nullable',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'session_duration_hours' => 'nullable|numeric|min:0.5|max:24',
            'status' => 'required|in:active,completed,cancelled',
            'public_booking_enabled' => 'sometimes|boolean',
            'public_slug' => [
                'nullable',
                'string',
                'max:120',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('offline_course_groups', 'public_slug')->ignore($group->id),
            ],
        ]);

        $validated['public_booking_enabled'] = $request->boolean('public_booking_enabled');
        if ($validated['public_booking_enabled'] && empty($validated['public_slug'] ?? '')) {
            $validated['public_slug'] = OfflineCourseGroup::generateUniquePublicSlug($validated['name'], $group->id);
        }

        $group->update($validated);

        return redirect()->route('admin.offline-courses.groups.index', $offlineCourse)
                        ->with('success', 'تم تحديث المجموعة بنجاح');
    }

    public function destroy(OfflineCourse $offlineCourse, OfflineCourseGroup $group)
    {
        $group->delete();

        return redirect()->route('admin.offline-courses.groups.index', $offlineCourse)
                        ->with('success', 'تم حذف المجموعة بنجاح');
    }

    /**
     * إضافة جلسة لمجموعة
     */
    public function storeSession(Request $request, OfflineCourse $offlineCourse, OfflineCourseGroup $group)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'location' => 'nullable|string|max:255',
            'instructor_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $start = Carbon::parse($validated['session_date'] . ' ' . $validated['start_time']);
        $end = Carbon::parse($validated['session_date'] . ' ' . $validated['end_time']);
        $validated['duration_minutes'] = (int) $start->diffInMinutes($end);
        $validated['group_id'] = $group->id;
        $validated['instructor_id'] = $validated['instructor_id'] ?? $group->instructor_id;

        OfflineGroupSession::create($validated);

        return back()->with('success', 'تم إضافة الجلسة بنجاح');
    }

    /**
     * تحديث جلسة
     */
    public function updateSession(Request $request, OfflineCourse $offlineCourse, OfflineCourseGroup $group, OfflineGroupSession $session)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'location' => 'nullable|string|max:255',
            'instructor_id' => 'nullable|exists:users,id',
            'status' => 'required|in:scheduled,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $start = Carbon::parse($validated['session_date'] . ' ' . $validated['start_time']);
        $end = Carbon::parse($validated['session_date'] . ' ' . $validated['end_time']);
        $validated['duration_minutes'] = (int) $start->diffInMinutes($end);

        $session->update($validated);

        return back()->with('success', 'تم تحديث الجلسة بنجاح');
    }

    /**
     * حذف جلسة
     */
    public function destroySession(OfflineCourse $offlineCourse, OfflineCourseGroup $group, OfflineGroupSession $session)
    {
        $session->delete();

        return back()->with('success', 'تم حذف الجلسة بنجاح');
    }

    /**
     * إنشاء جلسات متعددة دفعة واحدة
     */
    public function bulkCreateSessions(Request $request, OfflineCourse $offlineCourse, OfflineCourseGroup $group)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days' => 'required|array|min:1',
            'days.*' => 'integer|min:0|max:6',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'location' => 'nullable|string|max:255',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $days = $validated['days'];

        $start = Carbon::parse('2000-01-01 ' . $validated['start_time']);
        $end = Carbon::parse('2000-01-01 ' . $validated['end_time']);
        $duration = (int) $start->diffInMinutes($end);

        $sessionsCreated = 0;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            if (in_array($current->dayOfWeek, $days)) {
                OfflineGroupSession::create([
                    'group_id' => $group->id,
                    'title' => 'جلسة ' . ($sessionsCreated + 1),
                    'session_date' => $current->toDateString(),
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                    'duration_minutes' => $duration,
                    'location' => $validated['location'] ?? $group->location,
                    'instructor_id' => $group->instructor_id,
                    'status' => 'scheduled',
                ]);
                $sessionsCreated++;
            }
            $current->addDay();
        }

        // Update group dates
        $group->update([
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);

        return back()->with('success', "تم إنشاء {$sessionsCreated} جلسة بنجاح");
    }
}
