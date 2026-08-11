<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\InternshipApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InternshipController extends Controller
{
    public function index(Request $request): View
    {
        $query = Internship::query()->withCount('applications')->latest();

        if ($request->filled('status') && array_key_exists($request->status, Internship::statuses())) {
            $query->where('status', $request->status);
        }
        if ($request->filled('published')) {
            $query->where('is_published', $request->published === '1');
        }
        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('department', 'like', "%{$s}%")
                    ->orWhere('location', 'like', "%{$s}%");
            });
        }

        $internships = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Internship::count(),
            'open' => Internship::where('status', Internship::STATUS_OPEN)->where('is_published', true)->count(),
            'draft' => Internship::where('status', Internship::STATUS_DRAFT)->count(),
            'applications' => InternshipApplication::count(),
            'pending' => InternshipApplication::where('status', InternshipApplication::STATUS_PENDING)->count(),
        ];

        return view('admin.internships.index', compact('internships', 'stats'));
    }

    public function create(): View
    {
        return view('admin.internships.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = ! empty($data['slug'])
            ? Internship::generateSlug($data['slug'])
            : Internship::generateSlug($data['title']);
        $data['created_by'] = auth()->id();
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_published'] = $request->boolean('is_published') && ($data['status'] ?? '') === Internship::STATUS_OPEN;
        $data['published_at'] = $data['is_published'] ? now() : null;

        $internship = Internship::create($data);

        return redirect()
            ->route('admin.internships.edit', $internship)
            ->with('success', 'تم إنشاء فرصة التدريب بنجاح.');
    }

    public function edit(Internship $internship): View
    {
        $internship->loadCount('applications');

        return view('admin.internships.form', compact('internship'));
    }

    public function update(Request $request, Internship $internship): RedirectResponse
    {
        $data = $this->validated($request, $internship->id);
        $data['slug'] = ! empty($data['slug'])
            ? Internship::generateSlug($data['slug'], $internship->id)
            : Internship::generateSlug($data['title'], $internship->id);
        $data['is_featured'] = $request->boolean('is_featured');
        $wantPublish = $request->boolean('is_published') && ($data['status'] ?? '') === Internship::STATUS_OPEN;
        $data['is_published'] = $wantPublish;
        $data['published_at'] = $wantPublish
            ? ($internship->published_at ?: now())
            : null;

        $internship->update($data);

        return back()->with('success', 'تم حفظ تعديلات فرصة التدريب.');
    }

    public function destroy(Internship $internship): RedirectResponse
    {
        $internship->delete();

        return redirect()
            ->route('admin.internships.index')
            ->with('success', 'تم حذف فرصة التدريب.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:120',
            'summary' => 'nullable|string|max:1000',
            'description' => 'nullable|string|max:20000',
            'requirements' => 'nullable|string|max:10000',
            'benefits' => 'nullable|string|max:10000',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:onsite,remote,hybrid',
            'duration' => 'nullable|string|max:120',
            'seats' => 'nullable|integer|min:1|max:10000',
            'status' => 'required|in:draft,open,closed',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'application_deadline' => 'nullable|date',
            'sort_order' => 'nullable|integer|min:0|max:9999',
        ], [
            'title.required' => 'عنوان فرصة التدريب مطلوب.',
            'type.in' => 'نوع التدريب غير صالح.',
            'status.in' => 'حالة الفرصة غير صالحة.',
            'ends_at.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية.',
        ]);
    }
}
