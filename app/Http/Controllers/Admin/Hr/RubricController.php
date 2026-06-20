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
        $rubrics = HrRubric::query()->with('creator:id,name')->orderByDesc('is_default')->orderByDesc('updated_at')->paginate(20);

        return view('admin.hr.rubrics.index', compact('rubrics'));
    }

    public function create(): View
    {
        $defaultCriteria = [
            ['key' => 'experience', 'label' => 'الخبرة', 'weight' => 1, 'max' => 10],
            ['key' => 'skills', 'label' => 'المهارات', 'weight' => 1, 'max' => 10],
            ['key' => 'education', 'label' => 'التعليم', 'weight' => 1, 'max' => 10],
            ['key' => 'communication', 'label' => 'التواصل', 'weight' => 1, 'max' => 10],
        ];

        return view('admin.hr.rubrics.create', compact('defaultCriteria'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_default' => 'nullable|boolean',
            'criteria_json' => 'required|string',
        ]);

        $criteria = json_decode((string) $validated['criteria_json'], true);
        if (! is_array($criteria) || $criteria === []) {
            return back()->withErrors(['criteria_json' => 'صيغة JSON غير صحيحة أو فارغة.'])->withInput();
        }

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
            'criteria_json' => 'required|string',
        ]);

        $criteria = json_decode((string) $validated['criteria_json'], true);
        if (! is_array($criteria) || $criteria === []) {
            return back()->withErrors(['criteria_json' => 'صيغة JSON غير صحيحة أو فارغة.'])->withInput();
        }

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

        return redirect()->route('admin.hr.rubrics.index')->with('success', 'تم حذف قالب التقييم.');
    }
}

