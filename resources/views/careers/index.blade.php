@extends('layouts.public')

@section('title', 'التوظيف — Mindlytics')

@push('styles')
@include('careers._styles')
@endpush

@section('content')
@include('careers._hero', [
    'badge' => 'انضم إلى فريق Mindlytics',
    'title' => 'الوظائف المتاحة',
    'subtitle' => 'قدّم طلبك وارفع سيرتك الذاتية — فريق الموارد البشرية يراجع الطلبات ويتواصل مع المرشحين المناسبين',
])

{{-- إحصائيات سريعة --}}
<section class="py-8 bg-white border-b border-slate-100">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-5xl">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="stat-card p-5 text-center">
                <div class="text-3xl font-black text-blue-600 mb-1 tabular-nums">{{ $jobs->count() }}</div>
                <div class="text-sm font-semibold text-slate-600">وظيفة مفتوحة</div>
            </div>
            <div class="stat-card p-5 text-center">
                <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i class="fas fa-users"></i>
                </div>
                <div class="text-sm font-semibold text-slate-600">فريق متنامٍ في التعليم والتقنية</div>
            </div>
            <div class="stat-card p-5 text-center">
                <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                    <i class="fas fa-rocket"></i>
                </div>
                <div class="text-sm font-semibold text-slate-600">بيئة عمل محفّزة ومرنة</div>
            </div>
        </div>
    </div>
</section>

<section class="py-14 md:py-20 bg-gradient-to-b from-white via-blue-50/30 to-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
        @if(session('success'))
            <div class="mb-8 rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-5 py-4 text-sm font-semibold flex items-center gap-3 shadow-sm">
                <i class="fas fa-check-circle text-xl text-emerald-600"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="text-center mb-10 md:mb-12">
            <span class="careers-badge inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold mb-4">
                <i class="fas fa-star text-blue-600"></i>
                فرص العمل
            </span>
            <h2 class="section-title text-2xl md:text-3xl font-extrabold text-blue-900">الوظائف الحالية</h2>
            <p class="text-slate-600 mt-4 max-w-2xl mx-auto">اختر الوظيفة المناسبة لمهاراتك وقدّم طلبك مباشرة عبر المنصة</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-2 gap-6 lg:gap-8">
            @forelse($jobs as $job)
                <a href="{{ route('careers.show', $job) }}" class="job-card p-6 group">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="min-w-0 flex-1">
                            <h3 class="text-xl font-extrabold text-slate-900 group-hover:text-blue-700 transition-colors leading-snug">
                                {{ $job->title }}
                            </h3>
                            @if($job->employment_type)
                                <p class="text-xs font-bold text-sky-600 mt-1">{{ $job->employment_type }}</p>
                            @endif
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-sky-500 text-white flex items-center justify-center shadow-lg shadow-blue-500/25 flex-shrink-0">
                            <i class="fas fa-briefcase"></i>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-4">
                        @if($job->department)
                            <span class="job-meta-chip blue"><i class="fas fa-building text-[10px]"></i>{{ $job->department }}</span>
                        @endif
                        @if($job->location)
                            <span class="job-meta-chip green"><i class="fas fa-map-marker-alt text-[10px]"></i>{{ $job->location }}</span>
                        @endif
                    </div>

                    @if($job->description)
                        <p class="text-sm text-slate-600 leading-relaxed line-clamp-3 flex-1">
                            {{ \Illuminate\Support\Str::limit(strip_tags((string) $job->description), 180) }}
                        </p>
                    @endif

                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-sm font-bold text-blue-600 group-hover:text-blue-800 transition-colors">
                            عرض التفاصيل والتقديم
                        </span>
                        <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
                            <i class="fas fa-arrow-left text-xs"></i>
                        </span>
                    </div>
                </a>
            @empty
                <div class="sm:col-span-2 content-panel p-10 md:p-14 text-center">
                    <div class="w-20 h-20 mx-auto mb-5 rounded-2xl bg-gradient-to-br from-blue-100 to-sky-100 text-blue-600 flex items-center justify-center text-3xl">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h3 class="text-xl font-extrabold text-slate-900 mb-2">لا توجد وظائف منشورة حالياً</h3>
                    <p class="text-slate-600 max-w-md mx-auto mb-6">تابعنا لاحقاً — أو تواصل معنا لإرسال سيرتك الذاتية للفرص المستقبلية</p>
                    <a href="{{ route('public.contact') }}" class="btn-primary !text-base !py-3 !px-8">
                        <i class="fas fa-envelope"></i>
                        تواصل معنا
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
