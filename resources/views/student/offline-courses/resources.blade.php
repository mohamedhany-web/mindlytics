@extends('layouts.student-dashboard')

@section('title', __('student.oc_resources_page_title', ['title' => $offlineCourse->title]))

@php
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $isOnline = ($channel ?? 'offline') === 'online';
    $channelLabel = $isOnline ? __('student.online_badge') : __('student.offline_badge');
    $listTitle = $isOnline ? __('student.my_online_courses') : __('student.offline_courses_title');
@endphp

@push('styles')
@include('student.offline-courses.partials.los-styles')
@endpush

@section('content')
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="{{ __('student.oc_breadcrumb') }}">
                <a href="{{ route('dashboard') }}">{{ __('los.page_title') }}</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route($sg . '.index') }}">{{ $listTitle }}</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route($sg . '.show', $offlineCourse) }}">{{ \Illuminate\Support\Str::limit($offlineCourse->title, 28) }}</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700">{{ __('student.oc_resources') }}</span>
            </nav>
            <h1>{{ __('student.oc_resources') }}</h1>
            <p class="sub">{{ $offlineCourse->title }} · {{ $channelLabel }}</p>
        </div>
        <div class="oc-signals">
            <span class="oc-signal oc-signal-live">{{ $channelLabel }}</span>
        </div>
    </header>

<div class="space-y-6">
    <div class="mb-0">
        <a href="{{ route(($studentRouteGroup ?? 'student.offline-courses') . '.show', $offlineCourse) }}" class="oc-btn oc-btn-quiet" style="min-height:36px">
            <i class="fas fa-arrow-right text-xs"></i>
            {{ __('student.oc_back_to_course') }}
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-gray-100">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-file-alt text-[#49A4A2]"></i>
                        {{ __('student.oc_resources_heading', ['channel' => $channelLabel, 'title' => $offlineCourse->title]) }}
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ __('student.oc_resources_intro') }}
                    </p>
                </div>
                <form method="GET" class="flex flex-col sm:flex-row gap-2 sm:items-center">
                    <div class="relative">
                        <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input
                            type="text"
                            name="q"
                            value="{{ $search ?? '' }}"
                            placeholder="{{ __('student.oc_search_resources_placeholder') }}"
                            class="w-full sm:w-80 pr-9 pl-3 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm"
                        />
                    </div>
                    <select name="per_page" class="w-full sm:w-auto px-3 py-2 rounded-lg border border-gray-200 text-sm">
                        @php $pp = (int) ($perPage ?? 10); @endphp
                        <option value="5" {{ $pp === 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ $pp === 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ $pp === 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ $pp === 25 ? 'selected' : '' }}>25</option>
                    </select>
                    <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-[#2f7f7d] text-white text-sm font-semibold hover:bg-[#2f7f7d]">
                        <i class="fas fa-filter"></i>
                        {{ __('student.oc_apply') }}
                    </button>
                    @if(!empty($search))
                        <a href="{{ url()->current() }}?per_page={{ $pp }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200">
                            {{ __('student.oc_clear') }}
                        </a>
                    @endif
                </form>
            </div>
        </div>
        @php
            $hasGeneral = isset($generalResources) && $generalResources && $generalResources->isNotEmpty();
            $hasLectures = isset($lectures) && $lectures && $lectures->count() > 0;
        @endphp

        @if(! $hasGeneral && ! $hasLectures)
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-folder-open text-4xl mb-3 opacity-50"></i>
                <p>{{ __('student.oc_no_resources') }}</p>
            </div>
        @else
            @if($hasGeneral)
                <div class="p-4 sm:p-5 border-b border-gray-100 bg-gray-50/40">
                    <h2 class="font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-layer-group text-slate-500"></i>
                        {{ __('student.oc_general_resources') }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">{{ __('student.oc_general_resources_desc') }}</p>
                </div>
                <div class="p-4 sm:p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach($generalResources as $resource)
                            <div class="rounded-2xl border border-gray-200 bg-white p-4 hover:bg-gray-50/40 transition-colors">
                                <div class="min-w-0">
                                    <h3 class="font-semibold text-gray-900 leading-snug break-words">{{ $resource->title }}</h3>
                                    @if($resource->description)
                                        <p class="text-sm text-gray-600 mt-1 leading-relaxed">{{ Str::limit($resource->description, 180) }}</p>
                                    @endif
                                </div>

                                <div class="mt-3">
                                    @if($resource->type === 'link' && $resource->url)
                                        <a href="{{ $resource->url }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-[#2f7f7d] text-white rounded-xl font-semibold hover:bg-[#2f7f7d]">
                                            <i class="fas fa-external-link-alt"></i>
                                            {{ __('student.oc_open_link') }}
                                        </a>
                                    @else
                                        @php $files = $resource->getAllFiles(); @endphp
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            @foreach($files as $file)
                                                <a href="{{ offline_course_resource_file_url($file) }}"
                                                   download="{{ $file['name'] ?? 'download' }}"
                                                   class="group inline-flex items-center gap-2 w-full max-w-full px-3 py-2 rounded-xl bg-teal-50 text-teal-800 text-sm font-semibold hover:bg-teal-100 border border-teal-100">
                                                    <i class="fas fa-download flex-shrink-0"></i>
                                                    <span class="truncate min-w-0">{{ $file['name'] ?? __('student.oc_download') }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($hasLectures)
                <div class="p-4 sm:p-5 border-t border-gray-100 bg-white">
                    <h2 class="font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-chalkboard-teacher text-[#49A4A2]"></i>
                        {{ __('student.oc_by_lectures') }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">{{ __('student.oc_lectures_pick_resources') }}</p>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($lectures as $lec)
                        @php
                            $dateLabel = optional($lec->groupSession)->session_date
                                ? \Carbon\Carbon::parse($lec->groupSession->session_date)->format('Y-m-d')
                                : ($lec->scheduled_at ? $lec->scheduled_at->format('Y-m-d') : null);
                            $groupLabel = optional(optional($lec->groupSession)->group)->name ?? optional($lec->group)->name;
                            $resourcesForLecture = $lec->resources ?? collect();
                        @endphp
                        <details class="group">
                            <summary class="cursor-pointer list-none p-4 sm:p-5 hover:bg-gray-50/50 flex items-center justify-between gap-3 select-none">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-semibold text-gray-900">{{ $lec->title }}</span>
                                        @if($dateLabel)
                                            <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-700">{{ $dateLabel }}</span>
                                        @endif
                                        @if($groupLabel)
                                            <span class="text-xs px-2 py-1 rounded-full bg-amber-50 text-amber-700">{{ $groupLabel }}</span>
                                        @endif
                                        <span class="text-xs px-2 py-1 rounded-full bg-teal-50 text-teal-800">
                                            {{ __('student.oc_resource_count', ['count' => $resourcesForLecture->count()]) }}
                                        </span>
                                    </div>
                                    @if($lec->description)
                                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($lec->description, 140) }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 text-gray-500">
                                    <i class="fas fa-chevron-down transition-transform duration-200 group-open:rotate-180"></i>
                                </div>
                            </summary>
                            <div class="px-4 sm:px-5 pb-5">
                                @if($resourcesForLecture->isEmpty())
                                    <div class="text-sm text-gray-500 py-3">{{ __('student.oc_no_lecture_resources') }}</div>
                                @else
                                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                        @foreach($resourcesForLecture as $resource)
                                            <div class="rounded-2xl border border-gray-200 bg-white p-4 hover:bg-gray-50/40 transition-colors">
                                                <div class="min-w-0">
                                                    <div class="font-semibold text-gray-900 leading-snug break-words">{{ $resource->title }}</div>
                                                    @if($resource->description)
                                                        <div class="text-sm text-gray-600 mt-1 leading-relaxed">{{ Str::limit($resource->description, 180) }}</div>
                                                    @endif
                                                </div>
                                                <div class="mt-3">
                                                    @if($resource->type === 'link' && $resource->url)
                                                        <a href="{{ $resource->url }}" target="_blank" rel="noopener"
                                                           class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-[#2f7f7d] text-white rounded-xl font-semibold hover:bg-[#2f7f7d]">
                                                            <i class="fas fa-external-link-alt"></i>
                                                            {{ __('student.oc_open_link') }}
                                                        </a>
                                                    @else
                                                        @php $files = $resource->getAllFiles(); @endphp
                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                            @foreach($files as $file)
                                                                <a href="{{ offline_course_resource_file_url($file) }}"
                                                                   download="{{ $file['name'] ?? 'download' }}"
                                                                   class="group inline-flex items-center gap-2 w-full max-w-full px-3 py-2 rounded-xl bg-teal-50 text-teal-800 text-sm font-semibold hover:bg-teal-100 border border-teal-100">
                                                                    <i class="fas fa-download flex-shrink-0"></i>
                                                                    <span class="truncate min-w-0">{{ $file['name'] ?? __('student.oc_download') }}</span>
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </details>
                    @endforeach
                </div>

                <div class="p-4 sm:p-5 border-t border-gray-100">
                    {{ $lectures->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
</div>
@endsection
