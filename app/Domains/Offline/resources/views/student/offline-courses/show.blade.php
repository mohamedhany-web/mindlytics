@extends('layouts.student-dashboard')

@php
    use App\Support\StudentFigmaAssets;
    use Illuminate\Support\Str;

    $isOnlineChannel = ($channel ?? 'offline') === 'online';
    $examsModule = $isOnlineChannel ? 'online' : 'offline';
    $routeGroup = $studentRouteGroup ?? 'student.offline-courses';
    $sp = StudentFigmaAssets::urls();
    $stats = $hubStats ?? ['resources' => 0, 'lectures' => 0, 'activities' => 0, 'exams' => 0, 'pending_activities' => 0];

    $progressPct = max(0, min(100, (int) round((float) $enrollment->progress)));
    $ringDeg = (int) round($progressPct * 3.6);
    $isDone = $progressPct >= 100;
    $bubbleColors = ['#f9e4d7', '#d7e8f9', '#d7eef5', '#f9f0d7', '#dcdef2'];

    $primaryHref = $stats['pending_activities'] > 0
        ? '#activities-required'
        : route($routeGroup . '.schedule', $offlineCourse);
    $primaryLabel = $stats['pending_activities'] > 0
        ? __('student.oc_hub_view_activities')
        : __('student.oc_open_schedule');

    $quickLinks = [
        ['route' => 'curriculum', 'icon' => 'icon-path.svg', 'bubble' => 'var(--sp-sky)', 'title' => __('student.oc_tile_curriculum'), 'meta' => __('student.oc_badge_curriculum')],
        ['route' => 'schedule', 'icon' => 'icon-calendar.svg', 'bubble' => 'var(--sp-lilac)', 'title' => __('student.oc_tile_schedule'), 'meta' => __('student.oc_badge_schedule')],
        ['route' => 'lectures', 'icon' => 'icon-classes.svg', 'bubble' => 'var(--sp-mint)', 'title' => __('student.oc_tile_lectures'), 'meta' => (string) $stats['lectures']],
        ['route' => 'resources', 'icon' => 'icon-messages.svg', 'bubble' => 'var(--sp-peach)', 'title' => __('student.oc_tile_resources'), 'meta' => (string) $stats['resources']],
        ['route' => '#activities-required', 'icon' => 'icon-star.svg', 'bubble' => 'var(--sp-amber-soft)', 'title' => __('student.oc_tile_activities'), 'meta' => (string) $stats['pending_activities']],
        ['route' => 'exams', 'icon' => 'icon-exams.svg', 'bubble' => 'var(--sp-badge-done)', 'title' => __('student.oc_tile_exams'), 'meta' => (string) $stats['exams']],
    ];
@endphp

@section('title', $offlineCourse->title)
@section('header', $offlineCourse->title)

@push('styles')
<style>
    .sp-oc-hub-hero {
        background: #2f2e43;
        border-radius: 30px;
        color: #fff;
        padding: 28px 24px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--sp-shadow);
    }
    .sp-oc-hub-hero::before {
        content: '';
        position: absolute;
        inset-inline-end: -40px;
        top: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(174,217,234,0.28), transparent 70%);
        pointer-events: none;
    }
    .sp-oc-hub-hero::after {
        content: '';
        position: absolute;
        inset-inline-start: -30px;
        bottom: -80px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(249,228,215,0.18), transparent 70%);
        pointer-events: none;
    }
    .sp-oc-progress-ring {
        width: 132px;
        height: 132px;
        border-radius: 50%;
        background: conic-gradient(var(--sp-accent) {{ $ringDeg }}deg, rgba(255,255,255,0.12) 0deg);
        display: grid;
        place-items: center;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
    }
    .sp-oc-progress-ring-inner {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: #2f2e43;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .sp-oc-hub-sticky { position: sticky; top: 12px; }
    @media (max-width: 1023px) {
        .sp-oc-hub-sticky { position: static; }
        .sp-oc-progress-ring { width: 112px; height: 112px; }
        .sp-oc-progress-ring-inner { width: 84px; height: 84px; }
    }
</style>
@endpush

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <a href="{{ route($routeGroup . '.index') }}" class="sp-link inline-flex items-center gap-2">
            <img src="{{ $sp['chevron'] ?? StudentFigmaAssets::url('icon-chevron.svg') }}" alt="" class="size-4 rotate-180 rtl:rotate-0">
            {{ $isOnlineChannel ? __('student.online_courses_title') : __('student.offline_courses_title') }}
        </a>
        <span class="sp-pill {{ $isOnlineChannel ? 'sp-pill--progress' : 'sp-pill--upcoming' }}">
            {{ $isOnlineChannel ? __('student.exam_source_online') : __('student.exam_source_offline') }}
        </span>
    </div>

    {{-- Hero --}}
    <section class="sp-oc-hub-hero">
        <div class="relative z-[1] flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-8">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white/60 m-0 mb-2">{{ __('student.oc_hub_eyebrow') }}</p>
                <h2 class="text-2xl sm:text-[28px] font-extrabold m-0 leading-tight">{{ $offlineCourse->title }}</h2>
                <p class="text-sm text-white/70 m-0 mt-3 flex flex-wrap gap-x-2 gap-y-1">
                    <span>{{ $offlineCourse->instructor->name ?? '—' }}</span>
                    @if($offlineCourse->locationModel || $offlineCourse->location)
                        <span class="opacity-40">·</span>
                        <span>{{ $offlineCourse->locationModel->name ?? $offlineCourse->location }}</span>
                    @endif
                    @if($enrollment->group)
                        <span class="opacity-40">·</span>
                        <span>{{ __('student.oc_group') }}: {{ $enrollment->group->name }}</span>
                    @endif
                </p>
                @if(filled($offlineCourse->description))
                    <p class="text-sm text-white/55 m-0 mt-3 max-w-2xl leading-relaxed line-clamp-2">{{ Str::limit(strip_tags($offlineCourse->description), 160) }}</p>
                @endif
                <div class="flex flex-wrap gap-3 mt-6">
                    <a href="{{ $primaryHref }}" class="sp-promo-btn !mt-0 !text-[var(--sp-accent-text)]">{{ $primaryLabel }}</a>
                    <a href="{{ route($routeGroup . '.curriculum', $offlineCourse) }}" class="inline-flex items-center justify-center rounded-[20px] bg-white/10 hover:bg-white/15 px-5 py-3.5 text-sm font-extrabold text-white transition">
                        {{ __('student.oc_tile_curriculum') }}
                    </a>
                    @if($stats['lectures'] > 0)
                        <a href="{{ route($routeGroup . '.lectures', $offlineCourse) }}" class="inline-flex items-center justify-center rounded-[20px] bg-white/10 hover:bg-white/15 px-5 py-3.5 text-sm font-extrabold text-white transition">
                            {{ __('student.oc_tile_lectures') }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-5 shrink-0">
                <div class="sp-oc-progress-ring" role="img" aria-label="{{ __('student.percent_complete', ['pct' => $progressPct]) }}">
                    <div class="sp-oc-progress-ring-inner">
                        <span class="text-2xl font-black text-[var(--sp-accent)] leading-none">{{ $progressPct }}%</span>
                        <span class="text-[10px] font-bold text-white/50 mt-1 uppercase tracking-wide">{{ __('student.oc_progress') }}</span>
                    </div>
                </div>
                <div class="hidden sm:grid gap-2 min-w-[120px]">
                    <div class="rounded-2xl bg-white/8 px-3 py-2.5 border border-white/10">
                        <p class="text-[11px] font-bold text-white/50 m-0">{{ __('student.oc_tile_lectures') }}</p>
                        <p class="text-lg font-black m-0 text-white">{{ $stats['lectures'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/8 px-3 py-2.5 border border-white/10">
                        <p class="text-[11px] font-bold text-white/50 m-0">{{ __('student.oc_pending_activities') }}</p>
                        <p class="text-lg font-black m-0 text-white">{{ $stats['pending_activities'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="grid gap-5 xl:grid-cols-[minmax(0,1.7fr)_minmax(280px,1fr)]">
        {{-- Main --}}
        <div class="space-y-5 min-w-0">
            <section class="sp-card p-5 sm:p-6">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="sp-section-title m-0">{{ __('student.oc_hub_quick_access') }}</h3>
                        <p class="text-sm text-[var(--sp-muted)] m-0 mt-1">{{ __('student.oc_hub_quick_access_hint') }}</p>
                    </div>
                </div>
                <div class="grid gap-2 sm:grid-cols-2">
                    @foreach($quickLinks as $index => $link)
                        @php
                            $href = $link['route'] === 'exams'
                                ? route('student.exams.index', ['module' => $examsModule])
                                : ($link['route'] === '#activities-required'
                                    ? '#activities-required'
                                    : route($routeGroup . '.' . $link['route'], $offlineCourse));
                        @endphp
                        <a href="{{ $href }}" class="sp-process-row !shadow-none border border-[#f0f0ec] hover:border-[var(--sp-accent)] transition-colors">
                            <span class="sp-icon-bubble !w-10 !h-10" style="background:{{ $bubbleColors[$index % count($bubbleColors)] }}">
                                <x-student.figma-icon :name="$link['icon']" box="size-5" />
                            </span>
                            <span class="flex-1 min-w-0">
                                <span class="block font-extrabold text-sm truncate">{{ $link['title'] }}</span>
                                <span class="block text-xs font-bold text-[var(--sp-muted)] mt-0.5">{{ __('student.oc_hub_open_section') }}</span>
                            </span>
                            <span class="sp-pill sp-pill--progress !py-1.5 !px-2.5 !text-xs shrink-0">{{ $link['meta'] }}</span>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="sp-card p-5 sm:p-6 space-y-4" id="activities-required">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="sp-icon-bubble shrink-0" style="background:var(--sp-peach)">
                            <x-student.figma-icon name="icon-star.svg" />
                        </span>
                        <div>
                            <h3 class="sp-section-title m-0">{{ __('student.oc_pending_activities') }}</h3>
                            <p class="text-xs text-[var(--sp-muted)] m-0 mt-1">{{ __('student.oc_hub_activities_hint') }}</p>
                        </div>
                    </div>
                    @if($stats['pending_activities'] > 0)
                        <span class="sp-pill sp-pill--upcoming">{{ $stats['pending_activities'] }}</span>
                    @endif
                </div>

                @if($pendingActivities->count() > 0)
                    <div class="space-y-2">
                        @foreach($pendingActivities as $activity)
                            <a href="{{ route($routeGroup . '.activities.show', [$offlineCourse, $activity]) }}"
                               class="sp-process-row !shadow-none border border-[#f0f0ec] hover:border-[var(--sp-accent)] transition-colors">
                                <span class="sp-icon-bubble !w-10 !h-10" style="background:var(--sp-amber-soft)">
                                    <x-student.figma-icon name="icon-star.svg" box="size-5" />
                                </span>
                                <span class="flex-1 min-w-0">
                                    <span class="block font-extrabold text-sm truncate">{{ $activity->title }}</span>
                                    <span class="block text-xs font-bold text-[var(--sp-muted)] mt-0.5">
                                        {{ $activity->type }}
                                        @if($activity->due_date) · {{ $activity->due_date->format('Y/m/d') }}@endif
                                        · {{ $activity->max_score }} {{ __('student.oc_points') }}
                                    </span>
                                </span>
                                <span class="sp-pill sp-pill--upcoming shrink-0">{{ __('student.oc_submit_activity') }}</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-[20px] bg-[#f7f7f5] px-4 py-8 text-center">
                        <p class="font-extrabold text-sm m-0">{{ __('student.oc_no_pending_activities') }}</p>
                    </div>
                @endif
            </section>

            @if($completedActivities->count() > 0)
                <section class="sp-card p-5 sm:p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="sp-icon-bubble shrink-0" style="background:var(--sp-mint)">
                            <x-student.figma-icon name="icon-certificates.svg" />
                        </span>
                        <h3 class="sp-section-title m-0">{{ __('student.oc_completed_activities') }}</h3>
                    </div>
                    <div class="space-y-2">
                        @foreach($completedActivities as $activity)
                            @php $submission = $activity->submissions->firstWhere('student_id', auth()->id()); @endphp
                            <a href="{{ route($routeGroup . '.activities.show', [$offlineCourse, $activity]) }}"
                               class="sp-process-row !shadow-none border border-[#f0f0ec]">
                                <span class="sp-icon-bubble !w-10 !h-10" style="background:var(--sp-mint)">
                                    <x-student.figma-icon name="icon-certificates.svg" box="size-5" />
                                </span>
                                <span class="flex-1 min-w-0">
                                    <span class="block font-extrabold text-sm truncate">{{ $activity->title }}</span>
                                    @if($submission && $submission->score !== null)
                                        <span class="block text-xs font-bold text-[var(--sp-muted)] mt-0.5">
                                            {{ __('student.oc_graded_score', ['score' => $submission->score, 'max' => $activity->max_score]) }}
                                        </span>
                                    @endif
                                </span>
                                <span class="sp-pill sp-pill--done shrink-0">{{ __('student.completed_badge') }}</span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if(filled($offlineCourse->description))
                <section class="sp-card p-5 sm:p-6">
                    <h3 class="sp-section-title mb-3">{{ __('student.oc_about') }}</h3>
                    <p class="text-sm text-[var(--sp-muted)] leading-relaxed m-0 whitespace-pre-line">{{ $offlineCourse->description }}</p>
                    <a href="{{ route($routeGroup . '.curriculum', $offlineCourse) }}" class="sp-link text-sm font-extrabold inline-block mt-3">{{ __('student.oc_view_full_curriculum') }}</a>
                </section>
            @endif
        </div>

        {{-- Sidebar rail --}}
        <aside class="space-y-4 min-w-0 sp-oc-hub-sticky">
            <section class="sp-card p-5">
                <p class="text-xs font-bold text-[var(--sp-muted)] uppercase tracking-wide m-0 mb-2">{{ __('student.next_step') }}</p>
                <h3 class="font-extrabold text-base m-0 mb-3 leading-snug">
                    {{ $stats['pending_activities'] > 0 ? __('student.oc_hub_next_activity') : __('student.oc_hub_explore_course') }}
                </h3>
                <a href="{{ $primaryHref }}" class="sp-promo-btn !mt-0 w-full !text-[var(--sp-accent-text)]">{{ $primaryLabel }}</a>
            </section>

            @if($nextSession ?? null)
                <section class="sp-card p-5 space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="sp-icon-bubble shrink-0" style="background:var(--sp-lilac)">
                            <x-student.figma-icon name="icon-calendar.svg" />
                        </span>
                        <div>
                            <p class="text-xs font-bold text-[var(--sp-muted)] m-0">{{ __('student.oc_hub_next_session') }}</p>
                            <p class="font-extrabold text-sm m-0 mt-0.5">{{ $nextSession->session_date->translatedFormat('l j F') }}</p>
                        </div>
                    </div>
                    @if(filled($nextSession->title))
                        <p class="text-sm font-bold m-0">{{ $nextSession->title }}</p>
                    @endif
                    <a href="{{ route($routeGroup . '.schedule', $offlineCourse) }}" class="sp-link text-sm font-extrabold">{{ __('student.oc_open_schedule') }}</a>
                </section>
            @endif

            <section class="sp-card p-5">
                <div class="flex items-center gap-3 mb-4">
                    @if($offlineCourse->instructor?->profile_image_url)
                        <img src="{{ $offlineCourse->instructor->profile_image_url }}" alt="" class="w-12 h-12 rounded-[16px] object-cover">
                    @else
                        <span class="sp-icon-bubble shrink-0" style="background:var(--sp-peach)">
                            <x-student.figma-icon name="icon-profile.svg" />
                        </span>
                    @endif
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-[var(--sp-muted)] m-0">{{ __('student.oc_instructor') }}</p>
                        <p class="font-extrabold text-[15px] m-0 truncate">{{ $offlineCourse->instructor?->name ?? '—' }}</p>
                    </div>
                </div>
                <div class="space-y-2">
                    @if($offlineCourse->start_date)
                        <div class="flex items-center justify-between gap-3 rounded-[14px] bg-[#f7f7f5] px-3 py-2.5">
                            <span class="text-sm font-bold text-[var(--sp-muted)]">{{ __('student.oc_start_date') }}</span>
                            <span class="text-sm font-extrabold">{{ $offlineCourse->start_date->format('Y/m/d') }}</span>
                        </div>
                    @endif
                    @if($enrollment->group)
                        <div class="flex items-center justify-between gap-3 rounded-[14px] bg-[#f7f7f5] px-3 py-2.5">
                            <span class="text-sm font-bold text-[var(--sp-muted)]">{{ __('student.oc_group') }}</span>
                            <span class="text-sm font-extrabold truncate">{{ $enrollment->group->name }}</span>
                        </div>
                    @endif
                </div>
            </section>

            @if((float) $enrollment->total_amount > 0)
                <section class="sp-card p-5 space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="sp-icon-bubble shrink-0" style="background:var(--sp-mint)">
                            <x-student.figma-icon name="icon-wallet.svg" />
                        </span>
                        <h3 class="font-extrabold text-base m-0">{{ __('student.oc_payment_title') }}</h3>
                    </div>
                    @php
                        $paymentLabels = [
                            'paid' => __('student.oc_payment_paid'),
                            'partial' => __('student.oc_payment_partial'),
                            'unpaid' => __('student.oc_payment_unpaid'),
                        ];
                    @endphp
                    <div class="rounded-[16px] px-3 py-2.5" style="background:var(--sp-mint)">
                        <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.oc_payment_status') }}</p>
                        <p class="text-sm font-extrabold m-0">{{ $paymentLabels[$enrollment->payment_status] ?? '—' }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="rounded-[14px] bg-[#f7f7f5] px-3 py-2.5 text-center">
                            <p class="text-[10px] font-bold text-[var(--sp-muted)] m-0">{{ __('student.oc_paid_amount') }}</p>
                            <p class="text-sm font-extrabold m-0 mt-1">{{ number_format($enrollment->paid_amount, 0) }}</p>
                        </div>
                        <div class="rounded-[14px] bg-[#f7f7f5] px-3 py-2.5 text-center">
                            <p class="text-[10px] font-bold text-[var(--sp-muted)] m-0">{{ __('student.oc_remaining_amount') }}</p>
                            <p class="text-sm font-extrabold m-0 mt-1">{{ number_format($enrollment->remaining_amount, 0) }}</p>
                        </div>
                    </div>
                </section>
            @endif

            <section class="sp-card p-5">
                <h3 class="sp-section-title mb-3">{{ __('student.oc_hub_stats') }}</h3>
                <div class="grid grid-cols-2 gap-2">
                    <div class="rounded-[16px] bg-[var(--sp-mint)] p-3 text-center">
                        <p class="text-xl font-black text-[var(--sp-accent-text)] m-0">{{ $stats['lectures'] }}</p>
                        <p class="text-[10px] font-bold text-[var(--sp-muted)] m-0 mt-1">{{ __('student.oc_tile_lectures') }}</p>
                    </div>
                    <div class="rounded-[16px] bg-[var(--sp-lilac)] p-3 text-center">
                        <p class="text-xl font-black text-[var(--sp-accent-text)] m-0">{{ $stats['resources'] }}</p>
                        <p class="text-[10px] font-bold text-[var(--sp-muted)] m-0 mt-1">{{ __('student.oc_tile_resources') }}</p>
                    </div>
                    <div class="rounded-[16px] bg-[var(--sp-amber-soft)] p-3 text-center">
                        <p class="text-xl font-black text-[var(--sp-accent-text)] m-0">{{ $stats['exams'] }}</p>
                        <p class="text-[10px] font-bold text-[var(--sp-muted)] m-0 mt-1">{{ __('student.oc_tile_exams') }}</p>
                    </div>
                    <div class="rounded-[16px] bg-[var(--sp-peach)] p-3 text-center">
                        <p class="text-xl font-black text-[var(--sp-accent-text)] m-0">{{ $progressPct }}%</p>
                        <p class="text-[10px] font-bold text-[var(--sp-muted)] m-0 mt-1">{{ __('student.oc_progress') }}</p>
                    </div>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
