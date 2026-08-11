<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\JourneyProfile;
use App\Models\PortfolioProject;
use App\Services\JourneyAchievementService;
use App\Services\JourneyShareCardService;
use Illuminate\Http\Request;

class JourneyController extends Controller
{
    public function __construct(
        private JourneyAchievementService $achievements,
        private JourneyShareCardService $shareCards,
    ) {
    }

    /**
     * صفحة رحلة الطالب العامة — مخصصة للشركات والتوظيف.
     * /j/{slug}
     */
    public function show(Request $request, string $slug)
    {
        $profile = JourneyProfile::with(['user:id,name,profile_image,profile_image_disk,headline,bio,skills'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $isOwner = auth()->check() && (int) auth()->id() === (int) $profile->user_id;

        if (! $profile->isUnlistedAccessible() && ! $isOwner) {
            abort(404);
        }

        $projects = PortfolioProject::published()
            ->where('user_id', $profile->user_id)
            ->with(['academicYear:id,name', 'advancedCourse:id,title', 'offlineCourse:id,title,online_only', 'images'])
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'published' => PortfolioProject::published()->where('user_id', $profile->user_id)->count(),
            'recorded' => PortfolioProject::published()->where('user_id', $profile->user_id)->where('program_type', PortfolioProject::PROGRAM_RECORDED)->count(),
            'diploma' => PortfolioProject::published()->where('user_id', $profile->user_id)->where('program_type', PortfolioProject::PROGRAM_DIPLOMA)->count(),
            'capstone' => PortfolioProject::published()->where('user_id', $profile->user_id)->where('is_capstone', true)->count(),
            'featured' => PortfolioProject::published()->where('user_id', $profile->user_id)->where('is_featured', true)->count(),
        ];

        $technologies = PortfolioProject::published()
            ->where('user_id', $profile->user_id)
            ->whereNotNull('technologies')
            ->pluck('technologies')
            ->flatten()
            ->filter()
            ->map(fn ($t) => is_string($t) ? trim($t) : $t)
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(18);

        $achievements = $this->achievements->forUser($profile->user);
        $shareCards = $this->shareCards;
        $ogImage = $this->shareCards->profileCardUrl($profile);

        $this->shareCards->track(auth()->user(), JourneyProfile::class, $profile->id, 'profile_view');

        return view('public.journey.show', compact(
            'profile',
            'projects',
            'stats',
            'technologies',
            'isOwner',
            'achievements',
            'shareCards',
            'ogImage'
        ));
    }
}
