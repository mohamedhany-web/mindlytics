@extends('layouts.student-dashboard')

@section('title', __('student.academic_paths_title'))
@section('header', __('student.academic_paths_title'))

@php
    use App\Support\StudentFigmaAssets;
    $sp = StudentFigmaAssets::urls();
    $intent = $intent ?? 'choose';
    $fawaterakEnabled = $fawaterakEnabled ?? false;
@endphp

@push('styles')
<style>
    .ay-hub { max-width: 1120px; }
    .ay-hero {
        background: linear-gradient(135deg, #1f1e31 0%, #2a2940 55%, #1f1e31 100%);
        border-radius: 24px;
        padding: 28px 24px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .ay-hero::after {
        content: '';
        position: absolute;
        inset-inline-end: -40px;
        top: -40px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(174, 217, 234, 0.18);
        pointer-events: none;
    }
    .ay-hero h1 { font-size: clamp(1.4rem, 3vw, 2rem); font-weight: 900; margin: 0 0 8px; }
    .ay-hero p { margin: 0; color: rgba(255,255,255,0.72); font-size: 0.95rem; line-height: 1.6; max-width: 40rem; }
    .ay-intent-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
    }
    @media (min-width: 768px) {
        .ay-intent-grid { grid-template-columns: repeat(3, 1fr); }
    }
    .ay-intent-card {
        display: flex;
        flex-direction: column;
        gap: 14px;
        text-align: start;
        padding: 20px;
        border-radius: 20px;
        border: 1px solid #ecece8;
        background: #fff;
        box-shadow: var(--sp-shadow);
        cursor: pointer;
        transition: border-color .2s ease, transform .2s ease;
        width: 100%;
        font: inherit;
        color: inherit;
    }
    .ay-intent-card:hover { border-color: var(--sp-accent); transform: translateY(-2px); }
    .ay-intent-card.is-active {
        border-color: var(--sp-accent);
        box-shadow: 0 0 0 3px rgba(174, 217, 234, 0.45);
    }
    .ay-intent-card h2 { margin: 0; font-size: 1.05rem; font-weight: 800; }
    .ay-intent-card p { margin: 0; font-size: 0.85rem; color: var(--sp-muted); line-height: 1.55; }
    .ay-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
    }
    .ay-tab {
        border: 0;
        background: #fff;
        border-radius: 999px;
        padding: 10px 16px;
        font-weight: 800;
        font-size: 13px;
        color: var(--sp-muted);
        box-shadow: var(--sp-shadow);
        cursor: pointer;
        font-family: inherit;
    }
    .ay-tab.is-active {
        background: var(--sp-accent);
        color: var(--sp-accent-text);
    }
    .ay-item {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 16px;
        border-radius: 20px;
        background: #fff;
        box-shadow: var(--sp-shadow);
        border: 1px solid #ecece8;
        height: 100%;
    }
    @media (min-width: 640px) {
        .ay-item {
            flex-direction: row;
            align-items: center;
        }
    }
    .ay-item-body { flex: 1; min-width: 0; }
    .ay-item-title { margin: 0 0 4px; font-size: 1rem; font-weight: 800; }
    .ay-item-meta { margin: 0; font-size: 12px; color: var(--sp-muted); font-weight: 700; }
    .ay-item-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        flex-shrink: 0;
    }
    .ay-link-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 14px;
        border-radius: 16px;
        background: #f5f5f5;
        color: var(--sp-text);
        text-decoration: none;
        font-size: 13px;
        font-weight: 800;
    }
    .ay-link-btn:hover { background: #ecece8; }
    .ay-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        background: #f0f7fb;
        color: var(--sp-accent-text);
        font-size: 11px;
        font-weight: 800;
    }
    .ay-live-seg {
        display: inline-flex;
        gap: 6px;
        background: #f5f5f5;
        padding: 4px;
        border-radius: 999px;
        margin-bottom: 14px;
    }
    .ay-live-seg button {
        border: 0;
        background: transparent;
        border-radius: 999px;
        padding: 8px 14px;
        font-weight: 800;
        font-size: 13px;
        cursor: pointer;
        font-family: inherit;
        color: var(--sp-muted);
    }
    .ay-live-seg button.is-on {
        background: #fff;
        color: var(--sp-accent-text);
        box-shadow: var(--sp-shadow);
    }
    .ay-buy-overlay {
        position: fixed;
        inset: 0;
        z-index: 80;
        background: rgba(15,15,25,0.45);
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding: 16px;
    }
    @media (min-width: 640px) {
        .ay-buy-overlay { align-items: center; }
    }
    .ay-buy-sheet {
        width: min(440px, 100%);
        padding: 20px;
        max-height: 90dvh;
        overflow: auto;
    }
    .ay-buy-close {
        width: 36px; height: 36px; border-radius: 999px; border: 0;
        background: #f5f5f5; cursor: pointer; color: var(--sp-text);
    }
    .ay-buy-option {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px;
        border-radius: 16px;
        text-decoration: none;
        border: 1px solid #ecece8;
        background: #fafaf8;
        color: inherit;
    }
    .ay-buy-option--pay { background: #f0f9fc; border-color: #d7eef5; }
    .ay-buy-option--wa:hover,
    .ay-buy-option--pay:hover { border-color: var(--sp-accent); }
    .ay-empty {
        padding: 40px 20px;
        text-align: center;
        color: var(--sp-muted);
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="ay-hub space-y-5"
     x-data="{
        intent: '{{ $intent }}',
        liveTab: 'offline',
        setIntent(v) {
            this.intent = v;
            const u = new URL(window.location.href);
            if (v === 'choose') u.searchParams.delete('intent');
            else u.searchParams.set('intent', v);
            history.replaceState({}, '', u);
        }
     }">

    <section class="ay-hero">
        <p class="text-xs font-bold tracking-wide m-0 mb-2" style="color:rgba(174,217,234,.95)">{{ __('student.ay_hub_eyebrow') }}</p>
        <h1>{{ __('student.ay_hub_title') }}</h1>
        <p>{{ __('student.ay_hub_desc') }}</p>
    </section>

    {{-- Step 1: choose experience --}}
    <div x-show="intent === 'choose'" x-cloak class="space-y-4">
        <h2 class="sp-section-title m-0">{{ __('student.ay_what_do_you_want') }}</h2>
        <div class="ay-intent-grid">
            <button type="button" class="ay-intent-card" @click="setIntent('path')">
                <span class="sp-icon-bubble" style="background:var(--sp-lilac)">
                    <x-student.figma-icon name="icon-path.svg" box="size-6" />
                </span>
                <div>
                    <h2>{{ __('student.ay_intent_path') }}</h2>
                    <p>{{ __('student.ay_intent_path_desc') }}</p>
                </div>
                <span class="ay-chip">{{ $tracks->count() }} {{ __('student.ay_paths_count') }}</span>
            </button>

            <button type="button" class="ay-intent-card" @click="setIntent('recorded')">
                <span class="sp-icon-bubble" style="background:var(--sp-sky)">
                    <x-student.figma-icon name="icon-courses.svg" box="size-6" />
                </span>
                <div>
                    <h2>{{ __('student.ay_intent_recorded') }}</h2>
                    <p>{{ __('student.ay_intent_recorded_desc') }}</p>
                </div>
                <span class="ay-chip">{{ $recordedCourses->count() }} {{ __('student.ay_courses_count') }}</span>
            </button>

            <button type="button" class="ay-intent-card" @click="setIntent('live')">
                <span class="sp-icon-bubble" style="background:var(--sp-mint)">
                    <x-student.figma-icon name="icon-classes.svg" box="size-6" />
                </span>
                <div>
                    <h2>{{ __('student.ay_intent_live') }}</h2>
                    <p>{{ __('student.ay_intent_live_desc') }}</p>
                </div>
                <span class="ay-chip">{{ $offlineGroups->count() + $onlineGroups->count() }} {{ __('student.ay_groups_count') }}</span>
            </button>
        </div>
    </div>

    {{-- Tabs when inside a mode --}}
    <div x-show="intent !== 'choose'" x-cloak>
        <div class="ay-tabs">
            <button type="button" class="ay-tab" :class="{ 'is-active': intent === 'path' }" @click="setIntent('path')">{{ __('student.ay_intent_path') }}</button>
            <button type="button" class="ay-tab" :class="{ 'is-active': intent === 'recorded' }" @click="setIntent('recorded')">{{ __('student.ay_intent_recorded') }}</button>
            <button type="button" class="ay-tab" :class="{ 'is-active': intent === 'live' }" @click="setIntent('live')">{{ __('student.ay_intent_live') }}</button>
            <button type="button" class="ay-tab" @click="setIntent('choose')">{{ __('student.ay_back_choose') }}</button>
        </div>

        {{-- PATHS --}}
        <div x-show="intent === 'path'" class="space-y-3">
            <p class="text-sm text-[var(--sp-muted)] font-bold m-0 mb-2">{{ __('student.ay_path_intro') }}</p>
            @forelse($tracks as $track)
                @php $metrics = $track->track_metrics ?? []; @endphp
                <article class="ay-item">
                    <div class="ay-item-body">
                        <h3 class="ay-item-title">{{ $track->name }}</h3>
                        <p class="ay-item-meta">
                            {{ $track->academic_subjects_count }} {{ __('student.skill_groups') }}
                            · {{ $metrics['courses_count'] ?? 0 }} {{ __('student.ay_courses_count') }}
                        </p>
                        @if($track->description)
                            <p class="text-sm text-[var(--sp-muted)] m-0 mt-2">{{ \Illuminate\Support\Str::limit($track->description, 120) }}</p>
                        @endif
                    </div>
                    <div class="ay-item-actions">
                        <a href="{{ route('academic-years.subjects', $track) }}" class="ay-link-btn">{{ __('student.ay_explore_path') }}</a>
                        <x-student.purchase-chooser
                            :title="$track->name"
                            :whatsapp-url="$track->whatsapp_url"
                            :pay-url="$track->path_checkout_url"
                            :fawaterak-enabled="$fawaterakEnabled"
                            :hint="__('student.ay_path_pay_hint')"
                        />
                    </div>
                </article>
            @empty
                <div class="ay-empty sp-card">{{ __('student.ay_empty_paths') }}</div>
            @endforelse
        </div>

        {{-- RECORDED --}}
        <div x-show="intent === 'recorded'" class="space-y-3">
            <p class="text-sm text-[var(--sp-muted)] font-bold m-0 mb-2">{{ __('student.ay_recorded_intro') }}</p>
            @forelse($recordedCourses as $course)
                @php
                    $cTitle = $course->localized('title') ?: $course->title;
                    $priceLabel = $course->is_free ? __('student.free') : (number_format((float) $course->price, 0) . ' ' . __('student.egp'));
                @endphp
                <article class="ay-item">
                    <div class="ay-item-body">
                        <h3 class="ay-item-title">{{ $cTitle }}</h3>
                        <p class="ay-item-meta">
                            {{ $course->level ?: __('student.ay_all_levels') }}
                            @if($course->programming_language) · {{ $course->programming_language }} @endif
                        </p>
                        <p class="text-sm font-extrabold text-[var(--sp-accent-text)] m-0 mt-2">{{ $priceLabel }}</p>
                    </div>
                    <div class="ay-item-actions">
                        <a href="{{ route('courses.show', $course) }}" class="ay-link-btn">{{ __('student.ay_view_details') }}</a>
                        <x-student.purchase-chooser
                            :title="$cTitle"
                            :whatsapp-url="$course->whatsapp_url"
                            :pay-url="$course->portal_checkout_url"
                            :fawaterak-enabled="$fawaterakEnabled"
                            :price-label="$priceLabel"
                            :hint="__('student.ay_recorded_pay_hint')"
                        />
                    </div>
                </article>
            @empty
                <div class="ay-empty sp-card">{{ __('student.ay_empty_recorded') }}</div>
            @endforelse
        </div>

        {{-- LIVE --}}
        <div x-show="intent === 'live'">
            <p class="text-sm text-[var(--sp-muted)] font-bold m-0 mb-3">{{ __('student.ay_live_intro') }}</p>
            <div class="ay-live-seg">
                <button type="button" :class="{ 'is-on': liveTab === 'offline' }" @click="liveTab = 'offline'">
                    {{ __('student.ay_live_offline') }} ({{ $offlineGroups->count() }})
                </button>
                <button type="button" :class="{ 'is-on': liveTab === 'online' }" @click="liveTab = 'online'">
                    {{ __('student.ay_live_online') }} ({{ $onlineGroups->count() }})
                </button>
            </div>

            <div x-show="liveTab === 'offline'" class="space-y-3">
                @forelse($offlineGroups as $group)
                    @php
                        $gTitle = $group->name;
                        $priceLabel = number_format((float) ($group->course->price ?? 0), 0) . ' ' . __('student.egp');
                    @endphp
                    <article class="ay-item">
                        <div class="ay-item-body">
                            <span class="ay-chip mb-2">{{ __('student.ay_live_offline') }}</span>
                            <h3 class="ay-item-title">{{ $gTitle }}</h3>
                            <p class="ay-item-meta">
                                {{ $group->course->title ?? '' }}
                                · {{ __('student.ay_seats_left', ['n' => $group->seats_left]) }}
                                @if($group->start_date) · {{ $group->start_date->format('Y-m-d') }} @endif
                            </p>
                            <p class="text-sm font-extrabold text-[var(--sp-accent-text)] m-0 mt-2">{{ $priceLabel }}</p>
                        </div>
                        <div class="ay-item-actions">
                            <a href="{{ $group->book_url }}" class="ay-link-btn">{{ __('student.ay_open_booking') }}</a>
                            <x-student.purchase-chooser
                                :title="$gTitle"
                                :whatsapp-url="$group->whatsapp_url"
                                :book-url="$group->book_url"
                                :fawaterak-enabled="false"
                                :price-label="$priceLabel"
                                :hint="__('student.ay_live_pay_hint')"
                            />
                        </div>
                    </article>
                @empty
                    <div class="ay-empty sp-card">{{ __('student.ay_empty_offline') }}</div>
                @endforelse
            </div>

            <div x-show="liveTab === 'online'" class="space-y-3">
                @forelse($onlineGroups as $group)
                    @php
                        $gTitle = $group->name;
                        $priceLabel = number_format((float) ($group->course->price ?? 0), 0) . ' ' . __('student.egp');
                    @endphp
                    <article class="ay-item">
                        <div class="ay-item-body">
                            <span class="ay-chip mb-2">{{ __('student.ay_live_online') }}</span>
                            <h3 class="ay-item-title">{{ $gTitle }}</h3>
                            <p class="ay-item-meta">
                                {{ $group->course->title ?? '' }}
                                · {{ __('student.ay_seats_left', ['n' => $group->seats_left]) }}
                                @if($group->start_date) · {{ $group->start_date->format('Y-m-d') }} @endif
                            </p>
                            <p class="text-sm font-extrabold text-[var(--sp-accent-text)] m-0 mt-2">{{ $priceLabel }}</p>
                        </div>
                        <div class="ay-item-actions">
                            <a href="{{ $group->book_url }}" class="ay-link-btn">{{ __('student.ay_open_booking') }}</a>
                            <x-student.purchase-chooser
                                :title="$gTitle"
                                :whatsapp-url="$group->whatsapp_url"
                                :book-url="$group->book_url"
                                :fawaterak-enabled="false"
                                :price-label="$priceLabel"
                                :hint="__('student.ay_live_pay_hint')"
                            />
                        </div>
                    </article>
                @empty
                    <div class="ay-empty sp-card">{{ __('student.ay_empty_online') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
