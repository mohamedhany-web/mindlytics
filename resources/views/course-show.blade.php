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
            
            /* Sidebar styling - no sticky, no scroll */
            .course-sidebar {
                position: relative;
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

            /* Immersive course hero — Mindlytics blue/emerald identity */
            .course-immersive-hero {
                --hero-blue: #1e40af;
                --hero-blue-deep: #1e3a8a;
                --hero-blue-bright: #2563eb;
                --hero-emerald: #10b981;
                --hero-emerald-deep: #059669;
                --hero-accent: #34d399;
                --hero-accent-strong: #10b981;
                --hero-ink: #f8fafc;
                --hero-muted: rgba(226, 232, 240, 0.88);
                position: relative;
                min-height: min(92svh, 860px);
                display: flex;
                align-items: flex-end;
                overflow: hidden;
                color: var(--hero-ink);
                background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 45%, #0f766e 100%);
            }
            .course-immersive-hero__media,
            .course-immersive-hero__media img {
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .course-immersive-hero__media {
                transform: scale(1.03);
                animation: courseHeroKen 18s ease-in-out infinite alternate;
            }
            .course-immersive-hero__media--fallback {
                background:
                    radial-gradient(circle at 18% 20%, rgba(56, 189, 248, 0.28), transparent 42%),
                    radial-gradient(circle at 82% 18%, rgba(16, 185, 129, 0.22), transparent 40%),
                    linear-gradient(135deg, #1e3a8a 0%, #1e40af 42%, #2563eb 68%, #0f766e 100%);
            }
            @keyframes courseHeroKen {
                from { transform: scale(1.02) translate3d(0, 0, 0); }
                to { transform: scale(1.06) translate3d(-1%, -0.8%, 0); }
            }
            .course-immersive-hero__shade {
                position: absolute;
                inset: 0;
                background:
                    linear-gradient(90deg, rgba(30, 58, 138, 0.94) 0%, rgba(30, 64, 175, 0.78) 38%, rgba(5, 150, 105, 0.28) 100%),
                    linear-gradient(0deg, rgba(15, 23, 42, 0.72) 0%, rgba(30, 64, 175, 0.22) 52%, rgba(30, 58, 138, 0.45) 100%);
            }
            [dir="ltr"] .course-immersive-hero__shade {
                background:
                    linear-gradient(270deg, rgba(30, 58, 138, 0.94) 0%, rgba(30, 64, 175, 0.78) 38%, rgba(5, 150, 105, 0.28) 100%),
                    linear-gradient(0deg, rgba(15, 23, 42, 0.72) 0%, rgba(30, 64, 175, 0.22) 52%, rgba(30, 58, 138, 0.45) 100%);
            }
            .course-immersive-hero__glow {
                position: absolute;
                inset: auto auto -10% -8%;
                width: min(55vw, 420px);
                height: min(55vw, 420px);
                border-radius: 999px;
                background: radial-gradient(circle, rgba(16, 185, 129, 0.28), transparent 68%);
                filter: blur(8px);
                pointer-events: none;
            }
            [dir="ltr"] .course-immersive-hero__glow {
                inset: auto -8% -10% auto;
            }
            .course-immersive-hero__inner {
                position: relative;
                z-index: 2;
                width: 100%;
                max-width: 72rem;
                margin: 0 auto;
                padding: 7.5rem 1.25rem 3.5rem;
            }
            @media (min-width: 1024px) {
                .course-immersive-hero__inner { padding: 8.5rem 2rem 4.5rem; }
            }
            .course-immersive-brand {
                font-size: clamp(2rem, 5vw, 3.4rem);
                font-weight: 900;
                letter-spacing: -0.03em;
                line-height: 1;
                margin-bottom: 1rem;
            }
            .course-immersive-brand span {
                background: linear-gradient(90deg, #93c5fd, #6ee7b7);
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
            }
            .course-immersive-badges {
                display: flex;
                flex-wrap: wrap;
                gap: 0.6rem;
                margin-bottom: 1.1rem;
            }
            .course-immersive-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                padding: 0.4rem 0.85rem;
                border-radius: 999px;
                font-size: 0.72rem;
                font-weight: 800;
                letter-spacing: 0.02em;
                backdrop-filter: blur(10px);
            }
            .course-immersive-badge--offer {
                background: rgba(16, 185, 129, 0.2);
                color: #a7f3d0;
                border: 1px solid rgba(110, 231, 183, 0.4);
            }
            .course-immersive-badge--type {
                background: rgba(37, 99, 235, 0.28);
                color: #dbeafe;
                border: 1px solid rgba(147, 197, 253, 0.35);
            }
            .course-immersive-title {
                max-width: 16ch;
                font-size: clamp(2.1rem, 5.4vw, 4.2rem);
                font-weight: 900;
                line-height: 1.08;
                letter-spacing: -0.03em;
                margin-bottom: 1rem;
                text-shadow: 0 10px 30px rgba(15, 23, 42, 0.35);
            }
            .course-immersive-title em {
                font-style: normal;
                background: linear-gradient(90deg, #6ee7b7, #34d399);
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
            }
            .course-immersive-lead {
                max-width: 38rem;
                color: var(--hero-muted);
                font-size: clamp(0.98rem, 1.6vw, 1.15rem);
                line-height: 1.75;
                margin-bottom: 1.4rem;
            }
            .course-immersive-countdown {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 0.75rem 1rem;
                margin-bottom: 1.4rem;
            }
            .course-immersive-countdown__label {
                font-size: 0.8rem;
                font-weight: 700;
                color: rgba(219, 234, 254, 0.85);
            }
            .course-immersive-countdown__grid {
                display: flex;
                gap: 0.45rem;
            }
            .course-immersive-countdown__cell {
                min-width: 3.35rem;
                text-align: center;
                padding: 0.45rem 0.35rem;
                border-radius: 0.7rem;
                background: rgba(30, 58, 138, 0.55);
                border: 1px solid rgba(147, 197, 253, 0.28);
                box-shadow: inset 0 0 0 1px rgba(16, 185, 129, 0.08);
            }
            .course-immersive-countdown__num {
                display: block;
                font-size: 1.15rem;
                font-weight: 900;
                font-variant-numeric: tabular-nums;
            }
            .course-immersive-countdown__unit {
                display: block;
                font-size: 0.65rem;
                color: rgba(191, 219, 254, 0.7);
                font-weight: 700;
            }
            .course-immersive-cta {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.75rem;
                min-height: 3.4rem;
                padding: 0.9rem 1.4rem;
                border-radius: 999px;
                background: linear-gradient(90deg, #2563eb 0%, #3b82f6 45%, #10b981 100%);
                color: #fff;
                font-weight: 900;
                font-size: 1rem;
                box-shadow: 0 14px 36px rgba(37, 99, 235, 0.38);
                transition: transform 0.25s ease, box-shadow 0.25s ease, filter 0.25s ease;
            }
            .course-immersive-cta:hover {
                transform: translateY(-2px);
                filter: brightness(1.06);
                box-shadow: 0 18px 40px rgba(16, 185, 129, 0.35);
            }
            .course-immersive-cta__old {
                text-decoration: line-through;
                opacity: 0.72;
                font-weight: 800;
            }
            .course-immersive-secondary {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.8rem 1.2rem;
                border-radius: 999px;
                border: 1px solid rgba(147, 197, 253, 0.45);
                background: rgba(255, 255, 255, 0.08);
                color: #eff6ff;
                font-weight: 800;
                font-size: 0.9rem;
                transition: background 0.2s ease, border-color 0.2s ease;
            }
            .course-immersive-secondary:hover {
                background: rgba(37, 99, 235, 0.22);
                border-color: rgba(110, 231, 183, 0.45);
            }
            .course-immersive-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 0.85rem 1.25rem;
                margin-top: 1.35rem;
                color: rgba(226, 232, 240, 0.9);
                font-size: 0.86rem;
                font-weight: 700;
            }
            .course-immersive-meta span {
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
            }
            .course-immersive-meta i {
                color: #6ee7b7;
            }
            .course-immersive-float {
                position: fixed;
                z-index: 60;
                display: flex;
                flex-direction: column;
                gap: 0.65rem;
                bottom: 1.25rem;
            }
            [dir="rtl"] .course-immersive-float { left: 1rem; }
            [dir="ltr"] .course-immersive-float { right: 1rem; }
            .course-immersive-float a {
                display: inline-flex;
                align-items: center;
                gap: 0.55rem;
                padding: 0.7rem 1rem;
                border-radius: 999px;
                font-size: 0.82rem;
                font-weight: 800;
                color: #fff;
                box-shadow: 0 10px 28px rgba(30, 64, 175, 0.28);
                transition: transform 0.2s ease;
            }
            .course-immersive-float a:hover { transform: translateY(-2px); }
            .course-immersive-float__wa { background: linear-gradient(135deg, #059669, #10b981); }
            .course-immersive-float__ask { background: linear-gradient(135deg, #1e3a8a, #2563eb); }

            @media (prefers-reduced-motion: reduce) {
                .course-immersive-hero__media { animation: none; }
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
        </style>
    </head>

<body class="bg-gray-50 text-gray-900"
      x-data="{ mobileMenu: false, searchQuery: '' }"
      :class="{ 'overflow-hidden': mobileMenu }">

    @include('components.unified-navbar')

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
    <!-- Immersive course hero -->
    @php
        $heroThumb = $course->thumbnail ? str_replace('\\', '/', $course->thumbnail) : null;
        $heroImageUrl = $heroThumb ? asset('storage/' . $heroThumb) : null;
        $heroLecturesCount = (int) ($course->lectures_count ?? $course->total_lectures ?? 0);
        $heroDurationLabel = $course->display_duration_label;
        $heroHasDiscount = $course->hasCourseDiscount();
        $heroIsPaid = ($course->effectivePrice() ?? 0) > 0 && !($course->is_free ?? false);
        $heroOfferEndsAt = $course->ends_at && $course->ends_at->isFuture() ? $course->ends_at->toIso8601String() : null;
        $heroShowOffer = $heroHasDiscount || $heroOfferEndsAt;
        $heroTitle = $course->localized('title') ?: __('public.course_title_fallback');
        $heroWords = preg_split('/\s+/u', trim($heroTitle), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $heroAccentWord = count($heroWords) > 1 ? array_pop($heroWords) : null;
        $heroTitleLead = $heroAccentWord ? implode(' ', $heroWords) : $heroTitle;
        $heroDesc = \Illuminate\Support\Str::limit(strip_tags($course->localized('description') ?: __('public.course_desc_fallback')), 180);
        $heroCheckoutUrl = route('public.course.checkout', $course->id);
        $heroFreeUrl = auth()->check()
            ? route('public.course.enroll.free', $course->id)
            : route('register', ['redirect' => route('public.course.enroll.free', $course->id)]);
        $heroPrimaryUrl = ($isEnrolled ?? false)
            ? route('courses.show', $course->id)
            : ($heroIsPaid ? $heroCheckoutUrl : $heroFreeUrl);
        $heroLevelLabel = $course->level === 'beginner'
            ? __('public.level_beginner')
            : ($course->level === 'intermediate' ? __('public.level_intermediate') : __('public.level_advanced'));
        $heroWhatsApp = 'https://wa.me/201044610507?text=' . rawurlencode('مهتم بكورس: ' . $heroTitle);
    @endphp
    <section class="course-immersive-hero" aria-label="{{ $heroTitle }}">
        <div class="course-immersive-hero__media {{ $heroImageUrl ? '' : 'course-immersive-hero__media--fallback' }}" aria-hidden="true">
            @if($heroImageUrl)
                <img src="{{ $heroImageUrl }}" alt="" loading="eager" fetchpriority="high">
            @endif
        </div>
        <div class="course-immersive-hero__shade" aria-hidden="true"></div>
        <div class="course-immersive-hero__glow" aria-hidden="true"></div>

        <div class="course-immersive-hero__inner"
             @if($heroOfferEndsAt)
                 x-data="courseOfferCountdown(@js($heroOfferEndsAt))"
                 x-init="tick()"
             @endif>
            <p class="course-immersive-brand"><span>Mindlytics</span></p>

            <div class="course-immersive-badges">
                @if($heroShowOffer)
                    <span class="course-immersive-badge course-immersive-badge--offer">
                        <i class="fas fa-bolt"></i>
                        {{ $__pageRtl ? 'عرض لفترة محدودة' : 'Limited time offer' }}
                    </span>
                @endif
                <span class="course-immersive-badge course-immersive-badge--type">
                    {{ $__pageRtl ? 'كورس أونلاين مسجّل' : 'Recorded Online Course' }}
                </span>
            </div>

            <h1 class="course-immersive-title">
                @if($heroAccentWord)
                    {{ $heroTitleLead }} <em>{{ $heroAccentWord }}</em>
                @else
                    {{ $heroTitle }}
                @endif
            </h1>

            <p class="course-immersive-lead">
                {{ $heroDesc }}
                @if($heroLecturesCount > 0)
                    <strong class="text-white">
                        {{ $__pageRtl
                            ? " · {$heroLecturesCount} محاضرة مسجّلة — اتعلّم بوتيرتك."
                            : " · {$heroLecturesCount} recorded lectures — learn at your own pace." }}
                    </strong>
                @endif
            </p>

            @if($heroOfferEndsAt)
                <div class="course-immersive-countdown" x-show="alive" x-cloak>
                    <span class="course-immersive-countdown__label">{{ $__pageRtl ? 'ينتهي العرض خلال' : 'Offer ends in' }}</span>
                    <div class="course-immersive-countdown__grid" dir="ltr">
                        <div class="course-immersive-countdown__cell">
                            <span class="course-immersive-countdown__num" x-text="days">00</span>
                            <span class="course-immersive-countdown__unit">{{ $__pageRtl ? 'يوم' : 'Day' }}</span>
                        </div>
                        <div class="course-immersive-countdown__cell">
                            <span class="course-immersive-countdown__num" x-text="hours">00</span>
                            <span class="course-immersive-countdown__unit">{{ $__pageRtl ? 'ساعة' : 'Hour' }}</span>
                        </div>
                        <div class="course-immersive-countdown__cell">
                            <span class="course-immersive-countdown__num" x-text="mins">00</span>
                            <span class="course-immersive-countdown__unit">{{ $__pageRtl ? 'د' : 'Min' }}</span>
                        </div>
                        <div class="course-immersive-countdown__cell">
                            <span class="course-immersive-countdown__num" x-text="secs">00</span>
                            <span class="course-immersive-countdown__unit">{{ $__pageRtl ? 'ث' : 'Sec' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex flex-wrap items-center gap-3">
                @if($isEnrolled ?? false)
                    <a href="{{ $heroPrimaryUrl }}" class="course-immersive-cta">
                        <i class="fas fa-play"></i>
                        <span>{{ __('public.start_learning_now') }}</span>
                        <i class="fas {{ $__pageRtl ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                    </a>
                @elseif($heroIsPaid)
                    <a href="{{ $heroPrimaryUrl }}" class="course-immersive-cta">
                        @if($heroHasDiscount)
                            <span class="course-immersive-cta__old">{{ number_format($course->originalPrice(), 0) }} {{ $__pageRtl ? 'ج.م' : 'EGP' }}</span>
                            <span>{{ $__pageRtl ? 'اشترك الآن' : 'Enroll Now' }}, {{ number_format($course->effectivePrice(), 0) }} {{ $__pageRtl ? 'ج.م فقط' : 'EGP only' }}</span>
                        @else
                            <span>{{ $__pageRtl ? 'اشترك الآن' : 'Enroll Now' }} — {{ number_format($course->effectivePrice(), 0) }} {{ $__pageRtl ? 'ج.م' : 'EGP' }}</span>
                        @endif
                        <i class="fas {{ $__pageRtl ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                    </a>
                @else
                    <a href="{{ $heroPrimaryUrl }}" class="course-immersive-cta">
                        <i class="fas fa-gift"></i>
                        <span>{{ __('public.register_free') }}</span>
                    </a>
                @endif

                @if(!empty($courseMindMapVisible ?? false))
                    <a href="{{ route('public.course.mind-map', $course->id) }}" class="course-immersive-secondary">
                        <i class="fas fa-diagram-project"></i>
                        {{ __('public.course_mind_map_short') }}
                    </a>
                @endif
            </div>

            <div class="course-immersive-meta">
                <span><i class="fas fa-play-circle"></i> {{ $heroLecturesCount }} {{ __('public.lecture_single') }}</span>
                <span><i class="fas fa-clock"></i> {{ $heroDurationLabel }}</span>
                <span><i class="fas fa-signal"></i> {{ $heroLevelLabel }}</span>
                <span><i class="fas fa-certificate"></i> {{ $__pageRtl ? 'شهادة إتمام' : 'Certificate' }}</span>
            </div>
        </div>
    </section>

    <div class="course-immersive-float" aria-label="{{ $__pageRtl ? 'تواصل سريع' : 'Quick contact' }}">
        <a href="{{ $heroWhatsApp }}" target="_blank" rel="noopener" class="course-immersive-float__wa">
            <i class="fab fa-whatsapp text-lg"></i>
            <span>{{ $__pageRtl ? 'كلّم الفريق' : 'Talk to the team' }}</span>
        </a>
    </div>

    <!-- Course Details Section -->
    <section class="py-12 md:py-16 bg-gradient-to-b from-gray-50 via-white to-gray-50 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    @if($course->video_url ?? null)
                        <div class="bg-white rounded-2xl shadow-lg p-6 lg:p-8 border border-gray-200 fade-in-up">
                            <h2 class="text-2xl lg:text-3xl font-black text-gray-900 mb-4 flex items-center gap-3">
                                <i class="fas fa-play-circle text-blue-600"></i>
                                {{ __('public.intro_video_title') }}
                            </h2>
                            <p class="text-gray-500 text-sm mb-4">{{ __('public.intro_video_desc') }}</p>
                            @include('partials.intro-video-embed', [
                                'url' => trim((string) $course->video_url),
                                'title' => __('public.intro_video_title'),
                            ])
                        </div>
                    @endif

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

                    <!-- What You'll Learn -->
                    @if($course->what_you_learn)
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 lg:p-8 border border-gray-200 fade-in-up" style="animation-delay: 0.1s;">
                        <h2 class="text-2xl lg:text-3xl font-black text-gray-900 mb-6 flex items-center gap-3">
                            <i class="fas fa-graduation-cap text-blue-600"></i>
                            هتطلع من الكورس بإيه؟
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @php
                                $learnPoints = explode("\n", $course->what_you_learn);
                            @endphp
                            @foreach($learnPoints as $point)
                                @if(trim($point))
                                    <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-blue-50 to-green-50 rounded-xl border border-blue-100 hover:border-blue-300 transition-all duration-300">
                                        <i class="fas fa-check-circle text-green-600 mt-1 flex-shrink-0"></i>
                                        <span class="text-gray-700">{{ trim($point) }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

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
                    @endphp

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

                    <!-- Requirements -->
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
                            <div class="mt-8">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="fas fa-folder-open text-amber-500"></i>
                                    تقسيمات الكورس
                                </h3>
                                <ul class="space-y-2">
                                    @foreach($sections as $idx => $section)
                                        <li class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                            <span class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center text-sm font-bold">{{ $idx + 1 }}</span>
                                            <span class="font-semibold text-gray-800">{{ $section->title }}</span>
                                        </li>
                                    @endforeach
                                </ul>
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
                                </div>
                            </div>
                        </div>

                        <!-- Related Courses -->
                        @if(isset($relatedCourses) && count($relatedCourses) > 0)
                        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-200">
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

                        <!-- Reviews -->
                        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-200">
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <div>
                                    <h3 class="text-xl font-black text-gray-900">التقييمات والمراجعات</h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="font-bold text-yellow-600">{{ number_format((float) ($reviewsAvg ?? 0), 1) }}</span>
                                        <span class="text-gray-400">/ 5</span>
                                        <span class="text-gray-500">— ({{ number_format((int) ($reviewsCount ?? 0)) }} مراجعة)</span>
                                    </p>
                                </div>
                            </div>

                            @if(session('success') && !session('payment_success_modal'))
                                <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if($errors->any())
                                <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            @auth
                                @if($isEnrolled ?? false)
                                    <form action="{{ route('public.course.reviews.store', $course->id) }}" method="POST" class="mb-6 space-y-3">
                                        @csrf
                                        <div>
                                            <label class="block text-sm font-bold text-gray-800 mb-2">تقييمك <span class="text-rose-500">*</span></label>
                                            <select name="rating" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                                                <option value="">اختر التقييم</option>
                                                @for($i=5; $i>=1; $i--)
                                                    <option value="{{ $i }}" @selected((string) old('rating') === (string) $i)>{{ $i }} / 5</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-800 mb-2">اكتب مراجعتك <span class="text-rose-500">*</span></label>
                                            <textarea name="comment" rows="3" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500" placeholder="شارك رأيك في الكورس">{{ old('comment') }}</textarea>
                                        </div>
                                        <button type="submit" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition-all">
                                            <i class="fas fa-paper-plane"></i>
                                            نشر
                                        </button>
                                    </form>
                                @else
                                    <div class="mb-6 p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-sm font-semibold">
                                        يجب أن تكون مسجلاً في الكورس لتتمكن من إضافة تقييم.
                                    </div>
                                @endif
                            @endauth
                            @guest
                                <div class="mb-6 p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-sm font-semibold">
                                    <a class="text-blue-700 hover:underline" href="{{ route('login', ['redirect' => url()->current()]) }}">سجّل دخولك</a>
                                    لإضافة تقييم.
                                </div>
                            @endguest

                            @if(isset($approvedReviews) && $approvedReviews->count() > 0)
                                <div class="space-y-4">
                                    @foreach($approvedReviews as $r)
                                        <div class="p-4 rounded-xl border border-gray-200 bg-gray-50">
                                            <div class="flex items-center justify-between gap-3">
                                                <div class="font-bold text-gray-900 text-sm">{{ $r->user->name ?? 'طالب' }}</div>
                                                <div class="flex items-center gap-1 text-xs">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= (int) $r->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                            <div class="text-gray-700 text-sm mt-2 whitespace-pre-wrap">{{ $r->comment ?? $r->review ?? '' }}</div>
                                            <div class="text-xs text-gray-400 mt-2">{{ $r->created_at?->format('Y-m-d') }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-sm text-gray-500">لا توجد مراجعات منشورة بعد.</div>
                            @endif
                        </div>
                    </div>
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
    
    <!-- Unified Footer -->
    @include('components.unified-footer')

    <!-- Dynamic JavaScript -->
    <script>
        function courseOfferCountdown(isoEndsAt) {
            return {
                endsAt: new Date(isoEndsAt).getTime(),
                alive: true,
                days: '00',
                hours: '00',
                mins: '00',
                secs: '00',
                timer: null,
                pad(n) { return String(Math.max(0, n)).padStart(2, '0'); },
                tick() {
                    const self = this;
                    const run = function () {
                        const diff = self.endsAt - Date.now();
                        if (diff <= 0) {
                            self.alive = false;
                            self.days = self.hours = self.mins = self.secs = '00';
                            if (self.timer) clearInterval(self.timer);
                            return;
                        }
                        const totalSec = Math.floor(diff / 1000);
                        self.days = self.pad(Math.floor(totalSec / 86400));
                        self.hours = self.pad(Math.floor((totalSec % 86400) / 3600));
                        self.mins = self.pad(Math.floor((totalSec % 3600) / 60));
                        self.secs = self.pad(totalSec % 60);
                    };
                    run();
                    this.timer = setInterval(run, 1000);
                }
            };
        }
    </script>
    <script>
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

