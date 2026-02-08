@extends('layouts.app')

@section('title', 'محاضرات الكورس - ' . $offlineCourse->title)
@section('header', 'محاضرات الكورس الأوفلاين')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="mb-4">
        <a href="{{ route('student.offline-courses.show', $offlineCourse) }}" class="inline-flex items-center text-sky-600 hover:text-sky-700 text-sm font-medium">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة لصفحة الكورس
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-gray-100">
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-chalkboard-teacher text-violet-500"></i>
                محاضرات الكورس (أوفلاين) — {{ $offlineCourse->title }}
            </h1>
        </div>
        @if($lectures->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-chalkboard-teacher text-4xl mb-3 opacity-50"></i>
                <p>لا توجد محاضرات متاحة حالياً.</p>
            </div>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach($lectures as $lecture)
                    <li class="p-4 sm:p-5 hover:bg-gray-50/50">
                        <h3 class="font-semibold text-gray-900 mb-2">{{ $lecture->title }}</h3>
                        @if($lecture->description)
                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($lecture->description, 200) }}</p>
                        @endif
                        @if($lecture->scheduled_at)
                            <p class="text-xs text-gray-500 mb-2"><i class="fas fa-calendar ml-1"></i>{{ $lecture->scheduled_at->format('Y-m-d H:i') }}</p>
                        @endif
                        <div class="flex flex-wrap gap-2 mt-2">
                            @if($lecture->recording_url)
                                <a href="{{ $lecture->recording_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 px-3 py-1.5 bg-violet-100 text-violet-700 rounded-lg text-sm font-medium hover:bg-violet-200">
                                    <i class="fas fa-play"></i> تسجيل المحاضرة
                                </a>
                            @endif
                            @if($lecture->download_links && count($lecture->download_links) > 0)
                                @foreach($lecture->download_links as $link)
                                    <a href="{{ $link['url'] ?? '#' }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 px-3 py-1.5 bg-sky-100 text-sky-700 rounded-lg text-sm font-medium hover:bg-sky-200">
                                        <i class="fas fa-download"></i> {{ $link['label'] ?? 'تحميل' }}
                                    </a>
                                @endforeach
                            @endif
                            @if($lecture->attachments && count($lecture->attachments) > 0)
                                @foreach($lecture->attachments as $att)
                                    <a href="{{ asset('storage/' . ($att['path'] ?? '')) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200">
                                        <i class="fas fa-file"></i> {{ $att['name'] ?? 'ملف' }}
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
