<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\AdvancedCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $instructor = Auth::user();
        
        // جلب الكورسات التي يدرسها المدرب
        $courses = AdvancedCourse::where('instructor_id', $instructor->id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();
        
        // جلب المجموعات
        $query = Group::whereHas('course', function($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id);
            })
            ->with(['course', 'leader', 'members'])
            ->withCount('members');
        
        // فلترة حسب الكورس
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        
        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $groups = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // إحصائيات
        $stats = [
            'total' => Group::whereHas('course', function($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id);
            })->count(),
            'active' => Group::whereHas('course', function($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id);
            })->where('status', 'active')->count(),
            'inactive' => Group::whereHas('course', function($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id);
            })->where('status', 'inactive')->count(),
            'total_members' => \App\Models\GroupMember::whereHas('group.course', function($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id);
            })->count(),
        ];
        
        return view('instructor.groups.index', compact('groups', 'courses', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $instructor = Auth::user();
        
        $courses = AdvancedCourse::where('instructor_id', $instructor->id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();
        
        return view('instructor.groups.create', compact('courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $instructor = Auth::user();
        
        $validated = $request->validate([
            'course_id' => 'required|exists:advanced_courses,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'nullable|exists:users,id',
            'max_members' => 'required|integer|min:2|max:50',
            'status' => 'required|in:active,inactive,archived',
        ], [
            'course_id.required' => 'يجب اختيار الكورس',
            'course_id.exists' => 'الكورس المحدد غير موجود',
            'name.required' => 'اسم المجموعة مطلوب',
            'max_members.min' => 'الحد الأدنى للأعضاء هو 2',
            'max_members.max' => 'الحد الأقصى للأعضاء هو 50',
        ]);
        
        // التحقق من أن الكورس يخص هذا المدرب
        $course = AdvancedCourse::where('id', $validated['course_id'])
            ->where('instructor_id', $instructor->id)
            ->firstOrFail();
        
        $group = Group::create($validated);
        
        // إضافة القائد كعضو إذا تم تحديده
        if ($validated['leader_id']) {
            \App\Models\GroupMember::create([
                'group_id' => $group->id,
                'user_id' => $validated['leader_id'],
                'role' => 'leader',
            ]);
        }
        
        return redirect()->route('instructor.groups.show', $group)
            ->with('success', 'تم إنشاء المجموعة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        $instructor = Auth::user();
        
        // التحقق من أن المجموعة تخص كورس يدرسه هذا المدرب
        if ($group->course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بالوصول لهذه المجموعة');
        }
        
        $group->load(['course', 'leader', 'members']);
        
        // واجبات المجموعة (المخصصة لهذه المجموعة)
        $groupAssignments = \App\Models\Assignment::where('group_id', $group->id)
            ->withCount('submissions')
            ->orderBy('due_date')
            ->get();
        
        // جلب الطلاب المسجلين في الكورس (غير أعضاء في المجموعة)
        $enrollments = \App\Models\StudentCourseEnrollment::where('advanced_course_id', $group->course_id)
            ->where('status', 'active')
            ->with('user')
            ->whereDoesntHave('user.groupMembers', function($q) use ($group) {
                $q->where('group_id', $group->id);
            })
            ->get();
        
        return view('instructor.groups.show', compact('group', 'enrollments', 'groupAssignments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        $instructor = Auth::user();
        
        // التحقق من أن المجموعة تخص كورس يدرسه هذا المدرب
        if ($group->course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بتعديل هذه المجموعة');
        }
        
        $courses = AdvancedCourse::where('instructor_id', $instructor->id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();
        
        $group->load(['leader', 'members']);
        
        return view('instructor.groups.edit', compact('group', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        $instructor = Auth::user();
        
        // التحقق من أن المجموعة تخص كورس يدرسه هذا المدرب
        if ($group->course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بتعديل هذه المجموعة');
        }
        
        $validated = $request->validate([
            'course_id' => 'required|exists:advanced_courses,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'nullable|exists:users,id',
            'max_members' => 'required|integer|min:2|max:50',
            'status' => 'required|in:active,inactive,archived',
        ]);
        
        // التحقق من أن الكورس يخص هذا المدرب
        $course = AdvancedCourse::where('id', $validated['course_id'])
            ->where('instructor_id', $instructor->id)
            ->firstOrFail();
        
        $group->update($validated);
        
        // تحديث القائد
        if ($validated['leader_id']) {
            $existingLeader = \App\Models\GroupMember::where('group_id', $group->id)
                ->where('role', 'leader')
                ->first();
            
            if ($existingLeader) {
                $existingLeader->update(['user_id' => $validated['leader_id']]);
            } else {
                \App\Models\GroupMember::updateOrCreate(
                    ['group_id' => $group->id, 'user_id' => $validated['leader_id']],
                    ['role' => 'leader']
                );
            }
        }
        
        return redirect()->route('instructor.groups.show', $group)
            ->with('success', 'تم تحديث المجموعة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        $instructor = Auth::user();
        
        // التحقق من أن المجموعة تخص كورس يدرسه هذا المدرب
        if ($group->course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بحذف هذه المجموعة');
        }
        
        $group->delete();
        
        return redirect()->route('instructor.groups.index')
            ->with('success', 'تم حذف المجموعة بنجاح');
    }

    /**
     * إضافة عضو للمجموعة
     */
    public function addMember(Request $request, Group $group)
    {
        $instructor = Auth::user();
        
        // التحقق من أن المجموعة تخص كورس يدرسه هذا المدرب
        if ($group->course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بإضافة أعضاء لهذه المجموعة');
        }
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'nullable|in:leader,member',
        ]);
        
        // التحقق من أن المجموعة ليست ممتلئة
        if ($group->isFull()) {
            return back()->with('error', 'المجموعة ممتلئة');
        }
        
        // التحقق من أن الطالب مسجل في الكورس
        $enrollment = \App\Models\StudentCourseEnrollment::where('advanced_course_id', $group->course_id)
            ->where('user_id', $validated['user_id'])
            ->where('status', 'active')
            ->first();
        
        if (!$enrollment) {
            return back()->with('error', 'الطالب غير مسجل في هذا الكورس');
        }
        
        \App\Models\GroupMember::updateOrCreate(
            [
                'group_id' => $group->id,
                'user_id' => $validated['user_id'],
            ],
            [
                'role' => $validated['role'] ?? 'member',
            ]
        );
        
        return back()->with('success', 'تم إضافة العضو بنجاح');
    }

    /**
     * إزالة عضو من المجموعة
     */
    public function removeMember(Request $request, Group $group)
    {
        $instructor = Auth::user();
        
        // التحقق من أن المجموعة تخص كورس يدرسه هذا المدرب
        if ($group->course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بإزالة أعضاء من هذه المجموعة');
        }
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        
        \App\Models\GroupMember::where('group_id', $group->id)
            ->where('user_id', $validated['user_id'])
            ->delete();
        
        // إذا كان العضو هو القائد، إزالة القائد من المجموعة
        if ($group->leader_id == $validated['user_id']) {
            $group->update(['leader_id' => null]);
        }
        
        return back()->with('success', 'تم إزالة العضو بنجاح');
    }
}
