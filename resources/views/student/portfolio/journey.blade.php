@extends('layouts.student-dashboard')

@php
    $completionPct = min(100, (int) ($profile->profile_completion ?? 0));
@endphp

@section('title', __('student.pf_journey_profile_title'))
@section('header', __('student.pf_journey_profile_title'))

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
    .sp-pf-input {
        width: 100%;
        border-radius: 30px;
        border: 1px solid rgba(0,0,0,0.06);
        background: #f7f7f5;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--sp-text);
    }
    .sp-pf-input:focus { outline: none; box-shadow: 0 0 0 2px var(--sp-accent); }
    .sp-pf-textarea { border-radius: 20px; min-height: 120px; resize: vertical; }
</style>
@endpush

@section('content')
<div class="space-y-5 max-w-3xl">
    <nav class="flex flex-wrap items-center gap-2 text-sm font-bold text-[var(--sp-muted)]">
        <a href="{{ route('student.portfolio.index') }}" class="sp-link">{{ __('student.pf_page_title') }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <span class="text-[var(--sp-text)]">{{ __('student.pf_journey_profile_title') }}</span>
    </nav>

    @if(session('success'))
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold" style="background:var(--sp-mint);color:var(--sp-accent-text)">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold bg-[#f9e4d7] text-[#7a3b2e]">
            <ul class="space-y-1 m-0 p-0 list-none">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <section class="sp-pf-hero">
        <div class="relative z-[1]">
            <p class="text-sm font-bold text-white/60 m-0 mb-2">{{ __('student.pf_journey_eyebrow') }}</p>
            <h2 class="text-xl sm:text-2xl font-extrabold m-0">{{ __('student.pf_journey_profile_title') }}</h2>
            <p class="text-sm text-white/70 m-0 mt-2 leading-relaxed">{{ __('student.pf_journey_subtitle') }}</p>
            <p class="text-xs font-bold text-white/50 m-0 mt-3 font-mono" dir="ltr">{{ url('/j/'.$profile->slug) }}</p>
        </div>
    </section>

    <section class="sp-card p-4 sm:p-5">
        <p class="text-sm font-extrabold m-0">{{ __('student.pf_journey_completion_hint', ['pct' => $completionPct]) }}</p>
        <div class="h-2 rounded-full bg-[#f0f0ec] overflow-hidden mt-2">
            <div class="h-full rounded-full bg-[var(--sp-accent)]" style="width:{{ $completionPct }}%"></div>
        </div>
    </section>

    <section class="sp-card overflow-hidden">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-black/5 bg-[#f7f7f5]">
            <span class="sp-icon-bubble shrink-0" style="background:var(--sp-peach)">
                <x-student.figma-icon name="icon-profile.svg" />
            </span>
            <h3 class="font-extrabold text-base m-0">{{ __('student.pf_journey_profile_title') }}</h3>
        </div>

        <form method="POST" action="{{ route('student.portfolio.journey.update') }}" class="p-5 sm:p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.pf_display_name') }}</label>
                <input type="text" name="display_name" value="{{ old('display_name', $profile->display_name) }}" class="sp-pf-input">
            </div>

            <div>
                <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.pf_slug') }}</label>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-[var(--sp-muted)] shrink-0" dir="ltr">/j/</span>
                    <input type="text" name="slug" value="{{ old('slug', $profile->slug) }}" class="sp-pf-input font-mono" dir="ltr">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.pf_headline') }}</label>
                <input type="text" name="headline" value="{{ old('headline', $profile->headline) }}" placeholder="{{ __('student.pf_headline_placeholder') }}" class="sp-pf-input">
            </div>

            <div>
                <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.pf_bio') }}</label>
                <textarea name="bio" rows="4" class="sp-pf-input sp-pf-textarea">{{ old('bio', $profile->bio) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.pf_career_goal') }}</label>
                <input type="text" name="career_goal" value="{{ old('career_goal', $profile->career_goal) }}" class="sp-pf-input">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.pf_github') }}</label>
                    <input type="url" name="github_url" value="{{ old('github_url', $profile->github_url) }}" class="sp-pf-input" dir="ltr">
                </div>
                <div>
                    <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.pf_linkedin') }}</label>
                    <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $profile->linkedin_url) }}" class="sp-pf-input" dir="ltr">
                </div>
                <div>
                    <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.pf_website') }}</label>
                    <input type="url" name="website_url" value="{{ old('website_url', $profile->website_url) }}" class="sp-pf-input" dir="ltr">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.pf_visibility') }}</label>
                <select name="visibility" class="sp-pf-input">
                    <option value="private" {{ old('visibility', $profile->visibility) === 'private' ? 'selected' : '' }}>{{ __('student.pf_visibility_private') }}</option>
                    <option value="unlisted" {{ old('visibility', $profile->visibility) === 'unlisted' ? 'selected' : '' }}>{{ __('student.pf_visibility_unlisted') }}</option>
                    <option value="public" {{ old('visibility', $profile->visibility) === 'public' ? 'selected' : '' }}>{{ __('student.pf_visibility_public') }}</option>
                </select>
            </div>

            <label class="inline-flex items-center gap-2 text-sm font-bold cursor-pointer">
                <input type="checkbox" name="is_open_to_work" value="1" class="rounded border-black/10 text-[var(--sp-accent)]" {{ old('is_open_to_work', $profile->is_open_to_work) ? 'checked' : '' }}>
                {{ __('student.pf_open_to_work') }}
            </label>

            <div class="flex flex-wrap gap-3 pt-2 border-t border-black/5">
                <a href="{{ route('student.portfolio.index') }}" class="inline-flex items-center justify-center rounded-[20px] bg-[#f7f7f5] hover:bg-[var(--sp-accent)] px-5 py-3.5 text-sm font-extrabold text-[var(--sp-accent-text)] transition">
                    {{ __('student.pf_back') }}
                </a>
                <button type="submit" class="sp-promo-btn !mt-0 !text-[var(--sp-accent-text)]">{{ __('student.pf_save_profile') }}</button>
            </div>
        </form>
    </section>
</div>
@endsection
