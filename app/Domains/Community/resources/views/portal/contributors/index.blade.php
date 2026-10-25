@extends('layouts.public')

@section('title', 'المساهمون - مجتمع الذكاء الاصطناعي')

@section('content')
<section class="py-12 md:py-16 bg-gradient-to-b from-slate-50 to-white min-h-screen" style="padding-top: 6rem;">
    <div class="w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-cyan-100 text-cyan-800 text-sm font-bold mb-4">
                <i class="fas fa-users"></i>
                <span>مجتمع الذكاء الاصطناعي</span>
            </div>
            <h1 class="text-3xl md:text-4xl font-black text-gray-900 mb-3" style="font-family: 'Tajawal', 'Cairo', sans-serif;">
                المساهمون
            </h1>
            <p class="text-gray-600 max-w-2xl mx-auto">
                أعضاء يشاركوننا مجموعات البيانات والخبرات. تعرّف عليهم.
            </p>
        </div>

        @if($contributors->isEmpty())
            <div class="rounded-3xl bg-white border border-slate-200 p-12 text-center shadow-sm">
                <div class="w-20 h-20 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-friends text-4xl"></i>
                </div>
                <h2 class="text-xl font-black text-slate-900 mb-2">لا يوجد مساهمون معروضون حالياً</h2>
                <p class="text-slate-600 mb-6">سيظهر هنا المساهمون بعد مراجعة ملفاتهم من الإدارة.</p>
                <a href="{{ route('public.community.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700">
                    <i class="fas fa-arrow-right"></i> العودة للمجتمع
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($contributors as $profile)
                    @php $user = $profile->user; @endphp
                    <a href="{{ route('community.contributors.show', $user) }}" class="group rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-lg overflow-hidden block">
                        <div class="aspect-[4/3] bg-slate-100 overflow-hidden relative">
                            @if($profile->photo_path)
                                <img src="{{ $profile->photo_url }}" alt="{{ $user->name }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                <div class="hidden absolute inset-0 w-full h-full flex items-center justify-center text-slate-400 bg-slate-100"><i class="fas fa-user text-6xl"></i></div>
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400"><i class="fas fa-user text-6xl"></i></div>
                            @endif
                            <div class="absolute bottom-3 left-3 z-10 flex flex-wrap gap-2">
                                @if($profile->linkedin_url)
                                    <span onclick="event.preventDefault(); event.stopPropagation(); window.open('{{ $profile->linkedin_url }}', '_blank');" class="w-9 h-9 rounded-xl bg-[#0A66C2] text-white flex items-center justify-center shadow-md hover:bg-[#004182] hover:scale-105 transition-all cursor-pointer" title="LinkedIn" aria-label="LinkedIn"><i class="fab fa-linkedin text-lg"></i></span>
                                @endif
                                @if($profile->twitter_url)
                                    <span onclick="event.preventDefault(); event.stopPropagation(); window.open('{{ $profile->twitter_url }}', '_blank');" class="w-9 h-9 rounded-xl bg-black text-white flex items-center justify-center shadow-md hover:opacity-90 hover:scale-105 transition-all cursor-pointer" title="X (تويتر)" aria-label="X"><i class="fab fa-x-twitter text-lg"></i></span>
                                @endif
                                @if($profile->website_url)
                                    <span onclick="event.preventDefault(); event.stopPropagation(); window.open('{{ $profile->website_url }}', '_blank');" class="w-9 h-9 rounded-xl bg-white/90 text-slate-700 flex items-center justify-center shadow-md hover:bg-white hover:scale-105 transition-all cursor-pointer" title="الموقع" aria-label="الموقع"><i class="fas fa-globe text-lg"></i></span>
                                @endif
                            </div>
                        </div>
                        <div class="p-5">
                            <h2 class="text-lg font-bold text-slate-900">{{ $user->name }}</h2>
                            <p class="text-sm text-slate-600 mt-1">{{ $profile->bio ? Str::limit($profile->bio, 100) : 'مساهم في مجتمع الذكاء الاصطناعي' }}</p>
                            <span class="inline-flex items-center gap-2 mt-3 text-cyan-600 font-semibold text-sm group-hover:gap-3 transition-all">
                                <span>عرض الملف</span>
                                <i class="fas fa-arrow-left"></i>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-12 text-center">
                <a href="{{ route('public.community.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors">
                    <i class="fas fa-arrow-right"></i> العودة لصفحة المجتمع
                </a>
            </div>
        @endif
    </div>
</section>
@endsection
