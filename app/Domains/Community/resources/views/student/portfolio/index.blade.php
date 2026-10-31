@extends('layouts.student-dashboard')

@php
    $completionPct = min(100, (int) ($profile->profile_completion ?? 0));
    $statusPill = fn (string $status) => match ($status) {
        'published' => 'sp-pill--done',
        'approved' => 'sp-pill--progress',
        'pending_review', 'resubmitted' => 'sp-pill--upcoming',
        default => '',
    };
    $statusLabel = fn (string $status) => __('student.pf_status_' . $status);
    $bubbleColors = ['#f9e4d7', '#d7e8f9', '#d7eef5', '#f9f0d7', '#dcdef2'];
@endphp

@section('title', __('student.pf_page_title'))
@section('header', __('student.pf_page_title'))

@push('styles')
<style>
    .sp-pf-hero {
        background: #2f2e43;
        border-radius: 30px;
        color: #fff;
        padding: 28px 24px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--sp-shadow);
    }
    .sp-pf-hero::before {
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
    .sp-pf-card { transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease; }
    .sp-pf-card:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(0,0,0,0.1); border-color: var(--sp-accent); }
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
    <section class="sp-pf-hero">
        <div class="relative z-[1] flex flex-col lg:flex-row lg:items-end gap-6 lg:gap-8">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white/60 m-0 mb-2">{{ __('student.pf_index_eyebrow') }}</p>
                <h2 class="text-2xl sm:text-[28px] font-extrabold m-0 leading-tight">{{ __('student.pf_page_title') }}</h2>
                <p class="text-sm text-white/70 m-0 mt-3 max-w-2xl leading-relaxed">{{ __('student.pf_index_subtitle') }}</p>
                <div class="flex flex-wrap gap-3 mt-6">
                    <a href="{{ route('student.portfolio.journey') }}" class="inline-flex items-center justify-center rounded-[20px] bg-white/10 hover:bg-white/15 px-5 py-3.5 text-sm font-extrabold text-white transition">
                        {{ __('student.pf_journey_profile_btn') }}
                    </a>
                    <a href="{{ route('student.portfolio.create') }}" class="sp-promo-btn !mt-0 !text-[var(--sp-accent-text)]">
                        <x-student.figma-icon name="icon-plus.svg" box="size-4" class="me-2" />
                        {{ __('student.pf_add_project_btn') }}
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 shrink-0 w-full lg:w-auto lg:min-w-[320px]">
                @foreach([
                    ['key' => 'total', 'label' => __('student.pf_stat_total')],
                    ['key' => 'published', 'label' => __('student.pf_stat_published')],
                    ['key' => 'in_review', 'label' => __('student.pf_stat_in_review')],
                    ['key' => 'needs_work', 'label' => __('student.pf_stat_needs_work')],
                ] as $stat)
                    <div class="rounded-2xl bg-white/8 px-3 py-3 sm:px-4 border border-white/10 text-center">
                        <p class="text-xl sm:text-2xl font-black text-[var(--sp-accent)] m-0">{{ $counts[$stat['key']] ?? 0 }}</p>
                        <p class="text-[9px] sm:text-[10px] font-bold text-white/50 m-0 mt-1 uppercase tracking-wide leading-tight">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Profile progress --}}
    <section class="sp-card p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0 flex-1">
                <p class="text-sm font-extrabold m-0">{{ __('student.pf_profile_completion') }}: {{ $completionPct }}%</p>
                <div class="h-2 rounded-full bg-[#f0f0ec] overflow-hidden mt-2 max-w-md">
                    <div class="h-full rounded-full bg-[var(--sp-accent)]" style="width:{{ $completionPct }}%"></div>
                </div>
                @if(($counts['featured'] ?? 0) > 0)
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mt-2">{{ __('student.pf_stat_featured') }}: {{ $counts['featured'] }}</p>
                @endif
            </div>
            <div class="text-xs font-bold text-[var(--sp-muted)] min-w-0">
                <span class="block mb-1">{{ __('student.pf_public_link') }}:</span>
                @if($profile->isPubliclyVisible())
                    <a class="sp-link text-sm font-extrabold break-all" href="{{ route('public.journey.show', $profile->slug) }}" target="_blank">/j/{{ $profile->slug }}</a>
                @else
                    <span class="text-[var(--sp-text)] font-extrabold">/j/{{ $profile->slug }}</span>
                    <span class="sp-pill sp-pill--upcoming ms-2">{{ __('student.pf_not_public_yet') }}</span>
                @endif
            </div>
        </div>
    </section>

    @if(isset($achievements) && $achievements->count())
        <section class="sp-card overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-black/5 bg-[#f7f7f5]">
                <span class="sp-icon-bubble shrink-0" style="background:var(--sp-amber-soft)">
                    <x-student.figma-icon name="icon-star.svg" />
                </span>
                <h3 class="font-extrabold text-base m-0">{{ __('student.pf_achievements_title') }}</h3>
            </div>
            <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($achievements as $ua)
                    @continue(!$ua->achievement)
                    <div class="sp-process-row !shadow-none border border-[#f0f0ec]">
                        <span class="sp-icon-bubble shrink-0 !w-10 !h-10" style="background:var(--sp-mint)">
                            <x-student.figma-icon name="icon-certificates.svg" box="size-5" />
                        </span>
                        <div class="min-w-0">
                            <p class="font-extrabold text-sm m-0">{{ $ua->achievement->name }}</p>
                            <p class="text-xs text-[var(--sp-muted)] m-0 mt-0.5 line-clamp-2">{{ $ua->achievement->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if($projects->count() > 0)
        <section class="space-y-4">
            <div class="flex items-center gap-3">
                <span class="sp-icon-bubble shrink-0" style="background:var(--sp-lilac)">
                    <x-student.figma-icon name="icon-path.svg" />
                </span>
                <h3 class="sp-section-title m-0">{{ __('student.pf_projects_title') }}</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
                @foreach($projects as $project)
                    @php $bubble = $bubbleColors[$loop->index % count($bubbleColors)]; @endphp
                    <article class="sp-pf-card sp-card overflow-hidden border border-[#f0f0ec]">
                        @if($project->image_path)
                            <div class="aspect-video bg-[#f7f7f5]">
                                <img src="{{ asset($project->image_path) }}" alt="{{ $project->title }}" class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="aspect-video bg-[#f7f7f5] flex items-center justify-center">
                                <span class="sp-icon-bubble !w-14 !h-14" style="background:{{ $bubble }}">
                                    <x-student.figma-icon name="icon-cell-code.svg" box="size-7" />
                                </span>
                            </div>
                        @endif
                        <div class="p-4">
                            <h4 class="font-extrabold text-sm sm:text-base m-0 line-clamp-2 leading-snug">{{ $project->title }}</h4>
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <span class="sp-pill {{ $statusPill($project->status) }} !text-[10px]">{{ $statusLabel($project->status) }}</span>
                                @if($project->program_type)
                                    <span class="sp-pill !text-[10px]">{{ $project->programTypeLabel() }}</span>
                                @endif
                                @if($project->is_featured)
                                    <span class="sp-pill sp-pill--done !text-[10px]">{{ __('student.pf_stat_featured') }}</span>
                                @endif
                            </div>
                            @if($project->programContextLabel())
                                <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mt-2">{{ $project->programContextLabel() }}</p>
                            @endif
                            @if($project->rejected_reason && in_array($project->status, ['rejected', 'changes_requested']))
                                <p class="text-xs font-bold text-[#7a3b2e] m-0 mt-2 line-clamp-2">{{ $project->rejected_reason }}</p>
                            @endif
                            <div class="mt-3 pt-3 border-t border-black/5">
                                @if($project->isEditableByStudent())
                                    <a href="{{ route('student.portfolio.edit', $project) }}" class="sp-link text-xs font-extrabold">{{ __('student.pf_edit_resubmit') }}</a>
                                @elseif($project->status === 'published')
                                    <a href="{{ route('public.portfolio.show', $project->id) }}" target="_blank" class="sp-link text-xs font-extrabold block mb-2">{{ __('student.pf_view_gallery') }}</a>
                                    @include('components.journey-share-bar', [
                                        'canonicalUrl' => route('public.portfolio.show', $project->id),
                                        'shareTitle' => $project->title . ' — Mindlytics Verified',
                                        'shareableType' => 'project',
                                        'shareableId' => $project->id,
                                        'cardImageUrl' => $shareCards->projectCardUrl($project, $project->is_featured ? 'featured' : 'project_verified'),
                                        'cardType' => $project->is_featured ? 'featured' : 'project_verified',
                                    ])
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($projects->hasPages())
                <div class="flex justify-center pt-2">{{ $projects->links() }}</div>
            @endif
        </section>
    @else
        <div class="sp-card p-10 sm:p-12 text-center">
            <span class="sp-icon-bubble mx-auto mb-4 !w-16 !h-16" style="background:var(--sp-sky)">
                <x-student.figma-icon name="icon-path.svg" box="size-8" />
            </span>
            <h3 class="text-lg font-extrabold m-0">{{ __('student.pf_empty_title') }}</h3>
            <p class="text-sm text-[var(--sp-muted)] m-0 mt-2 max-w-md mx-auto leading-relaxed">{{ __('student.pf_empty_desc') }}</p>
            <a href="{{ route('student.portfolio.create') }}" class="sp-promo-btn !mt-6 inline-flex !text-[var(--sp-accent-text)]">
                <x-student.figma-icon name="icon-plus.svg" box="size-4" class="me-2" />
                {{ __('student.pf_empty_cta') }}
            </a>
        </div>
    @endif
</div>
@endsection
