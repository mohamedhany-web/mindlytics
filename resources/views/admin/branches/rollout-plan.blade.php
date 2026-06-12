@extends('layouts.admin')

@section('title', 'خطة الفروع والتوسع')

@section('content')
<div class="p-6 lg:p-8 max-w-4xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.branches.index') }}" class="text-sm text-blue-600 hover:underline">← فروع المنصة</a>
            <h1 class="text-2xl font-black text-slate-900 mt-2 flex items-center gap-2">
                <i class="fas fa-map text-amber-500"></i>
                خطة الفروع والتوسع
            </h1>
            <p class="text-sm text-slate-500 mt-1">المحتوى من الملف: <code class="bg-slate-100 px-1 rounded text-xs" dir="ltr">docs/branches-platform-rollout.md</code></p>
        </div>
    </div>

    <article class="rollout-markdown max-w-none bg-white rounded-2xl border border-slate-200 p-6 lg:p-8 text-slate-800 leading-relaxed space-y-4
        [&_h1]:text-2xl [&_h1]:font-black [&_h1]:text-slate-900 [&_h2]:text-lg [&_h2]:font-bold [&_h2]:mt-6 [&_h3]:font-semibold
        [&_ul]:list-disc [&_ul]:mr-6 [&_ol]:list-decimal [&_ol]:mr-6 [&_a]:text-blue-600 [&_code]:bg-slate-100 [&_code]:px-1 [&_code]:rounded [&_pre]:bg-slate-900 [&_pre]:text-slate-100 [&_pre]:p-4 [&_pre]:rounded-xl [&_pre]:overflow-x-auto">
        {!! $rolloutHtml !!}
    </article>
</div>
@endsection
