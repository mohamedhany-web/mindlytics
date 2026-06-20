<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\HrJobApplication;
use App\Models\HrJobPosting;
use App\Services\Hr\AtsScoringService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function __construct(
        private readonly AtsScoringService $scoringService,
    ) {}

    public function index(Request $request): View
    {
        $jobsQuery = HrJobPosting::query()
            ->with(['applications' => function ($q) use ($request) {
                $this->applyApplicationFilters($q, $request);
                $q->with(['cvFile', 'skills'])
                    ->orderByDesc('auto_score')
                    ->orderByDesc('submitted_at')
                    ->orderByDesc('id');
            }])
            ->withCount(['applications as applications_count' => function ($q) use ($request) {
                $this->applyApplicationFilters($q, $request);
            }]);

        if ($request->filled('job_id')) {
            $jobsQuery->where('id', (int) $request->job_id);
        } else {
            $jobsQuery->whereHas('applications', fn ($qq) => $this->applyApplicationFilters($qq, $request));
        }

        $jobs = $jobsQuery
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $allJobs = HrJobPosting::query()->orderByDesc('updated_at')->get(['id', 'title']);
        $educationLevels = config('hr.education_levels', []);

        $stats = [
            'total' => HrJobApplication::count(),
            'applied' => HrJobApplication::where('status', 'applied')->count(),
            'interview' => HrJobApplication::where('status', 'interview')->count(),
            'accepted' => HrJobApplication::where('status', 'accepted')->count(),
        ];

        return view('admin.hr.applications.index', compact('jobs', 'allJobs', 'stats', 'educationLevels'));
    }

    public function show(HrJobApplication $application): View
    {
        $application->load(['job', 'files', 'skills']);

        return view('admin.hr.applications.show', compact('application'));
    }

    public function updateStatus(Request $request, HrJobApplication $application): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:'.implode(',', array_keys(HrJobApplication::STATUSES)),
        ]);

        $application->update(['status' => $validated['status']]);

        return back()->with('success', 'تم تحديث الحالة.');
    }

    public function rescore(HrJobApplication $application): RedirectResponse
    {
        try {
            $this->scoringService->processApplication($application);
        } catch (\Throwable $e) {
            return back()->withErrors(['score' => 'تعذّر إعادة حساب التقييم: '.$e->getMessage()]);
        }

        return back()->with('success', 'تم إعادة حساب التقييم تلقائياً.');
    }

    public function destroy(HrJobApplication $application): RedirectResponse
    {
        $application->load('files');

        foreach ($application->files as $file) {
            try {
                Storage::disk($file->disk)->delete($file->path);
            } catch (\Throwable) {
                // ignore
            }
        }

        $application->delete();

        return redirect()->route('admin.hr.applications.index')->with('success', 'تم حذف التقديم.');
    }

    private function applyApplicationFilters(Builder $q, Request $request): void
    {
        if ($request->filled('status') && array_key_exists($request->status, HrJobApplication::STATUSES)) {
            $q->where('status', $request->status);
        }

        if ($request->filled('min_score')) {
            $q->where('auto_score', '>=', (float) $request->min_score);
        }

        if ($request->filled('min_experience')) {
            $q->where('parsed_experience_years', '>=', (float) $request->min_experience);
        }

        if ($request->filled('education')) {
            $q->where('parsed_education', $request->education);
        }

        if ($request->filled('skill')) {
            $skill = trim((string) $request->skill);
            $q->where(function ($qq) use ($skill) {
                $qq->whereHas('skills', fn ($s) => $s->where('skill_name', 'like', "%{$skill}%"));
            });
        }

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $q->where(function ($qq) use ($s) {
                $qq->where('full_name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }
    }
}
