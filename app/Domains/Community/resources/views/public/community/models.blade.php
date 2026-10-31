@extends('layouts.public')

@section('title', 'مكتبة النماذج (Model Zoo) - مجتمع الذكاء الاصطناعي')

@section('content')
<section class="min-h-screen bg-gradient-to-b from-slate-50 to-white w-full" style="padding-top: 6rem;">
    <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-8 md:py-12">
        {{-- الهيدر --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8">
            <div class="flex-1">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-100 text-amber-800 text-sm font-bold mb-4">
                    <i class="fas fa-brain"></i>
                    <span>مجتمع الذكاء الاصطناعي</span>
                </div>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-black text-slate-900 mb-3" style="font-family: 'Tajawal', 'Cairo', sans-serif;">
                    مكتبة النماذج (Model Zoo)
                </h1>
                <p class="text-slate-600 text-lg max-w-2xl">
                    نماذج مدربة جاهزة للاستخدام أو إعادة التدريب، مرتبطة بمجموعات البيانات مع شرح المنهجية وأداء كل نموذج — بدون تسجيل دخول.
                </p>
            </div>
            @auth
            @if(auth()->user()->is_community_contributor ?? false)
            <a href="{{ route('community.contributor.models.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-amber-600 text-white font-bold shadow-lg hover:bg-amber-700 transition-all shrink-0">
                <i class="fas fa-th-list"></i>
                <span>لوحة نماذجي</span>
            </a>
            @else
            <a href="{{ route('community.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-amber-600 text-white font-bold shadow-lg hover:bg-amber-700 transition-all shrink-0">
                <i class="fas fa-tachometer-alt"></i>
                <span>لوحة المجتمع</span>
            </a>
            @endif
            @else
            <a href="{{ route('community.login') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-slate-100 text-slate-700 font-bold border-2 border-slate-200 hover:border-amber-300 hover:bg-amber-50 transition-all shrink-0">
                <i class="fas fa-sign-in-alt"></i>
                <span>تسجيل الدخول للمشاركة</span>
            </a>
            @endauth
        </div>

        {{-- بحث --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6 mb-8">
            <form action="{{ route('community.models.index') }}" method="GET" role="search" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                    <input type="text"
                           name="q"
                           value="{{ request('q', $currentSearch ?? '') }}"
                           placeholder="ابحث في النماذج (العنوان، الوصف، المنهجية)..."
                           class="w-full pl-4 pr-12 py-3.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 bg-slate-50/50 text-slate-900 placeholder-slate-400">
                </div>
                <button type="submit" class="px-6 py-3.5 rounded-xl bg-amber-600 text-white font-bold hover:bg-amber-700 transition-colors shrink-0">
                    <i class="fas fa-search ml-1"></i> بحث
                </button>
            </form>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-brain text-amber-600"></i>
                النماذج المعتمدة
            </h2>
            @if($models->isNotEmpty())
                <p class="text-slate-500 text-sm">
                    عرض {{ $models->firstItem() }}–{{ $models->lastItem() }} من {{ number_format($models->total()) }} نموذج
                </p>
            @endif
        </div>

        @if($models->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3 sm:gap-4">
                @foreach($models as $model)
                    <a href="{{ route('community.models.show', $model) }}" class="group block bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-lg hover:border-amber-200 overflow-hidden transition-all duration-300">
                        <div class="aspect-[3/1] bg-gradient-to-br from-amber-50 to-orange-50 flex items-center justify-center border-b border-slate-100">
                            <div class="w-10 h-10 rounded-xl bg-white/80 shadow flex items-center justify-center text-amber-600 group-hover:scale-105 transition-transform">
                                <i class="fas fa-brain text-lg"></i>
                            </div>
                        </div>
                        <div class="p-3">
                            <h3 class="text-sm font-bold text-slate-900 mb-1 line-clamp-2 group-hover:text-amber-700 transition-colors">{{ $model->title }}</h3>
                            @if($model->creator)
                                <p class="text-xs text-slate-500 mb-1.5 truncate">{{ $model->creator->name }}</p>
                            @endif
                            <div class="flex flex-wrap items-center gap-1.5 mb-2">
                                @if($model->license)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600">{{ Str::limit($model->license, 12) }}</span>
                                @endif
                                @if($model->file_size)
                                    <span class="text-[10px] text-slate-400">{{ $model->file_size }}</span>
                                @endif
                            </div>
                            @if($model->dataset)
                                <p class="text-[10px] text-cyan-600 mb-1.5 truncate" title="{{ $model->dataset->title }}"><i class="fas fa-database ml-0.5"></i> {{ Str::limit($model->dataset->title, 22) }}</p>
                            @endif
                            <div class="flex items-center justify-between pt-1.5 border-t border-slate-100 text-[10px] text-slate-500">
                                @if($model->downloads_count > 0)
                                    <span><i class="fas fa-download ml-0.5"></i> {{ number_format($model->downloads_count) }} تحميل</span>
                                @else
                                    <span></span>
                                @endif
                                <span class="inline-flex items-center gap-0.5 font-bold text-amber-600 group-hover:underline">
                                    <span>عرض</span>
                                    <i class="fas fa-arrow-left text-[8px]"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($models->hasPages())
                <div class="mt-10 flex justify-center">
                    {{ $models->withQueryString()->links() }}
                </div>
            @endif
        @else
            <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center shadow-sm">
                <div class="w-24 h-24 rounded-2xl bg-amber-50 text-amber-400 flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-brain text-5xl"></i>
                </div>
                <h2 class="text-xl font-black text-slate-900 mb-3">لا توجد نماذج حالياً</h2>
                <p class="text-slate-600 max-w-xl mx-auto mb-6">
                    @if(request('q'))
                        جرّب تغيير كلمات البحث.
                    @else
                        سيتم إضافة النماذج قريباً. عد لاحقاً أو سجّل دخولك كمساهم في الذكاء الاصطناعي للمشاركة.
                    @endif
                </p>
                <a href="{{ route('community.models.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-amber-600 text-white font-bold hover:bg-amber-700">
                    <i class="fas fa-list"></i> عرض الكل
                </a>
            </div>
        @endif

        <div class="mt-12 text-center">
            <a href="{{ route('public.community.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 font-semibold">
                <i class="fas fa-arrow-right"></i>
                العودة لصفحة المجتمع
            </a>
        </div>
    </div>
</section>
@endsection
