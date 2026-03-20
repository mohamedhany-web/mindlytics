@extends('layouts.app')

@section('title', $assignment->title)
@section('header', 'تفاصيل الواجب')

@section('content')
<div class="w-full max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
    @if(session('success'))
        <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 mb-6">
        <h1 class="text-2xl font-bold text-slate-800">{{ $assignment->title }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ $assignment->course->title ?? '—' }}</p>
        @if($assignment->description)
            <p class="text-slate-700 mt-4 whitespace-pre-wrap">{{ $assignment->description }}</p>
        @endif
        @if($assignment->instructions)
            <div class="mt-4 p-4 bg-sky-50 border border-sky-200 rounded-xl text-sky-900 whitespace-pre-wrap">{{ $assignment->instructions }}</div>
        @endif
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
            <div class="rounded-lg border border-slate-200 p-3"><span class="text-slate-500">الدرجة:</span> <span class="font-semibold text-slate-800">{{ $assignment->max_score }}</span></div>
            <div class="rounded-lg border border-slate-200 p-3"><span class="text-slate-500">آخر موعد:</span> <span class="font-semibold text-slate-800">{{ $assignment->due_date ? $assignment->due_date->format('Y-m-d H:i') : 'غير محدد' }}</span></div>
            <div class="rounded-lg border border-slate-200 p-3"><span class="text-slate-500">التسليم المتأخر:</span> <span class="font-semibold text-slate-800">{{ $assignment->allow_late_submission ? 'مسموح' : 'غير مسموح' }}</span></div>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-4">تسليم الواجب</h2>
        <form method="POST" action="{{ route('student.assignments.submit', $assignment) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">المحتوى / رابط المشروع</label>
                <textarea name="content" rows="5" class="w-full px-3 py-2 border border-slate-200 rounded-xl" placeholder="اكتب الحل أو رابط المشروع...">{{ old('content', $submission->content ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">مرفقات (حتى 100MB لكل ملف)</label>
                <input type="file" name="attachments[]" multiple class="w-full text-sm text-slate-600" />
                <p class="text-xs text-slate-500 mt-1">الملفات سترفع على Cloudflare تلقائياً.</p>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-semibold">
                <i class="fas fa-upload"></i>
                تسليم الواجب
            </button>
        </form>

        @if($submission)
            <div class="mt-6 pt-6 border-t border-slate-200">
                <h3 class="font-bold text-slate-800 mb-2">آخر تسليم</h3>
                <p class="text-sm text-slate-600">الحالة: <span class="font-semibold">{{ $submission->status }}</span></p>
                @if($submission->submitted_at)
                    <p class="text-sm text-slate-600">تاريخ التسليم: {{ $submission->submitted_at->format('Y-m-d H:i') }}</p>
                @endif
                @if($submission->score !== null)
                    <p class="text-sm text-slate-600">الدرجة: <span class="font-semibold text-sky-700">{{ $submission->score }}</span> / {{ $assignment->max_score }}</p>
                @endif
                @if($submission->feedback)
                    <div class="mt-2 p-3 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 whitespace-pre-wrap">{{ $submission->feedback }}</div>
                @endif
                @if($submission->attachments && count($submission->attachments))
                    <ul class="mt-3 space-y-1 text-sm">
                        @foreach($submission->attachments as $att)
                            @php
                                $path = is_string($att) ? $att : ($att['path'] ?? $att['url'] ?? null);
                                $url = is_array($att) && !empty($att['url']) ? $att['url'] : ($path ? (str_starts_with($path, 'http') ? $path : url('storage/'.$path)) : '#');
                                $name = is_array($att) ? ($att['name'] ?? basename($path ?? 'attachment')) : basename($att);
                            @endphp
                            <li><a href="{{ $url }}" target="_blank" rel="noopener" class="text-sky-600 hover:underline">{{ $name }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
