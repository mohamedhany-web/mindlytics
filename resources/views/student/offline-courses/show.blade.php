@extends('layouts.app')

@section('title', $offlineCourse->title)
@section('header', $offlineCourse->title)

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="mb-4">
        <a href="{{ route('student.offline-courses.index') }}" class="inline-flex items-center text-sky-600 hover:text-sky-700 text-sm font-medium">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للقائمة
        </a>
    </div>

    <!-- معلومات الكورس -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4">{{ $offlineCourse->title }}</h1>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                <div class="py-2.5 px-3 bg-gray-50 rounded-lg border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 mb-0.5">المدرب</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $offlineCourse->instructor->name }}</p>
                </div>
                @if($offlineCourse->locationModel)
                <div class="py-2.5 px-3 bg-gray-50 rounded-lg border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 mb-0.5">المكان</p>
                    <p class="text-sm font-semibold text-gray-900"><i class="fas fa-map-marker-alt text-sky-500 ml-1"></i>{{ $offlineCourse->locationModel->name }}</p>
                    @if($offlineCourse->locationModel->address)
                        <p class="text-xs text-gray-500 mt-1">{{ $offlineCourse->locationModel->address }}</p>
                    @endif
                </div>
                @elseif($offlineCourse->location)
                <div class="py-2.5 px-3 bg-gray-50 rounded-lg border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 mb-0.5">الموقع</p>
                    <p class="text-sm font-semibold text-gray-900"><i class="fas fa-map-marker-alt text-sky-500 ml-1"></i>{{ $offlineCourse->location }}</p>
                </div>
                @endif
                @if($offlineCourse->start_date)
                <div class="py-2.5 px-3 bg-gray-50 rounded-lg border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 mb-0.5">تاريخ البدء</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $offlineCourse->start_date->format('Y-m-d') }}</p>
                </div>
                @endif
                @if($enrollment->group)
                <div class="py-2.5 px-3 bg-gray-50 rounded-lg border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 mb-0.5">المجموعة</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $enrollment->group->name }}</p>
                </div>
                @endif
                <div class="py-2.5 px-3 bg-sky-50 rounded-lg border border-sky-100">
                    <p class="text-xs font-medium text-gray-500 mb-1">التقدم</p>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-sky-500 h-2 rounded-full" style="width: {{ min($enrollment->progress, 100) }}%;"></div>
                        </div>
                        <span class="text-sm font-bold text-sky-600">{{ number_format($enrollment->progress, 0) }}%</span>
                    </div>
                </div>
            </div>
            @if($offlineCourse->description)
            <div class="pt-4 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-500 mb-2">الوصف</p>
                <p class="text-sm text-gray-700 leading-relaxed">{{ $offlineCourse->description }}</p>
            </div>
            @endif
            <!-- روابط المحتوى الأوفلاين (منفصلة عن الأونلاين) -->
            <div class="pt-4 border-t border-gray-100 flex flex-wrap gap-3">
                <a href="{{ route('student.offline-courses.resources', $offlineCourse) }}" class="inline-flex items-center gap-2 px-3 py-2 bg-sky-50 text-sky-700 rounded-lg border border-sky-100 font-medium text-sm hover:bg-sky-100">
                    <i class="fas fa-file-alt"></i> الموارد
                </a>
                <a href="{{ route('student.offline-courses.lectures', $offlineCourse) }}" class="inline-flex items-center gap-2 px-3 py-2 bg-violet-50 text-violet-700 rounded-lg border border-violet-100 font-medium text-sm hover:bg-violet-100">
                    <i class="fas fa-chalkboard-teacher"></i> المحاضرات
                </a>
            </div>
        </div>
    </div>

    <!-- الأنشطة المطلوبة -->
    @if($pendingActivities->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6">
            <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-tasks text-amber-500"></i>
                الأنشطة المطلوبة
            </h2>
            <div class="space-y-3">
                @foreach($pendingActivities as $activity)
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 rounded-lg border border-gray-100 hover:bg-gray-50/50 transition-colors">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 mb-1">{{ $activity->title }}</h3>
                        @if($activity->description)
                            <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ Str::limit($activity->description, 120) }}</p>
                        @endif
                        <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                            <span><i class="fas fa-tag text-sky-500 ml-1"></i>{{ $activity->type }}</span>
                            @if($activity->due_date)
                                <span><i class="fas fa-calendar text-sky-500 ml-1"></i>{{ $activity->due_date->format('Y-m-d') }}</span>
                            @endif
                            <span><i class="fas fa-star text-amber-500 ml-1"></i>{{ $activity->max_score }} نقطة</span>
                        </div>
                    </div>
                    <a href="{{ route('student.offline-courses.activities.show', [$offlineCourse, $activity]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200 hover:bg-amber-200 flex-shrink-0">
                        عرض / تسليم
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- الأنشطة المكتملة -->
    @if($completedActivities->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6">
            <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-500"></i>
                الأنشطة المكتملة
            </h2>
            <div class="space-y-3">
                @foreach($completedActivities as $activity)
                @php
                    $submission = $activity->submissions->firstWhere('student_id', auth()->id());
                @endphp
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 rounded-lg border border-gray-100 hover:bg-gray-50/50 transition-colors">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 mb-1">{{ $activity->title }}</h3>
                        @if($submission && $submission->score !== null)
                            <p class="text-sm text-emerald-600 font-semibold">
                                <i class="fas fa-check-circle ml-1"></i>تم التصحيح: {{ $submission->score }}/{{ $activity->max_score }}
                            </p>
                        @endif
                    </div>
                    <a href="{{ route('student.offline-courses.activities.show', [$offlineCourse, $activity]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 hover:bg-emerald-200 flex-shrink-0">
                        عرض
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
