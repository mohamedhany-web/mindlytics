<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\AdvancedCourse;
use App\Models\User;
use App\Models\StudentCourseEnrollment;
use App\Models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class GroupController extends Controller
{
    /**
     * عرض جميع المجموعات (لكل المدربين) - رقابة الأدمن
     */
    public function index(Request $request): View
    {
        $query = Group::with(['course.instructor', 'leader'])
            ->withCount('members');

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('instructor_id')) {
            $query->whereHas('course', function ($q) use ($request) {
                $q->where('instructor_id', $request->instructor_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $groups = $query->orderBy('created_at', 'desc')->paginate(20);

        $courses = AdvancedCourse::where('is_active', true)->orderBy('title')->get(['id', 'title']);
        $instructors = User::whereIn('role', ['instructor', 'teacher'])->orderBy('name')->get(['id', 'name']);

        $stats = [
            'total' => Group::count(),
            'active' => Group::where('status', 'active')->count(),
            'inactive' => Group::where('status', 'inactive')->count(),
            'archived' => Group::where('status', 'archived')->count(),
        ];

        return view('admin.groups.index', compact('groups', 'courses', 'instructors', 'stats'));
    }

    /**
     * نموذج إنشاء مجموعة جديدة
     */
    public function create(): View
    {
        $courses = AdvancedCourse::where('is_active', true)->with('instructor:id,name')->orderBy('title')->get();
        $students = collect(); // القائد يُعيَّن لاحقاً من صفحة المجموعة أو التعديل

        return view('admin.groups.create', compact('courses', 'students'));
    }

    /**
     * حفظ مجموعة جديدة
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:advanced_courses,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'nullable|exists:users,id',
            'max_members' => 'required|integer|min:2|max:50',
            'status' => 'required|in:active,inactive,archived',
        ], [
            'course_id.required' => 'يجب اختيار الكورس',
            'name.required' => 'اسم المجموعة مطلوب',
            'max_members.min' => 'الحد الأدنى للأعضاء هو 2',
            'max_members.max' => 'الحد الأقصى للأعضاء هو 50',
        ]);

        $group = Group::create($validated);

        if (!empty($validated['leader_id'])) {
            GroupMember::updateOrCreate(
                ['group_id' => $group->id, 'user_id' => $validated['leader_id']],
                ['role' => 'leader', 'joined_at' => now()]
            );
        }

        return redirect()->route('admin.groups.show', $group)
            ->with('success', 'تم إنشاء المجموعة بنجاح');
    }

    /**
     * عرض تفاصيل مجموعة
     */
    public function show(Group $group): View
    {
        $group->load(['course.instructor', 'leader', 'members']);

        $enrollments = StudentCourseEnrollment::where('advanced_course_id', $group->course_id)
            ->where('status', 'active')
            ->with('user')
            ->get();

        $memberUserIds = $group->members->pluck('id')->toArray();
        $availableStudents = $enrollments->pluck('user')->filter(function ($user) use ($memberUserIds) {
            return $user && !in_array($user->id, $memberUserIds);
        })->values();

        return view('admin.groups.show', compact('group', 'availableStudents'));
    }

    /**
     * نموذج تعديل مجموعة
     */
    public function edit(Group $group): View
    {
        $group->load(['course', 'leader', 'members']);

        $courses = AdvancedCourse::where('is_active', true)->orderBy('title')->get();

        $enrollments = StudentCourseEnrollment::where('advanced_course_id', $group->course_id)
            ->where('status', 'active')
            ->with('user')
            ->get();
        $students = $enrollments->pluck('user')->filter()->values();

        return view('admin.groups.edit', compact('group', 'courses', 'students'));
    }

    /**
     * تحديث مجموعة
     */
    public function update(Request $request, Group $group): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:advanced_courses,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'nullable|exists:users,id',
            'max_members' => 'required|integer|min:2|max:50',
            'status' => 'required|in:active,inactive,archived',
        ]);

        $group->update($validated);

        if (array_key_exists('leader_id', $validated)) {
            GroupMember::where('group_id', $group->id)->where('role', 'leader')->update(['role' => 'member']);
            if (!empty($validated['leader_id'])) {
                GroupMember::updateOrCreate(
                    ['group_id' => $group->id, 'user_id' => $validated['leader_id']],
                    ['role' => 'leader', 'joined_at' => now()]
                );
            }
        }

        return redirect()->route('admin.groups.show', $group)
            ->with('success', 'تم تحديث المجموعة بنجاح');
    }

    /**
     * حذف مجموعة
     */
    public function destroy(Group $group): RedirectResponse
    {
        $group->members()->detach();
        $group->delete();

        return redirect()->route('admin.groups.index')
            ->with('success', 'تم حذف المجموعة بنجاح');
    }

    /**
     * إضافة عضو للمجموعة
     */
    public function addMember(Request $request, Group $group): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'nullable|in:leader,member',
        ]);

        if ($group->isFull()) {
            return back()->with('error', 'المجموعة ممتلئة');
        }

        $enrollment = StudentCourseEnrollment::where('advanced_course_id', $group->course_id)
            ->where('user_id', $validated['user_id'])
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'الطالب غير مسجل في هذا الكورس');
        }

        GroupMember::updateOrCreate(
            ['group_id' => $group->id, 'user_id' => $validated['user_id']],
            ['role' => $validated['role'] ?? 'member', 'joined_at' => now()]
        );

        return back()->with('success', 'تم إضافة العضو بنجاح');
    }

    /**
     * إزالة عضو من المجموعة
     */
    public function removeMember(Group $group, $member): RedirectResponse
    {
        $userId = (int) $member;

        GroupMember::where('group_id', $group->id)->where('user_id', $userId)->delete();

        if ($group->leader_id == $userId) {
            $group->update(['leader_id' => null]);
        }

        return back()->with('success', 'تم إزالة العضو بنجاح');
    }
}
