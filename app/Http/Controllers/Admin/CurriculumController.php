<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Instructor\CurriculumController as InstructorCurriculumController;
use App\Models\AdvancedCourse;
use App\Models\Lecture;
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

    /**
     * تطبيق نسبة مشاهدة موحّدة على كل محاضرات الكورس (لفتح الفيديو التالي).
     */
    public function applyWatchPercent(Request $request, AdvancedCourse $course)
    {
        if ($request->input('min_watch_percent_to_unlock_next') === '' || $request->input('min_watch_percent_to_unlock_next') === null) {
            $request->merge(['min_watch_percent_to_unlock_next' => null]);
        }

        $validated = $request->validate([
            'min_watch_percent_to_unlock_next' => 'nullable|integer|min:0|max:100',
        ]);

        $percent = array_key_exists('min_watch_percent_to_unlock_next', $validated)
            ? $validated['min_watch_percent_to_unlock_next']
            : null;
        $percent = $percent === null ? null : (int) $percent;

        $updated = Lecture::where('course_id', $course->id)
            ->update(['min_watch_percent_to_unlock_next' => $percent]);

        $message = $percent === null
            ? "تم مسح نسبة المشاهدة من {$updated} محاضرة (سيُستخدم الافتراضي 90%)"
            : "تم تطبيق نسبة {$percent}% على {$updated} محاضرة";

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'updated' => $updated,
                'min_watch_percent_to_unlock_next' => $percent,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * تحديث نسبة مشاهدة محاضرة واحدة من صفحة المنهج.
     */
    public function updateLectureWatchPercent(Request $request, Lecture $lecture)
    {
        if ($request->input('min_watch_percent_to_unlock_next') === '' || $request->input('min_watch_percent_to_unlock_next') === null) {
            $request->merge(['min_watch_percent_to_unlock_next' => null]);
        }

        $validated = $request->validate([
            'min_watch_percent_to_unlock_next' => 'nullable|integer|min:0|max:100',
        ]);

        $percent = array_key_exists('min_watch_percent_to_unlock_next', $validated)
            ? $validated['min_watch_percent_to_unlock_next']
            : null;
        $percent = $percent === null ? null : (int) $percent;

        $lecture->update(['min_watch_percent_to_unlock_next' => $percent]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث نسبة المشاهدة',
                'min_watch_percent_to_unlock_next' => $lecture->min_watch_percent_to_unlock_next,
            ]);
        }

        return back()->with('success', 'تم تحديث نسبة المشاهدة');
    }

    protected function examQuestionsManageUrl(\App\Models\AdvancedExam $exam): string
    {
        if (\Illuminate\Support\Facades\Route::has('admin.exams.edit')) {
            return route('admin.exams.edit', $exam);
        }

        return parent::examQuestionsManageUrl($exam);
    }
}
