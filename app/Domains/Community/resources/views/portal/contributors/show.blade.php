@extends('layouts.public')

@section('title', ($profile->user->name ?? 'مساهم') . ' - المساهمون | مجتمع الذكاء الاصطناعي')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-12" style="padding-top: 6rem;">
    <div class="rounded-3xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="p-6 sm:p-8 flex flex-col sm:flex-row gap-6">
            <div class="flex-shrink-0">
                @if($profile->photo_path)
                    <div class="w-32 h-32 rounded-2xl border border-slate-200 overflow-hidden bg-slate-100 relative">
                        <img src="{{ $profile->photo_url }}" alt="{{ $profile->user->name }}" class="absolute inset-0 w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                        <div class="hidden absolute inset-0 w-full h-full bg-slate-200 flex items-center justify-center text-slate-500"><i class="fas fa-user text-5xl"></i></div>
                    </div>
                @else
                    <div class="w-32 h-32 rounded-2xl bg-slate-200 flex items-center justify-center text-slate-500"><i class="fas fa-user text-5xl"></i></div>
                @endif
            </div>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-slate-900">{{ $profile->user->name }}</h1>
                <p class="text-cyan-600 font-medium mt-1">مساهم في مجتمع الذكاء الاصطناعي</p>
                @if($profile->bio)
                    <div class="mt-4 text-slate-700 whitespace-pre-line">{{ $profile->bio }}</div>
                @endif
                <div class="flex flex-wrap gap-2 mt-4">
                    @if($profile->linkedin_url)
                        <a href="{{ $profile->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl bg-[#0A66C2] text-white px-4 py-2 text-sm font-semibold hover:bg-[#004182] transition-colors">
                            <i class="fab fa-linkedin text-lg"></i>
                            <span>LinkedIn</span>
                        </a>
                    @endif
                    @if($profile->twitter_url)
                        <a href="{{ $profile->twitter_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl bg-slate-800 text-white px-4 py-2 text-sm font-semibold hover:bg-black transition-colors">
                            <i class="fab fa-x-twitter text-lg"></i>
                            <span>X</span>
                        </a>
                    @endif
                    @if($profile->website_url)
                        <a href="{{ $profile->website_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl bg-slate-100 text-slate-700 border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-200 transition-colors">
                            <i class="fas fa-globe text-lg"></i>
                            <span>الموقع</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @if($profile->experience)
        <div class="px-6 sm:px-8 pb-6">
            <h2 class="text-lg font-bold text-slate-900 mb-3">الخبرات</h2>
            <div class="text-slate-700 whitespace-pre-line break-words pe-2">{{ $profile->experience }}</div>
        </div>
        @endif
    </div>
    <div class="mt-6">
        <a href="{{ route('community.contributors.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 font-semibold">
            <i class="fas fa-arrow-right"></i>
            العودة لصفحة المساهمين
        </a>
    </div>
</div>
@endsection
