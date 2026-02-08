<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\PortfolioProject;
use Illuminate\Http\Request;

class PortfolioReviewController extends Controller
{
    public function index(Request $request)
    {
        $pathIds = auth()->user()->teachingLearningPaths()->pluck('academic_years.id');
        $query = PortfolioProject::with(['user:id,name,profile_image', 'academicYear:id,name', 'advancedCourse:id,title'])
            ->whereIn('academic_year_id', $pathIds);

        $status = $request->get('status');
        if ($status && in_array($status, ['pending_review', 'approved', 'rejected', 'published'])) {
            $query->where('status', $status);
        } else {
            $query->whereIn('status', ['pending_review', 'approved']);
        }

        $projects = $query->latest()->paginate(15);
        return view('instructor.portfolio.index', compact('projects', 'status'));
    }

    public function show(PortfolioProject $project)
    {
        $pathIds = auth()->user()->teachingLearningPaths()->pluck('academic_years.id');
        if (!$pathIds->contains($project->academic_year_id)) {
            abort(403);
        }
        $project->load(['user', 'academicYear', 'advancedCourse']);
        return view('instructor.portfolio.show', compact('project'));
    }

    public function approve(Request $request, PortfolioProject $project)
    {
        $pathIds = auth()->user()->teachingLearningPaths()->pluck('academic_years.id');
        if (!$pathIds->contains($project->academic_year_id)) {
            abort(403);
        }
        if ($project->status !== PortfolioProject::STATUS_PENDING_REVIEW) {
            return back()->with('error', 'المشروع تمت مراجعته مسبقاً.');
        }
        $project->update([
            'status' => PortfolioProject::STATUS_APPROVED,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'instructor_notes' => $request->instructor_notes,
            'rejected_reason' => null,
        ]);
        return back()->with('success', 'تم اعتماد المشروع. يمكنك نشره في البورتفوليو عند الاستعداد.');
    }

    public function reject(Request $request, PortfolioProject $project)
    {
        $pathIds = auth()->user()->teachingLearningPaths()->pluck('academic_years.id');
        if (!$pathIds->contains($project->academic_year_id)) {
            abort(403);
        }
        if ($project->status !== PortfolioProject::STATUS_PENDING_REVIEW) {
            return back()->with('error', 'المشروع تمت مراجعته مسبقاً.');
        }
        $request->validate(['rejected_reason' => 'nullable|string|max:500']);
        $project->update([
            'status' => PortfolioProject::STATUS_REJECTED,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejected_reason' => $request->rejected_reason,
        ]);
        return back()->with('success', 'تم رفض المشروع.');
    }

    public function publish(PortfolioProject $project)
    {
        $pathIds = auth()->user()->teachingLearningPaths()->pluck('academic_years.id');
        if (!$pathIds->contains($project->academic_year_id)) {
            abort(403);
        }
        if ($project->status !== PortfolioProject::STATUS_APPROVED) {
            return back()->with('error', 'يجب اعتماد المشروع أولاً قبل النشر.');
        }
        $project->update([
            'status' => PortfolioProject::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
        return back()->with('success', 'تم نشر المشروع في البورتفوليو.');
    }
}
