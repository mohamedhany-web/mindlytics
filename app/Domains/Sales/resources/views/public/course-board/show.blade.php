@extends('layouts.public-site')

@section('title', $entry->name.' | Mindlytics Academy')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($entry->summary ?? $entry->name), 160))

@push('meta')
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $entry->name }} — Mindlytics Academy">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($entry->summary ?? ''), 200) }}">
@endpush

@push('styles')
<style>
    .ci-stage {
        position: relative;
        background: var(--lp-stage);
        color: var(--lp-primary);
        padding-block: clamp(2.5rem, 5vw, 4rem);
        overflow: hidden;
    }
    .ci-stage::after {
        content: '';
        position: absolute;
        inset-inline-end: -8%;
        top: -20%;
        width: min(42vw, 420px);
        height: min(42vw, 420px);
        border-radius: 50%;
        background: radial-gradient(circle, rgba(201,176,235,.45), transparent 70%);
        pointer-events: none;
    }
    .ci-crumbs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem 0.55rem;
        align-items: center;
        font-size: 0.85rem;
        color: var(--lp-text);
        margin-bottom: 1.1rem;
    }
    .ci-crumbs a { color: var(--lp-blue); text-decoration: none; font-weight: 700; }
    .ci-crumbs a:hover { text-decoration: underline; }
    .ci-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1.15rem;
    }
    .ci-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 0.85rem;
        border-radius: var(--lp-radius-pill);
        background: rgba(255,255,255,.72);
        border: 1px solid rgba(126,143,212,.28);
        color: var(--lp-primary);
        font-size: 0.82rem;
        font-weight: 700;
    }
    .ci-chip i { color: var(--lp-blue); font-size: 0.78rem; }
    .ci-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 0.85rem;
        margin-top: -1.5rem;
        position: relative;
        z-index: 2;
    }
    .ci-stat {
        background: #fff;
        border: 1px solid rgba(126,143,212,.18);
        border-radius: var(--lp-radius);
        padding: 1rem 1.1rem;
        box-shadow: 0 10px 28px rgba(63,51,86,.05);
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }
    .ci-stat__icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--lp-secondary);
        color: var(--lp-blue);
        flex: none;
    }
    .ci-stat__label {
        margin: 0;
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--lp-text);
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .ci-stat__value {
        margin: 0.15rem 0 0;
        font-size: 0.98rem;
        font-weight: 800;
        color: var(--lp-primary);
        line-height: 1.35;
        word-break: break-word;
    }
    .ci-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(280px, 0.9fr);
        gap: 1.5rem;
        align-items: start;
    }
    @media (max-width: 900px) {
        .ci-layout { grid-template-columns: 1fr; }
    }
    .ci-card {
        background: #fff;
        border: 1px solid rgba(126,143,212,.18);
        border-radius: var(--lp-radius);
        box-shadow: 0 10px 28px rgba(63,51,86,.05);
        overflow: hidden;
    }
    .ci-card + .ci-card { margin-top: 1rem; }
    .ci-card__head {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(126,143,212,.14);
        background: linear-gradient(180deg, #fff, rgba(235,230,246,.45));
    }
    .ci-card__head h2,
    .ci-card__head h3 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--lp-primary);
    }
    .ci-card__body { padding: 1.15rem 1.25rem 1.35rem; }
    .ci-highlights {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 0.65rem;
    }
    .ci-highlights li {
        display: flex;
        gap: 0.65rem;
        align-items: flex-start;
        padding: 0.75rem 0.9rem;
        border-radius: 10px;
        background: rgba(235,230,246,.45);
        border: 1px solid rgba(126,143,212,.16);
        color: var(--lp-primary);
        font-weight: 600;
        line-height: 1.55;
    }
    .ci-highlights li i {
        color: var(--lp-blue);
        margin-top: 0.2rem;
        flex: none;
    }
    .ci-details {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 0.55rem;
    }
    .ci-details li {
        position: relative;
        padding-inline-start: 1.15rem;
        color: var(--lp-text);
        line-height: 1.7;
        font-size: 0.95rem;
        white-space: pre-line;
    }
    .ci-details li::before {
        content: '';
        position: absolute;
        inset-inline-start: 0;
        top: 0.7rem;
        width: 0.45rem;
        height: 0.45rem;
        border-radius: 2px;
        background: var(--lp-yellow);
    }
    .ci-summary {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .ci-summary li {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(126,143,212,.14);
        font-size: 0.92rem;
    }
    .ci-summary li:last-child { border-bottom: 0; }
    .ci-summary span { color: var(--lp-text); font-weight: 600; }
    .ci-summary strong { color: var(--lp-primary); font-weight: 800; text-align: end; }
    .ci-price {
        background: linear-gradient(160deg, var(--lp-primary) 0%, #534d64 55%, #6a7bc4 120%);
        color: #fff;
        border-radius: var(--lp-radius);
        padding: 1.35rem 1.35rem 1.45rem;
        box-shadow: 0 16px 40px rgba(63,51,86,.22);
        position: sticky;
        top: 5.5rem;
    }
    .ci-price__eyebrow {
        margin: 0 0 0.25rem;
        font-size: 0.78rem;
        font-weight: 700;
        opacity: .78;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .ci-price h2 {
        margin: 0 0 1.15rem;
        font-size: 1.35rem;
        font-weight: 800;
        line-height: 1.3;
    }
    .ci-price__row {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.9rem 1rem;
        border-radius: 10px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.18);
        margin-bottom: 0.65rem;
    }
    .ci-price__row p { margin: 0; }
    .ci-price__row .label { font-size: 0.75rem; font-weight: 700; opacity: .8; margin-bottom: 0.25rem; }
    .ci-price__row .amount { font-size: 1.65rem; font-weight: 800; line-height: 1; }
    .ci-price__row .currency { font-size: 0.85rem; font-weight: 700; opacity: .85; padding-bottom: 0.2rem; }
    .ci-actions { display: grid; gap: 0.55rem; margin-top: 1rem; }
    .ci-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        min-height: 2.75rem;
        padding: 0.7rem 1rem;
        border-radius: var(--lp-radius-pill);
        font: inherit;
        font-weight: 800;
        font-size: 0.92rem;
        text-decoration: none;
        border: 0;
        cursor: pointer;
        transition: filter .15s ease, transform .15s ease;
    }
    .ci-btn:hover { filter: brightness(0.97); transform: translateY(-1px); }
    .ci-btn--primary {
        background: var(--lp-yellow);
        color: var(--lp-yellow-ink);
    }
    .ci-btn--ghost {
        background: rgba(255,255,255,.12);
        color: #fff;
        border: 1px solid rgba(255,255,255,.28);
    }
    .ci-note {
        margin-top: 0.85rem;
        text-align: center;
        font-size: 0.82rem;
        color: var(--lp-text);
        background: #fff;
        border: 1px solid rgba(126,143,212,.18);
        border-radius: var(--lp-radius);
        padding: 0.85rem 1rem;
    }
</style>
@endpush

@section('content')
@php
    $metaChips = array_values(array_filter([
        filled($entry->format) && $entry->format !== '—' ? ['label' => $entry->format, 'icon' => 'fas fa-laptop'] : null,
        filled($entry->audience) ? ['label' => $entry->audience, 'icon' => 'fas fa-users'] : null,
        filled($entry->startDisplay()) ? ['label' => __('landing.educo.course_info.start_prefix', ['value' => $entry->startDisplay()]), 'icon' => 'fas fa-calendar-day'] : null,
    ]));

    $detailRows = array_values(array_filter([
        ['icon' => 'fa-chalkboard-teacher', 'label' => __('landing.educo.course_info.instructor'), 'value' => $entry->instructor_name],
        ['icon' => 'fa-calendar-week', 'label' => __('landing.educo.course_info.schedule_days'), 'value' => $entry->schedule_days],
        ['icon' => 'fa-hourglass-half', 'label' => __('landing.educo.course_info.duration'), 'value' => $entry->duration],
        ['icon' => 'fa-clock', 'label' => __('landing.educo.course_info.hours'), 'value' => $entry->hours],
    ], fn ($r) => filled($r['value']) && $r['value'] !== '—'));

    $highlights = $entry->landingHighlights();
    $detailLines = filled($entry->landing_details)
        ? array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n|•|·(?=\s)/', $entry->landing_details))))
        : [];

    $courseUrl = $entry->advanced_course_id
        ? route('public.course.show', $entry->advanced_course_id)
        : null;
    $shareUrl = url()->current();
    $contactUrl = route('public.contact', ['subject' => 'استفسار عن كورس: '.$entry->name]);
@endphp

<section class="ci-stage">
    <div class="lp-container lp-reveal" style="position:relative;z-index:1">
        <nav class="ci-crumbs" aria-label="{{ __('landing.educo.course_info.breadcrumb') }}">
            <a href="{{ url('/') }}">{{ __('landing.educo.header.home') }}</a>
            <span aria-hidden="true">/</span>
            <a href="{{ url('/courses') }}">{{ __('landing.educo.course_info.all_courses') }}</a>
            <span aria-hidden="true">/</span>
            <span>{{ $entry->name }}</span>
        </nav>
        <p class="lp-eyebrow">{{ __('landing.educo.course_info.eyebrow') }}</p>
        <h1 class="lp-h1" style="margin:0.35rem 0 0.75rem;max-width:40rem">{{ $entry->name }}</h1>
        @if(filled($entry->summary))
            <p class="lp-lead" style="margin:0;max-width:42rem">{{ $entry->summary }}</p>
        @endif
        @if($metaChips !== [])
            <div class="ci-chips">
                @foreach($metaChips as $chip)
                    <span class="ci-chip"><i class="{{ $chip['icon'] }}"></i>{{ $chip['label'] }}</span>
                @endforeach
            </div>
        @endif
    </div>
</section>

@if($detailRows !== [])
<section class="lp-section" style="padding-block:0 0;background:var(--lp-cream)">
    <div class="lp-container">
        <div class="ci-stats lp-reveal">
            @foreach($detailRows as $row)
                <article class="ci-stat">
                    <span class="ci-stat__icon"><i class="fas {{ $row['icon'] }}"></i></span>
                    <div>
                        <p class="ci-stat__label">{{ $row['label'] }}</p>
                        <p class="ci-stat__value">{{ $row['value'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="lp-section" style="padding-block:clamp(2rem,4vw,3.25rem);background:var(--lp-cream)">
    <div class="lp-container ci-layout">
        <div class="lp-reveal">
            @if($highlights !== [])
                <section class="ci-card">
                    <div class="ci-card__head"><h2>{{ __('landing.educo.course_info.highlights_title') }}</h2></div>
                    <div class="ci-card__body">
                        <ul class="ci-highlights">
                            @foreach($highlights as $item)
                                <li><i class="fas fa-check"></i><span>{{ $item }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                </section>
            @endif

            @if($detailLines !== [])
                <section class="ci-card">
                    <div class="ci-card__head"><h2>{{ __('landing.educo.course_info.details_title') }}</h2></div>
                    <div class="ci-card__body">
                        <ul class="ci-details">
                            @foreach($detailLines as $line)
                                @if(mb_strlen($line) > 1)
                                    <li>{{ $line }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </section>
            @endif

            <section class="ci-card">
                <div class="ci-card__head"><h3>{{ __('landing.educo.course_info.summary_title') }}</h3></div>
                <div class="ci-card__body">
                    <ul class="ci-summary">
                        @foreach([
                            [__('landing.educo.course_info.audience'), $entry->audience],
                            [__('landing.educo.course_info.format'), $entry->format],
                            [__('landing.educo.course_info.start'), $entry->startDisplay()],
                            [__('landing.educo.course_info.instructor'), $entry->instructor_name],
                            [__('landing.educo.course_info.schedule_days'), $entry->schedule_days],
                            [__('landing.educo.course_info.duration'), $entry->duration],
                            [__('landing.educo.course_info.hours'), $entry->hours],
                        ] as [$label, $value])
                            @if(filled($value) && $value !== '—')
                                <li>
                                    <span>{{ $label }}</span>
                                    <strong>{{ $value }}</strong>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </section>
        </div>

        <aside class="lp-reveal" style="--lp-delay:100ms">
            <div class="ci-price">
                <p class="ci-price__eyebrow">{{ __('landing.educo.course_info.investment') }}</p>
                <h2>{{ $entry->name }}</h2>

                @if($entry->publicPrice() !== null || $entry->price_recorded)
                    @if($entry->publicPrice() !== null)
                        <div class="ci-price__row">
                            <div>
                                <p class="label">{{ __('landing.educo.course_info.price_online') }}</p>
                                <p class="amount">{{ $entry->publicPrice() > 0 ? number_format($entry->publicPrice(), 0) : __('landing.educo.course_info.free') }}</p>
                            </div>
                            @if($entry->publicPrice() > 0)
                                <span class="currency">{{ __('landing.educo.course_info.egp') }}</span>
                            @endif
                        </div>
                    @endif
                    @if($entry->price_recorded)
                        <div class="ci-price__row">
                            <div>
                                <p class="label">{{ __('landing.educo.course_info.price_recorded') }}</p>
                                <p class="amount">{{ number_format((float) $entry->price_recorded, 0) }}</p>
                            </div>
                            <span class="currency">{{ __('landing.educo.course_info.egp') }}</span>
                        </div>
                    @endif
                @else
                    <p style="margin:0 0 1rem;opacity:.85;font-size:0.92rem;line-height:1.55">{{ __('landing.educo.course_info.price_pending') }}</p>
                @endif

                <div class="ci-actions">
                    <a href="{{ $contactUrl }}" class="ci-btn ci-btn--primary">
                        <i class="fas fa-headset"></i>
                        {{ __('landing.educo.course_info.contact_sales') }}
                    </a>
                    @if($courseUrl)
                        <a href="{{ $courseUrl }}" class="ci-btn ci-btn--ghost">
                            <i class="fas fa-graduation-cap"></i>
                            {{ __('landing.educo.course_info.platform_page') }}
                        </a>
                    @endif
                    <button type="button" id="copy-course-link" data-url="{{ $shareUrl }}" class="ci-btn ci-btn--ghost">
                        <i class="fas fa-link"></i>
                        {{ __('landing.educo.course_info.copy_link') }}
                    </button>
                </div>
            </div>
            <p class="ci-note">
                <i class="fas fa-shield-halved" style="color:var(--lp-blue);margin-inline-end:0.35rem"></i>
                {{ __('landing.educo.course_info.trust_note') }}
            </p>
        </aside>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.getElementById('copy-course-link')?.addEventListener('click', function () {
    var url = this.getAttribute('data-url');
    if (!url || !navigator.clipboard) return;
    var btn = this;
    var copied = @js(__('landing.educo.course_info.link_copied'));
    var old = btn.innerHTML;
    navigator.clipboard.writeText(url).then(function () {
        btn.innerHTML = '<i class="fas fa-check"></i> ' + copied;
        setTimeout(function () { btn.innerHTML = old; }, 2000);
    });
});
</script>
@endpush
