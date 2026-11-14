@php
    $title = $project->title ?? '';
    $desc = trim(strip_tags((string) ($project->description ?? '')));
    $img = $project->preview_image_path ? asset($project->preview_image_path) : null;
    $initial = mb_strtoupper(mb_substr($title, 0, 1)) ?: 'P';
    $userName = $project->user->name ?? __('public.student_fallback');
    $userImg = $project->user->profile_image_url ?? null;
    $journey = $project->user->journeyProfile ?? null;
    $canOpenJourney = $journey && method_exists($journey, 'isPubliclyVisible') && $journey->isPubliclyVisible();
    $programLabel = match ($project->program_type ?? null) {
        'recorded' => __('landing.educo.portfolio.type_recorded'),
        'diploma' => __('landing.educo.portfolio.type_diploma'),
        default => null,
    };
    $context = $project->programContextLabel();
    $techs = collect($project->technologies ?? [])->filter()->take(3)->values();
    $delay = (int) ($delay ?? 0);
@endphp
<article class="lp-reveal" style="--lp-delay:{{ $delay }}ms">
    <a href="{{ route('public.portfolio.show', $project->id) }}" class="pf-project-card" aria-label="{{ $title }}">
        <div class="pf-project-card__media">
            @if ($img)
                <img
                    src="{{ $img }}"
                    alt="{{ $title }}"
                    loading="lazy"
                    decoding="async"
                    onerror="this.style.display='none'; this.nextElementSibling && this.nextElementSibling.removeAttribute('hidden');"
                >
                <span class="pf-project-card__placeholder" hidden aria-hidden="true">{{ $initial }}</span>
            @else
                <span class="pf-project-card__placeholder" aria-hidden="true">{{ $initial }}</span>
            @endif
        </div>
        <div class="pf-project-card__body">
            <div class="pf-badges">
                <span class="pf-badge pf-badge--ok">{{ __('landing.educo.portfolio.verified') }}</span>
                @if ($project->is_featured)
                    <span class="pf-badge pf-badge--hot">{{ __('landing.educo.portfolio.featured') }}</span>
                @endif
                @if ($project->is_capstone)
                    <span class="pf-badge">{{ __('landing.educo.portfolio.capstone') }}</span>
                @endif
                @if ($programLabel)
                    <span class="pf-badge">{{ $programLabel }}</span>
                @endif
            </div>
            <h3 class="pf-project-card__title">{{ $title }}</h3>
            @if ($desc !== '')
                <p class="pf-project-card__desc">{{ \Illuminate\Support\Str::limit($desc, 100) }}</p>
            @endif
            @if ($techs->isNotEmpty())
                <div class="pf-techs">
                    @foreach ($techs as $tech)
                        <span class="pf-tech">{{ $tech }}</span>
                    @endforeach
                </div>
            @endif
            <div class="pf-project-card__foot">
                <div class="pf-author">
                    <div class="pf-author__avatar">
                        @if ($userImg)
                            <img src="{{ $userImg }}" alt="" loading="lazy">
                        @else
                            {{ mb_strtoupper(mb_substr($userName, 0, 1)) }}
                        @endif
                    </div>
                    <strong>{{ $userName }}</strong>
                </div>
                <span class="pf-project-card__cta">
                    {{ $context ?: __('landing.educo.portfolio.view_project') }}
                </span>
            </div>
        </div>
    </a>
    @if ($canOpenJourney)
        <div style="margin-top:0.45rem;padding-inline:0.15rem;">
            <a href="{{ route('public.journey.show', $journey->slug) }}" class="lp-link-underline" style="font-size:0.8rem;font-weight:700;">
                {{ __('landing.educo.portfolio.view_profile') }}
            </a>
        </div>
    @endif
</article>
