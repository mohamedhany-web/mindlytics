@extends('layouts.app')

@section('title', $lecture->title . ' - محاضرة أوفلاين')
@section('header', $lecture->title)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="{{ route('instructor.offline-courses.index') }}" class="hover:text-amber-600">كورساتي الأوفلاين</a>
            <span class="mx-2">/</span>
            <a href="{{ route('instructor.offline-courses.lectures.index', $offlineCourse) }}" class="hover:text-amber-600">المحاضرات</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">{{ $lecture->title }}</span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-xl font-bold text-slate-800">{{ $lecture->title }}</h1>
            <a href="{{ route('instructor.offline-courses.lectures.edit', [$offlineCourse, $lecture]) }}" class="px-4 py-2 bg-violet-600 text-white rounded-xl font-semibold hover:bg-violet-700">تعديل</a>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 space-y-4">
        @if($lecture->description)
            <div>
                <h3 class="text-sm font-bold text-slate-600 mb-2">الوصف</h3>
                <p class="text-slate-700 whitespace-pre-line">{{ $lecture->description }}</p>
            </div>
        @endif
        @if($lecture->scheduled_at)
            <div>
                <h3 class="text-sm font-bold text-slate-600 mb-2">الموعد</h3>
                <p class="text-slate-700">{{ $lecture->scheduled_at->format('Y-m-d H:i') }} @if($lecture->duration_minutes)({{ $lecture->duration_minutes }} دقيقة)@endif</p>
            </div>
        @endif
        @if($lecture->recording_url)
            <div>
                <h3 class="text-sm font-bold text-slate-600 mb-2">تسجيل المحاضرة</h3>
                <a href="{{ $lecture->recording_url }}" target="_blank" rel="noopener" class="text-violet-600 hover:underline font-medium">{{ $lecture->recording_url }}</a>
            </div>
        @endif
        @if($lecture->download_links && count($lecture->download_links) > 0)
            <div>
                <h3 class="text-sm font-bold text-slate-600 mb-2">روابط التحميل</h3>
                <ul class="space-y-2">
                    @foreach($lecture->download_links as $link)
                        <li><a href="{{ $link['url'] ?? '#' }}" target="_blank" rel="noopener" class="text-violet-600 hover:underline">{{ $link['label'] ?? 'رابط' }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if($lecture->attachments && count($lecture->attachments) > 0)
            <div>
                <h3 class="text-sm font-bold text-slate-600 mb-2">مرفقات</h3>
                <ul class="space-y-2">
                    @foreach($lecture->attachments as $att)
                        <li><a href="{{ asset('storage/' . ($att['path'] ?? '')) }}" target="_blank" class="text-violet-600 hover:underline">{{ $att['name'] ?? 'ملف' }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if($lecture->notes)
            <div>
                <h3 class="text-sm font-bold text-slate-600 mb-2">ملاحظات</h3>
                <p class="text-slate-700 whitespace-pre-line">{{ $lecture->notes }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
