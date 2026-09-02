@extends('layouts.student-dashboard')

@php
    $isOnlineChannel = ($channel ?? 'offline') === 'online';
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $today = now()->startOfDay();
    $sessionCount = $sessions->count();
    $monthCount = $timelineByMonth->count();
    $timelineTotal = $timelineByMonth->flatten(1)->count();
@endphp

@section('title', __('student.oc_schedule_title') . ' — ' . $offlineCourse->title)
@section('header', __('student.oc_schedule_title'))

@section('content')
<div class="space-y-5">
    <nav class="flex flex-wrap items-center gap-2 text-sm font-bold text-[var(--sp-muted)]">
        <a href="{{ route($sg . '.index') }}" class="sp-link">{{ $isOnlineChannel ? __('student.online_courses_title') : __('student.offline_courses_title') }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <a href="{{ route($sg . '.show', $offlineCourse) }}" class="sp-link truncate max-w-[40vw]">{{ $offlineCourse->title }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <span class="text-[var(--sp-text)]">{{ __('student.oc_schedule_title') }}</span>
    </nav>

    <section class="sp-card p-5 sm:p-6 space-y-4">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-start gap-4 min-w-0">
                <span class="sp-icon-bubble shrink-0 !w-14 !h-14" style="background:var(--sp-lilac)">
                    <x-student.figma-icon name="icon-calendar.svg" box="size-7" />
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-1 uppercase tracking-wide">{{ __('student.oc_schedule_eyebrow') }}</p>
                    <h2 class="sp-section-title m-0">{{ __('student.oc_sessions_title') }}</h2>
                    <p class="text-sm text-[var(--sp-muted)] m-0 mt-2 max-w-2xl">{{ $offlineCourse->title }} — {{ __('student.oc_schedule_subtitle') }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 shrink-0">
                <a href="{{ route($sg . '.show', $offlineCourse) }}" class="inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition-colors">{{ __('student.oc_back_course') }}</a>
                <a href="{{ route($sg . '.curriculum', $offlineCourse) }}" class="sp-promo-btn !mt-0">{{ __('student.oc_tile_curriculum') }}</a>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="sp-card p-4 sm:p-5">
            <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.oc_stat_sessions') }}</p>
            <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1">{{ $sessionCount }}</p>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.oc_stat_no_due') }}</p>
            <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1">{{ $activitiesNoDue->count() }}</p>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.oc_stat_timeline') }}</p>
            <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1">{{ $timelineTotal }}</p>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.oc_stat_months') }}</p>
            <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1">{{ $monthCount }}</p>
        </div>
    </div>

    @if($enrollment->group)
        <div class="sp-card p-4 sm:p-5 flex items-center gap-3">
            <span class="sp-icon-bubble shrink-0" style="background:var(--sp-lilac)"><x-student.figma-icon name="icon-community.svg" /></span>
            <div class="min-w-0">
                <p class="text-xs font-bold text-[var(--sp-muted)] m-0">{{ __('student.oc_group') }}</p>
                <p class="font-extrabold m-0">{{ $enrollment->group->name }}</p>
                @if($enrollment->group->start_date)
                    <p class="text-xs text-[var(--sp-muted)] m-0 mt-1">{{ __('student.oc_path_start') }}: {{ $enrollment->group->start_date->translatedFormat('l j F Y') }}</p>
                @endif
            </div>
        </div>
    @endif

    @if($timelineByMonth->isEmpty() && $activitiesNoDue->isEmpty() && ! $enrollment->group)
        <div class="sp-card p-10 text-center">
            <span class="sp-icon-bubble mx-auto mb-3" style="background:var(--sp-lilac)"><x-student.figma-icon name="icon-calendar.svg" /></span>
            <p class="font-extrabold m-0">{{ __('student.oc_no_schedule') }}</p>
            <p class="text-sm text-[var(--sp-muted)] m-0 mt-2">{{ __('student.oc_no_schedule_hint') }}</p>
        </div>
    @endif

    <div class="space-y-4">
        @foreach($timelineByMonth as $monthLabel => $rows)
            <section class="sp-card overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-black/5 bg-[#f7f7f5]">
                    <span class="sp-icon-bubble shrink-0" style="background:var(--sp-sky)"><x-student.figma-icon name="icon-calendar.svg" /></span>
                    <h3 class="font-extrabold text-base m-0">{{ $monthLabel }}</h3>
                </div>
                <ul class="divide-y divide-black/5 m-0 p-0 list-none">
                    @foreach($rows as $row)
                        @php
                            $d = $row['date'];
                            $isPast = $d->lt($today);
                            $isToday = $d->isToday();
                        @endphp
                        <li class="flex flex-col sm:flex-row gap-3 sm:gap-4 px-4 sm:px-5 py-4 {{ $isToday ? 'bg-[rgba(174,217,234,.12)]' : '' }} {{ $isPast && ! $isToday ? 'opacity-80' : '' }}">
                            <div class="sm:w-36 shrink-0 space-y-1">
                                <p class="text-sm font-extrabold m-0">{{ $d->translatedFormat('l') }}</p>
                                <p class="text-xs text-[var(--sp-muted)] m-0">{{ $d->translatedFormat('j F Y') }}</p>
                                @if($isToday)
                                    <span class="sp-pill sp-pill--progress">{{ __('student.oc_today') }}</span>
                                @elseif($isPast)
                                    <span class="sp-pill">{{ __('student.oc_past') }}</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0 rounded-[16px] bg-[#f7f7f5] px-4 py-3">
                                @if($row['type'] === 'session')
                                    @php $s = $row['session']; @endphp
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="sp-pill sp-pill--progress">{{ __('student.oc_session_badge') }}</span>
                                        @if(filled($s->title))
                                            <span class="font-extrabold text-sm">{{ $s->title }}</span>
                                        @endif
                                    </div>
                                    @php
                                        $stLabel = $s->start_time ? \Carbon\Carbon::parse($s->start_time)->format('h:i A') : '—';
                                        $etLabel = $s->end_time ? \Carbon\Carbon::parse($s->end_time)->format('h:i A') : '—';
                                    @endphp
                                    <p class="text-sm m-0">{{ $stLabel }} — {{ $etLabel }} · {{ (int) $s->duration_minutes }} {{ __('student.oc_minutes') }}</p>
                                    @if(filled($s->location) || filled(optional($enrollment->group)->location))
                                        <p class="text-xs text-[var(--sp-muted)] m-0 mt-1">{{ $s->location ?? optional($enrollment->group)->location }}</p>
                                    @endif
                                @elseif($row['type'] === 'activity')
                                    @php $a = $row['activity']; @endphp
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="sp-pill sp-pill--upcoming">{{ __('student.oc_activity_badge') }}</span>
                                        <span class="font-extrabold text-sm">{{ $a->title }}</span>
                                    </div>
                                    <p class="text-xs text-[var(--sp-muted)] m-0">{{ __('student.oc_due_end_of') }} {{ $d->translatedFormat('l j F') }} — {{ $a->max_score }} {{ __('student.oc_points') }}</p>
                                    <a href="{{ route($sg . '.activities.show', [$offlineCourse, $a]) }}" class="sp-promo-btn !mt-3 !py-2 inline-flex">{{ __('student.oc_open_activity') }}</a>
                                @else
                                    @php $ex = $row['exam']; @endphp
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="sp-pill sp-pill--done">{{ __('student.oc_exam_badge') }}</span>
                                        <span class="font-extrabold text-sm">{{ $ex->title }}</span>
                                    </div>
                                    <p class="text-xs text-[var(--sp-muted)] m-0">{{ __('student.oc_exam_start') }}: {{ $d->translatedFormat('l j F Y') }}</p>
                                    <a href="{{ route('student.exams.show', $ex) }}" class="inline-flex items-center justify-center rounded-[30px] px-4 py-2 mt-3 text-xs font-extrabold bg-[var(--sp-accent)] text-[var(--sp-accent-text)]">{{ __('student.oc_open_exam') }}</a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    </div>

    @if($activitiesNoDue->isNotEmpty())
        <section class="sp-card overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-black/5" style="background:var(--sp-amber-soft)">
                <span class="sp-icon-bubble shrink-0" style="background:var(--sp-peach)"><x-student.figma-icon name="icon-star.svg" /></span>
                <div>
                    <h3 class="font-extrabold text-base m-0">{{ __('student.oc_undated_activities') }}</h3>
                    <p class="text-xs text-[var(--sp-muted)] m-0 mt-1">{{ __('student.oc_undated_activities_hint') }}</p>
                </div>
            </div>
            <ul class="divide-y divide-black/5 m-0 p-0 list-none">
                @foreach($activitiesNoDue as $activity)
                    <li class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-3.5">
                        <div class="min-w-0">
                            <p class="font-extrabold text-sm m-0">{{ $activity->title }}</p>
                            <p class="text-xs text-[var(--sp-muted)] m-0 mt-1">{{ $activity->type }} · {{ $activity->max_score }} {{ __('student.oc_points') }}</p>
                        </div>
                        <a href="{{ route($sg . '.activities.show', [$offlineCourse, $activity]) }}" class="sp-promo-btn !mt-0 !py-2 shrink-0">{{ __('student.oc_submit_activity') }}</a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
@endsection
