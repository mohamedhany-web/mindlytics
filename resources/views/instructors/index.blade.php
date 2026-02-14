@extends('layouts.public')
@section('title', 'المدربون - Mindlytics')
@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
    <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">مدربونا</h1>
        <p class="text-slate-600">تعرف على فريق المدربين والخبراء.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($profiles as $p)
        <a href="{{ route('public.instructors.show', $p->user) }}" class="group rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-lg overflow-hidden block">
            <div class="aspect-[4/3] bg-slate-100 overflow-hidden relative">
                @if($p->photo_path)
                    <img src="{{ $p->photo_url }}" alt="{{ $p->user->name }}" class="absolute inset-0 w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                    <div class="hidden absolute inset-0 w-full h-full flex items-center justify-center text-slate-400 bg-slate-100"><i class="fas fa-user text-6xl"></i></div>
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-400"><i class="fas fa-user text-6xl"></i></div>
                @endif
                @if(!empty($p->social_links['linkedin']))
                <span role="link" tabindex="0" data-linkedin="{{ $p->social_links['linkedin'] }}" onclick="event.preventDefault(); event.stopPropagation(); window.open(this.getAttribute('data-linkedin'), '_blank');" class="absolute bottom-3 left-3 z-10 w-9 h-9 rounded-xl bg-[#0A66C2] text-white flex items-center justify-center shadow-md hover:bg-[#004182] hover:scale-105 transition-all cursor-pointer" title="LinkedIn" aria-label="LinkedIn"><i class="fab fa-linkedin text-lg"></i></span>
                @endif
            </div>
            <div class="p-5">
                <h2 class="text-lg font-bold text-slate-900">{{ $p->user->name }}</h2>
                <p class="text-sm text-slate-600 mt-1">{{ $p->headline ?? 'مدرب' }}</p>
            </div>
        </a>
        @empty
        <div class="col-span-full text-center py-12 text-slate-500">لا يوجد مدربون معروضون حالياً.</div>
        @endforelse
    </div>
</div>
@endsection
