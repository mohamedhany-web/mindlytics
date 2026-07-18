@extends('layouts.app')

@section('title', 'تفاصيل المشاركة')
@section('header', 'تفاصيل المشاركة')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-5">
    <a href="{{ route('instructor.learn-discussions.index') }}" class="text-sm font-semibold text-teal-700 hover:underline">← رجوع للقائمة</a>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 text-sm px-4 py-3">{{ session('success') }}</div>
    @endif

    <article class="bg-white border border-slate-200 rounded-xl p-5 space-y-3">
        <div class="flex flex-wrap gap-2 items-center text-xs text-slate-500">
            <span class="font-bold px-2 py-0.5 rounded-full {{ $discussion->kind === 'qa' ? 'bg-amber-50 text-amber-800' : 'bg-teal-50 text-teal-800' }}">
                {{ $discussion->kind === 'qa' ? 'سؤال للطالب' : 'نقاش' }}
            </span>
            <span>{{ $discussion->course?->title }}</span>
            <span>·</span>
            <span>{{ $contextTitle }}</span>
        </div>
        <h1 class="text-base font-bold text-slate-900">{{ $discussion->user?->name }}</h1>
        <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap">{{ $discussion->body }}</p>
        <time class="text-xs text-slate-400">{{ $discussion->created_at?->format('Y/m/d H:i') }}</time>
    </article>

    <section class="space-y-3">
        <h2 class="text-sm font-bold text-slate-800">الردود ({{ $discussion->replies->count() }})</h2>
        @forelse($discussion->replies as $reply)
            <div class="rounded-xl border p-4 {{ $reply->isInstructorAuthor() ? 'border-teal-200 bg-teal-50/40' : 'border-slate-200 bg-white' }}">
                <div class="flex items-center gap-2 text-xs mb-1">
                    <strong class="text-slate-900 text-sm">{{ $reply->user?->name }}</strong>
                    @if($reply->isInstructorAuthor())
                        <span class="text-teal-700 font-bold">مدرب</span>
                    @endif
                    <span class="text-slate-400 ms-auto">{{ $reply->created_at?->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $reply->body }}</p>
            </div>
        @empty
            <p class="text-sm text-slate-500">لا توجد ردود بعد.</p>
        @endforelse
    </section>

    <form method="post" action="{{ route('instructor.learn-discussions.reply', $discussion) }}" class="bg-white border border-slate-200 rounded-xl p-4 space-y-3">
        @csrf
        <label class="block text-sm font-bold text-slate-800">ردّك للطالب</label>
        <textarea name="body" rows="4" required minlength="2" maxlength="5000"
                  class="w-full rounded-lg border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500"
                  placeholder="اكتب ردك هنا…">{{ old('body') }}</textarea>
        @error('body')
            <p class="text-xs text-red-600">{{ $message }}</p>
        @enderror
        <button type="submit" class="rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-5 py-2.5">إرسال الرد</button>
    </form>
</div>
@endsection
