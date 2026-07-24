<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\CourseLesson;
use App\Models\Lecture;
use App\Models\LectureMaterial;
use App\Models\User;
use Illuminate\Http\Request;

class LectureController extends Controller
{
    /**
     * عرض قائمة الكورسات (الدخول للكورس يعرض محاضراته).
     */
    public function index()
    {
        $courses = AdvancedCourse::where('is_active', true)
            ->withCount('lectures')
            ->orderBy('title')
            ->get();

        return view('admin.lectures.index', compact('courses'));
    }

    /**
     * عرض محاضرات كورس معين مع روابط CRUD.
     */
    public function indexByCourse(AdvancedCourse $course)
    {
        $course->loadCount('lectures');
        $lectures = $course->lectures()
            ->with('instructor')
            ->orderBy('scheduled_at', 'desc')
            ->paginate(20);

        return view('admin.lectures.by-course', compact('course', 'lectures'));
    }

    public function create(Request $request)
    {
        $courses = AdvancedCourse::where('is_active', true)->get();
        $instructors = User::where('role', 'instructor')->where('is_active', true)->get();
        $preselectedCourseId = $request->query('course_id');

        return view('admin.lectures.create', compact('courses', 'instructors', 'preselectedCourseId'));
    }

    public function store(Request $request)
    {
        $this->normalizeWatchPercent($request);
        $this->normalizeVideoPlatform($request);

        if ($this->isAjaxRequest($request)) {
            return $this->storeFromCurriculum($request);
        }

        $validated = $request->validate([
            'course_id' => 'required|exists:advanced_courses,id',
            'instructor_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teams_registration_link' => 'nullable|url',
            'teams_meeting_link' => 'nullable|url',
            'recording_url' => 'nullable|url',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:1',
            'min_watch_percent_to_unlock_next' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
            'has_attendance_tracking' => 'boolean',
            'has_assignment' => 'boolean',
            'has_evaluation' => 'boolean',
        ]);

        $validated['min_watch_percent_to_unlock_next'] = $this->parseWatchPercent($request);
        $lecture = Lecture::create($validated);

        return redirect()->route('admin.lectures.by-course', $lecture->course_id)
            ->with('success', 'تم إنشاء المحاضرة بنجاح');
    }

    public function show(Lecture $lecture)
    {
        if ($this->isAjaxRequest(request())) {
            $lecture->refresh();
            $videoPlatform = $lecture->video_platform ? strtolower(trim($lecture->video_platform)) : '';

            return response()->json([
                'id' => $lecture->id,
                'title' => $lecture->title,
                'description' => $lecture->description,
                'course_id' => $lecture->course_id,
                'course_lesson_id' => $lecture->course_lesson_id,
                'scheduled_at' => $lecture->scheduled_at ? $lecture->scheduled_at->toIso8601String() : null,
                'duration_minutes' => $lecture->duration_minutes,
                'min_watch_percent_to_unlock_next' => $lecture->min_watch_percent_to_unlock_next,
                'recording_url' => $lecture->recording_url ?? '',
                'video_platform' => $videoPlatform,
                'teams_registration_link' => $lecture->teams_registration_link ?? '',
                'teams_meeting_link' => $lecture->teams_meeting_link ?? '',
                'notes' => $lecture->notes ?? '',
                'has_attendance_tracking' => $lecture->has_attendance_tracking ?? false,
                'has_assignment' => $lecture->has_assignment ?? false,
                'has_evaluation' => $lecture->has_evaluation ?? false,
                'status' => $lecture->status ?? 'scheduled',
            ]);
        }

        $lecture->load([
            'course', 'instructor', 'lesson',
            'materials', 'assignments', 'attendanceRecords', 'evaluations',
        ]);

        return view('admin.lectures.show', compact('lecture'));
    }

    public function edit(Lecture $lecture)
    {
        $courses = AdvancedCourse::where('is_active', true)->get();
        $instructors = User::where('role', 'instructor')->where('is_active', true)->get();
        $lessons = CourseLesson::where('advanced_course_id', $lecture->course_id)->orderBy('order')->get();

        return view('admin.lectures.edit', compact('lecture', 'courses', 'instructors', 'lessons'));
    }

    public function update(Request $request, Lecture $lecture)
    {
        $this->normalizeWatchPercent($request);
        $this->normalizeVideoPlatform($request);

        if ($this->isAjaxRequest($request)) {
            return $this->updateFromCurriculum($request, $lecture);
        }

        $validated = $request->validate([
            'course_id' => 'required|exists:advanced_courses,id',
            'instructor_id' => 'required|exists:users,id',
            'course_lesson_id' => 'nullable|exists:course_lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'recording_url' => 'nullable|url',
            'video_platform' => 'nullable|string|max:50',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:1',
            'min_watch_percent_to_unlock_next' => 'nullable|integer|min:0|max:100',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'has_attendance_tracking' => 'boolean',
            'has_assignment' => 'boolean',
            'has_evaluation' => 'boolean',
        ]);

        $validated['course_lesson_id'] = $validated['course_lesson_id'] ?? null;
        $validated['min_watch_percent_to_unlock_next'] = $this->parseWatchPercent($request);
        $lecture->update($validated);

        return redirect()->route('admin.lectures.by-course', $lecture->course_id)
            ->with('success', 'تم تحديث المحاضرة بنجاح');
    }

    public function destroy(Lecture $lecture)
    {
        $courseId = $lecture->course_id;
        $lecture->delete();

        if ($this->isAjaxRequest(request())) {
            return response()->json([
                'success' => true,
                'message' => 'تم حذف المحاضرة بنجاح',
            ]);
        }

        return redirect()->route('admin.lectures.by-course', $courseId)
            ->with('success', 'تم حذف المحاضرة بنجاح');
    }

    private function storeFromCurriculum(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:advanced_courses,id',
            'course_lesson_id' => 'nullable|exists:course_lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'min_watch_percent_to_unlock_next' => 'nullable|integer|min:0|max:100',
            'teams_registration_link' => 'nullable|url',
            'teams_meeting_link' => 'nullable|url',
            'recording_url' => 'nullable|string|max:2000',
            'video_platform' => 'nullable|in:youtube,vimeo,google_drive,direct,bunny',
            'notes' => 'nullable|string',
            'has_attendance_tracking' => 'boolean',
            'has_assignment' => 'boolean',
            'has_evaluation' => 'boolean',
            'material_files' => 'nullable|array',
            'material_files.*' => 'nullable|file|max:20480',
            'material_titles' => 'nullable|array',
            'material_titles.*' => 'nullable|string|max:255',
            'material_visible' => 'nullable|array',
            'material_visible.*' => 'in:0,1',
        ]);

        $course = AdvancedCourse::findOrFail($validated['course_id']);
        $validated['instructor_id'] = $course->instructor_id ?: auth()->id();
        $validated['status'] = 'scheduled';
        $validated['has_attendance_tracking'] = $request->boolean('has_attendance_tracking');
        $validated['has_assignment'] = $request->boolean('has_assignment');
        $validated['has_evaluation'] = $request->boolean('has_evaluation');
        $validated['min_watch_percent_to_unlock_next'] = $this->parseWatchPercent($request);
        $validated['recording_url'] = $this->normalizeRecordingUrl($request->input('recording_url'));
        $validated['video_platform'] = $this->detectPlatform($validated['recording_url'], $validated['video_platform'] ?? null);

        $lecture = Lecture::create($validated);
        $this->storeMaterials($request, $lecture);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء المحاضرة بنجاح',
            'lecture' => $lecture->fresh(),
        ]);
    }

    private function updateFromCurriculum(Request $request, Lecture $lecture)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:advanced_courses,id',
            'course_lesson_id' => 'nullable|exists:course_lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'min_watch_percent_to_unlock_next' => 'nullable|integer|min:0|max:100',
            'teams_registration_link' => 'nullable|url',
            'teams_meeting_link' => 'nullable|url',
            'recording_url' => 'nullable|string|max:2000',
            'video_platform' => 'nullable|in:youtube,vimeo,google_drive,direct,bunny',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:scheduled,in_progress,completed,cancelled',
            'has_attendance_tracking' => 'boolean',
            'has_assignment' => 'boolean',
            'has_evaluation' => 'boolean',
            'material_files' => 'nullable|array',
            'material_files.*' => 'nullable|file|max:20480',
            'material_titles' => 'nullable|array',
            'material_titles.*' => 'nullable|string|max:255',
            'material_visible' => 'nullable|array',
            'material_visible.*' => 'in:0,1',
        ]);

        $validated['has_attendance_tracking'] = $request->boolean('has_attendance_tracking');
        $validated['has_assignment'] = $request->boolean('has_assignment');
        $validated['has_evaluation'] = $request->boolean('has_evaluation');
        $validated['status'] = $validated['status'] ?? $lecture->status;
        $validated['min_watch_percent_to_unlock_next'] = $this->parseWatchPercent($request);
        $validated['recording_url'] = $this->normalizeRecordingUrl($request->input('recording_url'));
        $validated['video_platform'] = $this->detectPlatform($validated['recording_url'], $validated['video_platform'] ?? null);

        $lecture->update($validated);
        $this->storeMaterials($request, $lecture);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المحاضرة بنجاح',
            'lecture' => $lecture->fresh(),
        ]);
    }

    private function storeMaterials(Request $request, Lecture $lecture): void
    {
        $materialFiles = $request->file('material_files');
        if (! $materialFiles || ! is_array($materialFiles)) {
            return;
        }

        $titles = $request->input('material_titles', []);
        $visible = $request->input('material_visible', []);
        $sortOrder = (int) $lecture->materials()->max('sort_order');

        foreach ($materialFiles as $index => $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }
            $path = $file->store('lecture-materials/' . $lecture->id, 'public');
            if (! $path) {
                continue;
            }
            $visibleVal = $visible[$index] ?? $visible[2 * $index + 1] ?? $visible[2 * $index] ?? 1;
            LectureMaterial::create([
                'lecture_id' => $lecture->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'title' => $titles[$index] ?? null,
                'is_visible_to_student' => (int) $visibleVal === 1,
                'sort_order' => ++$sortOrder,
            ]);
        }
    }

    private function isAjaxRequest(Request $request): bool
    {
        return $request->ajax() || $request->wantsJson() || $request->expectsJson();
    }

    private function normalizeWatchPercent(Request $request): void
    {
        $minWatch = $request->input('min_watch_percent_to_unlock_next');
        if ($minWatch === '' || (is_string($minWatch) && trim($minWatch) === '')) {
            $request->merge(['min_watch_percent_to_unlock_next' => null]);
        }
    }

    private function normalizeVideoPlatform(Request $request): void
    {
        if ($request->filled('video_platform')) {
            $request->merge(['video_platform' => strtolower(trim($request->input('video_platform')))]);
        }
    }

    private function parseWatchPercent(Request $request): ?int
    {
        $rawMin = $request->input('min_watch_percent_to_unlock_next');
        if ($rawMin === null || $rawMin === '' || ! is_numeric($rawMin)) {
            return null;
        }

        return (int) min(100, max(0, (float) $rawMin));
    }

    private function normalizeRecordingUrl($recordingUrl): ?string
    {
        if ($recordingUrl === null || trim((string) $recordingUrl) === '') {
            return null;
        }

        return trim((string) $recordingUrl);
    }

    private function detectPlatform(?string $recordingUrl, ?string $platform): ?string
    {
        if ($platform) {
            return $platform;
        }
        if (! $recordingUrl) {
            return null;
        }
        if (str_contains($recordingUrl, 'youtube.com') || str_contains($recordingUrl, 'youtu.be')) {
            return 'youtube';
        }
        if (str_contains($recordingUrl, 'vimeo.com')) {
            return 'vimeo';
        }
        if (str_contains($recordingUrl, 'drive.google.com')) {
            return 'google_drive';
        }
        if (str_contains($recordingUrl, 'mediadelivery.net')) {
            return 'bunny';
        }
        if (preg_match('/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i', $recordingUrl)) {
            return 'direct';
        }

        return null;
    }
}
