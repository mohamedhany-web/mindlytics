<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\SalesCourseBoardEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        return view('admin.sales-course-board.index', compact('entries', 'total', 'published'));
    }

    public function create(): View
    {
        return view('admin.sales-course-board.create', [
            'entry' => new SalesCourseBoardEntry(['is_active' => true, 'landing_published' => false]),
            'courses' => $this->courseOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $entry = SalesCourseBoardEntry::query()->create($validated);

        return redirect()
            ->route('admin.sales-course-board.edit', $entry)
            ->with('success', 'تم إضافة الكورس إلى لوحة المبيعات.');
    }

    public function edit(SalesCourseBoardEntry $salesCourseBoard): View
    {
        return view('admin.sales-course-board.edit', [
            'entry' => $salesCourseBoard,
            'courses' => $this->courseOptions(),
        ]);
    }

    public function update(Request $request, SalesCourseBoardEntry $salesCourseBoard): RedirectResponse
    {
        $validated = $this->validated($request, $salesCourseBoard);
        $salesCourseBoard->update($validated);

        return redirect()
            ->route('admin.sales-course-board.edit', $salesCourseBoard)
            ->with('success', 'تم تحديث بيانات الكورس.');
    }

    public function destroy(SalesCourseBoardEntry $salesCourseBoard): RedirectResponse
    {
        $salesCourseBoard->delete();

        return redirect()
            ->route('admin.sales-course-board.index')
            ->with('success', 'تم حذف الكورس من اللوحة.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?SalesCourseBoardEntry $entry = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:sales_course_board_entries,slug,'.($entry?->id ?? 'NULL')],
            'audience' => ['nullable', 'string', 'max:255'],
            'instructor_name' => ['nullable', 'string', 'max:255'],
            'start_label' => ['nullable', 'string', 'max:255'],
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
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['landing_published'] = $request->boolean('landing_published');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['highlights'] = $this->parseHighlights($request->input('highlights_text'));

        if (blank($validated['slug'] ?? null) && filled($validated['name'])) {
            $validated['slug'] = SalesCourseBoardEntry::generateUniqueSlug($validated['name']);
        }

        unset($validated['highlights_text']);

        return $validated;
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
