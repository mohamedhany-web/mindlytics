@php
    $__pageLocale = app()->getLocale();
    $__pageRtl = $__pageLocale === 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ $__pageLocale }}" dir="{{ $__pageRtl ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>{{ __('public.courses_page_title') }} - {{ __('public.site_suffix') }}</title>
    <x-tracking-tags placement="head" />

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

            /* Enhanced Hero Section - Matches welcome page */
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

            /* Floating Lines */
            .floating-line {
                position: absolute;
                background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.4), rgba(16, 185, 129, 0.3), rgba(59, 130, 246, 0.4), transparent);
                height: 3px;
                animation: floatLine 20s linear infinite;
                will-change: transform, opacity;
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
                    transform: translateX(-100%) translateY(0);
                    opacity: 0;
                }
                10% {
                    opacity: 0.8;
                }
                90% {
                    opacity: 0.8;
                }
                100% {
                    transform: translateX(200%) translateY(150px);
                    opacity: 0;
                }
            }

            /* Hero Glow */
            .hero-glow {
                position: absolute;
                animation: pulseGlow 4s ease-in-out infinite;
                filter: blur(80px);
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
                0%, 100% {
                    background-position: 0% 50%;
                }
                50% {
                    background-position: 100% 50%;
                }
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

            /* Course Card Styles - مثل كاردات الصفحة الرئيسية */
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
                border-radius: 1.5rem;
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
                pointer-events: none;
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
                pointer-events: none;
            }
            
            .course-card .course-image img {
                z-index: 0 !important;
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            .course-card .course-image:has(img) {
                background: transparent !important;
            }
            
            .course-card .course-image:has(img)::before {
                display: none !important;
            }
            
            .course-card .course-image img + * {
                z-index: 1 !important;
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

            .group\/btn:hover .btn-shimmer {
                animation: shimmer 3s infinite;
            }

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
            .card-hover {
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                position: relative;
                overflow: hidden;
            }
            .card-hover:hover {
                transform: translateY(-15px) scale(1.02);
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            }
            .btn-primary {
                background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 35%, #0369a1 60%, #475569 80%, #dc2626 100%);
                color: white;
                padding: 15px 40px;
                border-radius: 50px;
                font-weight: 600;
                font-size: 1.1rem;
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                border: none;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 10px;
                text-decoration: none;
                position: relative;
                overflow: hidden;
                box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
            }
            .btn-primary:hover {
                transform: translateY(-3px) scale(1.05);
                box-shadow: 0 15px 35px rgba(14, 165, 233, 0.6);
            }
            .btn-outline {
                background: transparent;
                color: #0ea5e9;
                padding: 15px 40px;
                border-radius: 50px;
                font-weight: 600;
                font-size: 1.1rem;
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                border: 2px solid #0ea5e9;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 10px;
                text-decoration: none;
                position: relative;
                overflow: hidden;
            }
            .btn-outline:hover {
                color: white;
                transform: translateY(-3px) scale(1.05);
                box-shadow: 0 15px 35px rgba(14, 165, 233, 0.5);
            }
            .nav-link {
                position: relative;
                transition: all 0.3s ease;
            }
            .nav-link::after {
                content: '';
                position: absolute;
                bottom: -5px;
                left: 50%;
                width: 0;
                height: 2px;
                background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 40%, #0369a1 65%, #475569 85%, #dc2626 100%);
                transition: all 0.3s ease;
                transform: translateX(-50%);
            }
            .nav-link:hover::after {
                width: 100%;
            }
            .course-card-hover {
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                position: relative;
            }
            .course-card-hover:hover {
                transform: translateY(-10px) rotateX(5deg);
                box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
            }
            .course-card-hover .course-header {
                transition: all 0.4s ease;
                position: relative;
                overflow: hidden;
            }
            .course-card-hover:hover .course-header {
                transform: scale(1.05);
            }
            .text-glow:hover {
                text-shadow: 0 0 20px rgba(14, 165, 233, 0.8);
                transition: all 0.3s ease;
            }
            .logo-animation {
                transition: all 0.4s ease;
            }
            .logo-animation:hover {
                transform: scale(1.1) rotate(5deg);
            }
            .pulse-animation {
                animation: pulse 2s infinite;
            }
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.7; }
            }
            .bounce-animation {
                animation: bounce 2s infinite;
            }
            @keyframes bounce {
                0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
                40% { transform: translateY(-10px); }
                60% { transform: translateY(-5px); }
            }
            .rotate-animation {
                animation: rotate 4s linear infinite;
            }
            @keyframes rotate {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            .fade-in {
                animation: fadeIn 1s ease-out;
            }
            @keyframes fadeIn {
                0% { opacity: 0; transform: translateY(30px); }
                100% { opacity: 1; transform: translateY(0); }
            }

            .fade-in-up {
                animation: fadeInUp 1s ease-out forwards;
                opacity: 0;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(40px) scale(0.95);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }
            .slide-in-left {
                animation: slideInLeft 0.8s ease-out;
            }
            @keyframes slideInLeft {
                0% { opacity: 0; transform: translateX(-50px); }
                100% { opacity: 1; transform: translateX(0); }
            }
            [x-cloak] { display: none !important; }
            
            /* Gradient Animation */
            @keyframes gradient {
                0%, 100% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
            }
            .animate-gradient {
                background-size: 200% auto;
                animation: gradient 3s ease infinite;
            }
            
            /* Line Clamp */
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            
            /* Enhanced Card Hover */
            .course-card-hover {
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }
            .course-card-hover:hover {
                transform: translateY(-8px) scale(1.02);
                box-shadow: 0 20px 40px rgba(14, 165, 233, 0.2);
            }
            
            /* Glass Effect Enhancement */
            .glass-effect {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
            }
            
            /* Select Arrow Customization */
            select {
                background-image: none;
            }
            
            /* Smooth Transitions */
            * {
                transition-property: color, background-color, border-color, transform, box-shadow;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            /* Mobile Menu Fix */
            .no-scroll {
                overflow: hidden !important;
                height: 100vh !important;
                position: fixed !important;
                width: 100% !important;
            }
            
            /* Ensure mobile menu is on top */
            nav[x-data] {
                position: relative;
                z-index: 9999;
            }
            
            /* Mobile menu overlay */
            [x-show*="mobileMenu"] {
                z-index: 9998 !important;
            }

            /* Enhanced Search Bar Styles */
            .search-container {
                position: relative;
            }

            .search-bar-wrapper {
                position: relative;
            }

            .search-bar-wrapper::before {
                content: '';
                position: absolute;
                inset: -3px;
                background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(16, 185, 129, 0.3));
                border-radius: 9999px;
                opacity: 0;
                transition: opacity 0.3s ease;
                z-index: -1;
                filter: blur(10px);
            }

            .search-bar-wrapper:focus-within::before {
                opacity: 0.6;
            }

            @media (max-width: 768px) {
                .search-bar-wrapper {
                    gap: 0;
                }
                
                .search-bar-wrapper input {
                    font-size: 0.875rem;
                }
                
                .search-bar-wrapper button {
                    font-size: 0.875rem;
                }
            }
        </style>
    </head>

<body class="bg-gray-50 text-gray-900"
      x-data="{ mobileMenu: false, searchQuery: '' }"
      :class="{ 'overflow-hidden': mobileMenu }">
    <x-tracking-tags placement="body" />

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
        
        <!-- Hero Glow -->
        <div class="hero-glow absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-gradient-radial from-blue-400/20 via-green-400/10 to-transparent rounded-full blur-3xl"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full py-16">
            <div class="text-center fade-in-up">
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black mb-6 leading-tight text-gray-900">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-green-500 to-blue-600 animate-gradient-text">{{ __('public.courses_hero') }}</span>
                </h1>
                <p class="text-lg md:text-xl lg:text-2xl text-gray-700 mb-10 leading-relaxed max-w-3xl mx-auto font-medium">
                    {{ __('public.courses_subtitle') }}
                </p>
                
                <!-- Enhanced Search and Filter -->
                <div class="max-w-5xl mx-auto mt-6 md:mt-10 fade-in-up px-2" style="animation-delay: 0.2s;">
                    <div class="search-container relative">
                        <!-- Desktop Search Bar -->
                        <div class="hidden md:flex items-center bg-white/95 backdrop-blur-xl rounded-full px-5 py-4 shadow-2xl transition-all duration-300 focus-within:shadow-3xl border-2 border-white/80 relative overflow-hidden">
                            <!-- Gradient Border Effect -->
                            <div class="absolute inset-0 rounded-full bg-gradient-to-r from-blue-600 via-green-500 to-blue-600 opacity-0 transition-opacity duration-300 focus-within:opacity-20 -z-10"></div>
                            
                            <!-- Search Icon -->
                            <div class="flex-shrink-0 mr-4">
                                <i class="fas fa-search text-blue-500 text-xl transition-all duration-300"></i>
                            </div>
                            
                            <!-- Search Input -->
                            <input type="text" 
                                   x-model="searchQuery"
                                   placeholder="{{ __('public.search_course_placeholder') }}" 
                                   class="flex-1 bg-transparent border-0 outline-none text-gray-800 placeholder-gray-400 text-base lg:text-lg transition-all duration-300 focus:placeholder-gray-300 font-medium"
                            >
                            
                            <!-- Level Filter Dropdown -->
                            <div class="flex items-center gap-3 mr-6">
                                <div class="w-px h-8 bg-gray-200"></div>
                                <div class="relative min-w-[150px] lg:min-w-[160px]">
                                    <select x-model="selectedLevel" 
                                            class="appearance-none bg-transparent border-0 outline-none text-gray-700 text-base lg:text-lg font-medium cursor-pointer pr-8 pl-2 focus:text-blue-600 transition-colors duration-300 w-full py-1">
                                        <option value="">{{ __('public.all_levels') }}</option>
                                        <option value="beginner">{{ __('public.level_beginner') }}</option>
                                        <option value="intermediate">{{ __('public.level_intermediate') }}</option>
                                        <option value="advanced">{{ __('public.level_advanced') }}</option>
                                    </select>
                                    <div class="absolute left-2 top-1/2 transform -translate-y-1/2 pointer-events-none text-gray-400">
                                        <i class="fas fa-chevron-down text-sm"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Search Button -->
                            <button class="bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-6 lg:px-8 py-3 rounded-full hover:from-blue-700 hover:via-blue-600 hover:to-green-600 transition-all duration-300 transform hover:scale-110 relative overflow-hidden group shadow-lg hover:shadow-xl flex-shrink-0">
                                <span class="relative z-10 flex items-center gap-2">
                                    <i class="fas fa-search text-sm"></i>
                                    <span class="font-bold text-sm lg:text-base">{{ __('public.search_btn') }}</span>
                                </span>
                                <span class="absolute inset-0 bg-white/30 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-300"></span>
                            </button>
                        </div>
                        
                        <!-- Mobile Search Bar -->
                        <div class="md:hidden space-y-3">
                            <!-- Search Input -->
                            <div class="flex items-center bg-white rounded-2xl px-4 py-3 shadow-lg border-2 border-gray-100 focus-within:border-blue-500 focus-within:shadow-xl transition-all duration-300">
                                <i class="fas fa-search text-blue-500 text-lg mr-3"></i>
                                <input type="text" 
                                       x-model="searchQuery"
                                       placeholder="{{ __('public.search_course_placeholder_mobile') }}" 
                                       class="flex-1 bg-transparent border-0 outline-none text-gray-800 placeholder-gray-400 text-base font-medium"
                                >
                            </div>
                            
                            <!-- Filter and Button Row -->
                            <div class="flex items-center gap-3">
                                <!-- Level Filter -->
                                <div class="flex-1 relative">
                                    <select x-model="selectedLevel" 
                                            class="appearance-none bg-white border-2 border-gray-200 rounded-2xl px-4 py-3 pr-10 w-full text-gray-700 text-base font-medium cursor-pointer focus:border-blue-500 focus:outline-none transition-colors duration-300">
                                        <option value="">{{ __('public.all_levels') }}</option>
                                        <option value="beginner">{{ __('public.level_beginner') }}</option>
                                        <option value="intermediate">{{ __('public.level_intermediate') }}</option>
                                        <option value="advanced">{{ __('public.level_advanced') }}</option>
                                    </select>
                                    <div class="absolute left-4 top-1/2 transform -translate-y-1/2 pointer-events-none text-gray-400">
                                        <i class="fas fa-chevron-down text-sm"></i>
                                    </div>
                                </div>
                                
                                <!-- Search Button -->
                                <button class="bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-2xl font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 flex-shrink-0 min-w-[80px]">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Results Count -->
                        <div x-show="searchQuery || selectedLevel" 
                             x-cloak
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             class="mt-4 text-center fade-in">
                            <p class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-full text-xs md:text-sm font-medium shadow-sm">
                                <i class="fas fa-filter text-xs"></i>
                                <span class="text-blue-600 font-bold" x-text="filteredCourses.length"></span> 
                                <span> {{ __('public.course_available') }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Courses Section -->
    <section class="py-12 md:py-16 bg-gradient-to-b from-gray-50 via-white to-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 fade-in-up">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 mb-4">
                    {{ __('public.courses_section_title') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-green-500">{{ __('public.programming') }}</span>
                </h2>
                <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto">{{ __('public.courses_section_subtitle') }}</p>
            </div>
            
            <!-- Courses Grid - نفس تصميم كاردات الصفحة الرئيسية -->
            @if(isset($courses) && is_array($courses) && count($courses) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-12 auto-rows-fr">
                @foreach($courses as $index => $course)
                @php
                    $thumbnailPath = $course['thumbnail'] ?? null;
                    $thumbnailPath = $thumbnailPath ? str_replace('\\', '/', $thumbnailPath) : null;
                    $hasThumbnail = $thumbnailPath && !empty(trim($thumbnailPath));
                    $imageUrl = $hasThumbnail ? asset('storage/' . $thumbnailPath) : null;
                @endphp
                <div class="group relative fade-in-up bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 border border-gray-100 hover:border-blue-200 h-full" style="animation-delay: {{ $index * 0.05 }}s;">
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-400 via-green-400 to-purple-400 rounded-2xl blur opacity-0 group-hover:opacity-20 transition-opacity duration-500 -z-10"></div>
                    <div class="relative h-44 sm:h-48 bg-gradient-to-br from-blue-600 via-blue-500 to-green-500 overflow-hidden">
                        @if($hasThumbnail && $imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $course['title'] ?? __('public.course_fallback') }}" class="absolute inset-0 w-full h-full object-cover">
                        @else
                            <div class="absolute inset-0 opacity-10" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.1) 10px, rgba(255,255,255,0.1) 20px);"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="relative z-10 transform group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                    <i class="fas fa-play-circle text-white text-5xl lg:text-6xl drop-shadow-lg"></i>
                                </div>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-black/5 to-transparent"></div>
                        @if(isset($course['is_featured']) && $course['is_featured'])
                            <span class="absolute top-2 left-2 bg-gradient-to-r from-yellow-400 to-yellow-500 text-yellow-900 px-2 py-0.5 rounded-full text-[9px] font-bold shadow-md flex items-center gap-0.5 z-20">
                                <i class="fas fa-star text-[8px]"></i>
                                <span>{{ __('public.featured_badge') }}</span>
                            </span>
                        @endif
                        <div class="absolute bottom-2 right-2 z-20">
                            <div class="bg-white/95 backdrop-blur-md rounded-lg px-2.5 py-1.5 shadow-xl border border-white/50 group-hover:scale-110 transition-transform duration-300">
                                <span class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                                    <i class="fas fa-play-circle text-blue-600 text-[11px]"></i>
                                    <span>{{ $course['lectures_count'] ?? 0 }} {{ __('public.lecture_single') }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-white relative overflow-hidden flex-grow flex flex-col">
                        <div class="absolute inset-0 opacity-[0.02] pointer-events-none" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(59, 130, 246, 0.05) 5px, rgba(59, 130, 246, 0.05) 10px);"></div>
                        @if(isset($course['academic_subject']['name']))
                        <div class="mb-2 relative z-10">
                            <span class="inline-flex items-center gap-1.5 text-xs text-gray-500 bg-blue-50 px-2 py-1 rounded-full">
                                <i class="fas fa-user text-blue-600 text-[10px]"></i>
                                <span class="text-blue-700 font-medium">{{ Str::limit($course['academic_subject']['name'], 15) }}</span>
                            </span>
                        </div>
                        @endif
                        <h3 class="text-base font-black text-gray-900 mb-2 line-clamp-2 group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-blue-600 group-hover:to-green-600 transition-all duration-300 leading-tight relative z-10">
                            {{ $course['title'] ?? __('public.no_title_fallback') }}
                        </h3>
                        <p class="text-gray-600 text-xs mb-3 line-clamp-2 leading-relaxed group-hover:text-gray-700 transition-colors duration-300 relative z-10 flex-grow">
                            {{ Str::limit($course['description'] ?? __('public.course_description_fallback'), 70) }}
                        </p>
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100 group-hover:border-blue-100 transition-colors duration-300 relative z-10">
                            <div>
                                @if(isset($course['price']) && ($course['price'] > 0 || ($course['original_price'] ?? 0) > 0))
                                    @if(($course['discount_amount'] ?? 0) > 0)
                                        <span class="inline-flex flex-col items-start gap-0.5">
                                            <span class="text-sm text-gray-400 line-through font-semibold">
                                                {{ number_format($course['original_price'] ?? $course['price'], 0) }}
                                                <span class="text-[10px] font-normal">{{ __('public.currency_egp') }}</span>
                                            </span>
                                            <span class="text-lg font-black text-blue-600 flex items-center gap-1 group-hover:scale-110 transition-transform duration-300">
                                                <span>{{ number_format($course['price'], 0) }}</span>
                                                <span class="text-[10px] text-gray-500 font-normal">{{ __('public.currency_egp') }}</span>
                                            </span>
                                        </span>
                                    @else
                                        <span class="text-lg font-black text-blue-600 flex items-center gap-1 group-hover:scale-110 transition-transform duration-300">
                                            <span>{{ number_format($course['price'], 0) }}</span>
                                            <span class="text-[10px] text-gray-500 font-normal">{{ __('public.currency_egp') }}</span>
                                        </span>
                                    @endif
                                @else
                                    <span class="text-lg font-black text-green-600 flex items-center gap-1.5 group-hover:scale-110 transition-transform duration-300">
                                        <div class="w-5 h-5 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-md">
                                            <i class="fas fa-gift text-white text-[8px]"></i>
                                        </div>
                                        <span>{{ __('public.free_price') }}</span>
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('public.course.show', $course['id']) }}"
                               class="group/btn relative bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-3.5 py-1.5 rounded-lg text-xs font-bold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 flex items-center gap-1.5 overflow-hidden"
                               data-ml-select-item
                               data-ml-item-id="course:{{ $course['id'] }}"
                               data-ml-item-name="{{ e($course['title'] ?? '') }}"
                               data-ml-item-price="{{ (float) ($course['price'] ?? 0) }}"
                               data-ml-item-index="{{ $index }}">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300 btn-shimmer"></div>
                                <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-400 to-green-400 rounded-lg blur opacity-0 group-hover/btn:opacity-50 transition-opacity duration-300"></div>
                                <span class="relative z-10">{{ __('public.view_details') }}</span>
                                <i class="fas fa-arrow-left text-[10px] relative z-10 group-hover/btn:translate-x-1 transition-transform duration-300"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Alpine.js Dynamic Filtering (يمكن استخدامه لاحقاً) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
                 x-show="false"
                 style="display: none;">
                <template x-for="(course, index) in filteredCourses" :key="course.id">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden course-card-hover">
                        <!-- Course Header -->
                        <div class="h-48 bg-gradient-to-br flex items-center justify-center relative overflow-hidden course-header"
                             :class="{
                                 'from-sky-400 to-sky-600': index % 6 === 0,
                                 'from-blue-500 to-blue-700': index % 6 === 1,
                                 'from-indigo-500 to-indigo-700': index % 6 === 2,
                                 'from-purple-500 to-purple-700': index % 6 === 3,
                                 'from-pink-500 to-pink-700': index % 6 === 4,
                                 'from-red-500 to-red-700': index % 6 === 5
                             }">
                            <i class="fas fa-code text-white text-6xl pulse-animation"></i>
                            <template x-if="course.is_featured">
                                <div class="absolute top-4 left-4 bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-xs font-bold bounce-animation">
                                    {{ __('public.featured_badge') }}
                                </div>
                            </template>
                            <template x-if="course.level">
                                <div class="absolute top-4 right-4 bg-white/20 backdrop-blur-sm rounded-full px-3 py-1">
                                    <span class="text-white text-xs font-medium" 
                                          x-text="course.level === 'beginner' ? '{{ __('public.level_beginner') }}' : course.level === 'intermediate' ? '{{ __('public.level_intermediate') }}' : '{{ __('public.level_advanced') }}'"></span>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Course Content -->
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2" x-text="course.title"></h3>
                            <p class="text-gray-600 mb-4 text-sm" x-text="course.description ? (course.description.substring(0, 100) + '...') : '{{ addslashes(__('public.course_desc_fallback')) }}'"></p>
                            
                            <!-- Course Info -->
                            <div class="flex items-center justify-between mb-4 text-sm text-gray-500">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-clock"></i>
                                    <span x-text="course.duration_hours ? course.duration_hours + ' ساعة' : 'غير محدد'"></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-layer-group"></i>
                                    <span x-text="(course.lectures_count || 0) + ' {{ __('public.lecture_single') }}'"></span>
                                </div>
                            </div>
                            
                            <!-- Subject Info -->
                            <template x-if="course.academic_subject">
                                <div class="mb-4">
                                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-sky-100 text-sky-700 rounded-full text-xs font-semibold">
                                        <i class="fas fa-book"></i>
                                        <span x-text="course.academic_subject.name"></span>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Price and CTA -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <div>
                                    <template x-if="course.price && course.price > 0">
                                        <span class="text-2xl font-black text-sky-600" x-text="course.price + ' ج.م'"></span>
                                    </template>
                                    <template x-if="!course.price || course.price == 0">
                                        <span class="text-2xl font-black text-green-600">{{ __('public.free_price') }}</span>
                                    </template>
                                </div>
                                @auth
                                    <a :href="'{{ url('/') }}/courses/' + course.id" class="btn-primary text-sm px-6 py-2">
                                        <i class="fas fa-eye"></i>
                                        {{ __('public.view_details') }}
                                    </a>
                                @else
                                    <a href="{{ route('register') }}" class="btn-primary text-sm px-6 py-2">
                                        <i class="fas fa-sign-in-alt"></i>
                                        {{ __('public.register_to_enroll') }}
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            
            <!-- No Courses Found -->
            <div x-show="!filteredCourses || filteredCourses.length === 0" class="text-center py-16 fade-in-up" x-cloak>
                <div class="max-w-md mx-auto">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-search text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">{{ __('public.no_results') }}</h3>
                    <p class="text-gray-600 mb-6">{{ __('public.no_results_hint') }}</p>
                </div>
            </div>
            @else
            <!-- Empty State -->
            <div class="text-center py-12 fade-in-up">
                <div class="max-w-md mx-auto">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-book text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('public.coming_soon') }}</h3>
                    <p class="text-gray-600 mb-6">{{ __('public.coming_soon_courses') }}</p>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-full font-bold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-bell"></i>
                        <span>{{ __('public.subscribe_updates') }}</span>
                    </a>
                </div>
            </div>
            @endif
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
                {{ __('public.cta_programming_title') }}
            </h2>
            <p class="text-lg md:text-xl text-gray-600 mb-10 font-medium">
                {{ __('public.cta_programming_desc') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-8 py-4 rounded-full font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 relative overflow-hidden group">
                    <span class="relative z-10 flex items-center gap-2">
                        <i class="fas fa-user-plus"></i>
                        <span>{{ __('public.register_free_now') }}</span>
                    </span>
                    <span class="absolute inset-0 bg-gradient-to-r from-green-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 bg-white text-blue-600 px-8 py-4 rounded-full font-bold text-lg border-2 border-blue-600 hover:bg-blue-50 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl relative">
                    <span class="flex items-center gap-2">
                        <span>{{ __('public.have_account') }}</span>
                        <i class="fas fa-arrow-left"></i>
                    </span>
                </a>
            </div>
        </div>
    </section>

    </main>
    
    <!-- Unified Footer -->
    @include('components.unified-footer')

    @php
        $__mlAnalytics = app(\App\Services\MarketingAnalyticsService::class);
        $__mlListItems = [];
        foreach (($courses ?? []) as $__mlIndex => $__mlCourse) {
            if (is_array($__mlCourse) && ! empty($__mlCourse['id'])) {
                $__mlListItems[] = $__mlAnalytics->itemFromCourseArray($__mlCourse, (int) $__mlIndex);
            }
        }
        $__mlViewItemList = $__mlListItems !== []
            ? $__mlAnalytics->viewItemList($__mlListItems, 'courses', 'Courses')
            : null;
    @endphp
    <x-ecommerce-datalayer :payload="$__mlViewItemList" />

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var navbar = document.getElementById('navbar');
            if (navbar) {
                window.addEventListener('scroll', function() {
                    navbar.classList.toggle('scrolled', window.pageYOffset > 100);
                }, { passive: true });
            }

            document.addEventListener('click', function (e) {
                var link = e.target.closest('[data-ml-select-item]');
                if (!link || typeof window.mindlyticsPushEcommerce !== 'function') {
                    return;
                }
                window.mindlyticsPushEcommerce({
                    event: 'select_item',
                    ecommerce: {
                        currency: @json(\App\Support\MarketingWebAnalyticsSettings::currency()),
                        item_list_id: 'courses',
                        item_list_name: 'Courses',
                        items: [{
                            item_id: link.getAttribute('data-ml-item-id'),
                            item_name: link.getAttribute('data-ml-item-name') || '',
                            price: parseFloat(link.getAttribute('data-ml-item-price') || '0') || 0,
                            quantity: 1,
                            index: parseInt(link.getAttribute('data-ml-item-index') || '0', 10) || 0,
                            item_brand: @json(\App\Support\MarketingWebAnalyticsSettings::itemBrand()),
                            item_category: 'online_course',
                            item_list_id: 'courses',
                            item_list_name: 'Courses'
                        }]
                    }
                });
            }, true);
        });
    </script>
</body>
</html>
