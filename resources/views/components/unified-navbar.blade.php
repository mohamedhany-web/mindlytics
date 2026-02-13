<!-- Unified Navigation Bar - واجهة محدثة بألوان الأكاديمية وخط عربي -->
<nav id="navbar" 
     x-data="{ 
         mobileMenu: false,
         toggleMenu() {
             this.mobileMenu = !this.mobileMenu;
             if (this.mobileMenu && window.innerWidth < 1024) {
                 document.body.style.overflow = 'hidden';
                 document.body.classList.add('overflow-hidden');
             } else {
                 document.body.style.setProperty('overflow', 'auto', 'important');
                 document.body.style.setProperty('overflow-y', 'auto', 'important');
                 document.body.style.setProperty('position', 'relative', 'important');
                 document.body.classList.remove('overflow-hidden');
             }
         },
         closeMenu() {
             this.mobileMenu = false;
             document.body.style.setProperty('overflow', 'auto', 'important');
             document.body.style.setProperty('overflow-y', 'auto', 'important');
             document.body.style.setProperty('position', 'relative', 'important');
             document.body.classList.remove('overflow-hidden');
         }
     }"
     class="navbar-gradient text-white relative overflow-hidden nav-modern"
     style="margin: 0; padding: 0; top: 0;">
    <!-- خلفية زخرفية خفيفة بألوان الأكاديمية -->
    <div class="absolute inset-0 opacity-[0.05] pointer-events-none" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 12px, rgba(255,255,255,0.08) 12px, rgba(255,255,255,0.08) 24px);"></div>
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-0 right-0 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl"></div>
        <div class="absolute top-0 left-1/3 w-56 h-56 bg-emerald-500/8 rounded-full blur-2xl"></div>
    </div>
    <div class="absolute inset-0 opacity-80 pointer-events-none" style="background: linear-gradient(135deg, rgba(30, 64, 175, 0.95) 0%, rgba(30, 58, 138, 0.98) 50%, rgba(5, 150, 105, 0.08) 100%);"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex justify-between items-center h-16 lg:h-20 gap-4 lg:gap-6">
            <!-- الشعار والعلامة -->
            <div class="flex items-center gap-3 gap-reverse flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-3 gap-reverse group nav-brand">
                    <div class="relative flex-shrink-0">
                        <div class="w-11 h-11 lg:w-14 lg:h-14 rounded-2xl flex items-center justify-center shadow-lg transition-all duration-300 group-hover:shadow-xl group-hover:scale-[1.02] relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-500 to-emerald-600 border border-white/20">
                            <span class="text-xl lg:text-2xl font-black text-white drop-shadow relative z-10" style="font-family: 'Tajawal', 'Cairo', sans-serif;">M</span>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-base lg:text-lg font-extrabold text-white group-hover:text-white/95 transition-colors leading-tight" style="font-family: 'Tajawal', 'Cairo', sans-serif;">Mindlytics</span>
                        <span class="text-[11px] lg:text-xs text-white/80 font-medium leading-tight" style="font-family: 'Tajawal', 'Cairo', sans-serif;">أكاديمية البرمجة</span>
                    </div>
                </a>
            </div>

            <!-- روابط سطح المكتب -->
            <div class="hidden lg:flex items-center gap-1 flex-1 justify-center max-w-3xl mx-auto" style="font-family: 'Tajawal', 'Cairo', sans-serif;">
                <a href="{{ route('public.learning-paths.index') }}" class="nav-link-modern px-4 py-2.5 rounded-xl text-white/90 hover:text-white font-bold text-[15px] transition-all duration-200 hover:bg-white/10">
                    <i class="fas fa-route text-sm ml-2 opacity-90"></i>
                    المسارات التعليمية
                </a>
                <a href="{{ route('public.courses') }}" class="nav-link-modern px-4 py-2.5 rounded-xl text-white/90 hover:text-white font-bold text-[15px] transition-all duration-200 hover:bg-white/10">
                    <i class="fas fa-book text-sm ml-2 opacity-90"></i>
                    الكورسات
                </a>
                <a href="{{ route('public.about') }}" class="nav-link-modern px-4 py-2.5 rounded-xl text-white/90 hover:text-white font-bold text-[15px] transition-all duration-200 hover:bg-white/10">
                    <i class="fas fa-info-circle text-sm ml-2 opacity-90"></i>
                    من نحن
                </a>
                <a href="{{ route('public.portfolio.index') }}" class="nav-link-modern px-4 py-2.5 rounded-xl text-white/90 hover:text-white font-bold text-[15px] transition-all duration-200 hover:bg-white/10">
                    <i class="fas fa-briefcase text-sm ml-2 opacity-90"></i>
                    البورتفوليو
                </a>
                <a href="{{ route('public.instructors.index') }}" class="nav-link-modern px-4 py-2.5 rounded-xl text-white/90 hover:text-white font-bold text-[15px] transition-all duration-200 hover:bg-white/10">
                    <i class="fas fa-user-tie text-sm ml-2 opacity-90"></i>
                    المدربون
                </a>
            </div>

            <!-- أزرار الدخول والتسجيل -->
            <div class="hidden lg:flex items-center gap-2 flex-shrink-0" style="font-family: 'Tajawal', 'Cairo', sans-serif;">
                @auth
                    <a href="{{ url('/dashboard') }}" class="nav-cta-btn bg-white text-blue-800 px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-blue-50 hover:shadow-lg transition-all duration-200 shadow-md border border-white/30">
                        <i class="fas fa-tachometer-alt text-xs ml-2"></i>
                        لوحة التحكم
                    </a>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="px-4 py-2.5 rounded-xl text-white/95 hover:text-white font-bold text-sm border border-white/30 hover:bg-white/10 transition-all duration-200">
                        تسجيل دخول
                    </a>
                    <a href="{{ route('register') }}" class="nav-cta-btn bg-white text-blue-800 px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-blue-50 hover:shadow-lg transition-all duration-200 shadow-md border border-white/30">
                        <i class="fas fa-user-plus text-xs ml-2"></i>
                        انشاء حساب
                    </a>
                @endguest
            </div>

            <!-- زر القائمة للموبايل -->
            <button type="button"
                    id="mobile-menu-toggle"
                    class="lg:hidden text-white p-3 rounded-xl flex-shrink-0 z-50 border border-white/25 hover:bg-white/10 transition-all duration-200"
                    aria-label="قائمة الهاتف"
                    aria-expanded="false">
                <span id="menu-bars-icon" class="relative z-10"><i class="fas fa-bars text-lg"></i></span>
                <span id="menu-times-icon" style="display: none;" class="relative z-10"><i class="fas fa-times text-lg"></i></span>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu-overlay"
         class="lg:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-[9999] mobile-menu-overlay"
         style="display: none; touch-action: none; transition: opacity 0.15s cubic-bezier(0.4, 0, 0.2, 1); will-change: opacity; backface-visibility: hidden;">
    </div>

    <!-- Mobile Menu Sidebar - محسّن للهاتف -->
    <div id="mobile-menu-sidebar"
         class="mobile-sidebar lg:hidden fixed top-0 right-0 h-full w-[min(320px,88vw)] shadow-2xl z-[10000] overflow-y-auto"
         style="display: none; transform: translate3d(100%, 0, 0); transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1); touch-action: pan-y; -webkit-overflow-scrolling: touch; background: linear-gradient(180deg, #1e40af 0%, #1e3a8a 100%); will-change: transform; padding-right: env(safe-area-inset-right); padding-top: env(safe-area-inset-top);">
        
        <!-- خلفية بسيطة بدون حركة (أداء أفضل على الهاتف) -->
        <div class="absolute inset-0 pointer-events-none opacity-90" style="background: linear-gradient(180deg, #1e40af 0%, #1e3a8a 100%);"></div>
        <div class="absolute top-0 left-0 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-1/4 right-0 w-40 h-40 bg-blue-400/10 rounded-full blur-2xl pointer-events-none"></div>
        
        <!-- رأس السايدبار -->
        <div class="mobile-sidebar-header relative flex items-center justify-between gap-4 px-5 py-4 border-b border-white/15 sticky top-0 z-10 bg-[#1e3a8a]/95 backdrop-blur-md" style="padding-top: max(1rem, env(safe-area-inset-top)); font-family: 'Tajawal', 'Cairo', sans-serif;">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-700 border border-white/20 shadow-lg">
                    <span class="text-xl font-black text-white">M</span>
                </div>
                <div class="min-w-0">
                    <h2 class="text-lg font-extrabold text-white truncate">Mindlytics</h2>
                    <p class="text-sm text-white/80 font-medium truncate">أكاديمية البرمجة</p>
                </div>
            </div>
            <button type="button" id="mobile-menu-close" class="mobile-sidebar-close flex-shrink-0 w-12 h-12 min-h-[48px] min-w-[48px] flex items-center justify-center text-white/90 hover:text-white hover:bg-white/15 active:bg-white/20 rounded-xl transition-colors touch-manipulation" aria-label="إغلاق القائمة">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="relative px-4 py-5 pb-8 space-y-2 mobile-sidebar-content" style="padding-bottom: calc(1.5rem + env(safe-area-inset-bottom)); font-family: 'Tajawal', 'Cairo', sans-serif;">
            <!-- روابط القائمة - مساحة لمس مناسبة للهاتف -->
            <div class="space-y-2">
                <a href="{{ route('public.learning-paths.index') }}" class="mobile-sidebar-link flex items-center gap-4 text-white hover:bg-white/12 active:bg-white/18 rounded-2xl px-4 min-h-[52px] touch-manipulation transition-colors">
                    <span class="w-11 h-11 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0"><i class="fas fa-route text-white text-lg"></i></span>
                    <span class="flex-1 font-bold text-[17px]">المسارات التعليمية</span>
                    <i class="fas fa-chevron-left text-white/40 text-sm flex-shrink-0"></i>
                </a>
                <a href="{{ route('public.courses') }}" class="mobile-sidebar-link flex items-center gap-4 text-white hover:bg-white/12 active:bg-white/18 rounded-2xl px-4 min-h-[52px] touch-manipulation transition-colors">
                    <span class="w-11 h-11 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0"><i class="fas fa-book text-white text-lg"></i></span>
                    <span class="flex-1 font-bold text-[17px]">الكورسات</span>
                    <i class="fas fa-chevron-left text-white/40 text-sm flex-shrink-0"></i>
                </a>
                <a href="{{ route('public.about') }}" class="mobile-sidebar-link flex items-center gap-4 text-white hover:bg-white/12 active:bg-white/18 rounded-2xl px-4 min-h-[52px] touch-manipulation transition-colors">
                    <span class="w-11 h-11 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0"><i class="fas fa-info-circle text-white text-lg"></i></span>
                    <span class="flex-1 font-bold text-[17px]">من نحن</span>
                    <i class="fas fa-chevron-left text-white/40 text-sm flex-shrink-0"></i>
                </a>
                <a href="{{ route('public.portfolio.index') }}" class="mobile-sidebar-link flex items-center gap-4 text-white hover:bg-white/12 active:bg-white/18 rounded-2xl px-4 min-h-[52px] touch-manipulation transition-colors">
                    <span class="w-11 h-11 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0"><i class="fas fa-briefcase text-white text-lg"></i></span>
                    <span class="flex-1 font-bold text-[17px]">البورتفوليو</span>
                    <i class="fas fa-chevron-left text-white/40 text-sm flex-shrink-0"></i>
                </a>
                <a href="{{ route('public.instructors.index') }}" class="mobile-sidebar-link flex items-center gap-4 text-white hover:bg-white/12 active:bg-white/18 rounded-2xl px-4 min-h-[52px] touch-manipulation transition-colors">
                    <span class="w-11 h-11 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0"><i class="fas fa-user-tie text-white text-lg"></i></span>
                    <span class="flex-1 font-bold text-[17px]">المدربون</span>
                    <i class="fas fa-chevron-left text-white/40 text-sm flex-shrink-0"></i>
                </a>
            </div>
            
            <div class="my-4 h-px bg-white/15"></div>
            
            <!-- الدخول والتسجيل -->
            <div class="space-y-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="flex items-center justify-center gap-2 bg-white text-blue-900 px-5 py-4 rounded-2xl font-bold text-[17px] shadow-lg hover:bg-blue-50 active:bg-blue-100 min-h-[52px] touch-manipulation transition-colors">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>لوحة التحكم</span>
                    </a>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="mobile-sidebar-link flex items-center gap-4 text-white hover:bg-white/12 active:bg-white/18 rounded-2xl px-4 min-h-[52px] touch-manipulation transition-colors">
                        <span class="w-11 h-11 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0"><i class="fas fa-sign-in-alt text-white text-lg"></i></span>
                        <span class="flex-1 font-bold text-[17px]">تسجيل دخول</span>
                        <i class="fas fa-chevron-left text-white/40 text-sm flex-shrink-0"></i>
                    </a>
                    <a href="{{ route('register') }}" class="flex items-center justify-center gap-2 bg-white text-blue-900 px-5 py-4 rounded-2xl font-bold text-[17px] shadow-lg hover:bg-blue-50 active:bg-blue-100 min-h-[52px] touch-manipulation transition-colors">
                        <i class="fas fa-user-plus"></i>
                        <span>انشاء حساب</span>
                    </a>
                @endguest
            </div>
            
            @auth
            <div class="mt-6 pt-5 border-t border-white/15">
                <div class="flex items-center gap-3 px-4 py-3 bg-white/10 rounded-2xl">
                    <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-blue-600 font-bold text-lg flex-shrink-0">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white font-bold text-[15px] truncate">{{ auth()->user()->name }}</p>
                        <p class="text-white/70 text-sm truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>
            @endauth
        </div>
    </div>

<style>
/* سايدبار الموبايل - مظهر محسّن للهاتف */
.mobile-sidebar {
    -webkit-overflow-scrolling: touch;
    overscroll-behavior: contain;
}
.mobile-sidebar-content {
    font-family: 'Tajawal', 'Cairo', sans-serif;
}
.mobile-sidebar-link {
    -webkit-tap-highlight-color: transparent;
}
.mobile-sidebar-close:active {
    transform: scale(0.96);
}
@media (max-width: 380px) {
    .mobile-sidebar {
        width: min(300px, 92vw) !important;
    }
    .mobile-sidebar-header h2 {
        font-size: 1.1rem;
    }
    .mobile-sidebar-content .font-bold {
        font-size: 1rem;
    }
}

/* Mobile Menu Animations */
@keyframes floatOrb {
    0%, 100% {
        transform: translate(0, 0) scale(1);
        opacity: 0.3;
    }
    50% {
        transform: translate(30px, -30px) scale(1.2);
        opacity: 0.5;
    }
}

@keyframes gradientShift {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

@keyframes gridMove {
    0% {
        transform: translate(0, 0);
    }
    100% {
        transform: translate(30px, 30px);
    }
}

@keyframes particleFloat {
    0% {
        transform: translateY(100vh) translateX(0) rotate(0deg) scale(0);
        opacity: 0;
    }
    10% {
        opacity: 1;
        transform: translateY(90vh) translateX(10px) rotate(36deg) scale(1);
    }
    50% {
        transform: translateY(50vh) translateX(50px) rotate(180deg) scale(1.2);
    }
    90% {
        opacity: 1;
        transform: translateY(10vh) translateX(90px) rotate(324deg) scale(1);
    }
    100% {
        transform: translateY(-10vh) translateX(100px) rotate(360deg) scale(0);
        opacity: 0;
    }
}

@keyframes floatShape {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg) scale(1);
        opacity: 0.3;
    }
    25% {
        transform: translate(20px, -20px) rotate(90deg) scale(1.1);
        opacity: 0.5;
    }
    50% {
        transform: translate(-15px, -30px) rotate(180deg) scale(0.9);
        opacity: 0.4;
    }
    75% {
        transform: translate(25px, -10px) rotate(270deg) scale(1.05);
        opacity: 0.6;
    }
}

@keyframes lineMove {
    0%, 100% {
        transform: translateY(0) translateX(0);
        opacity: 0.3;
    }
    50% {
        transform: translateY(-30px) translateX(10px);
        opacity: 0.6;
    }
}

@keyframes lineMoveHorizontal {
    0%, 100% {
        transform: translateX(0) translateY(0);
        opacity: 0.3;
    }
    50% {
        transform: translateX(20px) translateY(-10px);
        opacity: 0.6;
    }
}

@keyframes waveMove {
    0%, 100% {
        d: path("M0,100 Q300,50 600,100 T1200,100 L1200,200 L0,200 Z");
    }
    50% {
        d: path("M0,100 Q300,150 600,100 T1200,100 L1200,200 L0,200 Z");
    }
}

@keyframes twinkle {
    0%, 100% {
        opacity: 0.3;
        transform: scale(1) rotate(0deg);
    }
    50% {
        opacity: 1;
        transform: scale(1.5) rotate(180deg);
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

@keyframes patternShift {
    0% {
        background-position: 0 0;
    }
    100% {
        background-position: 20px 20px;
    }
}

@keyframes gradientFlow {
    0%, 100% {
        opacity: 0.2;
        transform: scale(1);
    }
    50% {
        opacity: 0.4;
        transform: scale(1.05);
    }
}

@keyframes shine {
    0% {
        transform: translateX(-100%) translateY(-100%) rotate(45deg);
    }
    100% {
        transform: translateX(200%) translateY(200%) rotate(45deg);
    }
}

@keyframes pulseGlow {
    0%, 100% {
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
    }
    50% {
        box-shadow: 0 0 40px rgba(59, 130, 246, 0.6);
    }
}

@keyframes floatUp {
    0% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
    100% {
        transform: translateY(0);
    }
}

.animate-shimmer {
    animation: shimmer 3s infinite;
}

.animate-pulse-glow {
    animation: pulseGlow 3s ease-in-out infinite;
}

.animate-float-up {
    animation: floatUp 4s ease-in-out infinite;
}

/* Particle positioning */
.particle:nth-child(1) {
    left: 10%;
    animation-duration: 12s;
}

.particle:nth-child(2) {
    left: 30%;
    animation-duration: 15s;
    animation-delay: 2s;
}

.particle:nth-child(3) {
    left: 50%;
    animation-duration: 10s;
    animation-delay: 4s;
}

.particle:nth-child(4) {
    left: 70%;
    animation-duration: 14s;
    animation-delay: 1s;
}

.particle:nth-child(5) {
    left: 90%;
    animation-duration: 13s;
    animation-delay: 3s;
}
</style>

<script>
(function() {
    'use strict';
    
    // Mobile Menu Toggle Functionality
    function initMobileMenu() {
        const menuToggle = document.getElementById('mobile-menu-toggle');
        const menuSidebar = document.getElementById('mobile-menu-sidebar');
        const menuOverlay = document.getElementById('mobile-menu-overlay');
        const menuClose = document.getElementById('mobile-menu-close');
        const menuBarsIcon = document.getElementById('menu-bars-icon');
        const menuTimesIcon = document.getElementById('menu-times-icon');
        
        if (!menuToggle || !menuSidebar || !menuOverlay) {
            console.error('Mobile menu elements not found');
            return;
        }
        
        let isOpen = false;
        
        function openMenu() {
            isOpen = true;
            menuSidebar.style.display = 'block';
            menuOverlay.style.display = 'block';
            // فقط على الموبايل
            if (window.innerWidth < 1024) {
                document.body.style.overflow = 'hidden';
                document.body.classList.add('overflow-hidden');
            }
            
            // Trigger animation immediately using requestAnimationFrame for better performance
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    menuSidebar.style.transform = 'translate3d(0, 0, 0)';
                menuOverlay.style.opacity = '1';
                });
            });
            
            // Update icons immediately
            if (menuBarsIcon) menuBarsIcon.style.display = 'none';
            if (menuTimesIcon) menuTimesIcon.style.display = 'block';
            if (menuToggle) menuToggle.setAttribute('aria-expanded', 'true');
        }
        
        function closeMenu() {
            isOpen = false;
            menuSidebar.style.transform = 'translate3d(100%, 0, 0)';
            menuOverlay.style.opacity = '0';
            
            // إعادة تفعيل التمرير بشكل كامل
            document.body.style.overflow = '';
            document.body.style.overflowY = 'auto';
            document.body.style.overflowX = 'hidden';
            document.body.classList.remove('overflow-hidden');
            document.body.style.position = '';
            document.body.style.width = '';
            document.body.style.height = '';
            
            // Hide after animation (reduced from 300ms to 150ms)
            setTimeout(() => {
                menuSidebar.style.display = 'none';
                menuOverlay.style.display = 'none';
                
                // التأكد مرة أخرى من تفعيل التمرير
                document.body.style.overflow = '';
                document.body.style.overflowY = 'auto';
                document.body.style.overflowX = 'hidden';
                document.body.classList.remove('overflow-hidden');
            }, 150);
            
            // Update icons immediately
            if (menuBarsIcon) menuBarsIcon.style.display = 'block';
            if (menuTimesIcon) menuTimesIcon.style.display = 'none';
            if (menuToggle) menuToggle.setAttribute('aria-expanded', 'false');
        }
        
        function toggleMenu() {
            if (isOpen) {
                closeMenu();
            } else {
                openMenu();
            }
        }
        
        // Event Listeners
        if (menuToggle) {
            menuToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleMenu();
            });
        }
        
        if (menuClose) {
            menuClose.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeMenu();
            });
        }
        
        if (menuOverlay) {
            menuOverlay.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeMenu();
            });
        }
        
        // Close menu when clicking on links
        const menuLinks = menuSidebar.querySelectorAll('a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                closeMenu();
            });
        });
        
        // Close menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isOpen) {
                closeMenu();
            }
        });
        
        // Close menu on window resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024 && isOpen) {
                closeMenu();
            }
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMobileMenu);
    } else {
        initMobileMenu();
    }
    
    // Also try after a short delay to ensure elements are rendered
    setTimeout(initMobileMenu, 100);
    setTimeout(initMobileMenu, 500);
    
    // التأكد من تفعيل التمرير عند تحميل الصفحة
    function ensureScrollingEnabled() {
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
    ensureScrollingEnabled();
    
    // تفعيل التمرير عند تحميل الصفحة
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            ensureScrollingEnabled();
            setTimeout(ensureScrollingEnabled, 100);
            setTimeout(ensureScrollingEnabled, 500);
        });
    } else {
        ensureScrollingEnabled();
        setTimeout(ensureScrollingEnabled, 100);
        setTimeout(ensureScrollingEnabled, 500);
    }
    
    window.addEventListener('load', function() {
        ensureScrollingEnabled();
        setTimeout(ensureScrollingEnabled, 100);
    });
    
    // مراقبة مستمرة لضمان تفعيل التمرير
    setInterval(function() {
        const mobileMenu = document.getElementById('mobile-menu-sidebar');
        const isMenuOpen = mobileMenu && (mobileMenu.style.display === 'block' || window.getComputedStyle(mobileMenu).display === 'block');
        if (!isMenuOpen) {
            const computedStyle = window.getComputedStyle(document.body);
            if (computedStyle.overflow === 'hidden' || computedStyle.position === 'fixed') {
                ensureScrollingEnabled();
            }
        }
    }, 2000);
})();
</script>

