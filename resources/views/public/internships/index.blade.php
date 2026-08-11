@extends('layouts.public')

@section('title', 'التدريب - Internships | Mindlytics')
@section('meta_description', 'قدّم على فرص التدريب (Internships) في Mindlytics وابنِ خبرة عملية حقيقية تحت إشراف متخصصين.')

@push('styles')
@include('careers._styles')
@endpush

@section('content')
@include('careers._hero', [
    'badge' => 'Mindlytics Training',
    'title' => 'فرص التدريب',
    'subtitle' => 'قدّم على Internships معتمدة من Mindlytics وابنِ خبرة عملية حقيقية تحت إشراف متخصصين.',
])

{{-- إحصائيات --}}
<section class="py-8 bg-white border-b border-slate-100">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-5xl">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="stat-card p-5 text-center">
                <div class="text-3xl font-black text-blue-600 mb-1 tabular-nums">{{ number_format($stats['open']) }}</div>
                <div class="text-sm font-semibold text-slate-600">فرصة مفتوحة للتقديم</div>
            </div>
            <div class="stat-card p-5 text-center">
                <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                    <i class="fas fa-star"></i>
                </div>
                <div class="text-sm font-semibold text-slate-600">
                    {{ number_format($stats['featured']) }} فرصة مميّزة
                </div>
            </div>
            <div class="stat-card p-5 text-center">
                <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="text-sm font-semibold text-slate-600">خبرة عملية + توجيه مهني</div>
            </div>
        </div>
    </div>
</section>

<section class="py-14 md:py-20 bg-gradient-to-b from-white via-blue-50/30 to-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
        <div class="text-center mb-8 md:mb-10">
            <span class="careers-badge inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold mb-4">
                <i class="fas fa-graduation-cap text-blue-600"></i>
                برامج التدريب
            </span>
            <h2 class="section-title text-2xl md:text-3xl font-extrabold text-blue-900">الفرص الحالية</h2>
            <p class="text-slate-600 mt-4 max-w-2xl mx-auto">اختر الفرصة المناسبة لمسارك وقدّم مباشرة عبر المنصة</p>
        </div>

        <form method="GET" action="{{ route('public.internships.index') }}"
              class="content-panel mb-10 p-4 sm:p-5 flex flex-col sm:flex-row gap-3">
            <input type="search" name="q" value="{{ $q }}" placeholder="ابحث عن فرصة تدريب..."
                   class="careers-input flex-1">
            <select name="type" class="careers-input sm:w-48">
                <option value="">كل الأنواع</option>
                @foreach(\App\Models\Internship::types() as $key => $label)
                    <option value="{{ $key }}" @selected($type === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="careers-btn-submit !rounded-2xl !px-6 whitespace-nowrap">
                <i class="fas fa-search"></i>
                بحث
            </button>
        </form>

        <div class="grid sm:grid-cols-2 gap-6 lg:gap-8">
            @forelse($internships as $item)
                <a href="{{ route('public.internships.show', $item->slug) }}" class="job-card p-6 group">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="min-w-0 flex-1">
                            <h3 class="text-xl font-extrabold text-slate-900 group-hover:text-blue-700 transition-colors leading-snug">
                                {{ $item->title }}
                            </h3>
                            @if($item->department)
                                <p class="text-xs font-bold text-sky-600 mt-1">{{ $item->department }}</p>
                            @endif
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-500 text-white flex items-center justify-center shadow-lg shadow-blue-500/25 flex-shrink-0">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-4">
                        @if($item->is_featured)
                            <span class="job-meta-chip violet"><i class="fas fa-star text-[10px]"></i>Featured</span>
                        @endif
                        <span class="job-meta-chip blue"><i class="fas fa-laptop-house text-[10px]"></i>{{ $item->typeLabel() }}</span>
                        @if($item->location)
                            <span class="job-meta-chip green"><i class="fas fa-map-marker-alt text-[10px]"></i>{{ $item->location }}</span>
                        @endif
                        @if($item->duration)
                            <span class="job-meta-chip blue"><i class="fas fa-clock text-[10px]"></i>{{ $item->duration }}</span>
                        @endif
                    </div>

                    <p class="text-sm text-slate-600 leading-relaxed line-clamp-3 flex-1">
                        {{ $item->summary ?: \Illuminate\Support\Str::limit(strip_tags($item->description ?? ''), 180) }}
                    </p>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                        <span class="text-xs text-slate-500 font-medium">
                            @if($item->application_deadline)
                                <i class="fas fa-calendar-alt ml-1 text-blue-500"></i>
                                حتى {{ $item->application_deadline->format('Y-m-d') }}
                            @else
                                التقديم مفتوح
                            @endif
                        </span>
                        <span class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 group-hover:text-blue-800 transition-colors">
                            التفاصيل والتقديم
                            <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
                                <i class="fas fa-arrow-left text-xs"></i>
                            </span>
                        </span>
                    </div>
                </a>
            @empty
                <div class="sm:col-span-2 content-panel p-10 md:p-14 text-center">
                    <div class="w-20 h-20 mx-auto mb-5 rounded-2xl bg-gradient-to-br from-blue-100 to-sky-100 text-blue-600 flex items-center justify-center text-3xl">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h3 class="text-xl font-extrabold text-slate-900 mb-2">لا توجد فرص مفتوحة حالياً</h3>
                    <p class="text-slate-600 max-w-md mx-auto mb-6">تابعنا قريباً — سيتم إضافة فرص تدريب جديدة، أو تواصل معنا لمعرفة المسارات المتاحة.</p>
                    <a href="{{ route('public.contact') }}" class="careers-btn-submit">
                        <i class="fas fa-envelope"></i>
                        تواصل معنا
                    </a>
                </div>
            @endforelse
        </div>

        @if($internships->hasPages())
            <div class="mt-10 flex justify-center">
                {{ $internships->withQueryString()->links() }}
            </div>
        @endif
    </div>
</section>

{{-- CTA --}}
<section class="py-16 md:py-20 bg-gradient-to-br from-blue-50 via-white to-emerald-50 relative overflow-hidden">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl text-center relative z-10">
        <span class="careers-badge inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold mb-5">
            <i class="fas fa-route text-blue-600"></i>
            رحلة التعلم
        </span>
        <h2 class="text-2xl md:text-3xl font-extrabold text-blue-900 mb-4">ابنِ ملفك المهني مع Mindlytics Journey</h2>
        <p class="text-slate-600 max-w-2xl mx-auto mb-8">اعرض مشاريعك الموثّقة وقدّم على فرص التدريب وأنت جاهز بسيرة عملية حقيقية.</p>
        <div class="flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('public.portfolio.index') }}" class="careers-btn-submit">
                <i class="fas fa-briefcase"></i>
                استكشف الرحلة
            </a>
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 rounded-full border-2 border-blue-200 bg-white px-6 py-3 text-sm font-extrabold text-blue-800 hover:bg-blue-50 transition-colors">
                إنشاء حساب
            </a>
        </div>
    </div>
</section>
@endsection
