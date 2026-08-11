<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\JourneyProfile;
use App\Models\PortfolioProject;
use App\Services\JourneyShareCardService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class JourneyShareCardController extends Controller
{
    public function __construct(private JourneyShareCardService $cards)
    {
    }

    public function project(Request $request, int $id): BinaryFileResponse
    {
        $project = PortfolioProject::published()->with('user')->findOrFail($id);
        $type = $request->get('type', JourneyShareCardService::TYPE_PROJECT_VERIFIED);
        if ($project->is_featured && $type === JourneyShareCardService::TYPE_PROJECT_VERIFIED) {
            // keep requested type; featured explicit via type=featured
        }
        if (! in_array($type, [
            JourneyShareCardService::TYPE_PROJECT_VERIFIED,
            JourneyShareCardService::TYPE_FEATURED,
        ], true)) {
            $type = JourneyShareCardService::TYPE_PROJECT_VERIFIED;
        }
        if ($type === JourneyShareCardService::TYPE_FEATURED && ! $project->is_featured) {
            $type = JourneyShareCardService::TYPE_PROJECT_VERIFIED;
        }

        $path = $this->cards->renderProjectPng($project, $type);
        $this->cards->track(auth()->user(), PortfolioProject::class, $project->id, 'og_fetch', $type);

        return response()->file($path, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    public function profile(Request $request, string $slug): BinaryFileResponse
    {
        $profile = JourneyProfile::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $isOwner = auth()->check() && (int) auth()->id() === (int) $profile->user_id;
        if (! $profile->isUnlistedAccessible() && ! $isOwner) {
            abort(404);
        }

        $type = $request->get('type', JourneyShareCardService::TYPE_PROFILE);
        if (! in_array($type, [
            JourneyShareCardService::TYPE_PROFILE,
            JourneyShareCardService::TYPE_MILESTONE,
        ], true)) {
            $type = JourneyShareCardService::TYPE_PROFILE;
        }

        $path = $this->cards->renderProfilePng($profile, $type);
        $this->cards->track(auth()->user(), JourneyProfile::class, $profile->id, 'og_fetch', $type);

        return response()->file($path, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    public function trackShare(Request $request)
    {
        $data = $request->validate([
            'shareable_type' => 'required|in:project,profile',
            'shareable_id' => 'required|integer',
            'channel' => 'required|in:linkedin,facebook,x,copy,download',
            'card_type' => 'nullable|string|max:40',
        ]);

        $type = $data['shareable_type'] === 'project'
            ? PortfolioProject::class
            : JourneyProfile::class;

        $this->cards->track(
            auth()->user(),
            $type,
            (int) $data['shareable_id'],
            $data['channel'],
            $data['card_type'] ?? null
        );

        return response()->json(['ok' => true]);
    }
}
