@extends('layouts.app')

@section('title', 'الواجبات والاختبارات - كورس أوفلاين')
@section('header', 'الواجبات والاختبارات الأوفلاين')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="{{ route('instructor.offline-courses.index') }}" class="hover:text-amber-600">كورساتي الأوفلاين</a>
            <span class="mx-2">/</span>
            <a href="{{ route('instructor.offline-courses.show', $offlineCourse) }}" class="hover:text-amber-600">{{ $offlineCourse->title }}</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">الواجبات والاختبارات</span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-tasks text-amber-500"></i>
                واجبات واختبارات الكورس (أوفلاين)
            </h1>
            <a href="{{ route('instructor.offline-courses.activities.create', $offlineCourse) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 text-white rounded-xl font-semibold hover:bg-amber-700">
                <i class="fas fa-plus"></i>
                إضافة نشاط
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        @if($activities->isEmpty())
            <div class="p-12 text-center text-slate-500">
                <i class="fas fa-tasks text-4xl mb-3 opacity-50"></i>
                <p>لا توجد واجبات أو اختبارات بعد. أضف أنشطة للطلاب لتسليمها وتصحيحها.</p>
                <a href="{{ route('instructor.offline-courses.activities.create', $offlineCourse) }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-amber-600 text-white rounded-xl font-semibold hover:bg-amber-700">إضافة نشاط</a>
            </div>
        @else
            <ul class="divide-y divide-slate-100">
                @foreach($activities as $activity)
                    <li class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/50">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-semibold text-slate-800">{{ $activity->title }}</span>
                                <span class="text-xs px-2 py-0.5 rounded bg-slate-100 text-slate-600">{{ $activity->type }}</span>
                                @if($activity->group_id)
                                    <span class="text-xs px-2 py-0.5 rounded bg-slate-100 text-slate-600">{{ $activity->group->name ?? '' }}</span>
                                @endif
                                @if($activity->status === 'draft')
                                    <span class="text-xs px-2 py-0.5 rounded bg-amber-100 text-amber-700">مسودة</span>
                                @elseif($activity->status === 'published')
                                    <span class="text-xs px-2 py-0.5 rounded bg-emerald-100 text-emerald-700">منشور</span>
                                @endif
                            </div>
                            @if($activity->due_date)
                                <p class="text-sm text-slate-600 mt-1"><i class="fas fa-calendar ml-1"></i> آخر موعد: {{ $activity->due_date->format('Y-m-d') }}</p>
                            @endif
                            <p class="text-xs text-slate-500 mt-1">{{ $activity->submissions_count }} تقديم</p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ route('instructor.offline-courses.activities.show', [$offlineCourse, $activity]) }}" class="px-3 py-1.5 text-sm bg-amber-100 text-amber-700 rounded-lg hover:bg-amber-200">عرض والتقديمات</a>
                            <a href="{{ route('instructor.offline-courses.activities.edit', [$offlineCourse, $activity]) }}" class="px-3 py-1.5 text-sm bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200">تعديل</a>
                            <form action="{{ route('instructor.offline-courses.activities.destroy', [$offlineCourse, $activity]) }}" method="post" class="inline" onsubmit="return confirm('حذف هذا النشاط؟');">
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
