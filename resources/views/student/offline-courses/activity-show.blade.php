@extends('layouts.app')

@section('title', $activity->title . ' - نشاط أوفلاين')
@section('header', $activity->title)

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
            <h1 class="text-xl font-bold text-gray-900">{{ $activity->title }}</h1>
            <p class="text-sm text-gray-600 mt-1">{{ $activity->type }} | الدرجة العظمى: {{ $activity->max_score }} @if($activity->due_date)| آخر موعد: {{ $activity->due_date->format('Y-m-d') }}@endif</p>
        </div>
        <div class="p-5 sm:p-6">
            @if($activity->description)
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-gray-700 mb-2">الوصف</h3>
                    <p class="text-gray-700 whitespace-pre-line">{{ $activity->description }}</p>
                </div>
            @endif
            @if($activity->instructions)
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-gray-700 mb-2">تعليمات التسليم</h3>
                    <p class="text-gray-700 whitespace-pre-line">{{ $activity->instructions }}</p>
                </div>
            @endif
            @if($activity->attachments && count($activity->attachments) > 0)
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-gray-700 mb-2">مرفقات</h3>
                    <ul class="space-y-1">
                        @foreach($activity->attachments as $att)
                            <li><a href="{{ asset('storage/' . ($att['path'] ?? '')) }}" target="_blank" class="text-sky-600 hover:underline">{{ $att['name'] ?? 'ملف' }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($submission && $submission->status === 'graded')
                <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4 mb-4">
                    <h3 class="font-semibold text-emerald-800 mb-1">تم التصحيح</h3>
                    <p class="text-emerald-700">الدرجة: {{ $submission->score }}/{{ $activity->max_score }}</p>
                    @if($submission->feedback)
                        <p class="text-gray-700 mt-2">{{ $submission->feedback }}</p>
                    @endif
                </div>
            @endif

            @if($activity->status !== 'published')
                <p class="text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3">هذا النشاط غير متاح للتسليم حالياً.</p>
            @elseif(!$submission || $submission->status !== 'graded')
                <form action="{{ route('student.offline-courses.activities.submit', [$offlineCourse, $activity]) }}" method="post" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">نص التقديم (اختياري)</label>
                        <textarea name="submission_content" rows="5" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 focus:ring-2 focus:ring-sky-500" placeholder="اكتب إجابتك أو وصف التقديم هنا...">{{ old('submission_content', $submission->submission_content ?? '') }}</textarea>
                        @error('submission_content')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">مرفقات (اختياري)</label>
                        <input type="file" name="attachments[]" multiple class="w-full rounded-xl border border-gray-200 px-4 py-2.5">
                        <p class="text-xs text-gray-500 mt-1">يمكنك رفع أكثر من ملف. الحد الأقصى 20 ميجا للملف.</p>
                        @error('attachments.*')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="px-4 py-2.5 bg-sky-600 text-white rounded-xl font-semibold hover:bg-sky-700">
                        @if($submission && $submission->status === 'submitted')
                            تحديث التقديم
                        @else
                            تسليم النشاط
                        @endif
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
