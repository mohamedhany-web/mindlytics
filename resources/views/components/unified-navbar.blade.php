<!-- Unified Navigation Bar Component -->
<nav id="navbar" 
     x-data="{ 
         mobileMenu: false,
         toggleMenu() {
             this.mobileMenu = !this.mobileMenu;
             if (this.mobileMenu && window.innerWidth < 1024) {
                 // فقط على الموبايل
                 document.body.style.overflow = 'hidden';
                 document.body.classList.add('overflow-hidden');
             } else {
                 // تفعيل التمرير
                 document.body.style.setProperty('overflow', 'auto', 'important');
                 document.body.style.setProperty('overflow-y', 'auto', 'important');
                 document.body.style.setProperty('position', 'relative', 'important');
                 document.body.classList.remove('overflow-hidden');
             }
         },
         closeMenu() {
             this.mobileMenu = false;
             // تفعيل التمرير بشكل كامل
             document.body.style.setProperty('overflow', 'auto', 'important');
             document.body.style.setProperty('overflow-y', 'auto', 'important');
             document.body.style.setProperty('position', 'relative', 'important');
             document.body.classList.remove('overflow-hidden');
         }
     }"
     class="navbar-gradient text-white shadow-lg relative overflow-hidden"
     style="margin: 0; padding: 0; top: 0;">
    <!-- Enhanced Animated Background Pattern -->
    <div class="absolute inset-0 opacity-[0.04] pointer-events-none" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.1) 10px, rgba(255,255,255,0.1) 20px); animation: patternShift 20s linear infinite;"></div>
    
    <!-- Floating Glow Effects -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-0 left-1/4 w-64 h-64 bg-[#2CA9BD]/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 0s;"></div>
        <div class="absolute top-0 right-1/4 w-48 h-48 bg-[#65DBE4]/8 rounded-full blur-2xl animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute -top-10 left-1/2 w-32 h-32 bg-[#7FBFE6]/6 rounded-full blur-xl animate-pulse" style="animation-delay: 2s;"></div>
    </div>
    
    <!-- Animated Gradient Overlay -->
    <div class="absolute inset-0 opacity-30 pointer-events-none" style="background: linear-gradient(135deg, rgba(44, 169, 189, 0.15) 0%, rgba(101, 219, 228, 0.1) 50%, rgba(127, 191, 230, 0.1) 100%); animation: gradientFlow 8s ease-in-out infinite;"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex justify-between items-center h-20 lg:h-24 gap-4 lg:gap-6">
            <!-- Enhanced Logo with Programming Theme -->
            <div class="flex items-center space-x-3 space-x-reverse flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 space-x-reverse group">
                    <div class="relative flex-shrink-0">
                        <!-- Logo Container with Letter M -->
                        <div class="w-12 h-12 lg:w-16 lg:h-16 rounded-xl flex items-center justify-center shadow-xl transition-all duration-500 group-hover:shadow-2xl group-hover:scale-110 relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-500 to-blue-700 border-2 border-blue-400/50">
                            <!-- Animated Shine Effect -->
                            <div class="absolute inset-0 bg-gradient-to-br from-white/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="animation: shine 3s ease-in-out infinite;"></div>
                            
                            <!-- Glow Effect -->
                            <div class="absolute -inset-1 bg-gradient-to-r from-blue-500/30 via-blue-400/20 to-blue-500/30 rounded-xl blur opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            
                            <!-- Letter M -->
                            <span class="text-2xl lg:text-3xl font-black text-white drop-shadow-lg relative z-10">M</span>
                            
                            <!-- Decorative Corner Accents -->
                            <div class="absolute top-0.5 right-0.5 w-1.5 h-1.5 bg-blue-400/60 rounded-full opacity-80 group-hover:opacity-100 group-hover:scale-150 transition-all duration-300 z-20"></div>
                            <div class="absolute bottom-0.5 left-0.5 w-1 h-1 bg-blue-300/50 rounded-full opacity-70 group-hover:opacity-100 group-hover:scale-150 transition-all duration-300 z-20"></div>
                        </div>
                        <!-- Enhanced Status Indicator -->
                        <div class="absolute -top-0.5 -right-0.5 lg:-top-1 lg:-right-1 w-3 h-3 lg:w-4 lg:h-4 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full shadow-lg ring-2 ring-white/60 animate-pulse group-hover:ring-blue-400/50 transition-all duration-300"></div>
                    </div>
                    <div class="flex flex-col">
                        <h1 class="text-lg lg:text-xl font-black text-white group-hover:text-blue-200 transition-all duration-300 leading-tight tracking-tight drop-shadow-lg">Mindlytics</h1>
                        <p class="text-xs lg:text-sm text-white/90 font-semibold leading-tight group-hover:text-white transition-colors duration-300">أكاديمية البرمجة</p>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center gap-6 flex-1 justify-center max-w-5xl mx-auto">
                <!-- Enhanced Navigation Links -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('public.learning-paths.index') }}" class="nav-link text-white/90 hover:text-white font-semibold transition-all duration-300 relative group px-4 py-2.5 whitespace-nowrap text-base rounded-xl hover:bg-white/15 backdrop-blur-sm border border-transparent hover:border-white/20">
                        <span class="relative z-10 flex items-center gap-2.5">
                            <i class="fas fa-route text-sm group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300"></i>
                            <span class="group-hover:translate-x-[-2px] transition-transform duration-300">المسارات التعليمية</span>
                        </span>
                        <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-1 bg-gradient-to-r from-transparent via-white/60 to-transparent rounded-full transition-all duration-500 group-hover:w-full group-hover:shadow-lg group-hover:shadow-white/30"></span>
                        <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-xl"></div>
                    </a>
                    <a href="{{ route('public.courses') }}" class="nav-link text-white/90 hover:text-white font-semibold transition-all duration-300 relative group px-4 py-2.5 whitespace-nowrap text-base rounded-xl hover:bg-white/15 backdrop-blur-sm border border-transparent hover:border-white/20">
                        <span class="relative z-10 flex items-center gap-2.5">
                            <i class="fas fa-book text-sm group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300"></i>
                            <span class="group-hover:translate-x-[-2px] transition-transform duration-300">الكورسات</span>
                        </span>
                        <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-1 bg-gradient-to-r from-transparent via-white/60 to-transparent rounded-full transition-all duration-500 group-hover:w-full group-hover:shadow-lg group-hover:shadow-white/30"></span>
                        <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-xl"></div>
                    </a>
                    <a href="{{ route('public.about') }}" class="nav-link text-white/90 hover:text-white font-semibold transition-all duration-300 relative group px-4 py-2.5 whitespace-nowrap text-base rounded-xl hover:bg-white/15 backdrop-blur-sm border border-transparent hover:border-white/20">
                        <span class="relative z-10 flex items-center gap-2.5">
                            <i class="fas fa-info-circle text-sm group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300"></i>
                            <span class="group-hover:translate-x-[-2px] transition-transform duration-300">من نحن</span>
                        </span>
                        <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-1 bg-gradient-to-r from-transparent via-white/60 to-transparent rounded-full transition-all duration-500 group-hover:w-full group-hover:shadow-lg group-hover:shadow-white/30"></span>
                        <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-xl"></div>
                    </a>
                    <a href="{{ route('public.portfolio.index') }}" class="nav-link text-white/90 hover:text-white font-semibold transition-all duration-300 relative group px-4 py-2.5 whitespace-nowrap text-base rounded-xl hover:bg-white/15 backdrop-blur-sm border border-transparent hover:border-white/20">
                        <span class="relative z-10 flex items-center gap-2.5">
                            <i class="fas fa-briefcase text-sm group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300"></i>
                            <span class="group-hover:translate-x-[-2px] transition-transform duration-300">البورتفوليو</span>
                        </span>
                        <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-1 bg-gradient-to-r from-transparent via-white/60 to-transparent rounded-full transition-all duration-500 group-hover:w-full group-hover:shadow-lg group-hover:shadow-white/30"></span>
                        <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-xl"></div>
                    </a>
                </div>
            </div>

            <!-- Auth Buttons -->
            <div class="hidden lg:flex items-center space-x-3 space-x-reverse flex-shrink-0">
                @auth
                    <a href="{{ url('/dashboard') }}" class="bg-white text-blue-900 px-6 py-2.5 rounded-xl font-bold hover:bg-blue-50 transition-all duration-500 shadow-lg hover:shadow-xl relative overflow-hidden group text-sm whitespace-nowrap border-2 border-white/30 hover:border-white/50">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-50/0 via-blue-100/30 to-blue-50/0 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <span class="relative z-10 flex items-center gap-2">
                            <i class="fas fa-tachometer-alt text-xs group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300"></i>
                            <span class="group-hover:translate-x-[-2px] transition-transform duration-300">لوحة التحكم</span>
                        </span>
                    </a>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="text-white/90 hover:text-white font-semibold transition-all duration-300 relative group px-5 py-2.5 rounded-xl hover:bg-white/15 backdrop-blur-sm text-sm whitespace-nowrap border-2 border-white/20 hover:border-white/40">
                        <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-xl"></div>
                        <span class="relative z-10 flex items-center gap-2">
                            <i class="fas fa-sign-in-alt text-xs group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300"></i>
                            <span class="group-hover:translate-x-[-2px] transition-transform duration-300">تسجيل دخول</span>
                        </span>
                    </a>
                    <a href="{{ route('register') }}" class="bg-white text-blue-900 px-6 py-2.5 rounded-xl font-bold hover:bg-blue-50 transition-all duration-500 shadow-lg hover:shadow-xl relative overflow-hidden group text-sm whitespace-nowrap border-2 border-white/30 hover:border-white/50">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-50/0 via-blue-100/30 to-blue-50/0 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <span class="relative z-10 flex items-center gap-2">
                            <i class="fas fa-user-plus text-xs group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300"></i>
                            <span class="group-hover:translate-x-[-2px] transition-transform duration-300">انشاء حساب</span>
                        </span>
                    </a>
                @endguest
            </div>

            <!-- Enhanced Mobile Menu Button -->
            <button type="button"
                    id="mobile-menu-toggle"
                    class="lg:hidden text-white text-xl sm:text-2xl transition-all duration-500 hover:bg-white/15 relative p-2.5 sm:p-3 rounded-xl flex-shrink-0 z-50 border-2 border-white/20 hover:border-white/40 backdrop-blur-sm shadow-lg hover:shadow-xl group"
                    aria-label="قائمة الهاتف"
                    aria-expanded="false">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-50/0 via-blue-100/30 to-blue-50/0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-xl"></div>
                <span id="menu-bars-icon" class="relative z-10 group-hover:scale-110 group-hover:rotate-90 transition-transform duration-500">
                    <i class="fas fa-bars"></i>
                </span>
                <span id="menu-times-icon" style="display: none;" class="relative z-10 group-hover:scale-110 group-hover:rotate-90 transition-transform duration-500">
                    <i class="fas fa-times"></i>
                </span>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu-overlay"
         class="lg:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-[9999] mobile-menu-overlay"
         style="display: none; touch-action: none; transition: opacity 0.15s cubic-bezier(0.4, 0, 0.2, 1); will-change: opacity; backface-visibility: hidden;">
    </div>

    <!-- Enhanced Mobile Menu Sidebar -->
    <div id="mobile-menu-sidebar"
         class="lg:hidden fixed top-0 right-0 h-full w-80 max-w-[85vw] shadow-2xl z-[10000] overflow-y-auto mobile-menu-sidebar"
         style="display: none; transform: translate3d(100%, 0, 0); transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1); touch-action: pan-y; -webkit-overflow-scrolling: touch; background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 50%, #1e3a8a 100%); will-change: transform; backface-visibility: hidden;">
        
        <!-- Animated Background Gradient -->
        <div class="absolute inset-0 opacity-30 pointer-events-none" style="background: linear-gradient(135deg, rgba(30, 64, 175, 0.3) 0%, rgba(30, 58, 138, 0.3) 25%, rgba(59, 130, 246, 0.2) 50%, rgba(37, 99, 235, 0.2) 75%, rgba(30, 58, 138, 0.2) 100%); animation: gradientShift 10s ease infinite; background-size: 200% 200%;"></div>
        
        <!-- Floating Orbs with multi-color -->
        <div class="absolute top-20 left-10 w-32 h-32 bg-blue-500/20 rounded-full blur-3xl pointer-events-none animate-float" style="animation: floatOrb 6s ease-in-out infinite;"></div>
        <div class="absolute bottom-32 right-8 w-40 h-40 bg-blue-400/15 rounded-full blur-3xl pointer-events-none animate-float" style="animation: floatOrb 8s ease-in-out infinite; animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/4 w-24 h-24 bg-blue-500/12 rounded-full blur-2xl pointer-events-none animate-float" style="animation: floatOrb 7s ease-in-out infinite; animation-delay: 1s;"></div>
        <div class="absolute top-3/4 right-1/4 w-28 h-28 bg-blue-400/15 rounded-full blur-2xl pointer-events-none animate-float" style="animation: floatOrb 9s ease-in-out infinite; animation-delay: 3s;"></div>
        
        <!-- Animated Grid Pattern -->
        <div class="absolute inset-0 opacity-[0.04] pointer-events-none" style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 30px 30px; animation: gridMove 20s linear infinite;"></div>
        
        <!-- Subtle Background Pattern - Matching navbar style -->
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.02) 10px, rgba(255,255,255,0.02) 20px);"></div>
        
        <!-- Radial gradient overlay like navbar -->
        <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(circle at 50% 0%, rgba(255, 255, 255, 0.1) 0%, transparent 70%);"></div>
        
        <!-- Animated Particles - More particles -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <!-- Small particles -->
            <div class="particle" style="position: absolute; width: 3px; height: 3px; background: rgba(59, 130, 246, 0.4); border-radius: 50%; animation: particleFloat 12s infinite; left: 10%;"></div>
            <div class="particle" style="position: absolute; width: 4px; height: 4px; background: rgba(16, 185, 129, 0.4); border-radius: 50%; animation: particleFloat 15s infinite; animation-delay: 2s; left: 20%;"></div>
            <div class="particle" style="position: absolute; width: 5px; height: 5px; background: rgba(139, 92, 246, 0.3); border-radius: 50%; animation: particleFloat 10s infinite; animation-delay: 4s; left: 30%;"></div>
            <div class="particle" style="position: absolute; width: 3px; height: 3px; background: rgba(59, 130, 246, 0.5); border-radius: 50%; animation: particleFloat 14s infinite; animation-delay: 1s; left: 40%;"></div>
            <div class="particle" style="position: absolute; width: 4px; height: 4px; background: rgba(16, 185, 129, 0.35); border-radius: 50%; animation: particleFloat 13s infinite; animation-delay: 3s; left: 50%;"></div>
            <div class="particle" style="position: absolute; width: 3px; height: 3px; background: rgba(139, 92, 246, 0.4); border-radius: 50%; animation: particleFloat 16s infinite; animation-delay: 2.5s; left: 60%;"></div>
            <div class="particle" style="position: absolute; width: 4px; height: 4px; background: rgba(59, 130, 246, 0.3); border-radius: 50%; animation: particleFloat 11s infinite; animation-delay: 5s; left: 70%;"></div>
            <div class="particle" style="position: absolute; width: 5px; height: 5px; background: rgba(16, 185, 129, 0.4); border-radius: 50%; animation: particleFloat 17s infinite; animation-delay: 1.5s; left: 80%;"></div>
            <div class="particle" style="position: absolute; width: 3px; height: 3px; background: rgba(139, 92, 246, 0.35); border-radius: 50%; animation: particleFloat 13s infinite; animation-delay: 3.5s; left: 90%;"></div>
            <div class="particle" style="position: absolute; width: 4px; height: 4px; background: rgba(59, 130, 246, 0.4); border-radius: 50%; animation: particleFloat 14s infinite; animation-delay: 4.5s; left: 15%;"></div>
        </div>
        
        <!-- Animated Geometric Shapes -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <!-- Floating triangles -->
            <div class="geometric-shape" style="position: absolute; width: 0; height: 0; border-left: 15px solid transparent; border-right: 15px solid transparent; border-bottom: 25px solid rgba(59, 130, 246, 0.15); top: 15%; left: 20%; animation: floatShape 8s ease-in-out infinite;"></div>
            <div class="geometric-shape" style="position: absolute; width: 0; height: 0; border-left: 12px solid transparent; border-right: 12px solid transparent; border-bottom: 20px solid rgba(16, 185, 129, 0.15); top: 60%; left: 70%; animation: floatShape 10s ease-in-out infinite; animation-delay: 2s;"></div>
            <div class="geometric-shape" style="position: absolute; width: 0; height: 0; border-left: 18px solid transparent; border-right: 18px solid transparent; border-bottom: 30px solid rgba(139, 92, 246, 0.12); top: 40%; left: 50%; animation: floatShape 9s ease-in-out infinite; animation-delay: 1s;"></div>
            
            <!-- Floating squares -->
            <div class="geometric-shape" style="position: absolute; width: 20px; height: 20px; background: rgba(59, 130, 246, 0.1); transform: rotate(45deg); top: 25%; left: 80%; animation: floatShape 7s ease-in-out infinite; animation-delay: 1.5s;"></div>
            <div class="geometric-shape" style="position: absolute; width: 15px; height: 15px; background: rgba(16, 185, 129, 0.12); transform: rotate(45deg); top: 70%; left: 30%; animation: floatShape 11s ease-in-out infinite; animation-delay: 3s;"></div>
            
            <!-- Floating circles -->
            <div class="geometric-shape" style="position: absolute; width: 25px; height: 25px; border: 2px solid rgba(139, 92, 246, 0.2); border-radius: 50%; top: 50%; left: 10%; animation: floatShape 9s ease-in-out infinite; animation-delay: 2.5s;"></div>
            <div class="geometric-shape" style="position: absolute; width: 18px; height: 18px; border: 2px solid rgba(59, 130, 246, 0.15); border-radius: 50%; top: 30%; left: 60%; animation: floatShape 8s ease-in-out infinite; animation-delay: 4s;"></div>
        </div>
        
        <!-- Animated Lines -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="animated-line" style="position: absolute; width: 2px; height: 100px; background: linear-gradient(180deg, transparent, rgba(59, 130, 246, 0.3), transparent); top: 10%; left: 25%; animation: lineMove 6s ease-in-out infinite;"></div>
            <div class="animated-line" style="position: absolute; width: 2px; height: 80px; background: linear-gradient(180deg, transparent, rgba(16, 185, 129, 0.3), transparent); top: 20%; left: 55%; animation: lineMove 8s ease-in-out infinite; animation-delay: 2s;"></div>
            <div class="animated-line" style="position: absolute; width: 2px; height: 120px; background: linear-gradient(180deg, transparent, rgba(139, 92, 246, 0.25), transparent); top: 5%; left: 75%; animation: lineMove 7s ease-in-out infinite; animation-delay: 1s;"></div>
            <div class="animated-line" style="position: absolute; width: 100px; height: 2px; background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.3), transparent); top: 40%; left: 15%; animation: lineMoveHorizontal 9s ease-in-out infinite; animation-delay: 3s;"></div>
            <div class="animated-line" style="position: absolute; width: 80px; height: 2px; background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.25), transparent); top: 65%; left: 45%; animation: lineMoveHorizontal 7s ease-in-out infinite; animation-delay: 1.5s;"></div>
        </div>
        
        <!-- Animated Waves -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <svg class="wave-animation" style="position: absolute; bottom: 0; left: 0; width: 100%; height: 200px; opacity: 0.1;" viewBox="0 0 1200 200" preserveAspectRatio="none">
                <path d="M0,100 Q300,50 600,100 T1200,100 L1200,200 L0,200 Z" fill="url(#waveGradient)" style="animation: waveMove 8s ease-in-out infinite;"></path>
                <defs>
                    <linearGradient id="waveGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" style="stop-color:rgba(59, 130, 246, 0.3);stop-opacity:1" />
                        <stop offset="50%" style="stop-color:rgba(16, 185, 129, 0.3);stop-opacity:1" />
                        <stop offset="100%" style="stop-color:rgba(139, 92, 246, 0.3);stop-opacity:1" />
                    </linearGradient>
                </defs>
            </svg>
        </div>
        
        <!-- Animated Stars -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="star" style="position: absolute; width: 3px; height: 3px; background: rgba(255, 255, 255, 0.6); clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%); top: 15%; left: 10%; animation: twinkle 3s ease-in-out infinite;"></div>
            <div class="star" style="position: absolute; width: 2px; height: 2px; background: rgba(255, 255, 255, 0.5); clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%); top: 35%; left: 40%; animation: twinkle 4s ease-in-out infinite; animation-delay: 1s;"></div>
            <div class="star" style="position: absolute; width: 4px; height: 4px; background: rgba(255, 255, 255, 0.7); clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%); top: 55%; left: 70%; animation: twinkle 3.5s ease-in-out infinite; animation-delay: 2s;"></div>
            <div class="star" style="position: absolute; width: 2.5px; height: 2.5px; background: rgba(255, 255, 255, 0.6); clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%); top: 75%; left: 25%; animation: twinkle 4.5s ease-in-out infinite; animation-delay: 0.5s;"></div>
            <div class="star" style="position: absolute; width: 3px; height: 3px; background: rgba(255, 255, 255, 0.5); clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%); top: 20%; left: 85%; animation: twinkle 3.8s ease-in-out infinite; animation-delay: 1.5s;"></div>
        </div>
        
        <!-- Mobile Menu Header -->
        <div class="relative flex items-center justify-between p-6 border-b border-white/20 backdrop-blur-xl sticky top-0 z-10 shadow-lg" style="background: linear-gradient(135deg, rgba(30, 64, 175, 0.98) 0%, rgba(30, 58, 138, 0.98) 50%, rgba(30, 58, 138, 0.98) 100%);">
            <!-- Glowing effect behind header -->
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 via-blue-400/15 to-blue-500/20 blur-xl opacity-60"></div>
            
            <div class="flex items-center space-x-3 space-x-reverse relative z-10">
                <div class="relative">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-2xl relative overflow-hidden group bg-gradient-to-br from-blue-600 via-blue-500 to-blue-700 border-2 border-blue-400/50">
                        <!-- Shine effect -->
                        <div class="absolute inset-0 bg-gradient-to-br from-white/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <!-- Glow effect with multi-color -->
                        <div class="absolute -inset-1 rounded-xl blur opacity-30 group-hover:opacity-50 transition-opacity duration-300" style="background: linear-gradient(135deg, #3b82f6, #2563eb, #1d4ed8);"></div>
                        <span class="text-2xl font-black text-white drop-shadow-lg relative z-10">M</span>
                    </div>
                </div>
                <div>
                    <h2 class="text-base font-black text-white drop-shadow-lg">Mindlytics</h2>
                    <p class="text-xs text-white/80 font-semibold">أكاديمية البرمجة</p>
                </div>
            </div>
            <button type="button" id="mobile-menu-close" class="w-10 h-10 flex items-center justify-center text-white/80 hover:text-white hover:bg-white/20 transition-all duration-300 rounded-xl border border-white/10 hover:border-white/30 relative z-10 shadow-md hover:shadow-lg">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <div class="relative px-5 py-6 space-y-1">
            <!-- Enhanced Mobile Links -->
            <div class="space-y-2">
                <a href="{{ route('public.learning-paths.index') }}" class="group relative flex items-center text-white/90 hover:text-white hover:bg-white/10 font-medium transition-all duration-300 px-4 py-4 rounded-2xl text-sm border border-white/10 hover:border-white/40 overflow-hidden backdrop-blur-md shadow-lg hover:shadow-2xl">
                    <!-- Animated background on hover -->
                    <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/15 to-white/0 opacity-0 group-hover:opacity-100 group-hover:animate-shimmer transition-opacity duration-300"></div>
                    <!-- Glow effect with multi-color -->
                    <div class="absolute -inset-0.5 rounded-2xl blur opacity-0 group-hover:opacity-100 transition-opacity duration-300" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.4), rgba(37, 99, 235, 0.3), rgba(59, 130, 246, 0.4));"></div>
                    
                    <div class="relative w-11 h-11 bg-white/20 group-hover:bg-white rounded-xl flex items-center justify-center ml-3 transition-all duration-300 group-hover:scale-110 group-hover:rotate-3 shadow-xl group-hover:shadow-2xl overflow-hidden">
                        <!-- Shine effect -->
                        <div class="absolute inset-0 bg-gradient-to-br from-white/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <!-- Glow effect with multi-color -->
                        <div class="absolute -inset-1 rounded-xl blur opacity-0 group-hover:opacity-50 transition-opacity duration-300" style="background: linear-gradient(135deg, #3b82f6, #2563eb);"></div>
                        <i class="fas fa-route text-white group-hover:text-blue-600 text-base transition-all duration-300 relative z-10 group-hover:scale-110"></i>
                    </div>
                    <span class="flex-1 relative z-10 font-bold text-base">المسارات التعليمية</span>
                    <i class="fas fa-chevron-left text-white/40 text-sm group-hover:text-white group-hover:translate-x-2 transition-all duration-300 relative z-10"></i>
                </a>
                
                <a href="{{ route('public.courses') }}" class="group relative flex items-center text-white/90 hover:text-white hover:bg-white/10 font-medium transition-all duration-300 px-4 py-4 rounded-2xl text-sm border border-white/10 hover:border-white/40 overflow-hidden backdrop-blur-md shadow-lg hover:shadow-2xl">
                    <!-- Animated background on hover -->
                    <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/15 to-white/0 opacity-0 group-hover:opacity-100 group-hover:animate-shimmer transition-opacity duration-300"></div>
                    <!-- Glow effect with multi-color -->
                    <div class="absolute -inset-0.5 rounded-2xl blur opacity-0 group-hover:opacity-100 transition-opacity duration-300" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.4), rgba(37, 99, 235, 0.3), rgba(59, 130, 246, 0.4));"></div>
                    
                    <div class="relative w-11 h-11 bg-white/20 group-hover:bg-white rounded-xl flex items-center justify-center ml-3 transition-all duration-300 group-hover:scale-110 group-hover:rotate-3 shadow-xl group-hover:shadow-2xl overflow-hidden">
                        <!-- Shine effect -->
                        <div class="absolute inset-0 bg-gradient-to-br from-white/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <!-- Glow effect with multi-color -->
                        <div class="absolute -inset-1 rounded-xl blur opacity-0 group-hover:opacity-50 transition-opacity duration-300" style="background: linear-gradient(135deg, #3b82f6, #2563eb);"></div>
                        <i class="fas fa-book text-white group-hover:text-blue-600 text-base transition-all duration-300 relative z-10 group-hover:scale-110"></i>
                    </div>
                    <span class="flex-1 relative z-10 font-bold text-base">الكورسات</span>
                    <i class="fas fa-chevron-left text-white/40 text-sm group-hover:text-white group-hover:translate-x-2 transition-all duration-300 relative z-10"></i>
                </a>
                
                <a href="{{ route('public.about') }}" class="group relative flex items-center text-white/90 hover:text-white hover:bg-white/10 font-medium transition-all duration-300 px-4 py-4 rounded-2xl text-sm border border-white/10 hover:border-white/40 overflow-hidden backdrop-blur-md shadow-lg hover:shadow-2xl">
                    <!-- Animated background on hover -->
                    <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/15 to-white/0 opacity-0 group-hover:opacity-100 group-hover:animate-shimmer transition-opacity duration-300"></div>
                    <!-- Glow effect with multi-color -->
                    <div class="absolute -inset-0.5 rounded-2xl blur opacity-0 group-hover:opacity-100 transition-opacity duration-300" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.4), rgba(37, 99, 235, 0.3), rgba(59, 130, 246, 0.4));"></div>
                    
                    <div class="relative w-11 h-11 bg-white/20 group-hover:bg-white rounded-xl flex items-center justify-center ml-3 transition-all duration-300 group-hover:scale-110 group-hover:rotate-3 shadow-xl group-hover:shadow-2xl overflow-hidden">
                        <!-- Shine effect -->
                        <div class="absolute inset-0 bg-gradient-to-br from-white/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <!-- Glow effect with multi-color -->
                        <div class="absolute -inset-1 rounded-xl blur opacity-0 group-hover:opacity-50 transition-opacity duration-300" style="background: linear-gradient(135deg, #3b82f6, #2563eb);"></div>
                        <i class="fas fa-info-circle text-white group-hover:text-blue-600 text-base transition-all duration-300 relative z-10 group-hover:scale-110"></i>
                    </div>
                    <span class="flex-1 relative z-10 font-bold text-base">من نحن</span>
                    <i class="fas fa-chevron-left text-white/40 text-sm group-hover:text-white group-hover:translate-x-2 transition-all duration-300 relative z-10"></i>
                </a>
                
                <a href="{{ route('public.portfolio.index') }}" class="group relative flex items-center text-white/90 hover:text-white hover:bg-white/10 font-medium transition-all duration-300 px-4 py-4 rounded-2xl text-sm border border-white/10 hover:border-white/40 overflow-hidden backdrop-blur-md shadow-lg hover:shadow-2xl">
                    <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/15 to-white/0 opacity-0 group-hover:opacity-100 group-hover:animate-shimmer transition-opacity duration-300"></div>
                    <div class="absolute -inset-0.5 rounded-2xl blur opacity-0 group-hover:opacity-100 transition-opacity duration-300" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.4), rgba(37, 99, 235, 0.3), rgba(59, 130, 246, 0.4));"></div>
                    <div class="relative w-11 h-11 bg-white/20 group-hover:bg-white rounded-xl flex items-center justify-center ml-3 transition-all duration-300 group-hover:scale-110 group-hover:rotate-3 shadow-xl group-hover:shadow-2xl overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-white/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute -inset-1 rounded-xl blur opacity-0 group-hover:opacity-50 transition-opacity duration-300" style="background: linear-gradient(135deg, #3b82f6, #2563eb);"></div>
                        <i class="fas fa-briefcase text-white group-hover:text-blue-600 text-base transition-all duration-300 relative z-10 group-hover:scale-110"></i>
                    </div>
                    <span class="flex-1 relative z-10 font-bold text-base">البورتفوليو</span>
                    <i class="fas fa-chevron-left text-white/40 text-sm group-hover:text-white group-hover:translate-x-2 transition-all duration-300 relative z-10"></i>
                </a>
            </div>
            
            <!-- Divider with accent -->
            <div class="my-5 relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full h-px bg-gradient-to-r from-transparent via-white/30 to-transparent"></div>
                </div>
                <div class="relative flex justify-center">
                    <div class="px-4 py-1 bg-white/10 backdrop-blur-md rounded-full border border-white/20">
                        <span class="text-white/60 text-xs font-semibold">القائمة</span>
                    </div>
                </div>
            </div>
            
            <!-- Auth Section -->
            <div class="space-y-2.5">
                @auth
                    <a href="{{ url('/dashboard') }}" class="group relative flex items-center justify-center gap-2 bg-white text-blue-900 px-6 py-3.5 rounded-xl font-semibold shadow-xl hover:shadow-2xl hover:bg-blue-50 transition-all duration-300 text-sm overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-50/0 via-blue-100/50 to-blue-50/0 opacity-0 group-hover:opacity-100 group-hover:animate-shimmer transition-opacity duration-300"></div>
                        <i class="fas fa-tachometer-alt text-sm relative z-10 group-hover:scale-110 transition-transform duration-300"></i>
                        <span class="relative z-10">لوحة التحكم</span>
                    </a>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="group relative flex items-center text-white/90 hover:text-white hover:bg-white/10 font-medium transition-all duration-300 px-4 py-3.5 rounded-xl text-sm border border-white/10 hover:border-white/30 overflow-hidden backdrop-blur-sm">
                        <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-0 group-hover:opacity-100 group-hover:animate-shimmer transition-opacity duration-300"></div>
                        <div class="w-10 h-10 bg-white/20 group-hover:bg-white rounded-xl flex items-center justify-center ml-3 transition-all duration-300 group-hover:scale-110 group-hover:rotate-3 shadow-lg group-hover:shadow-xl relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <i class="fas fa-sign-in-alt text-white group-hover:text-blue-600 text-sm transition-all duration-300 relative z-10"></i>
                        </div>
                        <span class="flex-1 relative z-10 font-semibold">تسجيل دخول</span>
                        <i class="fas fa-chevron-left text-white/40 text-xs group-hover:text-white group-hover:translate-x-1 transition-all duration-300 relative z-10"></i>
                    </a>
                    <a href="{{ route('register') }}" class="group relative flex items-center justify-center gap-2 bg-white text-blue-900 px-6 py-3.5 rounded-xl font-semibold shadow-xl hover:shadow-2xl hover:bg-blue-50 transition-all duration-300 text-sm overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-50/0 via-blue-100/50 to-blue-50/0 opacity-0 group-hover:opacity-100 group-hover:animate-shimmer transition-opacity duration-300"></div>
                        <i class="fas fa-user-plus text-sm relative z-10 group-hover:scale-110 transition-transform duration-300"></i>
                        <span class="relative z-10">انشاء حساب</span>
                    </a>
                @endguest
            </div>
            
            <!-- User Info (if logged in) -->
            @auth
            <div class="mt-6 pt-6 border-t border-white/20">
                <div class="group relative flex items-center gap-3 px-4 py-4 bg-white/10 backdrop-blur-md rounded-xl border border-white/20 hover:bg-white/15 hover:border-white/30 transition-all duration-300 overflow-hidden">
                    <!-- Animated background -->
                    <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 opacity-0 group-hover:opacity-100 group-hover:animate-shimmer transition-opacity duration-300"></div>
                    
                    <div class="relative w-14 h-14 bg-gradient-to-br from-white to-blue-50 rounded-full flex items-center justify-center text-blue-600 font-bold text-base shadow-xl border-2 border-white/30 group-hover:scale-110 transition-transform duration-300">
                        <!-- Glow effect with multi-color -->
                        <div class="absolute -inset-1 rounded-full blur opacity-30 group-hover:opacity-50 transition-opacity duration-300" style="background: linear-gradient(135deg, #3b82f6, #2563eb, #1d4ed8);"></div>
                        <span class="relative z-10">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0 relative z-10">
                        <p class="text-white font-bold text-sm truncate drop-shadow">{{ auth()->user()->name }}</p>
                        <p class="text-white/70 text-xs truncate font-medium">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="relative z-10">
                        <i class="fas fa-chevron-left text-white/40 text-xs group-hover:text-white/80 transition-colors duration-300"></i>
                    </div>
                </div>
            </div>
            @endauth
        </div>
    </div>

<style>
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

