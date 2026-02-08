<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\OfflineActivity;
use App\Models\OfflineActivitySubmission;
use App\Models\OfflineCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OfflineActivityController extends Controller
{
    /**
     * قائمة أنشطة الكورس الأوفلاين (واجبات / اختبارات)
     */
    public function index(OfflineCourse $offlineCourse)
    {
        $this->authorizeInstructor($offlineCourse);

        $activities = $offlineCourse->activities()
            ->with('group')
            ->withCount('submissions')
            ->orderBy('due_date')
            ->orderBy('created_at')
            ->get();

        $groups = $offlineCourse->groups()->orderBy('name')->get();

        return view('instructor.offline-courses.activities.index', compact('offlineCourse', 'activities', 'groups'));
    }

    /**
     * نموذج إضافة نشاط
     */
    public function create(OfflineCourse $offlineCourse)
    {
        $this->authorizeInstructor($offlineCourse);

        $groups = $offlineCourse->groups()->orderBy('name')->get();

        return view('instructor.offline-courses.activities.create', compact('offlineCourse', 'groups'));
    }

    /**
     * حفظ نشاط جديد
     */
    public function store(Request $request, OfflineCourse $offlineCourse)
    {
        $this->authorizeInstructor($offlineCourse);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:assignment,quiz,exam,project,presentation,other',
            'due_date' => 'nullable|date',
            'max_score' => 'required|integer|min:0|max:1000',
            'instructions' => 'nullable|string',
            'group_id' => 'nullable|exists:offline_course_groups,id',
            'status' => 'required|in:draft,published',
            'attachments.*' => 'nullable|file|max:51200',
        ], [
            'title.required' => 'عنوان النشاط مطلوب',
            'type.required' => 'نوع النشاط مطلوب',
        ]);

        $validated['offline_course_id'] = $offlineCourse->id;
        $validated['instructor_id'] = Auth::id();
        $validated['group_id'] = $validated['group_id'] ?? null;
        $validated['is_active'] = true;

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('offline-activities/' . $offlineCourse->id, 'public');
                $attachments[] = ['path' => $path, 'name' => $file->getClientOriginalName()];
            }
        }
        $validated['attachments'] = $attachments;

        OfflineActivity::create($validated);

        return redirect()
            ->route('instructor.offline-courses.activities.index', $offlineCourse)
            ->with('success', 'تم إضافة النشاط بنجاح');
    }

    /**
     * عرض نشاط وتقديمات الطلاب
     */
    public function show(OfflineCourse $offlineCourse, OfflineActivity $activity)
    {
        $this->authorizeInstructor($offlineCourse);
        if ($activity->offline_course_id !== $offlineCourse->id) {
            abort(404);
        }

        $activity->load(['group', 'submissions.student']);

        return view('instructor.offline-courses.activities.show', compact('offlineCourse', 'activity'));
    }

    /**
     * نموذج تعديل نشاط
     */
    public function edit(OfflineCourse $offlineCourse, OfflineActivity $activity)
    {
        $this->authorizeInstructor($offlineCourse);
        if ($activity->offline_course_id !== $offlineCourse->id) {
            abort(404);
        }

        $groups = $offlineCourse->groups()->orderBy('name')->get();

        return view('instructor.offline-courses.activities.edit', compact('offlineCourse', 'activity', 'groups'));
    }

    /**
     * تحديث نشاط
     */
    public function update(Request $request, OfflineCourse $offlineCourse, OfflineActivity $activity)
    {
        $this->authorizeInstructor($offlineCourse);
        if ($activity->offline_course_id !== $offlineCourse->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:assignment,quiz,exam,project,presentation,other',
            'due_date' => 'nullable|date',
            'max_score' => 'required|integer|min:0|max:1000',
            'instructions' => 'nullable|string',
            'group_id' => 'nullable|exists:offline_course_groups,id',
            'status' => 'required|in:draft,published,completed,cancelled',
            'is_active' => 'boolean',
            'attachments.*' => 'nullable|file|max:51200',
        ]);

        $activity->fill($validated);
        $activity->group_id = $validated['group_id'] ?? null;
        $activity->is_active = $request->boolean('is_active');

        if ($request->hasFile('attachments')) {
            $current = $activity->attachments ?? [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('offline-activities/' . $offlineCourse->id, 'public');
                $current[] = ['path' => $path, 'name' => $file->getClientOriginalName()];
            }
            $activity->attachments = $current;
        }

        $activity->save();

        return redirect()
            ->route('instructor.offline-courses.activities.show', [$offlineCourse, $activity])
            ->with('success', 'تم تحديث النشاط بنجاح');
    }

    /**
     * حذف نشاط
     */
    public function destroy(OfflineCourse $offlineCourse, OfflineActivity $activity)
    {
        $this->authorizeInstructor($offlineCourse);
        if ($activity->offline_course_id !== $offlineCourse->id) {
            abort(404);
        }

        if ($activity->attachments) {
            foreach ($activity->attachments as $att) {
                if (!empty($att['path'])) {
                    Storage::disk('public')->delete($att['path']);
                }
            }
        }
        $activity->delete();

        return redirect()
            ->route('instructor.offline-courses.activities.index', $offlineCourse)
            ->with('success', 'تم حذف النشاط');
    }

    /**
     * تصحيح تقديم طالب
     */
    public function gradeSubmission(Request $request, OfflineCourse $offlineCourse, OfflineActivity $activity, OfflineActivitySubmission $submission)
    {
        $this->authorizeInstructor($offlineCourse);
        if ($activity->offline_course_id !== $offlineCourse->id || $submission->activity_id !== $activity->id) {
            abort(404);
        }

        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:' . (int) $activity->max_score,
            'feedback' => 'nullable|string',
        ]);

        $submission->score = $validated['score'];
        $submission->feedback = $validated['feedback'] ?? null;
        $submission->graded_by = Auth::id();
        $submission->graded_at = now();
        $submission->status = 'graded';
        $submission->save();

        return redirect()
            ->route('instructor.offline-courses.activities.show', [$offlineCourse, $activity])
            ->with('success', 'تم تصحيح التقديم بنجاح');
    }

    private function authorizeInstructor(OfflineCourse $offlineCourse): void
    {
        if ($offlineCourse->instructor_id !== Auth::id()) {
            abort(403, 'غير مسموح لك بإدارة هذا الكورس الأوفلاين');
        }
    }
}
