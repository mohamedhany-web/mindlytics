<?php

namespace App\Http\Controllers\Admin\Scholarship;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipProgram;
use App\Models\ScholarshipRegistration;
use App\Models\User;
use App\Services\Scholarship\ScholarshipProgramService;
use App\Services\Scholarship\ScholarshipStatsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(Request $request, ScholarshipStatsService $stats): View
    {
        $query = ScholarshipProgram::query()
            ->with(['instructor', 'course'])
            ->withCount([
                'registrations',
                'registrations as activated_count' => fn ($q) => $q->where('status', ScholarshipRegistration::STATUS_ACTIVATED),
                'registrations as pending_count' => fn ($q) => $q->where('status', ScholarshipRegistration::STATUS_REGISTERED),
            ])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('slug', 'like', "%{$s}%");
            });
        }

        $programs = $query->paginate(20)->withQueryString();
        $overview = $stats->overview();

        return view('admin.scholarships.programs.index', compact('programs', 'overview'));
    }

    public function create(): View
    {
        $instructors = User::query()
            ->whereIn('role', ['instructor', 'teacher'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.scholarships.programs.create', compact('instructors'));
    }

    public function store(Request $request, ScholarshipProgramService $service): RedirectResponse
    {
        $validated = $this->validateProgram($request);
        $validated['is_active'] = $request->boolean('is_active', true);

        $program = $service->create($validated);

        return redirect()->route('admin.scholarships.programs.show', $program)
            ->with('success', 'تم إنشاء المنحة ورابط التسجيل بنجاح.');
    }

    public function show(ScholarshipProgram $program): View
    {
        $program->load(['instructor', 'course'])->loadCount([
            'registrations',
            'registrations as activated_count' => fn ($q) => $q->where('status', ScholarshipRegistration::STATUS_ACTIVATED),
            'registrations as pending_count' => fn ($q) => $q->where('status', ScholarshipRegistration::STATUS_REGISTERED),
            'registrations as rejected_count' => fn ($q) => $q->where('status', ScholarshipRegistration::STATUS_REJECTED),
            'groups',
        ]);

        $registrations = ScholarshipRegistration::query()
            ->where('scholarship_program_id', $program->id)
            ->with('user')
            ->orderByDesc('registered_at')
            ->paginate(30);

        $groups = $program->groups()
            ->with(['members:id,name'])
            ->withCount('members')
            ->get();

        return view('admin.scholarships.programs.show', compact('program', 'registrations', 'groups'));
    }

    public function edit(ScholarshipProgram $program): View
    {
        $instructors = User::query()
            ->whereIn('role', ['instructor', 'teacher'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.scholarships.programs.edit', compact('program', 'instructors'));
    }

    public function update(Request $request, ScholarshipProgram $program, ScholarshipProgramService $service): RedirectResponse
    {
        $validated = $this->validateProgram($request, $program);
        $validated['is_active'] = $request->boolean('is_active', true);

        $service->update($program, $validated);

        return redirect()->route('admin.scholarships.programs.show', $program)
            ->with('success', 'تم تحديث المنحة.');
    }

    public function destroy(ScholarshipProgram $program): RedirectResponse
    {
        if ($program->registrations()->where('status', ScholarshipRegistration::STATUS_ACTIVATED)->exists()) {
            return back()->with('error', 'لا يمكن حذف منحة لديها طلاب مفعّلين.');
        }

        $program->delete();

        return redirect()->route('admin.scholarships.programs.index')
            ->with('success', 'تم حذف المنحة.');
    }

    private function validateProgram(Request $request, ?ScholarshipProgram $program = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'instructor_id' => 'required|exists:users,id',
            'is_active' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'slug' => 'nullable|string|max:120|unique:scholarship_programs,slug,' . ($program?->id ?? 'NULL'),
        ], [
            'name.required' => 'اسم المنحة مطلوب',
            'instructor_id.required' => 'المدرب مطلوب',
        ]);
    }
}
