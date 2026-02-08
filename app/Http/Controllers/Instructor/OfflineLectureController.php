<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\OfflineCourse;
use App\Models\OfflineLecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OfflineLectureController extends Controller
{
    /**
     * قائمة محاضرات الكورس الأوفلاين
     */
    public function index(OfflineCourse $offlineCourse)
    {
        $this->authorizeInstructor($offlineCourse);

        $lectures = $offlineCourse->offlineLectures()
            ->with('group')
            ->ordered()
            ->get();

        $groups = $offlineCourse->groups()->orderBy('name')->get();

        return view('instructor.offline-courses.lectures.index', compact('offlineCourse', 'lectures', 'groups'));
    }

    /**
     * نموذج إضافة محاضرة
     */
    public function create(OfflineCourse $offlineCourse)
    {
        $this->authorizeInstructor($offlineCourse);

        $groups = $offlineCourse->groups()->orderBy('name')->get();

        return view('instructor.offline-courses.lectures.create', compact('offlineCourse', 'groups'));
    }

    /**
     * حفظ محاضرة جديدة
     */
    public function store(Request $request, OfflineCourse $offlineCourse)
    {
        $this->authorizeInstructor($offlineCourse);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
            'duration_minutes' => 'nullable|integer|min:0|max:600',
            'recording_url' => 'nullable|url',
            'notes' => 'nullable|string',
            'group_id' => 'nullable|exists:offline_course_groups,id',
            'download_links' => 'nullable|array',
            'download_links.*.label' => 'nullable|string|max:255',
            'download_links.*.url' => 'nullable|url',
            'attachments.*' => 'nullable|file|max:51200',
        ], [
            'title.required' => 'عنوان المحاضرة مطلوب',
        ]);

        $validated['instructor_id'] = Auth::id();
        $validated['offline_course_id'] = $offlineCourse->id;
        $validated['group_id'] = $validated['group_id'] ?? null;
        $validated['order'] = $offlineCourse->offlineLectures()->max('order') + 1;
        $validated['is_active'] = true;

        $links = [];
        if (!empty($validated['download_links'])) {
            foreach ($validated['download_links'] as $link) {
                if (!empty($link['url'])) {
                    $links[] = ['label' => $link['label'] ?? 'رابط', 'url' => $link['url']];
                }
            }
        }
        $validated['download_links'] = $links;

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('offline-lectures/' . $offlineCourse->id, 'public');
                $attachments[] = ['path' => $path, 'name' => $file->getClientOriginalName()];
            }
        }
        $validated['attachments'] = $attachments;

        unset($validated['download_links.*.label'], $validated['download_links.*.url']);

        OfflineLecture::create($validated);

        return redirect()
            ->route('instructor.offline-courses.lectures.index', $offlineCourse)
            ->with('success', 'تم إضافة المحاضرة بنجاح');
    }

    /**
     * عرض محاضرة
     */
    public function show(OfflineCourse $offlineCourse, OfflineLecture $lecture)
    {
        $this->authorizeInstructor($offlineCourse);
        if ($lecture->offline_course_id !== $offlineCourse->id) {
            abort(404);
        }

        $lecture->load('group');

        return view('instructor.offline-courses.lectures.show', compact('offlineCourse', 'lecture'));
    }

    /**
     * نموذج تعديل محاضرة
     */
    public function edit(OfflineCourse $offlineCourse, OfflineLecture $lecture)
    {
        $this->authorizeInstructor($offlineCourse);
        if ($lecture->offline_course_id !== $offlineCourse->id) {
            abort(404);
        }

        $groups = $offlineCourse->groups()->orderBy('name')->get();

        return view('instructor.offline-courses.lectures.edit', compact('offlineCourse', 'lecture', 'groups'));
    }

    /**
     * تحديث محاضرة
     */
    public function update(Request $request, OfflineCourse $offlineCourse, OfflineLecture $lecture)
    {
        $this->authorizeInstructor($offlineCourse);
        if ($lecture->offline_course_id !== $offlineCourse->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
            'duration_minutes' => 'nullable|integer|min:0|max:600',
            'recording_url' => 'nullable|url',
            'notes' => 'nullable|string',
            'group_id' => 'nullable|exists:offline_course_groups,id',
            'is_active' => 'boolean',
            'download_links' => 'nullable|array',
            'download_links.*.label' => 'nullable|string|max:255',
            'download_links.*.url' => 'nullable|url',
            'attachments.*' => 'nullable|file|max:51200',
        ]);

        $lecture->title = $validated['title'];
        $lecture->description = $validated['description'] ?? null;
        $lecture->scheduled_at = $validated['scheduled_at'] ?? null;
        $lecture->duration_minutes = $validated['duration_minutes'] ?? null;
        $lecture->recording_url = $validated['recording_url'] ?? null;
        $lecture->notes = $validated['notes'] ?? null;
        $lecture->group_id = $validated['group_id'] ?? null;
        $lecture->is_active = $request->boolean('is_active');

        $links = [];
        if (!empty($validated['download_links'])) {
            foreach ($validated['download_links'] as $link) {
                if (!empty($link['url'])) {
                    $links[] = ['label' => $link['label'] ?? 'رابط', 'url' => $link['url']];
                }
            }
        }
        $lecture->download_links = $links;

        if ($request->hasFile('attachments')) {
            $current = $lecture->attachments ?? [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('offline-lectures/' . $offlineCourse->id, 'public');
                $current[] = ['path' => $path, 'name' => $file->getClientOriginalName()];
            }
            $lecture->attachments = $current;
        }

        $lecture->save();

        return redirect()
            ->route('instructor.offline-courses.lectures.index', $offlineCourse)
            ->with('success', 'تم تحديث المحاضرة بنجاح');
    }

    /**
     * حذف محاضرة
     */
    public function destroy(OfflineCourse $offlineCourse, OfflineLecture $lecture)
    {
        $this->authorizeInstructor($offlineCourse);
        if ($lecture->offline_course_id !== $offlineCourse->id) {
            abort(404);
        }

        if ($lecture->attachments) {
            foreach ($lecture->attachments as $att) {
                if (!empty($att['path'])) {
                    Storage::disk('public')->delete($att['path']);
                }
            }
        }
        $lecture->delete();

        return redirect()
            ->route('instructor.offline-courses.lectures.index', $offlineCourse)
            ->with('success', 'تم حذف المحاضرة');
    }

    private function authorizeInstructor(OfflineCourse $offlineCourse): void
    {
        if ($offlineCourse->instructor_id !== Auth::id()) {
            abort(403, 'غير مسموح لك بإدارة هذا الكورس الأوفلاين');
        }
    }
}
