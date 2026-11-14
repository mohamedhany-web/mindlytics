{{-- Public-site alias of layouts.public — same Educo shell (BC for @extends public-site) --}}
@php
    $publicLocale = app()->getLocale();
    $publicRtl = $publicLocale === 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ $publicLocale }}" dir="{{ $publicRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title')@yield('title') - {{ config('app.name') }}@else{{ config('app.name') }} - {{ __('landing.nav.brand') }}@endif</title>

    @hasSection('meta_description')
        <meta name="description" content="@yield('meta_description')">
    @endif
    @hasSection('og_image')
        <meta property="og:type" content="@yield('og_type', 'website')">
        <meta property="og:url" content="@yield('og_url', url()->current())">
        <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title')))">
        <meta property="og:description" content="@yield('og_description', trim($__env->yieldContent('meta_description')))">
        <meta property="og:image" content="@yield('og_image')">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:site_name" content="Mindlytics">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="@yield('og_title', trim($__env->yieldContent('title')))">
        <meta name="twitter:description" content="@yield('og_description', trim($__env->yieldContent('meta_description')))">
        <meta name="twitter:image" content="@yield('og_image')">
    @endif
    @stack('meta')

    @include('components.favicon-meta')
    <x-tracking-tags placement="head" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap"></noscript>

    <script src="https://cdn.tailwindcss.com"></script>
    @stack('head_early')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>

    @include('components.landing.theme')
    <style>
        .lp-site-body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--lp-cream, #eef2f9);
            color: var(--lp-primary);
            font-family: 'Cairo', 'Tajawal', sans-serif;
            overflow-x: hidden;
        }
        .lp-site-main {
            flex: 1 0 auto;
            width: 100%;
        }
        [x-cloak] { display: none !important; }
    </style>
    {{-- Page-level CSS: prefer @push('head') or @push('styles') --}}
    @stack('head')
    @stack('styles')
</head>
<body class="lp-site-body" @yield('body_attrs')>
    <x-tracking-tags placement="body" />

    @include('components.landing.header')

    <main class="lp-site-main">
        @yield('content')
    </main>

    @include('components.landing.footer')

    <script>
        (function () {
            var nodes = document.querySelectorAll('.lp-reveal');
            if (!nodes.length) return;
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                nodes.forEach(function (el) { el.classList.add('is-in'); });
                return;
            }
            if (!('IntersectionObserver' in window)) {
                nodes.forEach(function (el) { el.classList.add('is-in'); });
                return;
            }
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add('is-in');
                    io.unobserve(entry.target);
                });
            }, { threshold: 0.16, rootMargin: '0px 0px -8% 0px' });
            nodes.forEach(function (el) { io.observe(el); });
            requestAnimationFrame(function () {
                document.querySelectorAll('.lp-cs-hero .lp-reveal, .lp-hero .lp-reveal, .cx-stage .lp-reveal').forEach(function (el) {
                    el.classList.add('is-in');
                });
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
