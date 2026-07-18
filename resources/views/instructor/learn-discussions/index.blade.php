@extends('layouts.app')

@section('title', 'نقاش وأسئلة الطلاب')
@section('header', 'نقاش وأسئلة الطلاب')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-900">نقاش وأسئلة الطلاب</h1>
            <p class="text-sm text-slate-500 mt-1">اطّلع على ما يكتبه طلابك في صفحة التعلّم وردّ عليهم.</p>
        </div>
        @if(($unreadQa ?? 0) > 0)
            <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 text-amber-800 border border-amber-200 px-3 py-1.5 text-xs font-bold">
                {{ $unreadQa }} سؤال بانتظار ردك
            </span>
        @endif
    </div>

    <form method="get" class="flex flex-wrap gap-2 items-center bg-white border border-slate-200 rounded-xl p-3">
        <select name="kind" class="rounded-lg border-slate-200 text-sm">
            <option value="">الكل</option>
            <option value="qa" @selected(($kind ?? '') === 'qa')>أسئلة وأجوبة</option>
            <option value="discussion" @selected(($kind ?? '') === 'discussion')>نقاش</option>
        </select>
        <select name="course_id" class="rounded-lg border-slate-200 text-sm min-w-[180px]">
            <option value="">كل المقررات</option>
            @foreach($courses as $c)
                <option value="{{ $c->id }}" @selected((string) ($courseFilter ?? '') === (string) $c->id)>{{ $c->title }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-4 py-2">تصفية</button>
    </form>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden divide-y divide-slate-100">
        @forelse($threads as $thread)
            <a href="{{ route('instructor.learn-discussions.show', $thread) }}"
               class="block p-4 hover:bg-slate-50 transition-colors">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $thread->kind === 'qa' ? 'bg-amber-50 text-amber-800' : 'bg-teal-50 text-teal-800' }}">
                        {{ $thread->kind === 'qa' ? 'سؤال' : 'نقاش' }}
                    </span>
                    <span class="text-xs text-slate-500">{{ $thread->course?->title }}</span>
                    <span class="text-xs text-slate-400 ms-auto">{{ $thread->created_at?->diffForHumans() }}</span>
                </div>
                <p class="text-sm font-semibold text-slate-900 line-clamp-2">{{ $thread->body }}</p>
                <p class="text-xs text-slate-500 mt-1">
                    {{ $thread->user?->name }}
                    · {{ $thread->replies_count }} رد
                </p>
            </a>
        @empty
            <div class="p-10 text-center text-slate-500 text-sm">لا توجد مشاركات بعد من الطلاب.</div>
        @endforelse
    </div>

    <div class="flex justify-center">{{ $threads->links() }}</div>
</div>
@endsection
