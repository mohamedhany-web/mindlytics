<?php

namespace App\Http\Controllers\Instructor\Scholarship;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipGroup;
use App\Models\ScholarshipProgram;
use App\Models\ScholarshipRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
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
        $this->authorizeProgram($program);

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
        $this->authorizeProgram($group->program);

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
        $group->load('program');
        $this->authorizeProgram($group->program);
        $group->delete();

        return back()->with('success', 'تم حذف المجموعة بنجاح.');
    }

    private function authorizeProgram(?ScholarshipProgram $program): void
    {
        if (! $program || (int) $program->instructor_id !== (int) auth()->id()) {
            abort(403, 'غير مصرح لك بإدارة مجموعات هذه المنحة.');
        }
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
