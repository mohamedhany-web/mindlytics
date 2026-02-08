@extends('layouts.app')

@section('title', 'محاضرات الكورس الأوفلاين - ' . $offlineCourse->title)
@section('header', 'محاضرات الكورس الأوفلاين')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="{{ route('instructor.offline-courses.index') }}" class="hover:text-amber-600">كورساتي الأوفلاين</a>
            <span class="mx-2">/</span>
            <a href="{{ route('instructor.offline-courses.show', $offlineCourse) }}" class="hover:text-amber-600">{{ $offlineCourse->title }}</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">المحاضرات</span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-chalkboard-teacher text-violet-500"></i>
                محاضرات الكورس (أوفلاين)
            </h1>
            <a href="{{ route('instructor.offline-courses.lectures.create', $offlineCourse) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-600 text-white rounded-xl font-semibold hover:bg-violet-700 transition-colors">
                <i class="fas fa-plus"></i>
                إضافة محاضرة
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        @if($lectures->isEmpty())
            <div class="p-12 text-center text-slate-500">
                <i class="fas fa-chalkboard-teacher text-4xl mb-3 opacity-50"></i>
                <p>لا توجد محاضرات بعد. أضف محاضرات مع روابط تحميل أو تسجيل.</p>
                <a href="{{ route('instructor.offline-courses.lectures.create', $offlineCourse) }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-violet-600 text-white rounded-xl font-semibold hover:bg-violet-700">إضافة محاضرة</a>
            </div>
        @else
            <ul class="divide-y divide-slate-100">
                @foreach($lectures as $lecture)
                    <li class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/50">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-semibold text-slate-800">{{ $lecture->title }}</span>
                                @if($lecture->group_id)
                                    <span class="text-xs px-2 py-0.5 rounded bg-slate-100 text-slate-600">{{ $lecture->group->name ?? '' }}</span>
                                @endif
                                @if(!$lecture->is_active)
                                    <span class="text-xs px-2 py-0.5 rounded bg-slate-200 text-slate-600">معطل</span>
                                @endif
                            </div>
                            @if($lecture->scheduled_at)
                                <p class="text-sm text-slate-600 mt-1"><i class="fas fa-calendar ml-1"></i>{{ $lecture->scheduled_at->format('Y-m-d H:i') }}</p>
                            @endif
                            @if($lecture->recording_url || ($lecture->download_links && count($lecture->download_links)))
                                <p class="text-xs text-slate-500 mt-1">
                                    @if($lecture->recording_url) تسجيل @endif
                                    @if($lecture->download_links && count($lecture->download_links)) | {{ count($lecture->download_links) }} رابط تحميل @endif
                                </p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ route('instructor.offline-courses.lectures.show', [$offlineCourse, $lecture]) }}" class="px-3 py-1.5 text-sm bg-violet-100 text-violet-700 rounded-lg hover:bg-violet-200">عرض</a>
                            <a href="{{ route('instructor.offline-courses.lectures.edit', [$offlineCourse, $lecture]) }}" class="px-3 py-1.5 text-sm bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200">تعديل</a>
                            <form action="{{ route('instructor.offline-courses.lectures.destroy', [$offlineCourse, $lecture]) }}" method="post" class="inline" onsubmit="return confirm('حذف هذه المحاضرة؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 text-sm bg-red-50 text-red-600 rounded-lg hover:bg-red-100">حذف</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
