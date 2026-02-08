@extends('layouts.app')

@section('title', $activity->title . ' - نشاط أوفلاين')
@section('header', $activity->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="{{ route('instructor.offline-courses.index') }}" class="hover:text-amber-600">كورساتي الأوفلاين</a>
            <span class="mx-2">/</span>
            <a href="{{ route('instructor.offline-courses.activities.index', $offlineCourse) }}" class="hover:text-amber-600">الواجبات والاختبارات</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">{{ $activity->title }}</span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-xl font-bold text-slate-800">{{ $activity->title }}</h1>
            <a href="{{ route('instructor.offline-courses.activities.edit', [$offlineCourse, $activity]) }}" class="px-4 py-2 bg-amber-600 text-white rounded-xl font-semibold hover:bg-amber-700">تعديل</a>
        </div>
        @if($activity->description)
            <p class="text-slate-600 mt-2">{{ $activity->description }}</p>
        @endif
        <p class="text-sm text-slate-500 mt-2">{{ $activity->type }} | آخر موعد: {{ $activity->due_date ? $activity->due_date->format('Y-m-d') : '—' }} | الدرجة العظمى: {{ $activity->max_score }}</p>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-800">تقديمات الطلاب ({{ $activity->submissions->count() }})</h2>
        </div>
        @if($activity->submissions->isEmpty())
            <div class="p-12 text-center text-slate-500">لا توجد تقديمات بعد.</div>
        @else
            <ul class="divide-y divide-slate-100">
                @foreach($activity->submissions as $sub)
                    <li class="p-4 sm:p-5">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <span class="font-semibold text-slate-800">{{ $sub->student->name ?? 'طالب' }}</span>
                            <span class="text-xs px-2 py-1 rounded
                                @if($sub->status === 'graded') bg-emerald-100 text-emerald-700
                                @elseif($sub->status === 'submitted') bg-amber-100 text-amber-700
                                @else bg-slate-100 text-slate-600 @endif">
                                @if($sub->status === 'graded') مصحح ({{ $sub->score }}/{{ $activity->max_score }})
                                @elseif($sub->status === 'submitted') مقدم
                                @else قيد الانتظار @endif
                            </span>
                        </div>
                        @if($sub->submitted_at)
                            <p class="text-sm text-slate-600">تاريخ التقديم: {{ $sub->submitted_at->format('Y-m-d H:i') }}</p>
                        @endif
                        @if($sub->submission_content)
                            <p class="text-sm text-slate-700 mt-2 whitespace-pre-line">{{ Str::limit($sub->submission_content, 300) }}</p>
                        @endif
                        @if($sub->attachments && count($sub->attachments))
                            <p class="text-xs text-slate-500 mt-1">مرفقات: {{ count($sub->attachments) }} ملف</p>
                        @endif
                        @if($sub->status === 'submitted' || $sub->status === 'graded')
                            <form action="{{ route('instructor.offline-courses.activities.submissions.grade', [$offlineCourse, $activity, $sub]) }}" method="post" class="mt-3 flex flex-wrap items-end gap-2">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600">الدرجة</label>
                                    <input type="number" name="score" value="{{ old('score', $sub->score) }}" min="0" max="{{ $activity->max_score }}" step="0.5" class="w-24 rounded-lg border border-slate-200 px-2 py-1.5">
                                </div>
                                <div class="flex-1 min-w-[200px]">
                                    <label class="block text-xs font-semibold text-slate-600">ملاحظات</label>
                                    <input type="text" name="feedback" value="{{ old('feedback', $sub->feedback) }}" class="w-full rounded-lg border border-slate-200 px-2 py-1.5" placeholder="ملاحظات للطالب">
                                </div>
                                <button type="submit" class="px-3 py-1.5 bg-amber-600 text-white rounded-lg text-sm font-semibold hover:bg-amber-700">تصحيح</button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
