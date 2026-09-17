<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SalesDiplomaBoardEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesDiplomaBoardController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage.courses');
    }

    public function index(Request $request): View
    {
        $query = SalesDiplomaBoardEntry::query()->ordered();

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

        $entries = $query->withCount('visits')->paginate(20)->withQueryString();
        $total = SalesDiplomaBoardEntry::query()->count();
        $published = SalesDiplomaBoardEntry::query()->where('landing_published', true)->count();

        return view('admin.sales-diploma-board.index', compact('entries', 'total', 'published'));
    }

    public function create(): View
    {
        return view('admin.sales-diploma-board.create', [
            'entry' => new SalesDiplomaBoardEntry(['is_active' => true, 'landing_published' => false]),
            'paths' => $this->pathOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $entry = SalesDiplomaBoardEntry::query()->create($validated);

        return redirect()
            ->route('admin.sales-diploma-board.edit', $entry)
            ->with('success', 'تم إضافة توصيف الدبلومة.');
    }

    public function edit(SalesDiplomaBoardEntry $salesDiplomaBoard): View
    {
        $salesDiplomaBoard->loadCount('visits');
        $recentVisits = $salesDiplomaBoard->visits()
            ->with('user:id,name,email')
            ->latest('created_at')
            ->limit(50)
            ->get();

        return view('admin.sales-diploma-board.edit', [
            'entry' => $salesDiplomaBoard,
            'paths' => $this->pathOptions(),
            'recentVisits' => $recentVisits,
        ]);
    }

    public function update(Request $request, SalesDiplomaBoardEntry $salesDiplomaBoard): RedirectResponse
    {
        $validated = $this->validated($request, $salesDiplomaBoard);
        $salesDiplomaBoard->update($validated);

        return redirect()
            ->route('admin.sales-diploma-board.edit', $salesDiplomaBoard)
            ->with('success', 'تم تحديث توصيف الدبلومة.');
    }

    public function destroy(SalesDiplomaBoardEntry $salesDiplomaBoard): RedirectResponse
    {
        $salesDiplomaBoard->delete();

        return redirect()
            ->route('admin.sales-diploma-board.index')
            ->with('success', 'تم حذف توصيف الدبلومة.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?SalesDiplomaBoardEntry $entry = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:sales_diploma_board_entries,slug,'.($entry?->id ?? 'NULL')],
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
            'booking_methods_text' => ['nullable', 'string'],
            'free_lecture_links_text' => ['nullable', 'string'],
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['sometimes', 'boolean'],
            'landing_published' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['landing_published'] = $request->boolean('landing_published');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['highlights'] = $this->parseLines($request->input('highlights_text'));
        $validated['booking_methods'] = $this->parseLines($request->input('booking_methods_text'));
        $validated['free_lecture_links'] = $this->parseFreeLectureLinks($request->input('free_lecture_links_text'));
        $validated['starts_at'] = $validated['starts_at'] ?? null;
        $validated['academic_year_id'] = $validated['academic_year_id'] ?? null;

        if (blank($validated['slug'] ?? null) && filled($validated['name'])) {
            $validated['slug'] = SalesDiplomaBoardEntry::generateUniqueSlug($validated['name']);
        }

        unset(
            $validated['highlights_text'],
            $validated['booking_methods_text'],
            $validated['free_lecture_links_text']
        );

        return $validated;
    }

    /** @return list<string> */
    private function parseLines(?string $text): array
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

    /**
     * Lines: "Title | https://example.com" or bare URL.
     *
     * @return list<array{title: string, url: string}>
     */
    private function parseFreeLectureLinks(?string $text): array
    {
        $lines = $this->parseLines($text);
        $out = [];

        foreach ($lines as $line) {
            if (str_contains($line, '|')) {
                [$title, $url] = array_map('trim', explode('|', $line, 2));
            } else {
                $title = '';
                $url = trim($line);
            }

            if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
                continue;
            }

            $out[] = [
                'title' => $title !== '' ? $title : $url,
                'url' => $url,
            ];
        }

        return $out;
    }

    /** @return \Illuminate\Support\Collection<int, AcademicYear> */
    private function pathOptions()
    {
        return AcademicYear::query()
            ->orderBy('name')
            ->get(['id', 'name', 'code']);
    }
}
