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
    <title>{{ $course->localized('title') ?: __('public.course_detail_title') }} - {{ __('public.site_suffix') }}</title>

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

            [x-cloak] { display: none !important; }

            body {
                overflow-x: hidden;
                background: #f8fafc;
                width: 100%;
                max-width: 100vw;
                position: relative;
                padding-top: 0;
                margin-top: 0;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
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

            /* Enhanced Navbar Styles - Same as welcome page */
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

            .navbar-gradient::before {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 3px;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), rgba(16, 185, 129, 0.6), rgba(255, 255, 255, 0.6), transparent);
                opacity: 0.8;
                transition: opacity 0.3s ease;
            }

            .navbar-gradient::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: radial-gradient(circle at 50% 0%, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
                pointer-events: none;
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

            /* Nav Link Styles */
            .nav-link {
                position: relative;
                display: inline-block;
                padding: 8px 16px;
                border-radius: 8px;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .nav-link::before {
                content: '';
                position: absolute;
                inset: 0;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 8px;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .nav-link:hover {
                transform: translateY(-2px);
                background: rgba(255, 255, 255, 0.1);
            }

            .nav-link:hover::before {
                opacity: 1;
            }

            /* Enhanced Hero Section - Matches welcome page */
            .hero-section {
                background: linear-gradient(to bottom, #f0f9ff, #e0f2fe, #ffffff);
                position: relative;
                overflow: hidden;
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

            /* Animated Background Elements */
            .animated-background {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                pointer-events: none;
                overflow: hidden;
                z-index: 0;
            }

            /* Floating Circles */
            .floating-circle {
                position: absolute;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(59, 130, 246, 0.25), rgba(59, 130, 246, 0.08), transparent);
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

            /* Enhanced Search Bar Styles */
            .search-bar-wrapper input:focus {
                width: 100%;
            }
            
            .search-bar-wrapper input:focus ~ button,
            .search-bar-wrapper:focus-within button {
                background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            }

            /* Buttons */
            .btn-primary {
                background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1e40af 100%);
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
                box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
            }

            .btn-primary:hover {
                transform: translateY(-3px) scale(1.05);
                box-shadow: 0 15px 35px rgba(59, 130, 246, 0.6);
            }
            .glass-effect {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(15px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                transition: all 0.4s ease;
                position: relative;
                overflow: hidden;
            }
            .card-hover {
                transition: all 0.3s ease;
                position: relative;
            }
            .card-hover:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                z-index: 5;
            }
            .particles {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                overflow: hidden;
                pointer-events: none;
            }
            .particle {
                position: absolute;
                width: 4px;
                height: 4px;
                background: rgba(255, 255, 255, 0.5);
                border-radius: 50%;
                animation: particleFloat 10s infinite linear;
            }
            @keyframes particleFloat {
                0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
                10% { opacity: 1; }
                90% { opacity: 1; }
                100% { transform: translateY(-10vh) rotate(360deg); opacity: 0; }
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
            .slide-in-left {
                animation: slideInLeft 0.8s ease-out;
            }
            @keyframes slideInLeft {
                0% { opacity: 0; transform: translateX(-50px); }
                100% { opacity: 1; transform: translateX(0); }
            }
            .slide-in-right {
                animation: slideInRight 0.8s ease-out;
            }
            @keyframes slideInRight {
                0% { opacity: 0; transform: translateX(50px); }
                100% { opacity: 1; transform: translateX(0); }
            }
            .feature-icon-hover {
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                position: relative;
            }
            .feature-icon-hover:hover {
                transform: rotateY(180deg) scale(1.1);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            }
            .floating-numbers {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                z-index: 0;
            }
            
            /* Sidebar: ثابت أثناء التمرير على الديسكتوب لتنظيم الصفحة الطويلة */
            .course-sidebar {
                position: relative;
            }
            @media (min-width: 1024px) {
                .course-sidebar {
                    position: sticky;
                    top: 5.5rem;
                    align-self: start;
                    max-height: calc(100vh - 6rem);
                    overflow-y: auto;
                    scrollbar-width: thin;
                }
            }
            
            /* Smooth scroll */
            html {
                scroll-behavior: smooth;
            }
            
            /* Prevent card overlap */
            .course-card {
                position: relative;
                z-index: 1;
                margin-bottom: 2rem;
                isolation: isolate;
            }
            
            .course-card:last-child {
                margin-bottom: 0;
            }
            
            /* Improve card hover without overlap */
            .card-hover {
                transition: all 0.3s ease;
                position: relative;
            }
            
            .card-hover:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                z-index: 5;
            }
            
            /* Fix for sections spacing */
            section {
                position: relative;
                z-index: 1;
                isolation: isolate;
            }
            
            /* Ensure proper stacking context */
            body {
                position: relative;
                z-index: 0;
            }
            
            /* Navbar z-index fix */
            nav {
                position: relative;
                z-index: 50;
            }
            
            /* مشغل الفيديو المخصص */
            .custom-video-player-wrapper {
                position: relative;
                width: 100%;
                border-radius: 1rem;
                overflow: hidden;
                background: #000;
            }

            /* تخصيص Plyr Player */
            .custom-video-player-wrapper .plyr {
                border-radius: 1rem;
            }

            .custom-video-player-wrapper .plyr__video-wrapper {
                background: #000;
                border-radius: 1rem;
                position: relative;
                overflow: hidden;
            }

            /* إخفاء علامات YouTube من Plyr */
            .custom-video-player-wrapper .plyr__video-embed {
                position: relative;
                overflow: hidden;
            }

            .custom-video-player-wrapper .plyr__video-embed iframe {
                border: none;
                position: relative;
            }

            /* إخفاء جميع عناصر YouTube */
            .custom-video-player-wrapper .plyr__video-embed::before,
            .custom-video-player-wrapper .plyr__video-embed::after {
                display: none !important;
            }

            /* حاوية فيديو المقدمة 16:9 (نفس فكرة المسار) */
            .intro-video-container {
                position: relative;
                width: 100%;
                padding-bottom: 56.25%; /* 16:9 */
                height: 0;
                background: #000;
                border-radius: 1rem;
                overflow: hidden;
            }

            .intro-video-container iframe {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                border: none;
            }

            /* Hero section z-index */
            .hero-gradient {
                position: relative;
                z-index: 2;
            }
            .floating-number {
                position: absolute;
                color: rgba(14, 165, 233, 0.3);
                font-size: 2rem;
                font-weight: bold;
                animation: floatNumber 15s linear infinite;
            }
            @keyframes floatNumber {
                0% { transform: translateY(100vh) rotate(0deg) scale(0.5); opacity: 0; }
                10% { opacity: 1; transform: translateY(90vh) rotate(36deg) scale(0.7); }
                50% { opacity: 0.8; transform: translateY(50vh) rotate(180deg) scale(1); }
                90% { opacity: 0.3; transform: translateY(10vh) rotate(324deg) scale(0.8); }
                100% { transform: translateY(-10vh) rotate(360deg) scale(0.3); opacity: 0; }
            }

            .course-whatsapp-float {
                position: fixed;
                left: 1rem;
                bottom: 1.25rem;
                z-index: 99980;
                display: inline-flex;
                align-items: center;
                gap: 0.55rem;
                padding: 0.7rem 1rem;
                border-radius: 9999px;
                background: #25D366;
                color: #fff;
                font-weight: 800;
                font-size: 0.9rem;
                box-shadow: 0 12px 30px rgba(37, 211, 102, 0.4);
                text-decoration: none;
                transform: translateY(120%);
                opacity: 0;
                pointer-events: none;
                transition: transform 0.35s ease, opacity 0.35s ease, box-shadow 0.25s ease;
            }

            .course-whatsapp-float.is-visible {
                transform: translateY(0);
                opacity: 1;
                pointer-events: auto;
            }

            .course-whatsapp-float:hover {
                box-shadow: 0 16px 36px rgba(37, 211, 102, 0.5);
                filter: brightness(1.03);
            }

            .course-whatsapp-float .course-whatsapp-icon {
                width: 2rem;
                height: 2rem;
                border-radius: 9999px;
                background: rgba(255,255,255,0.2);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 1.15rem;
            }

            .course-buy-float {
                position: fixed;
                right: 1rem;
                bottom: 1.25rem;
                z-index: 99980;
                display: inline-flex;
                align-items: center;
                gap: 0.55rem;
                padding: 0.75rem 1.15rem;
                border-radius: 9999px;
                background: linear-gradient(135deg, #2563eb 0%, #16a34a 100%);
                color: #fff;
                font-weight: 800;
                font-size: 0.92rem;
                box-shadow: 0 12px 30px rgba(37, 99, 235, 0.4);
                text-decoration: none;
                transform: translateY(120%);
                opacity: 0;
                pointer-events: none;
                transition: transform 0.35s ease, opacity 0.35s ease, box-shadow 0.25s ease;
                max-width: calc(100vw - 6.5rem);
            }

            .course-buy-float.is-visible {
                transform: translateY(0);
                opacity: 1;
                pointer-events: auto;
            }

            .course-buy-float:hover {
                box-shadow: 0 16px 36px rgba(37, 99, 235, 0.5);
                filter: brightness(1.04);
            }

            .course-buy-float .course-buy-icon {
                width: 2rem;
                height: 2rem;
                border-radius: 9999px;
                background: rgba(255,255,255,0.2);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 0.95rem;
                flex-shrink: 0;
            }

            .course-buy-float .course-buy-label {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* قسم التقييمات — سكرول جانبي مضغوط */
            .course-reviews-section-head {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 1rem;
                margin-bottom: 1.25rem;
                flex-wrap: wrap;
            }
            .course-reviews-section-head .head-copy {
                text-align: right;
            }
            .course-reviews-nav {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            .course-reviews-nav button {
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 9999px;
                border: 1px solid #e2e8f0;
                background: #fff;
                color: #475569;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: background 0.15s ease, border-color 0.15s ease;
            }
            .course-reviews-nav button:hover {
                background: #f8fafc;
                border-color: #cbd5e1;
            }
            .course-reviews-rail {
                display: flex;
                align-items: stretch;
                gap: 0.85rem;
                overflow-x: auto;
                scroll-snap-type: x mandatory;
                -webkit-overflow-scrolling: touch;
                padding: 0.25rem 0.15rem 0.85rem;
                scrollbar-width: thin;
                scrollbar-color: #94a3b8 transparent;
            }
            .course-reviews-rail::-webkit-scrollbar { height: 6px; }
            .course-reviews-rail::-webkit-scrollbar-thumb {
                background: #94a3b8;
                border-radius: 9999px;
            }
            .course-review-card {
                flex: 0 0 auto;
                width: min(58vw, 190px);
                scroll-snap-align: start;
                border-radius: 1rem;
                overflow: hidden;
                background: #fff;
                border: 1px solid #e2e8f0;
                box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                display: flex;
                flex-direction: column;
            }
            .course-review-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 28px rgba(15, 23, 42, 0.1);
            }
            .course-review-card.has-image {
                width: min(62vw, 200px);
            }
            .course-review-card .review-media {
                position: relative;
                display: block;
                width: 100%;
                height: 230px;
                margin: 0;
                padding: 0;
                background: #0f172a;
                overflow: hidden;
                cursor: zoom-in;
                border: 0;
            }
            .course-review-card .review-media img {
                display: block;
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: top center;
            }
            .course-review-card .review-media::after {
                content: '';
                position: absolute;
                inset: auto 0 0 0;
                height: 42%;
                background: linear-gradient(to top, rgba(15, 23, 42, 0.55), transparent);
                pointer-events: none;
            }
            .course-review-card .review-meta {
                padding: 0.55rem 0.7rem 0.65rem;
                border-top: 1px solid #f1f5f9;
            }
            .course-review-card.is-text-only {
                width: min(70vw, 220px);
                min-height: 180px;
                background: linear-gradient(160deg, #ffffff, #f8fafc);
            }
            .course-review-lightbox {
                position: fixed;
                inset: 0;
                z-index: 100000;
                background: rgba(2, 6, 23, 0.9);
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1rem;
            }
            .course-review-lightbox img {
                max-width: min(94vw, 920px);
                max-height: 90vh;
                width: auto;
                height: auto;
                border-radius: 0.75rem;
                object-fit: contain;
                box-shadow: 0 20px 60px rgba(0,0,0,0.45);
            }
            @media (min-width: 768px) {
                .course-review-card,
                .course-review-card.has-image {
                    width: 210px;
                }
                .course-review-card .review-media {
                    height: 260px;
                }
                .course-review-card.is-text-only {
                    width: 230px;
                }
            }

            @media (max-width: 640px) {
                .course-whatsapp-float {
                    left: 0.75rem;
                    bottom: 0.9rem;
                    padding: 0.75rem;
                }
                .course-whatsapp-float .course-whatsapp-label {
                    display: none;
                }
                .course-buy-float {
                    right: 0.75rem;
                    bottom: 0.9rem;
                    left: 4.25rem;
                    max-width: none;
                    justify-content: center;
                    padding: 0.8rem 1rem;
                }
            }
        </style>
    </head>

<body class="bg-gray-50 text-gray-900"
      x-data="{ mobileMenu: false, searchQuery: '' }"
      :class="{ 'overflow-hidden': mobileMenu }">

    @include('components.unified-navbar')

    @php
        $courseContact = $platformContact ?? \App\Support\PlatformSettings::contactPage();
        $courseWhatsappDigits = \App\Support\PlatformSettings::phoneDigits((string) ($courseContact['whatsapp'] ?? ''));
        if ($courseWhatsappDigits === '') {
            $courseWhatsappDigits = \App\Support\PlatformSettings::phoneDigits((string) ($courseContact['phone'] ?? ''));
        }
        // تطبيع أرقام مصر المحلية إلى صيغة دولية لـ wa.me
        if ($courseWhatsappDigits !== '' && str_starts_with($courseWhatsappDigits, '0')) {
            $courseWhatsappDigits = '20' . substr($courseWhatsappDigits, 1);
        }
        $courseWhatsappText = rawurlencode(
            'مرحباً فريق Mindlytics، أريد الاستفسار عن كورس: ' . ($course->localized('title') ?: $course->title)
        );
        $courseWhatsappUrl = $courseWhatsappDigits !== ''
            ? 'https://wa.me/' . $courseWhatsappDigits . '?text=' . $courseWhatsappText
            : null;

        $courseBuyFloatUrl = null;
        $courseBuyFloatLabel = __('public.buy_now');
        $courseBuyFloatIcon = 'fa-shopping-cart';
        if (!empty($isEnrolled)) {
            $courseBuyFloatUrl = route('courses.show', $course->id);
            $courseBuyFloatLabel = __('public.start_learning_now');
            $courseBuyFloatIcon = 'fa-play-circle';
        } elseif (($course->effectivePrice() ?? $course->price ?? 0) > 0 && !($course->is_free ?? false)) {
            $courseBuyFloatUrl = route('public.course.checkout', $course->id);
            $priceLabel = number_format((float) $course->effectivePrice(), 0) . ' ج.م';
            $courseBuyFloatLabel = __('public.buy_now') . ' · ' . $priceLabel;
            $courseBuyFloatIcon = 'fa-shopping-cart';
        } elseif (auth()->check()) {
            $courseBuyFloatUrl = route('public.course.enroll.free', $course->id);
            $courseBuyFloatLabel = __('public.register_free');
            $courseBuyFloatIcon = 'fa-gift';
        } else {
            $courseBuyFloatUrl = route('register', ['redirect' => route('public.course.enroll.free', $course->id)]);
            $courseBuyFloatLabel = __('public.register_free');
            $courseBuyFloatIcon = 'fa-gift';
        }
    @endphp

    @if(session('payment_success_modal'))
        @include('components.payment-success-modal', [
            'message' => session('success'),
            'redirectUrl' => session('payment_success_redirect_url') ?? (Auth::check() && Auth::user()->isStudent() ? route('my-courses.learn', $course->id) : null),
            'seconds' => 5,
        ])
    @endif
    
    <main class="pt-0 mt-0">
    {{-- رسائل النجاح / المعلومات / الأخطاء بعد إتمام الطلب أو أي إجراء --}}
    @if(session('success') && !session('payment_success_modal'))
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 lg:pt-24 pb-2" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)">
            <div class="rounded-xl border-2 border-green-200 bg-gradient-to-r from-green-50 to-emerald-50 px-4 py-4 shadow-lg flex items-start gap-3">
                <i class="fas fa-check-circle text-green-600 text-2xl flex-shrink-0 mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-green-800 font-bold">{{ session('success') }}</p>
                    <p class="text-green-700 text-sm mt-1">{{ __('public.order_success_hint') }}</p>
                </div>
                <button type="button" @click="show = false" class="text-green-600 hover:text-green-800 p-1"><i class="fas fa-times"></i></button>
            </div>
        </div>
    @endif
    @if(session('info'))
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 lg:pt-24 pb-2" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)">
            <div class="rounded-xl border-2 border-blue-200 bg-gradient-to-r from-blue-50 to-sky-50 px-4 py-4 shadow-lg flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-600 text-2xl flex-shrink-0 mt-0.5"></i>
                <p class="text-blue-800 font-bold flex-1">{{ session('info') }}</p>
                <button type="button" @click="show = false" class="text-blue-600 hover:text-blue-800 p-1"><i class="fas fa-times"></i></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 lg:pt-24 pb-2" x-data="{ show: true }" x-show="show">
            <div class="rounded-xl border-2 border-red-200 bg-gradient-to-r from-red-50 to-rose-50 px-4 py-4 shadow-lg flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-600 text-2xl flex-shrink-0 mt-0.5"></i>
                <p class="text-red-800 font-bold flex-1">{{ session('error') }}</p>
                <button type="button" @click="show = false" class="text-red-600 hover:text-red-800 p-1"><i class="fas fa-times"></i></button>
            </div>
        </div>
    @endif
    <!-- Hero Section - بدون صورة الكورس في الخلفية -->
    <section class="hero-section relative overflow-hidden min-h-[70vh] flex items-center pt-16 lg:pt-20">
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
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full py-8 lg:py-10">
            <!-- Breadcrumb -->
            <nav class="mb-4 text-gray-600 text-sm flex items-center fade-in-up">
                <a href="{{ url('/') }}" class="hover:text-blue-600 transition-colors">{{ __('public.home') }}</a>
                <span class="mx-2 text-gray-400">/</span>
                <a href="{{ route('public.courses') }}" class="hover:text-blue-600 transition-colors">{{ __('public.courses') }}</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-900 font-medium">{{ Str::limit($course->localized('title') ?: __('public.course_fallback'), 30) }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-start">
                <!-- Course Info -->
                <div class="slide-in-left">
                    @if($course->is_featured ?? false)
                        <div class="inline-flex items-center gap-1 px-2 py-0.5 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-full shadow-md mb-4 fade-in-up">
                            <i class="fas fa-star text-yellow-900 text-[8px]"></i>
                            <span class="text-yellow-900 font-bold text-[9px]">{{ __('public.featured_course_badge') }}</span>
                        </div>
                    @endif
                    
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-black mb-3 leading-tight text-gray-900 fade-in-up" style="animation-delay: 0.1s;">
                        {{ $course->localized('title') ?: __('public.course_title_fallback') }}
                    </h1>
                    
                    @php
                        $courseHook = trim((string) ($course->localized('hook') ?: ''));
                    @endphp
                    @if($courseHook !== '')
                        <p class="text-base md:text-lg text-gray-600 mb-5 leading-relaxed fade-in-up" style="animation-delay: 0.2s;">
                            {{ $courseHook }}
                        </p>
                    @endif

                    <!-- Course Stats -->
                    <div class="grid grid-cols-3 gap-4 mb-6 fade-in-up" style="animation-delay: 0.1s;">
                        <div class="bg-white rounded-2xl p-4 text-center border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300">
                            <div class="text-3xl font-black text-blue-600 mb-2">{{ $course->lectures_count ?? $course->total_lectures ?? 0 }}</div>
                            <div class="text-sm text-gray-600 font-medium">{{ __('public.lecture_single') }}</div>
                        </div>
                        <div class="bg-white rounded-2xl p-4 text-center border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300">
                            <div class="text-3xl font-black text-green-600 mb-2">{{ $course->display_duration_hours }}</div>
                            <div class="text-sm text-gray-600 font-medium">{{ __('public.hours') }}</div>
                        </div>
                        <div class="bg-white rounded-2xl p-4 text-center border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300">
                            <div class="text-xl font-black text-gray-700 mb-2">
                                @if($course->level == 'beginner') {{ __('public.level_beginner') }}
                                @elseif($course->level == 'intermediate') {{ __('public.level_intermediate') }}
                                @else {{ __('public.level_advanced') }}
                                @endif
                            </div>
                            <div class="text-sm text-gray-600 font-medium">{{ __('public.level_label') }}</div>
                        </div>
                    </div>

                    @if($course->instructor && \App\Models\InstructorProfile::where('user_id', $course->instructor->id)->where('status', 'approved')->exists())
                    <div class="mb-6 fade-in-up" style="animation-delay: 0.15s;">
                        <span class="text-sm text-gray-600 font-medium">{{ __('public.instructor_label') }}</span>
                        <a href="{{ route('public.instructors.show', $course->instructor) }}" class="text-blue-600 hover:text-blue-700 font-bold hover:underline">{{ $course->instructor->name }}</a>
                    </div>
                    @elseif($course->instructor)
                    <div class="mb-6 fade-in-up" style="animation-delay: 0.15s;">
                        <span class="text-sm text-gray-600 font-medium">{{ __('public.instructor_label') }}</span>
                        <span class="font-semibold text-gray-800">{{ $course->instructor->name }}</span>
                    </div>
                    @endif

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row flex-wrap gap-4 fade-in-up" style="animation-delay: 0.3s;">
                        @if(!empty($courseMindMapVisible ?? false))
                            <a href="{{ route('public.course.mind-map', $course->id) }}" class="inline-flex items-center justify-center gap-2 bg-white text-emerald-700 px-6 py-3 rounded-full font-bold text-base border-2 border-emerald-500 shadow-md hover:bg-emerald-50 hover:shadow-lg transition-all duration-300">
                                <i class="fas fa-diagram-project"></i>
                                {{ __('public.course_mind_map_button') }}
                            </a>
                        @endif
                        @auth
                            @if($isEnrolled ?? false)
                                <a href="{{ route('courses.show', $course->id) }}" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-play-circle"></i>
                                    {{ __('public.start_learning_now') }}
                                </a>
                            @else
                                @if(($course->effectivePrice() ?? 0) > 0 && !($course->is_free ?? false))
                                    <a href="{{ route('public.course.checkout', $course->id) }}" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                        <i class="fas fa-shopping-cart"></i>
                                        {{ __('public.buy_now') }}
                                    </a>
                                @else
                                    <a href="{{ route('public.course.enroll.free', $course->id) }}" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                        <i class="fas fa-gift"></i>
                                        {{ __('public.register_free') }}
                                    </a>
                                @endif
                            @endif
                        @endauth
                        @guest
                            @if(($course->price ?? 0) > 0 && !($course->is_free ?? false))
                                <a href="{{ route('public.course.checkout', $course->id) }}" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-shopping-cart"></i>
                                    {{ __('public.buy_now') }}
                                </a>
                            @else
                                <a href="{{ route('register', ['redirect' => route('public.course.enroll.free', $course->id)]) }}" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-gift"></i>
                                    {{ __('public.register_free') }}
                                </a>
                            @endif
                        @endguest
                        <a href="{{ route('public.courses') }}" class="inline-flex items-center justify-center gap-2 bg-white text-blue-600 px-6 py-3 rounded-full font-bold text-base border-2 border-blue-600 hover:bg-blue-50 transition-all duration-300">
                            <i class="fas fa-arrow-right"></i>
                            {{ __('public.all_courses') }}
                        </a>
                        @if($courseWhatsappUrl)
                            <a href="{{ $courseWhatsappUrl }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               data-course-whatsapp-inline
                               class="inline-flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1ebe57] text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300">
                                <i class="fab fa-whatsapp text-lg"></i>
                                تواصل مع التيم
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Right column: Intro video + Price card -->
                <div class="flex flex-col gap-6">
                    <!-- مقدمة الكورس (نفس فكرة المسار) -->
                    <div class="relative fade-in-up max-w-xl" style="animation-delay: 0.2s;">
                        @if($course->video_url ?? null)
                        @php
                            $introVideoUrl = trim((string) ($course->video_url ?? ''));
                        @endphp
                        <div class="bg-white rounded-2xl p-4 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300">
                            <div class="text-center mb-3">
                                <h2 class="text-lg font-bold text-gray-900 mb-0.5 flex items-center justify-center gap-2">
                                    <i class="fas fa-play-circle text-blue-600 text-base"></i>
                                    {{ __('public.intro_video_title') }}
                                </h2>
                                <p class="text-gray-500 text-sm">{{ __('public.intro_video_desc') }}</p>
                            </div>
                            @include('partials.intro-video-embed', [
                                'url' => $introVideoUrl,
                                'title' => __('public.intro_video_title'),
                            ])
                        </div>
                        @else
                        <div class="bg-white rounded-2xl p-4 shadow-lg border border-gray-200">
                            <div class="text-center text-gray-500 py-6">
                                <i class="fas fa-video text-2xl mb-2 text-gray-300"></i>
                                <p class="text-sm">{{ __('public.no_intro_video') ?? 'لا يوجد فيديو مقدمة' }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Course Details Section -->
    <section class="py-12 md:py-16 bg-gradient-to-b from-gray-50 via-white to-gray-50 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- About Course -->
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 lg:p-8 border border-gray-200 fade-in-up">
                        <h2 class="text-2xl lg:text-3xl font-black text-gray-900 mb-6 flex items-center gap-3">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            {{ __('public.about_course') }}
                        </h2>
                        <div class="prose max-w-none text-gray-700 leading-relaxed">
                            <p class="text-lg mb-4">{{ $course->localized('description') ?: __('public.course_desc_fallback') }}</p>
                            @if($course->objectives)
                                <div class="mt-6">
                                    <h3 class="text-xl font-bold text-gray-900 mb-4">{{ __('public.course_objectives') }}</h3>
                                    <div class="bg-gradient-to-br from-blue-50 to-green-50 rounded-xl p-6 border border-blue-100">
                                        <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $course->objectives }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @php
                        $infoBlocks = array_filter([
                            [
                                'title' => 'مناسب لمين؟',
                                'icon' => 'fa-user-check',
                                'content' => $course->suitable_for,
                                'tone' => 'from-sky-50 to-blue-50 border-sky-100',
                                'iconColor' => 'text-sky-600',
                            ],
                            [
                                'title' => 'مناسب لحد سن كام؟ ولمين؟',
                                'icon' => 'fa-people-group',
                                'content' => $course->age_suitability,
                                'tone' => 'from-violet-50 to-indigo-50 border-violet-100',
                                'iconColor' => 'text-violet-600',
                            ],
                            [
                                'title' => 'معلومات عن المحاضر',
                                'icon' => 'fa-chalkboard-teacher',
                                'content' => $course->instructor_info,
                                'tone' => 'from-amber-50 to-orange-50 border-amber-100',
                                'iconColor' => 'text-amber-600',
                            ],
                            [
                                'title' => 'الكورس متاح لإمتى؟',
                                'icon' => 'fa-calendar-check',
                                'content' => $course->available_until_info,
                                'tone' => 'from-emerald-50 to-teal-50 border-emerald-100',
                                'iconColor' => 'text-emerald-600',
                            ],
                            [
                                'title' => 'المتابعة إزاي؟',
                                'icon' => 'fa-comments',
                                'content' => $course->follow_up_info,
                                'tone' => 'from-rose-50 to-pink-50 border-rose-100',
                                'iconColor' => 'text-rose-600',
                            ],
                        ], fn ($block) => filled(trim((string) ($block['content'] ?? ''))));

                        $learnPoints = collect(preg_split("/\r\n|\n|\r/", (string) ($course->what_you_learn ?? '')))
                            ->map(fn ($p) => trim((string) $p))
                            ->filter()
                            ->values();
                        $learnPreviewCount = 8;
                    @endphp

                    <!-- محتوى الكورس: التقسيمات + معاينة فيديوهات (أول 2 مفتوحان، الثالث مقفول) -->
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 lg:p-8 border border-gray-200 fade-in-up" style="animation-delay: 0.25s;">
                        <h2 class="text-2xl lg:text-3xl font-black text-gray-900 mb-6 flex items-center gap-3">
                            <i class="fas fa-play-circle text-blue-600"></i>
                            محتوى الكورس — معاينة
                        </h2>
                        <p class="text-gray-600 mb-6">شاهد أول فيديوهين مجاناً، والثالث يُفتح بعد شراء الكورس.</p>

                        @if(isset($previewVideoLessons) && $previewVideoLessons->count() > 0)
                            @php
                                $previewUnlockedCount = (int) ($previewUnlockedCount ?? 2);
                                $previewUnlockCta = null;
                                if ($isEnrolled ?? false) {
                                    $previewUnlockCta = route('courses.show', $course->id);
                                } elseif (($course->effectivePrice() ?? $course->price ?? 0) > 0 && !($course->is_free ?? false)) {
                                    $previewUnlockCta = route('public.course.checkout', $course->id);
                                } elseif (auth()->check()) {
                                    $previewUnlockCta = route('public.course.enroll.free', $course->id);
                                } else {
                                    $previewUnlockCta = route('register', ['redirect' => route('public.course.enroll.free', $course->id)]);
                                }
                            @endphp
                            <div x-data="coursePreviewPopup({{ $course->id }})">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="fas fa-video text-green-600"></i>
                                    معاينة أول {{ $previewVideoLessons->count() }} فيديو
                                </h3>
                                <div class="space-y-4">
                                    @foreach($previewVideoLessons as $idx => $lesson)
                                        @php
                                            $isLocked = $idx >= $previewUnlockedCount;
                                            $isUnlocked = !$isLocked;
                                            $hasVideo = filled($lesson->recording_url ?? null) || filled($lesson->video_url ?? null);
                                        @endphp
                                        <div class="rounded-xl border-2 {{ $isLocked ? 'border-slate-200 bg-slate-50' : 'border-gray-200 bg-gray-50 hover:border-blue-300' }} overflow-hidden transition-all duration-300 {{ $isUnlocked && $hasVideo ? 'cursor-pointer' : '' }}"
                                             @if($isUnlocked && $hasVideo)
                                                 role="button"
                                                 tabindex="0"
                                                 @click="openPreview({{ (int) $lesson->id }}, @js($lesson->title))"
                                                 @keydown.enter.prevent="openPreview({{ (int) $lesson->id }}, @js($lesson->title))"
                                             @endif>
                                            <div class="p-4 flex flex-col sm:flex-row sm:items-center gap-3">
                                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                                    <div class="flex-shrink-0 w-10 h-10 {{ $isLocked ? 'bg-slate-400' : 'bg-blue-600' }} text-white rounded-lg flex items-center justify-center">
                                                        <i class="fas {{ $isLocked ? 'fa-lock' : 'fa-play' }} text-sm"></i>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="font-semibold text-gray-900">{{ $lesson->title }}</div>
                                                        @if($lesson->duration_minutes)
                                                            <div class="text-sm text-gray-500"><i class="fas fa-clock ml-1"></i> {{ $lesson->duration_minutes }} دقيقة</div>
                                                        @endif
                                                        @if($isLocked)
                                                            <div class="text-xs font-semibold text-amber-700 mt-1">مقفل — يُفتح بعد شراء الكورس</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($isUnlocked && $hasVideo)
                                                    <div class="flex-shrink-0">
                                                        <button type="button"
                                                                @click.stop="openPreview({{ (int) $lesson->id }}, @js($lesson->title))"
                                                                :disabled="loading"
                                                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 disabled:opacity-60 text-white rounded-xl text-sm font-semibold transition-colors shadow-md hover:shadow-lg">
                                                            <i class="fas" :class="loading && activeLessonId === {{ (int) $lesson->id }} ? 'fa-spinner fa-spin' : 'fa-play'"></i>
                                                            <span x-text="loading && activeLessonId === {{ (int) $lesson->id }} ? 'جاري الفتح...' : 'معاينة'"></span>
                                                        </button>
                                                    </div>
                                                @elseif($isUnlocked && !$hasVideo)
                                                    <div class="flex-shrink-0">
                                                        <span class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-200 text-slate-600 rounded-xl text-sm font-semibold">
                                                            <i class="fas fa-video-slash"></i>
                                                            <span>قريباً</span>
                                                        </span>
                                                    </div>
                                                @elseif($isLocked)
                                                    <div class="flex-shrink-0">
                                                        <a href="{{ $previewUnlockCta }}"
                                                           @click.stop
                                                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-700 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold transition-colors shadow-md">
                                                            <i class="fas fa-unlock-alt"></i>
                                                            <span>فتح بالشراء</span>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- بوب أب معاينة الفيديو -->
                                <template x-teleport="body">
                                    <div x-show="open"
                                         x-cloak
                                         class="fixed inset-0 z-[100000] flex items-center justify-center p-3 sm:p-6"
                                         style="background: rgba(15, 23, 42, 0.82); backdrop-filter: blur(6px);"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         @keydown.escape.window="closePopup()"
                                         @click.self="closePopup()"
                                         role="dialog"
                                         aria-modal="true">
                                        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[92vh] flex flex-col overflow-hidden"
                                             @click.stop
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                                            <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-gray-200 bg-gradient-to-l from-slate-50 to-white">
                                                <div class="min-w-0 flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center flex-shrink-0">
                                                        <i class="fas fa-play"></i>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-xs font-bold text-blue-600 mb-0.5">معاينة مجانية</p>
                                                        <h4 class="text-base sm:text-lg font-black text-gray-900 truncate" x-text="title"></h4>
                                                    </div>
                                                </div>
                                                <button type="button" @click="closePopup()" class="p-2.5 rounded-xl text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition-colors" aria-label="إغلاق">
                                                    <i class="fas fa-times text-xl"></i>
                                                </button>
                                            </div>
                                            <div class="flex-1 min-h-0 p-4 bg-slate-900">
                                                <div x-show="error" class="rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold p-4 mb-3" x-text="error"></div>
                                                <div x-show="loading && !watchUrl" class="aspect-video w-full bg-black rounded-xl flex items-center justify-center text-white">
                                                    <div class="text-center">
                                                        <i class="fas fa-spinner fa-spin text-3xl mb-3 text-blue-400"></i>
                                                        <p class="text-sm font-semibold">جاري فتح المشغّل...</p>
                                                    </div>
                                                </div>
                                                <div class="aspect-video w-full bg-black rounded-xl overflow-hidden shadow-inner" x-show="watchUrl">
                                                    <iframe :src="watchUrl"
                                                            class="w-full h-full border-0"
                                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                                                            allowfullscreen
                                                            referrerpolicy="strict-origin-when-cross-origin"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <script>
                                function coursePreviewPopup(courseId) {
                                    return {
                                        open: false,
                                        loading: false,
                                        watchUrl: '',
                                        title: '',
                                        error: '',
                                        activeLessonId: null,
                                        urlCache: {},
                                        async openPreview(lessonId, title) {
                                            this.title = title || 'معاينة';
                                            this.error = '';
                                            this.activeLessonId = lessonId;
                                            this.open = true;
                                            document.body.style.overflow = 'hidden';

                                            if (this.urlCache[lessonId]) {
                                                this.watchUrl = this.urlCache[lessonId];
                                                this.loading = false;
                                                return;
                                            }

                                            this.loading = true;
                                            this.watchUrl = '';
                                            try {
                                                const res = await fetch('/course/' + courseId + '/preview-watch-url/' + lessonId, {
                                                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                                                    credentials: 'same-origin'
                                                });
                                                const data = await res.json().catch(function () { return {}; });
                                                if (!res.ok || !data.watch_url) {
                                                    throw new Error(data.error || 'تعذر فتح المعاينة.');
                                                }
                                                this.urlCache[lessonId] = data.watch_url;
                                                this.watchUrl = data.watch_url;
                                            } catch (e) {
                                                this.error = (e && e.message) ? e.message : 'تعذر فتح المعاينة.';
                                                this.watchUrl = '';
                                            } finally {
                                                this.loading = false;
                                            }
                                        },
                                        closePopup() {
                                            this.open = false;
                                            this.watchUrl = '';
                                            this.error = '';
                                            this.activeLessonId = null;
                                            this.loading = false;
                                            document.body.style.overflow = '';
                                        }
                                    };
                                }
                            </script>
                        @endif

                        @if(isset($sections) && $sections->count() > 0)
                            <div class="mt-8" x-data="{ showAllSections: false, sectionLimit: 6 }">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="fas fa-folder-open text-amber-500"></i>
                                    تقسيمات الكورس
                                    <span class="text-sm font-semibold text-slate-500">({{ $sections->count() }})</span>
                                </h3>
                                <ul class="space-y-2">
                                    @foreach($sections as $idx => $section)
                                        <li class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100"
                                            x-show="showAllSections || {{ $idx }} < sectionLimit"
                                            @if($idx >= 6) x-cloak @endif>
                                            <span class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center text-sm font-bold">{{ $idx + 1 }}</span>
                                            <span class="font-semibold text-gray-800">{{ $section->title }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                                @if($sections->count() > 6)
                                    <div class="mt-4 text-center">
                                        <button type="button" @click="showAllSections = !showAllSections"
                                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm">
                                            <span x-text="showAllSections ? 'عرض أقل' : 'عرض كل التقسيمات ({{ $sections->count() }})'"></span>
                                            <i class="fas" :class="showAllSections ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if(!isset($previewVideoLessons) || $previewVideoLessons->count() == 0)
                            @if(isset($sections) && $sections->count() > 0)
                                <p class="text-sm text-gray-500">لا توجد فيديوهات معاينة في هذا الكورس. سجّل في الكورس لمشاهدة المحتوى كاملاً.</p>
                            @else
                                <p class="text-sm text-gray-500">لا توجد تقسيمات أو فيديوهات معاينة. سجّل في الكورس لمشاهدة المحتوى.</p>
                            @endif
                        @endif
                    </div>

                    <!-- What You'll Learn — مختصر مع عرض المزيد لتنظيم الصفحة -->
                    @if($learnPoints->isNotEmpty())
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 lg:p-8 border border-gray-200 fade-in-up"
                         style="animation-delay: 0.1s;"
                         x-data="{ expanded: false, limit: {{ $learnPreviewCount }} }">
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                            <h2 class="text-2xl lg:text-3xl font-black text-gray-900 flex items-center gap-3">
                                <i class="fas fa-graduation-cap text-blue-600"></i>
                                هتطلع من الكورس بإيه؟
                            </h2>
                            <span class="text-sm font-semibold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                                {{ $learnPoints->count() }} نقطة
                            </span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($learnPoints as $idx => $point)
                                <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-blue-50 to-green-50 rounded-xl border border-blue-100 hover:border-blue-300 transition-all duration-300"
                                     x-show="expanded || {{ $idx }} < limit"
                                     @if($idx >= $learnPreviewCount) x-cloak @endif>
                                    <i class="fas fa-check-circle text-green-600 mt-1 flex-shrink-0"></i>
                                    <span class="text-gray-700">{{ $point }}</span>
                                </div>
                            @endforeach
                        </div>
                        @if($learnPoints->count() > $learnPreviewCount)
                            <div class="mt-6 text-center">
                                <button type="button"
                                        @click="expanded = !expanded"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm transition-colors">
                                    <span x-text="expanded ? 'عرض أقل' : 'عرض المزيد ({{ $learnPoints->count() - $learnPreviewCount }})'"></span>
                                    <i class="fas" :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                    @endif

                    @if(count($infoBlocks))
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 lg:p-8 border border-gray-200 fade-in-up" style="animation-delay: 0.15s;">
                        <h2 class="text-2xl lg:text-3xl font-black text-gray-900 mb-6 flex items-center gap-3">
                            <i class="fas fa-circle-info text-blue-600"></i>
                            معلومات مهمة عن الكورس
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($infoBlocks as $block)
                                <div class="rounded-xl border bg-gradient-to-br {{ $block['tone'] }} p-5">
                                    <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                                        <i class="fas {{ $block['icon'] }} {{ $block['iconColor'] }}"></i>
                                        {{ $block['title'] }}
                                    </h3>
                                    <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $block['content'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($course->requirements)
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 lg:p-8 border border-gray-200 fade-in-up" style="animation-delay: 0.2s;">
                        <h2 class="text-2xl lg:text-3xl font-black text-gray-900 mb-6 flex items-center gap-3">
                            <i class="fas fa-list-check text-blue-600"></i>
                            {{ __('public.requirements') }}
                        </h2>
                        <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-xl p-6 border border-gray-200">
                            <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $course->requirements }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="space-y-6 course-sidebar">
                        <!-- Course Info Card -->
                        <div class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 p-6 lg:p-8 border-2 border-gray-100 hover:border-blue-200 relative overflow-hidden group">
                            <!-- Decorative Background -->
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-100/50 to-green-100/50 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            
                            <div class="relative z-10">
                                <h3 class="text-2xl font-black text-gray-900 mb-6 text-center flex items-center justify-center gap-2">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-green-500 rounded-xl flex items-center justify-center shadow-lg">
                                        <i class="fas fa-info-circle text-white text-lg"></i>
                                    </div>
                                    <span>{{ __('public.course_info') }}</span>
                                </h3>
                            
                            <div class="space-y-3">
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-green-50 rounded-xl border-2 border-blue-100 hover:border-blue-300 hover:shadow-md transition-all duration-300 group/item">
                                        <span class="text-gray-700 font-semibold flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md group-hover/item:scale-110 transition-transform duration-300">
                                                <i class="fas fa-clock text-white text-sm"></i>
                                            </div>
                                            <span>{{ __('public.duration') }}</span>
                                    </span>
                                        <span class="font-black text-gray-900 text-lg">{{ $course->display_duration_label }}</span>
                                </div>
                                
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-blue-50 rounded-xl border-2 border-green-100 hover:border-green-300 hover:shadow-md transition-all duration-300 group/item">
                                        <span class="text-gray-700 font-semibold flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center shadow-md group-hover/item:scale-110 transition-transform duration-300">
                                                <i class="fas fa-play-circle text-white text-sm"></i>
                                            </div>
                                            <span>{{ __('public.lectures_count_label') }}</span>
                                    </span>
                                        <span class="font-black text-gray-900 text-lg">{{ $course->lectures_count ?? $course->total_lectures ?? 0 }} {{ __('public.lecture_single') }}</span>
                                </div>
                                
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl border-2 border-purple-100 hover:border-purple-300 hover:shadow-md transition-all duration-300 group/item">
                                        <span class="text-gray-700 font-semibold flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center shadow-md group-hover/item:scale-110 transition-transform duration-300">
                                                <i class="fas fa-signal text-white text-sm"></i>
                                            </div>
                                            <span>المستوى</span>
                                    </span>
                                        <span class="font-black text-gray-900 text-lg">
                                        @if($course->level == 'beginner') مبتدئ
                                        @elseif($course->level == 'intermediate') متوسط
                                        @else متقدم
                                        @endif
                                    </span>
                                </div>
                                
                                @if($course->academicSubject)
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-indigo-50 to-blue-50 rounded-xl border-2 border-indigo-100 hover:border-indigo-300 hover:shadow-md transition-all duration-300 group/item">
                                        <span class="text-gray-700 font-semibold flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-md group-hover/item:scale-110 transition-transform duration-300">
                                                <i class="fas fa-book text-white text-sm"></i>
                                            </div>
                                            <span>المادة</span>
                                    </span>
                                        <span class="font-black text-gray-900 text-lg">{{ $course->academicSubject->name }}</span>
                                </div>
                                @endif

                                @if(!empty($courseMindMapVisible ?? false))
                                    <a href="{{ route('public.course.mind-map', $course->id) }}" class="flex items-center justify-center gap-2 p-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl border-2 border-emerald-200 hover:border-emerald-400 hover:shadow-md transition-all duration-300 font-bold text-emerald-800">
                                        <i class="fas fa-route"></i>
                                        {{ __('public.course_mind_map_button') }}
                                    </a>
                                @endif
                            </div>

                                <div class="mt-8 pt-6 border-t-2 border-gray-200">
                                @if($course->effectivePrice() > 0)
                                        <div class="text-center mb-6 p-4 bg-gradient-to-br from-blue-50 to-green-50 rounded-xl border-2 border-blue-100">
                                            @if($course->hasCourseDiscount())
                                                <div class="text-lg text-gray-400 line-through font-bold mb-1">{{ number_format($course->originalPrice(), 0) }} ج.م</div>
                                            @endif
                                            <div class="text-4xl font-black text-blue-600 mb-1">{{ number_format($course->effectivePrice(), 0) }}</div>
                                            <div class="text-sm text-gray-600 font-semibold">ج.م</div>
                                    </div>
                                @else
                                        <div class="text-center mb-6 p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border-2 border-green-100">
                                            <div class="text-4xl font-black text-green-600 flex items-center justify-center gap-2 mb-1">
                                                <i class="fas fa-gift text-2xl"></i>
                                            <span>مجاني</span>
                                        </div>
                                    </div>
                                @endif
                                
                                @auth
                                        <a href="{{ route('courses.show', $course->id) }}" class="group/btn relative inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-6 py-4 rounded-xl font-bold text-base shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 w-full overflow-hidden">
                                            <div class="absolute inset-0 bg-gradient-to-r from-green-500 to-blue-500 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></div>
                                            <i class="fas fa-play relative z-10"></i>
                                            <span class="relative z-10">ابدأ التعلم</span>
                                    </a>
                                @endauth
                                @guest
                                        @if(($course->effectivePrice() ?? 0) > 0 && !($course->is_free ?? false))
                                            <a href="{{ route('public.course.checkout', $course->id) }}" class="group/btn relative inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-6 py-4 rounded-xl font-bold text-base shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 w-full overflow-hidden">
                                                <div class="absolute inset-0 bg-gradient-to-r from-green-500 to-blue-500 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></div>
                                                <i class="fas fa-shopping-cart relative z-10"></i>
                                                <span class="relative z-10">{{ __('public.buy_now') }}</span>
                                            </a>
                                        @else
                                            <a href="{{ route('register', ['redirect' => route('public.course.enroll.free', $course->id)]) }}" class="group/btn relative inline-flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 via-green-500 to-emerald-500 text-white px-6 py-4 rounded-xl font-bold text-base shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 w-full overflow-hidden">
                                                <div class="absolute inset-0 bg-gradient-to-r from-emerald-500 to-green-500 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></div>
                                                <i class="fas fa-gift relative z-10"></i>
                                                <span class="relative z-10">{{ __('public.register_free') }}</span>
                                    </a>
                                        @endif
                                @endguest
                                @if($courseWhatsappUrl)
                                    <a href="{{ $courseWhatsappUrl }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       data-course-whatsapp-inline
                                       class="mt-3 inline-flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1ebe57] text-white px-6 py-3.5 rounded-xl font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 w-full">
                                        <i class="fab fa-whatsapp text-lg"></i>
                                        تواصل مع التيم عبر واتساب
                                    </a>
                                @endif
                                </div>
                            </div>
                        </div>

                        <!-- Related Courses (سطح المكتب فقط — على الهاتف تظهر بعد التقييمات) -->
                        @if(isset($relatedCourses) && count($relatedCourses) > 0)
                        <div class="hidden lg:block bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-200">
                            <h3 class="text-xl font-black text-gray-900 mb-4">كورسات ذات صلة</h3>
                            <div class="space-y-4">
                                @foreach($relatedCourses->take(3) as $index => $related)
                                @php
                                    $relThumb = $related->thumbnail ? str_replace('\\', '/', $related->thumbnail) : null;
                                    $relImageUrl = $relThumb ? asset('storage/' . $relThumb) : null;
                                @endphp
                                <a href="{{ route('public.course.show', $related->id) }}" class="flex gap-4 p-0 bg-gray-50 rounded-xl hover:bg-blue-50 transition-all duration-300 border border-gray-200 hover:border-blue-300 hover:shadow-md overflow-hidden fade-in-up" style="animation-delay: {{ $index * 0.1 }}s;">
                                    <div class="w-24 h-24 flex-shrink-0 bg-gradient-to-br from-blue-600 to-green-500 flex items-center justify-center">
                                        @if($relImageUrl)
                                            <img src="{{ $relImageUrl }}" alt="{{ $related->localized('title') }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-book text-white text-2xl"></i>
                                        @endif
                                    </div>
                                    <div class="p-4 flex-1 min-w-0">
                                        <h4 class="font-bold text-gray-900 mb-1 text-base">{{ $related->localized('title') }}</h4>
                                        <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit($related->localized('description') ?: '', 60) }}</p>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- التقييمات — سكرول جانبي بعرض الصفحة -->
    <section class="py-10 md:py-14 bg-white relative z-10 border-y border-slate-100" x-data="courseReviewsGallery()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="course-reviews-section-head">
                <div class="head-copy">
                    <div class="inline-flex items-center gap-2 mb-1.5">
                        <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 inline-flex items-center justify-center">
                            <i class="fas fa-star"></i>
                        </span>
                        <h2 class="text-xl md:text-2xl font-black text-gray-900">التقييمات</h2>
                    </div>
                    @if(($reviewsCount ?? 0) > 0)
                        <p class="text-sm text-gray-500">
                            <span class="font-bold text-amber-500">{{ number_format((float) ($reviewsAvg ?? 0), 1) }}</span>
                            <span class="text-gray-400">/ 5</span>
                            <span class="mx-1">·</span>
                            <span>{{ number_format((int) ($reviewsCount ?? 0)) }} تقييم</span>
                        </p>
                    @endif
                </div>
                @if(isset($approvedReviews) && $approvedReviews->count() > 1)
                    <div class="course-reviews-nav">
                        <button type="button" @click="scrollBy(-1)" aria-label="السابق">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button type="button" @click="scrollBy(1)" aria-label="التالي">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </div>
                @endif
            </div>

            @if(session('success') && !session('payment_success_modal'))
                <div class="mb-4 max-w-2xl mx-auto p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold text-center">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 max-w-2xl mx-auto p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            @auth
                @if($isEnrolled ?? false)
                    <form action="{{ route('public.course.reviews.store', $course->id) }}" method="POST"
                          class="mb-6 max-w-3xl mx-auto space-y-3 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        @csrf
                        <div class="grid sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-1">
                                <label class="block text-sm font-bold text-gray-800 mb-2">تقييمك <span class="text-rose-500">*</span></label>
                                <select name="rating" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                                    <option value="">اختر</option>
                                    @for($i=5; $i>=1; $i--)
                                        <option value="{{ $i }}" @selected((string) old('rating') === (string) $i)>{{ $i }} / 5</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-bold text-gray-800 mb-2">اكتب مراجعتك <span class="text-rose-500">*</span></label>
                                <textarea name="comment" rows="2" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500" placeholder="شارك رأيك في الكورس">{{ old('comment') }}</textarea>
                            </div>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg">
                            <i class="fas fa-paper-plane"></i>
                            نشر
                        </button>
                    </form>
                @endif
            @endauth

            @if(isset($approvedReviews) && $approvedReviews->count() > 0)
                <div class="course-reviews-rail" x-ref="rail">
                    @foreach($approvedReviews as $r)
                        @php
                            $hasImage = filled($r->image_path);
                            $body = $r->body_text;
                        @endphp
                        <article class="course-review-card {{ $hasImage ? 'has-image' : 'is-text-only' }}">
                            @if($hasImage)
                                <button type="button" class="review-media" @click="openLightbox(@js($r->image_url))" title="تكبير">
                                    <img src="{{ $r->image_url }}" alt="تقييم {{ $r->display_name }}" loading="lazy" decoding="async">
                                </button>
                                <div class="review-meta">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="font-semibold text-gray-900 text-xs truncate">{{ $r->display_name }}</div>
                                        <div class="flex items-center gap-0.5 shrink-0">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star text-[9px] {{ $i <= (int) $r->rating ? 'text-amber-400' : 'text-gray-200' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    @if($body !== '')
                                        <p class="text-gray-500 text-[11px] leading-snug line-clamp-2 mt-1 whitespace-pre-wrap">{{ $body }}</p>
                                    @endif
                                </div>
                            @else
                                <div class="review-meta flex-1 flex flex-col justify-between gap-2 p-4">
                                    <div>
                                        <div class="flex items-center gap-0.5 mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star text-[11px] {{ $i <= (int) $r->rating ? 'text-amber-400' : 'text-gray-200' }}"></i>
                                            @endfor
                                        </div>
                                        @if($body !== '')
                                            <p class="text-gray-700 text-xs leading-relaxed line-clamp-5 whitespace-pre-wrap">{{ $body }}</p>
                                        @endif
                                    </div>
                                    <div class="font-semibold text-gray-900 text-xs truncate pt-2 border-t border-slate-100">{{ $r->display_name }}</div>
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            @else
                <div class="text-center text-sm text-gray-500 py-6">لا توجد تقييمات منشورة بعد.</div>
            @endif
        </div>

        <div x-show="lightbox" x-cloak class="course-review-lightbox" @click.self="closeLightbox()" @keydown.escape.window="closeLightbox()">
            <button type="button" class="absolute top-4 left-4 w-11 h-11 rounded-full bg-white/10 text-white hover:bg-white/20" @click="closeLightbox()" aria-label="إغلاق">
                <i class="fas fa-times"></i>
            </button>
            <img :src="lightbox" alt="معاينة التقييم" @click.stop>
        </div>
    </section>

    <!-- Related Courses — نسخة الهاتف: تظهر بعد التقييمات فقط -->
    @if(isset($relatedCourses) && count($relatedCourses) > 0)
    <section class="lg:hidden py-10 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                <h3 class="text-xl font-black text-gray-900 mb-4">كورسات ذات صلة</h3>
                <div class="space-y-4">
                    @foreach($relatedCourses->take(3) as $index => $related)
                        @php
                            $relThumb = $related->thumbnail ? str_replace('\\', '/', $related->thumbnail) : null;
                            $relImageUrl = $relThumb ? asset('storage/' . $relThumb) : null;
                        @endphp
                        <a href="{{ route('public.course.show', $related->id) }}" class="flex gap-4 p-0 bg-gray-50 rounded-xl hover:bg-blue-50 transition-all duration-300 border border-gray-200 hover:border-blue-300 hover:shadow-md overflow-hidden">
                            <div class="w-24 h-24 flex-shrink-0 bg-gradient-to-br from-blue-600 to-green-500 flex items-center justify-center">
                                @if($relImageUrl)
                                    <img src="{{ $relImageUrl }}" alt="{{ $related->localized('title') }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-book text-white text-2xl"></i>
                                @endif
                            </div>
                            <div class="p-4 flex-1 min-w-0">
                                <h4 class="font-bold text-gray-900 mb-1 text-base">{{ $related->localized('title') }}</h4>
                                <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit($related->localized('description') ?: '', 60) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

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
                @auth
                    <a href="{{ route('courses.show', $course->id) }}" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-8 py-4 rounded-full font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 relative overflow-hidden group">
                        <span class="relative z-10 flex items-center gap-2">
                            <i class="fas fa-play"></i>
                            <span>{{ __('public.start_learning_now') }}</span>
                        </span>
                        <span class="absolute inset-0 bg-gradient-to-r from-green-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                    </a>
                @endauth
                @guest
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-8 py-4 rounded-full font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 relative overflow-hidden group">
                        <span class="relative z-10 flex items-center gap-2">
                            <i class="fas fa-user-plus"></i>
                            <span>{{ __('public.register_free') }} الآن</span>
                        </span>
                        <span class="absolute inset-0 bg-gradient-to-r from-green-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                    </a>
                @endguest
                <a href="{{ route('public.courses') }}" class="inline-flex items-center justify-center gap-2 bg-white text-blue-600 px-8 py-4 rounded-full font-bold text-lg border-2 border-blue-600 hover:bg-blue-50 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl relative">
                    <span class="flex items-center gap-2">
                        <span>استعرض {{ __('public.all_courses') }}</span>
                        <i class="fas fa-arrow-left"></i>
                    </span>
                </a>
            </div>
        </div>
    </section>

    </main>

    @if($courseWhatsappUrl)
        <a href="{{ $courseWhatsappUrl }}"
           id="course-whatsapp-float"
           class="course-whatsapp-float"
           target="_blank"
           rel="noopener noreferrer"
           aria-label="تواصل مع التيم عبر واتساب">
            <span class="course-whatsapp-icon"><i class="fab fa-whatsapp"></i></span>
            <span class="course-whatsapp-label">تواصل مع التيم</span>
        </a>
    @endif

    @if($courseBuyFloatUrl)
        <a href="{{ $courseBuyFloatUrl }}"
           id="course-buy-float"
           class="course-buy-float"
           aria-label="{{ $courseBuyFloatLabel }}">
            <span class="course-buy-icon"><i class="fas {{ $courseBuyFloatIcon }}"></i></span>
            <span class="course-buy-label">{{ $courseBuyFloatLabel }}</span>
        </a>
    @endif
    
    <!-- Unified Footer -->
    @include('components.unified-footer')

    <!-- Dynamic JavaScript -->
    <script>
        (function () {
            var floatBtns = [
                document.getElementById('course-whatsapp-float'),
                document.getElementById('course-buy-float')
            ].filter(Boolean);
            if (!floatBtns.length) return;

            var threshold = 280;
            function updateFloat() {
                var visible = window.scrollY > threshold;
                floatBtns.forEach(function (btn) {
                    btn.classList.toggle('is-visible', visible);
                });
            }

            window.addEventListener('scroll', updateFloat, { passive: true });
            updateFloat();
        })();

        function courseReviewsGallery() {
            return {
                lightbox: null,
                scrollBy(dir) {
                    var rail = this.$refs.rail;
                    if (!rail) return;
                    rail.scrollBy({ left: dir * -220, behavior: 'smooth' });
                },
                openLightbox(url) {
                    this.lightbox = url || null;
                    if (this.lightbox) document.body.style.overflow = 'hidden';
                },
                closeLightbox() {
                    this.lightbox = null;
                    document.body.style.overflow = '';
                }
            };
        }

        // إضافة أرقام طائرة ديناميكية
        function createFloatingNumber() {
            const numbers = ['{}', '</>', '#', '()', '[]'];
            const container = document.querySelector('.floating-numbers');
            
            if (!container) return;
            
            const number = document.createElement('div');
            number.className = 'floating-number';
            number.textContent = numbers[Math.floor(Math.random() * numbers.length)];
            number.style.left = Math.random() * 100 + '%';
            number.style.animationDelay = Math.random() * 5 + 's';
            number.style.fontSize = (Math.random() * 1.5 + 1.5) + 'rem';
            number.style.color = `rgba(14, 165, 233, 0.3)`;
            
            container.appendChild(number);
            
            setTimeout(() => {
                if (number.parentNode) {
                    number.parentNode.removeChild(number);
                }
            }, 15000);
        }

        function createParticle() {
            const particlesContainer = document.querySelector('.particles');
            if (!particlesContainer) return;
            
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 2 + 's';
            particle.style.animationDuration = (Math.random() * 5 + 8) + 's';
            particle.style.background = 'rgba(255, 255, 255, 0.5)';
            
            particlesContainer.appendChild(particle);
            
            setTimeout(() => {
                if (particle.parentNode) {
                    particle.parentNode.removeChild(particle);
                }
            }, 10000);
        }

        setInterval(createFloatingNumber, 1500);
        setInterval(createParticle, 800);
    </script>

</body>
</html>

