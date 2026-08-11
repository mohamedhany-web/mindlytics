@extends('layouts.public')

@section('title', 'التدريب - Internships | Mindlytics')

@section('content')
<section class="py-8 md:py-12 bg-slate-50" style="padding-top: 6rem;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <p class="text-sm font-semibold text-blue-700 mb-2">Mindlytics Training</p>
            <h1 class="text-3xl md:text-4xl font-black text-gray-900 mb-3">فرص التدريب</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">قدّم على Internships معتمدة من Mindlytics وابنِ خبرة عملية حقيقية تحت إشراف متخصصين.</p>
        </div>

        <div class="grid grid-cols-2 gap-3 max-w-md mx-auto mb-8">
            <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
                <div class="text-2xl font-black text-gray-900">{{ number_format($stats['open']) }}</div>
                <div class="text-xs text-gray-500">فرص مفتوحة</div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
                <div class="text-2xl font-black text-gray-900">{{ number_format($stats['featured']) }}</div>
                <div class="text-xs text-gray-500">Featured</div>
            </div>
        </div>

        <form method="GET" class="mb-8 flex flex-col sm:flex-row gap-3">
            <input type="search" name="q" value="{{ $q }}" placeholder="ابحث عن فرصة..." class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm">
            <select name="type" class="rounded-xl border border-gray-200 px-3 py-2.5 text-sm">
                <option value="">كل الأنواع</option>
                @foreach(\App\Models\Internship::types() as $key => $label)
                    <option value="{{ $key }}" @selected($type === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="rounded-xl bg-blue-600 text-white px-5 py-2.5 text-sm font-bold">بحث</button>
        </form>

        @if($internships->count())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($internships as $item)
                    <a href="{{ route('public.internships.show', $item->slug) }}" class="bg-white border border-gray-200 rounded-2xl p-5 hover:border-blue-300 transition-colors">
                        <div class="flex flex-wrap gap-2 mb-3">
                            @if($item->is_featured)
                                <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md">Featured</span>
                            @endif
                            <span class="text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">{{ $item->typeLabel() }}</span>
                            @if($item->department)
                                <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md">{{ $item->department }}</span>
                            @endif
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 mb-2">{{ $item->title }}</h2>
                        <p class="text-sm text-gray-500 line-clamp-2 mb-4">{{ $item->summary ?: \Illuminate\Support\Str::limit(strip_tags($item->description ?? ''), 120) }}</p>
                        <div class="flex flex-wrap gap-3 text-xs text-gray-500">
                            @if($item->location)<span><i class="fas fa-map-marker-alt ml-1"></i>{{ $item->location }}</span>@endif
                            @if($item->duration)<span><i class="fas fa-clock ml-1"></i>{{ $item->duration }}</span>@endif
                            @if($item->application_deadline)<span><i class="fas fa-calendar ml-1"></i>حتى {{ $item->application_deadline->format('Y-m-d') }}</span>@endif
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $internships->links() }}</div>
        @else
            <div class="bg-white border border-dashed border-gray-300 rounded-2xl p-12 text-center">
                <h3 class="text-lg font-bold text-gray-900 mb-2">لا توجد فرص مفتوحة حالياً</h3>
                <p class="text-gray-600">تابعنا قريباً — سيتم إضافة فرص تدريب جديدة.</p>
            </div>
        @endif
    </div>
</section>
@endsection
