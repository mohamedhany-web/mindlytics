@extends('layouts.student-dashboard')

@php
    $bubbleColors = ['#f9e4d7', '#d7e8f9', '#d7eef5', '#f9f0d7', '#dcdef2'];
    $hasIssued = isset($certificates) && $certificates->count() > 0;
    $hasEligible = isset($eligible) && $eligible->isNotEmpty();
@endphp

@section('title', __('student.my_certificates_title'))
@section('header', __('student.my_certificates_title'))

@push('styles')
<style>
    .sp-cert-hero {
        background: #2f2e43;
        border-radius: 30px;
        color: #fff;
        padding: 28px 24px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--sp-shadow);
    }
    .sp-cert-hero::before {
        content: '';
        position: absolute;
        inset-inline-end: -40px;
        top: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(174,217,234,0.28), transparent 70%);
        pointer-events: none;
    }
    .sp-cert-hero::after {
        content: '';
        position: absolute;
        inset-inline-start: -30px;
        bottom: -80px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(249,228,215,0.18), transparent 70%);
        pointer-events: none;
    }
    .sp-cert-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }
    .sp-cert-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(0,0,0,0.1);
        border-color: var(--sp-accent);
    }
</style>
@endpush

@section('content')
<div class="space-y-5">
    @if(session('success'))
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold" style="background:var(--sp-mint);color:var(--sp-accent-text)">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold bg-[#f9e4d7] text-[#7a3b2e]">{{ session('error') }}</div>
    @endif

    {{-- Hero --}}
    <section class="sp-cert-hero">
        <div class="relative z-[1] flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-8">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white/60 m-0 mb-2">{{ __('student.cert_index_eyebrow') }}</p>
                <h2 class="text-2xl sm:text-[28px] font-extrabold m-0 leading-tight">{{ __('student.my_certificates_title') }}</h2>
                <p class="text-sm text-white/70 m-0 mt-3 max-w-2xl leading-relaxed">{{ __('student.cert_index_subtitle') }}</p>
                <div class="flex flex-wrap gap-3 mt-6">
                    <a href="{{ route('my-courses.index') }}" class="sp-promo-btn !mt-0 !text-[var(--sp-accent-text)]">
                        <x-student.figma-icon name="icon-courses.svg" box="size-4" class="me-2" />
                        {{ __('student.view_my_courses') }}
                    </a>
                    @if($hasEligible)
                        <a href="#cert-ready" class="inline-flex items-center justify-center rounded-[20px] bg-white/10 hover:bg-white/15 px-5 py-3.5 text-sm font-extrabold text-white transition">
                            {{ __('student.cert_ready_to_issue') }}
                            <span class="ms-2 inline-flex items-center justify-center rounded-full bg-white/15 min-w-[22px] h-[22px] px-1.5 text-xs font-black">{{ $stats['ready'] ?? $eligible->count() }}</span>
                        </a>
                    @endif
                </div>
            </div>
            @if(isset($stats))
                <div class="grid grid-cols-3 gap-2 sm:gap-3 shrink-0 w-full lg:w-auto lg:min-w-[280px]">
                    <div class="rounded-2xl bg-white/8 px-3 py-3 sm:px-4 border border-white/10 text-center">
                        <p class="text-xl sm:text-2xl font-black text-[var(--sp-accent)] m-0">{{ $stats['total'] ?? 0 }}</p>
                        <p class="text-[9px] sm:text-[10px] font-bold text-white/50 m-0 mt-1 uppercase tracking-wide leading-tight">{{ __('student.total_certificates') }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/8 px-3 py-3 sm:px-4 border border-white/10 text-center">
                        <p class="text-xl sm:text-2xl font-black text-[var(--sp-accent)] m-0">{{ $stats['issued'] ?? 0 }}</p>
                        <p class="text-[9px] sm:text-[10px] font-bold text-white/50 m-0 mt-1 uppercase tracking-wide leading-tight">{{ __('student.issued_label') }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/8 px-3 py-3 sm:px-4 border border-white/10 text-center">
                        <p class="text-xl sm:text-2xl font-black text-[var(--sp-accent)] m-0">{{ $stats['ready'] ?? 0 }}</p>
                        <p class="text-[9px] sm:text-[10px] font-bold text-white/50 m-0 mt-1 uppercase tracking-wide leading-tight">{{ __('student.cert_ready_to_issue') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </section>

    @if(isset($stats))
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            <div class="sp-card p-4 sm:p-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.total_certificates') }}</p>
                        <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                    <span class="sp-icon-bubble" style="background:var(--sp-sky)">
                        <x-student.figma-icon name="icon-certificates.svg" />
                    </span>
                </div>
            </div>
            <div class="sp-card p-4 sm:p-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.issued_label') }}</p>
                        <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['issued'] ?? 0 }}</p>
                    </div>
                    <span class="sp-icon-bubble" style="background:var(--sp-mint)">
                        <x-student.figma-icon name="icon-star.svg" />
                    </span>
                </div>
            </div>
            <div class="sp-card p-4 sm:p-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-[var(--sp-muted)] m-0 uppercase tracking-wide">{{ __('student.cert_ready_to_issue') }}</p>
                        <p class="text-2xl font-black text-[var(--sp-accent-text)] m-0 mt-1 leading-none">{{ $stats['ready'] ?? 0 }}</p>
                    </div>
                    <span class="sp-icon-bubble" style="background:var(--sp-amber-soft)">
                        <x-student.figma-icon name="icon-trend.svg" />
                    </span>
                </div>
            </div>
        </div>
    @endif

    @if($hasEligible)
        <section id="cert-ready" class="sp-card overflow-hidden">
            <div class="flex flex-wrap items-start justify-between gap-4 px-5 py-4 sm:px-6 sm:py-5 border-b border-black/5" style="background:var(--sp-mint)">
                <div class="flex items-start gap-3 min-w-0">
                    <span class="sp-icon-bubble shrink-0 !w-12 !h-12" style="background:var(--sp-badge-done)">
                        <x-student.figma-icon name="icon-certificates.svg" box="size-6" />
                    </span>
                    <div>
                        <h3 class="font-extrabold text-base sm:text-lg m-0">{{ __('student.cert_eligible_title') }}</h3>
                        <p class="text-sm text-[var(--sp-muted)] m-0 mt-1 max-w-2xl">{{ __('student.cert_eligible_desc') }}</p>
                    </div>
                </div>
                <span class="sp-pill sp-pill--done shrink-0">{{ $eligible->count() }}</span>
            </div>
            <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                @foreach($eligible as $enrollment)
                    @php
                        $pct = max(0, min(100, (int) round((float) $enrollment->progress)));
                        $bubble = $bubbleColors[$loop->index % count($bubbleColors)];
                    @endphp
                    <article class="sp-card p-4 sm:p-5 flex flex-col gap-4 border border-[#f0f0ec]">
                        <div class="flex items-start gap-3 min-w-0">
                            <span class="sp-icon-bubble shrink-0 !w-11 !h-11" style="background:{{ $bubble }}">
                                <x-student.figma-icon name="icon-courses.svg" box="size-5" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="font-extrabold text-sm sm:text-base m-0 line-clamp-2 leading-snug">
                                    {{ $enrollment->course?->title ?? __('student.cert_course_fallback') }}
                                </p>
                                <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mt-1.5">
                                    {{ __('student.cert_progress_pct', ['pct' => $pct]) }}
                                </p>
                                <div class="mt-2 h-1.5 rounded-full bg-[#f0f0ec] overflow-hidden">
                                    <div class="h-full rounded-full bg-[var(--sp-accent)]" style="width:{{ $pct }}%"></div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('student.certificates.claim', $enrollment->advanced_course_id) }}"
                           class="sp-promo-btn !mt-0 w-full !text-[var(--sp-accent-text)] text-center justify-center">
                            <x-student.figma-icon name="icon-certificates.svg" box="size-4" class="me-2" />
                            {{ __('student.cert_claim_cta') }}
                        </a>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @if($hasIssued)
        <section class="space-y-4">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-3">
                    <span class="sp-icon-bubble shrink-0" style="background:var(--sp-lilac)">
                        <x-student.figma-icon name="icon-certificates.svg" />
                    </span>
                    <div>
                        <h3 class="sp-section-title m-0">{{ __('student.issued_certificates') }}</h3>
                        <p class="text-xs text-[var(--sp-muted)] m-0 mt-1">{{ __('student.certificates_subtitle') }}</p>
                    </div>
                </div>
                <span class="sp-pill sp-pill--progress">{{ $certificates->total() }}</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
                @foreach($certificates as $certificate)
                    @php
                        $bubble = $bubbleColors[$loop->index % count($bubbleColors)];
                        $issuedDate = $certificate->issued_at ?? $certificate->issue_date;
                        $serial = $certificate->serial_number ?: ($certificate->certificate_number ? '#'.substr($certificate->certificate_number, -6) : null);
                        $title = $certificate->title ?? $certificate->course_name ?? __('student.completion_certificate');
                    @endphp
                    <a href="{{ route('student.certificates.show', $certificate) }}"
                       class="sp-cert-card sp-card p-4 sm:p-5 block no-underline text-inherit border border-[#f0f0ec]">
                        <div class="flex items-start gap-3">
                            <span class="sp-icon-bubble shrink-0 !w-12 !h-12" style="background:{{ $bubble }}">
                                <x-student.figma-icon name="icon-certificates.svg" box="size-6" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <h4 class="font-extrabold text-sm sm:text-base m-0 line-clamp-2 leading-snug">{{ $title }}</h4>
                                @if($certificate->course)
                                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mt-1 line-clamp-2">{{ $certificate->course->title }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 mt-4 pt-3 border-t border-black/5">
                            @if($issuedDate)
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-[var(--sp-muted)]">
                                    <x-student.figma-icon name="icon-calendar.svg" box="size-3" />
                                    {{ $issuedDate->format('Y/m/d') }}
                                </span>
                            @endif
                            @if($serial)
                                <span class="sp-pill !py-1 !px-2 !text-[10px] font-mono">{{ $serial }}</span>
                            @endif
                        </div>
                        <p class="text-sm font-extrabold text-[var(--sp-accent-text)] m-0 mt-3 inline-flex items-center gap-2">
                            {{ __('student.view_certificate') }}
                            <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-60 rtl:rotate-180" />
                        </p>
                    </a>
                @endforeach
            </div>

            @if($certificates->hasPages())
                <div class="flex justify-center pt-2">{{ $certificates->links() }}</div>
            @endif
        </section>
    @elseif(! $hasEligible)
        <div class="sp-card p-10 sm:p-12 text-center">
            <span class="sp-icon-bubble mx-auto mb-4 !w-16 !h-16" style="background:var(--sp-sky)">
                <x-student.figma-icon name="icon-certificates.svg" box="size-8" />
            </span>
            <h3 class="text-lg font-extrabold m-0">{{ __('student.no_certificates') }}</h3>
            <p class="text-sm text-[var(--sp-muted)] m-0 mt-2 max-w-md mx-auto leading-relaxed">{{ __('student.cert_empty_cta_desc') }}</p>
            <a href="{{ route('my-courses.index') }}" class="sp-promo-btn !mt-6 inline-flex !text-[var(--sp-accent-text)]">
                <x-student.figma-icon name="icon-courses.svg" box="size-4" class="me-2" />
                {{ __('student.view_my_courses') }}
            </a>
        </div>
    @endif
</div>
@endsection
