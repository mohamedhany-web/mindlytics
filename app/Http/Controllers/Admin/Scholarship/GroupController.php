<?php

namespace App\Http\Controllers\Admin\Scholarship;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipGroup;
use App\Models\ScholarshipProgram;
use App\Models\ScholarshipRegistration;
use App\Services\Scholarship\ScholarshipStatsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index(Request $request, ScholarshipStatsService $stats): View
    {
        $query = ScholarshipGroup::query()
            ->with([
                'program:id,name,instructor_id,advanced_course_id',
                'program.instructor:id,name',
                'program.course:id,title',
                'members:id,name,email',
                'createdBy:id,name',
            ])
            ->withCount('members')
            ->orderByDesc('updated_at');

        if ($request->filled('program_id')) {
            $query->where('scholarship_program_id', $request->program_id);
        }

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%")
                    ->orWhereHas('program', fn ($p) => $p->where('name', 'like', "%{$s}%"));
            });
        }

        $groups = $query->paginate(24)->withQueryString();
        $programs = ScholarshipProgram::query()->orderBy('name')->get(['id', 'name']);
        $overview = $stats->overview();

        $activatedByProgram = ScholarshipRegistration::query()
            ->whereIn('scholarship_program_id', $programs->pluck('id'))
            ->activated()
            ->with('user:id,name,email')
            ->get()
            ->groupBy('scholarship_program_id')
            ->map(fn ($rows) => $rows->pluck('user')->filter()->unique('id')->values());

        return view('admin.scholarships.groups.index', compact(
            'groups',
            'programs',
            'overview',
            'activatedByProgram'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'scholarship_program_id' => 'required|exists:scholarship_programs,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'integer|exists:users,id',
        ], [
            'scholarship_program_id.required' => 'المنحة مطلوبة',
            'name.required' => 'اسم المجموعة مطلوب',
        ]);

        $program = ScholarshipProgram::findOrFail($validated['scholarship_program_id']);

        $group = ScholarshipGroup::create([
            'scholarship_program_id' => $program->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'created_by' => auth()->id(),
        ]);

        $group->members()->sync($this->allowedMemberIds($program, $validated['member_ids'] ?? []));

        return back()->with('success', 'تم إنشاء المجموعة بنجاح.');
    }

    public function update(Request $request, ScholarshipGroup $group): RedirectResponse
    {
        $group->load('program');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'integer|exists:users,id',
        ], [
            'name.required' => 'اسم المجموعة مطلوب',
        ]);

        $group->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $group->members()->sync($this->allowedMemberIds($group->program, $validated['member_ids'] ?? []));

        return back()->with('success', 'تم تحديث المجموعة بنجاح.');
    }

    public function destroy(ScholarshipGroup $group): RedirectResponse
    {
        $group->delete();

        return back()->with('success', 'تم حذف المجموعة بنجاح.');
    }

    /**
     * @param  array<int>  $memberIds
     * @return array<int>
     */
    private function allowedMemberIds(ScholarshipProgram $program, array $memberIds): array
    {
        $activatedIds = ScholarshipRegistration::query()
            ->where('scholarship_program_id', $program->id)
            ->activated()
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        return collect($memberIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0 && in_array($id, $activatedIds, true))
            ->unique()
            ->values()
            ->all();
    }
}
