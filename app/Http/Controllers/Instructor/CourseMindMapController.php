<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Lecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseMindMapController extends Controller
{
    public function edit(AdvancedCourse $course)
    {
        $this->authorizeCourse($course);

        $steps = old('steps');
        if (! is_array($steps)) {
            $steps = $course->mind_map_steps;
        }
        if (is_array($steps)) {
            $steps = array_values($steps);
        }
        if (! is_array($steps) || $steps === []) {
            $steps = [
                ['title' => '', 'description' => ''],
                ['title' => '', 'description' => ''],
            ];
        }

        $lecturesForTimetable = Lecture::query()
            ->where('course_id', $course->id)
            ->whereNotNull('scheduled_at')
            ->orderBy('scheduled_at')
            ->limit(50)
            ->get(['title', 'scheduled_at', 'status', 'duration_minutes']);

        return view('instructor.courses.mind-map', compact('course', 'steps', 'lecturesForTimetable'));
    }

    public function update(Request $request, AdvancedCourse $course)
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'steps' => 'nullable|array|max:40',
            'steps.*.title' => 'nullable|string|max:200',
            'steps.*.description' => 'nullable|string|max:5000',
            'mind_map_timetable' => 'nullable|string|max:12000',
        ]);

        $raw = $validated['steps'] ?? [];
        $clean = [];
        foreach ($raw as $row) {
            $title = isset($row['title']) ? trim((string) $row['title']) : '';
            if ($title === '') {
                continue;
            }
            $clean[] = [
                'title' => $title,
                'description' => isset($row['description']) ? trim((string) $row['description']) : '',
            ];
        }

        $publish = $request->has('mind_map_published');
        if ($publish && count($clean) < 2) {
            return redirect()
                ->route('instructor.courses.mind-map.edit', $course)
                ->withInput()
                ->withErrors(['steps' => __('instructor.mind_map_min_steps')]);
        }

        $course->mind_map_steps = $clean;
        $course->mind_map_published = $publish && count($clean) >= 2;
        $timetable = trim((string) ($validated['mind_map_timetable'] ?? ''));
        $course->mind_map_timetable = $timetable === '' ? null : $timetable;
        $course->save();

        return redirect()
            ->route('instructor.courses.mind-map.edit', $course)
            ->with('success', __('instructor.mind_map_saved'));
    }

    private function authorizeCourse(AdvancedCourse $course): void
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }
    }
}
