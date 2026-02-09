<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>الكورسات - Mindlytics - أكاديمية البرمجة</title>

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

            /* Course Card Styles - Matches welcome page */
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
                transform: scale(1.05);
            }

            .course-card:hover .course-image::before {
                opacity: 1;
            }

            .course-card:hover .course-image i {
                transform: scale(1.1);
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
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-green-500 to-blue-600 animate-gradient-text">الكورسات البرمجية</span>
                </h1>
                <p class="text-lg md:text-xl lg:text-2xl text-gray-700 mb-10 leading-relaxed max-w-3xl mx-auto font-medium">
                    اكتشف مجموعة شاملة من الكورسات البرمجية المصممة لتناسب جميع المستويات
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
                                   placeholder="ابحث عن كورس، لغة برمجة، أو موضوع..." 
                                   class="flex-1 bg-transparent border-0 outline-none text-gray-800 placeholder-gray-400 text-base lg:text-lg transition-all duration-300 focus:placeholder-gray-300 font-medium"
                            >
                            
                            <!-- Level Filter Dropdown -->
                            <div class="flex items-center gap-3 mr-6">
                                <div class="w-px h-8 bg-gray-200"></div>
                                <div class="relative min-w-[150px] lg:min-w-[160px]">
                                    <select x-model="selectedLevel" 
                                            class="appearance-none bg-transparent border-0 outline-none text-gray-700 text-base lg:text-lg font-medium cursor-pointer pr-8 pl-2 focus:text-blue-600 transition-colors duration-300 w-full py-1">
                                        <option value="">جميع المستويات</option>
                                        <option value="beginner">مبتدئ</option>
                                        <option value="intermediate">متوسط</option>
                                        <option value="advanced">متقدم</option>
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
                                    <span class="font-bold text-sm lg:text-base">ابحث</span>
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
                                       placeholder="ابحث عن كورس..." 
                                       class="flex-1 bg-transparent border-0 outline-none text-gray-800 placeholder-gray-400 text-base font-medium"
                                >
                            </div>
                            
                            <!-- Filter and Button Row -->
                            <div class="flex items-center gap-3">
                                <!-- Level Filter -->
                                <div class="flex-1 relative">
                                    <select x-model="selectedLevel" 
                                            class="appearance-none bg-white border-2 border-gray-200 rounded-2xl px-4 py-3 pr-10 w-full text-gray-700 text-base font-medium cursor-pointer focus:border-blue-500 focus:outline-none transition-colors duration-300">
                                        <option value="">جميع المستويات</option>
                                        <option value="beginner">مبتدئ</option>
                                        <option value="intermediate">متوسط</option>
                                        <option value="advanced">متقدم</option>
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
                                <span> كورس متاح</span>
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
                    الكورسات <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-green-500">البرمجية</span>
                </h2>
                <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto">استكشف مجموعة متنوعة من الكورسات البرمجية المصممة لتناسب جميع المستويات</p>
            </div>
            
            <!-- Courses Grid -->
            <?php if(isset($courses) && is_array($courses) && count($courses) > 0): ?>
            <!-- Display courses directly from PHP as fallback -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-12 auto-rows-fr">
                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $thumbnailPath = $course['thumbnail'] ?? null;
                    // تنظيف المسار من backslashes
                    $thumbnailPath = $thumbnailPath ? str_replace('\\', '/', $thumbnailPath) : null;
                    $hasThumbnail = $thumbnailPath && !empty(trim($thumbnailPath));
                    // إنشاء URL كامل للصورة
                    $imageUrl = $hasThumbnail ? asset('storage/' . $thumbnailPath) : null;
                ?>
                <div class="course-card rounded-3xl overflow-hidden shadow-xl fade-in-up group h-full" style="animation-delay: <?php echo e($index * 0.1); ?>s;"
                     data-thumbnail="<?php echo e($thumbnailPath ?? ''); ?>"
                     data-image-url="<?php echo e($imageUrl ?? ''); ?>">
                    <div class="h-48 lg:h-44 bg-gradient-to-br from-blue-600 via-blue-500 to-green-500 flex items-center justify-center relative course-image overflow-hidden flex-shrink-0">
                        <?php if($hasThumbnail && $imageUrl): ?>
                            <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($course['title'] ?? 'كورس'); ?>" class="w-full h-full object-cover absolute inset-0 z-0" style="display: block !important; position: absolute !important; top: 0 !important; left: 0 !important; width: 100% !important; height: 100% !important; object-fit: cover !important; z-index: 0 !important;" onload="console.log('Image loaded: <?php echo e($imageUrl); ?>');" onerror="console.error('Image failed to load: <?php echo e($imageUrl); ?>'); this.style.display='none';">
                        <?php else: ?>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-black/10 to-transparent z-10"></div>
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10" style="background: radial-gradient(circle at center, rgba(255, 255, 255, 0.15) 0%, transparent 70%);"></div>
                            <i class="fas fa-code text-white text-3xl lg:text-4xl relative z-20 transition-transform duration-300 drop-shadow-lg"></i>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-black/10 to-transparent z-10"></div>
                        
                        <?php if(isset($course['is_featured']) && $course['is_featured']): ?>
                            <span class="absolute top-2 left-2 bg-gradient-to-r from-yellow-400 to-yellow-500 text-yellow-900 px-2 py-0.5 rounded-full text-[9px] font-bold shadow-md flex items-center gap-0.5 z-20">
                                <i class="fas fa-star text-[8px]"></i>
                                <span>مميز</span>
                            </span>
                        <?php endif; ?>
                        
                        <div class="absolute bottom-3 right-3 bg-white/20 backdrop-blur-md rounded-full px-2 py-1 z-20">
                            <span class="text-white text-[10px] font-bold flex items-center gap-1">
                                <i class="fas fa-play-circle text-[10px]"></i>
                                <span><?php echo e($course['lessons_count'] ?? 0); ?> درس</span>
                            </span>
                        </div>
                    </div>

                    <div class="p-6 bg-white flex-grow flex flex-col justify-between">
                        <div class="flex items-center gap-2 mb-3">
                            <?php if(isset($course['academic_subject']['name'])): ?>
                            <div class="flex items-center gap-1.5 text-xs lg:text-sm text-gray-500 bg-blue-50 px-2 lg:px-3 py-1 lg:py-1.5 rounded-full">
                                <i class="fas fa-user text-blue-600 text-[10px] lg:text-xs"></i>
                                <span class="text-blue-700 font-medium"><?php echo e(Str::limit($course['academic_subject']['name'], 15)); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <h3 class="text-lg lg:text-xl font-black text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors duration-300 leading-tight min-h-[3.5rem]"><?php echo e($course['title'] ?? 'بدون عنوان'); ?></h3>
                        
                        <p class="text-gray-600 text-xs lg:text-sm mb-3 line-clamp-2 leading-relaxed min-h-[3rem]">
                            <?php echo e(Str::limit($course['description'] ?? 'دورة برمجية متكاملة', 80)); ?>

                        </p>
                        
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100 mt-auto gap-2 relative z-10">
                            <div class="flex flex-col">
                                <?php if(isset($course['price']) && $course['price'] > 0): ?>
                                    <span class="text-base lg:text-lg font-black text-blue-600 flex items-center gap-1">
                                        <span><?php echo e(number_format($course['price'], 0)); ?></span>
                                        <span class="text-[10px] text-gray-500 font-normal">ج.م</span>
                                    </span>
                                <?php else: ?>
                                    <span class="text-base lg:text-lg font-black text-green-600 flex items-center gap-1">
                                        <i class="fas fa-gift text-xs"></i>
                                        <span>مجاني</span>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <a href="<?php echo e(route('public.course.show', $course['id'])); ?>" class="bg-gradient-to-r from-blue-600 to-green-500 text-white px-3 lg:px-4 py-1.5 lg:py-2 rounded-full text-[10px] lg:text-xs font-bold shadow-md hover:shadow-lg hover:opacity-90 transition-all duration-200 whitespace-nowrap relative z-50 pointer-events-auto">
                                <span class="flex items-center gap-1">
                                    <span class="hidden sm:inline">عرض التفاصيل</span>
                                    <span class="sm:hidden">التفاصيل</span>
                                    <i class="fas fa-arrow-left text-[10px]"></i>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                                    مميز
                                </div>
                            </template>
                            <template x-if="course.level">
                                <div class="absolute top-4 right-4 bg-white/20 backdrop-blur-sm rounded-full px-3 py-1">
                                    <span class="text-white text-xs font-medium" 
                                          x-text="course.level === 'beginner' ? 'مبتدئ' : course.level === 'intermediate' ? 'متوسط' : 'متقدم'"></span>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Course Content -->
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2" x-text="course.title"></h3>
                            <p class="text-gray-600 mb-4 text-sm" x-text="course.description ? (course.description.substring(0, 100) + '...') : 'كورس برمجي شامل ومتخصص'"></p>
                            
                            <!-- Course Info -->
                            <div class="flex items-center justify-between mb-4 text-sm text-gray-500">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-clock"></i>
                                    <span x-text="course.duration_hours ? course.duration_hours + ' ساعة' : 'غير محدد'"></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-layer-group"></i>
                                    <span x-text="(course.lessons_count || 0) + ' درس'"></span>
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
                                        <span class="text-2xl font-black text-green-600">مجاني</span>
                                    </template>
                                </div>
                                <?php if(auth()->guard()->check()): ?>
                                    <a :href="'<?php echo e(url('/')); ?>/courses/' + course.id" class="btn-primary text-sm px-6 py-2">
                                        <i class="fas fa-eye"></i>
                                        عرض التفاصيل
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo e(route('register')); ?>" class="btn-primary text-sm px-6 py-2">
                                        <i class="fas fa-sign-in-alt"></i>
                                        سجل للوصول
                                    </a>
                                <?php endif; ?>
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
                    <h3 class="text-xl font-bold text-gray-900 mb-4">لا توجد نتائج</h3>
                    <p class="text-gray-600 mb-6">جرب البحث بكلمات مختلفة أو تصفية مختلف</p>
                </div>
            </div>
            <?php else: ?>
            <!-- Empty State -->
            <div class="text-center py-12 fade-in-up">
                <div class="max-w-md mx-auto">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-book text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">قريباً...</h3>
                    <p class="text-gray-600 mb-6">نعمل على إضافة المزيد من الكورسات البرمجية لخدمتكم بشكل أفضل</p>
                    <a href="<?php echo e(route('register')); ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-full font-bold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-bell"></i>
                        <span>اشترك للحصول على التحديثات</span>
                    </a>
                </div>
            </div>
            <?php endif; ?>
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
                <a href="<?php echo e(route('login')); ?>" class="inline-flex items-center justify-center gap-2 bg-white text-blue-600 px-8 py-4 rounded-full font-bold text-lg border-2 border-blue-600 hover:bg-blue-50 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl relative">
                    <span class="flex items-center gap-2">
                        <span>لدي حساب بالفعل</span>
                        <i class="fas fa-arrow-left"></i>
                    </span>
                </a>
            </div>
        </div>
    </section>

    </main>
    
    <!-- Unified Footer -->
    <?php echo $__env->make('components.unified-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/courses.blade.php ENDPATH**/ ?>