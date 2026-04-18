<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseSection;
use App\Models\OfflineCurriculumItem;
use App\Models\OfflineGroupSession;
use App\Models\OfflineLecture;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OfflineLectureController extends Controller
{
    /**
     * جلسات الكورس (التقويم) — قائمة بكل الجلسات التي يقدّمها المدرب في هذا الكورس.
     */
    public function index(OfflineCourse $offlineCourse)
    {
        $this->authorizeInstructor($offlineCourse);
        $channel = request()->query('channel') === 'online' ? 'online' : 'offline';

        $sessions = OfflineGroupSession::query()
            ->forOfflineCourse($offlineCourse, $channel)
            ->with(['group', 'instructor'])
            ->withCount('lectures')
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();

        return view('instructor.offline-courses.lectures.index', compact('offlineCourse', 'sessions', 'channel'));
    }

    /**
     * صفحة كاملة: تفاصيل جلسة واحدة من تقويم المجموعة.
     */
    public function showGroupSession(OfflineCourse $offlineCourse, OfflineGroupSession $session)
    {
        $this->authorizeInstructor($offlineCourse);
        $channel = request()->query('channel') === 'online' ? 'online' : 'offline';

        $this->assertGroupSessionBelongsToCourse($offlineCourse, $session, $channel);

        $session->load([
            'group.course',
            'instructor',
            'lectures' => fn ($q) => $q->orderBy('order')->orderBy('id'),
        ]);

        return view('instructor.offline-courses.lectures.group-session-show', compact('offlineCourse', 'session', 'channel'));
    }

    /**
     * نموذج إضافة محاضرة
     */
    public function create(Request $request, OfflineCourse $offlineCourse)
    {
        $this->authorizeInstructor($offlineCourse);
        $channel = $request->query('channel') === 'online' ? 'online' : 'offline';

        $groups = $this->groupsForChannel($offlineCourse, $channel)->get();

        $groupSessions = OfflineGroupSession::query()
            ->forOfflineCourse($offlineCourse, $channel)
            ->with('group')
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();
        $hasGroupSessions = $groupSessions->isNotEmpty();

        $curriculumSectionId = null;
        if ($request->filled('curriculum_section')) {
            $sid = (int) $request->query('curriculum_section');
            if ($sid > 0 && OfflineCourseSection::query()
                ->where('offline_course_id', $offlineCourse->id)
                ->whereKey($sid)
                ->exists()) {
                $curriculumSectionId = $sid;
            }
        }

        return view('instructor.offline-courses.lectures.create', compact(
            'offlineCourse',
            'groups',
            'channel',
            'curriculumSectionId',
            'groupSessions',
            'hasGroupSessions'
        ));
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
            'session_agenda' => 'nullable|string|max:60000',
            'offline_attendee_mindmap' => 'nullable|string|max:65000',
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
            'curriculum_section_id' => 'nullable|integer|exists:offline_course_sections,id',
            'offline_group_session_id' => 'nullable|integer|exists:offline_group_sessions,id',
        ], [
            'title.required' => 'عنوان المحاضرة مطلوب',
        ]);

        $curriculumSectionId = isset($validated['curriculum_section_id']) ? (int) $validated['curriculum_section_id'] : null;
        unset($validated['curriculum_section_id']);

        if ($curriculumSectionId && ! OfflineCourseSection::query()
            ->where('offline_course_id', $offlineCourse->id)
            ->whereKey($curriculumSectionId)
            ->exists()) {
            return back()->withErrors(['curriculum_section_id' => 'القسم المحدد غير تابع لهذا الكورس.'])->withInput();
        }

        $hasSessions = OfflineGroupSession::query()
            ->forOfflineCourse($offlineCourse, $channel)
            ->exists();
        $groupSession = $this->resolveGroupSessionFromRequest($request, $offlineCourse, $channel);
        if ($hasSessions && ! $groupSession) {
            return back()->withErrors(['offline_group_session_id' => 'يجب اختيار جلسة من التقويم (جلسات المجموعة التي أنشأتها الإدارة).'])->withInput();
        }
        if ($groupSession) {
            $this->applyGroupSessionToValidated($validated, $groupSession);
        } else {
            $validated['offline_group_session_id'] = null;
        }

        $validated['instructor_id'] = Auth::id();
        $validated['offline_course_id'] = $offlineCourse->id;
        if (! $groupSession) {
            $validated['group_id'] = $validated['group_id'] ?? null;
        }
        if ($validated['group_id'] ?? null) {
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

        $lecture = OfflineLecture::create($validated);

        if ($curriculumSectionId) {
            $this->attachLectureToCurriculumSection($offlineCourse, $lecture, $curriculumSectionId);

            return redirect()
                ->to(route('instructor.offline-courses.curriculum.index', $offlineCourse).'?channel='.urlencode($channel))
                ->with('success', 'تم إنشاء المحاضرة وربطها بالقسم في المنهج.');
        }

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

        $lecture->load(['group', 'groupSession.group']);

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

        $groups = $this->groupsForChannel($offlineCourse, $channel)->get();

        $groupSessions = OfflineGroupSession::query()
            ->forOfflineCourse($offlineCourse, $channel)
            ->with('group')
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();
        $hasGroupSessions = $groupSessions->isNotEmpty();

        $lecture->loadMissing('groupSession.group');

        return view('instructor.offline-courses.lectures.edit', compact(
            'offlineCourse',
            'lecture',
            'groups',
            'channel',
            'groupSessions',
            'hasGroupSessions'
        ));
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
            'session_agenda' => 'nullable|string|max:60000',
            'offline_attendee_mindmap' => 'nullable|string|max:65000',
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
            'offline_group_session_id' => 'nullable|integer|exists:offline_group_sessions,id',
        ]);

        $hasSessions = OfflineGroupSession::query()
            ->forOfflineCourse($offlineCourse, $channel)
            ->exists();
        $groupSession = $this->resolveGroupSessionFromRequest($request, $offlineCourse, $channel);
        if ($hasSessions && ! $groupSession) {
            return back()->withErrors(['offline_group_session_id' => 'يجب اختيار جلسة من التقويم.'])->withInput();
        }

        $lecture->title = $validated['title'];
        $lecture->description = $validated['description'] ?? null;
        $lecture->session_agenda = $validated['session_agenda'] ?? null;
        $lecture->offline_attendee_mindmap = $validated['offline_attendee_mindmap'] ?? null;
        $lecture->meeting_url = $validated['meeting_url'] ?? null;
        $lecture->recording_url = $validated['recording_url'] ?? null;
        $lecture->notes = $validated['notes'] ?? null;
        $lecture->is_active = $request->boolean('is_active');

        if ($groupSession) {
            $lecture->offline_group_session_id = $groupSession->id;
            $lecture->group_id = $groupSession->group_id;
            $dateStr = $groupSession->session_date->format('Y-m-d');
            $lecture->scheduled_at = Carbon::parse($dateStr.' '.$groupSession->start_time);
            $lecture->duration_minutes = (int) ($groupSession->duration_minutes ?: 60);
        } else {
            $lecture->offline_group_session_id = null;
            $lecture->scheduled_at = $validated['scheduled_at'] ?? null;
            $lecture->duration_minutes = $validated['duration_minutes'] ?? null;
            $lecture->group_id = $validated['group_id'] ?? null;
            if ($lecture->group_id) {
                $allowedGroupExists = $this->groupsForChannel($offlineCourse, $channel)
                    ->where('id', (int) $lecture->group_id)
                    ->exists();
                if (! $allowedGroupExists) {
                    return back()->withErrors(['group_id' => 'المجموعة المختارة غير متاحة لهذا النوع من الكورسات.'])->withInput();
                }
            }
        }

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

    private function assertGroupSessionBelongsToCourse(OfflineCourse $offlineCourse, OfflineGroupSession $session, string $channel): void
    {
        if (! OfflineGroupSession::query()->forOfflineCourse($offlineCourse, $channel)->whereKey($session->id)->exists()) {
            abort(404);
        }
    }

    private function resolveGroupSessionFromRequest(Request $request, OfflineCourse $offlineCourse, string $channel): ?OfflineGroupSession
    {
        $raw = $request->input('offline_group_session_id');
        if ($raw === null || $raw === '') {
            return null;
        }

        $id = (int) $raw;
        if ($id < 1) {
            return null;
        }

        return OfflineGroupSession::query()
            ->forOfflineCourse($offlineCourse, $channel)
            ->whereKey($id)
            ->with('group')
            ->first();
    }

    private function applyGroupSessionToValidated(array &$validated, OfflineGroupSession $session): void
    {
        $validated['offline_group_session_id'] = $session->id;
        $validated['group_id'] = $session->group_id;
        $dateStr = $session->session_date->format('Y-m-d');
        $validated['scheduled_at'] = Carbon::parse($dateStr.' '.$session->start_time)->format('Y-m-d H:i:s');
        $validated['duration_minutes'] = (int) ($session->duration_minutes ?: 60);
    }

    private function attachLectureToCurriculumSection(OfflineCourse $offlineCourse, OfflineLecture $lecture, int $sectionId): void
    {
        $section = OfflineCourseSection::query()
            ->where('offline_course_id', $offlineCourse->id)
            ->whereKey($sectionId)
            ->first();

        if (! $section) {
            return;
        }

        $exists = OfflineCurriculumItem::query()
            ->where('offline_course_section_id', $section->id)
            ->where('item_type', OfflineLecture::class)
            ->where('item_id', $lecture->id)
            ->exists();

        if ($exists) {
            return;
        }

        $lastOrder = $section->items()->max('order') ?? 0;
        OfflineCurriculumItem::create([
            'offline_course_section_id' => $section->id,
            'item_type' => OfflineLecture::class,
            'item_id' => $lecture->id,
            'order' => $lastOrder + 1,
            'is_active' => true,
        ]);
    }
}
