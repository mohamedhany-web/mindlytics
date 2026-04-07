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
        $channel = request()->query('channel') === 'online' ? 'online' : 'offline';

        $lectures = $offlineCourse->offlineLectures()
            ->with('group')
            ->when($channel === 'online', function ($q) {
                $q->whereHas('group', fn ($g) => $g->where('online_booking_enabled', true));
            })
            ->ordered()
            ->get();

        $groups = $this->groupsForChannel($offlineCourse, $channel);

        return view('instructor.offline-courses.lectures.index', compact('offlineCourse', 'lectures', 'groups', 'channel'));
    }

    /**
     * نموذج إضافة محاضرة
     */
    public function create(OfflineCourse $offlineCourse)
    {
        $this->authorizeInstructor($offlineCourse);
        $channel = request()->query('channel') === 'online' ? 'online' : 'offline';

        $groups = $this->groupsForChannel($offlineCourse, $channel);

        return view('instructor.offline-courses.lectures.create', compact('offlineCourse', 'groups', 'channel'));
    }

    /**
     * حفظ محاضرة جديدة
     */
    public function store(Request $request, OfflineCourse $offlineCourse)
    {
        $this->authorizeInstructor($offlineCourse);
        $channel = $request->query('channel') === 'online' ? 'online' : 'offline';

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
            'meeting_url' => 'nullable|url',
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
        if ($validated['group_id']) {
            $allowedGroupExists = $this->groupsForChannel($offlineCourse, $channel)
                ->where('id', (int) $validated['group_id'])
                ->exists();
            if (! $allowedGroupExists) {
                return back()->withErrors(['group_id' => 'المجموعة المختارة غير متاحة لهذا النوع من الكورسات.'])->withInput();
            }
        }
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
            ->route('instructor.offline-courses.lectures.index', ['offlineCourse' => $offlineCourse, 'channel' => $channel])
            ->with('success', 'تم إضافة المحاضرة بنجاح');
    }

    /**
     * عرض محاضرة
     */
    public function show(OfflineCourse $offlineCourse, OfflineLecture $lecture)
    {
        $this->authorizeInstructor($offlineCourse);
        $channel = request()->query('channel') === 'online' ? 'online' : 'offline';
        if ($lecture->offline_course_id !== $offlineCourse->id) {
            abort(404);
        }

        $lecture->load('group');

        return view('instructor.offline-courses.lectures.show', compact('offlineCourse', 'lecture', 'channel'));
    }

    /**
     * نموذج تعديل محاضرة
     */
    public function edit(OfflineCourse $offlineCourse, OfflineLecture $lecture)
    {
        $this->authorizeInstructor($offlineCourse);
        $channel = request()->query('channel') === 'online' ? 'online' : 'offline';
        if ($lecture->offline_course_id !== $offlineCourse->id) {
            abort(404);
        }

        $groups = $this->groupsForChannel($offlineCourse, $channel);

        return view('instructor.offline-courses.lectures.edit', compact('offlineCourse', 'lecture', 'groups', 'channel'));
    }

    /**
     * تحديث محاضرة
     */
    public function update(Request $request, OfflineCourse $offlineCourse, OfflineLecture $lecture)
    {
        $this->authorizeInstructor($offlineCourse);
        $channel = $request->query('channel') === 'online' ? 'online' : 'offline';
        if ($lecture->offline_course_id !== $offlineCourse->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
            'meeting_url' => 'nullable|url',
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
        $lecture->meeting_url = $validated['meeting_url'] ?? null;
        $lecture->duration_minutes = $validated['duration_minutes'] ?? null;
        $lecture->recording_url = $validated['recording_url'] ?? null;
        $lecture->notes = $validated['notes'] ?? null;
        $lecture->group_id = $validated['group_id'] ?? null;
        if ($lecture->group_id) {
            $allowedGroupExists = $this->groupsForChannel($offlineCourse, $channel)
                ->where('id', (int) $lecture->group_id)
                ->exists();
            if (! $allowedGroupExists) {
                return back()->withErrors(['group_id' => 'المجموعة المختارة غير متاحة لهذا النوع من الكورسات.'])->withInput();
            }
        }
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
            ->route('instructor.offline-courses.lectures.index', ['offlineCourse' => $offlineCourse, 'channel' => $channel])
            ->with('success', 'تم تحديث المحاضرة بنجاح');
    }

    /**
     * حذف محاضرة
     */
    public function destroy(OfflineCourse $offlineCourse, OfflineLecture $lecture)
    {
        $this->authorizeInstructor($offlineCourse);
        $channel = request()->query('channel') === 'online' ? 'online' : 'offline';
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
            ->route('instructor.offline-courses.lectures.index', ['offlineCourse' => $offlineCourse, 'channel' => $channel])
            ->with('success', 'تم حذف المحاضرة');
    }

    private function groupsForChannel(OfflineCourse $offlineCourse, string $channel)
    {
        $q = $offlineCourse->groups()->orderBy('name');
        if ($channel === 'online') {
            $q->where(function ($g) {
                $g->where('online_booking_enabled', true)
                    ->orWhere('current_students_online', '>', 0);
            });
        }

        return $q;
    }

    private function authorizeInstructor(OfflineCourse $offlineCourse): void
    {
        if ($offlineCourse->instructor_id !== Auth::id()) {
            abort(403, 'غير مسموح لك بإدارة هذا الكورس الأوفلاين');
        }
    }
}
