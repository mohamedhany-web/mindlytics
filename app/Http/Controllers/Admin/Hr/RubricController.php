<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\HrRubric;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RubricController extends Controller
{
    public function index(): View
    {
        HrRubric::ensureDefaultExists();

        $rubrics = HrRubric::query()->with('creator:id,name')->orderByDesc('is_default')->orderByDesc('updated_at')->paginate(20);

        $stats = [
            'total' => HrRubric::count(),
            'default' => HrRubric::where('is_default', true)->count(),
        ];

        return view('admin.hr.rubrics.index', compact('rubrics', 'stats'));
    }

    public function create(): View
    {
        $defaultCriteria = HrRubric::defaultCriteriaTemplate();

        return view('admin.hr.rubrics.create', compact('defaultCriteria'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_default' => 'nullable|boolean',
            'criteria' => 'required|array|min:1',
            'criteria.*.key' => 'required|string|max:60|regex:/^[a-z0-9_]+$/',
            'criteria.*.label' => 'required|string|max:255',
            'criteria.*.weight' => 'required|numeric|min:0',
            'criteria.*.max' => 'required|numeric|min:0.1',
        ], [
            'criteria.*.key.regex' => 'المفتاح يجب أن يكون حروف إنجليزية صغيرة وأرقام و _ فقط.',
        ]);

        $criteria = $this->normalizeCriteriaInput($validated['criteria']);

        if ($request->boolean('is_default')) {
            HrRubric::query()->update(['is_default' => false]);
        }

        $rubric = HrRubric::create([
            'name' => $validated['name'],
            'criteria_json' => $criteria,
            'is_default' => $request->boolean('is_default'),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.hr.rubrics.edit', $rubric)->with('success', 'تم إنشاء قالب التقييم.');
    }

    public function edit(HrRubric $rubric): View
    {
        return view('admin.hr.rubrics.edit', compact('rubric'));
    }

    public function update(Request $request, HrRubric $rubric): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_default' => 'nullable|boolean',
            'criteria' => 'required|array|min:1',
            'criteria.*.key' => 'required|string|max:60|regex:/^[a-z0-9_]+$/',
            'criteria.*.label' => 'required|string|max:255',
            'criteria.*.weight' => 'required|numeric|min:0',
            'criteria.*.max' => 'required|numeric|min:0.1',
        ], [
            'criteria.*.key.regex' => 'المفتاح يجب أن يكون حروف إنجليزية صغيرة وأرقام و _ فقط.',
        ]);

        $criteria = $this->normalizeCriteriaInput($validated['criteria']);

        if ($request->boolean('is_default')) {
            HrRubric::query()->where('id', '!=', $rubric->id)->update(['is_default' => false]);
        }

        $rubric->update([
            'name' => $validated['name'],
            'criteria_json' => $criteria,
            'is_default' => $request->boolean('is_default'),
        ]);

        return back()->with('success', 'تم حفظ قالب التقييم.');
    }

    public function destroy(HrRubric $rubric): RedirectResponse
    {
        $rubric->delete();

        HrRubric::ensureDefaultExists();

        return redirect()->route('admin.hr.rubrics.index')->with('success', 'تم حذف قالب التقييم.');
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array{key: string, label: string, weight: float, max: float}>
     */
    private function normalizeCriteriaInput(array $rows): array
    {
        $out = [];
        $keys = [];

        foreach ($rows as $row) {
            $key = strtolower(trim((string) ($row['key'] ?? '')));
            if ($key === '' || in_array($key, $keys, true)) {
                continue;
            }

            $keys[] = $key;
            $out[] = [
                'key' => $key,
                'label' => trim((string) ($row['label'] ?? $key)),
                'weight' => max(0.0, (float) ($row['weight'] ?? 1)),
                'max' => max(0.1, (float) ($row['max'] ?? 10)),
            ];
        }

        if ($out === []) {
            abort(422, 'يجب إضافة معيار واحد على الأقل.');
        }

        return $out;
    }
}
