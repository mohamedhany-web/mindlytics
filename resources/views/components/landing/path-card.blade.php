@php
    $name = $path->name ?? '';
    $slug = $path->slug ?? \Illuminate\Support\Str::slug($name);
    $desc = trim(strip_tags((string) ($path->description ?? '')));
    $coursesCount = (int) ($path->courses_count ?? 0);
    $subjectsCount = (int) ($path->academic_subjects_count ?? 0);
    $price = (float) ($path->price ?? 0);
    $thumb = filled($path->thumbnail ?? null)
        ? asset('storage/'.ltrim(str_replace('\\', '/', $path->thumbnail), '/'))
        : null;
    $initial = mb_strtoupper(mb_substr($name, 0, 1)) ?: 'M';
    $step = isset($step) ? (int) $step : null;
    $delay = (int) ($delay ?? 0);
@endphp
<a
    href="{{ route('public.learning-path.show', $slug) }}"
    class="lp-path-card lp-reveal"
    style="--lp-delay:{{ $delay }}ms"
    aria-label="{{ $name }}"
>
    <div class="lp-path-card__media {{ $thumb ? '' : 'is-empty' }}">
        @if ($step !== null)
            <span class="lp-path-card__step">{{ str_pad((string) $step, 2, '0', STR_PAD_LEFT) }}</span>
        @endif
        @if ($thumb)
            <img
                src="{{ $thumb }}"
                alt="{{ $name }}"
                loading="lazy"
                decoding="async"
                onerror="this.style.display='none'; this.parentElement.classList.add('is-empty'); var i=this.parentElement.querySelector('.lp-path-card__initial'); if(i){ i.hidden=false; }"
            >
            <span class="lp-path-card__initial" hidden aria-hidden="true">{{ $initial }}</span>
        @else
            <span class="lp-path-card__initial" aria-hidden="true">{{ $initial }}</span>
        @endif
    </div>
    <div class="lp-path-card__body">
        <h3 class="lp-path-card__title">{{ $name }}</h3>
        @if ($desc !== '')
            <p class="lp-path-card__desc">{{ \Illuminate\Support\Str::limit($desc, 110) }}</p>
        @endif
        <div class="lp-path-card__meta">
            <span>
                <strong>{{ trans_choice('landing.educo.paths.courses_count', $coursesCount, ['count' => $coursesCount]) }}</strong>
            </span>
            @if ($subjectsCount > 0)
                <span>· {{ trans_choice('landing.educo.paths.subjects_count', $subjectsCount, ['count' => $subjectsCount]) }}</span>
            @endif
        </div>
        <div class="lp-path-card__foot">
            <span class="lp-path-card__price">
                @if ($price > 0)
                    {{ number_format($price, 0) }} {{ __('landing.educo.paths.egp') }}
                @else
                    {{ __('landing.educo.paths.free') }}
                @endif
            </span>
            <span class="lp-path-card__cta">{{ __('landing.educo.paths.view_path') }}</span>
        </div>
    </div>
</a>
