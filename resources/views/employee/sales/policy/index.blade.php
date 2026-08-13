@extends('layouts.employee')

@section('title', 'قواعد وسياسات المبيعات')
@section('header', 'قواعد وسياسات المبيعات')

@push('styles')
@include('employee.sales._styles')
<style>
    .policy-doc { max-width: 920px; margin: 0 auto; }
    .policy-toc a { scroll-margin-top: 5rem; }
    .policy-rule { scroll-margin-top: 5rem; }
    .policy-content { white-space: pre-line; line-height: 1.75; }
    .policy-badge { font-variant-numeric: tabular-nums; }
</style>
@endpush

@section('content')
<div class="policy-doc space-y-6">
    <div class="dashboard-card border-emerald-200 bg-gradient-to-l from-emerald-50 to-white">
        <p class="text-xs font-bold tracking-widest text-emerald-700 uppercase">Mindlytics Academy</p>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ $settings->document_title }}</h1>
        <p class="text-slate-600 mt-1">{{ $settings->document_title_en }}</p>
        <div class="flex flex-wrap gap-3 mt-4 text-sm text-slate-600">
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white border border-slate-200"><strong>Version</strong> {{ $settings->version }}</span>
            @if($settings->effective_date)
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white border border-slate-200"><strong>Effective</strong> {{ $settings->effective_date->format('Y-m-d') }}</span>
            @endif
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white border border-slate-200"><strong>Applies To</strong> Sales Team</span>
        </div>
    </div>

    @if(filled($settings->intro_content))
        <div class="panel-card p-5 sm:p-6">
            <h2 class="font-black text-lg text-slate-900 mb-3"><i class="fas fa-info-circle text-emerald-600 ml-2"></i> مقدمة وأهداف الدليل</h2>
            <div class="policy-content text-slate-700">{{ $settings->intro_content }}</div>
        </div>
    @endif

    <div class="panel-card p-5 sm:p-6 policy-toc">
        <h2 class="font-black text-lg text-slate-900 mb-3">Contents — المحتويات</h2>
        <ol class="space-y-2 text-sm">
            @foreach($sections as $section)
                <li>
                    <a href="#section-{{ $section->id }}" class="text-emerald-700 hover:underline font-semibold">
                        {{ $section->title }}
                        @if($section->rules_range)
                            <span class="text-slate-500 font-normal">(Rules {{ $section->rules_range }})</span>
                        @endif
                    </a>
                </li>
            @endforeach
            @if(filled($settings->acknowledgement_content))
                <li><a href="#acknowledgement" class="text-emerald-700 hover:underline font-semibold">إقرار الاستلام والالتزام</a></li>
            @endif
        </ol>
    </div>

    @foreach($sections as $section)
        <div id="section-{{ $section->id }}" class="panel-card overflow-hidden">
            <div class="panel-card-head px-5 py-4">
                <h2 class="font-black text-lg text-slate-900">{{ $section->title }}</h2>
                @if($section->title_en)
                    <p class="text-sm text-slate-500 mt-0.5">{{ $section->title_en }}</p>
                @endif
                @if($section->rules_range)
                    <p class="text-xs text-emerald-700 font-semibold mt-1">Rules {{ $section->rules_range }}</p>
                @endif
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($section->activeRules as $rule)
                    <article id="rule-{{ $rule->id }}" class="policy-rule p-5 sm:p-6">
                        <div class="flex items-start gap-3">
                            <span class="policy-badge shrink-0 inline-flex items-center justify-center min-w-[3rem] h-9 px-2 rounded-xl bg-emerald-600 text-white text-sm font-black">
                                {{ $rule->displayNumber() }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-bold text-slate-900 text-lg">{{ $rule->title }}</h3>
                                @if($rule->title_en)
                                    <p class="text-sm text-slate-500">{{ $rule->title_en }}</p>
                                @endif
                                @if(filled($rule->content))
                                    <div class="policy-content text-slate-700 mt-3 text-sm sm:text-base">{{ $rule->content }}</div>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @endforeach

    @if(filled($settings->acknowledgement_content))
        <div id="acknowledgement" class="panel-card p-5 sm:p-6 border-amber-200 bg-amber-50/40">
            <h2 class="font-black text-lg text-slate-900 mb-3"><i class="fas fa-file-signature text-amber-600 ml-2"></i> إقرار الاستلام والالتزام</h2>
            <div class="policy-content text-slate-700">{{ $settings->acknowledgement_content }}</div>
        </div>
    @endif
</div>
@endsection
