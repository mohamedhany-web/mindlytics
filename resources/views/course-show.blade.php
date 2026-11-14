@extends('layouts.public-site')

@section('title', $course->localized('title') ?: __('public.course_detail_title'))

@push('head')
<style>
    .cx {
        --cx-ink: var(--lp-primary);
        --cx-mute: var(--lp-text);
        --cx-stage: var(--lp-stage);
    }

    /* ——— Stage: one composition ——— */
    .cx-stage {
        position: relative;
        min-height: min(62vh, 560px);
        display: flex;
        align-items: flex-end;
        color: var(--lp-stage-ink);
        background: var(--lp-stage);
        overflow: hidden;
    }
    .cx-stage__media {
        position: absolute;
        inset: 0;
    }
    .cx-stage__media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.32;
        transform: scale(1.04);
    }
    .cx-stage__wash {
        position: absolute;
        inset: 0;
        background:
            linear-gradient(180deg, rgba(238, 242, 249, 0.45) 0%, rgba(228, 224, 242, 0.82) 48%, var(--lp-stage) 100%),
            radial-gradient(ellipse 70% 55% at 90% 10%, rgba(201, 176, 235, 0.38), transparent 58%),
            linear-gradient(90deg, rgba(235, 230, 246, 0.92) 0%, transparent 60%);
    }
    .cx-stage.has-cover .cx-stage__wash {
        background:
            linear-gradient(180deg, rgba(238, 242, 249, 0.4) 0%, rgba(228, 224, 242, 0.75) 45%, var(--lp-stage) 100%),
            linear-gradient(90deg, rgba(235, 230, 246, 0.85) 0%, transparent 58%);
    }
    .cx-stage__inner {
        position: relative;
        z-index: 1;
        width: 100%;
        padding-block: 3.5rem 2.75rem;
    }
    .cx-crumb {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        font-size: 0.8rem;
        color: var(--lp-stage-mute);
        margin-bottom: 1.25rem;
    }
    .cx-crumb a { color: var(--lp-stage-ink); text-decoration: none; font-weight: 600; }
    .cx-crumb a:hover { color: var(--lp-blue-hover); }
    .cx-brand {
        font-weight: 800;
        font-size: clamp(1.75rem, 4vw, 2.35rem);
        letter-spacing: -0.02em;
        margin: 0 0 0.85rem;
        color: var(--lp-stage-ink);
    }
    .cx-brand span { color: var(--lp-blue-hover); }
    .cx-title {
        margin: 0 0 0.85rem;
        max-width: min(100%, 28ch);
        font-weight: 800;
        font-size: clamp(1.85rem, 5vw, 3.1rem);
        line-height: 1.12;
        letter-spacing: -0.03em;
        color: var(--lp-stage-ink);
    }
    .cx-hook {
        margin: 0 0 1.35rem;
        max-width: 36rem;
        font-size: clamp(1rem, 2.2vw, 1.2rem);
        line-height: 1.6;
        color: var(--lp-stage-mute);
    }
    .cx-facts {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem 1.25rem;
        margin: 0 0 1.75rem;
        font-size: 0.9rem;
        color: var(--lp-stage-mute);
    }
    .cx-facts strong { color: var(--lp-stage-ink); font-weight: 700; }
    .cx-cta-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.85rem 1.15rem;
    }
    .cx-cta-row .cx-link {
        font-size: 0.9rem;
    }
    .cx-price {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
        min-width: 6.5rem;
    }
    .cx-price__now {
        font-size: 1.65rem;
        font-weight: 800;
        color: var(--lp-blue-hover);
        line-height: 1;
    }
    .cx-price__was {
        font-size: 0.85rem;
        color: var(--lp-text);
        text-decoration: line-through;
    }
    .cx-cta-row .lp-btn-yellow {
        padding: 1rem 1.75rem;
        font-size: 0.95rem;
    }
    .cx-link {
        color: var(--lp-blue-hover);
        font-weight: 700;
        text-decoration: underline;
        text-underline-offset: 3px;
    }
    .cx-link:hover { color: var(--lp-primary); }

    /* ——— Journey strip ——— */
    .cx-journey {
        background: var(--lp-secondary);
        padding-block: 2.75rem;
        border-bottom: 1px solid rgba(63, 51, 86,0.05);
    }
    .cx-journey__grid {
        display: grid;
        gap: 1.75rem;
    }
    @media (min-width: 900px) {
        .cx-journey__grid { grid-template-columns: repeat(3, 1fr); gap: 2rem; }
    }
    .cx-step { position: relative; }
    .cx-step__n {
        display: block;
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--lp-blue);
        line-height: 1;
        margin-bottom: 0.65rem;
        opacity: 0.9;
    }
    .cx-step h2 {
        margin: 0 0 0.45rem;
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--cx-ink);
    }
    .cx-step p {
        margin: 0;
        color: var(--cx-mute);
        line-height: 1.55;
        font-size: 0.95rem;
    }

    /* ——— Theater + curriculum (one soft purple/blue surface) ——— */
    .cx-theater {
        padding-block: 3.5rem;
        background:
            linear-gradient(180deg, var(--lp-secondary) 0%, var(--lp-cream) 100%);
    }
    .cx-theater__head {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 1rem;
        align-items: end;
        margin-bottom: 1.5rem;
    }
    .cx-theater__head .lp-h1 { margin: 0.35rem 0 0; }
    .cx-playlist {
        display: grid;
        gap: 0;
        border-top: 1px solid rgba(63, 51, 86,0.08);
    }
    .cx-track {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 1rem;
        align-items: center;
        padding: 1.1rem 0;
        border-bottom: 1px solid rgba(63, 51, 86,0.08);
        background: transparent;
        width: 100%;
        text-align: start;
        font: inherit;
        color: inherit;
        cursor: pointer;
        transition: background 0.15s;
    }
    .cx-track:hover { background: var(--lp-secondary); }
    .cx-track.is-locked { cursor: default; opacity: 0.72; }
    .cx-track__idx {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--lp-secondary);
        color: var(--lp-blue);
        font-weight: 800;
        font-size: 0.85rem;
    }
    .cx-track.is-locked .cx-track__idx {
        background: var(--lp-grey);
        color: var(--cx-mute);
    }
    .cx-track__title {
        margin: 0;
        font-weight: 700;
        color: var(--cx-ink);
        font-size: 1rem;
    }
    .cx-track__meta {
        margin: 0.2rem 0 0;
        font-size: 0.8rem;
        color: var(--cx-mute);
    }
    .cx-track__action {
        font-size: 0.8rem;
        font-weight: 800;
        color: var(--lp-blue);
        white-space: nowrap;
    }
    .cx-track.is-locked .cx-track__action { color: var(--cx-mute); }

    /* ——— Story ——— */
    .cx-story {
        padding-block: 3.5rem 4rem;
        background: var(--lp-grey);
    }
    .cx-story__grid {
        display: grid;
        gap: 2.5rem;
    }
    @media (min-width: 960px) {
        .cx-story__grid {
            grid-template-columns: 1.15fr 0.85fr;
            gap: 3.5rem;
            align-items: start;
        }
    }
    .cx-prose p {
        margin: 0 0 1rem;
        font-size: 1.05rem;
        line-height: 1.75;
        color: var(--cx-ink);
    }
    .cx-prose .muted {
        color: var(--cx-mute);
        white-space: pre-line;
    }
    .cx-outcomes {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 0.85rem;
    }
    .cx-outcomes li {
        position: relative;
        padding-inline-start: 1.35rem;
        color: var(--cx-ink);
        font-weight: 600;
        line-height: 1.45;
    }
    .cx-outcomes li::before {
        content: '';
        position: absolute;
        inset-inline-start: 0;
        top: 0.45rem;
        width: 0.55rem;
        height: 0.55rem;
        border-radius: 2px;
        background: var(--lp-yellow);
    }
    .cx-aside-block + .cx-aside-block { margin-top: 2rem; }
    .cx-aside-block h3 {
        margin: 0 0 0.65rem;
        font-size: 0.8rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--lp-green);
        font-weight: 700;
    }
    .cx-aside-block p {
        margin: 0;
        color: var(--cx-mute);
        line-height: 1.6;
        white-space: pre-line;
    }

    /* Important course info (admin «معلومات صفحة الكورس العامة») */
    .cx-info-section {
        padding-block: clamp(2.5rem, 5vw, 4rem);
        background: linear-gradient(180deg, rgba(246, 244, 251, 0.9), #fff 70%);
        border-block: 1px solid rgba(126, 143, 212, 0.14);
    }
    .cx-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }
    .cx-info-card {
        background: #fff;
        border: 1px solid rgba(126, 143, 212, 0.2);
        border-radius: var(--lp-radius);
        padding: 1.25rem 1.35rem;
        box-shadow: 0 10px 28px rgba(63, 51, 86, 0.05);
    }
    .cx-info-card h3 {
        margin: 0 0 0.7rem;
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--lp-ink, #2b2340);
    }
    .cx-info-card p {
        margin: 0;
        color: var(--cx-mute);
        line-height: 1.7;
        white-space: pre-line;
        font-size: 0.95rem;
    }

    /* ——— Curriculum timeline ——— */
    .cx-curriculum {
        padding-block: 0;
        background: transparent;
    }
    .cx-curriculum > .lp-eyebrow,
    .cx-curriculum > .lp-h1 {
        margin-inline: 0;
    }
    .cx-timeline {
        list-style: none;
        margin: 1.35rem 0 0;
        padding: 1.35rem 1.35rem 0.35rem;
        border: 1px solid rgba(126, 143, 212, 0.22);
        border-radius: var(--lp-radius);
        background: rgba(255, 255, 255, 0.72);
        box-shadow: 0 10px 28px rgba(63, 51, 86, 0.04);
        position: relative;
    }
    .cx-timeline::before {
        content: '';
        position: absolute;
        inset-inline-start: calc(1.35rem + 0.3rem);
        top: 1.6rem;
        bottom: 1.1rem;
        width: 2px;
        background: linear-gradient(180deg, var(--lp-blue), var(--lp-yellow));
        opacity: 0.45;
        border-radius: 2px;
    }
    .cx-timeline li {
        position: relative;
        padding: 0 0 1.35rem 1.85rem;
        margin: 0;
    }
    .cx-timeline li::before {
        content: '';
        position: absolute;
        inset-inline-start: 0.05rem;
        top: 0.35rem;
        width: 0.75rem;
        height: 0.75rem;
        border-radius: 50%;
        background: var(--lp-blue);
        box-shadow: 0 0 0 4px rgba(238, 242, 249, 0.95);
        z-index: 1;
    }
    .cx-timeline strong {
        display: block;
        font-size: 1.05rem;
        color: var(--cx-ink);
        margin-bottom: 0.2rem;
    }
    .cx-timeline span {
        font-size: 0.85rem;
        color: var(--cx-mute);
    }
    .cx-curriculum .lp-btn-outline {
        margin-top: 0.35rem;
        border-color: rgba(126, 143, 212, 0.28);
        background: rgba(255, 255, 255, 0.55);
    }

    /* ——— Reviews ——— */
    .cx-reviews {
        padding-block: 3.5rem;
        background: var(--lp-secondary);
    }
    .cx-quote {
        margin: 0;
        max-width: 40rem;
        font-size: clamp(1.2rem, 2.5vw, 1.55rem);
        font-weight: 700;
        line-height: 1.45;
        color: var(--cx-ink);
    }
    .cx-quote-meta {
        margin-top: 1rem;
        color: var(--cx-mute);
        font-size: 0.9rem;
    }
    .cx-review-form {
        margin-top: 1.75rem;
        display: grid;
        gap: 0.75rem;
        max-width: 36rem;
    }
    .cx-review-form select,
    .cx-review-form textarea {
        width: 100%;
        border: 0;
        border-radius: var(--lp-radius);
        background: #fff;
        padding: 0.85rem 1rem;
        font: inherit;
    }

    /* ——— Related ——— */
    .cx-related {
        padding-block: 3.5rem 4.5rem;
        background: var(--lp-secondary);
    }

    /* ——— Sticky dock ——— */
    .cx-dock {
        position: fixed;
        inset-inline: 0;
        bottom: 0;
        z-index: 70;
        background: rgba(255,255,255,0.96);
        backdrop-filter: blur(12px);
        border-top: 1px solid rgba(63, 51, 86,0.08);
        padding: 0.75rem 1rem calc(0.75rem + env(safe-area-inset-bottom));
        transform: translateY(110%);
        transition: transform 0.25s ease;
    }
    .cx-dock.is-visible { transform: translateY(0); }
    .cx-dock__inner {
        max-width: var(--lp-max);
        margin-inline: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }
    .cx-dock__price {
        font-weight: 800;
        font-size: 1.15rem;
        color: var(--cx-ink);
    }
    .cx-dock__price small {
        display: block;
        font-size: 0.75rem;
        color: var(--cx-mute);
        font-weight: 600;
    }
    .cx-dock .lp-btn-yellow {
        padding: 0.85rem 1.25rem;
        font-size: 0.875rem;
    }
    @media (max-width: 899px) {
        body.lp-site-body {
            padding-bottom: 5.5rem;
        }
    }
    @media (min-width: 900px) {
        .cx-dock { display: none; }
    }

    /* Stage content must never stay invisible */
    .cx-stage .lp-reveal { opacity: 1; transform: none; }

    /* ——— Preview modal ——— */
    .cx-modal-bg {
        position: fixed;
        inset: 0;
        z-index: 100000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: rgba(63, 51, 86,0.84);
        backdrop-filter: blur(6px);
    }
    .cx-modal {
        background: #fff;
        border-radius: var(--lp-radius);
        width: min(960px, 100%);
        max-height: 92vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .cx-modal__bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.9rem 1.1rem;
        border-bottom: 1px solid rgba(63, 51, 86,0.06);
    }
    .cx-modal__bar h4 {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
    }
    .cx-modal__bar button {
        border: 0;
        background: var(--lp-grey);
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 999px;
        cursor: pointer;
        font-weight: 700;
    }
    .cx-modal__body { min-height: 240px; background: #000; }
    .cx-modal__body iframe,
    .cx-modal__body video {
        width: 100%;
        height: min(70vh, 540px);
        border: 0;
        display: block;
    }

    .cx-alert {
        max-width: var(--lp-max);
        margin: 1rem auto 0;
        padding: 0.85rem 1rem;
        border-radius: var(--lp-radius);
        font-weight: 700;
        font-size: 0.9rem;
    }
    .cx-alert--ok { background: #ecfdf5; color: #065f46; }
    .cx-alert--info { background: var(--lp-secondary); color: var(--lp-primary); }
    .cx-alert--err { background: #fef2f2; color: #991b1b; }

    @media (prefers-reduced-motion: reduce) {
        .cx-dock { transition: none; }
        .cx-stage__media img { transform: none; }
    }
</style>
@endpush

@section('content')
@php
    $courseContact = $platformContact ?? \App\Support\PlatformSettings::contactPage();
    $courseWhatsappDigits = \App\Support\PlatformSettings::phoneDigits((string) ($courseContact['whatsapp'] ?? ''));
    if ($courseWhatsappDigits === '') {
        $courseWhatsappDigits = \App\Support\PlatformSettings::phoneDigits((string) ($courseContact['phone'] ?? ''));
    }
    if ($courseWhatsappDigits !== '' && str_starts_with($courseWhatsappDigits, '0')) {
        $courseWhatsappDigits = '20' . substr($courseWhatsappDigits, 1);
    }
    $courseWhatsappText = rawurlencode(
        __('landing.educo.course_show.whatsapp_prefill', [
            'title' => $course->localized('title') ?: $course->title,
        ])
    );
    $courseWhatsappUrl = $courseWhatsappDigits !== ''
        ? 'https://wa.me/' . $courseWhatsappDigits . '?text=' . $courseWhatsappText
        : null;

    $isPaid = (($course->effectivePrice() ?? $course->price ?? 0) > 0) && !($course->is_free ?? false);
    $primaryUrl = null;
    $primaryLabel = __('public.buy_now');
    if (!empty($isEnrolled)) {
        $primaryUrl = route('courses.show', $course->id);
        $primaryLabel = __('landing.educo.course_show.start_learning');
    } elseif ($isPaid) {
        $primaryUrl = route('public.course.checkout', $course->id);
        $primaryLabel = __('public.buy_now');
    } elseif (auth()->check()) {
        $primaryUrl = route('public.course.enroll.free', $course->id);
        $primaryLabel = __('public.register_free');
    } else {
        $primaryUrl = route('register', ['redirect' => route('public.course.enroll.free', $course->id)]);
        $primaryLabel = __('public.register_free');
    }

    $coverUrl = method_exists($course, 'thumbnailUrl') ? $course->thumbnailUrl() : null;
    $courseTitle = $course->localized('title') ?: __('public.course_title_fallback');
    $courseHook = trim((string) ($course->localized('hook') ?: ''));
    if ($courseHook === '') {
        $courseHook = \Illuminate\Support\Str::limit(strip_tags((string) ($course->localized('description') ?: '')), 160);
    }

    $levelLabel = match ($course->level ?? '') {
        'beginner' => __('public.level_beginner'),
        'intermediate' => __('public.level_intermediate'),
        'advanced' => __('public.level_advanced'),
        default => __('public.level_label'),
    };
    $lessonsCount = (int) ($course->lessons_count ?? $course->lectures_count ?? 0);
    $hoursLabel = $course->display_duration_label
        ?? ((int) ($course->display_duration_hours ?? $course->duration_hours ?? 0)
            ? ((int) ($course->display_duration_hours ?? $course->duration_hours)).' '.__('public.hours')
            : null);
    $hasPreviewVideos = isset($previewVideoLessons) && $previewVideoLessons->count() > 0;
    $hasSections = isset($sections) && $sections->count() > 0;
    $hasIntroVideo = filled($course->video_url ?? null);

    $learnPoints = collect();
    if (!empty($course->what_you_learn)) {
        $learnPoints = collect(preg_split('/\r\n|\r|\n/', (string) $course->what_you_learn))
            ->map(fn ($l) => trim($l))
            ->filter();
    }
    $learnPreviewCount = 6;

    $infoBlocks = collect([
        ['key' => 'suitable_for', 'value' => $course->suitable_for],
        ['key' => 'age_suitability', 'value' => $course->age_suitability],
        ['key' => 'instructor_info', 'value' => $course->instructor_info],
        ['key' => 'available_until', 'value' => $course->available_until_info],
        ['key' => 'follow_up', 'value' => $course->follow_up_info],
    ])->filter(fn ($block) => filled($block['value']))->values();

    $previewUnlockedCount = (int) ($previewUnlockedCount ?? 2);
    $previewUnlockCta = $primaryUrl;
    if (!empty($isEnrolled)) {
        $previewUnlockCta = route('courses.show', $course->id);
    }

    $__mlAnalytics = app(\App\Domains\Marketing\Services\MarketingAnalyticsService::class);
    $__mlViewItem = $__mlAnalytics->viewItem($__mlAnalytics->itemFromCourse($course));
@endphp

<x-ecommerce-datalayer :payload="$__mlViewItem" />

@if(session('payment_success_modal'))
    @include('components.payment-success-modal', [
        'message' => session('success'),
        'redirectUrl' => session('payment_success_redirect_url') ?? (Auth::check() && Auth::user()->isStudent() ? route('my-courses.learn', $course->id) : null),
        'seconds' => 5,
    ])
@endif

@if(session('success') && !session('payment_success_modal'))
    <div class="lp-container"><div class="cx-alert cx-alert--ok">{{ session('success') }}</div></div>
@endif
@if(session('info'))
    <div class="lp-container"><div class="cx-alert cx-alert--info">{{ session('info') }}</div></div>
@endif
@if(session('error'))
    <div class="lp-container"><div class="cx-alert cx-alert--err">{{ session('error') }}</div></div>
@endif

{{-- 1) Stage: brand + title + one CTA —— not a dashboard of cards --}}
<section class="cx-stage {{ $coverUrl ? 'has-cover' : '' }}" aria-label="{{ $courseTitle }}">
    <div class="cx-stage__media" aria-hidden="true">
        @if($coverUrl)
            <img src="{{ $coverUrl }}" alt="" loading="eager" decoding="async">
        @endif
        <div class="cx-stage__wash"></div>
    </div>
    <div class="cx-stage__inner">
        <div class="lp-container lp-reveal">
            <nav class="cx-crumb" aria-label="breadcrumb">
                <a href="{{ route('home') }}">{{ __('public.home') }}</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('public.courses') }}">{{ __('public.courses') }}</a>
                <span aria-hidden="true">/</span>
                <span>{{ \Illuminate\Support\Str::limit($courseTitle, 42) }}</span>
            </nav>

            <p class="cx-brand">Mind<span>lytics</span></p>
            <h1 class="cx-title">{{ $courseTitle }}</h1>
            @if($courseHook !== '')
                <p class="cx-hook">{{ $courseHook }}</p>
            @endif

            <p class="cx-facts">
                <span><strong>{{ $levelLabel }}</strong></span>
                @if($hoursLabel)
                    <span>· {{ $hoursLabel }}</span>
                @endif
                @if($lessonsCount > 0)
                    <span>· {{ trans_choice('landing.educo.course_show.lessons_count', $lessonsCount, ['count' => $lessonsCount]) }}</span>
                @endif
                @if(($reviewsCount ?? 0) > 0)
                    <span>· {{ number_format((float) ($reviewsAvg ?? 0), 1) }} / 5</span>
                @endif
                @if($course->instructor)
                    <span>· {{ $course->instructor->name }}</span>
                @endif
            </p>

            <div class="cx-cta-row">
                <div class="cx-price">
                    @if($isPaid)
                        @if($course->hasCourseDiscount())
                            <span class="cx-price__was">{{ number_format($course->originalPrice(), 0) }} {{ __('landing.educo.course_show.egp') }}</span>
                        @endif
                        <span class="cx-price__now">{{ number_format($course->effectivePrice(), 0) }} {{ __('landing.educo.course_show.egp') }}</span>
                    @else
                        <span class="cx-price__now">{{ __('landing.educo.course_show.free') }}</span>
                    @endif
                </div>
                <a href="{{ $primaryUrl }}" class="lp-btn-yellow">{{ $primaryLabel }}</a>
                @if($hasPreviewVideos || $hasIntroVideo)
                    <a href="#cx-preview" class="cx-link">{{ __('landing.educo.course_show.watch_preview') }}</a>
                @elseif($hasSections)
                    <a href="#cx-curriculum" class="cx-link">{{ __('landing.educo.course_show.see_curriculum') }}</a>
                @endif
                @if(!empty($courseMindMapVisible ?? false))
                    <a href="{{ route('public.course.mind-map', $course->id) }}" class="cx-link">{{ __('public.course_mind_map_button') }}</a>
                @endif
                @if($courseWhatsappUrl)
                    <a href="{{ $courseWhatsappUrl }}" class="cx-link" target="_blank" rel="noopener noreferrer">{{ __('landing.educo.course_show.contact_team') }}</a>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- 2) Journey — one purpose strip --}}
<section class="cx-journey">
    <div class="lp-container">
        <p class="lp-eyebrow lp-reveal">{{ __('landing.educo.course_show.journey_eyebrow') }}</p>
        <div class="cx-journey__grid">
            <article class="cx-step lp-reveal">
                <span class="cx-step__n">01</span>
                <h2>{{ $hasPreviewVideos || $hasIntroVideo ? __('landing.educo.course_show.journey_1_title') : __('landing.educo.course_show.journey_1_title_no_preview') }}</h2>
                <p>{{ $hasPreviewVideos || $hasIntroVideo ? __('landing.educo.course_show.journey_1_desc') : __('landing.educo.course_show.journey_1_desc_no_preview') }}</p>
            </article>
            <article class="cx-step lp-reveal" style="--lp-delay:80ms">
                <span class="cx-step__n">02</span>
                <h2>{{ __('landing.educo.course_show.journey_2_title') }}</h2>
                <p>{{ __('landing.educo.course_show.journey_2_desc') }}</p>
            </article>
            <article class="cx-step lp-reveal" style="--lp-delay:160ms">
                <span class="cx-step__n">03</span>
                <h2>{{ __('landing.educo.course_show.journey_3_title') }}</h2>
                <p>{{ __('landing.educo.course_show.journey_3_desc') }}</p>
            </article>
        </div>
    </div>
</section>

{{-- 3) Preview and/or curriculum --}}
@if($hasPreviewVideos || $hasIntroVideo || $hasSections)
<section class="cx-theater" id="cx-preview">
    <div class="lp-container">
        @if($hasPreviewVideos || $hasIntroVideo)
            <div class="cx-theater__head lp-reveal">
                <div>
                    <p class="lp-eyebrow">{{ __('landing.educo.course_show.theater_eyebrow') }}</p>
                    <h2 class="lp-h1">{{ __('landing.educo.course_show.content_preview_title') }}</h2>
                    <p class="lp-lead" style="margin: 0.5rem 0 0; max-width: 34rem;">{{ __('landing.educo.course_show.content_preview_desc') }}</p>
                </div>
            </div>

            @if($hasIntroVideo)
                <div class="lp-reveal" style="margin-bottom: 1.75rem; border-radius: var(--lp-radius); overflow: hidden; background: #000;">
                    @include('partials.intro-video-embed', [
                        'url' => trim((string) $course->video_url),
                        'title' => __('public.intro_video_title'),
                        'autoplay' => false,
                    ])
                </div>
            @endif

            @if($hasPreviewVideos)
                <div x-data="coursePreviewPopup({{ (int) $course->id }})" class="lp-reveal">
                    <div class="cx-playlist">
                        @foreach($previewVideoLessons as $idx => $lesson)
                            @php
                                $isLocked = $idx >= $previewUnlockedCount;
                                $hasVideo = filled($lesson->recording_url ?? null) || filled($lesson->video_url ?? null);
                            @endphp
                            @if(!$isLocked && $hasVideo)
                                <button
                                    type="button"
                                    class="cx-track"
                                    @click="openPreview({{ (int) $lesson->id }}, @js($lesson->title))"
                                    :disabled="loading"
                                >
                                    <span class="cx-track__idx">{{ $idx + 1 }}</span>
                                    <span>
                                        <p class="cx-track__title">{{ $lesson->title }}</p>
                                        <p class="cx-track__meta">
                                            @if($lesson->duration_minutes)
                                                {{ $lesson->duration_minutes }} {{ __('landing.educo.course_show.minutes') }}
                                            @else
                                                {{ __('landing.educo.course_show.free_preview') }}
                                            @endif
                                        </p>
                                    </span>
                                    <span class="cx-track__action" x-text="loading && activeLessonId === {{ (int) $lesson->id }} ? @js(__('landing.educo.course_show.opening')) : @js(__('landing.educo.course_show.preview'))"></span>
                                </button>
                            @elseif($isLocked)
                                <a href="{{ $previewUnlockCta }}" class="cx-track is-locked" style="text-decoration:none;">
                                    <span class="cx-track__idx">{{ $idx + 1 }}</span>
                                    <span>
                                        <p class="cx-track__title">{{ $lesson->title }}</p>
                                        <p class="cx-track__meta">{{ __('landing.educo.course_show.locked_hint') }}</p>
                                    </span>
                                    <span class="cx-track__action">{{ __('landing.educo.course_show.unlock_buy') }}</span>
                                </a>
                            @else
                                <div class="cx-track is-locked">
                                    <span class="cx-track__idx">{{ $idx + 1 }}</span>
                                    <span>
                                        <p class="cx-track__title">{{ $lesson->title }}</p>
                                        <p class="cx-track__meta">{{ __('landing.educo.course_show.coming_soon') }}</p>
                                    </span>
                                    <span class="cx-track__action">{{ __('landing.educo.course_show.coming_soon') }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <template x-teleport="body">
                        <div
                            class="cx-modal-bg"
                            x-show="open"
                            x-cloak
                            @keydown.escape.window="closePopup()"
                            @click.self="closePopup()"
                            role="dialog"
                            aria-modal="true"
                        >
                            <div class="cx-modal" @click.stop>
                                <div class="cx-modal__bar">
                                    <div>
                                        <p class="lp-eyebrow" style="margin:0 0 0.25rem;">{{ __('landing.educo.course_show.free_preview') }}</p>
                                        <h4 x-text="title"></h4>
                                    </div>
                                    <button type="button" @click="closePopup()" aria-label="{{ __('landing.educo.course_show.close') }}">×</button>
                                </div>
                                <div class="cx-modal__body">
                                    <template x-if="error">
                                        <p style="color:#fff;padding:2rem;text-align:center;" x-text="error"></p>
                                    </template>
                                    <template x-if="!error && loading">
                                        <p style="color:#fff;padding:2rem;text-align:center;">{{ __('landing.educo.course_show.opening_player') }}</p>
                                    </template>
                                    <template x-if="!error && !loading && watchUrl">
                                        <iframe :src="watchUrl" allow="autoplay; fullscreen; encrypted-media" allowfullscreen></iframe>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            @endif
        @endif

        @if($hasSections)
            <div class="cx-curriculum" id="cx-curriculum" style="padding-block: {{ ($hasPreviewVideos || $hasIntroVideo) ? '2.5rem 0' : '0' }};" x-data="{ showAll: false, limit: 8 }">
                <p class="lp-eyebrow">{{ __('landing.educo.course_show.sections_title') }}</p>
                <h2 class="lp-h1" style="margin: 0.35rem 0 0; font-size: clamp(1.4rem, 3vw, 1.85rem);">
                    {{ __('landing.educo.course_show.curriculum_title') }}
                </h2>
                <ol class="cx-timeline">
                    @foreach($sections as $idx => $section)
                        <li x-show="showAll || {{ $idx }} < limit" @if($idx >= 8) x-cloak @endif>
                            <strong>{{ $section->title }}</strong>
                            <span>{{ __('landing.educo.course_show.section_index', ['n' => $idx + 1]) }}</span>
                        </li>
                    @endforeach
                </ol>
                @if($sections->count() > 8)
                    <button type="button" class="cx-link" style="color: var(--lp-blue); margin-top: 0.5rem; background:none; border:0; cursor:pointer; font:inherit;" @click="showAll = !showAll"
                        x-text="showAll ? @js(__('landing.educo.course_show.show_less')) : @js(__('landing.educo.course_show.show_all_sections', ['count' => $sections->count()]))"></button>
                @endif
            </div>
        @endif
    </div>
</section>
@endif

{{-- 4) Story / outcomes --}}
<section class="cx-story">
    <div class="lp-container cx-story__grid">
        <div class="lp-reveal">
            <p class="lp-eyebrow">{{ __('public.about_course') }}</p>
            <h2 class="lp-h1" style="margin: 0.35rem 0 1.25rem;">{{ __('landing.educo.course_show.story_title') }}</h2>
            <div class="cx-prose">
                <p>{{ $course->localized('description') ?: __('public.course_desc_fallback') }}</p>
                @if($course->objectives)
                    <p class="muted">{{ $course->objectives }}</p>
                @endif
                @if($course->requirements)
                    <p class="lp-eyebrow" style="margin-top: 1.5rem;">{{ __('public.requirements') }}</p>
                    <p class="muted">{{ $course->requirements }}</p>
                @endif
            </div>
        </div>

        <aside class="lp-reveal" style="--lp-delay:100ms">
            @if($learnPoints->isNotEmpty())
                <div class="cx-aside-block" x-data="{ expanded: false, limit: {{ $learnPreviewCount }} }">
                    <h3>{{ __('landing.educo.course_show.what_you_learn') }}</h3>
                    <ul class="cx-outcomes">
                        @foreach($learnPoints as $idx => $point)
                            <li x-show="expanded || {{ $idx }} < limit" @if($idx >= $learnPreviewCount) x-cloak @endif>{{ $point }}</li>
                        @endforeach
                    </ul>
                    @if($learnPoints->count() > $learnPreviewCount)
                        <button type="button" class="cx-link" style="color:var(--lp-blue);margin-top:0.85rem;background:none;border:0;cursor:pointer;font:inherit;"
                            @click="expanded = !expanded"
                            x-text="expanded ? @js(__('landing.educo.course_show.show_less')) : @js(__('landing.educo.course_show.show_more', ['count' => $learnPoints->count() - $learnPreviewCount]))"></button>
                    @endif
                </div>
            @endif

        </aside>
    </div>
</section>

{{-- 4b) Important course info from admin «بيانات الكورس» --}}
@if($infoBlocks->isNotEmpty())
<section class="cx-info-section" id="cx-important-info">
    <div class="lp-container lp-reveal">
        <p class="lp-eyebrow">{{ __('landing.educo.course_show.important_info') }}</p>
        <h2 class="lp-h1" style="margin: 0.35rem 0 0;">{{ __('landing.educo.course_show.important_info_title') }}</h2>
        <div class="cx-info-grid">
            @foreach($infoBlocks as $block)
                <article class="cx-info-card">
                    <h3>{{ __('landing.educo.course_show.'.$block['key']) }}</h3>
                    <p>{{ $block['value'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- 5) Social proof --}}
<section class="cx-reviews" x-data="courseReviewsGallery()">
    <div class="lp-container">
        <p class="lp-eyebrow">{{ __('landing.educo.course_show.reviews_title') }}</p>
        @if(($reviewsCount ?? 0) > 0)
            <p class="lp-lead" style="margin: 0.35rem 0 1.5rem;">
                {{ number_format((float) ($reviewsAvg ?? 0), 1) }} / 5 · {{ __('landing.educo.course_show.reviews_count', ['count' => $reviewsCount]) }}
            </p>
        @endif

        @auth
            @if($isEnrolled ?? false)
                <form action="{{ route('public.course.reviews.store', $course->id) }}" method="POST" class="cx-review-form lp-reveal">
                    @csrf
                    <label>
                        <span class="lp-eyebrow">{{ __('landing.educo.course_show.your_rating') }}</span>
                        <select name="rating" required>
                            <option value="">{{ __('landing.educo.course_show.choose') }}</option>
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" @selected((string) old('rating') === (string) $i)>{{ $i }} / 5</option>
                            @endfor
                        </select>
                    </label>
                    <label>
                        <span class="lp-eyebrow">{{ __('landing.educo.course_show.write_review') }}</span>
                        <textarea name="comment" rows="3" required placeholder="{{ __('landing.educo.course_show.review_placeholder') }}">{{ old('comment') }}</textarea>
                    </label>
                    <button type="submit" class="lp-btn-yellow" style="border:0;cursor:pointer;justify-self:start;">{{ __('landing.educo.course_show.publish') }}</button>
                </form>
            @endif
        @endauth

        @if(isset($approvedReviews) && $approvedReviews->count() > 0)
            @php $firstReview = $approvedReviews->first(); @endphp
            <blockquote class="cx-quote lp-reveal">
                “{{ \Illuminate\Support\Str::limit($firstReview->body_text ?: ($firstReview->comment ?? ''), 220) }}”
            </blockquote>
            <p class="cx-quote-meta">
                {{ optional($firstReview->user)->name ?? __('landing.educo.courses.fallback_instructor') }}
                @if($firstReview->rating)
                    · {{ $firstReview->rating }}/5
                @endif
            </p>

            @if($approvedReviews->count() > 1)
                <div class="course-reviews-rail lp-reveal" x-ref="rail" style="margin-top: 1.75rem;">
                    @foreach($approvedReviews->skip(1) as $r)
                        @php $hasImage = filled($r->image_path); @endphp
                        <article class="course-review-card {{ $hasImage ? 'has-image' : 'is-text-only' }}">
                            @if($hasImage)
                                <button type="button" class="review-media" @click="openLightbox(@js($r->image_url))">
                                    <img src="{{ $r->image_url }}" alt="" loading="lazy">
                                </button>
                            @endif
                            <div class="review-meta">
                                <p style="margin:0;font-weight:700;font-size:0.85rem;">{{ optional($r->user)->name }}</p>
                                <p style="margin:0.35rem 0 0;font-size:0.8rem;color:var(--lp-text);">{{ \Illuminate\Support\Str::limit($r->body_text ?: ($r->comment ?? ''), 90) }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
                <template x-teleport="body">
                    <div class="course-review-lightbox" x-show="lightbox" x-cloak @click.self="closeLightbox()" @keydown.escape.window="closeLightbox()">
                        <img :src="lightbox" alt="">
                    </div>
                </template>
            @endif
        @else
            <p class="lp-lead">{{ __('landing.educo.course_show.no_reviews') }}</p>
        @endif
    </div>
</section>

{{-- 6) Related --}}
@if(isset($relatedCourses) && $relatedCourses->count() > 0)
<section class="cx-related">
    <div class="lp-container">
        <p class="lp-eyebrow">{{ __('landing.educo.course_show.related_eyebrow') }}</p>
        <h2 class="lp-h1" style="margin: 0.35rem 0 1.5rem;">{{ __('landing.educo.course_show.related_title') }}</h2>
        <div class="lp-courses__grid">
            @foreach($relatedCourses->take(3) as $related)
                @include('components.landing.course-card', ['course' => $related, 'delay' => $loop->index * 60])
            @endforeach
        </div>
        <div style="margin-top: 2rem;">
            <a href="{{ route('public.courses') }}" class="lp-link-underline">{{ __('landing.educo.course_show.browse_all') }}</a>
        </div>
    </div>
</section>
@endif

{{-- Mobile purchase dock — one control, not floating stickers --}}
<div class="cx-dock" id="cx-dock" data-threshold="420">
    <div class="cx-dock__inner">
        <div class="cx-dock__price">
            @if($isPaid)
                {{ number_format($course->effectivePrice(), 0) }} {{ __('landing.educo.course_show.egp') }}
            @else
                {{ __('landing.educo.course_show.free') }}
            @endif
            <small>{{ \Illuminate\Support\Str::limit($courseTitle, 28) }}</small>
        </div>
        <a href="{{ $primaryUrl }}" class="lp-btn-yellow">{{ $primaryLabel }}</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function coursePreviewPopup(courseId) {
        return {
            open: false,
            loading: false,
            watchUrl: '',
            title: '',
            error: '',
            activeLessonId: null,
            urlCache: {},
            async openPreview(lessonId, title) {
                this.title = title || @json(__('landing.educo.course_show.preview'));
                this.error = '';
                this.activeLessonId = lessonId;
                this.open = true;
                document.body.style.overflow = 'hidden';
                if (this.urlCache[lessonId]) {
                    this.watchUrl = this.urlCache[lessonId];
                    this.loading = false;
                    return;
                }
                this.loading = true;
                this.watchUrl = '';
                try {
                    const res = await fetch('/course/' + courseId + '/preview-watch-url/' + lessonId, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin'
                    });
                    const data = await res.json().catch(function () { return {}; });
                    if (!res.ok || !data.watch_url) {
                        throw new Error(data.error || @json(__('landing.educo.course_show.preview_error')));
                    }
                    this.urlCache[lessonId] = data.watch_url;
                    this.watchUrl = data.watch_url;
                } catch (e) {
                    this.error = (e && e.message) ? e.message : @json(__('landing.educo.course_show.preview_error'));
                    this.watchUrl = '';
                } finally {
                    this.loading = false;
                }
            },
            closePopup() {
                this.open = false;
                this.watchUrl = '';
                this.error = '';
                this.activeLessonId = null;
                this.loading = false;
                document.body.style.overflow = '';
            }
        };
    }

    function courseReviewsGallery() {
        return {
            lightbox: null,
            openLightbox(url) {
                this.lightbox = url || null;
                if (this.lightbox) document.body.style.overflow = 'hidden';
            },
            closeLightbox() {
                this.lightbox = null;
                document.body.style.overflow = '';
            }
        };
    }

    (function () {
        var dock = document.getElementById('cx-dock');
        if (!dock) return;
        var threshold = parseInt(dock.getAttribute('data-threshold') || '420', 10);
        function update() {
            dock.classList.toggle('is-visible', window.scrollY > threshold);
        }
        window.addEventListener('scroll', update, { passive: true });
        update();
    })();
</script>
<style>
    .course-reviews-rail {
        display: flex;
        gap: 0.85rem;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        padding-bottom: 0.5rem;
        -webkit-overflow-scrolling: touch;
    }
    .course-review-card {
        flex: 0 0 auto;
        width: min(58vw, 200px);
        scroll-snap-align: start;
        border-radius: var(--lp-radius);
        overflow: hidden;
        background: #fff;
        border: 1px solid rgba(63, 51, 86,0.08);
    }
    .course-review-card .review-media {
        display: block; width: 100%; height: 180px; padding: 0; border: 0; background: #111; cursor: zoom-in;
    }
    .course-review-card .review-media img { width: 100%; height: 100%; object-fit: cover; }
    .course-review-card .review-meta { padding: 0.65rem 0.75rem; }
    .course-review-card.is-text-only { background: #fff; min-height: 140px; }
    .course-review-lightbox {
        position: fixed; inset: 0; z-index: 100000; background: rgba(63, 51, 86,0.9);
        display: flex; align-items: center; justify-content: center; padding: 1rem;
    }
    .course-review-lightbox img { max-width: min(960px, 100%); max-height: 90vh; object-fit: contain; }
    [x-cloak] { display: none !important; }
</style>
@endpush
