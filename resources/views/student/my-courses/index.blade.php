@extends('layouts.app')

@section('title', 'كورساتي المفعلة')
@section('header', 'كورساتي المفعلة')

@push('styles')
<style>
    .course-card {
        transition: all 0.25s ease;
        background: #fff;
        border: 1px solid #e5e7eb;
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }

    .course-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(14, 165, 233, 0.12);
        border-color: #bae6fd;
    }

    .course-thumbnail {
        position: relative;
        overflow: hidden;
    }

    .stats-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }

    .stats-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .empty-state {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">كورساتي المفعلة</h1>
                <p class="text-sm text-gray-500">الكورسات التي تم تفعيلها لك من قبل الإدارة</p>
            </div>
            <a href="{{ route('academic-years') }}" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                <i class="fas fa-search"></i>
                تصفح كورسات جديدة
            </a>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="stats-card p-4">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">المفعلة</p>
                    <p class="text-2xl font-bold text-sky-600 leading-none">{{ $stats['total_active'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-sky-100 flex items-center justify-center text-sky-600 flex-shrink-0">
                    <i class="fas fa-book-open"></i>
                </div>
            </div>
        </div>
        <div class="stats-card p-4">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">مكتملة</p>
                    <p class="text-2xl font-bold text-emerald-600 leading-none">{{ $stats['total_completed'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="stats-card p-4">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">الساعات</p>
                    <p class="text-2xl font-bold text-gray-700 leading-none">{{ $stats['total_hours'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 flex-shrink-0">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="stats-card p-4">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">متوسط التقدم</p>
                    <p class="text-2xl font-bold text-amber-600 leading-none">{{ $stats['avg_progress'] }}%</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- الكورسات -->
    @if($activeCourses->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($activeCourses as $course)
            @php
                $progress = $course->pivot->progress ?? 0;
                $isCompleted = $progress >= 100;
            @endphp
            <a href="{{ route('my-courses.show', $course) }}" class="course-card block">
                <div class="course-thumbnail h-36 bg-sky-100 flex items-center justify-center relative">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="text-sky-600">
                            <i class="fas fa-graduation-cap text-3xl"></i>
                            <p class="text-xs font-medium mt-1 text-sky-700">{{ $course->academicSubject->name ?? 'كورس' }}</p>
                        </div>
                    @endif
                    @if($isCompleted)
                        <span class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-semibold bg-emerald-500 text-white">
                            <i class="fas fa-check-circle"></i> مكتمل
                        </span>
                    @else
                        <span class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-semibold bg-sky-500 text-white">
                            <i class="fas fa-play-circle"></i> مفعل
                        </span>
                    @endif
                </div>

                <div class="p-4">
                    <h3 class="text-base font-bold text-gray-900 line-clamp-2 mb-2 leading-snug">{{ $course->title }}</h3>
                    <p class="text-xs text-gray-500 mb-3">
                        {{ $course->academicSubject->name ?? '—' }} · {{ $course->teacher->name ?? '—' }} · {{ $course->lessons->count() }} درس
                    </p>

                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="text-xs font-medium text-gray-600">التقدم</span>
                        <span class="text-sm font-bold text-sky-600">{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                        <div class="h-full bg-sky-500 rounded-full transition-all duration-500" style="width: {{ min($progress, 100) }}%;"></div>
                    </div>

                    <span class="mt-3 inline-flex items-center justify-center gap-2 w-full py-2.5 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold transition-colors">
                        <i class="fas fa-play text-xs"></i>
                        متابعة التعلم
                    </span>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-6 flex justify-center">
            {{ $activeCourses->links() }}
        </div>
    @else
        <div class="empty-state rounded-xl p-10 sm:p-12 text-center">
            <div class="w-16 h-16 bg-sky-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-sky-600">
                <i class="fas fa-graduation-cap text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">لا توجد كورسات مفعلة</h3>
            <p class="text-sm text-gray-500 mb-6 max-w-sm mx-auto">لم يتم تفعيل أي كورسات لك بعد. يمكنك تصفح الكورسات المتاحة وطلب تفعيلها.</p>
            <a href="{{ route('academic-years') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold rounded-lg transition-colors">
                <i class="fas fa-search"></i>
                تصفح الكورسات
            </a>
        </div>
    @endif
</div>
@endsection
