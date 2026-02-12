<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title><?php echo e($course->title ?? 'تفاصيل الكورس'); ?> - Mindlytics - أكاديمية البرمجة</title>

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
    
    <!-- Plyr - Custom Video Player -->
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    
    <!-- YouTube IFrame API -->
    <script src="https://www.youtube.com/iframe_api"></script>
    
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

            /* مشغل YouTube المخصص */
            .custom-youtube-player-container {
                position: relative;
                width: 100%;
                padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
                height: 0;
                background: #000;
                border-radius: 1rem;
                overflow: hidden;
                margin: 0;
            }

            .custom-youtube-player-container #custom-video-player {
                position: absolute;
                top: -60px; /* إخفاء الجزء العلوي الذي يحتوي على مشاركة ومشاهدة لاحقاً */
                left: 0;
                width: 100%;
                height: calc(100% + 120px); /* زيادة الارتفاع لإخفاء الأجزاء العلوية والسفلية */
                border: none;
            }

            /* إخفاء جميع عناصر YouTube */
            .custom-youtube-player-container iframe {
                border: none !important;
                position: absolute !important;
                top: -60px !important;
                left: 0 !important;
                width: 100% !important;
                height: calc(100% + 120px) !important;
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

    <?php echo $__env->make('components.unified-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <main class="pt-0 mt-0">
    
    <?php if(session('success')): ?>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 lg:pt-24 pb-2" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)">
            <div class="rounded-xl border-2 border-green-200 bg-gradient-to-r from-green-50 to-emerald-50 px-4 py-4 shadow-lg flex items-start gap-3">
                <i class="fas fa-check-circle text-green-600 text-2xl flex-shrink-0 mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-green-800 font-bold"><?php echo e(session('success')); ?></p>
                    <p class="text-green-700 text-sm mt-1">يمكنك متابعة حالة الطلب من صفحة طلباتي في لوحة التحكم.</p>
                </div>
                <button type="button" @click="show = false" class="text-green-600 hover:text-green-800 p-1"><i class="fas fa-times"></i></button>
            </div>
        </div>
    <?php endif; ?>
    <?php if(session('info')): ?>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 lg:pt-24 pb-2" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)">
            <div class="rounded-xl border-2 border-blue-200 bg-gradient-to-r from-blue-50 to-sky-50 px-4 py-4 shadow-lg flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-600 text-2xl flex-shrink-0 mt-0.5"></i>
                <p class="text-blue-800 font-bold flex-1"><?php echo e(session('info')); ?></p>
                <button type="button" @click="show = false" class="text-blue-600 hover:text-blue-800 p-1"><i class="fas fa-times"></i></button>
            </div>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 lg:pt-24 pb-2" x-data="{ show: true }" x-show="show">
            <div class="rounded-xl border-2 border-red-200 bg-gradient-to-r from-red-50 to-rose-50 px-4 py-4 shadow-lg flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-600 text-2xl flex-shrink-0 mt-0.5"></i>
                <p class="text-red-800 font-bold flex-1"><?php echo e(session('error')); ?></p>
                <button type="button" @click="show = false" class="text-red-600 hover:text-red-800 p-1"><i class="fas fa-times"></i></button>
            </div>
        </div>
    <?php endif; ?>
    <?php
        $courseThumb = $course->thumbnail ? str_replace('\\', '/', $course->thumbnail) : null;
        $courseImageUrl = $courseThumb ? asset('storage/' . $courseThumb) : null;
    ?>
    <!-- Hero Section -->
    <section class="hero-section relative overflow-hidden min-h-[70vh] flex items-center pt-16 lg:pt-20">
        <?php if($courseImageUrl): ?>
        <!-- Course cover image (like learning paths) -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?php echo e($courseImageUrl); ?>');"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-white/92 via-white/75 to-white"></div>
        </div>
        <?php endif; ?>
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
                <a href="<?php echo e(url('/')); ?>" class="hover:text-blue-600 transition-colors">الرئيسية</a>
                <span class="mx-2 text-gray-400">/</span>
                <a href="<?php echo e(route('public.courses')); ?>" class="hover:text-blue-600 transition-colors">الكورسات</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-900 font-medium"><?php echo e(Str::limit($course->title ?? 'الكورس', 30)); ?></span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-start">
                <!-- Course Info -->
                <div class="slide-in-left">
                    <?php if($course->is_featured ?? false): ?>
                        <div class="inline-flex items-center gap-1 px-2 py-0.5 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-full shadow-md mb-4 fade-in-up">
                            <i class="fas fa-star text-yellow-900 text-[8px]"></i>
                            <span class="text-yellow-900 font-bold text-[9px]">كورس مميز</span>
                        </div>
                    <?php endif; ?>
                    
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-black mb-3 leading-tight text-gray-900 fade-in-up" style="animation-delay: 0.1s;">
                        <?php echo e($course->title ?? 'اسم الكورس'); ?>

                    </h1>
                    
                    <p class="text-base md:text-lg text-gray-600 mb-5 leading-relaxed fade-in-up" style="animation-delay: 0.2s;">
                        <?php echo e($course->description ?? 'كورس برمجي شامل ومتخصص'); ?>

                    </p>

                    <!-- Course Stats -->
                    <div class="grid grid-cols-3 gap-4 mb-6 fade-in-up" style="animation-delay: 0.1s;">
                        <div class="bg-white rounded-2xl p-4 text-center border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300">
                            <div class="text-3xl font-black text-blue-600 mb-2"><?php echo e($course->lessons_count ?? 0); ?></div>
                            <div class="text-sm text-gray-600 font-medium">درس</div>
                        </div>
                        <div class="bg-white rounded-2xl p-4 text-center border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300">
                            <div class="text-3xl font-black text-green-600 mb-2"><?php echo e($course->duration_hours ?? 0); ?></div>
                            <div class="text-sm text-gray-600 font-medium">ساعة</div>
                        </div>
                        <div class="bg-white rounded-2xl p-4 text-center border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300">
                            <div class="text-xl font-black text-gray-700 mb-2">
                                <?php if($course->level == 'beginner'): ?> مبتدئ
                                <?php elseif($course->level == 'intermediate'): ?> متوسط
                                <?php else: ?> متقدم
                                <?php endif; ?>
                            </div>
                            <div class="text-sm text-gray-600 font-medium">المستوى</div>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 fade-in-up" style="animation-delay: 0.3s;">
                        <?php if(auth()->guard()->check()): ?>
                            <?php if($isEnrolled ?? false): ?>
                                <a href="<?php echo e(route('courses.show', $course->id)); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-play-circle"></i>
                                    ابدأ التعلم الآن
                                </a>
                            <?php else: ?>
                                <?php if(($course->price ?? 0) > 0 && !($course->is_free ?? false)): ?>
                                    <a href="<?php echo e(route('public.course.checkout', $course->id)); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                        <i class="fas fa-shopping-cart"></i>
                                        شراء الآن
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo e(route('public.course.enroll.free', $course->id)); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                        <i class="fas fa-gift"></i>
                                        سجل مجاناً
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if(auth()->guard()->guest()): ?>
                            <?php if(($course->price ?? 0) > 0 && !($course->is_free ?? false)): ?>
                                <a href="<?php echo e(route('register', ['redirect' => route('public.course.checkout', $course->id)])); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-shopping-cart"></i>
                                    شراء الآن
                                </a>
                            <?php else: ?>
                                <a href="<?php echo e(route('register', ['redirect' => route('public.course.enroll.free', $course->id)])); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-gift"></i>
                                    سجل مجاناً
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                        <a href="<?php echo e(route('public.courses')); ?>" class="inline-flex items-center justify-center gap-2 bg-white text-blue-600 px-6 py-3 rounded-full font-bold text-base border-2 border-blue-600 hover:bg-blue-50 transition-all duration-300">
                            <i class="fas fa-arrow-right"></i>
                            جميع الكورسات
                        </a>
                    </div>
                </div>

                <!-- Course Preview Card -->
                <div class="relative fade-in-up" style="animation-delay: 0.2s;">
                    <div class="bg-white rounded-3xl p-6 lg:p-8 shadow-xl border border-gray-200 hover:shadow-2xl transition-all duration-300">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-green-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                                <i class="fas fa-code text-white text-3xl"></i>
                            </div>
                            
                            <!-- Price -->
                            <div class="mb-6">
                                <?php if(($course->price ?? 0) > 0): ?>
                                    <div class="text-4xl font-black text-blue-600 mb-2"><?php echo e(number_format($course->price, 2)); ?> <span class="text-xl text-gray-600">ج.م</span></div>
                                <?php else: ?>
                                    <div class="text-4xl font-black text-green-600 mb-2">مجاني</div>
                                <?php endif; ?>
                            </div>

                            <!-- Course Features -->
                            <div class="space-y-3 mb-6 text-right">
                                <div class="flex items-center gap-3 text-gray-700">
                                    <i class="fas fa-check-circle text-green-500"></i>
                                    <span class="text-sm">وصول مدى الحياة</span>
                                </div>
                                <div class="flex items-center gap-3 text-gray-700">
                                    <i class="fas fa-check-circle text-green-500"></i>
                                    <span class="text-sm">شهادة إتمام معتمدة</span>
                                </div>
                                <div class="flex items-center gap-3 text-gray-700">
                                    <i class="fas fa-check-circle text-green-500"></i>
                                    <span class="text-sm">مشاريع عملية</span>
                                </div>
                                <div class="flex items-center gap-3 text-gray-700">
                                    <i class="fas fa-check-circle text-green-500"></i>
                                    <span class="text-sm">دعم مباشر</span>
                                </div>
                            </div>

                            <?php if(auth()->guard()->check()): ?>
                                <?php if($isEnrolled ?? false): ?>
                                    <a href="<?php echo e(route('courses.show', $course->id)); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 w-full">
                                        <i class="fas fa-play"></i>
                                        ابدأ الآن
                                    </a>
                                <?php else: ?>
                                    <?php if(($course->price ?? 0) > 0 && !($course->is_free ?? false)): ?>
                                        <a href="<?php echo e(route('public.course.checkout', $course->id)); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 w-full">
                                            <i class="fas fa-shopping-cart"></i>
                                            شراء الآن
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('public.course.enroll.free', $course->id)); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 w-full">
                                            <i class="fas fa-gift"></i>
                                            سجل مجاناً
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if(auth()->guard()->guest()): ?>
                                <?php if(($course->price ?? 0) > 0 && !($course->is_free ?? false)): ?>
                                    <a href="<?php echo e(route('register', ['redirect' => route('public.course.checkout', $course->id)])); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 w-full">
                                        <i class="fas fa-shopping-cart"></i>
                                        شراء الآن
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo e(route('register', ['redirect' => route('public.course.enroll.free', $course->id)])); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 to-green-500 text-white px-6 py-3 rounded-full font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 w-full">
                                        <i class="fas fa-gift"></i>
                                        سجل مجاناً
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Video Introduction Section -->
                <?php if($course->video_url ?? null): ?>
                <div class="relative fade-in-up" style="animation-delay: 0.3s;">
                    <div class="bg-white rounded-3xl p-6 lg:p-8 shadow-xl border border-gray-200 hover:shadow-2xl transition-all duration-300">
                        <div class="text-center mb-6">
                            <h2 class="text-2xl md:text-3xl font-black text-gray-900 mb-2">
                                <i class="fas fa-video text-blue-600 ml-2"></i>
                                فيديو مقدمة الكورس
                            </h2>
                            <p class="text-gray-600 text-sm">شاهد هذا الفيديو للتعرف على محتوى الكورس</p>
                        </div>
                        
                        <div class="custom-video-player-wrapper">
                            <?php
                                $videoUrl = $course->video_url;
                                $videoId = null;
                                $videoType = null;
                                
                                // استخراج ID من رابط YouTube
                                if (strpos($videoUrl, 'youtube.com/watch') !== false || strpos($videoUrl, 'youtu.be/') !== false) {
                                    preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $videoUrl, $matches);
                                    if (isset($matches[1])) {
                                        $videoId = $matches[1];
                                        $videoType = 'youtube';
                                    }
                                } elseif (strpos($videoUrl, 'vimeo.com') !== false) {
                                    preg_match('/vimeo\.com\/(?:.*\/)?(\d+)/', $videoUrl, $matches);
                                    if (isset($matches[1])) {
                                        $videoId = $matches[1];
                                        $videoType = 'vimeo';
                                    }
                                } elseif (preg_match('/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i', $videoUrl)) {
                                    $videoType = 'html5';
                                }
                            ?>
                            
                            <?php if($videoType === 'youtube' && $videoId): ?>
                                <!-- مشغل الفيديو المخصص - YouTube -->
                                <div id="custom-video-player-container" class="custom-youtube-player-container">
                                    <div id="custom-video-player"></div>
                                </div>
                            <?php elseif($videoType === 'vimeo' && $videoId): ?>
                                <!-- مشغل الفيديو المخصص - Vimeo -->
                                <div class="plyr__video-embed" id="custom-video-player" data-plyr-provider="vimeo" data-plyr-embed-id="<?php echo e($videoId); ?>"></div>
                            <?php elseif($videoType === 'html5'): ?>
                                <!-- مشغل الفيديو المخصص - HTML5 -->
                                <video id="custom-video-player" class="plyr__video" playsinline controls>
                                    <source src="<?php echo e($videoUrl); ?>" type="video/mp4">
                                </video>
                            <?php else: ?>
                                <div class="bg-gray-100 rounded-lg p-8 text-center">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl mb-3"></i>
                                    <p class="text-gray-600">رابط الفيديو غير مدعوم</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
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
                            عن الكورس
                        </h2>
                        <div class="prose max-w-none text-gray-700 leading-relaxed">
                            <p class="text-lg mb-4"><?php echo e($course->description ?? 'كورس برمجي شامل ومتخصص'); ?></p>
                            <?php if($course->objectives): ?>
                                <div class="mt-6">
                                    <h3 class="text-xl font-bold text-gray-900 mb-4">أهداف الكورس:</h3>
                                    <div class="bg-gradient-to-br from-blue-50 to-green-50 rounded-xl p-6 border border-blue-100">
                                        <p class="text-gray-700 whitespace-pre-line leading-relaxed"><?php echo e($course->objectives); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- What You'll Learn -->
                    <?php if($course->what_you_learn): ?>
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 lg:p-8 border border-gray-200 fade-in-up" style="animation-delay: 0.1s;">
                        <h2 class="text-2xl lg:text-3xl font-black text-gray-900 mb-6 flex items-center gap-3">
                            <i class="fas fa-graduation-cap text-blue-600"></i>
                            ما ستعلمه
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php
                                $learnPoints = explode("\n", $course->what_you_learn);
                            ?>
                            <?php $__currentLoopData = $learnPoints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(trim($point)): ?>
                                    <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-blue-50 to-green-50 rounded-xl border border-blue-100 hover:border-blue-300 transition-all duration-300">
                                        <i class="fas fa-check-circle text-green-600 mt-1 flex-shrink-0"></i>
                                        <span class="text-gray-700"><?php echo e(trim($point)); ?></span>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Requirements -->
                    <?php if($course->requirements): ?>
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 lg:p-8 border border-gray-200 fade-in-up" style="animation-delay: 0.2s;">
                        <h2 class="text-2xl lg:text-3xl font-black text-gray-900 mb-6 flex items-center gap-3">
                            <i class="fas fa-list-check text-blue-600"></i>
                            المتطلبات
                        </h2>
                        <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-xl p-6 border border-gray-200">
                            <p class="text-gray-700 whitespace-pre-line leading-relaxed"><?php echo e($course->requirements); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
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
                                    <span>معلومات الكورس</span>
                                </h3>
                            
                            <div class="space-y-3">
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-green-50 rounded-xl border-2 border-blue-100 hover:border-blue-300 hover:shadow-md transition-all duration-300 group/item">
                                        <span class="text-gray-700 font-semibold flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md group-hover/item:scale-110 transition-transform duration-300">
                                                <i class="fas fa-clock text-white text-sm"></i>
                                            </div>
                                            <span>المدة</span>
                                    </span>
                                        <span class="font-black text-gray-900 text-lg"><?php echo e($course->duration_hours ?? 0); ?> ساعة</span>
                                </div>
                                
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-blue-50 rounded-xl border-2 border-green-100 hover:border-green-300 hover:shadow-md transition-all duration-300 group/item">
                                        <span class="text-gray-700 font-semibold flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center shadow-md group-hover/item:scale-110 transition-transform duration-300">
                                                <i class="fas fa-layer-group text-white text-sm"></i>
                                            </div>
                                            <span>عدد الدروس</span>
                                    </span>
                                        <span class="font-black text-gray-900 text-lg"><?php echo e($course->lessons_count ?? 0); ?> درس</span>
                                </div>
                                
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl border-2 border-purple-100 hover:border-purple-300 hover:shadow-md transition-all duration-300 group/item">
                                        <span class="text-gray-700 font-semibold flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center shadow-md group-hover/item:scale-110 transition-transform duration-300">
                                                <i class="fas fa-signal text-white text-sm"></i>
                                            </div>
                                            <span>المستوى</span>
                                    </span>
                                        <span class="font-black text-gray-900 text-lg">
                                        <?php if($course->level == 'beginner'): ?> مبتدئ
                                        <?php elseif($course->level == 'intermediate'): ?> متوسط
                                        <?php else: ?> متقدم
                                        <?php endif; ?>
                                    </span>
                                </div>
                                
                                <?php if($course->academicSubject): ?>
                                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-indigo-50 to-blue-50 rounded-xl border-2 border-indigo-100 hover:border-indigo-300 hover:shadow-md transition-all duration-300 group/item">
                                        <span class="text-gray-700 font-semibold flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-md group-hover/item:scale-110 transition-transform duration-300">
                                                <i class="fas fa-book text-white text-sm"></i>
                                            </div>
                                            <span>المادة</span>
                                    </span>
                                        <span class="font-black text-gray-900 text-lg"><?php echo e($course->academicSubject->name); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>

                                <div class="mt-8 pt-6 border-t-2 border-gray-200">
                                <?php if(($course->price ?? 0) > 0): ?>
                                        <div class="text-center mb-6 p-4 bg-gradient-to-br from-blue-50 to-green-50 rounded-xl border-2 border-blue-100">
                                            <div class="text-4xl font-black text-blue-600 mb-1"><?php echo e(number_format($course->price, 0)); ?></div>
                                            <div class="text-sm text-gray-600 font-semibold">ج.م</div>
                                    </div>
                                <?php else: ?>
                                        <div class="text-center mb-6 p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border-2 border-green-100">
                                            <div class="text-4xl font-black text-green-600 flex items-center justify-center gap-2 mb-1">
                                                <i class="fas fa-gift text-2xl"></i>
                                            <span>مجاني</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if(auth()->guard()->check()): ?>
                                        <a href="<?php echo e(route('courses.show', $course->id)); ?>" class="group/btn relative inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-6 py-4 rounded-xl font-bold text-base shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 w-full overflow-hidden">
                                            <div class="absolute inset-0 bg-gradient-to-r from-green-500 to-blue-500 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></div>
                                            <i class="fas fa-play relative z-10"></i>
                                            <span class="relative z-10">ابدأ التعلم</span>
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->guard()->guest()): ?>
                                        <?php if(($course->price ?? 0) > 0 && !($course->is_free ?? false)): ?>
                                            <a href="<?php echo e(route('register', ['redirect' => route('public.course.checkout', $course->id)])); ?>" class="group/btn relative inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-6 py-4 rounded-xl font-bold text-base shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 w-full overflow-hidden">
                                                <div class="absolute inset-0 bg-gradient-to-r from-green-500 to-blue-500 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></div>
                                                <i class="fas fa-shopping-cart relative z-10"></i>
                                                <span class="relative z-10">شراء الآن</span>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('register', ['redirect' => route('public.course.enroll.free', $course->id)])); ?>" class="group/btn relative inline-flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 via-green-500 to-emerald-500 text-white px-6 py-4 rounded-xl font-bold text-base shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 w-full overflow-hidden">
                                                <div class="absolute inset-0 bg-gradient-to-r from-emerald-500 to-green-500 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></div>
                                                <i class="fas fa-gift relative z-10"></i>
                                                <span class="relative z-10">سجل مجاناً</span>
                                    </a>
                                        <?php endif; ?>
                                <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Related Courses -->
                        <?php if(isset($relatedCourses) && count($relatedCourses) > 0): ?>
                        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-200">
                            <h3 class="text-xl font-black text-gray-900 mb-4">كورسات ذات صلة</h3>
                            <div class="space-y-4">
                                <?php $__currentLoopData = $relatedCourses->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $relThumb = $related->thumbnail ? str_replace('\\', '/', $related->thumbnail) : null;
                                    $relImageUrl = $relThumb ? asset('storage/' . $relThumb) : null;
                                ?>
                                <a href="<?php echo e(route('public.course.show', $related->id)); ?>" class="flex gap-4 p-0 bg-gray-50 rounded-xl hover:bg-blue-50 transition-all duration-300 border border-gray-200 hover:border-blue-300 hover:shadow-md overflow-hidden fade-in-up" style="animation-delay: <?php echo e($index * 0.1); ?>s;">
                                    <div class="w-24 h-24 flex-shrink-0 bg-gradient-to-br from-blue-600 to-green-500 flex items-center justify-center">
                                        <?php if($relImageUrl): ?>
                                            <img src="<?php echo e($relImageUrl); ?>" alt="<?php echo e($related->title); ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <i class="fas fa-code text-white text-2xl"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="p-4 flex-1 min-w-0">
                                        <h4 class="font-bold text-gray-900 mb-1 text-base"><?php echo e($related->title); ?></h4>
                                        <p class="text-sm text-gray-600 line-clamp-2"><?php echo e(Str::limit($related->description ?? '', 60)); ?></p>
                                    </div>
                                </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <?php endif; ?>
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
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('courses.show', $course->id)); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-8 py-4 rounded-full font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 relative overflow-hidden group">
                        <span class="relative z-10 flex items-center gap-2">
                            <i class="fas fa-play"></i>
                            <span>ابدأ التعلم الآن</span>
                        </span>
                        <span class="absolute inset-0 bg-gradient-to-r from-green-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                    </a>
                <?php endif; ?>
                <?php if(auth()->guard()->guest()): ?>
                    <a href="<?php echo e(route('register')); ?>" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-8 py-4 rounded-full font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 relative overflow-hidden group">
                        <span class="relative z-10 flex items-center gap-2">
                            <i class="fas fa-user-plus"></i>
                            <span>سجل مجاناً الآن</span>
                        </span>
                        <span class="absolute inset-0 bg-gradient-to-r from-green-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                    </a>
                <?php endif; ?>
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

    <!-- Plyr JavaScript -->
    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    
    <!-- Script لمشغل الفيديو المخصص -->
    <script>
        <?php if(isset($course->video_url) && $course->video_url): ?>
        <?php
            $videoUrl = $course->video_url;
            $videoId = null;
            $videoType = null;
            
            if (strpos($videoUrl, 'youtube.com/watch') !== false || strpos($videoUrl, 'youtu.be/') !== false) {
                preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $videoUrl, $matches);
                if (isset($matches[1])) {
                    $videoId = $matches[1];
                    $videoType = 'youtube';
                }
            } elseif (strpos($videoUrl, 'vimeo.com') !== false) {
                preg_match('/vimeo\.com\/(?:.*\/)?(\d+)/', $videoUrl, $matches);
                if (isset($matches[1])) {
                    $videoId = $matches[1];
                    $videoType = 'vimeo';
                }
            } elseif (preg_match('/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i', $videoUrl)) {
                $videoType = 'html5';
            }
        ?>
        
        <?php if($videoType === 'youtube' && $videoId): ?>
        let youtubePlayer;
        
        // تهيئة YouTube IFrame API
        function onYouTubeIframeAPIReady() {
            youtubePlayer = new YT.Player('custom-video-player', {
                height: '100%',
                width: '100%',
                videoId: '<?php echo e($videoId); ?>',
                playerVars: {
                    'autoplay': 0,
                    'controls': 1,
                    'rel': 0,
                    'showinfo': 0,
                    'modestbranding': 1,
                    'iv_load_policy': 3,
                    'cc_load_policy': 0,
                    'disablekb': 0,
                    'fs': 1,
                    'playsinline': 1,
                    'origin': window.location.origin,
                    'widget_referrer': window.location.origin,
                    'enablejsapi': 1,
                    'autohide': 1,
                    'wmode': 'opaque',
                    'loop': 0,
                    'playlist': ''
                },
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange
                }
            });
        }

        function onPlayerReady(event) {
            // إخفاء جميع علامات YouTube
            hideAllYouTubeBranding();
            setInterval(hideAllYouTubeBranding, 1000);
        }

        function onPlayerStateChange(event) {
            setTimeout(hideAllYouTubeBranding, 500);
        }

        function hideAllYouTubeBranding() {
            try {
                const container = document.querySelector('.custom-youtube-player-container');
                const iframe = container ? container.querySelector('iframe') : null;
                
                if (!iframe) return;
                
                // إضافة CSS لإخفاء علامات YouTube باستخدام CSS clipping
                const style = document.createElement('style');
                style.id = 'youtube-complete-hide';
                style.textContent = `
                    /* إخفاء جميع عناصر YouTube باستخدام CSS clipping */
                    .custom-youtube-player-container {
                        position: relative !important;
                        overflow: hidden !important;
                    }
                    
                    .custom-youtube-player-container #custom-video-player {
                        position: absolute !important;
                        top: -60px !important;
                        left: 0 !important;
                        width: 100% !important;
                        height: calc(100% + 120px) !important;
                    }
                    
                    .custom-youtube-player-container iframe {
                        border: none !important;
                        position: absolute !important;
                        top: -60px !important;
                        left: 0 !important;
                        width: 100% !important;
                        height: calc(100% + 120px) !important;
                    }
                `;
                
                const oldStyle = document.getElementById('youtube-complete-hide');
                if (oldStyle) oldStyle.remove();
                document.head.appendChild(style);
            } catch(e) {
                console.log('Hiding YouTube branding...');
            }
        }

        // التأكد من تحميل API
        if (typeof YT !== 'undefined' && YT.Player) {
            onYouTubeIframeAPIReady();
        } else {
            window.onYouTubeIframeAPIReady = onYouTubeIframeAPIReady;
        }
        <?php elseif($videoType === 'vimeo' && $videoId || $videoType === 'html5'): ?>
        document.addEventListener('DOMContentLoaded', function() {
            // تهيئة مشغل الفيديو المخصص للـ Vimeo و HTML5
            const player = new Plyr('#custom-video-player', {
                controls: [
                    'play-large',
                    'play',
                    'progress',
                    'current-time',
                    'duration',
                    'mute',
                    'volume',
                    'settings',
                    'pip',
                    'airplay',
                    'fullscreen'
                ],
                settings: ['quality', 'speed'],
                quality: {
                    default: 720,
                    options: [4320, 2880, 2160, 1440, 1080, 720, 576, 480, 360, 240]
                },
                speed: {
                    selected: 1,
                    options: [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2]
                },
                keyboard: {
                    focused: true,
                    global: false
                },
                tooltips: {
                    controls: true,
                    seek: true
                },
                hideControls: true,
                clickToPlay: true,
                autoplay: false,
                volume: 1,
                muted: false,
                ratio: '16:9',
                vimeo: {
                    byline: false,
                    portrait: false,
                    title: false,
                    transparent: false
                }
            });
        });
        <?php endif; ?>
        <?php endif; ?>
    </script>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/course-show.blade.php ENDPATH**/ ?>