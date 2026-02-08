<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Mindlytics - أكاديمية البرمجة')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo-removebg-preview.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo-removebg-preview.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('logo-removebg-preview.png') }}">

    <!-- الخطوط العربية - محسّن للأداء -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Resource Hints للأداء -->
    <link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome - محسّن -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>

    <!-- Custom Styles from welcome.blade.php -->
    @include('layouts.public-styles')
</head>

<body class="bg-gray-50 text-gray-900"
      x-data="{ mobileMenu: false, searchQuery: '' }"
      :class="{ 'overflow-hidden': mobileMenu }">
    
    <!-- Navigation Header - نفس الناف بار من الصفحة الرئيسية -->
    @include('components.unified-navbar')

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer - نفس فوتر الصفحة الرئيسية -->
    @include('components.unified-footer')

    @stack('scripts')

    <script>
        // تأثير الناف بار عند السكرول - محسّن للأداء
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.getElementById('navbar');
            if (navbar) {
                let ticking = false;
                let isScrolled = false;
                
                function handleScroll() {
                    if (!ticking) {
                        window.requestAnimationFrame(() => {
                            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
                            const shouldBeScrolled = currentScroll > 100;
                            
                            if (shouldBeScrolled !== isScrolled) {
                                if (shouldBeScrolled) {
                                    navbar.classList.add('scrolled');
                                } else {
                                    navbar.classList.remove('scrolled');
                                }
                                isScrolled = shouldBeScrolled;
                            }
                            
                            ticking = false;
                        });
                        ticking = true;
                    }
                }
                
                window.addEventListener('scroll', handleScroll, { passive: true });
            }
        });
    </script>
</body>
</html>

