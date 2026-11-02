@php
    $publicLocale = app()->getLocale();
    $publicRtl = $publicLocale === 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ $publicLocale }}" dir="{{ $publicRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name') . ' - ' . __('landing.nav.brand'))</title>
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

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome - محسّن -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>

    <!-- أنماط قديمة لبعض الصفحات، وبعدها هوية الموقع وطبقة التوحيد (docs/SITE_DESIGN.md) -->
    @include('layouts.public-styles')
    @include('components.site.styles')
    @include('components.site.legacy-theme')
    @stack('styles')
</head>

<body class="h-site h-has-mobile-bar">
    <x-tracking-tags placement="body" />

    @include('components.site.header')

    <main class="h-legacy">
        @yield('content')
    </main>

    @include('components.site.footer')

    @stack('scripts')
</body>
</html>
