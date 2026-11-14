@php
    $name = $profile->resolvedDisplayName();
    $headline = $profile->resolvedHeadline() ?: __('landing.educo.portfolio.talent_default_headline');
    $photo = $profile->user->profile_image_url ?? null;
    $initial = mb_strtoupper(mb_substr($name, 0, 1)) ?: '?';
    $projectsCount = (int) ($profile->published_projects_count ?? 0);
    $completion = (int) ($profile->profile_completion ?? 0);
    $techs = collect($profile->top_technologies ?? [])->filter()->take(5)->values();
    $delay = (int) ($delay ?? 0);
@endphp
<a
    href="{{ route('public.journey.show', $profile->slug) }}"
    class="pf-talent-card lp-reveal"
    style="--lp-delay:{{ $delay }}ms"
    aria-label="{{ $name }}"
>
    <div class="pf-talent-card__top">
        <div class="pf-talent-card__photo">
            @if ($photo)
                <img src="{{ $photo }}" alt="{{ $name }}" loading="lazy" decoding="async">
            @else
                {{ $initial }}
            @endif
        </div>
        <div style="min-width:0;flex:1;">
            <h3 class="pf-talent-card__name">{{ $name }}</h3>
            <p class="pf-talent-card__role">{{ $headline }}</p>
        </div>
    </div>

    <div class="pf-badges">
        @if ($profile->is_open_to_work)
            <span class="pf-badge pf-badge--hot">{{ __('landing.educo.portfolio.open_to_work') }}</span>
        @endif
        <span class="pf-badge pf-badge--ok">
            {{ trans_choice('landing.educo.portfolio.talent_projects', $projectsCount, ['count' => $projectsCount]) }}
        </span>
        @if ($completion > 0)
            <span class="pf-badge">{{ __('landing.educo.portfolio.talent_completion', ['pct' => $completion]) }}</span>
        @endif
    </div>

    @if ($techs->isNotEmpty())
        <div class="pf-techs">
            @foreach ($techs as $tech)
                <span class="pf-tech">{{ $tech }}</span>
            @endforeach
        </div>
    @endif

    @if (filled($profile->career_goal ?? null))
        <p class="pf-talent-card__goal">{{ $profile->career_goal }}</p>
    @endif
</a>
