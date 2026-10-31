@extends('layouts.student-dashboard')

@php
    $isOnlineChannel = ($channel ?? 'offline') === 'online';
    $pageTitle = $isOnlineChannel ? __('student.online_courses_title') : __('student.offline_courses_title');
    $pageSubtitle = $isOnlineChannel ? __('student.online_courses_subtitle') : __('student.offline_courses_subtitle');
    $examsModule = $isOnlineChannel ? 'online' : 'offline';
    $bubbleColors = ['var(--sp-peach)', 'var(--sp-sky)', 'var(--sp-mint)', 'var(--sp-lilac)'];
@endphp

@section('title', $pageTitle)
@section('header', $pageTitle)

@section('content')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm font-bold text-[var(--sp-muted)] m-0 mb-1">
                {{ $isOnlineChannel ? __('student.exam_module_online') : __('student.exam_module_offline') }}
            </p>
            <p class="text-sm text-[var(--sp-muted)] m-0 max-w-2xl">{{ $pageSubtitle }}</p>
        </div>
        <div class="flex flex-wrap gap-2 shrink-0">
            <a href="{{ route('student.exams.index', ['module' => $examsModule]) }}" class="inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition-colors">
                {{ __('student.exams') }}
            </a>
            @if(!$isOnlineChannel)
                <a href="{{ route('student.online-courses.index') }}" class="inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition-colors">
                    {{ __('student.online_courses_list') }}
                </a>
            @else
                <a href="{{ route('student.offline-courses.index') }}" class="inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition-colors">
                    {{ __('student.my_offline_courses') }}
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.courses_count_label') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['total_offline'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-sky)">
                    <x-student.figma-icon name="{{ $isOnlineChannel ? 'icon-community.svg' : 'icon-classes.svg' }}" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.activities_label') }}</p>
                    <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['total_activities'] }}</p>
                </div>
                <span class="sp-icon-bubble" style="background:var(--sp-peach)">
                    <x-student.figma-icon name="icon-messages.svg" />
                </span>
            </div>
        </div>
        <div class="sp-card p-4 sm:p-5 col-span-2 lg:col-span-2">
            <div class="flex items-center gap-3 h-full">
                <span class="sp-icon-bubble shrink-0" style="background:var(--sp-mint)">
                    <x-student.figma-icon name="icon-exams.svg" />
                </span>
                <div class="min-w-0">
                    <p class="font-extrabold text-sm m-0">{{ __('student.oc_exams_hint_title') }}</p>
                    <p class="text-xs text-[var(--sp-muted)] m-0 mt-1">{{ __('student.oc_exams_hint_body') }}</p>
                    <a href="{{ route('student.exams.index', ['module' => $examsModule]) }}" class="sp-link text-xs font-extrabold inline-block mt-2">{{ __('student.view_exams') }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($enrollments as $index => $enrollment)
            @php $course = $enrollment->course; @endphp
            <a href="{{ route(($studentRouteGroup ?? 'student.offline-courses') . '.show', $course->id) }}" class="sp-card overflow-hidden block hover:shadow-lg transition-shadow">
                <div class="h-28 flex items-center justify-center" style="background:{{ $bubbleColors[$index % count($bubbleColors)] }}">
                    <x-student.figma-icon name="{{ $isOnlineChannel ? 'icon-community.svg' : 'icon-classes.svg' }}" box="size-10" />
                </div>
                <div class="p-4 sm:p-5 space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="font-extrabold text-base m-0 leading-snug line-clamp-2">{{ $course->title }}</h3>
                        <span class="sp-pill sp-pill--progress shrink-0">{{ $isOnlineChannel ? __('student.exam_source_online') : __('student.exam_source_offline') }}</span>
                    </div>
                    <p class="text-xs text-[var(--sp-muted)] m-0">
                        {{ $course->instructor->name ?? '—' }}
                        @if($course->locationModel || $course->location)
                            · {{ $course->locationModel->name ?? $course->location }}
                        @endif
                    </p>
                    @if($enrollment->group)
                        <p class="text-xs font-bold text-[var(--sp-muted)] m-0">{{ __('student.oc_group') }}: {{ $enrollment->group->name }}</p>
                    @endif
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-[var(--sp-muted)] mb-1.5">
                            <span>{{ __('student.oc_progress') }}</span>
                            <span class="text-[var(--sp-accent-text)]">{{ number_format($enrollment->progress, 0) }}%</span>
                        </div>
                        <div class="h-2 rounded-full bg-[#f7f7f5] overflow-hidden">
                            <div class="h-full rounded-full transition-all" style="width:{{ min($enrollment->progress, 100) }}%;background:var(--sp-accent)"></div>
                        </div>
                    </div>
                    <span class="sp-promo-btn !mt-0 w-full text-center !py-2.5">{{ __('student.oc_view_details') }}</span>
                </div>
            </a>
        @empty
            @if(($bookings ?? collect())->isEmpty())
                <div class="col-span-full sp-card p-10 text-center">
                    <span class="sp-icon-bubble mx-auto mb-4" style="background:var(--sp-sky);width:56px;height:56px">
                        <x-student.figma-icon name="{{ $isOnlineChannel ? 'icon-community.svg' : 'icon-classes.svg' }}" box="size-7" />
                    </span>
                    <h3 class="font-extrabold text-lg m-0 mb-2">{{ $isOnlineChannel ? __('student.no_online_courses') : __('student.no_offline_courses') }}</h3>
                    <p class="text-sm text-[var(--sp-muted)] m-0 max-w-md mx-auto">{{ $isOnlineChannel ? __('student.no_online_courses_desc') : __('student.no_offline_courses_desc') }}</p>
                </div>
            @endif
        @endforelse

        @foreach(($bookings ?? collect()) as $booking)
            @php
                $course = $booking->course;
                $approved = $booking->status === 'approved';
            @endphp
            @if($course)
                <div class="sp-card overflow-hidden border-2 border-dashed border-[var(--sp-accent)]/40">
                    <div class="h-28 flex items-center justify-center" style="background:var(--sp-amber-soft)">
                        <x-student.figma-icon name="icon-calendar.svg" box="size-10" />
                    </div>
                    <div class="p-4 sm:p-5 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-extrabold text-base m-0 leading-snug line-clamp-2">{{ $course->title }}</h3>
                            <span class="sp-pill {{ $approved ? 'sp-pill--done' : 'sp-pill--upcoming' }} shrink-0">
                                {{ $approved ? __('student.oc_booking_approved') : __('student.oc_booking_pending') }}
                            </span>
                        </div>
                        <p class="text-xs text-[var(--sp-muted)] m-0">{{ optional($booking->created_at)->format('Y/m/d') }}</p>
                        @if($booking->requestedGroup || $booking->assignedGroup)
                            <p class="text-xs font-bold text-[var(--sp-muted)] m-0">{{ __('student.oc_group') }}: {{ $booking->assignedGroup->name ?? $booking->requestedGroup->name }}</p>
                        @endif
                        <p class="text-xs rounded-[12px] px-3 py-2 m-0" style="background:var(--sp-mint);color:var(--sp-accent-text)">{{ __('student.oc_booking_hint') }}</p>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    @if($enrollments->hasPages())
        <div class="flex justify-center pt-2">{{ $enrollments->links() }}</div>
    @endif
</div>
@endsection
