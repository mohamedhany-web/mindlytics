@extends('layouts.app')

@section('title', 'المسار التعليمي - ' . $learningPath->name)
@section('header', 'المسار التعليمي - ' . $learningPath->name)

@push('styles')
<style>
    .progress-ring {
        transform: rotate(-90deg);
    }
    
    .progress-ring-circle {
        transition: stroke-dashoffset 0.35s;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-50 via-green-50 to-blue-50 rounded-2xl p-6 border-2 border-blue-200 shadow-lg">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    @if($learningPath->icon)
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-green-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                            <i class="{{ $learningPath->icon }} text-3xl"></i>
                        </div>
                    @else
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-green-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                            <i class="fas fa-route text-3xl"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl md:text-3xl font-black text-gray-900">{{ $learningPath->name }}</h1>
                        @if($learningPath->code)
                            <p class="text-sm text-gray-600 mt-1">الرمز: {{ $learningPath->code }}</p>
                        @endif
                    </div>
                </div>
                @if($learningPath->description)
                    <p class="text-gray-700 leading-relaxed">{{ $learningPath->description }}</p>
                @endif
            </div>
            <div class="flex-shrink-0">
                <div class="bg-white rounded-xl p-6 border-2 border-blue-200 shadow-lg text-center min-w-[150px]">
                    <div class="text-4xl font-black text-blue-600 mb-2">{{ $learningPath->progress }}%</div>
                    <div class="text-sm text-gray-600 font-semibold">التقدم</div>
                    <div class="mt-3 w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-blue-600 to-green-500 h-3 rounded-full transition-all duration-500" style="width: {{ $learningPath->progress }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 border-2 border-blue-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">إجمالي الكورسات</p>
                    <p class="text-3xl font-black text-blue-600">{{ $learningPath->courses_count }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-book text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border-2 border-green-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">الكورسات المسجلة</p>
                    <p class="text-3xl font-black text-green-600">{{ $learningPath->enrolled_courses_count }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border-2 border-yellow-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">المتبقي</p>
                    <p class="text-3xl font-black text-yellow-600">{{ $learningPath->courses_count - $learningPath->enrolled_courses_count }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border-2 border-purple-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">تاريخ التسجيل</p>
                    <p class="text-lg font-black text-purple-600">{{ $enrollment->enrolled_at->format('Y-m-d') }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Video Introduction -->
    @if($learningPath->video_url)
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <h2 class="text-xl font-black text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-play-circle text-blue-600"></i>
            مقدمة المسار التعليمي
        </h2>
        <div class="relative w-full" style="padding-bottom: 56.25%;">
            @php
                $videoUrl = $learningPath->video_url;
                if (strpos($videoUrl, 'youtube.com/watch') !== false || strpos($videoUrl, 'youtu.be/') !== false) {
                    preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $videoUrl, $matches);
                    if (isset($matches[1])) {
                        $videoUrl = 'https://www.youtube.com/embed/' . $matches[1];
                    }
                }
            @endphp
            <iframe src="{{ $videoUrl }}" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen
                    class="absolute top-0 left-0 w-full h-full rounded-lg"
                    loading="lazy">
            </iframe>
        </div>
    </div>
    @endif

    <!-- Courses List -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-black text-gray-900 flex items-center gap-2">
                <i class="fas fa-graduation-cap text-blue-600"></i>
                الكورسات في المسار
            </h2>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse($learningPath->courses as $index => $course)
            <div class="p-6 hover:bg-gray-50 transition-all {{ $course->is_enrolled ? 'bg-green-50/50' : '' }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-green-500 rounded-xl flex items-center justify-center text-white font-bold shadow-lg">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-black text-gray-900 mb-1">{{ $course->title }}</h3>
                                @if($course->academicSubject)
                                    <p class="text-sm text-gray-600">
                                        <i class="fas fa-book mr-1"></i>
                                        {{ $course->academicSubject->name }}
                                    </p>
                                @endif
                            </div>
                            @if($course->is_enrolled)
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    مسجل
                                </span>
                            @endif
                        </div>
                        
                        @if($course->description)
                            <p class="text-gray-600 mb-3 line-clamp-2">{{ Str::limit($course->description, 150) }}</p>
                        @endif

                        <div class="flex items-center gap-4 flex-wrap text-sm text-gray-600">
                            @if($course->lessons_count > 0)
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-play-circle text-blue-600"></i>
                                    {{ $course->lessons_count }} درس
                                </span>
                            @endif
                            @if($course->instructor)
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-user-tie text-green-600"></i>
                                    {{ $course->instructor->name }}
                                </span>
                            @endif
                            @if(($course->price ?? 0) > 0)
                                <span class="flex items-center gap-1 font-bold text-blue-600">
                                    <i class="fas fa-tag"></i>
                                    {{ number_format($course->price, 0) }} ج.م
                                </span>
                            @else
                                <span class="flex items-center gap-1 font-bold text-green-600">
                                    <i class="fas fa-gift"></i>
                                    مجاني
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        @if($course->is_enrolled)
                            <a href="{{ route('my-courses.show', $course->id) }}" 
                               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-play mr-1"></i>
                                ابدأ التعلم
                            </a>
                        @else
                            <a href="{{ route('courses.show', $course->id) }}" 
                               class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-eye mr-1"></i>
                                عرض الكورس
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-book text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-600">لا توجد كورسات في هذا المسار حالياً</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
