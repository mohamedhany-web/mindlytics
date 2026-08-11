<?php

namespace App\Services;

use App\Models\JourneyProfile;
use App\Models\JourneyShareEvent;
use App\Models\PortfolioProject;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class JourneyAnalyticsService
{
    /**
     * @return array{
     *   totals: array<string,int>,
     *   by_channel: Collection,
     *   daily: Collection,
     *   top_projects: Collection,
     *   top_profiles: Collection,
     *   catalog: array{published_projects:int,public_profiles:int,featured_projects:int,open_to_work:int}
     * }
     */
    public function dashboard(?Carbon $from = null, ?Carbon $to = null): array
    {
        $from = ($from ?: now()->subDays(29))->startOfDay();
        $to = ($to ?: now())->endOfDay();

        $base = JourneyShareEvent::query()
            ->whereBetween('created_at', [$from, $to]);

        $totals = [
            'events' => (clone $base)->count(),
            'shares' => (clone $base)->whereIn('channel', ['linkedin', 'facebook', 'x', 'copy', 'download'])->count(),
            'card_views' => (clone $base)->where('channel', 'og_fetch')->count(),
            'page_views' => (clone $base)->whereIn('channel', ['project_view', 'profile_view'])->count(),
            'linkedin' => (clone $base)->where('channel', 'linkedin')->count(),
            'unique_actors' => (clone $base)->whereNotNull('user_id')->distinct('user_id')->count('user_id'),
        ];

        $byChannel = (clone $base)
            ->select('channel', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('channel')
            ->orderByDesc('aggregate')
            ->pluck('aggregate', 'channel');

        $daily = (clone $base)
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as aggregate'))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $topProjects = $this->topShareables(
            (clone $base)->where('shareable_type', PortfolioProject::class),
            PortfolioProject::class,
            8
        );

        $topProfiles = $this->topShareables(
            (clone $base)->where('shareable_type', JourneyProfile::class),
            JourneyProfile::class,
            8
        );

        $catalog = [
            'published_projects' => PortfolioProject::published()->count(),
            'public_profiles' => JourneyProfile::discoverable()->count(),
            'featured_projects' => PortfolioProject::published()->where('is_featured', true)->count(),
            'open_to_work' => JourneyProfile::discoverable()->where('is_open_to_work', true)->count(),
        ];

        return [
            'totals' => $totals,
            'by_channel' => $byChannel,
            'daily' => $daily,
            'top_projects' => $topProjects,
            'top_profiles' => $topProfiles,
            'catalog' => $catalog,
            'from' => $from,
            'to' => $to,
        ];
    }

    private function topShareables($query, string $type, int $limit): Collection
    {
        $rows = $query
            ->select('shareable_id', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('shareable_id')
            ->orderByDesc('aggregate')
            ->limit($limit)
            ->get();

        if ($rows->isEmpty()) {
            return collect();
        }

        $ids = $rows->pluck('shareable_id')->all();

        if ($type === PortfolioProject::class) {
            $models = PortfolioProject::with('user:id,name')
                ->whereIn('id', $ids)
                ->get()
                ->keyBy('id');
        } else {
            $models = JourneyProfile::with('user:id,name')
                ->whereIn('id', $ids)
                ->get()
                ->keyBy('id');
        }

        return $rows->map(function ($row) use ($models, $type) {
            $model = $models->get($row->shareable_id);

            return (object) [
                'id' => $row->shareable_id,
                'aggregate' => (int) $row->aggregate,
                'label' => $type === PortfolioProject::class
                    ? ($model->title ?? ('#' . $row->shareable_id))
                    : ($model?->resolvedDisplayName() ?? ('#' . $row->shareable_id)),
                'meta' => $model?->user?->name,
                'url' => $type === PortfolioProject::class
                    ? route('public.portfolio.show', $row->shareable_id)
                    : ($model ? route('public.journey.show', $model->slug) : null),
            ];
        });
    }
}
