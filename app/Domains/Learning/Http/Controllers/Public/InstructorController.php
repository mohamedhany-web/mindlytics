<?php

namespace App\Domains\Learning\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Domains\Learning\Models\InstructorProfile;
use App\Models\User;

class InstructorController extends Controller
{
    public function index()
    {
        $profiles = InstructorProfile::approved()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $courseCounts = \App\Domains\Learning\Models\AdvancedCourse::query()
            ->where('is_active', true)
            ->visibleOnCurrentHost()
            ->publicCatalog()
            ->whereIn('instructor_id', $profiles->pluck('user_id')->filter()->all())
            ->selectRaw('instructor_id, COUNT(*) as aggregate')
            ->groupBy('instructor_id')
            ->pluck('aggregate', 'instructor_id');

        $profiles->each(function (InstructorProfile $profile) use ($courseCounts) {
            $profile->setAttribute('courses_count', (int) ($courseCounts[$profile->user_id] ?? 0));
        });

        return view('learning::instructors.index', compact('profiles'));
    }

    public function show(User $instructor)
    {
        if (!$instructor->isInstructor()) {
            abort(404);
        }
        $profile = InstructorProfile::where('user_id', $instructor->id)->approved()->with('user')->firstOrFail();
        $courses = \App\Domains\Learning\Models\AdvancedCourse::where('instructor_id', $instructor->id)
            ->where('is_active', true)
            ->visibleOnCurrentHost()
            ->publicCatalog()
            ->with(['academicSubject', 'instructor'])
            ->withCount(['lectures', 'lessons'])
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->get();

        return view('learning::instructors.show', compact('profile', 'courses'));
    }
}
