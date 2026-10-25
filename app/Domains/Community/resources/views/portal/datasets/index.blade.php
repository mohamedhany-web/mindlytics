@extends('community::portal.layouts.app')

@section('title', __('admin.community_datasets'))

@section('content')
<div class="w-full">
    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2">
        مجموعات البيانات
    </h1>
    <p class="text-slate-600 mb-6">
        استكشف مجموعات بيانات مفتوحة للتدريب والتجربة في مشاريعك. استخدم البحث والتصنيف لتضييق النتائج.
    </p>

    {{-- بحث وتصفية --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm mb-8">
        <form action="{{ route('community.datasets.index') }}" method="GET" class="space-y-4" role="search">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text"
                           name="q"
                           value="{{ request('q', $currentSearch ?? '') }}"
                           placeholder="ابحث بالعنوان أو الوصف..."
                           class="w-full pl-4 pr-10 py-3 rounded-xl border border-slate-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 bg-slate-50/50">
                </div>
                <input type="hidden" name="category" value="{{ request('category', $currentCategory ?? '') }}">
                <button type="submit" class="px-6 py-3 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700 transition-colors shrink-0">
                    <i class="fas fa-search ml-1"></i> بحث
                </button>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-bold text-slate-600">التصنيف:</span>
                <a href="{{ route('community.datasets.index', ['q' => request('q')]) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ empty($currentCategory) ? 'bg-cyan-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    الكل
                </a>
                @foreach($categoriesWithCount ?? [] as $cat)
                    @if($cat['count'] > 0 || ($currentCategory ?? '') === $cat['key'])
                        <a href="{{ route('community.datasets.index', ['category' => $cat['key'], 'q' => request('q')]) }}"
                           class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ ($currentCategory ?? '') === $cat['key'] ? 'bg-cyan-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $cat['label'] }}
                            <span class="opacity-80">({{ $cat['count'] }})</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </form>
    </div>

    @if($datasets->isNotEmpty())
        <p class="text-slate-500 text-sm mb-3">
            عرض {{ $datasets->firstItem() }}–{{ $datasets->lastItem() }} من {{ $datasets->total() }} مجموعة
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($datasets as $dataset)
                <a href="{{ route('community.datasets.show', $dataset) }}" class="group block bg-white rounded-xl border border-slate-200/80 p-4 shadow-sm hover:shadow hover:border-cyan-300/60 transition-all duration-200">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="w-9 h-9 rounded-lg bg-cyan-50 text-cyan-600 flex items-center justify-center shrink-0 group-hover:bg-cyan-100 transition-colors">
                            <i class="fas fa-database text-sm"></i>
                        </div>
                        @if($dataset->category)
                            <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-500">
                                {{ $dataset->category_label }}
                            </span>
                        @endif
                    </div>
                    <h3 class="text-base font-bold text-slate-800 mb-1.5 line-clamp-2 group-hover:text-cyan-700 transition-colors">{{ $dataset->title }}</h3>
                    @if($dataset->description)
                        <p class="text-slate-500 text-xs leading-relaxed mb-3 line-clamp-2">{{ Str::limit($dataset->description, 80) }}</p>
                    @else
                        <div class="mb-3"></div>
                    @endif
                    <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100">
                        @if($dataset->file_size)
                            <span class="text-xs text-slate-400">{{ $dataset->file_size }}</span>
                        @else
                            <span></span>
                        @endif
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-cyan-600 group-hover:underline">
                            <i class="fas fa-arrow-left text-[10px]"></i>
                            <span>عرض</span>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
        @if($datasets->hasPages())
            <div class="mt-8">{{ $datasets->withQueryString()->links() }}</div>
        @endif
    @else
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm text-center">
            <div class="w-20 h-20 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-search text-4xl"></i>
            </div>
            <h2 class="text-xl font-black text-slate-900 mb-3">لا توجد نتائج</h2>
            <p class="text-slate-600 max-w-xl mx-auto mb-6">
                @if(request('q') || request('category'))
                    جرّب تغيير كلمات البحث أو التصنيف.
                @else
                    لم تُضف مجموعات بيانات بعد. عد لاحقاً أو شارك بمجموعة من لوحة المساهم.
                @endif
            </p>
            <a href="{{ route('community.datasets.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700">
                <i class="fas fa-list"></i> عرض الكل
            </a>
        </div>
    @endif
</div>
@endsection
