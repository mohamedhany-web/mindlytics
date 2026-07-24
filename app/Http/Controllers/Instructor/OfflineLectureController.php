<?php

namespace App\Http\Controllers\Instructor;

use App\Helpers\VideoHelper;
use App\Http\Controllers\Controller;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseSection;
use App\Models\OfflineCurriculumItem;
use App\Models\OfflineGroupSession;
use App\Models\OfflineLecture;
use App\Support\LectureRecordingResolver;
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
            'recording_path' => 'nullable|string|max:500',
            'recording_disk' => 'nullable|string|max:32',
            'recording_original_name' => 'nullable|string|max:255',
            'recording_mime' => 'nullable|string|max:100',
            'recording_size' => 'nullable|integer|min:0',
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

        $this->applyRecordingUploadFields($validated, $request, $offlineCourse);

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
            'recording_path' => 'nullable|string|max:500',
            'recording_disk' => 'nullable|string|max:32',
            'recording_original_name' => 'nullable|string|max:255',
            'recording_mime' => 'nullable|string|max:100',
            'recording_size' => 'nullable|integer|min:0',
            'remove_recording_file' => 'nullable|boolean',
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

        $recordingPayload = [];
        $this->applyRecordingUploadFields($recordingPayload, $request, $offlineCourse, $lecture);
        foreach ($recordingPayload as $key => $value) {
            $lecture->{$key} = $value;
        }

        $lecture->save();

        return redirect()
            ->route('instructor.offline-courses.lectures.index', ['offlineCourse' => $offlineCourse, 'channel' => $channel])
            ->with('success', 'تم تحديث المحاضرة بنجاح');
    }

    /**
     * إنشاء رابط رفع موقّع إلى Cloudflare R2 (أو تفعيل الرفع عبر السيرفر كبديل).
     */
    public function createRecordingUploadUrl(Request $request, OfflineCourse $offlineCourse)
    {
        $this->authorizeInstructor($offlineCourse);

        $maxBytes = offline_lecture_recording_max_bytes();
        $validated = $request->validate([
            'filename' => 'required|string|max:255',
            'content_type' => 'required|string|max:100',
            'size' => 'required|integer|min:1|max:'.$maxBytes,
        ]);

        $mime = strtolower($validated['content_type']);
        $allowed = ['video/mp4', 'video/webm', 'video/quicktime', 'video/x-matroska', 'application/octet-stream'];
        if (! in_array($mime, $allowed, true) && ! str_starts_with($mime, 'video/')) {
            return response()->json(['message' => 'نوع الملف غير مدعوم. ارفع فيديو (mp4/webm/mov).'], 422);
        }

        $disk = offline_lecture_recordings_disk();
        $ext = pathinfo($validated['filename'], PATHINFO_EXTENSION) ?: 'mp4';
        $ext = preg_replace('/[^a-zA-Z0-9]/', '', $ext) ?: 'mp4';
        $path = 'offline-lecture-recordings/'.$offlineCourse->id.'/'.now()->format('Y/m').'/'.uniqid('rec_', true).'.'.$ext;

        $headers = [
            'Content-Type' => $mime,
        ];

        $allowDirect = (bool) config('filesystems.offline_lecture_recording_direct_upload', false);

        if ($allowDirect) {
            try {
                $driver = Storage::disk($disk);
                if (method_exists($driver, 'temporaryUploadUrl') && $disk === 'r2') {
                    $upload = $driver->temporaryUploadUrl($path, now()->addMinutes(60), $headers);

                    return response()->json([
                        'mode' => 'direct',
                        'upload_url' => is_array($upload) ? ($upload['url'] ?? null) : $upload,
                        'headers' => is_array($upload) ? ($upload['headers'] ?? $headers) : $headers,
                        'path' => $path,
                        'disk' => $disk,
                        'public_url' => $this->publicRecordingUrl($disk, $path),
                    ]);
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return response()->json([
            'mode' => 'server',
            'upload_url' => route('instructor.offline-courses.lectures.recording-upload', $offlineCourse),
            'path' => $path,
            'disk' => $disk,
            'max_bytes' => $maxBytes,
        ]);
    }

    /**
     * رفع الفيديو عبر السيرفر ثم إلى R2/public (بديل عند تعذر الرفع المباشر).
     */
    public function uploadRecording(Request $request, OfflineCourse $offlineCourse)
    {
        $this->authorizeInstructor($offlineCourse);

        $maxKb = (int) ceil(offline_lecture_recording_max_bytes() / 1024);
        $validated = $request->validate([
            'video' => 'required|file|max:'.$maxKb,
            'path' => 'nullable|string|max:500',
        ]);

        $file = $request->file('video');
        $disk = offline_lecture_recordings_disk();
        $path = $validated['path'] ?? null;
        if (! $path) {
            $ext = $file->getClientOriginalExtension() ?: 'mp4';
            $path = 'offline-lecture-recordings/'.$offlineCourse->id.'/'.now()->format('Y/m').'/'.uniqid('rec_', true).'.'.$ext;
        }

        try {
            Storage::disk($disk)->put($path, fopen($file->getRealPath(), 'r'), [
                'visibility' => 'private',
                'ContentType' => $file->getMimeType() ?: 'video/mp4',
            ]);
        } catch (\Throwable $e) {
            report($e);
            $disk = 'public';
            Storage::disk($disk)->put($path, fopen($file->getRealPath(), 'r'));
        }

        return response()->json([
            'success' => true,
            'path' => $path,
            'disk' => $disk,
            'original_name' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'public_url' => $this->publicRecordingUrl($disk, $path),
        ]);
    }

    private function applyRecordingUploadFields(array &$validated, Request $request, OfflineCourse $offlineCourse, ?OfflineLecture $existing = null): void
    {
        $removing = $request->boolean('remove_recording_file');
        if ($removing && $existing && $existing->recording_path) {
            try {
                Storage::disk($existing->recording_disk ?: offline_lecture_recordings_disk())->delete($existing->recording_path);
            } catch (\Throwable $e) {
                report($e);
            }
            $validated['recording_path'] = null;
            $validated['recording_disk'] = null;
            $validated['recording_original_name'] = null;
            $validated['recording_mime'] = null;
            $validated['recording_size'] = null;
            if (! $request->filled('recording_url')) {
                $validated['recording_url'] = null;
            }
        }

        $path = trim((string) $request->input('recording_path', ''));
        // عند الحذف: تجاهل المسار القديم في الحقول المخفية ما لم يُرفع ملف جديد
        if ($removing && $existing && $path !== '' && $path === (string) $existing->recording_path) {
            $path = '';
        }
        if ($path !== '') {
            // استبدال ملف قديم إن وُجد
            if ($existing && $existing->recording_path && $existing->recording_path !== $path) {
                try {
                    Storage::disk($existing->recording_disk ?: offline_lecture_recordings_disk())->delete($existing->recording_path);
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            $disk = $request->input('recording_disk') ?: offline_lecture_recordings_disk();
            $validated['recording_path'] = $path;
            $validated['recording_disk'] = $disk;
            $validated['recording_original_name'] = $request->input('recording_original_name');
            $validated['recording_mime'] = $request->input('recording_mime');
            $validated['recording_size'] = $request->filled('recording_size') ? (int) $request->input('recording_size') : null;

            $public = $this->publicRecordingUrl($disk, $path);
            if ($public) {
                $validated['recording_url'] = $public;
            } elseif (! $request->filled('recording_url')) {
                // بدون CDN عام: التشغيل عبر رابط موقّع مؤقت (playbackUrl)
                $validated['recording_url'] = null;
            }
        } elseif ($request->filled('recording_url')) {
            $validated['recording_url'] = $request->input('recording_url');
        } elseif ($existing && ! $removing) {
            $validated['recording_url'] = $existing->recording_url;
        } elseif (! array_key_exists('recording_url', $validated)) {
            $validated['recording_url'] = null;
        }
    }

    private function publicRecordingUrl(string $disk, string $path): ?string
    {
        $cdnBase = rtrim((string) config('filesystems.disks.'.$disk.'.url', ''), '/');
        if ($cdnBase === '' || str_contains($cdnBase, 'r2.cloudflarestorage.com')) {
            if ($disk === 'public') {
                return asset('storage/'.ltrim($path, '/'));
            }

            return null;
        }

        return $cdnBase.'/'.ltrim($path, '/');
    }

    /**
     * مشاهدة تسجيل المحاضرة داخل المنصة (للمدرب — iframe/بوب أب).
     */
    public function watchRecording(OfflineCourse $offlineCourse, OfflineLecture $lecture)
    {
        $this->authorizeInstructor($offlineCourse);
        if ($lecture->offline_course_id !== $offlineCourse->id) {
            abort(404);
        }

        $raw = $lecture->playbackUrl() ?: ($lecture->recording_url ? trim((string) $lecture->recording_url) : '');
        if ($raw === '') {
            abort(404, 'لا يوجد تسجيل لهذه المحاضرة');
        }

        if ($lecture->hasStoredRecording()) {
            return response()
                ->view('video.protected-embed', [
                    'type' => 'html5',
                    'src' => $raw,
                    'mime' => $lecture->recording_mime ?: 'video/mp4',
                    'title' => $lecture->title ?: 'التسجيل',
                ])
                ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate')
                ->header('Pragma', 'no-cache');
        }

        $source = VideoHelper::getVideoSource($raw);
        $embed = VideoHelper::getEmbedUrl($raw) ?: $raw;

        if ($source === 'bunny') {
            $resolved = LectureRecordingResolver::resolve($raw, 'bunny');
            $embed = $resolved['recording_url'] ?: $embed;
        }

        if ($source === 'direct' || ($lecture->recording_mime && str_starts_with((string) $lecture->recording_mime, 'video/'))) {
            return response()
                ->view('video.protected-embed', [
                    'type' => 'html5',
                    'src' => $embed,
                    'mime' => $lecture->recording_mime ?: 'video/mp4',
                    'title' => $lecture->title ?: 'التسجيل',
                ])
                ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate')
                ->header('Pragma', 'no-cache');
        }

        return response()
            ->view('video.protected-embed', [
                'type' => 'iframe',
                'src' => $embed,
                'title' => $lecture->title ?: 'التسجيل',
            ])
            ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');
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
        if ($lecture->recording_path) {
            try {
                Storage::disk($lecture->recording_disk ?: offline_lecture_recordings_disk())->delete($lecture->recording_path);
            } catch (\Throwable $e) {
                report($e);
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
