<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioProject;
use App\Services\JourneyAchievementService;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function __construct(private JourneyAchievementService $achievements)
    {
    }

    /**
     * عرض كل مشاريع البورتفوليو (رقابة الجودة)
     */
    public function index(Request $request)
    {
        $query = PortfolioProject::with([
            'user:id,name,profile_image,email',
            'academicYear:id,name',
            'advancedCourse:id,title',
            'offlineCourse:id,title',
            'reviewer:id,name',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('visible')) {
            if ($request->visible === '1') {
                $query->where('is_visible', true);
            } else {
                $query->where('is_visible', false);
            }
        }
        if ($request->filled('featured')) {
            $query->where('is_featured', $request->featured === '1');
        }

        $projects = $query->latest()->paginate(20);

        return view('admin.portfolio.index', compact('projects'));
    }

    public function show(PortfolioProject $project)
    {
        $project->load(['user', 'academicYear', 'advancedCourse', 'offlineCourse', 'reviewer']);

        return view('admin.portfolio.show', compact('project'));
    }

    public function toggleVisibility(PortfolioProject $project)
    {
        $project->update(['is_visible' => ! $project->is_visible]);
        $message = $project->is_visible ? 'تم إظهار المشروع في المعرض.' : 'تم إخفاء المشروع من المعرض.';

        return back()->with('success', $message);
    }

    public function toggleFeatured(PortfolioProject $project)
    {
        if ($project->status !== PortfolioProject::STATUS_PUBLISHED) {
            return back()->with('error', 'يمكن تمييز المشاريع المنشورة فقط.');
        }

        $project->update(['is_featured' => ! $project->is_featured]);

        if ($project->is_featured) {
            $this->achievements->ensureDefinitions();
            $this->achievements->grantFeatured($project->fresh(['user']));
        }

        $message = $project->is_featured
            ? 'تم تمييز المشروع كـ Featured.'
            : 'تم إلغاء تمييز المشروع.';

        return back()->with('success', $message);
    }
}
