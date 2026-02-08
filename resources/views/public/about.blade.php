<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <title>من نحن - Mindlytics - أكاديمية البرمجة</title>

        <!-- خط عربي أصيل -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&family=Noto+Sans+Arabic:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <style>
            * {
                font-family: 'Cairo', 'Noto Sans Arabic', sans-serif;
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

            /* Enhanced Navbar Styles - Same as courses page */
            .navbar-gradient {
                background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #3b82f6 100%);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1), 0 0 40px rgba(59, 130, 246, 0.2);
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1000;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                backdrop-filter: blur(20px) saturate(180%);
                border-bottom: 2px solid rgba(255, 255, 255, 0.2);
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

            /* Fade in animations */
            .fade-in-up {
                animation: fadeInUp 0.5s ease-out forwards;
                opacity: 0;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .fade-in-left {
                animation: fadeInLeft 0.6s ease-out forwards;
                opacity: 0;
            }

            @keyframes fadeInLeft {
                from {
                    opacity: 0;
                    transform: translateX(-30px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            .fade-in-right {
                animation: fadeInRight 0.6s ease-out forwards;
                opacity: 0;
            }

            @keyframes fadeInRight {
                from {
                    opacity: 0;
                    transform: translateX(30px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            .fade-in {
                animation: fadeIn 1s ease-out;
            }

            @keyframes fadeIn {
                0% { opacity: 0; transform: translateY(30px); }
                100% { opacity: 1; transform: translateY(0); }
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

    @include('components.unified-navbar')
    
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
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-green-500 to-blue-600 animate-gradient-text">من نحن</span>
                </h1>
                <p class="text-lg md:text-xl lg:text-2xl text-gray-700 mb-10 leading-relaxed max-w-3xl mx-auto font-medium">
                    نؤمن بقوة التعليم في تحويل المستقبل
                </p>
            </div>
        </div>
    </section>

    <!-- About Content -->
    <section class="py-12 md:py-16 bg-gradient-to-b from-gray-50 via-white to-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Introduction -->
            <div class="text-center mb-12 fade-in-up">
                <div class="inline-block mb-4">
                    <span class="bg-gradient-to-r from-blue-100 to-green-100 text-blue-700 px-4 py-2 rounded-full text-sm font-bold inline-flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        <span>تعرف علينا</span>
                    </span>
                </div>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 mb-4">
                    من نحن في <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-green-500">Mindlytics</span>
                </h2>
                <p class="text-lg md:text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    نحن في <span class="font-bold text-blue-600">Mindlytics</span> نقدم تعليماً برمجياً عالي الجودة يجمع بين النظرية والتطبيق العملي. نهدف إلى تخريج جيل من المبرمجين المحترفين القادرين على المنافسة في السوق العالمي.
                </p>
            </div>

            <!-- Vision & Mission -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 mb-16 auto-rows-fr">
                <!-- Vision -->
                <div class="course-card rounded-3xl overflow-hidden shadow-xl fade-in-left h-full">
                    <div class="h-48 bg-gradient-to-br from-blue-600 via-blue-500 to-green-500 flex items-center justify-center relative course-image overflow-hidden flex-shrink-0">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-black/10 to-transparent"></div>
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300" style="background: radial-gradient(circle at center, rgba(255, 255, 255, 0.15) 0%, transparent 70%);"></div>
                        <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center shadow-xl relative z-10">
                            <i class="fas fa-eye text-white text-4xl relative z-10 transition-transform duration-300 drop-shadow-lg"></i>
                        </div>
                    </div>
                    <div class="p-6 bg-white flex-grow flex flex-col justify-between">
                        <div>
                            <h3 class="text-2xl md:text-3xl font-black text-gray-900 mb-4">رؤيتنا</h3>
                            <p class="text-gray-700 text-base md:text-lg leading-relaxed">
                                نطمح لأن نكون الرائدين في مجال التعليم البرمجي في المنطقة، حيث نقدم تعليماً عالي الجودة يجمع بين النظرية والتطبيق العملي لتخريج مبرمجين محترفين.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Mission -->
                <div class="course-card rounded-3xl overflow-hidden shadow-xl fade-in-right h-full">
                    <div class="h-48 bg-gradient-to-br from-green-600 via-green-500 to-blue-500 flex items-center justify-center relative course-image overflow-hidden flex-shrink-0">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-black/10 to-transparent"></div>
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300" style="background: radial-gradient(circle at center, rgba(255, 255, 255, 0.15) 0%, transparent 70%);"></div>
                        <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center shadow-xl relative z-10">
                            <i class="fas fa-bullseye text-white text-4xl relative z-10 transition-transform duration-300 drop-shadow-lg"></i>
                        </div>
                    </div>
                    <div class="p-6 bg-white flex-grow flex flex-col justify-between">
                        <div>
                            <h3 class="text-2xl md:text-3xl font-black text-gray-900 mb-4">مهمتنا</h3>
                            <p class="text-gray-700 text-base md:text-lg leading-relaxed mb-4">
                                مهمتنا هي تقديم أفضل تجربة تعليمية في البرمجة والتطوير، مع التركيز على:
                            </p>
                            <ul class="space-y-3">
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check-circle text-green-500 ml-3 text-lg"></i>
                                    <span>تعليم عملي يواكب أحدث التقنيات</span>
                                </li>
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check-circle text-green-500 ml-3 text-lg"></i>
                                    <span>دعم مستمر ومتابعة شخصية</span>
                                </li>
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check-circle text-green-500 ml-3 text-lg"></i>
                                    <span>شهادات معتمدة ومعترف بها</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Why Choose Us -->
            <div class="text-center mb-12 fade-in-up">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 mb-4">
                    لماذا <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-green-500">Mindlytics</span>؟
                </h2>
                <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto">
                    منصة تعليمية متكاملة تجمع بين أفضل المحتوى التعليمي والتقنيات الحديثة
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 mb-16 auto-rows-fr">
                <div class="course-card text-center p-6 md:p-8 rounded-3xl fade-in-left h-full flex flex-col items-center justify-center group">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-code text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-black text-gray-900 mb-3">محتوى حديث ومتطور</h3>
                    <p class="text-gray-600 text-sm md:text-base leading-relaxed">نقدم محتوى تعليمي محدث باستمرار ليتوافق مع أحدث التقنيات والمتطلبات في سوق العمل</p>
                </div>
                
                <div class="course-card text-center p-6 md:p-8 rounded-3xl fade-in-up h-full flex flex-col items-center justify-center group">
                    <div class="w-20 h-20 bg-gradient-to-br from-green-600 to-green-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-user-tie text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-black text-gray-900 mb-3">مدربون محترفون</h3>
                    <p class="text-gray-600 text-sm md:text-base leading-relaxed">فريق من المدربين المحترفين ذوي الخبرة الواسعة في المجال البرمجي</p>
                </div>
                
                <div class="course-card text-center p-6 md:p-8 rounded-3xl fade-in-up h-full flex flex-col items-center justify-center group">
                    <div class="w-20 h-20 bg-gradient-to-br from-purple-600 to-purple-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-headset text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-black text-gray-900 mb-3">دعم فني مستمر</h3>
                    <p class="text-gray-600 text-sm md:text-base leading-relaxed">نوفر دعم فني وتعليمي مستمر على مدار الساعة لمساعدتك في رحلتك التعليمية</p>
                </div>
                
                <div class="course-card text-center p-6 md:p-8 rounded-3xl fade-in-right h-full flex flex-col items-center justify-center group">
                    <div class="w-20 h-20 bg-gradient-to-br from-orange-600 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-certificate text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-black text-gray-900 mb-3">شهادات معتمدة</h3>
                    <p class="text-gray-600 text-sm md:text-base leading-relaxed">احصل على شهادات معتمدة ومعترف بها عند إتمامك للكورسات بنجاح</p>
                </div>
            </div>

            <!-- Values -->
            <div class="text-center mb-12 fade-in-up">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 mb-4">
                    قيمنا <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-green-500">الأساسية</span>
                </h2>
                <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto">
                    القيم التي نؤمن بها ونسير عليها في رحلتنا التعليمية
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 mb-16 auto-rows-fr">
                <div class="course-card text-center p-6 md:p-8 rounded-3xl fade-in-left h-full flex flex-col items-center justify-center group">
                    <div class="w-24 h-24 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <i class="fas fa-lightbulb text-white text-4xl"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-black text-gray-900 mb-3">الابتكار</h3>
                    <p class="text-gray-600 text-sm md:text-base leading-relaxed">نواكب أحدث التقنيات والمناهج التعليمية العالمية لنقدم تجربة تعليمية متطورة</p>
                </div>
                
                <div class="course-card text-center p-6 md:p-8 rounded-3xl fade-in-up h-full flex flex-col items-center justify-center group">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-600 to-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <i class="fas fa-award text-white text-4xl"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-black text-gray-900 mb-3">الجودة</h3>
                    <p class="text-gray-600 text-sm md:text-base leading-relaxed">نلتزم بأعلى معايير الجودة في التعليم والتدريب لتخريج مبرمجين محترفين</p>
                </div>
                
                <div class="course-card text-center p-6 md:p-8 rounded-3xl fade-in-right h-full flex flex-col items-center justify-center group">
                    <div class="w-24 h-24 bg-gradient-to-br from-green-600 to-green-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <i class="fas fa-heart text-white text-4xl"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-black text-gray-900 mb-3">الشغف</h3>
                    <p class="text-gray-600 text-sm md:text-base leading-relaxed">نحب ما نفعله ونؤمن بقوة التعليم في تحويل حياة الطلاب وتطوير مهاراتهم</p>
                </div>
            </div>

            <!-- Stats -->
            <div class="text-center mb-12 fade-in-up">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 mb-4">
                    إحصائياتنا
                </h2>
                <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto">
                    أرقام تتحدث عن نفسها
                </p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6 max-w-5xl mx-auto mb-16">
                <div class="stat-card p-6 fade-in-up" style="animation-delay: 0.1s;">
                    <div class="counter-wrapper">
                        <div class="text-3xl md:text-4xl font-black text-blue-600 mb-2 counter" data-target="{{ $stats['courses'] ?? 50 }}">{{ $stats['courses'] ?? 50 }}+</div>
                    </div>
                    <div class="text-gray-600 font-medium text-sm md:text-base">كورس متاح</div>
                </div>
                
                <div class="stat-card p-6 fade-in-up" style="animation-delay: 0.2s;">
                    <div class="counter-wrapper">
                        <div class="text-3xl md:text-4xl font-black text-blue-600 mb-2 counter" data-target="{{ $stats['students'] ?? 1000 }}">{{ $stats['students'] ?? 1000 }}+</div>
                    </div>
                    <div class="text-gray-600 font-medium text-sm md:text-base">طالب نشط</div>
                </div>
                
                <div class="stat-card p-6 fade-in-up" style="animation-delay: 0.3s;">
                    <div class="counter-wrapper">
                        <div class="text-3xl md:text-4xl font-black text-blue-600 mb-2 counter" data-target="{{ $stats['instructors'] ?? 20 }}">{{ $stats['instructors'] ?? 20 }}+</div>
                    </div>
                    <div class="text-gray-600 font-medium text-sm md:text-base">مدرس محترف</div>
                </div>
                
                <div class="stat-card p-6 fade-in-up" style="animation-delay: 0.4s;">
                    <div class="counter-wrapper">
                        <div class="text-3xl md:text-4xl font-black text-blue-600 mb-2">100%</div>
                    </div>
                    <div class="text-gray-600 font-medium text-sm md:text-base">رضا العملاء</div>
                </div>
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
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-8 py-4 rounded-full font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 relative overflow-hidden group">
                    <span class="relative z-10 flex items-center gap-2">
                        <i class="fas fa-user-plus"></i>
                        <span>سجل مجاناً الآن</span>
                    </span>
                    <span class="absolute inset-0 bg-gradient-to-r from-green-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                </a>
                <a href="{{ route('public.courses') }}" class="inline-flex items-center justify-center gap-2 bg-white text-blue-600 px-8 py-4 rounded-full font-bold text-lg border-2 border-blue-600 hover:bg-blue-50 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl relative">
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
    @include('components.unified-footer')

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

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.counter').forEach(counter => {
                animateCounter(counter);
            });

            document.querySelectorAll('.fade-in-up, .fade-in-left, .fade-in-right').forEach(el => {
                fadeObserver.observe(el);
            });
        });
    </script>
</body>
</html>
