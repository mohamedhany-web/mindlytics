<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::withCount(['courses'])
            ->orderBy('name')
            ->get();

        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('admin.subjects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subjects',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'اسم المادة مطلوب',
            'name.unique' => 'اسم المادة موجود مسبقاً',
            'name.max' => 'اسم المادة لا يجب أن يتجاوز 255 حرف',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        Subject::create($data);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم إضافة المادة بنجاح');
    }

    public function show(Subject $subject)
    {
        $subject->load(['courses.classroom']);
        return view('admin.subjects.show', compact('subject'));
    }

    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects')->ignore($subject->id),
            ],
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'اسم المادة مطلوب',
            'name.unique' => 'اسم المادة موجود مسبقاً',
            'name.max' => 'اسم المادة لا يجب أن يتجاوز 255 حرف',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $subject->update($data);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم تحديث المادة بنجاح');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->courses()->count() > 0) {
            return redirect()->route('admin.subjects.index')
                ->with('error', 'لا يمكن حذف المادة لأنها تحتوي على كورسات');
        }

        $subject->delete();

        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم حذف المادة بنجاح');
    }
}