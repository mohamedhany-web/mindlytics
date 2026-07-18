@extends('layouts.student-dashboard')

@section('title', __('student.settings_title'))

@php
    $currentLocale = app()->getLocale();
@endphp

@push('styles')
@include('student.offline-courses.partials.los-styles')
<style>
    .st-sec { margin-bottom: 12px; }
    .st-sec-head {
        display: flex; align-items: center; gap: 10px; margin-bottom: 12px;
    }
    .st-sec-head .ico {
        width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: rgba(73, 164, 162, 0.12); color: var(--ml-teal-deep);
    }
    .st-sec-head h2 { margin: 0; font-size: 15px; font-weight: 700; }
    .st-row {
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        padding: 12px; border-radius: 12px; background: var(--ml-well);
        border: 1px solid var(--ml-line); margin-bottom: 8px;
    }
    .st-row:last-child { margin-bottom: 0; }
    .st-row h3 { margin: 0 0 2px; font-size: 13px; font-weight: 700; line-height: 1.35; }
    .st-row p { margin: 0; font-size: 12px; color: var(--ml-muted); line-height: 1.45; }
    .st-toggle {
        position: relative; display: inline-block; width: 46px; height: 26px; flex-shrink: 0;
    }
    .st-toggle input { opacity: 0; width: 0; height: 0; }
    .st-toggle .slider {
        position: absolute; inset: 0; cursor: pointer;
        background: #cbd5e1; border-radius: 999px; transition: background 0.2s ease;
    }
    .st-toggle .slider::before {
        content: ''; position: absolute; width: 20px; height: 20px;
        inset-inline-start: 3px; bottom: 3px; border-radius: 50%;
        background: #fff; box-shadow: 0 1px 3px rgba(26,34,56,0.2);
        transition: transform 0.2s ease;
    }
    .st-toggle input:checked + .slider { background: var(--ml-teal); }
    .st-toggle input:checked + .slider::before { transform: translateX(20px); }
    html[dir="rtl"] .st-toggle input:checked + .slider::before { transform: translateX(-20px); }
    .st-field { margin-bottom: 10px; }
    .st-field:last-child { margin-bottom: 0; }
    .st-field label {
        display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; color: var(--ml-muted);
    }
    .st-field select {
        width: 100%; min-height: 40px; padding: 0 12px;
        border-radius: 12px; border: 1px solid var(--ml-line);
        background: var(--ml-surface); color: var(--ml-ink);
        font-family: inherit; font-size: 13px;
    }
    .st-field select:focus {
        outline: none; border-color: rgba(73,164,162,0.55);
        box-shadow: 0 0 0 3px rgba(73,164,162,0.12);
    }
    .st-action {
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px;
        padding: 12px; border-radius: 12px; margin-bottom: 8px;
        border: 1px solid var(--ml-line); background: var(--ml-well);
    }
    .st-action:last-child { margin-bottom: 0; }
    .st-action.warn { background: rgba(245,158,11,0.1); border-color: rgba(245,158,11,0.28); }
    .st-action.danger { background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.25); }
    .st-action .left { display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1; }
    .st-action .left .ico {
        width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
    }
    .st-action.warn .ico { background: rgba(245,158,11,0.18); color: #92400e; }
    .st-action.danger .ico { background: rgba(239,68,68,0.14); color: #b91c1c; }
    .st-action h3 { margin: 0 0 2px; font-size: 13px; font-weight: 700; }
    .st-action p { margin: 0; font-size: 12px; color: var(--ml-muted); }
    .st-btn-warn {
        display: inline-flex; align-items: center; gap: 6px;
        min-height: 38px; padding: 0 14px; border-radius: 12px; border: 0;
        background: #d97706; color: #fff; font-size: 13px; font-weight: 700; cursor: pointer;
    }
    .st-btn-danger {
        display: inline-flex; align-items: center; gap: 6px;
        min-height: 38px; padding: 0 14px; border-radius: 12px; border: 0;
        background: #dc2626; color: #fff; font-size: 13px; font-weight: 700; cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="{{ __('student.settings_title') }}">
                <a href="{{ route('dashboard') }}">{{ __('student.learning_center') }}</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700">{{ __('student.settings_title') }}</span>
            </nav>
            <h1>{{ __('student.settings_title') }}</h1>
            <p class="sub">{{ __('student.settings_subtitle') }}</p>
        </div>
        <div class="oc-signals">
            <span class="oc-signal oc-signal-live">{{ strtoupper($currentLocale) }}</span>
        </div>
    </header>

    <section class="oc-stage">
        <div class="oc-eyebrow">{{ __('student.options') }}</div>
        <h2>{{ __('student.settings_title') }}</h2>
        <p class="oc-copy">{{ __('student.settings_coming_soon') }}</p>
        <div class="oc-nav">
            <a class="oc-btn oc-btn-quiet" href="{{ route('profile') }}">
                <i class="fas fa-user text-xs"></i> {{ __('student.go_to_profile') }}
            </a>
        </div>
    </section>

    <section class="oc-panel st-sec">
        <div class="st-sec-head">
            <div class="ico" aria-hidden="true"><i class="fas fa-bell"></i></div>
            <h2>{{ __('student.notification_settings') }}</h2>
        </div>
        <div class="st-row">
            <div class="min-w-0">
                <h3>{{ __('student.new_courses_notif') }}</h3>
                <p>{{ __('student.new_courses_notif_desc') }}</p>
            </div>
            <label class="st-toggle">
                <input type="checkbox" checked>
                <span class="slider"></span>
            </label>
        </div>
        <div class="st-row">
            <div class="min-w-0">
                <h3>{{ __('student.orders_notif') }}</h3>
                <p>{{ __('student.orders_notif_desc') }}</p>
            </div>
            <label class="st-toggle">
                <input type="checkbox" checked>
                <span class="slider"></span>
            </label>
        </div>
        <div class="st-row">
            <div class="min-w-0">
                <h3>{{ __('student.exams_notif') }}</h3>
                <p>{{ __('student.exams_notif_desc') }}</p>
            </div>
            <label class="st-toggle">
                <input type="checkbox" checked>
                <span class="slider"></span>
            </label>
        </div>
    </section>

    <section class="oc-panel st-sec">
        <div class="st-sec-head">
            <div class="ico" aria-hidden="true"><i class="fas fa-shield-halved"></i></div>
            <h2>{{ __('student.privacy_settings') }}</h2>
        </div>
        <div class="st-row">
            <div class="min-w-0">
                <h3>{{ __('student.show_progress_label') }}</h3>
                <p>{{ __('student.show_progress_desc') }}</p>
            </div>
            <label class="st-toggle">
                <input type="checkbox" checked>
                <span class="slider"></span>
            </label>
        </div>
        <div class="st-row">
            <div class="min-w-0">
                <h3>{{ __('student.show_activity_label') }}</h3>
                <p>{{ __('student.show_activity_desc') }}</p>
            </div>
            <label class="st-toggle">
                <input type="checkbox">
                <span class="slider"></span>
            </label>
        </div>
    </section>

    <section class="oc-panel st-sec">
        <div class="st-sec-head">
            <div class="ico" aria-hidden="true"><i class="fas fa-palette"></i></div>
            <h2>{{ __('student.display_settings') }}</h2>
        </div>
        <div class="st-field">
            <label for="st-theme">{{ __('student.theme_label') }}</label>
            <select id="st-theme">
                <option value="light" selected>{{ __('student.theme_light') }}</option>
                <option value="dark">{{ __('student.theme_dark') }}</option>
                <option value="auto">{{ __('student.theme_auto') }}</option>
            </select>
        </div>
        <div class="st-field">
            <label for="st-lang">{{ __('student.language_label') }}</label>
            <select id="st-lang">
                <option value="ar" @selected($currentLocale === 'ar')>العربية</option>
                <option value="en" @selected($currentLocale === 'en')>English</option>
            </select>
        </div>
    </section>

    <section class="oc-panel st-sec">
        <div class="st-sec-head">
            <div class="ico" aria-hidden="true"><i class="fas fa-user-cog"></i></div>
            <h2>{{ __('student.account_settings') }}</h2>
        </div>
        <div class="st-action warn">
            <div class="left">
                <div class="ico"><i class="fas fa-download"></i></div>
                <div class="min-w-0">
                    <h3>{{ __('student.download_data_label') }}</h3>
                    <p>{{ __('student.download_data_desc') }}</p>
                </div>
            </div>
            <button type="button" class="st-btn-warn">
                <i class="fas fa-download text-xs"></i> {{ __('student.download_btn') }}
            </button>
        </div>
        <div class="st-action danger">
            <div class="left">
                <div class="ico"><i class="fas fa-trash-alt"></i></div>
                <div class="min-w-0">
                    <h3>{{ __('student.delete_account_label') }}</h3>
                    <p>{{ __('student.delete_account_desc') }}</p>
                </div>
            </div>
            <button type="button" class="st-btn-danger" id="st-delete-account">
                <i class="fas fa-trash-alt text-xs"></i> {{ __('student.delete_account_btn') }}
            </button>
        </div>
    </section>

    <div style="display:flex;justify-content:flex-end;padding-top:4px">
        <button type="button" class="oc-btn" id="st-save">
            <i class="fas fa-save text-xs"></i> {{ __('student.save_all_settings') }}
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var lang = document.getElementById('st-lang');
    if (lang) {
        lang.addEventListener('change', function () {
            var url = new URL(window.location.href);
            url.searchParams.set('lang', this.value);
            window.location.href = url.toString();
        });
    }

    var del = document.getElementById('st-delete-account');
    if (del) {
        del.addEventListener('click', function () {
            confirm(@json(__('student.delete_account_confirm')));
        });
    }

    var save = document.getElementById('st-save');
    if (save) {
        save.addEventListener('click', function () {
            alert(@json(__('student.settings_coming_soon')));
        });
    }
});
</script>
@endpush
