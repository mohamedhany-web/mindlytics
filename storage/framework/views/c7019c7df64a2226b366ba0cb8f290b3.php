<!DOCTYPE html>
<html lang="ar" dir="rtl" itemscope itemtype="https://schema.org/EducationalOrganization">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        
        <!-- Primary Meta Tags -->
        <title>Mindlytics - أكاديمية البرمجة والذكاء الاصطناعي | تعلم البرمجة من الصفر</title>
        <meta name="title" content="Mindlytics - أكاديمية البرمجة والذكاء الاصطناعي | تعلم البرمجة من الصفر">
        <meta name="description" content="تعلم البرمجة والذكاء الاصطناعي من الصفر إلى الاحتراف مع Mindlytics. كورسات برمجة عربية شاملة، دروس تفاعلية، مشاريع عملية، وشهادات معتمدة. انضم إلى آلاف الطلاب الناجحين.">
        <meta name="keywords" content="برمجة, ذكاء اصطناعي, تعلم البرمجة, كورسات برمجة, دورات برمجة, برمجة عربية, Python, JavaScript, Laravel, React, تعلم البرمجة من الصفر, أكاديمية برمجة, منصة تعليمية">
        <meta name="author" content="Mindlytics">
        <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
        <meta name="language" content="Arabic">
        <meta name="revisit-after" content="7 days">
        <meta name="rating" content="general">
        <meta name="distribution" content="global">
        <meta name="geo.region" content="EG">
        <meta name="geo.placename" content="Egypt">
        
        <!-- Canonical URL -->
        <link rel="canonical" href="<?php echo e(url('/')); ?>">
        
        <!-- Language and Region -->
        <link rel="alternate" hreflang="ar" href="<?php echo e(url('/')); ?>">
        <link rel="alternate" hreflang="x-default" href="<?php echo e(url('/')); ?>">
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="<?php echo e(url('/')); ?>">
        <meta property="og:title" content="Mindlytics - أكاديمية البرمجة والذكاء الاصطناعي">
        <meta property="og:description" content="تعلم البرمجة والذكاء الاصطناعي من الصفر إلى الاحتراف مع Mindlytics. كورسات برمجة عربية شاملة، دروس تفاعلية، مشاريع عملية، وشهادات معتمدة.">
        <meta property="og:image" content="<?php echo e(asset('images/og-image.jpg')); ?>">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="Mindlytics - أكاديمية البرمجة والذكاء الاصطناعي">
        <meta property="og:locale" content="ar_AR">
        <meta property="og:site_name" content="Mindlytics">
        
        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="<?php echo e(url('/')); ?>">
        <meta name="twitter:title" content="Mindlytics - أكاديمية البرمجة والذكاء الاصطناعي">
        <meta name="twitter:description" content="تعلم البرمجة والذكاء الاصطناعي من الصفر إلى الاحتراف مع Mindlytics. كورسات برمجة عربية شاملة.">
        <meta name="twitter:image" content="<?php echo e(asset('images/og-image.jpg')); ?>">
        <meta name="twitter:image:alt" content="Mindlytics - أكاديمية البرمجة والذكاء الاصطناعي">
        
        <!-- Additional SEO Meta Tags -->
        <meta name="theme-color" content="#0ea5e9">
        <meta name="msapplication-TileColor" content="#0ea5e9">
        <meta name="application-name" content="Mindlytics">
        
        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('logo-removebg-preview.png')); ?>">
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(asset('logo-removebg-preview.png')); ?>">
        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('logo-removebg-preview.png')); ?>">
        
        <!-- Preconnect for Performance -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
        <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
        <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
        
        <!-- Structured Data (JSON-LD) -->
        <?php
        $schema1 = [
            "@context" => "https://schema.org",
            "@type" => "EducationalOrganization",
            "name" => "Mindlytics",
            "alternateName" => "Mindlytics Academy",
            "url" => url('/'),
            "logo" => asset('images/logo.png'),
            "description" => "أكاديمية برمجة عربية متخصصة في تعليم البرمجة والذكاء الاصطناعي من الصفر إلى الاحتراف",
            "address" => [
                "@type" => "PostalAddress",
                "addressCountry" => "EG",
                "addressLocality" => "Egypt"
            ],
            "contactPoint" => [
                "@type" => "ContactPoint",
                "contactType" => "Customer Service",
                "email" => "info@mindlytics-academy.com",
                "availableLanguage" => "Arabic"
            ],
            "sameAs" => [
                "https://www.facebook.com/mindlytics",
                "https://www.twitter.com/mindlytics",
                "https://www.linkedin.com/company/mindlytics",
                "https://www.youtube.com/@mindlytics"
            ],
            "offers" => [
                "@type" => "Offer",
                "category" => "Online Courses",
                "availability" => "https://schema.org/InStock"
            ]
        ];
        $schema2 = [
            "@context" => "https://schema.org",
            "@type" => "WebSite",
            "name" => "Mindlytics",
            "url" => url('/'),
            "potentialAction" => [
                "@type" => "SearchAction",
                "target" => [
                    "@type" => "EntryPoint",
                    "urlTemplate" => url('/courses?search={search_term_string}')
                ],
                "query-input" => "required name=search_term_string"
            ]
        ];
        $schema3 = [
            "@context" => "https://schema.org",
            "@type" => "Organization",
            "name" => "Mindlytics",
            "url" => url('/'),
            "logo" => asset('images/logo.png'),
            "description" => "أكاديمية برمجة عربية متخصصة في تعليم البرمجة والذكاء الاصطناعي",
            "foundingDate" => "2024",
            "founders" => [[
                "@type" => "Person",
                "name" => "Mindlytics Team"
            ]],
            "contactPoint" => [
                "@type" => "ContactPoint",
                "telephone" => "+20-XXX-XXX-XXXX",
                "email" => "info@mindlytics-academy.com",
                "contactType" => "Customer Service",
                "areaServed" => "EG",
                "availableLanguage" => ["Arabic"]
            ]
        ];
        ?>
        <script type="application/ld+json">
        <?php echo json_encode($schema1, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>

        </script>
        
        <script type="application/ld+json">
        <?php echo json_encode($schema2, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>

        </script>
        
        <script type="application/ld+json">
        <?php echo json_encode($schema3, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>

        </script>

    <!-- خط عربي أصيل - محسّن للأداء -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    
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

            <style>
        /* Alpine.js x-cloak */
        [x-cloak] {
            display: none !important;
        }
        
        * {
            font-family: 'Tajawal', 'Cairo', 'Noto Sans Arabic', sans-serif;
        }

        /* إصلاح التمرير - الصفحة الرئيسية قابلة للتمرير افتراضياً */
        html {
            overflow-x: hidden !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
            position: relative !important;
            width: 100% !important;
            height: auto !important;
            min-height: 100% !important;
            max-height: none !important;
        }

        body {
            overflow-x: hidden !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
            background: #f8fafc;
            width: 100% !important;
            max-width: 100vw !important;
            position: relative !important;
            min-height: 100vh !important;
            min-height: -webkit-fill-available !important;
            height: auto !important;
            max-height: none !important;
            display: flex !important;
            flex-direction: column !important;
            padding-top: 0 !important;
            margin-top: 0 !important;
            overscroll-behavior-x: none;
            overscroll-behavior-y: auto;
        }
        
        /* منع التمرير فقط عند فتح القائمة المتنقلة (يُضاف من النافبار) */
        body.overflow-hidden {
            overflow: hidden !important;
            position: fixed !important;
            width: 100% !important;
            height: 100% !important;
        }
        
        /* إجبار تفعيل التمرير - حل قوي جداً */
        html, body {
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }
        
        /* التأكد من أن body ليس fixed إلا عند فتح القائمة */
        body:not(.overflow-hidden) {
            position: relative !important;
            overflow: auto !important;
            overflow-y: auto !important;
            height: auto !important;
            max-height: none !important;
        }
        
        /* حل إضافي - إجبار التمرير على جميع العناصر */
        * {
            -webkit-overflow-scrolling: touch;
        }
        
        body > * {
            flex-shrink: 0;
        }
        
        .content-wrapper {
            flex: 1 0 auto;
            position: relative;
            width: 100%;
        }

        /* Enhanced Background with Subtle Animations - محسّن للأداء */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            width: 100vw;
            height: 100%;
            height: 100vh;
            height: -webkit-fill-available;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #ffffff 100%);
            max-width: 100vw;
            /* تحسين الأداء */
            will-change: auto;
            transform: translateZ(0);
            contain: layout style paint;
        }

        /* Subtle Floating Shapes - محسّن للأداء */
        .bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.03;
            /* تقليل الأنيميشن على الموبايل */
            animation: floatShape 30s infinite ease-in-out;
            will-change: transform;
            transform: translateZ(0);
        }

        @keyframes floatShape {
            0%, 100% {
                transform: translate3d(0, 0, 0) scale(1);
            }
            50% {
                transform: translate3d(100px, -100px, 0) scale(1.2);
            }
        }

        .bg-shape-1 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, #3b82f6, transparent);
            top: 10%;
            left: 10%;
            animation-delay: 0s;
}

        .bg-shape-2 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, #10b981, transparent);
            top: 60%;
            right: 10%;
            animation-delay: 10s;
        }

        .bg-shape-3 {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, #8b5cf6, transparent);
            bottom: 20%;
            left: 50%;
            animation-delay: 20s;
        }
        
        /* تعطيل الأنيميشن على الموبايل للأداء */
        @media (max-width: 768px) {
            .bg-shape {
                animation: none !important;
                opacity: 0.01;
            }
        }

        /* Enhanced Navbar */
        .navbar-gradient {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 45%, #1d4ed8 100%);
            box-shadow: 0 1px 0 rgba(255, 255, 255, 0.08);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1000;
            transition: box-shadow 0.25s ease, background 0.25s ease;
            backdrop-filter: blur(12px) saturate(140%);
            -webkit-backdrop-filter: blur(12px) saturate(140%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            max-width: 100vw;
            overflow-x: hidden;
            transform: translateZ(0);
        }
        @media (max-width: 768px) {
            .navbar-gradient {
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
            }
        }
        
        /* Mobile Menu Sidebar Styles */
        @media (max-width: 1023px) {
            /* Ensure mobile menu covers full screen */
            .mobile-menu-overlay {
                position: fixed !important;
                inset: 0 !important;
                z-index: 9999 !important;
            }
            
            .mobile-menu-sidebar {
                position: fixed !important;
                top: 0 !important;
                right: 0 !important;
                height: 100vh !important;
                height: 100dvh !important;
                z-index: 10000 !important;
            }
        }
        
        .nav-link {
            position: relative;
            display: inline-block;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .navbar-gradient.scrolled {
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(255, 255, 255, 0.06);
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.97) 0%, rgba(30, 58, 138, 0.98) 50%, rgba(29, 78, 216, 0.97) 100%);
            backdrop-filter: blur(16px) saturate(150%);
            -webkit-backdrop-filter: blur(16px) saturate(150%);
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }
        .nav-modern .nav-link-modern:hover {
            background: rgba(255, 255, 255, 0.12);
        }
        .nav-cta-btn:hover {
            transform: translateY(-1px);
        }
        
        /* Enhanced Search Bar Styles */
        .search-bar-wrapper input:focus {
            width: 100%;
        }
        
        .search-bar-wrapper input:focus ~ button,
        .search-bar-wrapper:focus-within button {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }

        body {
            padding-top: 0 !important;
            margin-top: 0 !important;
            overflow-x: hidden;
            /* Prevent layout shift */
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        html {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            /* Prevent horizontal scroll */
            width: 100%;
            max-width: 100vw;
        }
        
        * {
            box-sizing: border-box;
        }

        /* Prevent horizontal scroll on all elements */
        * {
            max-width: 100%;
        }

        img, video, iframe {
            max-width: 100%;
            height: auto;
        }

        /* Mobile Only Adjustments - لا تؤثر على الكومبيوتر */
        @media (max-width: 1023px) {
            html, body {
                position: relative;
                overflow-x: hidden;
                width: 100%;
                max-width: 100vw;
                /* Prevent horizontal scroll */
                overscroll-behavior-x: none;
            }

            body {
                padding-top: 0;
                /* Fix for iOS Safari address bar */
                min-height: -webkit-fill-available;
            }

            .hero-glow {
                display: none;
            }

            .bg-shape-1, .bg-shape-2, .bg-shape-3 {
                display: none;
            }

            /* Hero adjustments for mobile */
            .hero-section {
                padding-top: 2.5rem;
                padding-bottom: 2rem;
                padding-left: 1rem;
                padding-right: 1rem;
                /* Fix overflow issues */
                overflow-x: hidden !important;
                max-width: 100vw !important;
                width: 100% !important;
                /* Prevent layout shift */
                contain: layout style paint;
            }
            
            /* Fix content inside hero */
            .hero-section > * {
                max-width: 100%;
                overflow-x: hidden;
            }
            
            /* Fix max-w-7xl on mobile */
            .hero-section .max-w-7xl {
                max-width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            /* Fix for all sections on mobile */
            section {
                max-width: 100vw;
                overflow-x: hidden;
            }
        }

            .hero-section h1 {
                font-size: 3rem !important;
                line-height: 1.2 !important;
            }

            .hero-section p {
                font-size: 1.5rem !important;
                line-height: 1.5 !important;
            }

            /* Stats adjustments */
            .stat-card {
                padding: 1.25rem !important;
            }

            .stat-card .text-3xl,
            .stat-card .text-4xl {
                font-size: 2.25rem !important;
            }
            
            .btn-primary, .btn-secondary {
                font-size: 1.125rem !important;
                padding: 1rem 2rem !important;
            }
        }

        @media (max-width: 640px) {
            body {
                padding-top: 60px;
            }

            .navbar-gradient {
                min-height: 60px;
            }
            
            .navbar-gradient h1 {
                font-size: 1rem !important;
            }
            
            .navbar-gradient p {
                font-size: 0.65rem !important;
            }
            
            .navbar-gradient .w-10 {
                width: 2.5rem !important;
                height: 2.5rem !important;
            }
            
            .navbar-gradient .text-lg {
                font-size: 1rem !important;
            }

            .hero-section {
                padding-top: 2rem;
                padding-bottom: 1.5rem;
                padding-left: 0.75rem;
                padding-right: 0.75rem;
        }

            .hero-section h1 {
                font-size: 2.75rem !important;
                line-height: 1.2 !important;
            }

            .hero-section p {
                font-size: 1.375rem !important;
                line-height: 1.5 !important;
            }

            .stat-card .text-3xl,
            .stat-card .text-4xl {
                font-size: 2rem !important;
            }
            
            .stat-card {
                padding: 1.25rem !important;
            }

            /* Buttons full width on mobile */
            .btn-primary, .btn-secondary {
            width: 100%;
                font-size: 1rem !important;
                padding: 1rem 1.5rem !important;
            }
        }

        @media (max-width: 480px) {
            body {
                padding-top: 55px;
        }

            .navbar-gradient {
                min-height: 55px;
            }

            .hero-section h1 {
                font-size: 2.5rem !important;
                line-height: 1.2 !important;
            }

            .hero-section p {
                font-size: 1.25rem !important;
                line-height: 1.5 !important;
            }

            .stat-card {
                padding: 1rem !important;
            }
            
            .stat-card .text-3xl,
            .stat-card .text-4xl {
                font-size: 1.75rem !important;
            }
            
            .btn-primary, .btn-secondary {
                font-size: 1rem !important;
                padding: 0.875rem 1.5rem !important;
            }
        }

        @media (min-width: 640px) and (max-width: 1024px) {
            .hero-section {
                padding-top: 4rem !important;
                padding-bottom: 2rem !important;
                padding-left: 1.5rem !important;
                padding-right: 1.5rem !important;
            }

            .stat-card {
                padding: 1.25rem !important;
            }
        }


        /* Enhanced Hero Section */
        .hero-section {
            background: linear-gradient(to bottom, #f0f9ff, #e0f2fe, #ffffff);
            position: relative;
            overflow: hidden;
            overflow-x: hidden;
            padding-top: 4rem;
            padding-bottom: 3rem;
            margin-top: 0;
            width: 100%;
            max-width: 100vw;
            /* Prevent layout shift */
            contain: layout style paint;
            /* Optimize rendering */
            will-change: auto;
            transform: translateZ(0);
        }

        @media (min-width: 768px) {
            .hero-section {
                padding-top: 5rem;
                padding-bottom: 4rem;
            }
        }
        
        @media (min-width: 1024px) {
            .hero-section {
                padding-top: 6rem;
                padding-bottom: 5rem;
        }
        }
        
        @media (min-width: 1280px) {
            .hero-section {
                padding-top: 7rem;
                padding-bottom: 6rem;
            }
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(59, 130, 246, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(16, 185, 129, 0.06) 0%, transparent 50%);
            pointer-events: none;
            animation: pulseGradient 5s ease-in-out infinite;
        }

        @keyframes pulseGradient {
            0%, 100% {
            opacity: 1;
                transform: scale(1);
            }
            50% { 
                opacity: 0.9;
                transform: scale(1.1);
            }
        }

        .hero-glow {
            position: absolute;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.2), transparent);
            border-radius: 50%;
            filter: blur(100px);
            top: -400px;
            right: -400px;
            animation: floatGlow 10s ease-in-out infinite;
        }

        @keyframes floatGlow {
            0%, 100% {
                transform: translate(0, 0) scale(1);
                opacity: 0.8;
            }
            33% {
                transform: translate(-100px, 100px) scale(1.3);
                opacity: 1;
            }
            66% {
                transform: translate(80px, -80px) scale(0.9);
                opacity: 0.7;
            }
        }

        /* Animated Background Elements */
        .animated-background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            overflow: hidden;
            overflow-x: hidden;
            z-index: 0;
            width: 100%;
            max-width: 100%;
            /* Prevent causing reflow */
            contain: layout style paint;
            will-change: auto;
            transform: translateZ(0);
        }

        /* Floating Circles - محسّن للأداء */
        .floating-circle {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.25), rgba(59, 130, 246, 0.08), transparent);
            filter: blur(4px);
            animation: floatCircle 14s ease-in-out infinite;
            /* تحسين الأداء */
            will-change: transform;
            transform: translate3d(0, 0, 0);
            contain: layout style paint;
            /* Contain within bounds */
            max-width: 100%;
            max-height: 100%;
        }
        
        /* Disable animations on mobile to prevent jitter */
        @media (max-width: 1023px) {
            .hero-section::before {
                animation: none !important;
            }
            
            .floating-circle,
            .floating-code-symbol,
            .floating-line,
            .floating-particle {
                animation: none !important;
                will-change: auto !important;
                transform: none !important;
                /* Keep them visible but static */
                opacity: 0.3 !important;
            }
            
            /* Hide some elements on very small screens */
            @media (max-width: 640px) {
                .floating-code-symbol,
                .floating-line {
                    display: none !important;
                }
                
                .floating-circle,
                .floating-particle {
                    opacity: 0.15 !important;
                }
            }
        }

        .circle-1 {
            width: 300px;
            height: 300px;
            top: 10%;
            left: 5%;
            animation-delay: 0s;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.3), rgba(59, 130, 246, 0.1), transparent);
        }

        .circle-2 {
            width: 240px;
            height: 240px;
            top: 60%;
            right: 10%;
            animation-delay: 1.5s;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.3), rgba(16, 185, 129, 0.1), transparent);
        }

        .circle-3 {
            width: 200px;
            height: 200px;
            top: 30%;
            right: 30%;
            animation-delay: 3s;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.25), rgba(59, 130, 246, 0.08), transparent);
        }

        .circle-4 {
            width: 280px;
            height: 280px;
            bottom: 15%;
            left: 15%;
            animation-delay: 4.5s;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.28), rgba(16, 185, 129, 0.1), transparent);
        }

        .circle-5 {
            width: 180px;
            height: 180px;
            top: 50%;
            left: 50%;
            animation-delay: 6s;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.22), rgba(59, 130, 246, 0.08), transparent);
        }

        @keyframes floatCircle {
            0%, 100% {
                transform: translate(0, 0) scale(1) rotate(0deg);
                opacity: 0.7;
            }
            20% {
                transform: translate(100px, -100px) scale(1.4) rotate(10deg);
                opacity: 0.9;
        }
            40% {
                transform: translate(-80px, 80px) scale(0.75) rotate(-10deg);
                opacity: 0.8;
            }
            60% {
                transform: translate(70px, 70px) scale(1.3) rotate(5deg);
                opacity: 0.95;
            }
            80% {
                transform: translate(-50px, -50px) scale(0.9) rotate(-5deg);
                opacity: 0.85;
        }
        }

        /* Floating Code Symbols */
        .floating-code-symbol {
            position: absolute;
            font-family: 'Courier New', 'Monaco', 'Consolas', monospace;
            font-weight: normal;
            font-size: 1.2rem;
            color: rgba(59, 130, 246, 0.08);
            opacity: 0.08;
            animation: floatCodeSymbol 15s ease-in-out infinite;
            text-shadow: none;
            user-select: none;
            pointer-events: none;
            z-index: 0;
            /* Prevent causing reflow */
            will-change: transform;
            transform: translateZ(0);
            /* Contain within bounds */
            white-space: nowrap;
            max-width: 100%;
        }

        .code-symbol-1 {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
            color: rgba(59, 130, 246, 0.06);
        }

        .code-symbol-2 {
            top: 70%;
            right: 20%;
            animation-delay: 2s;
            color: rgba(16, 185, 129, 0.06);
        }

        .code-symbol-3 {
            top: 40%;
            right: 15%;
            animation-delay: 4s;
            color: rgba(59, 130, 246, 0.05);
        }

        .code-symbol-4 {
            bottom: 25%;
            left: 25%;
            animation-delay: 6s;
            color: rgba(16, 185, 129, 0.05);
        }

        .code-symbol-5 {
            top: 15%;
            right: 40%;
            animation-delay: 8s;
            color: rgba(59, 130, 246, 0.06);
        }

        .code-symbol-6 {
            top: 55%;
            left: 50%;
            animation-delay: 1s;
            color: rgba(16, 185, 129, 0.06);
        }

        .code-symbol-7 {
            bottom: 40%;
            right: 30%;
            animation-delay: 3s;
            color: rgba(59, 130, 246, 0.05);
            font-size: 1rem;
        }

        .code-symbol-8 {
            top: 35%;
            left: 30%;
            animation-delay: 5s;
            color: rgba(16, 185, 129, 0.06);
        }

        .code-symbol-9 {
            top: 60%;
            left: 40%;
            animation-delay: 7s;
            color: rgba(59, 130, 246, 0.05);
            font-size: 0.9rem;
        }

        .code-symbol-10 {
            bottom: 35%;
            right: 25%;
            animation-delay: 9s;
            color: rgba(16, 185, 129, 0.05);
            font-size: 0.9rem;
        }

        .code-symbol-11 {
            top: 25%;
            right: 35%;
            animation-delay: 11s;
            color: rgba(59, 130, 246, 0.04);
            font-size: 0.85rem;
        }

        .code-symbol-12 {
            bottom: 20%;
            left: 40%;
            animation-delay: 13s;
            color: rgba(16, 185, 129, 0.04);
            font-size: 0.85rem;
        }

        @keyframes floatCodeSymbol {
            0%, 100% {
                transform: translate3d(0, 0, 0) scale(1);
                opacity: 0.08;
            }
            25% { 
                transform: translate3d(60px, -60px, 0) scale(1.02);
                opacity: 0.1;
            }
            50% {
                transform: translate3d(-40px, 40px, 0) scale(0.98);
                opacity: 0.09;
            }
            75% { 
                transform: translate3d(30px, -30px, 0) scale(1.01);
                opacity: 0.095;
            }
        }

        @media (max-width: 1024px) {
            .floating-code-symbol {
                font-size: 1rem;
                opacity: 0.06;
            }
        }

        @media (max-width: 768px) {
            .floating-code-symbol {
                font-size: 0.85rem;
                opacity: 0.05;
            }
        }

        /* Floating Lines */
        .floating-line {
            position: absolute;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.4), rgba(16, 185, 129, 0.3), rgba(59, 130, 246, 0.4), transparent);
            height: 3px;
            animation: floatLine 20s linear infinite;
        }

        .line-1 {
            width: 300px;
            top: 25%;
            left: 0;
            transform: rotate(45deg);
            animation-delay: 0s;
        }

        .line-2 {
            width: 250px;
            top: 65%;
            right: 0;
            transform: rotate(-45deg);
            animation-delay: 5s;
            background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.3), transparent);
        }

        .line-3 {
            width: 200px;
            top: 45%;
            left: 50%;
            transform: rotate(90deg);
            animation-delay: 10s;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.4), transparent);
        }

        @keyframes floatLine {
            0% {
                transform: translate3d(-100%, 0, 0);
                opacity: 0;
            }
            10% {
                opacity: 0.8;
            }
            90% {
                opacity: 0.8;
            }
            100% {
                transform: translate3d(200%, 150px, 0);
                opacity: 0;
            }
        }

        /* Floating Particles */
        .floating-particle {
            position: absolute;
            width: 12px;
            height: 12px;
            background: rgba(59, 130, 246, 0.7);
            border-radius: 50%;
            animation: floatParticle 12s ease-in-out infinite;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.7), 0 0 40px rgba(59, 130, 246, 0.35);
            /* Prevent causing reflow */
            will-change: transform;
            transform: translateZ(0);
            /* Contain within bounds */
            max-width: 100%;
            max-height: 100%;
        }

        .particle-1 {
            top: 10%;
            left: 20%;
            animation-delay: 0s;
            background: rgba(59, 130, 246, 0.7);
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.6), 0 0 30px rgba(59, 130, 246, 0.3);
            }

        .particle-2 {
            top: 30%;
            right: 25%;
            animation-delay: 1s;
            background: rgba(16, 185, 129, 0.7);
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.6), 0 0 30px rgba(16, 185, 129, 0.3);
        }

        .particle-3 {
            top: 50%;
            left: 10%;
            animation-delay: 2s;
            background: rgba(59, 130, 246, 0.7);
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.6), 0 0 30px rgba(59, 130, 246, 0.3);
        }

        .particle-4 {
            bottom: 30%;
            right: 15%;
            animation-delay: 3s;
            background: rgba(16, 185, 129, 0.7);
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.6), 0 0 30px rgba(16, 185, 129, 0.3);
        }

        .particle-5 {
            top: 70%;
            left: 40%;
            animation-delay: 4s;
            background: rgba(59, 130, 246, 0.65);
            box-shadow: 0 0 12px rgba(59, 130, 246, 0.5), 0 0 25px rgba(59, 130, 246, 0.25);
        }

        .particle-6 {
            top: 25%;
            right: 50%;
            animation-delay: 5s;
            background: rgba(16, 185, 129, 0.7);
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.6), 0 0 30px rgba(16, 185, 129, 0.3);
        }

        .particle-7 {
            bottom: 20%;
            left: 30%;
            animation-delay: 6s;
            background: rgba(16, 185, 129, 0.65);
            box-shadow: 0 0 12px rgba(16, 185, 129, 0.5), 0 0 25px rgba(16, 185, 129, 0.25);
        }

        .particle-8 {
            top: 80%;
            right: 30%;
            animation-delay: 7s;
            background: rgba(59, 130, 246, 0.7);
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.6), 0 0 30px rgba(59, 130, 246, 0.3);
        }

        @keyframes floatParticle {
            0%, 100% {
                transform: translate3d(0, 0, 0) scale(1);
                opacity: 0.7;
            }
            20% {
                transform: translate3d(120px, -120px, 0) scale(2.2);
                opacity: 1;
            }
            40% {
                transform: translate3d(-70px, 70px, 0) scale(0.6);
                opacity: 0.5;
        }
            60% {
                transform: translate3d(80px, 80px, 0) scale(1.8);
                opacity: 0.95;
            }
            80% {
                transform: translate3d(-50px, -50px, 0) scale(1.2);
                opacity: 0.8;
            }
        }

        /* Mobile Optimizations */
        @media (max-width: 768px) {
            .floating-circle {
                width: 150px !important;
                height: 150px !important;
                filter: blur(2px);
                animation-duration: 16s;
            }
            
            .circle-1, .circle-4 {
                width: 180px !important;
                height: 180px !important;
            }
            
            .circle-2, .circle-3, .circle-5 {
                width: 120px !important;
                height: 120px !important;
        }

            .floating-line {
                height: 2px;
                width: 150px !important;
                animation-duration: 20s;
            }
            
            .floating-particle {
                width: 8px;
                height: 8px;
                animation-duration: 12s;
            }
            
            .floating-code-symbol {
                font-size: 0.75rem;
                opacity: 0.04;
            }
        }

        @media (max-width: 480px) {
            .floating-circle {
                width: 100px !important;
                height: 100px !important;
            }
            
            .circle-1, .circle-4 {
                width: 140px !important;
                height: 140px !important;
        }

            .floating-line {
                display: none;
            }
            
            .floating-particle {
                width: 6px;
                height: 6px;
        }
        }

        /* Enhanced Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .btn-primary:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(30, 58, 138, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-secondary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .btn-secondary:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
        }

        /* Enhanced Stat Cards */
        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-radius: 16px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(59, 130, 246, 0.1);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 3px;
            height: 100%;
            background: linear-gradient(180deg, #3b82f6, #10b981);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card:hover::after {
            opacity: 1;
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(59, 130, 246, 0.2);
            border-color: rgba(59, 130, 246, 0.3);
        }

        .feature-card {
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .course-card {
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            position: relative;
            overflow: hidden;
            border: 2px solid rgba(226, 232, 240, 0.8);
            display: flex;
            flex-direction: column;
            margin: 0;
            height: 100%;
        }

        .course-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #3b82f6, #10b981, #3b82f6);
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: 1;
        }

        .course-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.03), rgba(16, 185, 129, 0.03));
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .course-card:hover::before {
            opacity: 1;
        }

        .course-card:hover::after {
            opacity: 1;
        }

        .course-card:hover {
            transform: translateY(-15px) scale(1.04);
            box-shadow: 0 30px 60px rgba(59, 130, 246, 0.3), 0 0 40px rgba(16, 185, 129, 0.2);
            border-color: rgba(59, 130, 246, 0.3);
        }

        .course-card .course-image {
            transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
        }

        .course-card .course-image::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(16, 185, 129, 0.2));
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: 1;
        }

        .course-card:hover .course-image {
            transform: scale(1.12);
        }

        .course-card:hover .course-image::before {
            opacity: 1;
        }

        .course-card:hover .course-image i {
            transform: scale(1.2) rotate(5deg);
        }

        /* Course Card Desktop Optimizations */
        /* Grid Fixes for Course Cards */
        .grid.grid-cols-1.sm\\:grid-cols-2.lg\\:grid-cols-3 {
            display: grid !important;
        }
        
        @media (min-width: 640px) {
            .grid.grid-cols-1.sm\\:grid-cols-2.lg\\:grid-cols-3 {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
        }
        
        @media (min-width: 1024px) {
            .grid.grid-cols-1.sm\\:grid-cols-2.lg\\:grid-cols-3 {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        }
        }

        /* Course Card Desktop Optimizations */
        @media (min-width: 1024px) {
            .course-card {
                max-width: 100%;
                width: 100%;
                min-width: 0;
        }

            .course-card .course-image {
                height: 180px !important;
                flex-shrink: 0;
        }

            .course-card .course-image i {
                font-size: 3rem !important;
            }
        }

        /* Course Card Tablet Optimizations */
        @media (min-width: 640px) and (max-width: 1023px) {
            .course-card {
                max-width: 100%;
                width: 100%;
                min-width: 0;
        }

            .course-card .course-image {
                height: 180px !important;
                flex-shrink: 0;
            }
        }

        /* Course Card Mobile Optimizations */
        @media (max-width: 639px) {
            .course-card {
                border-radius: 1.5rem;
                max-width: 100%;
            width: 100%;
                min-width: 0;
            }
            
            .course-card .course-image {
                height: 160px !important;
                flex-shrink: 0;
            }
            
            .course-card .course-image i {
                font-size: 2.5rem !important;
            }
            
            .course-card:hover {
                transform: translateY(-8px) scale(1.02);
            }
        }


        .section-title {
            position: relative;
            display: inline-block;
            padding-bottom: 10px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, #3b82f6, #10b981, #8b5cf6);
            border-radius: 2px;
            animation: expandWidth 1s ease-out;
        }

        .section-title::before {
            content: '';
            position: absolute;
            bottom: -2px;
            right: 0;
            width: 40px;
            height: 8px;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(16, 185, 129, 0.3));
            border-radius: 2px;
            animation: expandWidth 1.2s ease-out;
        }

        @keyframes expandWidth {
            from { width: 0; }
            to { width: 80px; }
        }

        /* Scroll Animations */
        .fade-in-up {
                opacity: 0;
            transform: translateY(80px) scale(0.95);
            transition: all 1s cubic-bezier(0.34, 1.56, 0.64, 1);
            }

        .fade-in-up.visible {
                opacity: 1;
            transform: translateY(0) scale(1);
        }

        .fade-in-left {
                opacity: 0;
            transform: translateX(-80px) scale(0.95);
            transition: all 1s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .fade-in-left.visible {
            opacity: 1;
            transform: translateX(0) scale(1);
        }

        .fade-in-right {
            opacity: 0;
            transform: translateX(80px) scale(0.95);
            transition: all 1s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .fade-in-right.visible {
            opacity: 1;
            transform: translateX(0) scale(1);
        }

        /* Counter Animation */
        .counter {
            font-variant-numeric: tabular-nums;
        }

        /* Smooth scroll - disabled for better performance on mobile */
        html {
            scroll-behavior: auto;
            /* Prevent scroll bounce on iOS */
            -webkit-overflow-scrolling: touch;
        }
        
        /* Enable smooth scroll only for anchor links on desktop */
        @media (prefers-reduced-motion: no-preference) and (min-width: 1024px) {
            html:has(a[href^="#"]:focus) {
                scroll-behavior: smooth;
            }
        }

        /* Disable smooth scroll on mobile to prevent jank */
        @media (max-width: 1023px) {
            html {
                scroll-behavior: auto;
            }
        }

        /* Enhanced section transitions */
        section {
            position: relative;
            transition: transform 0.3s ease-out;
            /* Prevent horizontal overflow */
            max-width: 100vw;
            overflow-x: hidden;
            /* Optimize rendering */
            will-change: auto;
        }

        /* Fix for all containers on mobile */
        @media (max-width: 1023px) {
            .max-w-7xl, .max-w-6xl, .max-w-5xl, .max-w-4xl {
                max-width: 100%;
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        /* Subtle shine effect on hover */
        .shine-effect {
            position: relative;
            overflow: hidden;
        }

        .shine-effect::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .shine-effect:hover::before {
            left: 100%;
        }

        /* Gradient text animation */
        @keyframes gradientShift {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }
        
        @keyframes shimmer {
            0% {
                transform: translateX(-100%) skewX(-15deg);
            }
            100% {
                transform: translateX(200%) skewX(-15deg);
            }
        }
        
        .animate-shimmer {
            animation: shimmer 3s infinite;
        }
        
        @keyframes patternMove {
            0% {
                transform: translateX(0) translateY(0);
            }
            100% {
                transform: translateX(20px) translateY(20px);
            }
        }
        
        @keyframes pulse-slow {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }
        
        .animate-pulse-slow {
            animation: pulse-slow 3s ease-in-out infinite;
        }
        
        @keyframes spin-slow {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        .animate-spin-slow {
            animation: spin-slow 8s linear infinite;
        }

        .gradient-text {
            background: linear-gradient(135deg, #3b82f6, #10b981, #8b5cf6, #3b82f6);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 5s ease infinite;
        }

        /* Featured Courses Section - عنوان بألوان الأكاديمية وخط عربي أصيل */
        .featured-courses-title {
            font-family: 'Tajawal', 'Cairo', 'Noto Sans Arabic', sans-serif;
            font-weight: 800;
            letter-spacing: -0.02em;
            line-height: 1.35;
            position: relative;
        }
        /* النص يظهر وكأنه يُرسم من اليمين لليسار */
        .featured-courses-title-draw {
            display: inline-block;
            clip-path: inset(0 0 0 100%);
            animation: featuredDrawReveal 1.4s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }
        @keyframes featuredDrawReveal {
            to {
                clip-path: inset(0 0 0 0);
            }
        }
        /* خط زخرفي بألوان الأكاديمية (أزرق → أخضر) */
        .featured-courses-title-line {
            display: block;
            height: 4px;
            width: 100%;
            max-width: 100%;
            margin-top: 0.4em;
            background: linear-gradient(90deg, #1e40af 0%, #3b82f6 35%, #10b981 70%, #059669 100%);
            border-radius: 2px;
            transform: scaleX(0);
            transform-origin: right;
            animation: featuredLineDraw 0.8s cubic-bezier(0.22, 1, 0.36, 1) 0.5s forwards;
        }
        @keyframes featuredLineDraw {
            to {
                transform: scaleX(1);
            }
        }
        .featured-courses-title-main {
            color: #1e3a8a;
            font-weight: 800;
            text-shadow: 0 1px 2px rgba(30, 58, 138, 0.08);
        }
        .featured-courses-title-highlight {
            background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 40%, #10b981 70%, #059669 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            padding: 0 4px;
            letter-spacing: 0.02em;
            font-weight: 800;
        }
        .featured-courses-badge {
            font-family: 'Tajawal', 'Cairo', 'Noto Sans Arabic', sans-serif;
            font-weight: 700;
        }
        .featured-courses-scroll {
            width: 100vw;
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
            box-sizing: border-box;
            -webkit-overflow-scrolling: touch;
            contain: layout style paint;
        }
        #featured-courses-track {
            contain: layout style;
        }
        @media (min-width: 640px) {
            .featured-courses-scroll {
                padding-left: 2.5rem;
                padding-right: 2.5rem;
            }
        }
        @media (min-width: 1024px) {
            .featured-courses-scroll {
                padding-left: 3rem;
                padding-right: 3rem;
            }
        }
        .scrollbar-featured::-webkit-scrollbar {
            height: 8px;
        }
        .scrollbar-featured::-webkit-scrollbar-track {
            background: rgba(243, 244, 246, 0.8);
            border-radius: 10px;
        }
        .scrollbar-featured::-webkit-scrollbar-thumb {
            background: linear-gradient(90deg, #3b82f6, #10b981);
            border-radius: 10px;
        }
        .scrollbar-featured::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(90deg, #2563eb, #059669);
        }

        /* Enhanced Section Titles */
        .section-title-wrapper {
            position: relative;
            display: inline-block;
        }

        .section-title-wrapper::after {
            content: '';
            position: absolute;
            bottom: -5px;
            right: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6, #10b981);
            transform: scaleX(0);
            transform-origin: right;
            animation: expandLine 1s ease-out 0.5s forwards;
        }

        @keyframes expandLine {
            to {
                transform: scaleX(1);
                transform-origin: left;
            }
        }

        /* Number Counter Animation Enhancement */
        .counter-wrapper {
            position: relative;
            display: inline-block;
        }

        .counter-wrapper::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #3b82f6, #10b981);
            transition: width 0.5s ease;
        }

        .stat-card:hover .counter-wrapper::after {
            width: 100%;
        }

        /* Content wrapper */
        .content-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Logo Animation */
        .logo-icon {
            animation: logoPulse 2s ease-in-out infinite;
        }

        @keyframes logoPulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.15);
        }
        }

        /* Icon Hover Effects */
        .icon-hover {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .icon-hover:hover {
            transform: rotate(360deg) scale(1.15);
        }

        /* Enhanced Search Bar */
        .search-bar-wrapper {
            position: relative;
        }

        .search-bar-wrapper::before {
            content: '';
            position: absolute;
            inset: -2px;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(16, 185, 129, 0.3));
            border-radius: 9999px;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
            filter: blur(8px);
        }

        .search-bar-wrapper:focus-within::before {
            opacity: 1;
        }

        /* Loading Animation */
        @keyframes shimmerLoad {
            0% {
                background-position: -1000px 0;
            }
            100% {
                background-position: 1000px 0;
            }
        }

        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 1000px 100%;
            animation: shimmerLoad 2s infinite;
        }

        /* Stagger Animation for Cards */
        .stagger-item {
            animation-delay: calc(var(--stagger-delay) * 0.1s);
        }

        /* Enhanced gradient backgrounds */
        .gradient-overlay {
            position: relative;
        }

        .gradient-overlay::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(16, 185, 129, 0.05));
            pointer-events: none;
        }

        /* Enhanced shadows */
        .shadow-enhanced {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Pulse animation for special elements */
        @keyframes spin-slow {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        .animate-spin-slow {
            animation: spin-slow 3s linear infinite;
        }
        
        @keyframes pulse-glow {
            0%, 100% {
                box-shadow: 0 0 30px rgba(59, 130, 246, 0.6), 0 0 60px rgba(59, 130, 246, 0.3);
            }
            50% {
                box-shadow: 0 0 50px rgba(59, 130, 246, 0.9), 0 0 100px rgba(59, 130, 246, 0.5);
        }
        }


        /* Subtle Glow Effects */
        .glow-effect {
            position: relative;
        }

        .glow-effect::after {
            content: '';
            position: absolute;
            inset: -2px;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(16, 185, 129, 0.2));
            border-radius: inherit;
            z-index: -1;
            opacity: 0;
            filter: blur(10px);
            transition: opacity 0.3s ease;
        }

        .glow-effect:hover::after {
            opacity: 1;
        }

        /* Animated Grid Pattern */
        .animated-grid {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(59, 130, 246, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59, 130, 246, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: gridMove 12s linear infinite;
            opacity: 0.5;
        }

        @keyframes gridMove {
            0% {
                transform: translate(0, 0);
        }
            100% {
                transform: translate(50px, 50px);
            }
        }

        /* Smooth Scroll Indicator */
        .scroll-indicator {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6, #10b981);
            z-index: 9999;
            transform-origin: left;
            transform: scaleX(0);
        }
    </style>
    </head>

<body class="bg-gray-50 text-gray-900" 
      style="overflow-y: auto !important; overflow-x: hidden !important; position: relative !important; height: auto !important; min-height: 100vh !important;">
    <script>
        // حل فوري وقوي للتمرير بالماوس - يجب أن يكون في بداية body
        (function() {
            'use strict';
            
            function forceEnableScrolling() {
                try {
                    const mobileMenu = document.getElementById('mobile-menu-sidebar');
                    const isMenuOpen = mobileMenu && (mobileMenu.style.display === 'block' || (window.getComputedStyle && window.getComputedStyle(mobileMenu).display === 'block'));
                    
                    if (!isMenuOpen) {
                        // Force scroll on html and body
                        document.documentElement.style.setProperty('overflow', 'auto', 'important');
                        document.documentElement.style.setProperty('overflow-y', 'auto', 'important');
                        document.documentElement.style.setProperty('overflow-x', 'hidden', 'important');
                        document.documentElement.style.setProperty('height', 'auto', 'important');
                        document.documentElement.style.setProperty('position', 'relative', 'important');
                        document.documentElement.style.removeProperty('max-height');
                        
                        document.body.style.setProperty('overflow', 'auto', 'important');
                        document.body.style.setProperty('overflow-y', 'auto', 'important');
                        document.body.style.setProperty('overflow-x', 'hidden', 'important');
                        document.body.style.setProperty('position', 'relative', 'important');
                        document.body.style.setProperty('height', 'auto', 'important');
                        document.body.style.setProperty('width', '100%', 'important');
                        document.body.style.removeProperty('max-height');
                        document.body.classList.remove('overflow-hidden');
                        document.body.classList.remove('no-scroll');
                    }
                } catch(e) {
                    console.error('Error enabling scroll:', e);
                }
            }
            
            // تفعيل فوراً
            forceEnableScrolling();
            
            // إضافة wheel handler بسيط وقوي يقوم بالتمرير
            function handleWheel(e) {
                const mobileMenu = document.getElementById('mobile-menu-sidebar');
                const isMenuOpen = mobileMenu && (mobileMenu.style.display === 'block' || (window.getComputedStyle && window.getComputedStyle(mobileMenu).display === 'block'));
                
                if (!isMenuOpen) {
                    forceEnableScrolling();
                    // السماح بالتمرير الطبيعي - لا نمنع الافتراضي
                }
            }
            
            // إضافة wheel listeners على جميع المستويات
            window.addEventListener('wheel', handleWheel, { passive: true, capture: true });
            document.addEventListener('wheel', handleWheel, { passive: true, capture: true });
            document.body.addEventListener('wheel', handleWheel, { passive: true, capture: true });
            
            // للتوافق مع المتصفحات القديمة
            window.addEventListener('mousewheel', handleWheel, { passive: true, capture: true });
            document.addEventListener('mousewheel', handleWheel, { passive: true, capture: true });
            document.body.addEventListener('mousewheel', handleWheel, { passive: true, capture: true });
            
            // تفعيل عند تحميل DOM
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    forceEnableScrolling();
                    setTimeout(forceEnableScrolling, 50);
                    setTimeout(forceEnableScrolling, 100);
                    setTimeout(forceEnableScrolling, 500);
                });
            } else {
                forceEnableScrolling();
                setTimeout(forceEnableScrolling, 50);
                setTimeout(forceEnableScrolling, 100);
                setTimeout(forceEnableScrolling, 500);
            }
            
            // تفعيل عند تحميل الصفحة
            window.addEventListener('load', function() {
                forceEnableScrolling();
                setTimeout(forceEnableScrolling, 100);
                setTimeout(forceEnableScrolling, 500);
            });
            
            // مراقبة عند تغيير الحجم أو الرؤية فقط (تقليل التقطيع)
            var lastCheck = 0;
            function throttleScrollFix() {
                var now = Date.now();
                if (now - lastCheck < 2000) return;
                lastCheck = now;
                forceEnableScrolling();
            }
            window.addEventListener('resize', throttleScrollFix);
            document.addEventListener('visibilitychange', function() { if (document.visibilityState === 'visible') forceEnableScrolling(); });
        })();
    </script>
    <script>
        // إصلاح فوري وقوي للتمرير - يجب أن يكون في بداية body
        (function() {
            'use strict';
            
            let isFixing = false;
            
            function forceEnableScrolling() {
                if (isFixing) return;
                isFixing = true;
                
                try {
                    const mobileMenu = document.getElementById('mobile-menu-sidebar');
                    const isMenuOpen = mobileMenu && (mobileMenu.style.display === 'block' || (window.getComputedStyle && window.getComputedStyle(mobileMenu).display === 'block'));
                    
                    if (!isMenuOpen) {
                        // إجبار تفعيل التمرير على html
                        document.documentElement.style.setProperty('overflow', 'auto', 'important');
                        document.documentElement.style.setProperty('overflow-y', 'auto', 'important');
                        document.documentElement.style.setProperty('overflow-x', 'hidden', 'important');
                        document.documentElement.style.setProperty('height', 'auto', 'important');
                        document.documentElement.style.setProperty('position', 'relative', 'important');
                        document.documentElement.style.removeProperty('max-height');
                        
                        // إجبار تفعيل التمرير على body
                        document.body.style.setProperty('overflow', 'auto', 'important');
                        document.body.style.setProperty('overflow-y', 'auto', 'important');
                        document.body.style.setProperty('overflow-x', 'hidden', 'important');
                        document.body.style.setProperty('position', 'relative', 'important');
                        document.body.style.setProperty('height', 'auto', 'important');
                        document.body.style.setProperty('width', '100%', 'important');
                        document.body.style.removeProperty('max-height');
                        document.body.classList.remove('overflow-hidden');
                        document.body.classList.remove('no-scroll');
                    }
                } catch(e) {
                    console.error('Error enabling scroll:', e);
                } finally {
                    setTimeout(() => { isFixing = false; }, 50);
                }
            }
            
            // تفعيل فوراً
            forceEnableScrolling();
            
            // تفعيل عند تحميل DOM
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', forceEnableScrolling);
            } else {
                forceEnableScrolling();
            }
            
            // تفعيل عند تحميل الصفحة
            window.addEventListener('load', forceEnableScrolling);
            
            // MutationObserver لمراقبة أي تغييرات على body
            if (typeof MutationObserver !== 'undefined') {
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'attributes' && 
                            (mutation.attributeName === 'style' || mutation.attributeName === 'class')) {
                            const mobileMenu = document.getElementById('mobile-menu-sidebar');
                            const isMenuOpen = mobileMenu && (mobileMenu.style.display === 'block' || (window.getComputedStyle && window.getComputedStyle(mobileMenu).display === 'block'));
                            
                            if (!isMenuOpen) {
                                const bodyComputed = window.getComputedStyle(document.body);
                                if (bodyComputed.overflow === 'hidden' || bodyComputed.position === 'fixed') {
                                    setTimeout(forceEnableScrolling, 10);
                                }
                            }
                        }
                    });
                });
                
                observer.observe(document.body, {
                    attributes: true,
                    attributeFilter: ['style', 'class']
                });
                
                observer.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['style', 'class']
                });
            }
            
            // إصلاح التمرير بالماوس - حل مباشر وقوي (مشابه تماماً للوحة التحكم)
            function enableWheelScrolling() {
                // إزالة أي wheel handlers قديمة
                if (window._welcomeWheelHandler) {
                    window.removeEventListener('wheel', window._welcomeWheelHandler, true);
                    document.removeEventListener('wheel', window._welcomeWheelHandler, true);
                    document.body.removeEventListener('wheel', window._welcomeWheelHandler, true);
                    window.removeEventListener('mousewheel', window._welcomeMousewheelHandler, true);
                    document.removeEventListener('mousewheel', window._welcomeMousewheelHandler, true);
                    document.body.removeEventListener('mousewheel', window._welcomeMousewheelHandler, true);
                }
                
                // التأكد من أن التمرير مفعّل
                forceEnableScrolling();
                
                // إضافة wheel event handler مباشر على window - يقوم بالتمرير الفعلي
                window._welcomeWheelHandler = function(e) {
                    const mobileMenu = document.getElementById('mobile-menu-sidebar');
                    const isMenuOpen = mobileMenu && (mobileMenu.style.display === 'block' || (window.getComputedStyle && window.getComputedStyle(mobileMenu).display === 'block'));
                    
                    // إذا كانت القائمة المتنقلة مفتوحة، لا نفعل شيئاً
                    if (isMenuOpen) {
                        return;
                    }
                    
                    // التأكد من أن التمرير مفعّل
                    const bodyComputed = window.getComputedStyle(document.body);
                    const htmlComputed = window.getComputedStyle(document.documentElement);
                    
                    // إذا كان التمرير معطل، نفعّله
                    if (bodyComputed.overflow === 'hidden' || bodyComputed.position === 'fixed' || 
                        htmlComputed.overflow === 'hidden' || htmlComputed.position === 'fixed') {
                        forceEnableScrolling();
                    }
                    
                    // السماح بالتمرير الطبيعي - لا نمنع الافتراضي
                    // المتصفح سيقوم بالتمرير تلقائياً إذا كان overflow: auto
                };
                
                // إضافة event listeners على window و document و body
                // نستخدم passive: true للسماح بالتمرير الطبيعي
                window.addEventListener('wheel', window._welcomeWheelHandler, { passive: true, capture: false });
                document.addEventListener('wheel', window._welcomeWheelHandler, { passive: true, capture: false });
                document.body.addEventListener('wheel', window._welcomeWheelHandler, { passive: true, capture: false });
                
                // للتوافق مع المتصفحات القديمة
                window._welcomeMousewheelHandler = function(e) {
                    window._welcomeWheelHandler(e);
                };
                window.addEventListener('mousewheel', window._welcomeMousewheelHandler, { passive: true, capture: false });
                document.addEventListener('mousewheel', window._welcomeMousewheelHandler, { passive: true, capture: false });
                document.body.addEventListener('mousewheel', window._welcomeMousewheelHandler, { passive: true, capture: false });
            }
            
            // تفعيل wheel scrolling
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    enableWheelScrolling();
                    setTimeout(enableWheelScrolling, 100);
                    setTimeout(enableWheelScrolling, 500);
                });
            } else {
                enableWheelScrolling();
                setTimeout(enableWheelScrolling, 100);
                setTimeout(enableWheelScrolling, 500);
            }
            
            window.addEventListener('load', function() {
                setTimeout(enableWheelScrolling, 100);
                setTimeout(enableWheelScrolling, 500);
            });
        })();
    </script>

    <!-- Navigation Header -->
    <?php echo $__env->make('components.unified-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Scroll Progress Indicator - Hidden on mobile for performance -->
    <div class="scroll-indicator hidden sm:block" id="scrollIndicator"></div>

    <!-- Enhanced Background with Subtle Animations -->
    <div class="animated-bg">
        <div class="bg-shape bg-shape-1"></div>
        <div class="bg-shape bg-shape-2"></div>
        <div class="bg-shape bg-shape-3"></div>
    </div>

    <!-- Hero Section -->
    <section class="hero-section px-4 content-wrapper relative" style="padding-top: 100px; overflow-x: hidden !important; max-width: 100vw !important; width: 100% !important;">
        <div class="hero-glow"></div>
        
        <!-- Animated Background Elements -->
        <div class="animated-background">
            <!-- Floating Circles -->
            <div class="floating-circle circle-1"></div>
            <div class="floating-circle circle-2"></div>
            <div class="floating-circle circle-3"></div>
            <div class="floating-circle circle-4"></div>
            <div class="floating-circle circle-5"></div>
            
            <!-- Floating Code Symbols -->
            <div class="floating-code-symbol code-symbol-1">&lt; /&gt;</div>
            <div class="floating-code-symbol code-symbol-2">{ }</div>
            <div class="floating-code-symbol code-symbol-3">( )</div>
            <div class="floating-code-symbol code-symbol-4">[ ]</div>
            <div class="floating-code-symbol code-symbol-5">#</div>
            <div class="floating-code-symbol code-symbol-6">$</div>
            <div class="floating-code-symbol code-symbol-7">&lt;div&gt;</div>
            <div class="floating-code-symbol code-symbol-8">=&gt;</div>
            <div class="floating-code-symbol code-symbol-9">const</div>
            <div class="floating-code-symbol code-symbol-10">function</div>
            <div class="floating-code-symbol code-symbol-11">import</div>
            <div class="floating-code-symbol code-symbol-12">export</div>
            
            <!-- Floating Lines -->
            <div class="floating-line line-1"></div>
            <div class="floating-line line-2"></div>
            <div class="floating-line line-3"></div>

            <!-- Floating Particles -->
            <div class="floating-particle particle-1"></div>
            <div class="floating-particle particle-2"></div>
            <div class="floating-particle particle-3"></div>
            <div class="floating-particle particle-4"></div>
            <div class="floating-particle particle-5"></div>
            <div class="floating-particle particle-6"></div>
            <div class="floating-particle particle-7"></div>
            <div class="floating-particle particle-8"></div>
        </div>

        <div class="max-w-7xl mx-auto text-center relative z-10">
            <!-- Main Headline -->
            <h1 class="hero-headline text-5xl sm:text-6xl md:text-7xl lg:text-8xl xl:text-9xl font-extrabold text-blue-900 mb-6 sm:mb-8 md:mb-10 leading-tight fade-in-up" style="font-family: 'Tajawal', 'Cairo', sans-serif;">
                استعد لمستقبل البرمجة والذكاء الاصطناعي
            </h1>
            <!-- Sub-headline -->
            <p class="text-xl sm:text-2xl md:text-3xl lg:text-4xl text-blue-700/95 mb-8 sm:mb-10 md:mb-14 font-bold fade-in-up max-w-4xl mx-auto leading-relaxed" style="font-family: 'Tajawal', 'Cairo', sans-serif;">
                تعلم التقنيات الأحدث في البرمجة والذكاء الاصطناعي وكن جزءاً من الثورة التقنية
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-10 sm:mb-12 md:mb-16 fade-in-up">
                <a href="<?php echo e(route('public.courses')); ?>" class="btn-primary text-white w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 rounded-full font-bold text-base sm:text-lg shadow-lg relative overflow-hidden">
                    <span class="relative z-10">
                        <i class="fas fa-book ml-2"></i>
                        تصفح الكورسات
                    </span>
                </a>
                <a href="<?php echo e(route('register')); ?>" class="btn-secondary text-white w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 rounded-full font-bold text-base sm:text-lg shadow-lg relative overflow-hidden">
                    <span class="relative z-10">
                        <i class="fas fa-user-plus ml-2"></i>
                        اشترك الآن
                    </span>
                </a>
                </div>

            <!-- Statistics Section -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6 max-w-5xl mx-auto">
                <!-- Stat 1 -->
                <div class="stat-card p-6 fade-in-up" style="animation-delay: 0.1s;">
                    <div class="counter-wrapper">
                        <div class="text-3xl md:text-4xl font-black text-blue-600 mb-2 counter" data-target="35200">35,200</div>
                            </div>
                    <div class="text-gray-600 font-medium">دقيقة مشاهدة</div>
                        </div>

                <!-- Stat 2 -->
                <div class="stat-card p-6 fade-in-up" style="animation-delay: 0.2s;">
                    <div class="counter-wrapper">
                        <div class="text-3xl md:text-4xl font-black text-blue-600 mb-2 counter" data-target="1250">1,250+</div>
                                </div>
                    <div class="text-gray-600 font-medium">شهادة</div>
                            </div>

                <!-- Stat 3 -->
                <div class="stat-card p-6 fade-in-up" style="animation-delay: 0.3s;">
                    <div class="counter-wrapper">
                        <div class="text-3xl md:text-4xl font-black text-blue-600 mb-2 counter" data-target="85">85+</div>
                                </div>
                    <div class="text-gray-600 font-medium">دورة تدريبية</div>
                            </div>

                <!-- Stat 4 -->
                <div class="stat-card p-6 fade-in-up" style="animation-delay: 0.4s;">
                    <div class="counter-wrapper">
                        <div class="text-3xl md:text-4xl font-black text-blue-600 mb-2 counter" data-target="3250">3,250+</div>
                                </div>
                    <div class="text-gray-600 font-medium">متعلم عربي</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Courses Section -->
    <section class="py-16 md:py-20 lg:py-24 bg-gradient-to-b from-white via-blue-50/40 to-white content-wrapper relative parallax-section overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 fade-in-up">
                <div class="inline-block mb-4">
                    <span class="featured-courses-badge bg-gradient-to-r from-blue-50 via-green-50/80 to-blue-50 text-blue-800 px-4 py-2 rounded-full text-sm font-bold inline-flex items-center gap-2 shadow-sm border border-blue-200/60">
                        <i class="fas fa-star text-blue-600"></i>
                        <span>أفضل الدورات التدريبية</span>
                    </span>
                </div>
                <h2 class="featured-courses-title text-4xl md:text-5xl lg:text-6xl font-black mb-5 inline-block">
                    <span class="featured-courses-title-draw">
                        <span class="featured-courses-title-main">الكورسات</span><span class="featured-courses-title-highlight"> المميزة</span>
                    </span>
                    <span class="featured-courses-title-line" aria-hidden="true"></span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    اكتشف أفضل الكورسات البرمجية المصممة خصيصاً لمساعدتك على النجاح وتحقيق أهدافك
                </p>
            </div>
        </div>

        <?php
            $featuredCourses = \App\Models\AdvancedCourse::where('is_active', true)
                ->where('is_featured', true)
                ->with(['academicSubject', 'instructor'])
                ->withCount('lessons')
                ->limit(12)
                ->get();
        ?>

        <?php if($featuredCourses->count() > 0): ?>
            <div class="featured-courses-scroll w-full">
                <div id="featured-courses-track" class="flex gap-4 lg:gap-6 overflow-x-auto overflow-y-hidden pb-4 scroll-smooth snap-x snap-mandatory scrollbar-featured" style="scrollbar-gutter: stable;">
                    <?php $__currentLoopData = $featuredCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="group relative flex-shrink-0 w-[280px] sm:w-[320px] lg:w-[340px] snap-center fade-in-up bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 border border-gray-100 hover:border-blue-200" style="animation-delay: <?php echo e($index * 0.05); ?>s;">
                            <!-- Animated Background Glow -->
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-400 via-green-400 to-purple-400 rounded-2xl blur opacity-0 group-hover:opacity-20 transition-opacity duration-500 -z-10"></div>
                            
                            <!-- Header with Enhanced Gradient -->
                            <div class="relative h-32 bg-gradient-to-br from-blue-600 via-blue-500 to-green-500 overflow-hidden">
                                <!-- Background Pattern (ثابت - بدون حركة للأداء) -->
                                <div class="absolute inset-0 opacity-10" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.1) 10px, rgba(255,255,255,0.1) 20px);"></div>
                                
                                <!-- Shimmer على الهوفر فقط -->
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-black/5 to-transparent"></div>
                                
                                <!-- Logo - Letter M -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="relative z-10 transform group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                        <div class="text-white font-black text-5xl lg:text-6xl relative" style="font-family: 'Cairo', 'Noto Sans Arabic', sans-serif; text-shadow: 0 4px 20px rgba(0,0,0,0.4); letter-spacing: -2px;">
                                            M
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Featured Badge -->
                                <?php if($course->is_featured): ?>
                                    <div class="absolute top-2 left-2 z-20">
                                        <span class="bg-gradient-to-r from-yellow-400 via-yellow-500 to-orange-500 text-yellow-900 px-2.5 py-1 rounded-full text-[10px] font-bold shadow-lg flex items-center gap-1 border border-yellow-300/50 backdrop-blur-sm">
                                            <i class="fas fa-star text-[8px]"></i>
                                            <span>مميز</span>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Lessons Count with Enhanced Design -->
                                <div class="absolute bottom-2 right-2 z-20">
                                    <div class="bg-white/95 backdrop-blur-md rounded-lg px-2.5 py-1.5 shadow-xl border border-white/50 group-hover:scale-110 transition-transform duration-300">
                                        <span class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                                            <i class="fas fa-play-circle text-blue-600 text-[11px] group-hover:text-green-600 transition-colors duration-300"></i>
                                            <span><?php echo e($course->lessons_count ?? 0); ?> درس</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Content with Enhanced Design -->
                            <div class="p-4 bg-white relative overflow-hidden">
                                <!-- Subtle Background Pattern -->
                                <div class="absolute inset-0 opacity-[0.02] pointer-events-none" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(59, 130, 246, 0.05) 5px, rgba(59, 130, 246, 0.05) 10px);"></div>
                                
                                <!-- Instructor with Enhanced Design -->
                                <div class="mb-3 relative z-10">
                                    <div class="inline-flex items-center gap-1.5 bg-gradient-to-r from-blue-50 to-green-50 px-2.5 py-1.5 rounded-full border border-blue-100 group-hover:border-blue-200 transition-colors duration-300 shadow-sm group-hover:shadow-md">
                                        <div class="w-4 h-4 bg-gradient-to-br from-blue-500 to-green-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-white text-[8px]"></i>
                                        </div>
                                        <span class="text-xs font-semibold text-blue-700 group-hover:text-blue-800 transition-colors duration-300"><?php echo e(Str::limit($course->instructor->name ?? 'مدرب', 18)); ?></span>
                                    </div>
                                </div>

                                <!-- Title with Enhanced Design -->
                                <h3 class="text-base font-black text-gray-900 mb-2 line-clamp-2 group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-blue-600 group-hover:to-green-600 transition-all duration-300 leading-tight relative z-10">
                                    <?php echo e($course->title); ?>

                                </h3>
                                
                                <!-- Description with Enhanced Design -->
                                <p class="text-gray-600 text-xs mb-3 line-clamp-2 leading-relaxed group-hover:text-gray-700 transition-colors duration-300 relative z-10">
                                    <?php echo e(Str::limit($course->description ?? 'دورة برمجية متكاملة', 70)); ?>

                                </p>
                                
                                <!-- Footer with Enhanced Design -->
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100 group-hover:border-blue-100 transition-colors duration-300 relative z-10">
                                    <!-- Price with Enhanced Design -->
                                    <div>
                                        <?php if($course->is_free): ?>
                                            <span class="text-lg font-black text-green-600 flex items-center gap-1.5 group-hover:scale-110 transition-transform duration-300">
                                                <div class="w-5 h-5 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-md">
                                                    <i class="fas fa-gift text-white text-[8px]"></i>
                                                </div>
                                                <span>مجاني</span>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-lg font-black text-blue-600 flex items-center gap-1 group-hover:scale-110 transition-transform duration-300">
                                                <span><?php echo e(number_format($course->price ?? 0)); ?></span>
                                                <span class="text-[10px] text-gray-500 font-normal">ج.م</span>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- CTA Button with Enhanced Design -->
                                    <a href="<?php echo e(route('public.course.show', $course->id)); ?>" class="relative bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-3.5 py-1.5 rounded-lg text-xs font-bold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 flex items-center gap-1.5 overflow-hidden group/btn">
                                        <!-- Shimmer Effect -->
                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent opacity-0 group-hover/btn:opacity-100 group-hover/btn:animate-shimmer transition-opacity duration-300"></div>
                                        <!-- Glow Effect -->
                                        <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-400 to-green-400 rounded-lg blur opacity-0 group-hover/btn:opacity-50 transition-opacity duration-300"></div>
                                        <span class="relative z-10">عرض</span>
                                        <i class="fas fa-arrow-left text-[10px] relative z-10 group-hover/btn:translate-x-1 transition-transform duration-300"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
                <div class="text-center fade-in-up">
                    <a href="<?php echo e(route('public.courses')); ?>" class="inline-flex items-center gap-3 bg-gradient-to-r from-blue-600 to-green-500 text-white px-10 py-4 rounded-full font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-110 relative overflow-hidden group">
                        <span class="relative z-10 flex items-center gap-2">
                            <i class="fas fa-book-open"></i>
                            <span>عرض جميع الكورسات</span>
                            <i class="fas fa-arrow-left transition-transform duration-300 group-hover:-translate-x-1"></i>
                        </span>
                        <span class="absolute inset-0 bg-gradient-to-r from-green-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center py-12 fade-in-up">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-book text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">قريباً...</h3>
                    <p class="text-gray-600 mb-6">نعمل على إضافة المزيد من الكورسات المميزة</p>
                    <a href="<?php echo e(route('register')); ?>" class="btn-primary text-white px-6 py-3 rounded-full relative overflow-hidden">
                        <span class="relative z-10">اشترك للحصول على التحديثات</span>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <?php if(isset($featuredCourses) && $featuredCourses->count() > 0): ?>
    <script>
    (function() {
        var track = document.getElementById('featured-courses-track');
        if (!track) return;
        var stepMs = 5000;
        var isPaused = false;
        track.addEventListener('mouseenter', function() { isPaused = true; });
        track.addEventListener('mouseleave', function() { isPaused = false; });
        track.addEventListener('touchstart', function() { isPaused = true; }, { passive: true });
        track.addEventListener('touchend', function() { setTimeout(function() { isPaused = false; }, 3000); }, { passive: true });
        setInterval(function() {
            if (isPaused) return;
            var card = track.querySelector('.flex-shrink-0');
            var gap = 16;
            if (window.matchMedia('(min-width: 1024px)').matches) gap = 24;
            var step = (card ? card.offsetWidth : 320) + gap;
            var maxScroll = track.scrollWidth - track.clientWidth;
            if (maxScroll <= 0) return;
            var isRtl = document.documentElement.getAttribute('dir') === 'rtl';
            if (isRtl) {
                track.scrollBy({ left: -step, behavior: 'auto' });
                if (track.scrollLeft <= -maxScroll || track.scrollLeft >= maxScroll) track.scrollLeft = 0;
            } else {
                track.scrollBy({ left: step, behavior: 'auto' });
                if (track.scrollLeft >= maxScroll - 10) track.scrollLeft = 0;
            }
        }, stepMs);
    })();
    </script>
    <?php endif; ?>

    <!-- Features Section -->
    <section class="py-12 md:py-16 lg:py-20 bg-gradient-to-b from-white via-gray-50 to-white relative">
        <!-- Subtle animated background elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-10 left-10 w-96 h-96 bg-blue-400/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-green-400/5 rounded-full blur-3xl"></div>
                </div>
                
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-12 fade-in-up">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 mb-4 section-title" style="font-family: 'Tajawal', 'Cairo', sans-serif;">
                    لماذا <span class="gradient-text">Mindlytics</span>؟
                </h2>
                <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto">
                    منصة تعليمية متكاملة تجمع بين أفضل المحتوى التعليمي والتقنيات الحديثة
                </p>
                        </div>
                        
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 auto-rows-fr">
                <!-- Feature 1 -->
                <div class="course-card text-center p-8 rounded-3xl fade-in-left h-full flex flex-col items-center justify-center group">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-graduation-cap text-white text-3xl"></i>
                                </div>
                    <h3 class="text-xl md:text-2xl font-black text-gray-900 mb-3">تعليم احترافي</h3>
                    <p class="text-gray-600 text-base leading-relaxed">محاضرات عالية الجودة من خبراء في المجال</p>
                            </div>
                            
                <!-- Feature 2 -->
                <div class="course-card text-center p-8 rounded-3xl fade-in-up h-full flex flex-col items-center justify-center group">
                    <div class="w-20 h-20 bg-gradient-to-br from-green-600 to-green-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-certificate text-white text-3xl"></i>
                                </div>
                    <h3 class="text-xl md:text-2xl font-black text-gray-900 mb-3">شهادات معتمدة</h3>
                    <p class="text-gray-600 text-base leading-relaxed">احصل على شهادات معتمدة بعد إتمام كل دورة</p>
                            </div>
                            
                <!-- Feature 3 -->
                <div class="course-card text-center p-8 rounded-3xl fade-in-up h-full flex flex-col items-center justify-center group">
                    <div class="w-20 h-20 bg-gradient-to-br from-purple-600 to-purple-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-users text-white text-3xl"></i>
                                </div>
                    <h3 class="text-xl md:text-2xl font-black text-gray-900 mb-3">مجتمع نشط</h3>
                    <p class="text-gray-600 text-base leading-relaxed">انضم لمجتمع من المتعلمين والمبدعين</p>
                            </div>
                            
                <!-- Feature 4 -->
                <div class="course-card text-center p-8 rounded-3xl fade-in-right h-full flex flex-col items-center justify-center group">
                    <div class="w-20 h-20 bg-gradient-to-br from-orange-600 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-headset text-white text-3xl"></i>
                            </div>
                    <h3 class="text-xl md:text-2xl font-black text-gray-900 mb-3">دعم فني مستمر</h3>
                    <p class="text-gray-600 text-base leading-relaxed">فريق دعم متاح لمساعدتك في أي وقت</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mindlytics Portfolio Section -->
    <?php
        $portfolioCards = [
            [
                'name' => 'رفع المشاريع',
                'subtitle' => 'بعد إتمام الكورسات',
                'icon' => 'folder-open',
                'icon_color' => 'blue',
                'border_color' => 'blue-200',
                'hover_border' => 'blue-500',
                'bg_gradient' => 'from-white to-blue-50/50',
                'icon_bg' => 'from-blue-100 to-blue-200',
                'icon_text' => 'blue-600',
                'button_class' => 'btn-primary',
                'features' => [
                    'ارفع مشاريعك الحقيقية بعد كل كورس',
                    'اعرض أعمالك البرمجية في معرض واحد',
                    'وثّق تقدمك وبناء البورتفوليو',
                    'شارك روابط مشاريعك بسهولة'
                ],
                'cta_text' => 'ابدأ التعلم الآن',
                'cta_route' => 'public.courses'
            ],
            [
                'name' => 'معرض أعمال الطلاب',
                'subtitle' => 'حيث ترى الشركات الموهبة',
                'icon' => 'briefcase',
                'icon_color' => 'green',
                'border_color' => 'green-200',
                'hover_border' => 'green-500',
                'bg_gradient' => 'from-white to-green-50/50',
                'icon_bg' => 'from-green-100 to-green-200',
                'icon_text' => 'green-600',
                'button_class' => 'btn-secondary',
                'is_popular' => true,
                'features' => [
                    'الشركات تشاهد أعمال طلابنا مباشرة',
                    'تصفّح مشاريع حقيقية حسب التخصص',
                    'اكتشف مواهب جاهزة للتوظيف',
                    'تواصل مع المطورين عبر المنصة',
                    'معرض واحد يربط التعليم بسوق العمل',
                    'شهادات وإنجازات موثّقة لكل طالب'
                ],
                'cta_text' => 'استعرض المعرض',
                'cta_route' => 'public.courses'
            ],
            [
                'name' => 'فرص عمل حقيقية',
                'subtitle' => 'من التعلم إلى الوظيفة',
                'icon' => 'handshake',
                'icon_color' => 'purple',
                'border_color' => 'purple-200',
                'hover_border' => 'purple-500',
                'bg_gradient' => 'from-white to-purple-50/50',
                'icon_bg' => 'from-purple-100 to-purple-200',
                'icon_text' => 'purple-600',
                'button_class' => 'bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800',
                'features' => [
                    'شركات تبحث عن مواهب خريجي Mindlytics',
                    'طلابنا يحصلون على فرص من معرض الأعمال',
                    'ربط مباشر بين الموهبة واحتياجات السوق',
                    'من مشروع تخرّج إلى أول عقد عمل',
                    'شبكة تواصل بين الطالب والشركة',
                    'مسار واضح من الكورس إلى الوظيفة'
                ],
                'cta_text' => 'انضم كطالب أو شركة',
                'cta_route' => 'register'
            ]
        ];
    ?>

    <section class="py-12 md:py-16 bg-gradient-to-b from-blue-50 via-white to-green-50 content-wrapper relative">
        <div class="absolute inset-0">
            <div class="absolute top-0 left-0 w-1/2 h-full bg-gradient-to-r from-blue-50/50 to-transparent"></div>
            <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-green-50/50 to-transparent"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-12 fade-in-up">
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4 section-title" style="font-family: 'Tajawal', 'Cairo', sans-serif;">
                    <span class="gradient-text">Mindlytics</span> Portfolio
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    من الكورس إلى المشروع ثم الوظيفة — معرض أعمال يربط طلابنا بالشركات ويفتح أبواب الفرص
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 auto-rows-fr">
                <?php $__currentLoopData = $portfolioCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $colorClasses = [
                            'blue' => [
                                'border' => 'border-blue-200',
                                'border_hover' => 'hover:border-blue-500',
                                'bg' => 'bg-blue-200/20',
                                'icon_bg' => 'from-blue-100 to-blue-200',
                                'icon_text' => 'text-blue-600'
                            ],
                            'green' => [
                                'border' => 'border-green-200',
                                'border_hover' => 'hover:border-green-500',
                                'bg' => 'bg-green-200/20',
                                'icon_bg' => 'from-green-100 to-green-200',
                                'icon_text' => 'text-green-600'
                            ],
                            'purple' => [
                                'border' => 'border-purple-200',
                                'border_hover' => 'hover:border-purple-500',
                                'bg' => 'bg-purple-200/20',
                                'icon_bg' => 'from-purple-100 to-purple-200',
                                'icon_text' => 'text-purple-600'
                            ]
                        ];
                        $colors = $colorClasses[$card['icon_color']] ?? $colorClasses['blue'];
                        $animationClass = $index === 0 ? 'fade-in-left' : ($index === 1 ? 'fade-in-up' : 'fade-in-right');
                        $popularClass = ($card['is_popular'] ?? false) ? 'md:-mt-4 md:mb-4 md:scale-105 border-4' : '';
                    ?>

                    <div class="bg-gradient-to-br <?php echo e($card['bg_gradient']); ?> rounded-2xl p-8 shadow-xl border-2 <?php echo e($colors['border']); ?> <?php echo e($colors['border_hover']); ?> transition-all <?php echo e($animationClass); ?> relative overflow-hidden group h-full flex flex-col <?php echo e($popularClass); ?> <?php echo e(($card['is_popular'] ?? false) ? 'pt-14 md:pt-16' : ''); ?>">
                        <?php if($card['is_popular'] ?? false): ?>
                            <div class="absolute top-2 left-1/2 transform -translate-x-1/2 z-30">
                                <span class="bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-1.5 rounded-full text-xs font-bold shadow-lg flex items-center gap-1 whitespace-nowrap">
                                    <i class="fas fa-star text-[10px]"></i>
                                    الأكثر تفاعلاً
                                </span>
                            </div>
                        <?php endif; ?>

                        <div class="absolute top-0 right-0 w-32 h-32 <?php echo e($colors['bg']); ?> rounded-full opacity-80 group-hover:scale-150 transition-transform"></div>

                        <div class="w-20 h-20 bg-gradient-to-br <?php echo e($colors['icon_bg']); ?> rounded-full flex items-center justify-center mx-auto mb-6 icon-hover shadow-lg group-hover:shadow-xl transition-shadow relative z-10">
                            <i class="fas fa-<?php echo e($card['icon']); ?> <?php echo e($colors['icon_text']); ?> text-3xl"></i>
                        </div>

                        <h3 class="text-2xl font-bold text-gray-900 mb-2 text-center relative z-10"><?php echo e($card['name']); ?></h3>
                        <p class="text-center text-gray-600 text-sm mb-6 relative z-10"><?php echo e($card['subtitle']); ?></p>

                        <ul class="space-y-3 mb-6 relative z-10 flex-grow">
                            <?php $__currentLoopData = $card['features']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="flex items-start text-gray-600 group/item">
                                    <i class="fas fa-check text-green-500 ml-2 mt-1 group-hover/item:scale-125 transition-transform flex-shrink-0"></i>
                                    <span class="text-sm"><?php echo e($feature); ?></span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>

                        <?php
                            $buttonClass = !empty($card['button_class']) ? $card['button_class'] : 'bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800';
                        ?>

                        <a href="<?php echo e(route($card['cta_route'] ?? 'public.courses')); ?>" class="<?php echo e($buttonClass); ?> text-white w-full py-3 rounded-full text-center block relative overflow-hidden transition-all duration-300 font-bold transform group-hover:scale-105 shadow-lg group-hover:shadow-xl relative z-10 mt-auto">
                            <span class="relative z-10 flex items-center justify-center gap-2">
                                <i class="fas fa-arrow-left"></i>
                                <span><?php echo e($card['cta_text']); ?></span>
                            </span>
                        </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 md:py-20 lg:py-24 bg-gradient-to-br from-blue-50 via-white to-green-50 relative overflow-hidden">
        <!-- Subtle animated background elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-10 left-10 w-96 h-96 bg-blue-400/5 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-green-400/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-300/3 rounded-full blur-3xl"></div>
        </div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center fade-in-up relative z-10">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 mb-6 leading-tight">
                جاهز لبدء رحلتك البرمجية؟
            </h2>
            <p class="text-lg md:text-xl text-gray-600 mb-10 font-medium">
                انضم إلى آلاف الطلاب الذين حققوا التميز في البرمجة مع Mindlytics
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?php echo e(route('register')); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-8 py-4 rounded-full font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 relative overflow-hidden group">
                    <span class="relative z-10 flex items-center gap-2">
                    <i class="fas fa-user-plus"></i>
                        <span>سجل مجاناً الآن</span>
                    </span>
                    <span class="absolute inset-0 bg-gradient-to-r from-green-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                </a>
                <a href="<?php echo e(route('public.courses')); ?>" class="inline-flex items-center justify-center gap-2 bg-white text-blue-600 px-8 py-4 rounded-full font-bold text-lg border-2 border-blue-600 hover:bg-blue-50 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl relative">
                    <span class="flex items-center gap-2">
                        <span>استعرض جميع الكورسات</span>
                        <i class="fas fa-arrow-left"></i>
                    </span>
                </a>
            </div>
        </div>
    </section>

    </main>

    <!-- Unified Footer -->
    <?php echo $__env->make('components.unified-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>
        // Counter Animation
        function animateCounter(counter) {
            const target = parseInt(counter.getAttribute('data-target'));
            const duration = 2500;
            const increment = target / (duration / 16);
                    let current = 0;
                    
                    const updateCounter = () => {
                        current += increment;
                        if (current < target) {
                    const formatted = Math.floor(current).toLocaleString('ar-EG');
                    counter.textContent = formatted + (target >= 85 && target < 4000 ? '+' : '');
                            requestAnimationFrame(updateCounter);
                        } else {
                    const formatted = target.toLocaleString('ar-EG');
                    counter.textContent = formatted + (target >= 85 && target < 4000 ? '+' : '');
                        }
                    };
                    
            // Use Intersection Observer to trigger animation when visible
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                updateCounter();
                                observer.unobserve(entry.target);
                            }
                        });
            }, { threshold: 0.3 });
                    
                    observer.observe(counter);
        }

        // Scroll Animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

        const fadeObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    }
                });
            }, observerOptions);

        // Simplified Scroll Handler - محسّن للأداء
        const scrollIndicator = document.getElementById('scrollIndicator');
        const navbar = document.getElementById('navbar');
        let ticking = false;
        let lastScrollY = 0;
        const SCROLL_THROTTLE = 16; // ~60fps

        function handleScroll() {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
                    
                    // Update scroll progress indicator (only on desktop for performance)
                    if (scrollIndicator && window.innerWidth >= 640) {
                        const windowHeight = document.documentElement.scrollHeight - window.innerHeight;
                        const progress = Math.min((currentScroll / windowHeight) * 100, 100);
                        scrollIndicator.style.transform = `scaleX(${progress / 100})`;
                    }
                    
                    // Update navbar - فقط عند التغيير
                    if (navbar) {
                        const shouldBeScrolled = currentScroll > 100;
                        const isScrolled = navbar.classList.contains('scrolled');
                        
                        if (shouldBeScrolled !== isScrolled) {
                            if (shouldBeScrolled) {
                                navbar.classList.add('scrolled');
                            } else {
                                navbar.classList.remove('scrolled');
                            }
                        }
                    }
                    
                    lastScrollY = currentScroll;
                    ticking = false;
                });
                ticking = true;
            }
        }

        // Single scroll event listener with throttling and passive for better performance
        window.addEventListener('scroll', handleScroll, { passive: true, capture: false });
        
        // Prevent horizontal scroll on touch devices
        let lastTouchX = 0;
        let lastTouchY = 0;
        document.addEventListener('touchstart', (e) => {
            lastTouchX = e.touches[0].clientX;
            lastTouchY = e.touches[0].clientY;
        }, { passive: true });
        
        document.addEventListener('touchmove', (e) => {
            if (e.touches.length === 1) {
                const touchX = e.touches[0].clientX;
                const touchY = e.touches[0].clientY;
                const deltaX = Math.abs(touchX - lastTouchX);
                const deltaY = Math.abs(touchY - lastTouchY);
                // منع التمرير الأفقي فقط عندما يكون الحركة أفقية بوضوح (لا نمنع التمرير العمودي)
                if (deltaX > 2 * deltaY && deltaX > 15) {
                    e.preventDefault();
                }
            }
        }, { passive: false });


        // Initialize on page load
        // Ensure Alpine.js is loaded and working
        document.addEventListener('alpine:init', () => {
            console.log('Alpine.js initialized');
        });

        // Fallback for mobile menu if Alpine.js fails
        document.addEventListener('DOMContentLoaded', () => {
            // Check if Alpine.js is available
            if (typeof Alpine === 'undefined') {
                console.error('Alpine.js not loaded');
                // Fallback: Use vanilla JS for mobile menu
                const mobileMenuBtn = document.querySelector('[aria-label="قائمة الهاتف"]');
                const mobileMenu = document.getElementById('mobile-menu-sidebar');
                const mobileOverlay = document.querySelector('.mobile-menu-overlay');
                
                if (mobileMenuBtn && mobileMenu) {
                    let isOpen = false;
                    mobileMenuBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        isOpen = !isOpen;
                        if (isOpen) {
                            mobileMenu.style.display = 'block';
                            if (mobileOverlay) mobileOverlay.style.display = 'block';
                            document.body.style.overflow = 'hidden';
                        } else {
                            mobileMenu.style.display = 'none';
                            if (mobileOverlay) mobileOverlay.style.display = 'none';
                            document.body.style.overflow = '';
                        }
                    });
                    
                    if (mobileOverlay) {
                        mobileOverlay.addEventListener('click', () => {
                            isOpen = false;
                            mobileMenu.style.display = 'none';
                            mobileOverlay.style.display = 'none';
                            document.body.style.overflow = '';
                        });
                    }
                }
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            // Animate counters
            document.querySelectorAll('.counter').forEach(counter => {
                animateCounter(counter);
            });

            // Observe fade-in elements
            document.querySelectorAll('.fade-in-up, .fade-in-left, .fade-in-right').forEach(el => {
                fadeObserver.observe(el);
            });

            // Initial scroll update
            handleScroll();
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Search functionality
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && document.activeElement.tagName === 'INPUT') {
                const searchInput = document.activeElement;
                if (searchInput.placeholder.includes('ابحث')) {
                    console.log('Searching for:', searchInput.value);
                    // Implement search functionality here
                }
            }
        });
        
        // إصلاح التمرير - التأكد من أن التمرير مفعّل افتراضياً
        (function() {
            function enableScrolling() {
                // التحقق من أن القائمة المتنقلة ليست مفتوحة
                const mobileMenu = document.getElementById('mobile-menu-sidebar');
                const isMenuOpen = mobileMenu && (mobileMenu.style.display === 'block' || window.getComputedStyle(mobileMenu).display === 'block');
                
                if (!isMenuOpen) {
                    // إجبار تفعيل التمرير
                    document.body.style.setProperty('overflow', 'auto', 'important');
                    document.body.style.setProperty('overflow-y', 'auto', 'important');
                    document.body.style.setProperty('overflow-x', 'hidden', 'important');
                    document.body.style.setProperty('position', 'relative', 'important');
                    document.body.style.setProperty('width', '', 'important');
                    document.body.style.setProperty('height', '', 'important');
                    document.body.classList.remove('overflow-hidden');
                    
                    // التأكد من أن html قابل للتمرير
                    document.documentElement.style.setProperty('overflow', 'auto', 'important');
                    document.documentElement.style.setProperty('overflow-y', 'auto', 'important');
                    document.documentElement.style.setProperty('overflow-x', 'hidden', 'important');
                }
            }
            
            // تفعيل التمرير فوراً
            enableScrolling();
            
            // تفعيل التمرير عند تحميل الصفحة
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    enableScrolling();
                    setTimeout(enableScrolling, 100);
                    setTimeout(enableScrolling, 500);
                });
            } else {
                enableScrolling();
                setTimeout(enableScrolling, 100);
                setTimeout(enableScrolling, 500);
            }
            
            // تفعيل التمرير عند تحميل الصفحة بالكامل
            window.addEventListener('load', function() {
                enableScrolling();
                setTimeout(enableScrolling, 100);
            });
            
            // التأكد من تفعيل التمرير عند تغيير حجم النافذة
            window.addEventListener('resize', function() {
                setTimeout(enableScrolling, 100);
            });
            
            // مراقبة مستمرة لضمان تفعيل التمرير
            setInterval(function() {
                const mobileMenu = document.getElementById('mobile-menu-sidebar');
                const isMenuOpen = mobileMenu && (mobileMenu.style.display === 'block' || window.getComputedStyle(mobileMenu).display === 'block');
                if (!isMenuOpen) {
                    const computedStyle = window.getComputedStyle(document.body);
                    if (computedStyle.overflow === 'hidden' || computedStyle.position === 'fixed') {
                        enableScrolling();
                    }
                }
            }, 1000);
            
            // التأكد من أن wheel events تعمل بشكل صحيح
            document.addEventListener('wheel', function(e) {
                const mobileMenu = document.getElementById('mobile-menu-sidebar');
                const isMenuOpen = mobileMenu && (mobileMenu.style.display === 'block' || window.getComputedStyle(mobileMenu).display === 'block');
                
                if (!isMenuOpen) {
                    // التأكد من أن التمرير مفعّل
                    const computedStyle = window.getComputedStyle(document.body);
                    if (computedStyle.overflow === 'hidden' || computedStyle.position === 'fixed') {
                        enableScrolling();
                    }
                }
            }, { passive: true });
            
            // التأكد من تفعيل التمرير عند النقر في أي مكان (إذا كانت القائمة مغلقة)
            document.addEventListener('click', function() {
                setTimeout(enableScrolling, 50);
            }, { passive: true });
            
            // التأكد من تفعيل التمرير عند تحريك الماوس
            document.addEventListener('mousemove', function() {
                setTimeout(enableScrolling, 100);
            }, { passive: true });
        })();
        
        // حل نهائي وقوي للتمرير - يتم تنفيذه بعد تحميل كل شيء
        (function() {
            'use strict';
            
            function forceScrollEnable() {
                const mobileMenu = document.getElementById('mobile-menu-sidebar');
                const isMenuOpen = mobileMenu && (mobileMenu.style.display === 'block' || window.getComputedStyle(mobileMenu).display === 'block');
                
                if (!isMenuOpen) {
                    // إجبار تفعيل التمرير على html
                    document.documentElement.style.setProperty('overflow', 'auto', 'important');
                    document.documentElement.style.setProperty('overflow-y', 'auto', 'important');
                    document.documentElement.style.setProperty('overflow-x', 'hidden', 'important');
                    document.documentElement.style.setProperty('height', 'auto', 'important');
                    document.documentElement.style.setProperty('position', 'relative', 'important');
                    
                    // إجبار تفعيل التمرير على body
                    document.body.style.setProperty('overflow', 'auto', 'important');
                    document.body.style.setProperty('overflow-y', 'auto', 'important');
                    document.body.style.setProperty('overflow-x', 'hidden', 'important');
                    document.body.style.setProperty('position', 'relative', 'important');
                    document.body.style.setProperty('height', 'auto', 'important');
                    document.body.style.setProperty('width', '100%', 'important');
                    document.body.classList.remove('overflow-hidden');
                    
                    // إزالة أي inline styles تعطل التمرير
                    const bodyStyle = document.body.getAttribute('style') || '';
                    if (bodyStyle.includes('overflow: hidden') || bodyStyle.includes('position: fixed')) {
                        document.body.removeAttribute('style');
                        document.body.style.setProperty('overflow', 'auto', 'important');
                        document.body.style.setProperty('overflow-y', 'auto', 'important');
                        document.body.style.setProperty('position', 'relative', 'important');
                    }
                }
            }
            
            // تنفيذ فوراً
            forceScrollEnable();
            
            // تنفيذ بعد تحميل DOM
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    forceScrollEnable();
                    setTimeout(forceScrollEnable, 100);
                    setTimeout(forceScrollEnable, 500);
                });
            } else {
                forceScrollEnable();
                setTimeout(forceScrollEnable, 100);
                setTimeout(forceScrollEnable, 500);
            }
            
            // تنفيذ بعد تحميل الصفحة بالكامل
            window.addEventListener('load', function() {
                forceScrollEnable();
                setTimeout(forceScrollEnable, 100);
                setTimeout(forceScrollEnable, 500);
            });
            
            // مراقبة مستمرة كل 300ms
            setInterval(function() {
                forceScrollEnable();
            }, 300);
            
            // إجبار تفعيل التمرير عند أي wheel event
            function handleWheelEvent(e) {
                const mobileMenu = document.getElementById('mobile-menu-sidebar');
                const isMenuOpen = mobileMenu && (mobileMenu.style.display === 'block' || window.getComputedStyle(mobileMenu).display === 'block');
                
                if (!isMenuOpen) {
                    forceScrollEnable();
                }
            }
            
            // إضافة wheel listeners
            window.addEventListener('wheel', handleWheelEvent, { passive: true });
            document.addEventListener('wheel', handleWheelEvent, { passive: true });
            document.body.addEventListener('wheel', handleWheelEvent, { passive: true });
            
            // التأكد عند أي scroll event
            document.addEventListener('scroll', function() {
                forceScrollEnable();
            }, { passive: true, capture: true });
        })();
    </script>
    </body>
</html><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/welcome.blade.php ENDPATH**/ ?>