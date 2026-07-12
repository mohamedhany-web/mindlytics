<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipGroup;
use App\Models\ScholarshipProgram;
use App\Models\ScholarshipRegistration;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScholarshipController extends Controller
{
    public function index(): View
    {
        $programs = ScholarshipProgram::query()
            ->where('instructor_id', auth()->id())
            ->with(['course'])
            ->withCount([
                'registrations',
                'registrations as activated_count' => fn ($q) => $q->where('status', ScholarshipRegistration::STATUS_ACTIVATED),
                'registrations as pending_count' => fn ($q) => $q->where('status', ScholarshipRegistration::STATUS_REGISTERED),
            ])
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total' => $programs->count(),
            'registrations' => (int) $programs->sum('registrations_count'),
            'pending' => (int) $programs->sum('pending_count'),
            'activated' => (int) $programs->sum('activated_count'),
        ];

        return view('instructor.scholarships.index', compact('programs', 'stats'));
    }

    public function students(Request $request): View
    {
        $programIds = ScholarshipProgram::where('instructor_id', auth()->id())->pluck('id');

        $query = ScholarshipRegistration::query()
            ->whereIn('scholarship_program_id', $programIds)
            ->with(['user', 'program'])
            ->orderByDesc('registered_at');

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->whereHas('user', function ($u) use ($s) {
                $u->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('program_id') && $programIds->contains((int) $request->program_id)) {
            $query->where('scholarship_program_id', $request->program_id);
        }

        $registrations = $query->paginate(25)->withQueryString();

        $programs = ScholarshipProgram::where('instructor_id', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);

        $stats = [
            'total' => ScholarshipRegistration::whereIn('scholarship_program_id', $programIds)->count(),
            'registered' => ScholarshipRegistration::whereIn('scholarship_program_id', $programIds)
                ->where('status', ScholarshipRegistration::STATUS_REGISTERED)->count(),
            'activated' => ScholarshipRegistration::whereIn('scholarship_program_id', $programIds)
                ->where('status', ScholarshipRegistration::STATUS_ACTIVATED)->count(),
            'rejected' => ScholarshipRegistration::whereIn('scholarship_program_id', $programIds)
                ->where('status', ScholarshipRegistration::STATUS_REJECTED)->count(),
        ];

        $groups = ScholarshipGroup::query()
            ->whereIn('scholarship_program_id', $programIds)
            ->with(['program:id,name', 'members:id,name,email'])
            ->withCount('members')
            ->orderBy('name')
            ->get();

        $activatedByProgram = ScholarshipRegistration::query()
            ->whereIn('scholarship_program_id', $programIds)
            ->activated()
            ->with('user:id,name,email')
            ->get()
            ->groupBy('scholarship_program_id')
            ->map(fn ($rows) => $rows->pluck('user')->filter()->unique('id')->values());

        return view('instructor.scholarships.students.index', compact(
            'registrations',
            'programs',
            'stats',
            'groups',
            'activatedByProgram'
        ));
    }

    public function show(Request $request, ScholarshipProgram $program): View
    {
        $this->authorizeProgram($program);

        $program->load('course')->loadCount([
            'registrations',
            'registrations as activated_count' => fn ($q) => $q->where('status', ScholarshipRegistration::STATUS_ACTIVATED),
            'registrations as pending_count' => fn ($q) => $q->where('status', ScholarshipRegistration::STATUS_REGISTERED),
            'registrations as rejected_count' => fn ($q) => $q->where('status', ScholarshipRegistration::STATUS_REJECTED),
        ]);

        $query = ScholarshipRegistration::query()
            ->where('scholarship_program_id', $program->id)
            ->with(['user', 'enrollment'])
            ->orderByDesc('registered_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->whereHas('user', function ($u) use ($s) {
                $u->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $registrations = $query->paginate(30)->withQueryString();

        return view('instructor.scholarships.show', compact('program', 'registrations'));
    }

    private function authorizeProgram(ScholarshipProgram $program): void
    {
        if ((int) $program->instructor_id !== (int) auth()->id()) {
            abort(403);
        }
    }
}
