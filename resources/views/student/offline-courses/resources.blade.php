@extends('layouts.student-dashboard')

@php
    $isOnlineChannel = ($channel ?? 'offline') === 'online';
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $hasGeneral = isset($generalResources) && $generalResources && $generalResources->isNotEmpty();
    $hasLectures = isset($lectures) && $lectures && $lectures->count() > 0;
    $pp = (int) ($perPage ?? 10);
@endphp

@section('title', __('student.oc_resources_title') . ' — ' . $offlineCourse->title)
@section('header', __('student.oc_resources_title'))

@section('content')
<div class="space-y-5">
    <nav class="flex flex-wrap items-center gap-2 text-sm font-bold text-[var(--sp-muted)]">
        <a href="{{ route($sg . '.index') }}" class="sp-link">{{ $isOnlineChannel ? __('student.online_courses_title') : __('student.offline_courses_title') }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <a href="{{ route($sg . '.show', $offlineCourse) }}" class="sp-link truncate max-w-[40vw]">{{ $offlineCourse->title }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <span class="text-[var(--sp-text)]">{{ __('student.oc_resources_title') }}</span>
    </nav>

    <section class="sp-card p-5 sm:p-6 space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div class="flex items-start gap-4 min-w-0">
                <span class="sp-icon-bubble shrink-0 !w-14 !h-14" style="background:var(--sp-peach)">
                    <x-student.figma-icon name="icon-messages.svg" box="size-7" />
                </span>
                <div class="min-w-0">
                    <h2 class="sp-section-title m-0">{{ __('student.oc_resources_title') }}</h2>
                    <p class="text-sm text-[var(--sp-muted)] m-0 mt-2">{{ __('student.oc_resources_subtitle') }}</p>
                </div>
            </div>
            <form method="GET" class="flex flex-col sm:flex-row gap-2 sm:items-center shrink-0">
                <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="{{ __('student.oc_resources_search') }}"
                       class="w-full sm:w-72 rounded-[30px] border-0 bg-[#f7f7f5] px-4 py-2.5 text-sm font-bold focus:ring-2 focus:ring-[var(--sp-accent)]" />
                <select name="per_page" class="rounded-[30px] border-0 bg-[#f7f7f5] px-4 py-2.5 text-sm font-bold">
                    @foreach([5,10,15,25] as $n)
                        <option value="{{ $n }}" @selected($pp === $n)>{{ $n }}</option>
                    @endforeach
                </select>
                <button type="submit" class="sp-promo-btn !mt-0 !py-2.5">{{ __('student.oc_apply_filter') }}</button>
                @if(!empty($search))
                    <a href="{{ url()->current() }}?per_page={{ $pp }}" class="inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5]">{{ __('student.oc_clear_filter') }}</a>
                @endif
            </form>
        </div>
    </section>

    @if(! $hasGeneral && ! $hasLectures)
        <div class="sp-card p-10 text-center">
            <span class="sp-icon-bubble mx-auto mb-3" style="background:var(--sp-peach)"><x-student.figma-icon name="icon-messages.svg" /></span>
            <p class="font-extrabold m-0">{{ __('student.oc_no_resources') }}</p>
        </div>
    @else
        @if($hasGeneral)
            <section class="space-y-3">
                <div class="flex items-center gap-3">
                    <span class="sp-icon-bubble" style="background:var(--sp-sky)"><x-student.figma-icon name="icon-courses.svg" /></span>
                    <div>
                        <h3 class="font-extrabold text-base m-0">{{ __('student.oc_general_resources') }}</h3>
                        <p class="text-xs text-[var(--sp-muted)] m-0">{{ __('student.oc_general_resources_hint') }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($generalResources as $resource)
                        <div class="sp-card p-4 sm:p-5 space-y-3">
                            <h4 class="font-extrabold text-sm m-0 leading-snug">{{ $resource->title }}</h4>
                            @if($resource->description)
                                <p class="text-xs text-[var(--sp-muted)] m-0">{{ Str::limit($resource->description, 180) }}</p>
                            @endif
                            @if($resource->type === 'link' && $resource->url)
                                <a href="{{ $resource->url }}" target="_blank" rel="noopener" class="sp-promo-btn !mt-0 w-full text-center !py-2.5">{{ __('student.oc_open_link') }}</a>
                            @else
                                @php $files = $resource->getAllFiles(); @endphp
                                <div class="grid grid-cols-1 gap-2">
                                    @foreach($files as $file)
                                        <a href="{{ offline_course_resource_file_url($file) }}" download="{{ $file['name'] ?? 'download' }}"
                                           class="inline-flex items-center gap-2 rounded-[16px] bg-[#f7f7f5] px-3 py-2 text-xs font-extrabold hover:bg-[var(--sp-accent)] transition-colors">
                                            <x-student.figma-icon name="icon-plus.svg" box="size-3.5" class="opacity-60" />
                                            <span class="truncate">{{ $file['name'] ?? __('student.oc_download') }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if($hasLectures)
            <section class="sp-card overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-black/5 bg-[#f7f7f5]">
                    <span class="sp-icon-bubble shrink-0" style="background:var(--sp-mint)"><x-student.figma-icon name="icon-classes.svg" /></span>
                    <div>
                        <h3 class="font-extrabold text-base m-0">{{ __('student.oc_tile_lectures') }}</h3>
                        <p class="text-xs text-[var(--sp-muted)] m-0">{{ __('student.oc_lecture_resources_hint') }}</p>
                    </div>
                </div>
                <div class="divide-y divide-black/5">
                    @foreach($lectures as $lec)
                        @php
                            $dateLabel = optional($lec->groupSession)->session_date
                                ? \Carbon\Carbon::parse($lec->groupSession->session_date)->format('Y-m-d')
                                : ($lec->scheduled_at ? $lec->scheduled_at->format('Y-m-d') : null);
                            $groupLabel = optional(optional($lec->groupSession)->group)->name ?? optional($lec->group)->name;
                            $resourcesForLecture = $lec->resources ?? collect();
                        @endphp
                        <details class="group">
                            <summary class="cursor-pointer list-none p-4 sm:p-5 hover:bg-[#f7f7f5] flex items-center justify-between gap-3 select-none">
                                <div class="min-w-0 space-y-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-extrabold text-sm">{{ $lec->title }}</span>
                                        @if($dateLabel)<span class="sp-pill">{{ $dateLabel }}</span>@endif
                                        @if($groupLabel)<span class="sp-pill sp-pill--progress">{{ $groupLabel }}</span>@endif
                                        <span class="sp-pill sp-pill--done">{{ $resourcesForLecture->count() }} {{ __('student.oc_resource_count') }}</span>
                                    </div>
                                    @if($lec->description)
                                        <p class="text-xs text-[var(--sp-muted)] m-0">{{ Str::limit($lec->description, 140) }}</p>
                                    @endif
                                </div>
                                <x-student.figma-icon name="icon-dropdown.svg" box="size-4" class="opacity-40 shrink-0 group-open:rotate-180 transition-transform" />
                            </summary>
                            <div class="px-4 sm:px-5 pb-5">
                                @if($resourcesForLecture->isEmpty())
                                    <p class="text-sm text-[var(--sp-muted)] m-0 py-2">{{ __('student.oc_no_lecture_resources') }}</p>
                                @else
                                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                        @foreach($resourcesForLecture as $resource)
                                            <div class="rounded-[16px] bg-[#f7f7f5] p-4 space-y-3">
                                                <p class="font-extrabold text-sm m-0">{{ $resource->title }}</p>
                                                @if($resource->description)
                                                    <p class="text-xs text-[var(--sp-muted)] m-0">{{ Str::limit($resource->description, 180) }}</p>
                                                @endif
                                                @if($resource->type === 'link' && $resource->url)
                                                    <a href="{{ $resource->url }}" target="_blank" rel="noopener" class="sp-promo-btn !mt-0 w-full text-center !py-2">{{ __('student.oc_open_link') }}</a>
                                                @else
                                                    @php $files = $resource->getAllFiles(); @endphp
                                                    <div class="grid gap-2">
                                                        @foreach($files as $file)
                                                            <a href="{{ offline_course_resource_file_url($file) }}" download="{{ $file['name'] ?? 'download' }}"
                                                               class="inline-flex items-center gap-2 rounded-[14px] bg-white px-3 py-2 text-xs font-extrabold hover:bg-[var(--sp-accent)] transition-colors">
                                                                <span class="truncate">{{ $file['name'] ?? __('student.oc_download') }}</span>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </details>
                    @endforeach
                </div>
                <div class="p-4 sm:p-5 border-t border-black/5 flex justify-center">{{ $lectures->links() }}</div>
            </section>
        @endif
    @endif
</div>
@endsection
