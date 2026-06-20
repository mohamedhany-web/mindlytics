<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\HrJobApplication;
use App\Models\HrJobPosting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobPostingController extends Controller
{
    public function index(Request $request): View
    {
        $q = HrJobPosting::query()->withCount('applications')->orderByDesc('updated_at');

        if ($request->filled('published')) {
            $published = $request->published === '1';
            $q->where('is_published', $published);
        }

        if ($request->filled('status') && array_key_exists($request->status, HrJobPosting::STATUSES)) {
            $q->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $q->where(function ($qq) use ($s) {
                $qq->where('title', 'like', "%{$s}%")
                    ->orWhere('department', 'like', "%{$s}%")
                    ->orWhere('location', 'like', "%{$s}%");
            });
        }

        $jobs = $q->paginate(20)->withQueryString();

        $stats = [
            'total' => HrJobPosting::count(),
            'open' => HrJobPosting::open()->count(),
            'published' => HrJobPosting::where('is_published', true)->count(),
            'applications' => HrJobApplication::count(),
        ];

        return view('admin.hr.jobs.index', compact('jobs', 'stats'));
    }

    public function show(HrJobPosting $job): RedirectResponse
    {
        return redirect()->route('admin.hr.jobs.edit', $job);
    }

    public function create(): View
    {
        return view('admin.hr.jobs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateJob($request);

        $job = HrJobPosting::create([
            ...$validated,
            'is_published' => $request->boolean('is_published') && ($validated['status'] ?? 'open') === 'open',
            'published_at' => $request->boolean('is_published') && ($validated['status'] ?? 'open') === 'open' ? now() : null,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.hr.jobs.edit', $job)->with('success', 'تم إنشاء الوظيفة.');
    }

    public function edit(HrJobPosting $job): View
    {
        $job->loadCount('applications');

        return view('admin.hr.jobs.edit', compact('job'));
    }

    public function update(Request $request, HrJobPosting $job): RedirectResponse
    {
        $validated = $this->validateJob($request);

        $wasPublished = (bool) $job->is_published;
        $status = $validated['status'] ?? 'open';
        $nowPublished = $request->boolean('is_published') && $status === 'open';

        $job->update([
            ...$validated,
            'is_published' => $nowPublished,
            'published_at' => $nowPublished ? ($job->published_at ?: now()) : ($wasPublished ? null : $job->published_at),
        ]);

        return back()->with('success', 'تم حفظ التعديلات.');
    }

    public function destroy(HrJobPosting $job): RedirectResponse
    {
        $job->delete();

        return redirect()->route('admin.hr.jobs.index')->with('success', 'تم حذف الوظيفة.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateJob(Request $request): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'nullable|string|max:120',
            'location' => 'nullable|string|max:120',
            'employment_type' => 'nullable|string|max:60',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'required_skills' => 'nullable|string|max:5000',
            'required_experience' => 'nullable|integer|min:0|max:50',
            'required_education' => 'nullable|string|in:'.implode(',', array_keys(config('hr.education_levels', []))),
            'status' => 'required|string|in:'.implode(',', array_keys(HrJobPosting::STATUSES)),
            'is_published' => 'nullable|boolean',
        ]);

        $skills = array_values(array_unique(array_filter(array_map(
            'trim',
            preg_split('/[,،\n]+/', (string) ($validated['required_skills'] ?? '')) ?: []
        ))));

        $validated['required_skills'] = $skills;
        unset($validated['is_published']);

        return $validated;
    }
}
