@extends('layouts.student-dashboard')

@php
    use App\Support\StudentFigmaAssets;
    use Illuminate\Support\Str;

    $isOnlineChannel = ($channel ?? 'offline') === 'online';
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $stats = $curriculumStats ?? ['sections' => 0, 'items' => 0];
    $examsModule = $isOnlineChannel ? 'online' : 'offline';
    $sp = StudentFigmaAssets::urls();
@endphp

@section('title', __('student.oc_curriculum_title') . ' — ' . $offlineCourse->title)
@section('header', __('student.oc_curriculum_title'))

@push('styles')
<style>
    .sp-oc-curriculum-hero {
        background: #2f2e43;
        border-radius: 30px;
        color: #fff;
        padding: 28px 24px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--sp-shadow);
    }
    .sp-oc-curriculum-hero::before {
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
    .sp-oc-curriculum-sticky { position: sticky; top: 12px; }
    .sp-oc-section-details > summary { list-style: none; cursor: pointer; }
    .sp-oc-section-details > summary::-webkit-details-marker { display: none; }
    .sp-oc-section-details .sp-oc-section-chevron { transition: transform 0.18s ease; }
    .sp-oc-section-details[open] > summary .sp-oc-section-chevron { transform: rotate(180deg); }
    @media (max-width: 1023px) {
        .sp-oc-curriculum-sticky { position: static; }
    }
</style>
@endpush

@section('content')
<div class="space-y-5">
    <nav class="flex flex-wrap items-center gap-2 text-sm font-bold text-[var(--sp-muted)]">
        <a href="{{ route($sg . '.index') }}" class="sp-link">{{ $isOnlineChannel ? __('student.online_courses_title') : __('student.offline_courses_title') }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <a href="{{ route($sg . '.show', $offlineCourse) }}" class="sp-link truncate max-w-[40vw]">{{ $offlineCourse->title }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <span class="text-[var(--sp-text)]">{{ __('student.oc_curriculum_title') }}</span>
    </nav>

    {{-- Hero --}}
    <section class="sp-oc-curriculum-hero">
        <div class="relative z-[1] flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-8">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white/60 m-0 mb-2">{{ __('student.oc_curriculum_eyebrow') }}</p>
                <h2 class="text-2xl sm:text-[28px] font-extrabold m-0 leading-tight">{{ $offlineCourse->title }}</h2>
                <p class="text-sm text-white/70 m-0 mt-3 max-w-2xl leading-relaxed">{{ __('student.oc_curriculum_subtitle') }}</p>
                <div class="flex flex-wrap gap-2 mt-4">
                    <span class="sp-pill sp-pill--progress">{{ $isOnlineChannel ? __('student.exam_source_online') : __('student.exam_source_offline') }}</span>
                    @if($enrollment->group)
                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-white/80">{{ __('student.oc_group') }}: {{ $enrollment->group->name }}</span>
                    @endif
                </div>
                <div class="flex flex-wrap gap-3 mt-6">
                    <a href="{{ route($sg . '.show', $offlineCourse) }}" class="inline-flex items-center justify-center rounded-[20px] bg-white/10 hover:bg-white/15 px-5 py-3.5 text-sm font-extrabold text-white transition">
                        {{ __('student.oc_back_course') }}
                    </a>
                    <a href="{{ route($sg . '.schedule', $offlineCourse) }}" class="sp-promo-btn !mt-0 !text-[var(--sp-accent-text)]">{{ __('student.oc_tile_schedule') }}</a>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 sm:gap-3 shrink-0 min-w-[200px] lg:min-w-[240px]">
                <div class="rounded-2xl bg-white/8 px-4 py-3 border border-white/10 text-center">
                    <p class="text-2xl font-black text-[var(--sp-accent)] m-0">{{ $stats['sections'] }}</p>
                    <p class="text-[10px] font-bold text-white/50 m-0 mt-1 uppercase tracking-wide">{{ __('student.oc_stat_sections') }}</p>
                </div>
                <div class="rounded-2xl bg-white/8 px-4 py-3 border border-white/10 text-center">
                    <p class="text-2xl font-black text-[var(--sp-accent)] m-0">{{ $stats['items'] }}</p>
                    <p class="text-[10px] font-bold text-white/50 m-0 mt-1 uppercase tracking-wide">{{ __('student.oc_stat_items') }}</p>
                </div>
            </div>
        </div>
    </section>

    <div class="grid gap-5 xl:grid-cols-[minmax(0,1.7fr)_minmax(280px,1fr)]">
        {{-- Main --}}
        <div class="space-y-5 min-w-0">
            @if(filled($offlineCourse->description))
                <section class="sp-card overflow-hidden">
                    <div class="flex items-center gap-3 px-5 py-4 border-b border-black/5 bg-[#f7f7f5]">
                        <span class="sp-icon-bubble shrink-0" style="background:var(--sp-sky)">
                            <x-student.figma-icon name="icon-courses.svg" />
                        </span>
                        <h3 class="font-extrabold text-base m-0">{{ __('student.oc_course_description') }}</h3>
                    </div>
                    <div class="p-5 sm:p-6 text-sm leading-relaxed whitespace-pre-wrap break-words text-[var(--sp-text)]">{{ $offlineCourse->description }}</div>
                </section>
            @endif

            @if(filled($offlineCourse->notes))
                <section class="sp-card overflow-hidden">
                    <div class="flex items-center gap-3 px-5 py-4 border-b border-black/5" style="background:var(--sp-amber-soft)">
                        <span class="sp-icon-bubble shrink-0" style="background:var(--sp-peach)">
                            <x-student.figma-icon name="icon-messages.svg" />
                        </span>
                        <h3 class="font-extrabold text-base m-0">{{ __('student.oc_extra_notes') }}</h3>
                    </div>
                    <div class="p-5 sm:p-6 text-sm leading-relaxed whitespace-pre-wrap break-words text-[var(--sp-text)]">{{ $offlineCourse->notes }}</div>
                </section>
            @endif

            <section class="space-y-3">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <div class="flex items-center gap-3">
                        <span class="sp-icon-bubble shrink-0" style="background:var(--sp-mint)">
                            <x-student.figma-icon name="icon-path.svg" />
                        </span>
                        <div>
                            <h3 class="sp-section-title m-0">{{ __('student.oc_curriculum_structure') }}</h3>
                            <p class="text-xs text-[var(--sp-muted)] m-0 mt-1">{{ __('student.oc_curriculum_structure_hint') }}</p>
                        </div>
                    </div>
                    @if($stats['items'] > 0)
                        <span class="sp-pill sp-pill--progress">{{ __('student.oc_stat_items') }}: {{ $stats['items'] }}</span>
                    @endif
                </div>

                @if($curriculumRoots->isNotEmpty())
                    @include('student.offline-courses.partials.curriculum-sections', [
                        'sections' => $curriculumRoots,
                        'offlineCourse' => $offlineCourse,
                        'channel' => $channel,
                        'studentRouteGroup' => $studentRouteGroup,
                        'depth' => 0,
                    ])
                @else
                    <div class="sp-card p-10 text-center">
                        <span class="sp-icon-bubble mx-auto mb-3" style="background:var(--sp-sky)">
                            <x-student.figma-icon name="icon-path.svg" />
                        </span>
                        <p class="font-extrabold m-0">{{ __('student.oc_no_curriculum') }}</p>
                        <p class="text-sm text-[var(--sp-muted)] m-0 mt-2">{{ __('student.oc_no_curriculum_hint') }}</p>
                    </div>
                @endif
            </section>
        </div>

        {{-- Sidebar --}}
        <aside class="space-y-4 min-w-0 sp-oc-curriculum-sticky">
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
                @if(filled($offlineCourse->instructor?->bio))
                    <p class="text-sm text-[var(--sp-muted)] m-0 whitespace-pre-wrap leading-relaxed">{{ Str::limit($offlineCourse->instructor->bio, 280) }}</p>
                    @if(strlen($offlineCourse->instructor->bio) > 280)
                        <p class="text-xs text-[var(--sp-muted)] m-0 mt-2">{{ __('student.oc_instructor_bio_more') }}</p>
                    @endif
                @else
                    <p class="text-sm text-[var(--sp-muted)] m-0">{{ __('student.oc_no_instructor_bio') }}</p>
                @endif
            </section>

            <section class="sp-card p-5 space-y-2">
                <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide mb-1">{{ __('student.oc_quick_links') }}</p>
                @foreach([
                    ['route' => 'resources', 'icon' => 'icon-messages.svg', 'label' => __('student.oc_tile_resources')],
                    ['route' => 'lectures', 'icon' => 'icon-classes.svg', 'label' => __('student.oc_tile_lectures')],
                    ['route' => 'schedule', 'icon' => 'icon-calendar.svg', 'label' => __('student.oc_tile_schedule')],
                ] as $link)
                    <a href="{{ route($sg . '.' . $link['route'], $offlineCourse) }}" class="sp-process-row !shadow-none border border-[#f0f0ec] hover:border-[var(--sp-accent)] transition-colors">
                        <span class="sp-icon-bubble !w-9 !h-9 shrink-0" style="background:var(--sp-sky)">
                            <x-student.figma-icon :name="$link['icon']" box="size-4" />
                        </span>
                        <span class="flex-1 font-extrabold text-sm">{{ $link['label'] }}</span>
                        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180 shrink-0" />
                    </a>
                @endforeach
                <a href="{{ route('student.exams.index', ['module' => $examsModule]) }}" class="sp-process-row !shadow-none border border-[#f0f0ec] hover:border-[var(--sp-accent)] transition-colors">
                    <span class="sp-icon-bubble !w-9 !h-9 shrink-0" style="background:var(--sp-mint)">
                        <x-student.figma-icon name="icon-exams.svg" box="size-4" />
                    </span>
                    <span class="flex-1 font-extrabold text-sm">{{ __('student.oc_tile_exams') }}</span>
                    <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180 shrink-0" />
                </a>
            </section>

            @if($offlineCourse->start_date || $enrollment->group)
                <section class="sp-card p-5 space-y-2">
                    <h3 class="sp-section-title mb-3">{{ __('student.oc_curriculum_meta') }}</h3>
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
                    @if($offlineCourse->locationModel || $offlineCourse->location)
                        <div class="flex items-center justify-between gap-3 rounded-[14px] bg-[#f7f7f5] px-3 py-2.5">
                            <span class="text-sm font-bold text-[var(--sp-muted)]">{{ __('student.oc_location') }}</span>
                            <span class="text-sm font-extrabold truncate">{{ $offlineCourse->locationModel->name ?? $offlineCourse->location }}</span>
                        </div>
                    @endif
                </section>
            @endif
        </aside>
    </div>
</div>
@endsection
