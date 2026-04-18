<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;

class CourseMindMapController extends Controller
{
    public function show(AdvancedCourse $course)
    {
        if (! $course->is_active) {
            abort(404);
        }

        $steps = $course->mind_map_steps;
        if (! $course->mind_map_published || ! is_array($steps) || count($steps) < 2) {
            abort(404);
        }

        $course->load(['instructor', 'academicSubject']);

        return view('public.course-mind-map', compact('course', 'steps'));
    }
}
