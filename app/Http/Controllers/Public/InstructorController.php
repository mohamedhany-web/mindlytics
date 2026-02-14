<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\InstructorProfile;
use App\Models\User;

class InstructorController extends Controller
{
    public function index()
    {
        $profiles = InstructorProfile::approved()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('instructors.index', compact('profiles'));
    }

    public function show(User $instructor)
    {
        if (!$instructor->isInstructor()) {
            abort(404);
        }
        $profile = InstructorProfile::where('user_id', $instructor->id)->approved()->with('user')->firstOrFail();
        $courses = \App\Models\AdvancedCourse::where('instructor_id', $instructor->id)->where('is_active', true)->orderBy('is_featured', 'desc')->get();
        return view('instructors.show', compact('profile', 'courses'));
    }
}
