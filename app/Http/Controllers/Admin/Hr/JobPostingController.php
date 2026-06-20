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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'nullable|string|max:120',
            'location' => 'nullable|string|max:120',
            'employment_type' => 'nullable|string|max:60',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);

        $job = HrJobPosting::create([
            ...$validated,
            'is_published' => $request->boolean('is_published'),
            'published_at' => $request->boolean('is_published') ? now() : null,
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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'nullable|string|max:120',
            'location' => 'nullable|string|max:120',
            'employment_type' => 'nullable|string|max:60',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);

        $wasPublished = (bool) $job->is_published;
        $nowPublished = $request->boolean('is_published');

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
}

