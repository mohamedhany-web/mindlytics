<?php

namespace App\Http\Controllers\Admin\Scholarship;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipProgram;
use App\Models\ScholarshipRegistration;
use App\Models\User;
use App\Services\Scholarship\ScholarshipStatsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstructorController extends Controller
{
    public function index(Request $request, ScholarshipStatsService $stats): View
    {
        $instructorIds = ScholarshipProgram::query()->distinct()->pluck('instructor_id')->filter();

        $query = User::query()
            ->whereIn('id', $instructorIds)
            ->whereIn('role', ['instructor', 'teacher'])
            ->orderBy('name');

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $instructors = $query->paginate(20)->withQueryString()->through(function (User $instructor) {
            $programIds = ScholarshipProgram::where('instructor_id', $instructor->id)->pluck('id');
            $instructor->programs_count = $programIds->count();
            $instructor->activated_students_count = ScholarshipRegistration::query()
                ->whereIn('scholarship_program_id', $programIds)
                ->where('status', ScholarshipRegistration::STATUS_ACTIVATED)
                ->count();
            $instructor->pending_students_count = ScholarshipRegistration::query()
                ->whereIn('scholarship_program_id', $programIds)
                ->where('status', ScholarshipRegistration::STATUS_REGISTERED)
                ->count();

            return $instructor;
        });

        $overview = $stats->overview();

        return view('admin.scholarships.instructors.index', compact('instructors', 'overview'));
    }

    public function show(User $instructor, ScholarshipStatsService $stats): View
    {
        abort_unless($instructor->isInstructor() || $instructor->role === 'teacher', 404);

        $hasPrograms = ScholarshipProgram::where('instructor_id', $instructor->id)->exists();
        abort_unless($hasPrograms, 404);

        $programs = ScholarshipProgram::query()
            ->where('instructor_id', $instructor->id)
            ->with('course')
            ->withCount([
                'registrations',
                'registrations as activated_count' => fn ($q) => $q->where('status', ScholarshipRegistration::STATUS_ACTIVATED),
                'registrations as pending_count' => fn ($q) => $q->where('status', ScholarshipRegistration::STATUS_REGISTERED),
            ])
            ->orderByDesc('created_at')
            ->get();

        $activatedStudents = ScholarshipRegistration::query()
            ->whereIn('scholarship_program_id', $programs->pluck('id'))
            ->where('status', ScholarshipRegistration::STATUS_ACTIVATED)
            ->with(['user', 'program'])
            ->orderByDesc('activated_at')
            ->paginate(25);

        return view('admin.scholarships.instructors.show', compact('instructor', 'programs', 'activatedStudents'));
    }
}
