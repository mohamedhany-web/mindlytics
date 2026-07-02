<?php

namespace App\Http\Controllers\Admin\Scholarship;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\ScholarshipRegistration;
use App\Services\Scholarship\ScholarshipStatsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request, ScholarshipStatsService $stats): View
    {
        $query = AdvancedCourse::query()
            ->where('is_scholarship_only', true)
            ->with(['instructor', 'scholarshipProgram'])
            ->withCount([
                'enrollments as active_enrollments_count' => fn ($q) => $q
                    ->where('status', 'active')
                    ->scholarshipOnly(),
            ])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhereHas('scholarshipProgram', fn ($p) => $p->where('name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $courses = $query->paginate(20)->withQueryString();
        $overview = $stats->overview();

        return view('admin.scholarships.courses.index', compact('courses', 'overview'));
    }

    public function show(AdvancedCourse $course, ScholarshipStatsService $stats): View
    {
        abort_unless($course->is_scholarship_only, 404);

        $course->load(['instructor', 'scholarshipProgram.instructor'])
            ->loadCount([
                'enrollments as active_enrollments_count' => fn ($q) => $q
                    ->where('status', 'active')
                    ->scholarshipOnly(),
            ]);

        $registrations = ScholarshipRegistration::query()
            ->where('scholarship_program_id', $course->scholarship_program_id)
            ->where('status', ScholarshipRegistration::STATUS_ACTIVATED)
            ->with('user')
            ->orderByDesc('activated_at')
            ->paginate(20, ['*'], 'students_page');

        return view('admin.scholarships.courses.show', compact('course', 'registrations'));
    }
}
