<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Instructor\CurriculumController as InstructorCurriculumController;
use App\Models\AdvancedCourse;
use Illuminate\Http\Request;

class CurriculumController extends InstructorCurriculumController
{
    protected bool $requireInstructorOwnership = false;

    protected function curriculumView(): string
    {
        return 'admin.curriculum.builder';
    }

    /**
     * قائمة الكورسات لإدارة المنهج وإعدادات فتح الفيديو.
     */
    public function hub()
    {
        $courses = AdvancedCourse::query()
            ->with(['instructor:id,name'])
            ->withCount(['sections', 'lectures'])
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('admin.curriculum.hub', compact('courses'));
    }

    /**
     * تفعيل/إيقاف فتح كل فيديوهات الكورس بدون قيود التسلسل.
     */
    public function updateUnlockPolicy(Request $request, AdvancedCourse $course)
    {
        $validated = $request->validate([
            'admin_unlock_all_videos' => 'required|boolean',
        ]);

        $course->update([
            'admin_unlock_all_videos' => (bool) $validated['admin_unlock_all_videos'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $course->admin_unlock_all_videos
                    ? 'تم فتح كل فيديوهات الكورس بدون قيود'
                    : 'تم إعادة القيود العادية للكورس',
                'admin_unlock_all_videos' => (bool) $course->admin_unlock_all_videos,
            ]);
        }

        return back()->with(
            'success',
            $course->admin_unlock_all_videos
                ? 'تم فتح كل فيديوهات الكورس بدون قيود'
                : 'تم إعادة القيود العادية للكورس'
        );
    }

    protected function examQuestionsManageUrl(\App\Models\AdvancedExam $exam): string
    {
        if (\Illuminate\Support\Facades\Route::has('admin.exams.edit')) {
            return route('admin.exams.edit', $exam);
        }

        return parent::examQuestionsManageUrl($exam);
    }
}
