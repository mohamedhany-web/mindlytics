@extends('layouts.public')

@section('title', 'مجموعات البيانات - مجتمع الذكاء الاصطناعي')

@section('content')
<section class="min-h-screen bg-gradient-to-b from-slate-50 to-white w-full" style="padding-top: 6rem;">
    <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-8 md:py-12">
        {{-- الهيدر: عنوان + وصف + زر --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8">
            <div class="flex-1">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-100 text-blue-800 text-sm font-bold mb-4">
                    <i class="fas fa-database"></i>
                    <span>مجتمع الذكاء الاصطناعي</span>
                </div>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-black text-slate-900 mb-3" style="font-family: 'Tajawal', 'Cairo', sans-serif;">
                    مجموعات البيانات
                </h1>
                <p class="text-slate-600 text-lg max-w-2xl">
                    استكشف، حلّل وشارك بيانات ذات جودة. تعرّف على أنواع البيانات وكيفية إنشائها والتعاون عليها — بدون تسجيل دخول.
                </p>
            </div>
            @auth
            <a href="{{ route('community.datasets.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-cyan-600 text-white font-bold shadow-lg hover:bg-cyan-700 transition-all shrink-0">
                <i class="fas fa-th-list"></i>
                <span>لوحة المجموعات</span>
            </a>
            @else
            <a href="{{ route('community.login') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-slate-100 text-slate-700 font-bold border-2 border-slate-200 hover:border-cyan-300 hover:bg-cyan-50 transition-all shrink-0">
                <i class="fas fa-sign-in-alt"></i>
                <span>تسجيل الدخول للمشاركة</span>
            </a>
            @endauth
        </div>

        {{-- بحث وفلاتر --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6 mb-8">
            <form action="{{ route('community.data.index') }}" method="GET" role="search" class="space-y-4">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        <input type="text"
                               name="q"
                               value="{{ request('q', $currentSearch ?? '') }}"
                               placeholder="ابحث في مجموعات البيانات..."
                               class="w-full pl-4 pr-12 py-3.5 rounded-xl border border-slate-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 bg-slate-50/50 text-slate-900 placeholder-slate-400">
                    </div>
                    <input type="hidden" name="category" value="{{ request('category', $currentCategory ?? '') }}">
                    <button type="submit" class="px-6 py-3.5 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700 transition-colors shrink-0">
                        <i class="fas fa-search ml-1"></i> بحث
                    </button>
                </div>
                <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100">
                    <span class="text-sm font-bold text-slate-600 me-1">التصنيف:</span>
                    <a href="{{ route('community.data.index', ['q' => request('q')]) }}"
                       class="px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ empty($currentCategory) ? 'bg-cyan-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        الكل
                    </a>
                    @foreach($categoriesWithCount ?? [] as $cat)
                        <a href="{{ route('community.data.index', ['category' => $cat['key'], 'q' => request('q')]) }}"
                           class="px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ ($currentCategory ?? '') === $cat['key'] ? 'bg-cyan-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $cat['label'] }}
                            <span class="opacity-90">({{ $cat['count'] }})</span>
                        </a>
                    @endforeach
                </div>
            </form>
        </div>

        {{-- عنوان القسم + عدد النتائج --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-chart-line text-cyan-600"></i>
                مجموعات البيانات
            </h2>
            @if($datasets->isNotEmpty())
                <p class="text-slate-500 text-sm">
                    عرض {{ $datasets->firstItem() }}–{{ $datasets->lastItem() }} من {{ number_format($datasets->total()) }} مجموعة
                </p>
            @endif
        </div>

        @if($datasets->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3 sm:gap-4">
                @foreach($datasets as $dataset)
                    <a href="{{ route('community.data.show', $dataset) }}" class="group block bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-lg hover:border-cyan-200 overflow-hidden transition-all duration-300">
                        <div class="aspect-[3/1] bg-gradient-to-br from-cyan-50 to-blue-50 flex items-center justify-center border-b border-slate-100">
                            <div class="w-10 h-10 rounded-xl bg-white/80 shadow flex items-center justify-center text-cyan-600 group-hover:scale-105 transition-transform">
                                <i class="fas fa-database text-lg"></i>
                            </div>
                        </div>
                        <div class="p-3">
                            <h3 class="text-sm font-bold text-slate-900 mb-1 line-clamp-2 group-hover:text-cyan-700 transition-colors">{{ $dataset->title }}</h3>
                            @if($dataset->creator)
                                <p class="text-xs text-slate-500 mb-1.5 truncate">{{ $dataset->creator->name }}</p>
                            @endif
                            <div class="flex flex-wrap items-center gap-1.5 mb-2">
                                @if($dataset->category)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600">{{ $dataset->category_label }}</span>
                                @endif
                                @if($dataset->file_size)
                                    <span class="text-[10px] text-slate-400">{{ $dataset->file_size }}</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between pt-1.5 border-t border-slate-100 text-[10px] text-slate-500">
                                @if($dataset->downloads_count > 0)
                                    <span><i class="fas fa-download ml-0.5"></i> {{ number_format($dataset->downloads_count) }} تحميل</span>
                                @else
                                    <span></span>
                                @endif
                                <span class="inline-flex items-center gap-0.5 font-bold text-cyan-600 group-hover:underline">
                                    <span>عرض</span>
                                    <i class="fas fa-arrow-left text-[8px]"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($datasets->hasPages())
                <div class="mt-10 flex justify-center">
                    {{ $datasets->withQueryString()->links() }}
                </div>
            @endif
        @else
            <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center shadow-sm">
                <div class="w-24 h-24 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-database text-5xl"></i>
                </div>
                <h2 class="text-xl font-black text-slate-900 mb-3">لا توجد مجموعات بيانات حالياً</h2>
                <p class="text-slate-600 max-w-xl mx-auto mb-6">
                    @if(request('q') || request('category'))
                        جرّب تغيير كلمات البحث أو التصنيف.
                    @else
                        سيتم إضافة مجموعات البيانات قريباً. عد لاحقاً أو سجّل دخولك للمجتمع للمشاركة.
                    @endif
                </p>
                <a href="{{ route('community.data.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700">
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
