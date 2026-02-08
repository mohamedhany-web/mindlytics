<style>
* {
    font-family: 'Cairo', system-ui, sans-serif;
}

/* Navbar - نفس الصفحة الرئيسية (unified) */
.navbar-gradient {
    background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 50%, #1e3a8a 100%);
    background-image: linear-gradient(135deg, #1e40af 0%, #1e3a8a 50%, #1e3a8a 100%),
        repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255, 255, 255, 0.02) 10px, rgba(255, 255, 255, 0.02) 20px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2), 0 0 40px rgba(30, 58, 138, 0.3);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
    z-index: 1000;
    transition: background 0.3s ease, box-shadow 0.3s ease;
    backdrop-filter: blur(10px) saturate(150%);
    -webkit-backdrop-filter: blur(10px) saturate(150%);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    will-change: auto;
    transform: translateZ(0);
    contain: layout style paint;
    max-width: 100vw;
    overflow-x: hidden;
}
@media (max-width: 768px) {
    .navbar-gradient {
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }
}
.navbar-gradient::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), rgba(59, 130, 246, 0.6), rgba(255, 255, 255, 0.4), transparent);
    opacity: 0.8;
    transition: all 0.5s ease;
    animation: navbarShimmer 3s ease-in-out infinite;
}
.navbar-gradient::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(59, 130, 246, 0.1) 0%, transparent 50%);
    pointer-events: none;
    animation: navbarPulse 4s ease-in-out infinite;
}
.navbar-gradient.scrolled {
    box-shadow: 0 6px 30px rgba(0, 0, 0, 0.25), 0 0 50px rgba(30, 58, 138, 0.4);
    background: linear-gradient(135deg, rgba(30, 64, 175, 0.98) 0%, rgba(30, 58, 138, 0.98) 50%, rgba(30, 58, 138, 0.98) 100%);
    backdrop-filter: blur(12px) saturate(150%);
    -webkit-backdrop-filter: blur(12px) saturate(150%);
    border-bottom-width: 1px;
    border-bottom-color: rgba(255, 255, 255, 0.15);
}
.navbar-gradient.scrolled::before {
    opacity: 1;
    height: 3px;
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.4);
}
@keyframes navbarShimmer {
    0%, 100% { opacity: 0.7; }
    50% { opacity: 1; }
}
@keyframes navbarPulse {
    0%, 100% { opacity: 0.1; }
    50% { opacity: 0.2; }
}

.hero-gradient {
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.85) 25%, rgba(14, 165, 233, 0.7) 50%, rgba(14, 165, 233, 0.75) 75%, rgba(2, 132, 199, 0.8) 100%);
    position: relative;
    overflow: hidden;
}

/* Navbar Scrolled State - شفافية مع نص واضح */
#mainNavbar.navbar-scrolled {
    background: linear-gradient(to bottom, rgba(240, 249, 255, 0.9), rgba(224, 242, 254, 0.85)) !important;
    backdrop-filter: blur(20px) !important;
    border-bottom: 1px solid rgba(59, 130, 246, 0.2) !important;
}

/* Text Shadow for readability when navbar is transparent */
#mainNavbar.navbar-scrolled h1,
#mainNavbar.navbar-scrolled p,
#mainNavbar.navbar-scrolled a,
#mainNavbar.navbar-scrolled .text-white {
    color: #1e3a8a !important;
    text-shadow: 0 1px 3px rgba(255, 255, 255, 0.5),
                 0 0 2px rgba(255, 255, 255, 0.3) !important;
}

/* Enhanced logo visibility when scrolled */
#mainNavbar.navbar-scrolled .logo-animation {
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3),
                0 0 8px rgba(59, 130, 246, 0.4) !important;
}

.hero-gradient::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
    animation: patternMove 20s linear infinite;
}

@keyframes patternMove {
    0% { transform: translateX(0) translateY(0); }
    100% { transform: translateX(60px) translateY(60px); }
}

.glass-effect {
    background: linear-gradient(to bottom, rgba(240, 249, 255, 0.85), rgba(224, 242, 254, 0.8));
    backdrop-filter: blur(15px);
    border: 1px solid rgba(59, 130, 246, 0.2);
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
}

.glass-effect::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(59, 130, 246, 0.15), transparent);
    transform: rotate(45deg);
    transition: all 0.6s ease;
    opacity: 0;
}

.glass-effect:hover::before {
    animation: shimmer 1.5s ease-in-out;
    opacity: 1;
}

@keyframes shimmer {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

.card-hover {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
}

.card-hover::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: all 0.6s ease;
}

.card-hover:hover::before {
    left: 100%;
}

.card-hover:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
}

.btn-primary {
    background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 30%, #0369a1 50%, #475569 75%, #dc2626 95%, #991b1b 100%);
    color: white;
    padding: 15px 40px;
    border-radius: 50px;
    font-weight: 700;
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
    box-shadow: 0 4px 20px rgba(14, 165, 233, 0.5),
                0 2px 10px rgba(220, 38, 38, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.1);
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.btn-primary:hover::before {
    left: 100%;
}

.btn-primary:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 8px 30px rgba(14, 165, 233, 0.7),
                0 4px 15px rgba(220, 38, 38, 0.5),
                0 0 0 1px rgba(255, 255, 255, 0.2);
    background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 25%, #0369a1 45%, #475569 70%, #dc2626 90%, #991b1b 100%);
}

.btn-primary:active {
    transform: translateY(-1px) scale(1.02);
}

.btn-secondary {
    background: white;
    color: #0ea5e9;
    padding: 15px 40px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.4s ease;
    border: 2px solid #0ea5e9;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 35%, #0369a1 60%, #475569 80%, #dc2626 100%);
    color: white;
    transform: translateY(-3px) scale(1.05);
}

.btn-outline {
    background: transparent;
    color: white;
    padding: 15px 40px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.4s ease;
    border: 2px solid white;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}

.btn-outline:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-3px) scale(1.05);
}

.nav-link {
    position: relative;
    transition: all 0.3s ease;
}

.mobile-accordion {
    border-radius: 1.25rem;
    border: 1px solid rgba(148, 163, 184, 0.25);
    background: rgba(248, 250, 252, 0.95);
    padding: 1rem 1.25rem;
}

.mobile-accordion button {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-weight: 700;
    color: #0f172a;
}

.mobile-accordion-content {
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px dashed rgba(148, 163, 184, 0.4);
}

.text-glow {
    text-shadow: 0 0 8px rgba(255,255,255,0.5);
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

.dropdown-menu {
    position: absolute !important;
    top: calc(100% + 0.75rem) !important;
    right: 0 !important;
    left: auto !important;
    bottom: auto !important;
    min-width: 300px;
    max-width: 350px;
    max-height: 75vh;
    overflow-y: auto;
    overflow-x: hidden;
    background: #ffffff !important;
    backdrop-filter: blur(30px);
    border: 1px solid rgba(14, 165, 233, 0.15);
    border-radius: 1.25rem;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2),
                0 15px 30px rgba(0, 0, 0, 0.15),
                0 5px 15px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.8);
    z-index: 99999 !important;
    transform-origin: top right;
    padding: 0.5rem 0;
    margin-top: 0;
}

.dropdown-menu::-webkit-scrollbar {
    width: 6px;
}

.dropdown-menu::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.05);
    border-radius: 10px;
}

.dropdown-menu::-webkit-scrollbar-thumb {
    background: rgba(14, 165, 233, 0.3);
    border-radius: 10px;
}

.dropdown-menu a {
    display: flex;
    align-items: center;
    padding: 0.875rem 1.5rem;
    color: #1f2937;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    border-bottom: 1px solid rgba(0, 0, 0, 0.04);
    text-decoration: none;
    position: relative;
    margin: 0 0.5rem;
    border-radius: 0.75rem;
}

.dropdown-menu a:last-child {
    border-bottom: none;
}

.dropdown-menu a:hover {
    background: linear-gradient(90deg, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0.08) 100%);
    color: #0284c7;
    transform: translateX(-4px);
    box-shadow: 0 2px 8px rgba(14, 165, 233, 0.15);
}

.dropdown-menu a.bg-sky-50 {
    background: linear-gradient(90deg, rgba(14, 165, 233, 0.15) 0%, rgba(14, 165, 233, 0.1) 100%) !important;
    color: #0284c7 !important;
    font-weight: 700;
    border-right: 3px solid #0284c7;
}

.dropdown-menu a i {
    margin-left: 0.875rem;
    color: #0284c7;
    width: 20px;
    font-size: 1rem;
    transition: transform 0.25s ease;
}

.dropdown-menu a:hover i {
    transform: scale(1.15);
}

[x-cloak] {
    display: none !important;
}

.no-scroll {
    overflow: hidden !important;
    height: 100vh !important;
    position: fixed !important;
    width: 100% !important;
}

/* Mobile Menu Styles */
@media (max-width: 1023px) {
    /* Ensure mobile menu appears above content but below navbar */
    .mobile-menu-container {
        z-index: 45 !important;
        height: calc(100vh - 6rem) !important;
        max-height: calc(100vh - 6rem) !important;
        overflow: hidden !important;
        display: flex !important;
        flex-direction: column !important;
    }
    
    /* Prevent body scroll when menu is open on mobile */
    body.overflow-hidden {
        overflow: hidden !important;
        position: fixed !important;
        width: 100% !important;
        height: 100% !important;
    }
    
    /* Ensure mobile menu inner container is scrollable */
    .mobile-menu-scrollable {
        flex: 1 !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        -webkit-overflow-scrolling: touch !important;
        overscroll-behavior: contain !important;
        position: relative !important;
        min-height: 0 !important;
    }
    
    /* Custom scrollbar for mobile menu */
    .mobile-menu-scrollable::-webkit-scrollbar {
        width: 6px;
    }
    
    .mobile-menu-scrollable::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 3px;
    }
    
    .mobile-menu-scrollable::-webkit-scrollbar-thumb {
        background: rgba(14, 165, 233, 0.3);
        border-radius: 3px;
    }
    
    .mobile-menu-scrollable::-webkit-scrollbar-thumb:hover {
        background: rgba(14, 165, 233, 0.5);
    }
    
    /* Force scrollbar to be visible on mobile */
    .mobile-menu-scrollable {
        scrollbar-width: thin;
        scrollbar-color: rgba(14, 165, 233, 0.3) rgba(0, 0, 0, 0.05);
    }
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

/* Particles Background */
.particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    pointer-events: none;
    z-index: 1;
}

.particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    animation: floatParticle 20s infinite linear;
    opacity: 0;
}

@keyframes floatParticle {
    0% {
        transform: translateY(100vh) translateX(0) scale(0);
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    90% {
        opacity: 1;
    }
    100% {
        transform: translateY(-100vh) translateX(100px) scale(1);
        opacity: 0;
    }
}

html {
    scroll-behavior: smooth;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    height: auto !important;
    position: relative !important;
}

/* إصلاح شامل للتمرير */
html, body {
    overflow-y: auto !important;
    overflow-x: hidden !important;
}

body {
    overflow-y: auto !important;
    overflow-x: hidden !important;
    position: relative !important;
    height: auto !important;
}

body:not(.overflow-hidden) {
    overflow: auto !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    position: relative !important;
}

section[id] {
    scroll-margin-top: 100px;
}

/* Navbar Scroll Effect */
.navbar-scrolled {
    background: linear-gradient(to bottom, rgba(240, 249, 255, 0.9), rgba(224, 242, 254, 0.85)) !important;
    backdrop-filter: blur(10px) !important;
    border-bottom: 1px solid rgba(59, 130, 246, 0.2) !important;
}

/* Enhanced Dark Theme */
body.dark-theme {
    background: #0f172a;
    color: #f1f5f9;
}

body.dark-theme .bg-white {
    background: #1e293b !important;
    color: #f1f5f9 !important;
}

body.dark-theme .bg-gray-50 {
    background: #0f172a !important;
}

body.dark-theme .text-gray-900 {
    color: #f1f5f9 !important;
}

body.dark-theme .text-gray-700 {
    color: #cbd5e1 !important;
}

body.dark-theme .text-gray-600 {
    color: #94a3b8 !important;
}
</style>

<script>
tailwind.config = {
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                'arabic': ['Cairo', 'system-ui', 'sans-serif'],
            }
        }
    }
}
</script>

