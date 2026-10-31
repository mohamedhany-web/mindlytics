@extends('layouts.public')

@section('title', __('public.community_page_title'))

@section('content')
<section class="py-12 md:py-20 bg-gradient-to-b from-slate-50 to-white w-full" style="padding-top: 6rem;">
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14 md:mb-20">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-100 text-blue-800 text-sm font-bold mb-6">
                <i class="fas fa-users-cog"></i>
                <span>{{ __('landing.nav.community') }}</span>
            </div>
            <h1 class="text-3xl md:text-5xl lg:text-6xl font-black text-gray-900 mb-6 leading-tight" style="font-family: 'Tajawal', 'Cairo', sans-serif;">
                <span class="bg-gradient-to-r from-blue-600 via-green-500 to-blue-600 bg-clip-text text-transparent">{{ __('public.community_heading') }}</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                {{ __('public.community_subtitle') }}
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 mb-16">
            <div class="rounded-3xl bg-white p-8 shadow-xl border border-gray-100 hover:shadow-2xl hover:border-blue-100 transition-all duration-300 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 text-white flex items-center justify-center mx-auto mb-5 shadow-lg">
                    <i class="fas fa-trophy text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">{{ __('public.community_features_competitions') }}</h3>
                <p class="text-gray-600 text-sm">مسابقات برمجة وذكاء اصطناعي مع جوائز ولوحة متصدرين</p>
            </div>
            <div class="rounded-3xl bg-white p-8 shadow-xl border border-gray-100 hover:shadow-2xl hover:border-blue-100 transition-all duration-300 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center mx-auto mb-5 shadow-lg">
                    <i class="fas fa-database text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">{{ __('public.community_features_datasets') }}</h3>
                <p class="text-gray-600 text-sm">مجموعات بيانات مفتوحة للتدريب والتجربة</p>
            </div>
            <div class="rounded-3xl bg-white p-8 shadow-xl border border-gray-100 hover:shadow-2xl hover:border-amber-100 transition-all duration-300 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 text-white flex items-center justify-center mx-auto mb-5 shadow-lg">
                    <i class="fas fa-brain text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">مكتبة النماذج (Model Zoo)</h3>
                <p class="text-gray-600 text-sm">نماذج مدربة جاهزة مع منهجية وأداء وطريقة استخدام</p>
            </div>
            <div class="rounded-3xl bg-white p-8 shadow-xl border border-gray-100 hover:shadow-2xl hover:border-blue-100 transition-all duration-300 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 text-white flex items-center justify-center mx-auto mb-5 shadow-lg">
                    <i class="fas fa-comments text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">{{ __('public.community_features_discussions') }}</h3>
                <p class="text-gray-600 text-sm">مناقشات مع الخبراء والمتعلمين في المجال</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-4 mb-10">
            <a href="{{ route('community.data.index') }}" class="inline-flex items-center gap-2 bg-white border-2 border-slate-200 text-slate-800 px-6 py-3.5 rounded-2xl font-bold shadow-lg hover:border-blue-400 hover:bg-blue-50 transition-all">
                <i class="fas fa-database text-blue-600"></i>
                <span>صفحة البيانات</span>
            </a>
            <a href="{{ route('community.models.index') }}" class="inline-flex items-center gap-2 bg-white border-2 border-slate-200 text-slate-800 px-6 py-3.5 rounded-2xl font-bold shadow-lg hover:border-amber-400 hover:bg-amber-50 transition-all">
                <i class="fas fa-brain text-amber-600"></i>
                <span>صفحة النماذج</span>
            </a>
            <a href="{{ route('community.contributors.index') }}" class="inline-flex items-center gap-2 bg-white border-2 border-slate-200 text-slate-800 px-6 py-3.5 rounded-2xl font-bold shadow-lg hover:border-cyan-400 hover:bg-cyan-50 transition-all">
                <i class="fas fa-user-friends text-cyan-600"></i>
                <span>المساهمون</span>
            </a>
        </div>

        <div class="rounded-3xl bg-gradient-to-r from-blue-600 via-blue-500 to-green-600 p-8 md:p-12 text-center text-white shadow-2xl">
            <h2 class="text-2xl md:text-3xl font-black mb-4">{{ __('public.community_coming_soon') }}</h2>
            <p class="text-blue-100 text-lg mb-8 max-w-2xl mx-auto">{{ __('public.community_coming_desc') }}</p>
            @auth
                <a href="{{ route('community.dashboard') }}" class="inline-flex items-center gap-2 bg-white text-blue-700 px-8 py-4 rounded-full font-bold text-lg shadow-xl hover:bg-blue-50 transition-all">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>{{ __('public.community_enter_community') }}</span>
                </a>
            @else
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ route('community.login') }}" class="inline-flex items-center gap-2 bg-white text-blue-700 px-8 py-4 rounded-full font-bold text-lg shadow-xl hover:bg-blue-50 transition-all">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>{{ __('public.community_enter_community') }}</span>
                    </a>
                    <a href="{{ route('community.register') }}" class="inline-flex items-center gap-2 bg-blue-400/90 text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-blue-300/90 transition-all border-2 border-white">
                        <i class="fas fa-user-plus"></i>
                        <span>{{ __('landing.nav.register') }}</span>
                    </a>
                </div>
            @endauth
        </div>
    </div>
</section>
@endsection
