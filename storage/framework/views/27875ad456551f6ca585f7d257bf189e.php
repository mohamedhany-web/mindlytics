<?php
    $__pageLocale = app()->getLocale();
    $__pageRtl = $__pageLocale === 'ar';
?>
<!DOCTYPE html>
<html lang="<?php echo e($__pageLocale); ?>" dir="<?php echo e($__pageRtl ? 'rtl' : 'ltr'); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <title><?php echo e(__('public.about_page_title')); ?> - <?php echo e(__('public.site_suffix')); ?></title>

        <!-- خط عربي موحّد مع الصفحة الرئيسية -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <style>
            * {
                font-family: 'Tajawal', 'Cairo', sans-serif;
            }

            body {
                overflow-x: hidden;
                background: #f8fafc;
                width: 100%;
                max-width: 100vw;
                position: relative;
                padding-top: 0 !important;
                margin-top: 0 !important;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }
            
            html {
                margin: 0;
                padding: 0;
            }
            
            body > * {
                flex-shrink: 0;
            }
            
            main {
                flex: 1 0 auto;
            }

            html {
                overflow-x: hidden;
                scroll-behavior: smooth;
            }

            * {
                box-sizing: border-box;
            }

            /* Navbar موحّد مع الصفحة الرئيسية */
            .navbar-gradient {
                background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 45%, #1d4ed8 100%);
                box-shadow: 0 1px 0 rgba(255, 255, 255, 0.08);
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1000;
                transition: box-shadow 0.25s ease, background 0.25s ease;
                backdrop-filter: blur(12px) saturate(140%);
                -webkit-backdrop-filter: blur(12px) saturate(140%);
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            }
            .navbar-gradient.scrolled {
                box-shadow: 0 4px 24px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(255, 255, 255, 0.06);
                background: linear-gradient(135deg, rgba(30, 64, 175, 0.97) 0%, rgba(30, 58, 138, 0.98) 50%, rgba(29, 78, 216, 0.97) 100%);
                backdrop-filter: blur(16px) saturate(150%);
                -webkit-backdrop-filter: blur(16px) saturate(150%);
                border-bottom-color: rgba(255, 255, 255, 0.1);
            }

            /* Mobile Menu Styles */
            @media (max-width: 1023px) {
                body.overflow-hidden {
                    overflow: hidden !important;
                    position: fixed !important;
                    width: 100% !important;
                }
                
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

            /* Enhanced Hero Section - Matches courses page */
            .hero-section {
                background: linear-gradient(to bottom, #f0f9ff, #e0f2fe, #ffffff);
                position: relative;
                overflow: hidden;
            }

            .animated-background {
                position: absolute;
                inset: 0;
                overflow: hidden;
                z-index: 0;
                pointer-events: none;
            }

            /* Floating Circles */
            .floating-circle {
                position: absolute;
                border-radius: 50%;
                filter: blur(40px);
                animation: floatCircle 20s ease-in-out infinite;
                will-change: transform, opacity;
            }

            .circle-1 {
                width: 400px;
                height: 400px;
                top: 10%;
                right: 10%;
                animation-delay: 0s;
                background: radial-gradient(circle, rgba(59, 130, 246, 0.3), rgba(59, 130, 246, 0.12), transparent);
            }

            .circle-2 {
                width: 300px;
                height: 300px;
                bottom: 20%;
                right: 25%;
                animation-delay: 2s;
                background: radial-gradient(circle, rgba(16, 185, 129, 0.3), rgba(16, 185, 129, 0.12), transparent);
            }

            .circle-3 {
                width: 350px;
                height: 350px;
                top: 60%;
                left: 5%;
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
            }

            .code-symbol-1 { top: 20%; left: 10%; animation-delay: 0s; color: rgba(59, 130, 246, 0.06); }
            .code-symbol-2 { top: 70%; right: 20%; animation-delay: 2s; color: rgba(16, 185, 129, 0.06); }
            .code-symbol-3 { top: 40%; right: 15%; animation-delay: 4s; color: rgba(59, 130, 246, 0.05); }
            .code-symbol-4 { bottom: 25%; left: 25%; animation-delay: 6s; color: rgba(16, 185, 129, 0.05); }
            .code-symbol-5 { top: 15%; right: 40%; animation-delay: 8s; color: rgba(59, 130, 246, 0.06); }
            .code-symbol-6 { top: 55%; left: 50%; animation-delay: 1s; color: rgba(16, 185, 129, 0.06); }

            @keyframes floatCodeSymbol {
                0%, 100% { 
                    transform: translate(0, 0) rotate(0deg) scale(1);
                    opacity: 0.08;
                }
                25% { 
                    transform: translate(60px, -60px) rotate(3deg) scale(1.02);
                    opacity: 0.1;
                }
                50% { 
                    transform: translate(-40px, 40px) rotate(-3deg) scale(0.98);
                    opacity: 0.09;
                }
                75% { 
                    transform: translate(30px, -30px) rotate(2deg) scale(1.01);
                    opacity: 0.095;
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
                will-change: transform, opacity;
            }

            .particle-1 { top: 10%; left: 20%; animation-delay: 0s; background: rgba(59, 130, 246, 0.7); }
            .particle-2 { top: 30%; right: 25%; animation-delay: 1s; background: rgba(16, 185, 129, 0.7); }
            .particle-3 { top: 50%; left: 10%; animation-delay: 2s; background: rgba(59, 130, 246, 0.7); }
            .particle-4 { bottom: 30%; right: 15%; animation-delay: 3s; background: rgba(16, 185, 129, 0.7); }
            .particle-5 { top: 70%; left: 40%; animation-delay: 4s; background: rgba(59, 130, 246, 0.65); }
            .particle-6 { top: 25%; right: 50%; animation-delay: 5s; background: rgba(16, 185, 129, 0.7); }
            .particle-7 { bottom: 20%; left: 30%; animation-delay: 6s; background: rgba(16, 185, 129, 0.65); }
            .particle-8 { top: 80%; right: 30%; animation-delay: 7s; background: rgba(59, 130, 246, 0.7); }

            @keyframes floatParticle {
                0%, 100% {
                    transform: translate(0, 0) scale(1) rotate(0deg);
                    opacity: 0.7;
                }
                20% {
                    transform: translate(120px, -120px) scale(2.2) rotate(180deg);
                    opacity: 1;
                }
                40% {
                    transform: translate(-70px, 70px) scale(0.6) rotate(-180deg);
                    opacity: 0.5;
                }
                60% {
                    transform: translate(80px, 80px) scale(1.8) rotate(90deg);
                    opacity: 0.95;
                }
                80% {
                    transform: translate(-50px, -50px) scale(1.2) rotate(-90deg);
                    opacity: 0.8;
                }
            }

            /* Floating Lines */
            .floating-line {
                position: absolute;
                background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.4), rgba(16, 185, 129, 0.3), rgba(59, 130, 246, 0.4), transparent);
                height: 3px;
                animation: floatLine 20s linear infinite;
                will-change: transform, opacity;
            }

            .line-1 { width: 300px; top: 25%; left: 0; transform: rotate(45deg); animation-delay: 0s; }
            .line-2 { width: 250px; top: 65%; right: 0; transform: rotate(-45deg); animation-delay: 5s; background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.3), transparent); }
            .line-3 { width: 200px; top: 45%; left: 50%; transform: rotate(90deg); animation-delay: 10s; background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.4), transparent); }

            @keyframes floatLine {
                0% { transform: translateX(-100%) translateY(0); opacity: 0; }
                10% { opacity: 0.8; }
                90% { opacity: 0.8; }
                100% { transform: translateX(200%) translateY(150px); opacity: 0; }
            }

            /* Hero Glow */
            .hero-glow {
                position: absolute;
                top: 1/2;
                left: 1/2;
                transform: translate(-50%, -50%);
                width: 800px;
                height: 800px;
                background: radial-gradient(circle, rgba(59, 130, 246, 0.2), rgba(16, 185, 129, 0.1), transparent);
                border-radius: 50%;
                filter: blur(100px);
                animation: pulseGlow 4s ease-in-out infinite;
            }

            @keyframes pulseGlow {
                0%, 100% {
                    opacity: 0.6;
                    transform: translate(-50%, -50%) scale(1);
                }
                50% {
                    opacity: 0.8;
                    transform: translate(-50%, -50%) scale(1.1);
                }
            }

            /* Gradient Text Animation */
            .animate-gradient-text {
                background-size: 200% auto;
                background-clip: text;
                -webkit-background-clip: text;
                animation: gradientText 3s ease infinite;
            }

            @keyframes gradientText {
                0%, 100% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
            }

            /* Course Card Styles - Matches courses page */
            .course-card {
                transition: all 0.3s ease;
                background: #ffffff;
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
                height: 3px;
                background: linear-gradient(90deg, #3b82f6, #10b981);
                opacity: 0;
                transition: opacity 0.3s ease;
                z-index: 1;
            }

            .course-card:hover::before {
                opacity: 1;
            }

            .course-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 40px rgba(59, 130, 246, 0.15), 0 0 20px rgba(16, 185, 129, 0.1);
                border-color: rgba(59, 130, 246, 0.3);
            }

            .course-card .course-image {
                transition: transform 0.3s ease;
                position: relative;
            }

            .course-card .course-image::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(16, 185, 129, 0.1));
                opacity: 0;
                transition: opacity 0.3s ease;
                z-index: 1;
            }

            .course-card:hover .course-image {
                transform: scale(1.05);
            }

            .course-card:hover .course-image::before {
                opacity: 1;
            }

            .course-card:hover .course-image i {
                transform: scale(1.1);
            }

            /* Stat Card Styles */
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

            /* Fade in animations (hero / initial) */
            .fade-in-up {
                animation: fadeInUp 0.5s ease-out forwards;
                opacity: 0;
            }

            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .fade-in-left {
                animation: fadeInLeft 0.6s ease-out forwards;
                opacity: 0;
            }

            @keyframes fadeInLeft {
                from { opacity: 0; transform: translateX(-30px); }
                to { opacity: 1; transform: translateX(0); }
            }

            .fade-in-right {
                animation: fadeInRight 0.6s ease-out forwards;
                opacity: 0;
            }

            @keyframes fadeInRight {
                from { opacity: 0; transform: translateX(30px); }
                to { opacity: 1; transform: translateX(0); }
            }

            .fade-in {
                animation: fadeIn 1s ease-out;
            }

            @keyframes fadeIn {
                0% { opacity: 0; transform: translateY(30px); }
                100% { opacity: 1; transform: translateY(0); }
            }

            /* ─── Scroll reveal animations (عرض مع انيميشن عند التمرير) ─── */
            .reveal {
                opacity: 0;
                transform: translateY(36px);
                transition: opacity 0.75s cubic-bezier(0.22, 1, 0.36, 1), transform 0.75s cubic-bezier(0.22, 1, 0.36, 1);
            }
            .reveal.revealed {
                opacity: 1;
                transform: translateY(0);
            }

            .reveal-from-right {
                opacity: 0;
                transform: translate(40px, 20px);
                transition: opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1), transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
            }
            .reveal-from-right.revealed {
                opacity: 1;
                transform: translate(0, 0);
            }

            .reveal-from-left {
                opacity: 0;
                transform: translate(-40px, 20px);
                transition: opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1), transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
            }
            .reveal-from-left.revealed {
                opacity: 1;
                transform: translate(0, 0);
            }

            .reveal-scale {
                opacity: 0;
                transform: translateY(24px) scale(0.97);
                transition: opacity 0.8s cubic-bezier(0.22, 1, 0.36, 1), transform 0.8s cubic-bezier(0.22, 1, 0.36, 1);
            }
            .reveal-scale.revealed {
                opacity: 1;
                transform: translateY(0) scale(1);
            }

            /* خط الاقتباس يظهر تدريجياً من أعلى لأسفل */
            .reveal-quote {
                opacity: 0;
                transform: translateY(24px);
                transition: opacity 0.65s cubic-bezier(0.22, 1, 0.36, 1), transform 0.65s cubic-bezier(0.22, 1, 0.36, 1);
            }
            .reveal-quote .quote-line {
                transform: scaleY(0);
                transform-origin: top;
                transition: transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
                transition-delay: 0.25s;
            }
            .reveal-quote.revealed {
                opacity: 1;
                transform: translateY(0);
            }
            .reveal-quote.revealed .quote-line {
                transform: scaleY(1);
            }

            /* عنوان القسم: يظهر مع خط تحته يرسم */
            .reveal-heading {
                opacity: 0;
                transform: translateY(20px);
                transition: opacity 0.5s ease-out, transform 0.5s ease-out;
            }
            .reveal-heading .heading-underline {
                transform: scaleX(0);
                transform-origin: right;
                transition: transform 0.6s cubic-bezier(0.22, 1, 0.36, 1);
                transition-delay: 0.15s;
            }
            .reveal-heading.revealed {
                opacity: 1;
                transform: translateY(0);
            }
            .reveal-heading.revealed .heading-underline {
                transform: scaleX(1);
                transform-origin: left;
            }

            /* تأخير متتابع للأطفال */
            .reveal-stagger > * {
                opacity: 0;
                transform: translateY(28px);
                transition: opacity 0.6s cubic-bezier(0.22, 1, 0.36, 1), transform 0.6s cubic-bezier(0.22, 1, 0.36, 1);
            }
            .reveal-stagger.revealed > *:nth-child(1) { transition-delay: 0s; }
            .reveal-stagger.revealed > *:nth-child(2) { transition-delay: 0.08s; }
            .reveal-stagger.revealed > *:nth-child(3) { transition-delay: 0.16s; }
            .reveal-stagger.revealed > *:nth-child(4) { transition-delay: 0.24s; }
            .reveal-stagger.revealed > *:nth-child(5) { transition-delay: 0.32s; }
            .reveal-stagger.revealed > *:nth-child(6) { transition-delay: 0.4s; }
            .reveal-stagger.revealed > *:nth-child(7) { transition-delay: 0.48s; }
            .reveal-stagger.revealed > *:nth-child(8) { transition-delay: 0.56s; }
            .reveal-stagger.revealed > *:nth-child(9) { transition-delay: 0.64s; }
            .reveal-stagger.revealed > *:nth-child(10) { transition-delay: 0.72s; }
            .reveal-stagger.revealed > * {
                opacity: 1;
                transform: translateY(0);
            }

            /* Gradient Text */
            .gradient-text {
                background: linear-gradient(135deg, #3b82f6, #10b981, #8b5cf6, #3b82f6);
                background-size: 300% 300%;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                animation: gradientShift 5s ease infinite;
            }

            @keyframes gradientShift {
                0%, 100% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
            }

            /* Section Title */
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

            /* Counter Animation */
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

            @media (max-width: 1024px) {
                .floating-code-symbol {
                    font-size: 1rem;
                    opacity: 0.06;
                }
                
                .floating-line {
                    display: none;
                }
                
                .floating-circle {
                    filter: blur(30px);
                    animation-duration: 18s;
                }
            }

            @media (max-width: 768px) {
                .floating-code-symbol {
                    font-size: 0.85rem;
                    opacity: 0.05;
                }
                
                .floating-circle {
                    width: 150px !important;
                    height: 150px !important;
                    filter: blur(20px);
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
                
                .floating-particle {
                    width: 8px;
                    height: 8px;
                    animation-duration: 12s;
                }
            }

            [x-cloak] { display: none !important; }
        </style>
    </head>

<body class="bg-gray-50 text-gray-900"
      x-data="{ mobileMenu: false, searchQuery: '' }"
      :class="{ 'overflow-hidden': mobileMenu }">

    <?php echo $__env->make('components.unified-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <main>

    <!-- Hero Section -->
    <section class="hero-section relative overflow-hidden min-h-[85vh] flex items-center">
        <!-- Animated Background -->
        <div class="animated-background absolute inset-0 overflow-hidden">
            <!-- Floating Circles -->
            <div class="floating-circle circle-1"></div>
            <div class="floating-circle circle-2"></div>
            <div class="floating-circle circle-3"></div>
            <div class="floating-circle circle-4"></div>
            <div class="floating-circle circle-5"></div>
            
            <!-- Floating Code Symbols -->
            <div class="floating-code-symbol code-symbol-1">&lt;/&gt;</div>
            <div class="floating-code-symbol code-symbol-2">{ }</div>
            <div class="floating-code-symbol code-symbol-3">( )</div>
            <div class="floating-code-symbol code-symbol-4">[ ]</div>
            <div class="floating-code-symbol code-symbol-5">#</div>
            <div class="floating-code-symbol code-symbol-6">$</div>
            
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
        
        <!-- Hero Glow -->
        <div class="hero-glow absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-gradient-radial from-blue-400/20 via-green-400/10 to-transparent rounded-full blur-3xl"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full py-16">
            <div class="text-center fade-in-up">
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black mb-6 leading-tight text-gray-900">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-green-500 to-blue-600 animate-gradient-text"><?php echo e(__('public.about_hero')); ?></span>
                </h1>
                <p class="text-lg md:text-xl lg:text-2xl text-gray-700 mb-10 leading-relaxed max-w-3xl mx-auto font-medium">
                    <?php echo e(__('public.about_hero_sub')); ?>

                </p>
            </div>
        </div>
    </section>

    <!-- من نحن - نص بعرض كامل وتنسيق واضح -->
    <section class="py-16 md:py-24 bg-white">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="reveal-heading text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 mb-10 md:mb-14">
                <?php echo e(__('public.about_heading')); ?>

                <span class="heading-underline block h-1 w-28 mt-2 bg-gradient-to-l from-blue-600 to-green-500 rounded-full"></span>
            </h2>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
                <div class="lg:col-span-7 reveal-from-right space-y-6">
                    <p class="text-lg md:text-xl text-gray-700 leading-[1.85]">
                        <?php echo __('public.about_para1', ['brand' => '<strong class="text-blue-600 font-black">Mindlytics</strong>']); ?>

                    </p>
                    <p class="text-lg md:text-xl text-gray-700 leading-[1.85]">
                        <?php echo e(__('public.about_para2')); ?>

                    </p>
                </div>
                <div class="lg:col-span-5 reveal-from-left flex justify-center lg:justify-start">
                    <div class="w-full max-w-sm aspect-square rounded-3xl bg-gradient-to-br from-blue-100 via-green-50/80 to-blue-100 flex items-center justify-center shadow-2xl shadow-blue-200/30 border border-blue-100/50">
                        <i class="fas fa-graduation-cap text-blue-400/60 text-7xl md:text-8xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- الرؤية والمهمة - كروت بعرض الصفحة -->
    <section class="py-16 md:py-24 bg-gradient-to-b from-slate-50 to-white">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-10">
                <div class="reveal-scale rounded-3xl bg-white p-8 md:p-10 shadow-xl shadow-blue-100/40 border border-blue-50 hover:shadow-2xl hover:shadow-blue-200/30 transition-all duration-500 overflow-hidden relative group">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-l from-blue-600 to-blue-400 opacity-90"></div>
                    <div class="flex gap-6 items-start">
                        <span class="flex-shrink-0 w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-600 to-blue-500 text-white flex items-center justify-center shadow-lg shadow-blue-500/40 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-eye text-2xl"></i>
                        </span>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-2xl md:text-3xl font-black text-gray-900 mb-4"><?php echo e(__('public.our_vision')); ?></h3>
                            <p class="text-lg text-gray-700 leading-relaxed">
                                <?php echo e(__('public.vision_text')); ?>

                            </p>
                        </div>
                    </div>
                </div>
                <div class="reveal-scale rounded-3xl bg-white p-8 md:p-10 shadow-xl shadow-green-100/40 border border-green-50 hover:shadow-2xl hover:shadow-green-200/30 transition-all duration-500 overflow-hidden relative group">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-l from-green-600 to-green-400 opacity-90"></div>
                    <div class="flex gap-6 items-start">
                        <span class="flex-shrink-0 w-16 h-16 rounded-2xl bg-gradient-to-br from-green-600 to-green-500 text-white flex items-center justify-center shadow-lg shadow-green-500/40 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-bullseye text-2xl"></i>
                        </span>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-2xl md:text-3xl font-black text-gray-900 mb-4"><?php echo e(__('public.our_mission')); ?></h3>
                            <p class="text-lg text-gray-700 leading-relaxed mb-5"><?php echo e(__('public.mission_intro')); ?></p>
                            <ul class="space-y-3 text-gray-700">
                                <li class="flex items-center gap-3 text-base"><span class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></span> <?php echo e(__('public.mission_1')); ?></li>
                                <li class="flex items-center gap-3 text-base"><span class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></span> <?php echo e(__('public.mission_2')); ?></li>
                                <li class="flex items-center gap-3 text-base"><span class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></span> <?php echo e(__('public.mission_3')); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- لماذا نحن - شبكة 2x2 بعرض كامل -->
    <section class="py-16 md:py-24 bg-white">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="reveal text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 mb-12 md:mb-16 text-center">
                <?php echo e(__('public.why_mindlytics')); ?>

            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 reveal-stagger">
                <div class="rounded-2xl bg-gradient-to-br from-blue-50 to-white p-6 md:p-8 border border-blue-100/80 hover:border-blue-200 hover:shadow-lg transition-all duration-300 h-full flex flex-col">
                    <div class="w-14 h-14 rounded-xl bg-blue-600 text-white flex items-center justify-center mb-5 shadow-lg shadow-blue-500/30">
                        <i class="fas fa-code text-xl"></i>
                    </div>
                    <h4 class="text-xl font-black text-gray-900 mb-2"><?php echo e(__('public.why_1_title')); ?></h4>
                    <p class="text-gray-600 leading-relaxed flex-1"><?php echo e(__('public.why_1_desc')); ?></p>
                </div>
                <div class="rounded-2xl bg-gradient-to-br from-green-50 to-white p-6 md:p-8 border border-green-100/80 hover:border-green-200 hover:shadow-lg transition-all duration-300 h-full flex flex-col">
                    <div class="w-14 h-14 rounded-xl bg-green-600 text-white flex items-center justify-center mb-5 shadow-lg shadow-green-500/30">
                        <i class="fas fa-user-tie text-xl"></i>
                    </div>
                    <h4 class="text-xl font-black text-gray-900 mb-2"><?php echo e(__('public.why_2_title')); ?></h4>
                    <p class="text-gray-600 leading-relaxed flex-1"><?php echo e(__('public.why_2_desc')); ?></p>
                </div>
                <div class="rounded-2xl bg-gradient-to-br from-blue-50 to-white p-6 md:p-8 border border-blue-100/80 hover:border-blue-200 hover:shadow-lg transition-all duration-300 h-full flex flex-col">
                    <div class="w-14 h-14 rounded-xl bg-blue-600 text-white flex items-center justify-center mb-5 shadow-lg shadow-blue-500/30">
                        <i class="fas fa-headset text-xl"></i>
                    </div>
                    <h4 class="text-xl font-black text-gray-900 mb-2"><?php echo e(__('public.why_3_title')); ?></h4>
                    <p class="text-gray-600 leading-relaxed flex-1"><?php echo e(__('public.why_3_desc')); ?></p>
                </div>
                <div class="rounded-2xl bg-gradient-to-br from-green-50 to-white p-6 md:p-8 border border-green-100/80 hover:border-green-200 hover:shadow-lg transition-all duration-300 h-full flex flex-col">
                    <div class="w-14 h-14 rounded-xl bg-green-600 text-white flex items-center justify-center mb-5 shadow-lg shadow-green-500/30">
                        <i class="fas fa-certificate text-xl"></i>
                    </div>
                    <h4 class="text-xl font-black text-gray-900 mb-2"><?php echo e(__('public.why_4_title')); ?></h4>
                    <p class="text-gray-600 leading-relaxed flex-1"><?php echo e(__('public.why_4_desc')); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- القيم - ثلاثة أعمدة بعرض كامل -->
    <section class="py-16 md:py-24 bg-gradient-to-b from-slate-50 to-white">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="reveal text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 mb-12 md:mb-16 text-center">
                <?php echo e(__('public.our_values')); ?>

            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-10 reveal-stagger">
                <div class="rounded-3xl bg-white p-8 md:p-10 shadow-xl shadow-slate-200/50 border border-slate-100 text-center hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <span class="inline-flex w-16 h-16 rounded-2xl bg-blue-100 text-blue-700 font-black text-2xl items-center justify-center mb-6">1</span>
                    <h4 class="text-xl md:text-2xl font-black text-gray-900 mb-3"><?php echo e(__('public.value_1_title')); ?></h4>
                    <p class="text-gray-600 leading-relaxed text-base md:text-lg"><?php echo e(__('public.value_1_desc')); ?></p>
                </div>
                <div class="rounded-3xl bg-white p-8 md:p-10 shadow-xl shadow-slate-200/50 border border-slate-100 text-center hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <span class="inline-flex w-16 h-16 rounded-2xl bg-green-100 text-green-700 font-black text-2xl items-center justify-center mb-6">2</span>
                    <h4 class="text-xl md:text-2xl font-black text-gray-900 mb-3"><?php echo e(__('public.value_2_title')); ?></h4>
                    <p class="text-gray-600 leading-relaxed text-base md:text-lg"><?php echo e(__('public.value_2_desc')); ?></p>
                </div>
                <div class="rounded-3xl bg-white p-8 md:p-10 shadow-xl shadow-slate-200/50 border border-slate-100 text-center hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <span class="inline-flex w-16 h-16 rounded-2xl bg-blue-100 text-blue-700 font-black text-2xl items-center justify-center mb-6">3</span>
                    <h4 class="text-xl md:text-2xl font-black text-gray-900 mb-3"><?php echo e(__('public.value_3_title')); ?></h4>
                    <p class="text-gray-600 leading-relaxed text-base md:text-lg"><?php echo e(__('public.value_3_desc')); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- أرقام - شريط بعرض كامل -->
    <section class="py-16 md:py-20 bg-gradient-to-r from-blue-600 via-blue-500 to-green-600 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 md:gap-12 text-center reveal-stagger">
                <div>
                    <div class="text-4xl md:text-5xl lg:text-6xl font-black text-white counter drop-shadow-lg" data-target="<?php echo e($stats['courses'] ?? 50); ?>"><?php echo e($stats['courses'] ?? 50); ?>+</div>
                    <div class="text-blue-100 font-semibold mt-2 text-lg"><?php echo e(__('public.stat_courses')); ?></div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl lg:text-6xl font-black text-white counter drop-shadow-lg" data-target="<?php echo e($stats['students'] ?? 1000); ?>"><?php echo e($stats['students'] ?? 1000); ?>+</div>
                    <div class="text-blue-100 font-semibold mt-2 text-lg"><?php echo e(__('public.stat_students')); ?></div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl lg:text-6xl font-black text-white counter drop-shadow-lg" data-target="<?php echo e($stats['instructors'] ?? 20); ?>"><?php echo e($stats['instructors'] ?? 20); ?>+</div>
                    <div class="text-blue-100 font-semibold mt-2 text-lg"><?php echo e(__('public.stat_instructors')); ?></div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl lg:text-6xl font-black text-white drop-shadow-lg">100%</div>
                    <div class="text-blue-100 font-semibold mt-2 text-lg"><?php echo e(__('public.stat_quality')); ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section - عرض كامل -->
    <section class="py-20 md:py-28 lg:py-32 bg-gradient-to-br from-blue-50 via-white to-green-50 relative overflow-hidden">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-10 left-10 w-[500px] h-[500px] bg-blue-400/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-10 right-10 w-[500px] h-[500px] bg-green-400/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-gradient-to-r from-blue-200/5 to-green-200/5 rounded-full blur-3xl"></div>
        </div>
        <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal-scale">
            <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black text-gray-900 mb-6 leading-tight max-w-4xl mx-auto">
                <?php echo e(__('public.cta_programming_title')); ?>

            </h2>
            <p class="text-lg md:text-xl lg:text-2xl text-gray-600 mb-12 font-medium max-w-2xl mx-auto">
                <?php echo e(__('public.cta_programming_desc')); ?>

            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="<?php echo e(route('register')); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-10 py-4 rounded-full font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 relative overflow-hidden group">
                    <span class="relative z-10 flex items-center gap-2">
                        <i class="fas fa-user-plus"></i>
                        <span><?php echo e(__('public.register_free_now')); ?></span>
                    </span>
                    <span class="absolute inset-0 bg-gradient-to-r from-green-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                </a>
                <a href="<?php echo e(route('public.courses')); ?>" class="inline-flex items-center justify-center gap-2 bg-white text-blue-600 px-10 py-4 rounded-full font-bold text-lg border-2 border-blue-600 hover:bg-blue-50 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl relative">
                    <span class="flex items-center gap-2">
                        <span><?php echo e(__('public.browse_all_courses_btn')); ?></span>
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

        // Scroll Reveal: ظهور الفقرات والأقسام عند التمرير
        const revealSelector = '.reveal, .reveal-from-right, .reveal-from-left, .reveal-scale, .reveal-quote, .reveal-heading, .reveal-stagger';
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.counter[data-target]').forEach(counter => {
                animateCounter(counter);
            });

            document.querySelectorAll(revealSelector).forEach(el => {
                revealObserver.observe(el);
            });

            const navbar = document.getElementById('navbar');
            if (navbar) {
                window.addEventListener('scroll', () => {
                    const should = window.pageYOffset > 100;
                    if (should) navbar.classList.add('scrolled');
                    else navbar.classList.remove('scrolled');
                }, { passive: true });
            }
        });
    </script>
</body>
</html>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/public/about.blade.php ENDPATH**/ ?>