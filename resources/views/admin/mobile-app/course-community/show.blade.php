@extends('layouts.admin')

@section('title', 'منشور #'.$post->id)
@section('page_title', 'منشور مجتمع الكورس #'.$post->id)

@section('content')
<div class="w-full min-h-screen p-3 sm:p-4 md:p-6 lg:p-8 space-y-6" style="background: #f8fafc;">

    @if(session('success'))
        <div class="rounded-2xl border-2 border-emerald-200/80 bg-gradient-to-r from-emerald-50 to-white px-5 py-4 text-emerald-900 shadow-lg flex items-center gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg"><i class="fas fa-check text-lg"></i></span>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="flex flex-wrap items-center gap-4 justify-between">
        <a href="{{ route('admin.mobile-app.course-community.index') }}" class="text-slate-600 hover:text-violet-600 font-semibold">
            <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} ml-1"></i> كل المنشورات
        </a>
        <div class="flex flex-wrap gap-2">
            <form method="post" action="{{ route('admin.mobile-app.course-community.posts.pin', $post) }}" class="inline">
                @csrf
                <button type="submit" class="rounded-xl border-2 border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold px-4 py-2 text-sm">
                    <i class="fas fa-thumbtack ml-1"></i> {{ $post->is_pinned ? 'إلغاء التثبيت' : 'تثبيت' }}
                </button>
            </form>
            <form method="post" action="{{ route('admin.mobile-app.course-community.posts.destroy', $post) }}" class="inline" onsubmit="return confirm('حذف المنشور وجميع التعليقات المرتبطة؟');">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold px-4 py-2 text-sm">
                    <i class="fas fa-trash ml-1"></i> حذف المنشور
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-wrap gap-4 text-sm">
            <span class="rounded-full bg-slate-100 px-3 py-1 font-bold text-slate-800"><i class="fas fa-book ml-1 text-violet-600"></i> {{ $post->course->title ?? '—' }}</span>
            <span class="rounded-full bg-slate-100 px-3 py-1 font-bold text-slate-800"><i class="fas fa-user ml-1 text-violet-600"></i> {{ $post->user->name ?? '—' }}</span>
            @if($post->user->email ?? null)
                <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">{{ $post->user->email }}</span>
            @endif
            <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">{{ $post->created_at?->format('Y-m-d H:i') }}</span>
            @if($post->is_pinned)
                <span class="rounded-full bg-amber-100 text-amber-900 px-3 py-1 font-bold"><i class="fas fa-thumbtack"></i> مثبّت</span>
            @endif
        </div>

        <div class="prose prose-slate max-w-none">
            <p class="whitespace-pre-wrap text-slate-800 leading-relaxed">{{ $post->body }}</p>
        </div>

        @if($post->images->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 pt-2">
                @foreach($post->images as $img)
                    <a href="{{ $img->url }}" target="_blank" rel="noopener" class="block rounded-xl overflow-hidden border border-slate-200 shadow-sm hover:opacity-95">
                        <img src="{{ $img->url }}" alt="" class="w-full h-36 object-cover"/>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
            <i class="fas fa-comments text-violet-600"></i> التعليقات ({{ $post->comments->count() }})
        </h2>
        @forelse($post->comments as $comment)
            <div class="border-b border-slate-100 last:border-0 py-4 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-900">{{ $comment->user->name ?? '—' }}</p>
                    @if($comment->user->email ?? null)
                        <p class="text-xs text-slate-500">{{ $comment->user->email }}</p>
                    @endif
                    <p class="mt-2 text-slate-700 whitespace-pre-wrap">{{ $comment->body }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $comment->created_at?->format('Y-m-d H:i') }}</p>
                </div>
                <form method="post" action="{{ route('admin.mobile-app.course-community.posts.comments.destroy', [$post, $comment]) }}" class="shrink-0" onsubmit="return confirm('حذف هذا التعليق؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-sm whitespace-nowrap">
                        <i class="fas fa-times-circle"></i> حذف
                    </button>
                </form>
            </div>
        @empty
            <p class="text-slate-500 font-semibold">لا توجد تعليقات بعد.</p>
        @endforelse
    </div>
</div>
@endsection
