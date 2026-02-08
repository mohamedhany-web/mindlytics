<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use App\Models\AdvancedCourse;
use App\Models\User;
use Illuminate\Http\Request;

class LectureController extends Controller
{
    public function index(Request $request)
    {
        $query = Lecture::with(['course', 'instructor']);

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $lectures = $query->orderBy('scheduled_at', 'desc')->paginate(20);
        $courses = AdvancedCourse::where('is_active', true)->get();

        return view('admin.lectures.index', compact('lectures', 'courses'));
    }

    public function create()
    {
        $courses = AdvancedCourse::where('is_active', true)->get();
        $instructors = User::where('role', 'instructor')->where('is_active', true)->get();

        return view('admin.lectures.create', compact('courses', 'instructors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:advanced_courses,id',
            'instructor_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teams_registration_link' => 'nullable|url',
            'teams_meeting_link' => 'nullable|url',
            'recording_url' => 'nullable|url',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'has_attendance_tracking' => 'boolean',
            'has_assignment' => 'boolean',
            'has_evaluation' => 'boolean',
        ]);

        $lecture = Lecture::create($validated);

        return redirect()->route('admin.lectures.show', $lecture)
            ->with('success', 'تم إنشاء المحاضرة بنجاح');
    }

    public function show(Lecture $lecture)
    {
        $lecture->load(['course', 'instructor', 'assignments', 'attendanceRecords', 'evaluations']);
        
        return view('admin.lectures.show', compact('lecture'));
    }

    public function edit(Lecture $lecture)
    {
        $courses = AdvancedCourse::where('is_active', true)->get();
        $instructors = User::where('role', 'instructor')->where('is_active', true)->get();

        return view('admin.lectures.edit', compact('lecture', 'courses', 'instructors'));
    }

    public function update(Request $request, Lecture $lecture)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:advanced_courses,id',
            'instructor_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teams_registration_link' => 'nullable|url',
            'teams_meeting_link' => 'nullable|url',
            'recording_url' => 'nullable|url',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:1',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'has_attendance_tracking' => 'boolean',
            'has_assignment' => 'boolean',
            'has_evaluation' => 'boolean',
        ]);

        $lecture->update($validated);

        return redirect()->route('admin.lectures.show', $lecture)
            ->with('success', 'تم تحديث المحاضرة بنجاح');
    }

    public function destroy(Lecture $lecture)
    {
        $lecture->delete();

        return redirect()->route('admin.lectures.index')
            ->with('success', 'تم حذف المحاضرة بنجاح');
    }
}
