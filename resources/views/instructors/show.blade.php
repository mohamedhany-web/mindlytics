@extends('layouts.public')

@section('title', ($profile->user->name ?? 'مدرب') . ' - Mindlytics')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
    <div class="rounded-3xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="p-6 sm:p-8 flex flex-col sm:flex-row gap-6">
            <div class="flex-shrink-0">
                @if($profile->photo_path)
                    <div class="w-32 h-32 rounded-2xl border border-slate-200 overflow-hidden bg-slate-100 relative flex items-center justify-center">
                        <img src="{{ $profile->photo_url }}" alt="{{ $profile->user->name }}" class="w-full h-full object-contain" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                        <div class="hidden absolute inset-0 w-full h-full bg-slate-200 flex items-center justify-center text-slate-500"><i class="fas fa-user text-5xl"></i></div>
                    </div>
                @else
                    <div class="w-32 h-32 rounded-2xl bg-slate-200 flex items-center justify-center text-slate-500"><i class="fas fa-user text-5xl"></i></div>
                @endif
            </div>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-slate-900">{{ $profile->user->name }}</h1>
                <p class="text-sky-600 font-medium mt-1">{{ $profile->headline ?? 'مدرب' }}</p>
                @if($profile->bio)
                    <div class="mt-4 text-slate-700 whitespace-pre-line">{{ $profile->bio }}</div>
                @endif
                @if(!empty($profile->social_links['linkedin']))
                <a href="{{ $profile->social_links['linkedin'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 mt-4 rounded-xl bg-[#0A66C2] text-white px-4 py-2 text-sm font-semibold hover:bg-[#004182] transition-colors">
                    <i class="fab fa-linkedin text-lg"></i>
                    <span>LinkedIn</span>
                </a>
                @endif
            </div>
        </div>
        @if($profile->experience)
        <div class="px-6 sm:px-8 pb-6">
            <h2 class="text-lg font-bold text-slate-900 mb-2">الخبرات</h2>
            <div class="text-slate-700 whitespace-pre-line">{{ $profile->experience }}</div>
        </div>
        @endif
        @if($profile->skills_list)
        <div class="px-6 sm:px-8 pb-6">
            <h2 class="text-lg font-bold text-slate-900 mb-3">المهارات</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($profile->skills_list as $skill)
                <span class="inline-flex items-center rounded-xl bg-slate-100 text-slate-700 px-3 py-1.5 text-sm font-medium border border-slate-200">{{ $skill }}</span>
                @endforeach
            </div>
        </div>
        @endif
        @if($courses->count() > 0)
        <div class="px-6 sm:px-8 pb-8 border-t border-slate-100 pt-6">
            <h2 class="text-lg font-bold text-slate-900 mb-4">كورسات المدرب</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($courses as $c)
                <a href="{{ route('public.course.show', $c->id) }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-colors">
                    @if($c->thumbnail)
                        <img src="{{ asset('storage/' . str_replace('\\', '/', $c->thumbnail)) }}" alt="" class="w-14 h-14 rounded-lg object-cover">
                    @else
                        <div class="w-14 h-14 rounded-lg bg-slate-200 flex items-center justify-center text-slate-500"><i class="fas fa-book"></i></div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-900 truncate">{{ $c->title }}</p>
                        <p class="text-xs text-slate-500">{{ $c->price > 0 ? number_format($c->price) . ' ج.م' : 'مجاني' }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
