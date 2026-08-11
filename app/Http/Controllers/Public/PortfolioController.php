<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\JourneyProfile;
use App\Models\PortfolioProject;
use App\Services\JourneyShareCardService;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function __construct(private JourneyShareCardService $shareCards)
    {
    }
    /**
     * معرض المشاريع المعتمدة — منظم للشركات (مسجّل / دبلومات) مع بحث وتصفية.
     */
    public function index(Request $request)
    {
        $learningPaths = AcademicYear::where('is_active', true)
            ->visibleOnCurrentHost()
            ->ordered()
            ->get(['id', 'name']);

        $programType = $request->get('type'); // recorded | diploma
        $categoryId = $request->get('path');
        $q = trim((string) $request->get('q', ''));
        $sort = $request->get('sort', 'latest'); // latest | featured

        $query = PortfolioProject::published()
            ->with([
                'user:id,name,profile_image,profile_image_disk',
                'user.journeyProfile:id,user_id,slug,visibility,published_at,is_active',
                'academicYear:id,name',
                'advancedCourse:id,title',
                'offlineCourse:id,title,online_only',
            ])
            ->programType($programType);

        if ($categoryId) {
            $query->where('academic_year_id', $categoryId);
        }

        if ($q !== '') {
            $query->where(function ($builder) use ($q) {
                $builder->where('title', 'like', '%' . $q . '%')
                    ->orWhere('description', 'like', '%' . $q . '%')
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%' . $q . '%'));
            });
        }

        if ($sort === 'featured') {
            $query->orderByDesc('is_featured')->latest('published_at');
        } else {
            $query->latest('published_at');
        }

        $projects = $query->paginate(16)->withQueryString();

        $stats = [
            'projects' => PortfolioProject::published()->count(),
            'recorded' => PortfolioProject::published()->where('program_type', PortfolioProject::PROGRAM_RECORDED)->count(),
            'diploma' => PortfolioProject::published()->where('program_type', PortfolioProject::PROGRAM_DIPLOMA)->count(),
            'talent' => JourneyProfile::discoverable()->count(),
        ];

        return view('public.portfolio.index', compact(
            'projects',
            'learningPaths',
            'categoryId',
            'programType',
            'q',
            'sort',
            'stats'
        ));
    }

    /**
     * دليل المواهب — صفحات رحلات عامة فقط (قابل للتوسع بآلاف الطلبة عبر pagination).
     * فلاتر للشركات: بحث، Open to work، مهارة، نوع برنامج (مسجّل/دبلوم)، ترتيب.
     */
    public function talent(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $skill = trim((string) $request->get('skill', ''));
        $programType = $request->get('type'); // recorded | diploma
        $openToWork = $request->boolean('open_to_work');
        $sort = $request->get('sort', 'recent'); // recent | projects | completion

        $profiles = JourneyProfile::discoverable()
            ->with(['user:id,name,profile_image,profile_image_disk,headline,bio,skills'])
            ->when($q !== '', function ($builder) use ($q) {
                $builder->where(function ($inner) use ($q) {
                    $inner->where('display_name', 'like', '%' . $q . '%')
                        ->orWhere('headline', 'like', '%' . $q . '%')
                        ->orWhere('career_goal', 'like', '%' . $q . '%')
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%' . $q . '%'));
                });
            })
            ->when($openToWork, fn ($builder) => $builder->where('is_open_to_work', true))
            ->when($skill !== '', function ($builder) use ($skill) {
                $builder->where(function ($inner) use ($skill) {
                    $inner->whereHas('user', function ($u) use ($skill) {
                        $u->where('skills', 'like', '%' . $skill . '%');
                    })->orWhereHas('user.portfolioProjects', function ($p) use ($skill) {
                        $p->published()->where('technologies', 'like', '%' . $skill . '%');
                    });
                });
            })
            ->when(in_array($programType, [PortfolioProject::PROGRAM_RECORDED, PortfolioProject::PROGRAM_DIPLOMA], true), function ($builder) use ($programType) {
                $builder->whereHas('user.portfolioProjects', function ($p) use ($programType) {
                    $p->published()->where('program_type', $programType);
                });
            })
            ->when($sort === 'completion', fn ($b) => $b->orderByDesc('profile_completion')->latest('published_at'))
            ->when($sort !== 'completion', fn ($b) => $b->latest('published_at'))
            ->paginate(24)
            ->withQueryString();

        $userIds = $profiles->getCollection()->pluck('user_id')->all();
        $projectCounts = PortfolioProject::published()
            ->whereIn('user_id', $userIds)
            ->selectRaw('user_id, count(*) as aggregate')
            ->groupBy('user_id')
            ->pluck('aggregate', 'user_id');

        $techHints = PortfolioProject::published()
            ->whereIn('user_id', $userIds)
            ->whereNotNull('technologies')
            ->get(['user_id', 'technologies'])
            ->groupBy('user_id')
            ->map(function ($rows) {
                return collect($rows)->pluck('technologies')->flatten()->filter()->unique()->take(5)->values();
            });

        $profiles->getCollection()->transform(function (JourneyProfile $profile) use ($projectCounts, $techHints) {
            $profile->published_projects_count = (int) ($projectCounts[$profile->user_id] ?? 0);
            $profile->top_technologies = $techHints->get($profile->user_id, collect());

            return $profile;
        });

        if ($sort === 'projects') {
            $sorted = $profiles->getCollection()->sortByDesc('published_projects_count')->values();
            $profiles->setCollection($sorted);
        }

        $skillSuggestions = PortfolioProject::published()
            ->whereNotNull('technologies')
            ->pluck('technologies')
            ->flatten()
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(12)
            ->keys();

        return view('public.portfolio.talent', compact(
            'profiles',
            'q',
            'openToWork',
            'skill',
            'programType',
            'sort',
            'skillSuggestions'
        ));
    }

    public function show($id)
    {
        $project = PortfolioProject::published()
            ->with([
                'user:id,name,profile_image,profile_image_disk,bio,headline',
                'user.journeyProfile',
                'academicYear:id,name',
                'advancedCourse:id,title',
                'offlineCourse:id,title,online_only',
                'images',
                'reviews' => fn ($q) => $q->where('decision', 'approved')->latest()->limit(1),
            ])
            ->findOrFail($id);

        $related = PortfolioProject::published()
            ->where('id', '!=', $project->id)
            ->when(
                $project->academic_year_id,
                fn ($q) => $q->where('academic_year_id', $project->academic_year_id),
                fn ($q) => $q->where('program_type', $project->program_type)
            )
            ->with('user:id,name,profile_image,profile_image_disk')
            ->latest('published_at')
            ->take(4)
            ->get();

        $shareCards = $this->shareCards;
        $ogImage = $this->shareCards->projectCardUrl(
            $project,
            $project->is_featured
                ? JourneyShareCardService::TYPE_FEATURED
                : JourneyShareCardService::TYPE_PROJECT_VERIFIED
        );

        $this->shareCards->track(auth()->user(), PortfolioProject::class, $project->id, 'project_view');

        return view('public.portfolio.show', compact('project', 'related', 'shareCards', 'ogImage'));
    }
}
