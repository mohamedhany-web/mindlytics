@extends('layouts.employee')

@section('title', 'قواعد وسياسات المبيعات')
@section('header', 'قواعد وسياسات المبيعات')

@push('styles')
@include('employee.sales._styles')
<style>
    .sales-hub .dashboard-card,
    .sales-hub .panel-card {
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .sales-hub .dashboard-card::before { display: none; }
    .sales-hub .dashboard-card:hover {
        transform: none;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
    }
    .sales-hub .panel-card-head {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .policy-doc-shell { width: 100%; }
    .policy-toc-link {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.65rem 0.75rem;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #334155;
        transition: background 0.15s, color 0.15s;
    }
    .policy-toc-link:hover {
        background: #ecfdf5;
        color: #047857;
    }
    .policy-toc-num {
        flex-shrink: 0;
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 0.5rem;
        background: #ecfdf5;
        color: #047857;
        font-size: 0.75rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .policy-rule-block { scroll-margin-top: 6rem; }
    .policy-section-block { scroll-margin-top: 6rem; }
    .policy-rule-num {
        flex-shrink: 0;
        min-width: 2.75rem;
        height: 2.25rem;
        padding: 0 0.5rem;
        border-radius: 0.75rem;
        background: linear-gradient(135deg, #059669, #0d9488);
        color: #fff;
        font-size: 0.8125rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-variant-numeric: tabular-nums;
    }
    .policy-prose p {
        margin-bottom: 0.65rem;
        line-height: 1.8;
        color: #475569;
    }
    .policy-prose ul {
        list-style: none;
        padding: 0;
        margin: 0.5rem 0 0;
    }
    .policy-prose li {
        position: relative;
        padding-right: 1.1rem;
        margin-bottom: 0.45rem;
        line-height: 1.75;
        color: #475569;
    }
    .policy-prose li::before {
        content: '';
        position: absolute;
        right: 0;
        top: 0.65rem;
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #10b981;
    }
    .policy-meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.22);
    }
    @media (min-width: 1280px) {
        .policy-rules-grid > article:nth-child(odd) {
            border-left: 1px solid #f1f5f9;
        }
    }
</style>
@endpush

@section('content')
@php
    $totalRules = $sections->sum(fn ($s) => $s->activeRules->count());
@endphp

<div class="space-y-6 sales-hub policy-doc-shell">
    @include('employee.sales._hero', [
        'heroTitle' => 'قواعد وسياسات المبيعات',
        'heroSubtitle' => 'مرجع موحّد لقواعد الفريق — اقرأه وطبّقه في الشغل اليومي',
        'heroIcon' => 'fa-book-open',
        'backUrl' => route('employee.sales.dashboard'),
        'backLabel' => 'مركز المبيعات',
    ])

    <div class="rounded-3xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 sm:px-8 py-6 sm:py-7 bg-gradient-to-l from-emerald-700 to-teal-700 text-white">
            <p class="text-xs font-bold tracking-widest uppercase opacity-80">Mindlytics Academy</p>
            <h1 class="text-2xl sm:text-3xl font-black mt-1">{{ $settings->document_title }}</h1>
            @if(filled($settings->document_title_en))
                <p class="text-sm sm:text-base opacity-90 mt-1">{{ $settings->document_title_en }}</p>
            @endif
            <div class="flex flex-wrap gap-2 mt-4">
                <span class="policy-meta-chip"><i class="fas fa-code-branch"></i> الإصدار {{ $settings->version }}</span>
                @if($settings->effective_date)
                    <span class="policy-meta-chip"><i class="fas fa-calendar-check"></i> ساري من {{ $settings->effective_date->format('Y-m-d') }}</span>
                @endif
                <span class="policy-meta-chip"><i class="fas fa-users"></i> فريق المبيعات</span>
                <span class="policy-meta-chip"><i class="fas fa-list-ol"></i> {{ $sections->count() }} أقسام · {{ $totalRules }} قاعدة</span>
            </div>
        </div>

        @if(filled($settings->intro_content))
            <div class="px-6 sm:px-8 py-6 border-b border-slate-100 bg-emerald-50/30">
                <h2 class="font-black text-slate-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-info-circle text-emerald-600"></i>
                    مقدمة وأهداف الدليل
                </h2>
                <div class="policy-prose text-sm sm:text-base">
                    @foreach(preg_split('/\r\n|\r|\n/', trim($settings->intro_content)) as $paragraph)
                        @if(filled(trim($paragraph)))
                            <p>{{ trim($paragraph) }}</p>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="grid lg:grid-cols-12 gap-6 items-start">
        <aside class="lg:col-span-4 xl:col-span-3 lg:sticky lg:top-24">
            <div class="panel-card">
                <div class="panel-card-head px-4 py-3 font-bold text-slate-800 text-sm">
                    <i class="fas fa-list-ul ml-2 text-emerald-600"></i>
                    المحتويات
                </div>
                <nav class="p-3 space-y-1 max-h-[70vh] overflow-y-auto">
                    @foreach($sections as $index => $section)
                        <a href="#section-{{ $section->id }}" class="policy-toc-link">
                            <span class="policy-toc-num">{{ $index + 1 }}</span>
                            <span class="min-w-0">
                                <span class="block leading-snug">{{ $section->title }}</span>
                                @if($section->rules_range)
                                    <span class="block text-xs font-normal text-slate-500 mt-0.5">Rules {{ $section->rules_range }}</span>
                                @endif
                            </span>
                        </a>
                    @endforeach
                </nav>
            </div>
        </aside>

        <div class="lg:col-span-8 xl:col-span-9 space-y-5">
            @foreach($sections as $index => $section)
                <section id="section-{{ $section->id }}" class="policy-section-block panel-card overflow-hidden">
                    <div class="panel-card-head px-5 sm:px-6 py-4">
                        <div class="flex items-start gap-3">
                            <span class="policy-toc-num shrink-0">{{ $index + 1 }}</span>
                            <div class="min-w-0">
                                <h2 class="font-black text-lg text-slate-900">{{ $section->title }}</h2>
                                @if($section->title_en)
                                    <p class="text-sm text-slate-500 mt-0.5">{{ $section->title_en }}</p>
                                @endif
                                @if($section->rules_range)
                                    <p class="text-xs text-emerald-700 font-bold mt-1">Rules {{ $section->rules_range }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="policy-rules-grid grid xl:grid-cols-2 divide-y divide-slate-100 xl:divide-y-0">
                        @foreach($section->activeRules as $rule)
                            <article id="rule-{{ $rule->id }}" class="policy-rule-block px-5 sm:px-6 py-5 xl:border-b xl:border-slate-100">
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <span class="policy-rule-num">{{ $rule->displayNumber() }}</span>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="font-bold text-slate-900 text-base sm:text-lg">{{ $rule->title }}</h3>
                                        @if($rule->title_en)
                                            <p class="text-sm text-slate-500 mt-0.5">{{ $rule->title_en }}</p>
                                        @endif

                                        @if(filled($rule->content))
                                            <div class="policy-prose mt-3 text-sm sm:text-base">
                                                @php
                                                    $lines = preg_split('/\r\n|\r|\n/', trim($rule->content));
                                                    $bullets = [];
                                                    $paragraphs = [];
                                                    foreach ($lines as $line) {
                                                        $line = trim($line);
                                                        if ($line === '') {
                                                            continue;
                                                        }
                                                        if (str_starts_with($line, '•') || str_starts_with($line, '-')) {
                                                            $bullets[] = ltrim($line, "•-\t ");
                                                        } else {
                                                            $paragraphs[] = $line;
                                                        }
                                                    }
                                                @endphp
                                                @foreach($paragraphs as $paragraph)
                                                    <p>{{ $paragraph }}</p>
                                                @endforeach
                                                @if($bullets !== [])
                                                    <ul>
                                                        @foreach($bullets as $bullet)
                                                            <li>{{ $bullet }}</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach

            @if($sections->isEmpty())
                <div class="panel-card p-8 text-center text-slate-500">
                    <i class="fas fa-inbox text-3xl mb-3 text-slate-300"></i>
                    <p>لا توجد قواعد منشورة حالياً — تواصل مع الإدارة.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
