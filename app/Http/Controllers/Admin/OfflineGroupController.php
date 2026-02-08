<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseGroup;
use App\Models\User;
use Illuminate\Http\Request;

class OfflineGroupController extends Controller
{
    /**
     * عرض قائمة المجموعات لكورس معين
     */
    public function index(OfflineCourse $offlineCourse)
    {
        $groups = $offlineCourse->groups()->with('instructor')->latest()->get();
        $instructors = User::where('role', 'instructor')->where('is_active', true)->get();

        return view('admin.offline-courses.groups.index', compact('offlineCourse', 'groups', 'instructors'));
    }

    /**
     * حفظ مجموعة جديدة
     */
    public function store(Request $request, OfflineCourse $offlineCourse)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructor_id' => 'required|exists:users,id',
            'max_students' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'class_time' => 'nullable',
        ]);

        $validated['offline_course_id'] = $offlineCourse->id;
        $validated['status'] = 'active';

        OfflineCourseGroup::create($validated);

        return redirect()->route('admin.offline-courses.groups.index', $offlineCourse)
                        ->with('success', 'تم إنشاء المجموعة بنجاح');
    }

    /**
     * تحديث مجموعة
     */
    public function update(Request $request, OfflineCourse $offlineCourse, OfflineCourseGroup $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructor_id' => 'required|exists:users,id',
            'max_students' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'class_time' => 'nullable',
            'status' => 'required|in:active,completed,cancelled',
        ]);

        $group->update($validated);

        return redirect()->route('admin.offline-courses.groups.index', $offlineCourse)
                        ->with('success', 'تم تحديث المجموعة بنجاح');
    }

    /**
     * حذف مجموعة
     */
    public function destroy(OfflineCourse $offlineCourse, OfflineCourseGroup $group)
    {
        $group->delete();

        return redirect()->route('admin.offline-courses.groups.index', $offlineCourse)
                        ->with('success', 'تم حذف المجموعة بنجاح');
    }
}
