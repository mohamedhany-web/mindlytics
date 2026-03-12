<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>{{ $course->title ?? __('public.course_detail_title') }} - {{ __('public.site_suffix') }}</title>

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
    
    <main class="pt-0 mt-0">
    {{-- رسائل النجاح / المعلومات / الأخطاء بعد إتمام الطلب أو أي إجراء --}}
    @if(session('success'))
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
                <span class="text-gray-900 font-medium">{{ Str::limit($course->title ?? __('public.course_fallback'), 30) }}</span>
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
                        {{ $course->title ?? __('public.course_title_fallback') }}
                    </h1>
                    
                    <p class="text-base md:text-lg text-gray-600 mb-5 leading-relaxed fade-in-up" style="animation-delay: 0.2s;">
                        {{ $course->description ?? __('public.course_desc_fallback') }}
                    </p>

                    <!-- Course Stats -->
                    <div class="grid grid-cols-3 gap-4 mb-6 fade-in-up" style="animation-delay: 0.1s;">
                        <div class="bg-white rounded-2xl p-4 text-center border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300">
                            <div class="text-3xl font-black text-blue-600 mb-2">{{ $course->lessons_count ?? 0 }}</div>
                            <div class="text-sm text-gray-600 font-medium">{{ __('public.lesson_single') }}</div>
                        </div>
                        <div class="bg-white rounded-2xl p-4 text-center border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300">
                            <div class="text-3xl font-black text-green-600 mb-2">{{ $course->duration_hours ?? 0 }}</div>
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
                    <div class="flex flex-col sm:flex-row gap-4 fade-in-up" style="animation-delay: 0.3s;">
                        @auth
                            @if($isEnrolled ?? false)
                                <a href="{{ route('courses.show', $course->id) }}" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-play-circle"></i>
                                    {{ __('public.start_learning_now') }}
                                </a>
                            @else
                                @if(($course->price ?? 0) > 0 && !($course->is_free ?? false))
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
                                <a href="{{ route('register', ['redirect' => route('public.course.checkout', $course->id)]) }}" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
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
                    </div>
                </div>

                <!-- Right column: Intro video + Price card -->
                <div class="flex flex-col gap-6">
                    <!-- مقدمة الكورس (نفس فكرة المسار) -->
                    <div class="relative fade-in-up max-w-xl" style="animation-delay: 0.2s;">
                        @if($course->video_url ?? null)
                        @php
                            $introVideoUrl = trim((string) ($course->video_url ?? ''));
                            $introVideoId = null;
                            $introVideoType = null;
                            $introEmbedUrl = null;
                            if ($introVideoUrl !== '') {
                                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $introVideoUrl, $m)) {
                                    $introVideoId = $m[1];
                                    $introVideoType = 'youtube';
                                    $introEmbedUrl = 'https://www.youtube.com/embed/' . $introVideoId . '?rel=0&modestbranding=1&showinfo=0';
                                } elseif (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $introVideoUrl, $m)) {
                                    $introVideoId = $m[1];
                                    $introVideoType = 'vimeo';
                                    $introEmbedUrl = 'https://player.vimeo.com/video/' . $introVideoId;
                                } elseif (preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $introVideoUrl)) {
                                    $introVideoType = 'html5';
                                }
                            }
                        @endphp
                        <div class="bg-white rounded-2xl p-4 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300">
                            <div class="text-center mb-3">
                                <h2 class="text-lg font-bold text-gray-900 mb-0.5 flex items-center justify-center gap-2">
                                    <i class="fas fa-play-circle text-blue-600 text-base"></i>
                                    مقدمة الكورس
                                </h2>
                                <p class="text-gray-500 text-sm">شاهد المقدمة</p>
                            </div>
                            <div class="custom-video-player-wrapper">
                            @if($introVideoType === 'youtube' && $introEmbedUrl)
                                <div class="intro-video-container">
                                    <iframe src="{{ $introEmbedUrl }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen title="مقدمة الكورس"></iframe>
                                </div>
                            @elseif($introVideoType === 'vimeo' && $introEmbedUrl)
                                <div class="intro-video-container">
                                    <iframe src="{{ $introEmbedUrl }}" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen title="مقدمة الكورس"></iframe>
                                </div>
                            @elseif($introVideoType === 'html5')
                                <div class="intro-video-container" style="padding-bottom: 0; height: auto; min-height: 320px;">
                                    <video class="w-full rounded-lg" style="max-height: 70vh;" playsinline controls>
                                        <source src="{{ $introVideoUrl }}" type="video/mp4">
                                        المتصفح لا يدعم تشغيل الفيديو.
                                    </video>
                                </div>
                            @else
                                <div class="bg-gray-100 rounded-lg p-6 text-center">
                                    <i class="fas fa-exclamation-triangle text-amber-500 text-2xl mb-2"></i>
                                    <p class="text-gray-700 text-sm font-medium">رابط الفيديو غير مدعوم. استخدم YouTube أو Vimeo أو رابط .mp4</p>
                                </div>
                            @endif
                            </div>
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
                            <p class="text-lg mb-4">{{ $course->description ?? __('public.course_desc_fallback') }}</p>
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
                            {{ __('public.what_you_learn') }}
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

                    <!-- محتوى الكورس: التقسيمات + معاينة أول 3 فيديوهات (للتجربة قبل الشراء) -->
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 lg:p-8 border border-gray-200 fade-in-up" style="animation-delay: 0.25s;">
                        <h2 class="text-2xl lg:text-3xl font-black text-gray-900 mb-6 flex items-center gap-3">
                            <i class="fas fa-play-circle text-blue-600"></i>
                            محتوى الكورس — معاينة
                        </h2>
                        <p class="text-gray-600 mb-6">اطّلع على أول 3 فيديوهات وتقسيمات الكورس قبل الشراء.</p>

                        @if(isset($previewVideoLessons) && $previewVideoLessons->count() > 0)
                            <div x-data="coursePreviewPopup()">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="fas fa-video text-green-600"></i>
                                    معاينة أول {{ $previewVideoLessons->count() }} فيديو
                                </h3>
                                <div class="space-y-4">
                                    @foreach($previewVideoLessons as $idx => $lesson)
                                        @php
                                            $embedUrl = null;
                                            if ($lesson->video_url) {
                                                $embedUrl = \App\Helpers\VideoHelper::getEmbedUrl(trim($lesson->video_url));
                                            }
                                            $encodedSrc = $embedUrl ? base64_encode($embedUrl) : '';
                                        @endphp
                                        <div class="rounded-xl border-2 border-gray-200 overflow-hidden bg-gray-50 hover:border-blue-300 transition-all duration-300">
                                            <div class="p-4 flex flex-col sm:flex-row sm:items-center gap-3">
                                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                                    <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-lg flex items-center justify-center">
                                                        <i class="fas fa-play text-sm"></i>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="font-semibold text-gray-900">{{ $lesson->title }}</div>
                                                        @if($lesson->duration_minutes)
                                                            <div class="text-sm text-gray-500"><i class="fas fa-clock ml-1"></i> {{ $lesson->duration_minutes }} دقيقة</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($encodedSrc)
                                                    <div class="flex-shrink-0">
                                                        <button type="button"
                                                                data-video-src="{{ $encodedSrc }}"
                                                                data-title="{{ e($lesson->title) }}"
                                                                @click="openPreview($event.currentTarget.dataset.videoSrc, $event.currentTarget.dataset.title)"
                                                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-semibold transition-colors shadow-md hover:shadow-lg">
                                                            <i class="fas fa-eye"></i>
                                                            <span>معاينة</span>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- بوب أب المعاينة (الرابط مخزّن مشفّراً base64 فلا يظهر جلياً في مصدر الصفحة) -->
                                <div x-show="open" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="background: rgba(0,0,0,0.75); backdrop-filter: blur(4px);"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     @click.self="closePopup()">
                                    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] flex flex-col overflow-hidden"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100">
                                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                                            <h4 class="text-lg font-bold text-gray-900" x-text="title"></h4>
                                            <button type="button" @click="closePopup()" class="p-2 rounded-xl text-gray-500 hover:bg-gray-200 hover:text-gray-800 transition-colors">
                                                <i class="fas fa-times text-xl"></i>
                                            </button>
                                        </div>
                                        <div class="flex-1 min-h-0 p-4 overflow-auto" x-show="embedUrl">
                                            <div class="aspect-video w-full bg-black rounded-xl overflow-hidden" x-show="embedUrl">
                                                <iframe :src="embedUrl" class="w-full h-full" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                function coursePreviewPopup() {
                                    return {
                                        open: false,
                                        embedUrl: '',
                                        title: '',
                                        openPreview(encodedSrc, title) {
                                            try {
                                                this.title = title || 'معاينة';
                                                this.embedUrl = encodedSrc ? (typeof atob !== 'undefined' ? atob(encodedSrc) : '') : '';
                                                this.open = true;
                                                document.body.style.overflow = 'hidden';
                                            } catch (e) {
                                                this.embedUrl = '';
                                            }
                                        },
                                        closePopup() {
                                            this.open = false;
                                            this.embedUrl = '';
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
                                        <span class="font-black text-gray-900 text-lg">{{ $course->duration_hours ?? 0 }} ساعة</span>
                                </div>
                                
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-blue-50 rounded-xl border-2 border-green-100 hover:border-green-300 hover:shadow-md transition-all duration-300 group/item">
                                        <span class="text-gray-700 font-semibold flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center shadow-md group-hover/item:scale-110 transition-transform duration-300">
                                                <i class="fas fa-layer-group text-white text-sm"></i>
                                            </div>
                                            <span>{{ __('public.lessons_count_label') }}</span>
                                    </span>
                                        <span class="font-black text-gray-900 text-lg">{{ $course->lessons_count ?? 0 }} درس</span>
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
                            </div>

                                <div class="mt-8 pt-6 border-t-2 border-gray-200">
                                @if(($course->price ?? 0) > 0)
                                        <div class="text-center mb-6 p-4 bg-gradient-to-br from-blue-50 to-green-50 rounded-xl border-2 border-blue-100">
                                            <div class="text-4xl font-black text-blue-600 mb-1">{{ number_format($course->price, 0) }}</div>
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
                                        @if(($course->price ?? 0) > 0 && !($course->is_free ?? false))
                                            <a href="{{ route('register', ['redirect' => route('public.course.checkout', $course->id)]) }}" class="group/btn relative inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-6 py-4 rounded-xl font-bold text-base shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 w-full overflow-hidden">
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
                                            <img src="{{ $relImageUrl }}" alt="{{ $related->title }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-book text-white text-2xl"></i>
                                        @endif
                                    </div>
                                    <div class="p-4 flex-1 min-w-0">
                                        <h4 class="font-bold text-gray-900 mb-1 text-base">{{ $related->title }}</h4>
                                        <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit($related->description ?? '', 60) }}</p>
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

