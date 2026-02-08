<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfflineEnrollmentController extends Controller
{
    /**
     * عرض قائمة التسجيلات لكورس معين
     */
    public function index(OfflineCourse $offlineCourse)
    {
        $enrollments = $offlineCourse->enrollments()
            ->with(['student', 'group'])
            ->latest('enrolled_at')
            ->paginate(20);

        $students = User::where('role', 'student')
            ->where('is_active', true)
            ->whereDoesntHave('offlineEnrollments', function($q) use ($offlineCourse) {
                $q->where('offline_course_id', $offlineCourse->id);
            })
            ->get();

        $groups = $offlineCourse->groups()->where('is_active', true)->get();

        return view('admin.offline-courses.enrollments.index', compact('offlineCourse', 'enrollments', 'students', 'groups'));
    }

    /**
     * تسجيل طالب في كورس أوفلاين
     */
    public function store(Request $request, OfflineCourse $offlineCourse)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'group_id' => 'nullable|exists:offline_course_groups,id',
            'status' => 'required|in:pending,active',
        ]);

        // التحقق من عدم وجود تسجيل مسبق
        $existing = OfflineCourseEnrollment::where('user_id', $validated['user_id'])
            ->where('offline_course_id', $offlineCourse->id)
            ->exists();

        if ($existing) {
            return back()->withErrors(['error' => 'الطالب مسجل بالفعل في هذا الكورس']);
        }

        // التحقق من إمكانية التسجيل
        if (!$offlineCourse->canEnroll()) {
            return back()->withErrors(['error' => 'الكورس ممتلئ أو غير متاح للتسجيل']);
        }

        DB::beginTransaction();
        try {
            $enrollment = OfflineCourseEnrollment::create([
                'user_id' => $validated['user_id'],
                'offline_course_id' => $offlineCourse->id,
                'group_id' => $validated['group_id'] ?? null,
                'status' => $validated['status'],
                'enrolled_at' => now(),
            ]);

            // تحديث عدد الطلاب
            $offlineCourse->incrementStudents();
            if ($validated['group_id']) {
                $group = $offlineCourse->groups()->find($validated['group_id']);
                if ($group) {
                    $group->increment('current_students');
                }
            }

            DB::commit();

            return redirect()->route('admin.offline-courses.enrollments.index', $offlineCourse)
                            ->with('success', 'تم تسجيل الطالب بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ أثناء التسجيل']);
        }
    }

    /**
     * تحديث حالة التسجيل
     */
    public function updateStatus(Request $request, OfflineCourse $offlineCourse, OfflineCourseEnrollment $enrollment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,active,completed,suspended,cancelled',
        ]);

        $enrollment->update($validated);

        return back()->with('success', 'تم تحديث حالة التسجيل بنجاح');
    }

    /**
     * حذف تسجيل
     */
    public function destroy(OfflineCourse $offlineCourse, OfflineCourseEnrollment $enrollment)
    {
        DB::beginTransaction();
        try {
            $offlineCourse->decrementStudents();
            if ($enrollment->group_id) {
                $group = $offlineCourse->groups()->find($enrollment->group_id);
                if ($group) {
                    $group->decrement('current_students');
                }
            }

            $enrollment->delete();
            DB::commit();

            return back()->with('success', 'تم حذف التسجيل بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ أثناء الحذف']);
        }
    }
}
