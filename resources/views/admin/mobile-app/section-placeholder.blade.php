@extends('layouts.admin')

@section('title', $pageTitle . ' — Mindlytics Community')
@section('page_title', $pageTitle)

@section('content')
@php
    $icon = $pageIcon ?? 'mobile-alt';
@endphp
<div class="w-full min-h-screen p-3 sm:p-4 md:p-6 lg:p-8 space-y-4 sm:space-y-6" style="background: #f8fafc;">

    {{-- بطاقة رئيسية بعرض كامل — نفس أسلوب لوحة التحكم --}}
    <div class="rounded-2xl p-6 sm:p-8 lg:p-10 relative overflow-hidden border-2 border-violet-200/60 shadow-xl hover:shadow-2xl transition-all duration-300 w-full group"
         style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(245,243,255,0.96) 40%, rgba(237,233,254,0.93) 100%);">
        <div class="absolute inset-0 bg-gradient-to-br from-violet-100/45 via-indigo-50/25 to-fuchsia-50/15 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
        <div class="absolute -top-10 -right-10 w-56 h-56 bg-gradient-to-br from-violet-400/20 to-transparent rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>
        <div class="relative z-10 flex flex-col xl:flex-row xl:items-start gap-8">
            <div class="flex items-start gap-5 min-w-0 flex-1">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl flex items-center justify-center shadow-xl shrink-0"
                     style="background: linear-gradient(135deg, #7c3aed 0%, #6366f1 55%, #4f46e5 100%); box-shadow: 0 12px 32px rgba(99, 102, 241, 0.38);">
                    <i class="fas fa-{{ $icon }} text-white text-2xl sm:text-3xl"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-violet-700/90 mb-1 tracking-wide">Mindlytics Community · لوحة التحكم</p>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight bg-gradient-to-r from-violet-900 via-indigo-800 to-violet-800 bg-clip-text text-transparent">
                        {{ $pageTitle }}
                    </h1>
                    <p class="mt-4 text-slate-600 text-sm sm:text-base lg:text-lg leading-relaxed max-w-5xl">
                        {{ $pageDescription }}
                    </p>
                    <div class="mt-8 inline-flex flex-wrap items-center gap-3 rounded-2xl border-2 border-amber-300/60 bg-gradient-to-r from-amber-50 to-orange-50/90 px-5 py-4 text-sm font-bold text-amber-950 shadow-md">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg shrink-0"><i class="fas fa-hammer"></i></span>
                        <span>هذه الوحدة مخصّصة في القائمة؛ سيتم ربط الإعدادات والمنطق مع التطبيق والـ API في المراحل القادمة.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- شبكة مساعدة بعرض الصفحة --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 w-full">
        <a href="{{ route('admin.mobile-app.edit') }}" class="rounded-2xl p-5 sm:p-6 border-2 border-blue-200/50 shadow-lg hover:shadow-xl hover:border-blue-300/70 transition-all duration-300 block group"
           style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(239,246,255,0.96) 100%);">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-black text-blue-900">محتوى الصفحة الرئيسية</span>
                <i class="fas fa-arrow-left text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
            </div>
            <p class="text-xs sm:text-sm text-blue-800/75 leading-relaxed">تعديل الترحيب والمهمة ورسائل عدم الاشتراك ومسار الكتالوج.</p>
        </a>
        <div class="rounded-2xl p-5 sm:p-6 border-2 border-emerald-200/50 shadow-lg"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(236,253,245,0.96) 100%);">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-black text-emerald-900">التطبيق على الجهاز</span>
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md"><i class="fas fa-mobile-screen-button text-white text-sm"></i></div>
            </div>
            <p class="text-xs sm:text-sm text-emerald-900/75 leading-relaxed">يجب أن يصل الطلاب إلى نفس الخادم (<span class="font-mono text-xs">WEB_BASE_URL</span>) لعرض التحديثات.</p>
        </div>
        <div class="rounded-2xl p-5 sm:p-6 border-2 border-indigo-200/50 shadow-lg"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(238,242,255,0.96) 100%);">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-black text-indigo-900">لوحة متّسقة</span>
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-md"><i class="fas fa-layer-group text-white text-sm"></i></div>
            </div>
            <p class="text-xs sm:text-sm text-indigo-900/75 leading-relaxed">التصميم يطابق خلفية لوحة التحكم الرئيسية وعرض الشبكة الكامل.</p>
        </div>
    </div>

    {{-- روابط سريعة للأقسام الأخرى --}}
    <div class="rounded-2xl border-2 border-slate-200/80 bg-white p-5 sm:p-6 shadow-lg w-full">
        <h2 class="text-base font-black text-slate-900 mb-4 flex items-center gap-2">
            <i class="fas fa-compass text-violet-600"></i>
            أقسام تطبيق الطلاب
        </h2>
        <div class="flex flex-wrap gap-2 sm:gap-3">
            <a href="{{ route('admin.mobile-app.notifications') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-violet-50 hover:border-violet-200 hover:text-violet-900 transition-colors {{ request()->routeIs('admin.mobile-app.notifications') ? 'bg-violet-100 border-violet-300 text-violet-900' : '' }}"><i class="fas fa-bell text-violet-600"></i> إشعارات</a>
            <a href="{{ route('admin.mobile-app.maintenance') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-violet-50 hover:border-violet-200 hover:text-violet-900 transition-colors {{ request()->routeIs('admin.mobile-app.maintenance') ? 'bg-violet-100 border-violet-300 text-violet-900' : '' }}"><i class="fas fa-tools text-slate-600"></i> الصيانة</a>
            <a href="{{ route('admin.mobile-app.links') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-violet-50 hover:border-violet-200 hover:text-violet-900 transition-colors {{ request()->routeIs('admin.mobile-app.links') ? 'bg-violet-100 border-violet-300 text-violet-900' : '' }}"><i class="fas fa-link text-indigo-600"></i> الروابط</a>
            <a href="{{ route('admin.mobile-app.appearance') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-violet-50 hover:border-violet-200 hover:text-violet-900 transition-colors {{ request()->routeIs('admin.mobile-app.appearance') ? 'bg-violet-100 border-violet-300 text-violet-900' : '' }}"><i class="fas fa-palette text-pink-600"></i> المظهر</a>
            <a href="{{ route('admin.mobile-app.edit') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-violet-50 hover:border-violet-200 hover:text-violet-900 transition-colors {{ request()->routeIs('admin.mobile-app.edit') ? 'bg-violet-100 border-violet-300 text-violet-900' : '' }}"><i class="fas fa-home text-blue-600"></i> الصفحة الرئيسية</a>
        </div>
    </div>
</div>
@endsection
