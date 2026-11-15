<?php

namespace App\Domains\Sales\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\Learning\Http\Controllers\ProtectedVideoController;
use App\Domains\Learning\Models\AdvancedCourse;
use App\Domains\Learning\Models\CourseLesson;
use App\Domains\Learning\Models\Lecture;
use App\Domains\Sales\Models\SalesCourseBoardEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SalesCourseBoardController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage.courses');
    }

    public function index(Request $request): View
    {
        $query = SalesCourseBoardEntry::query()->ordered();

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('instructor_name', 'like', "%{$s}%")
                    ->orWhere('slug', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $entries = $query->paginate(20)->withQueryString();
        $total = SalesCourseBoardEntry::query()->count();
        $published = SalesCourseBoardEntry::query()->where('landing_published', true)->count();

        return view('sales::admin.course-board.index', compact('entries', 'total', 'published'));
    }

    public function create(): View
    {
        return view('sales::admin.course-board.create', [
            'entry' => new SalesCourseBoardEntry(['is_active' => true, 'landing_published' => false]),
            'courses' => $this->courseOptions(),
            'linkedCourse' => null,
            'previewCandidates' => $this->emptyPreviewCandidates(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        [$validated, $lms] = $this->validated($request);
        $entry = SalesCourseBoardEntry::query()->create($validated);
        $this->syncLinkedCourse($entry, $lms);

        return redirect()
            ->route('admin.sales-course-board.edit', $entry)
            ->with('success', 'تم إضافة الكورس إلى لوحة المبيعات.');
    }

    public function edit(SalesCourseBoardEntry $salesCourseBoard): View
    {
        $salesCourseBoard->load('advancedCourse');
        $linked = $salesCourseBoard->advancedCourse;

        return view('sales::admin.course-board.edit', [
            'entry' => $salesCourseBoard,
            'courses' => $this->courseOptions(),
            'linkedCourse' => $linked,
            'previewCandidates' => $this->previewCandidatesFor($linked),
        ]);
    }

    public function update(Request $request, SalesCourseBoardEntry $salesCourseBoard): RedirectResponse
    {
        [$validated, $lms] = $this->validated($request, $salesCourseBoard);
        $salesCourseBoard->update($validated);
        $this->syncLinkedCourse($salesCourseBoard->fresh(), $lms);

        return redirect()
            ->route('admin.sales-course-board.edit', $salesCourseBoard)
            ->with('success', 'تم تحديث بيانات الكورس وصفحة العرض.');
    }

    public function destroy(SalesCourseBoardEntry $salesCourseBoard): RedirectResponse
    {
        $salesCourseBoard->delete();

        return redirect()
            ->route('admin.sales-course-board.index')
            ->with('success', 'تم حذف الكورس من اللوحة.');
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<string, mixed>}
     */
    private function validated(Request $request, ?SalesCourseBoardEntry $entry = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:sales_course_board_entries,slug,'.($entry?->id ?? 'NULL')],
            'audience' => ['nullable', 'string', 'max:255'],
            'instructor_name' => ['nullable', 'string', 'max:255'],
            'start_label' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['nullable', 'date'],
            'schedule_days' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
            'hours' => ['nullable', 'string', 'max:255'],
            'price_online' => ['nullable', 'numeric', 'min:0'],
            'price_recorded' => ['nullable', 'numeric', 'min:0'],
            'format' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'landing_details' => ['nullable', 'string'],
            'highlights_text' => ['nullable', 'string'],
            'advanced_course_id' => ['nullable', 'integer', 'exists:advanced_courses,id'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['sometimes', 'boolean'],
            'landing_published' => ['sometimes', 'boolean'],
            'lms.title' => ['nullable', 'string', 'max:255'],
            'lms.hook' => ['nullable', 'string', 'max:500'],
            'lms.description' => ['nullable', 'string'],
            'lms.objectives' => ['nullable', 'string'],
            'lms.what_you_learn' => ['nullable', 'string'],
            'lms.suitable_for' => ['nullable', 'string'],
            'lms.age_suitability' => ['nullable', 'string'],
            'lms.instructor_info' => ['nullable', 'string'],
            'lms.available_until_info' => ['nullable', 'string'],
            'lms.follow_up_info' => ['nullable', 'string'],
            'lms.level' => ['nullable', 'in:beginner,intermediate,advanced'],
            'lms.duration_hours' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'lms.duration_minutes' => ['nullable', 'integer', 'min:0', 'max:59'],
            'lms.public_lectures_count' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'lms.preview_unlocked_count' => ['nullable', 'integer', 'min:0', 'max:10'],
            'lms.preview_lecture_ids' => ['nullable', 'array'],
            'lms.preview_lecture_ids.*' => ['integer'],
            'lms.preview_lesson_ids' => ['nullable', 'array'],
            'lms.preview_lesson_ids.*' => ['integer'],
            'lms.price' => ['nullable', 'numeric', 'min:0'],
            'lms.discount_amount' => ['nullable', 'numeric', 'min:0'],
            'lms.video_url' => ['nullable', 'url', 'max:1000'],
            'lms.is_free' => ['sometimes', 'boolean'],
        ]);

        $validated['starts_at'] = $validated['starts_at'] ?? null;
        $validated['is_active'] = $request->boolean('is_active');
        $validated['landing_published'] = $request->boolean('landing_published');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['highlights'] = $this->parseHighlights($request->input('highlights_text'));

        if (blank($validated['slug'] ?? null) && filled($validated['name'])) {
            $validated['slug'] = SalesCourseBoardEntry::generateUniqueSlug($validated['name']);
        }

        $lms = $validated['lms'] ?? [];
        $lms['is_free'] = $request->boolean('lms.is_free');
        $lms['preview_lecture_ids'] = array_values(array_unique(array_map(
            'intval',
            $request->input('lms.preview_lecture_ids', [])
        )));
        $lms['preview_lesson_ids'] = array_values(array_unique(array_map(
            'intval',
            $request->input('lms.preview_lesson_ids', [])
        )));
        $lms['public_lectures_count'] = $request->filled('lms.public_lectures_count')
            ? (int) $request->input('lms.public_lectures_count')
            : null;
        $lms['duration_minutes'] = $request->filled('lms.duration_minutes')
            ? (int) $request->input('lms.duration_minutes')
            : 0;
        $lms['preview_unlocked_count'] = $request->filled('lms.preview_unlocked_count')
            ? (int) $request->input('lms.preview_unlocked_count')
            : null;
        unset($validated['highlights_text'], $validated['lms']);

        return [$validated, $lms];
    }

    /** @param  array<string, mixed>  $lms */
    private function syncLinkedCourse(SalesCourseBoardEntry $entry, array $lms): void
    {
        if (! $entry->advanced_course_id || $lms === []) {
            return;
        }

        $course = AdvancedCourse::query()->find($entry->advanced_course_id);
        if (! $course) {
            return;
        }

        $allowedLectureIds = Lecture::query()
            ->where('course_id', $course->id)
            ->whereNotNull('recording_url')
            ->where('recording_url', '!=', '')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
        $allowedLessonIds = CourseLesson::query()
            ->where('advanced_course_id', $course->id)
            ->where('is_active', true)
            ->where('type', 'video')
            ->whereNotNull('video_url')
            ->where('video_url', '!=', '')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $previewLectureIds = array_values(array_intersect(
            array_map('intval', $lms['preview_lecture_ids'] ?? []),
            $allowedLectureIds
        ));
        $previewLessonIds = array_values(array_intersect(
            array_map('intval', $lms['preview_lesson_ids'] ?? []),
            $allowedLessonIds
        ));

        $payload = [
            'title' => $lms['title'] ?? $course->title,
            'hook' => $lms['hook'] ?? null,
            'description' => $lms['description'] ?? null,
            'objectives' => $lms['objectives'] ?? null,
            'what_you_learn' => $lms['what_you_learn'] ?? null,
            'suitable_for' => $lms['suitable_for'] ?? null,
            'age_suitability' => $lms['age_suitability'] ?? null,
            'instructor_info' => $lms['instructor_info'] ?? null,
            'available_until_info' => $lms['available_until_info'] ?? null,
            'follow_up_info' => $lms['follow_up_info'] ?? null,
            'level' => $lms['level'] ?? $course->level,
            'duration_hours' => $lms['duration_hours'] ?? $course->duration_hours,
            'duration_minutes' => $lms['duration_minutes'] ?? $course->duration_minutes,
            'public_lectures_count' => array_key_exists('public_lectures_count', $lms)
                ? $lms['public_lectures_count']
                : $course->public_lectures_count,
            'preview_unlocked_count' => array_key_exists('preview_unlocked_count', $lms)
                ? $lms['preview_unlocked_count']
                : $course->preview_unlocked_count,
            'preview_lecture_ids' => $previewLectureIds,
            'preview_lesson_ids' => $previewLessonIds,
            'price' => $lms['price'] ?? $course->price,
            'discount_amount' => $lms['discount_amount'] ?? $course->discount_amount,
            'video_url' => $lms['video_url'] ?? $course->video_url,
            'is_free' => (bool) ($lms['is_free'] ?? false),
        ];

        $course->fill($payload)->save();
        ProtectedVideoController::flushPreviewCache((int) $course->id);
    }

    /** @return array{lectures: Collection<int, Lecture>, lessons: Collection<int, CourseLesson>} */
    private function emptyPreviewCandidates(): array
    {
        return [
            'lectures' => collect(),
            'lessons' => collect(),
        ];
    }

    /** @return array{lectures: Collection<int, Lecture>, lessons: Collection<int, CourseLesson>} */
    private function previewCandidatesFor(?AdvancedCourse $course): array
    {
        if (! $course) {
            return $this->emptyPreviewCandidates();
        }

        return [
            'lectures' => Lecture::query()
                ->where('course_id', $course->id)
                ->whereNotNull('recording_url')
                ->where('recording_url', '!=', '')
                ->orderByRaw('CASE WHEN scheduled_at IS NULL THEN 1 ELSE 0 END')
                ->orderBy('scheduled_at')
                ->orderBy('id')
                ->get(['id', 'title', 'duration_minutes', 'scheduled_at']),
            'lessons' => CourseLesson::query()
                ->where('advanced_course_id', $course->id)
                ->where('is_active', true)
                ->where('type', 'video')
                ->whereNotNull('video_url')
                ->where('video_url', '!=', '')
                ->orderBy('order')
                ->orderBy('id')
                ->get(['id', 'title', 'duration_minutes', 'order']),
        ];
    }

    /** @return list<string> */
    private function parseHighlights(?string $text): array
    {
        if (blank($text)) {
            return [];
        }

        return collect(preg_split('/\r\n|\r|\n/', $text) ?: [])
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->values()
            ->all();
    }

    /** @return \Illuminate\Support\Collection<int, AdvancedCourse> */
    private function courseOptions()
    {
        return AdvancedCourse::query()
            ->where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title']);
    }
}
