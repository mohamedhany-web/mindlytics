@php $learnLocale = app()->getLocale(); @endphp
<!DOCTYPE html>
<html lang="{{ $learnLocale }}" dir="rtl" class="learn-rtl-root">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Mindlytics') }} - @yield('title', __('student.learn'))</title>
    @include('components.favicon-meta')
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('meta')
    @stack('styles')
    <style>html.learn-precapture-flash::before{content:'';position:fixed;inset:0;z-index:2147483000;background:#000;pointer-events:none;display:block;}</style>
    <script>
    (function () {
        'use strict';
        var suspectUntil = 0;
        function now() { return Date.now(); }
        function markSuspect(ms) { suspectUntil = Math.max(suspectUntil, now() + (ms || 12000)); }
        function isSuspect() { return now() < suspectUntil; }
        function flash(ms) {
            document.documentElement.classList.add('learn-precapture-flash', 'learn-screen-covered');
            clearTimeout(window.__lspFlashTimer);
            window.__lspFlashTimer = setTimeout(function () {
                document.documentElement.classList.remove('learn-precapture-flash', 'learn-screen-covered');
            }, ms || 8000);
        }
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
        function onCapture(e) {
            markSuspect(12000);
            flash(10000);
            block(e);
        }
        document.addEventListener('keydown', function (e) {
            if (isCapture(e)) { onCapture(e); return; }
            if (e.metaKey && e.shiftKey) { markSuspect(12000); }
            if (isPrt(e)) { onCapture(e); }
        }, true);
        document.addEventListener('keyup', function (e) {
            if (isPrt(e) || isMacDigit(e)) { onCapture(e); }
        }, true);
        window.addEventListener('blur', function () { if (isSuspect()) { flash(8000); } });
        document.addEventListener('visibilitychange', function () {
            if (document.hidden && isSuspect()) { flash(8000); }
        });
        try {
            if (navigator.mediaDevices && navigator.mediaDevices.getDisplayMedia) {
                var patchedGdm = function () {
                    markSuspect(15000);
                    flash(12000);
                    return Promise.reject(new DOMException('Not allowed', 'NotAllowedError'));
                };
                patchedGdm.__mindlytics_lsp_v3__ = true;
                navigator.mediaDevices.getDisplayMedia = patchedGdm;
                setInterval(function () {
                    if (!navigator.mediaDevices.getDisplayMedia || !navigator.mediaDevices.getDisplayMedia.__mindlytics_lsp_v3__) {
                        navigator.mediaDevices.getDisplayMedia = patchedGdm;
                    }
                }, 1500);
            }
        } catch (err) {}
    })();
    </script>
</head>
<body class="learn-immersive-body learn-rtl antialiased" dir="rtl">
    @yield('content')
    @include('student.my-courses.partials.learn-screen-protection')
    @stack('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
</body>
</html>
