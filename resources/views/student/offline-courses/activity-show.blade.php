@extends('layouts.app')

@section('title', $activity->title . ' - نشاط أوفلاين')
@section('header', $activity->title)

@section('content')
@php
    $maxScore = (int) $activity->max_score;
@endphp
<div class="w-full max-w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="mb-4">
        <a href="{{ route(($studentRouteGroup ?? 'student.offline-courses') . '.show', $offlineCourse) }}" class="inline-flex items-center text-sky-600 hover:text-sky-700 text-sm font-medium">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة لصفحة الكورس
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-gray-100">
            <h1 class="text-xl font-bold text-gray-900">{{ $activity->title }}</h1>
            <p class="text-sm text-gray-600 mt-1">
                {{ $activity->type }}
                | الدرجة العظمى: {{ $activity->max_score }}
                @if($activity->due_date)| آخر موعد: {{ $activity->due_date->format('Y-m-d') }}@endif
            </p>
        </div>
        <div class="p-5 sm:p-6 space-y-6">
            @if($activity->description)
                <div>
                    <h3 class="text-sm font-bold text-gray-700 mb-2">الوصف</h3>
                    <p class="text-gray-700 whitespace-pre-line">{{ $activity->description }}</p>
                </div>
            @endif
            @if($activity->instructions)
                <div>
                    <h3 class="text-sm font-bold text-gray-700 mb-2">تعليمات التسليم</h3>
                    <p class="text-gray-700 whitespace-pre-line">{{ $activity->instructions }}</p>
                </div>
            @endif
            @if($activity->attachments && count($activity->attachments) > 0)
                <div>
                    <h3 class="text-sm font-bold text-gray-700 mb-2">مرفقات من المدرب</h3>
                    <ul class="space-y-2">
                        @foreach($activity->attachments as $att)
                            <li>
                                <a href="{{ stored_upload_file_url($att) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sky-600 hover:underline font-medium">
                                    <i class="fas fa-paperclip text-slate-400"></i>
                                    {{ $att['name'] ?? 'ملف' }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($submission)
                <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 sm:p-5 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h3 class="font-bold text-slate-800">تسليمك</h3>
                        @if($submission->submitted_at)
                            <span class="text-xs text-slate-500">آخر تحديث: {{ $submission->submitted_at->format('Y-m-d H:i') }}</span>
                        @endif
                    </div>
                    @if($submission->status === 'submitted')
                        <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">تسليمك قيد المراجعة من المدرب. ستظهر الدرجة والملاحظات هنا بعد التصحيح.</p>
                    @endif
                    @if($submission->submission_content)
                        <div>
                            <h4 class="text-xs font-semibold text-slate-600 mb-1">نص التقديم</h4>
                            <p class="text-gray-800 whitespace-pre-line text-sm">{{ $submission->submission_content }}</p>
                        </div>
                    @endif
                    @if($submission->attachments && count($submission->attachments))
                        <div>
                            <h4 class="text-xs font-semibold text-slate-600 mb-2">ملفاتك المرفوعة</h4>
                            <ul class="space-y-2">
                                @foreach($submission->attachments as $f)
                                    <li>
                                        <a href="{{ offline_activity_submission_file_url($f) }}" target="_blank" rel="noopener" download="{{ $f['name'] ?? 'download' }}" class="inline-flex items-center gap-2 text-sky-600 hover:underline text-sm font-medium">
                                            <i class="fas fa-download text-slate-400"></i>
                                            {{ $f['name'] ?? 'ملف' }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            @if($submission && $submission->status === 'graded')
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 sm:p-5 space-y-3">
                    <h3 class="font-bold text-emerald-900">نتيجة التصحيح</h3>
                    <div class="flex flex-wrap items-baseline gap-3">
                        <p class="text-2xl font-bold text-emerald-800">{{ $submission->score }}</p>
                        <span class="text-emerald-700 font-medium">/ {{ $activity->max_score }}</span>
                        @if($maxScore > 0)
                            @php $pct = round((float) $submission->score / $maxScore * 100, 1); @endphp
                            <span class="text-sm text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-lg">نسبة: {{ $pct }}%</span>
                        @endif
                    </div>
                    @if($submission->graded_at)
                        <p class="text-xs text-emerald-800/90">تاريخ التصحيح: {{ $submission->graded_at->format('Y-m-d H:i') }}</p>
                    @endif
                    @if($submission->relationLoaded('grader') && $submission->grader)
                        <p class="text-xs text-emerald-800/90">المدرب: {{ $submission->grader->name }}</p>
                    @endif
                    @if($submission->feedback)
                        <div>
                            <h4 class="text-xs font-semibold text-emerald-900 mb-1">ملاحظات المدرب</h4>
                            <p class="text-gray-800 whitespace-pre-line text-sm leading-relaxed">{{ $submission->feedback }}</p>
                        </div>
                    @else
                        <p class="text-sm text-emerald-800/80">لم يُضف ملاحظة نصية مع هذه الدرجة.</p>
                    @endif
                </div>
            @endif

            @if($activity->status !== 'published')
                <p class="text-amber-800 bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm">هذا النشاط غير متاح للتسليم حالياً.</p>
            @elseif(!$submission || $submission->status !== 'graded')
                <div class="border-t border-gray-100 pt-6">
                    <h3 class="text-sm font-bold text-gray-800 mb-3">@if($submission && $submission->status === 'submitted')تحديث التسليم@elseتسليم النشاط@endif</h3>
                    <form action="{{ route(($studentRouteGroup ?? 'student.offline-courses') . '.activities.submit', [$offlineCourse, $activity]) }}" method="post" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">نص التقديم (اختياري)</label>
                            <textarea name="submission_content" rows="5" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 focus:ring-2 focus:ring-sky-500" placeholder="اكتب إجابتك أو وصف التقديم هنا...">{{ old('submission_content', $submission->submission_content ?? '') }}</textarea>
                            @error('submission_content')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">مرفقات (اختياري)</label>
                            <input type="file" name="attachments[]" multiple class="w-full rounded-xl border border-gray-200 px-4 py-2.5">
                            <p class="text-xs text-gray-500 mt-1">يمكنك رفع أكثر من ملف (حتى 20 ميجابايت لكل ملف). الملفات تُخزَّن على خوادم المنصة بشكل آمن.</p>
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
                </div>
            @elseif($submission && $submission->status === 'graded')
                <p class="text-sm text-slate-600 border border-slate-200 rounded-lg px-3 py-2 bg-slate-50">تم تثبيت درجتك بعد التصحيح. لا يمكن تعديل التسليم من هذه الصفحة.</p>
            @endif
        </div>
    </div>
</div>
@endsection
