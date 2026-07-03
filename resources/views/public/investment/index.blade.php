@extends('layouts.public')

@section('title', 'الاستثمار في Mindlytics')

@section('content')
<section class="hero-gradient min-h-[45vh] flex items-center relative overflow-hidden pt-28" style="background: linear-gradient(135deg, #78350f 0%, #b45309 40%, #f59e0b 100%);">
    <div class="container mx-auto px-4 text-center relative z-10">
        <h1 class="text-4xl md:text-5xl font-black text-white mb-4">الاستثمار في Mindlytics</h1>
        <p class="text-lg md:text-xl text-amber-100 max-w-3xl mx-auto">فرص استثمارية في أكاديمية البرمجة والتعليم التقني — بشفافية وإطار قانوني واضح</p>
    </div>
</section>

@if(session('success'))
    <div class="container mx-auto px-4 -mt-6 relative z-20">
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
    </div>
@endif

<section class="py-14 bg-slate-50">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-10">
            <h2 class="text-2xl font-black text-slate-900 mb-4">نظرة عامة</h2>
            <p class="text-slate-700 whitespace-pre-wrap leading-relaxed">{{ $policy->overview }}</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            <div class="bg-white rounded-2xl shadow p-6">
                <h3 class="font-bold text-amber-800 mb-2"><i class="fas fa-list-check ml-1"></i> قواعد الأهلية</h3>
                <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $policy->eligibility_rules }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow p-6">
                <h3 class="font-bold text-amber-800 mb-2"><i class="fas fa-route ml-1"></i> كيفية الاستثمار</h3>
                <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $policy->process_description }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow p-6 md:col-span-2 lg:col-span-1">
                <h3 class="font-bold text-amber-800 mb-2"><i class="fas fa-gavel ml-1"></i> الإطار القانوني</h3>
                <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $policy->legal_framework }}</p>
            </div>
        </div>
    </div>
</section>

<section class="py-14 bg-white" id="plans">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-black text-center text-slate-900 mb-10">الخطط الاستثمارية المتاحة</h2>
        @if($plans->isEmpty())
            <p class="text-center text-slate-500">لا توجد خطط متاحة حالياً — تواصل معنا للاستفسار.</p>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($plans as $plan)
                    <article class="rounded-2xl border border-amber-100 bg-gradient-to-b from-white to-amber-50/30 p-6 shadow-sm hover:shadow-lg transition-shadow flex flex-col">
                        @if($plan->is_featured)
                            <span class="self-start text-xs font-bold bg-amber-500 text-white px-2 py-0.5 rounded-full mb-2">مميز</span>
                        @endif
                        <h3 class="text-xl font-black text-slate-900">{{ $plan->title }}</h3>
                        <p class="text-xs text-slate-500 mt-1">{{ $plan->planTypeLabel() }} · مخاطر {{ $plan->riskLevelLabel() }}</p>
                        <p class="text-sm text-slate-600 mt-3 flex-1">{{ $plan->short_description }}</p>
                        <ul class="text-sm mt-4 space-y-1 text-slate-700">
                            <li><strong>الحد الأدنى:</strong> {{ $plan->formattedMinInvestment() }}</li>
                            @if($plan->duration_months)<li><strong>المدة:</strong> {{ $plan->duration_months }} شهر</li>@endif
                            @if($plan->expected_return_min)<li><strong>عائد متوقع:</strong> {{ $plan->expected_return_min }}% — {{ $plan->expected_return_max }}%</li>@endif
                        </ul>
                        <a href="{{ route('investment.show', $plan->slug) }}" class="mt-5 inline-flex justify-center items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2.5 px-4 rounded-xl transition-colors">
                            التفاصيل والتقديم <i class="fas fa-arrow-left"></i>
                        </a>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section class="py-12 bg-amber-50 border-t border-amber-100">
    <div class="container mx-auto px-4 text-center text-sm text-amber-950 space-y-4">
        <p class="whitespace-pre-wrap max-w-4xl mx-auto">{{ $policy->disclaimer }}</p>
        @if($policy->contact_email || $policy->contact_phone)
            <p class="font-semibold text-base">
                للاستفسارات:
                @if($policy->contact_email)
                    <a href="mailto:{{ $policy->contact_email }}" class="text-amber-800 hover:underline dir-ltr inline-block">{{ $policy->contact_email }}</a>
                @endif
                @if($policy->contact_phone)
                    · <a href="tel:{{ $policy->contact_phone }}" class="text-amber-800 hover:underline dir-ltr inline-block">{{ $policy->contact_phone }}</a>
                @endif
            </p>
        @endif
    </div>
</section>
@endsection
