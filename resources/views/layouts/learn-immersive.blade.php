@php $learnLocale = app()->getLocale(); $learnRtl = $learnLocale === 'ar'; @endphp
<!DOCTYPE html>
<html lang="{{ $learnLocale }}" dir="{{ $learnRtl ? 'rtl' : 'ltr' }}" class="learn-rtl-root student-portal">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Mindlytics') }} - @yield('title', __('student.learn'))</title>
    @include('components.favicon-meta')
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('components.student.portal-theme')
    @stack('meta')
    @stack('styles')
    <style>html.learn-precapture-flash::before{content:'';position:fixed;inset:0;z-index:2147483000;background:#000;pointer-events:none;display:block;}.learn-immersive-body{font-family:var(--sp-font)!important;background:var(--sp-bg);}</style>
    <script>
    (function () {
        'use strict';
        /* Lightweight capture block only — full UI cover lives in learn-screen-protection.js.
           Do NOT flash black on window blur (iframe focus) — that caused learn-page tremble. */
        function block(e) {
            try { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); } catch (err) {}
            return false;
        }
        function isPrt(e) {
            return e.key === 'PrintScreen' || e.keyCode === 44 || e.code === 'PrintScreen';
        }
        function isMacDigit(e) {
            var key = e.key || '', code = e.code || '';
            return e.metaKey && e.shiftKey && (key === '3' || key === '4' || key === '5' || code === 'Digit3' || code === 'Digit4' || code === 'Digit5');
        }
        function isWinSnip(e) {
            var key = e.key || '', code = e.code || '';
            return e.shiftKey && (key === 'S' || key === 's' || code === 'KeyS') && e.getModifierState && e.getModifierState('OS');
        }
        function isCapture(e) {
            return isPrt(e) || isMacDigit(e) || isWinSnip(e);
        }
        document.addEventListener('keydown', function (e) {
            if (isCapture(e)) {
                if (window.__mindlyticsLearnProtection && typeof window.__mindlyticsLearnProtection.showCover === 'function') {
                    window.__mindlyticsLearnProtection.showCover(10000);
                } else {
                    document.documentElement.classList.add('learn-precapture-flash', 'learn-screen-covered');
                    clearTimeout(window.__lspFlashTimer);
                    window.__lspFlashTimer = setTimeout(function () {
                        document.documentElement.classList.remove('learn-precapture-flash', 'learn-screen-covered');
                    }, 8000);
                }
                block(e);
            }
        }, true);
        try {
            if (navigator.mediaDevices && navigator.mediaDevices.getDisplayMedia) {
                var patchedGdm = function () {
                    if (window.__mindlyticsLearnProtection && typeof window.__mindlyticsLearnProtection.showCover === 'function') {
                        window.__mindlyticsLearnProtection.showCover(12000);
                    }
                    return Promise.reject(new DOMException('Not allowed', 'NotAllowedError'));
                };
                patchedGdm.__mindlytics_lsp_v3__ = true;
                navigator.mediaDevices.getDisplayMedia = patchedGdm;
            }
        } catch (err) {}
    })();
    </script>
</head>
<body class="learn-immersive-body student-portal-body learn-rtl antialiased" dir="{{ $learnRtl ? 'rtl' : 'ltr' }}">
    @yield('content')
    @include('student.my-courses.partials.learn-screen-protection')
    @stack('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
</body>
</html>
