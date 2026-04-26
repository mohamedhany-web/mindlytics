@extends('layouts.app')

@section('title', 'موارد الكورس - ' . $offlineCourse->title)
@section('header', 'موارد الكورس الأوفلاين')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="mb-4">
        <a href="{{ route(($studentRouteGroup ?? 'student.offline-courses') . '.show', $offlineCourse) }}" class="inline-flex items-center text-sky-600 hover:text-sky-700 text-sm font-medium">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة لصفحة الكورس
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-gray-100">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-file-alt text-sky-500"></i>
                        موارد الكورس (أوفلاين) — {{ $offlineCourse->title }}
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        اعرض الموارد حسب <span class="font-semibold text-gray-700">المحاضرات</span>، مع قسم للموارد العامة.
                    </p>
                </div>
                <form method="GET" class="flex flex-col sm:flex-row gap-2 sm:items-center">
                    <div class="relative">
                        <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input
                            type="text"
                            name="q"
                            value="{{ $search ?? '' }}"
                            placeholder="ابحث بالعنوان أو الوصف أو اسم الملف..."
                            class="w-full sm:w-80 pr-9 pl-3 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm"
                        />
                    </div>
                    <select name="per_page" class="w-full sm:w-auto px-3 py-2 rounded-lg border border-gray-200 text-sm">
                        @php $pp = (int) ($perPage ?? 10); @endphp
                        <option value="5" {{ $pp === 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ $pp === 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ $pp === 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ $pp === 25 ? 'selected' : '' }}>25</option>
                    </select>
                    <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">
                        <i class="fas fa-filter"></i>
                        تطبيق
                    </button>
                    @if(!empty($search))
                        <a href="{{ url()->current() }}?per_page={{ $pp }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200">
                            مسح
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
                <p>لا توجد موارد متاحة حالياً.</p>
            </div>
        @else
            @if($hasGeneral)
                <div class="p-4 sm:p-5 border-b border-gray-100 bg-gray-50/40">
                    <h2 class="font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-layer-group text-slate-500"></i>
                        موارد عامة
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">موارد غير مرتبطة بمحاضرة محددة.</p>
                </div>
                <ul class="divide-y divide-gray-100">
                    @foreach($generalResources as $resource)
                        <li class="p-4 sm:p-5 hover:bg-gray-50/50">
                            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $resource->title }}</h3>
                                    @if($resource->description)
                                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($resource->description, 150) }}</p>
                                    @endif
                                </div>
                                <div class="flex-shrink-0 flex flex-wrap gap-2 justify-end">
                                    @if($resource->type === 'link' && $resource->url)
                                        <a href="{{ $resource->url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 text-white rounded-lg font-medium hover:bg-sky-700">
                                            <i class="fas fa-external-link-alt"></i>
                                            فتح الرابط
                                        </a>
                                    @else
                                        @foreach($resource->getAllFiles() as $file)
                                            <a href="{{ offline_course_resource_file_url($file) }}" download="{{ $file['name'] ?? 'download' }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-100 text-sky-700 rounded-lg text-sm font-medium hover:bg-sky-200">
                                                <i class="fas fa-download"></i>
                                                {{ Str::limit($file['name'] ?? 'تحميل', 26) }}
                                            </a>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if($hasLectures)
                <div class="p-4 sm:p-5 border-t border-gray-100 bg-white">
                    <h2 class="font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-chalkboard-teacher text-sky-500"></i>
                        المحاضرات
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">اختر محاضرة لتحميل مواردها.</p>
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
                                        <span class="text-xs px-2 py-1 rounded-full bg-sky-50 text-sky-700">
                                            {{ $resourcesForLecture->count() }} مورد
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
                                    <div class="text-sm text-gray-500 py-3">لا توجد موارد مرتبطة بهذه المحاضرة.</div>
                                @else
                                    <ul class="space-y-3">
                                        @foreach($resourcesForLecture as $resource)
                                            <li class="rounded-xl border border-gray-200 bg-white p-4">
                                                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-3">
                                                    <div class="min-w-0 flex-1">
                                                        <div class="font-semibold text-gray-900">{{ $resource->title }}</div>
                                                        @if($resource->description)
                                                            <div class="text-sm text-gray-600 mt-1">{{ Str::limit($resource->description, 160) }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="flex-shrink-0 flex flex-wrap gap-2 justify-end">
                                                        @if($resource->type === 'link' && $resource->url)
                                                            <a href="{{ $resource->url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 text-white rounded-lg font-medium hover:bg-sky-700">
                                                                <i class="fas fa-external-link-alt"></i>
                                                                فتح الرابط
                                                            </a>
                                                        @else
                                                            @foreach($resource->getAllFiles() as $file)
                                                                <a href="{{ offline_course_resource_file_url($file) }}" download="{{ $file['name'] ?? 'download' }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-100 text-sky-700 rounded-lg text-sm font-medium hover:bg-sky-200">
                                                                    <i class="fas fa-download"></i>
                                                                    {{ Str::limit($file['name'] ?? 'تحميل', 26) }}
                                                                </a>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
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
@endsection
