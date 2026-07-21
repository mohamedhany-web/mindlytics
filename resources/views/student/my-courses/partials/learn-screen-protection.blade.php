{{--
  Screen-capture protection for the immersive learn page.
  Isolated from the video player — no Alpine / player hooks.
--}}
@php
    $lspUser = auth()->user();
    $lspName = $lspUser?->name ?: 'طالب';
    $lspBrand = config('app.name', 'Mindlytics');
@endphp

<div id="learn-screen-protection-root"
     hidden
     data-user-name="{{ $lspName }}"
     data-brand="{{ $lspBrand }}"
     data-course-id="{{ isset($course) ? $course->id : '' }}"></div>

<style>
    html.learn-precapture-flash::before,
    #learn-screen-protection-overlay {
        position: fixed;
        inset: 0;
        z-index: 2147483000;
        background: #000;
        pointer-events: none;
    }

    html.learn-precapture-flash::before {
        content: '';
        display: block;
    }

    #learn-screen-protection-overlay {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #f8fafc;
        opacity: 0;
        visibility: hidden;
        transition: none !important;
        font-family: 'Cairo', 'Inter', system-ui, sans-serif;
        text-align: center;
        padding: 24px;
    }

    #learn-screen-protection-overlay.is-visible {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    #learn-screen-protection-overlay .lsp-inner {
        max-width: 28rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
    }

    #learn-screen-protection-overlay .lsp-mark {
        width: 4.5rem;
        height: 4.5rem;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(37, 99, 235, 0.18);
        color: #60a5fa;
        font-size: 1.75rem;
        margin-bottom: 0.25rem;
    }

    #learn-screen-protection-overlay .lsp-brand {
        font-size: clamp(1.75rem, 4vw, 2.5rem);
        font-weight: 900;
        letter-spacing: 0.02em;
        color: #ef4444;
        text-shadow: 0 0 24px rgba(239, 68, 68, 0.35);
    }

    #learn-screen-protection-overlay .lsp-sub {
        font-size: 0.95rem;
        font-weight: 600;
        color: #cbd5e1;
        line-height: 1.6;
    }

    #learn-screen-protection-overlay .lsp-meta {
        margin-top: 0.75rem;
        font-size: 0.8rem;
        font-weight: 500;
        color: #94a3b8;
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        justify-content: center;
        direction: rtl;
    }

    #learn-screen-protection-overlay .lsp-sep {
        opacity: 0.5;
    }

    html.learn-screen-covered {
        overflow: hidden;
    }

    .learn-video-capture-shield {
        position: absolute;
        inset: 0;
        z-index: 42;
        pointer-events: none;
        overflow: hidden;
    }

    .learn-video-black-guard {
        position: absolute;
        inset: 0;
        background: #000;
        opacity: 0;
        transition: none !important;
        z-index: 2;
        pointer-events: none;
    }

    .learn-video-black-guard.is-active {
        opacity: 1;
    }
</style>

<script src="{{ asset('js/learn-screen-protection.js') }}?v={{ @filemtime(public_path('js/learn-screen-protection.js')) ?: '1' }}" defer></script>
