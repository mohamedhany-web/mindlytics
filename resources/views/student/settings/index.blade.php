@extends('layouts.student-dashboard')

@section('title', __('student.settings_title'))
@section('header', __('student.settings_title'))

@push('styles')
<style>
    .sp-settings-cover {
        height: 120px;
        border-radius: 30px 30px 0 0;
        background: linear-gradient(135deg, rgba(174,217,234,0.45) 0%, rgba(247,247,245,1) 60%, rgba(220,222,242,0.35) 100%);
        position: relative;
        overflow: hidden;
    }
    .sp-settings-cover::after {
        content: '';
        position: absolute;
        inset-inline-start: -24px;
        bottom: -40px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(174,217,234,0.3);
    }
    .sp-settings-shell {
        border-radius: 30px;
        overflow: hidden;
        box-shadow: var(--sp-shadow);
        background: #fff;
    }
    .sp-settings-nav a {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 999px;
        font-size: 0.8125rem;
        font-weight: 800;
        color: var(--sp-muted);
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }
    .sp-settings-nav a:hover { background: #f7f7f5; color: var(--sp-accent-text); }
    .sp-settings-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.1rem;
        border-radius: 18px;
        background: #f7f7f5;
        border: 1px solid rgba(0,0,0,0.04);
    }
    .sp-toggle {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 26px;
        flex-shrink: 0;
    }
    .sp-toggle input { opacity: 0; width: 0; height: 0; }
    .sp-toggle-track {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #d8d8d4;
        transition: 0.2s;
        border-radius: 999px;
    }
    .sp-toggle-track::before {
        content: '';
        position: absolute;
        height: 20px;
        width: 20px;
        inset-inline-start: 3px;
        bottom: 3px;
        background: #fff;
        transition: 0.2s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
    }
    .sp-toggle input:checked + .sp-toggle-track { background: var(--sp-accent); }
    .sp-toggle input:checked + .sp-toggle-track::before { transform: translateX(22px); }
    [dir="rtl"] .sp-toggle input:checked + .sp-toggle-track::before { transform: translateX(-22px); }
    .sp-settings-select {
        width: 100%;
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.06);
        background: #fafaf8;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--sp-text);
    }
    .sp-settings-select:focus {
        outline: none;
        border-color: var(--sp-accent);
        box-shadow: 0 0 0 3px rgba(174,217,234,0.35);
        background: #fff;
    }
    .sp-settings-warn {
        border-radius: 18px;
        border: 1px solid rgba(244,168,154,0.45);
        background: rgba(249,228,215,0.35);
    }
    .sp-settings-danger {
        border-radius: 18px;
        border: 1px solid rgba(244,168,154,0.65);
        background: rgba(254,242,242,0.65);
    }
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
@endphp

<div class="space-y-5 max-w-4xl mx-auto">
    <div class="sp-settings-shell">
        <div class="sp-settings-cover" aria-hidden="true"></div>

        <div class="px-5 sm:px-8 pb-6 -mt-10 relative z-[1]">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4 sm:gap-6">
                <div class="w-16 h-16 rounded-[20px] border-4 border-white shadow-lg overflow-hidden bg-[var(--sp-accent)] flex items-center justify-center shrink-0">
                    <x-student.figma-icon name="icon-settings.svg" box="size-8" class="opacity-90" />
                </div>
                <div class="flex-1 min-w-0 pb-1">
                    <p class="text-xs font-bold text-[var(--sp-muted)] uppercase tracking-wide m-0">{{ __('student.settings_studio_eyebrow') }}</p>
                    <h2 class="text-2xl sm:text-3xl font-extrabold m-0 mt-1 leading-tight">{{ __('student.settings_title') }}</h2>
                    <p class="text-sm text-[var(--sp-muted)] m-0 mt-1">{{ __('student.settings_subtitle') }}</p>
                </div>
                <a href="{{ route('profile') }}" class="inline-flex items-center justify-center rounded-[20px] bg-[#f7f7f5] hover:bg-[var(--sp-accent)] px-4 py-2.5 text-sm font-extrabold text-[var(--sp-accent-text)] transition shrink-0">
                    {{ __('student.settings_back_profile') }}
                </a>
            </div>

            <nav class="sp-settings-nav flex flex-wrap gap-2 mt-6 pt-5 border-t border-black/5">
                <a href="#settings-notifications"><x-student.figma-icon name="icon-notifications.svg" box="size-4" />{{ __('student.notification_settings') }}</a>
                <a href="#settings-privacy"><x-student.figma-icon name="icon-profile.svg" box="size-4" />{{ __('student.settings_privacy_title') }}</a>
                <a href="#settings-display"><x-student.figma-icon name="icon-settings.svg" box="size-4" />{{ __('student.settings_display_title') }}</a>
                <a href="#settings-account"><x-student.figma-icon name="icon-admin.svg" box="size-4" />{{ __('student.settings_account_title') }}</a>
            </nav>
        </div>
    </div>

    <div class="space-y-5">
        {{-- Notifications --}}
        <section id="settings-notifications" class="sp-card p-5 sm:p-6 space-y-4 scroll-mt-24">
            <header class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-[14px] bg-[var(--sp-accent)] flex items-center justify-center shrink-0">
                    <x-student.figma-icon name="icon-notifications.svg" box="size-5" />
                </span>
                <div>
                    <h3 class="text-lg font-extrabold m-0">{{ __('student.notification_settings') }}</h3>
                    <p class="text-xs text-[var(--sp-muted)] font-bold m-0 mt-0.5">{{ __('student.settings_notif_hint') }}</p>
                </div>
            </header>

            @foreach([
                ['title' => __('student.new_courses_notif'), 'desc' => __('student.new_courses_notif_desc'), 'checked' => true],
                ['title' => __('student.orders_notif'), 'desc' => __('student.orders_notif_desc'), 'checked' => true],
                ['title' => __('student.exams_notif'), 'desc' => __('student.exams_notif_desc'), 'checked' => true],
            ] as $row)
                <div class="sp-settings-row">
                    <div class="min-w-0">
                        <p class="text-sm font-extrabold m-0">{{ $row['title'] }}</p>
                        <p class="text-xs text-[var(--sp-muted)] font-bold m-0 mt-0.5">{{ $row['desc'] }}</p>
                    </div>
                    <label class="sp-toggle" aria-label="{{ $row['title'] }}">
                        <input type="checkbox" @checked($row['checked']) disabled>
                        <span class="sp-toggle-track"></span>
                    </label>
                </div>
            @endforeach
            <p class="text-xs text-[var(--sp-muted)] font-bold m-0">{{ __('student.settings_coming_soon') }}</p>
        </section>

        {{-- Privacy --}}
        <section id="settings-privacy" class="sp-card p-5 sm:p-6 space-y-4 scroll-mt-24">
            <header class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-[14px] bg-[#f7f7f5] flex items-center justify-center shrink-0">
                    <x-student.figma-icon name="icon-profile.svg" box="size-5" />
                </span>
                <h3 class="text-lg font-extrabold m-0">{{ __('student.settings_privacy_title') }}</h3>
            </header>

            @foreach([
                ['title' => __('student.settings_show_progress'), 'desc' => __('student.settings_show_progress_desc'), 'checked' => true],
                ['title' => __('student.settings_show_activity'), 'desc' => __('student.settings_show_activity_desc'), 'checked' => false],
            ] as $row)
                <div class="sp-settings-row">
                    <div class="min-w-0">
                        <p class="text-sm font-extrabold m-0">{{ $row['title'] }}</p>
                        <p class="text-xs text-[var(--sp-muted)] font-bold m-0 mt-0.5">{{ $row['desc'] }}</p>
                    </div>
                    <label class="sp-toggle" aria-label="{{ $row['title'] }}">
                        <input type="checkbox" @checked($row['checked']) disabled>
                        <span class="sp-toggle-track"></span>
                    </label>
                </div>
            @endforeach
        </section>

        {{-- Display & language --}}
        <section id="settings-display" class="sp-card p-5 sm:p-6 space-y-4 scroll-mt-24">
            <header class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-[14px] bg-[#f7f7f5] flex items-center justify-center shrink-0">
                    <x-student.figma-icon name="icon-settings.svg" box="size-5" />
                </span>
                <h3 class="text-lg font-extrabold m-0">{{ __('student.settings_display_title') }}</h3>
            </header>

            <div class="sp-settings-row flex-col !items-stretch gap-3">
                <div>
                    <p class="text-sm font-extrabold m-0">{{ __('student.settings_theme_label') }}</p>
                    <p class="text-xs text-[var(--sp-muted)] font-bold m-0 mt-0.5">{{ __('student.settings_theme_hint') }}</p>
                </div>
                <select class="sp-settings-select" disabled aria-disabled="true">
                    <option value="light" selected>{{ __('student.settings_theme_light') }}</option>
                    <option value="dark">{{ __('student.settings_theme_dark') }}</option>
                    <option value="auto">{{ __('student.settings_theme_auto') }}</option>
                </select>
            </div>

            <div class="sp-settings-row flex-col !items-stretch gap-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-extrabold m-0">{{ __('student.language') }}</p>
                        <p class="text-xs text-[var(--sp-muted)] font-bold m-0 mt-0.5">{{ __('student.settings_language_hint') }}</p>
                    </div>
                    <x-student.language-switcher />
                </div>
            </div>
        </section>

        {{-- Account --}}
        <section id="settings-account" class="sp-card p-5 sm:p-6 space-y-4 scroll-mt-24">
            <header class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-[14px] bg-[#f7f7f5] flex items-center justify-center shrink-0">
                    <x-student.figma-icon name="icon-admin.svg" box="size-5" />
                </span>
                <h3 class="text-lg font-extrabold m-0">{{ __('student.settings_account_title') }}</h3>
            </header>

            <div class="sp-settings-warn p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-extrabold m-0">{{ __('student.settings_download_data') }}</p>
                    <p class="text-xs text-[var(--sp-muted)] font-bold m-0 mt-0.5">{{ __('student.settings_download_data_desc') }}</p>
                </div>
                <button type="button" disabled class="inline-flex items-center justify-center rounded-[16px] bg-[#f7f7f5] px-4 py-2.5 text-sm font-extrabold text-[var(--sp-muted)] cursor-not-allowed shrink-0">
                    {{ __('student.settings_download_btn') }}
                </button>
            </div>

            <div class="sp-settings-danger p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-extrabold m-0">{{ __('student.settings_delete_account') }}</p>
                    <p class="text-xs text-[var(--sp-muted)] font-bold m-0 mt-0.5">{{ __('student.settings_delete_account_desc') }}</p>
                </div>
                <button type="button" disabled
                        class="inline-flex items-center justify-center rounded-[16px] bg-[#f4a89a] px-4 py-2.5 text-sm font-extrabold text-[#1f1e31] cursor-not-allowed shrink-0">
                    {{ __('student.settings_delete_btn') }}
                </button>
            </div>
            <p class="text-xs text-[var(--sp-muted)] font-bold m-0">{{ __('student.settings_coming_soon') }}</p>
        </section>
    </div>
</div>
@endsection
