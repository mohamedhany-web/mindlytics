<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة الإدارة - Mindlytics')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo-removebg-preview.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo-removebg-preview.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('logo-removebg-preview.png') }}">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js (نسخة ثابتة لضمان عمل الدروب داون) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    
    <!-- Custom Styles -->
    <style>
        * {
            font-family: 'IBM Plex Sans Arabic', sans-serif;
        }
        
        * {
            box-sizing: border-box;
        }
        
        html {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
            position: relative !important;
            -webkit-text-size-adjust: 100% !important;
            -ms-text-size-adjust: 100% !important;
        }
        
        body {
            margin: 0 !important;
            padding: 0 !important;
            margin-top: 0 !important;
            padding-top: 0 !important;
            min-height: 100vh !important;
            height: auto !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
            position: relative !important;
            top: 0 !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
        }
        
        @media (max-width: 1023px) {
            html {
                overflow: auto !important;
            }
            
            body {
                overflow: auto !important;
                height: auto !important;
                min-height: 100vh !important;
            }
        }
        
        /* Remove all spacing from body direct children */
        body > * {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        
        /* Main container - no spacing */
        body > div.flex.h-screen {
            margin: 0 !important;
            padding: 0 !important;
            margin-top: 0 !important;
            padding-top: 0 !important;
            top: 0 !important;
        }
        
        /* Sidebar container - full height using h-full like student sidebar */
        aside[class*="lg:fixed"] {
            position: fixed !important;
            top: 0 !important;
            bottom: 0 !important;
            right: 0 !important;
            left: auto !important;
            margin: 0 !important;
            padding: 0 !important;
            margin-top: 0 !important;
            padding-top: 0 !important;
            transform: translateY(0) !important;
            z-index: 20 !important;
            isolation: isolate !important;
        }
        
        aside[class*="lg:fixed"] > div {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            margin-top: 0 !important;
            padding-top: 0 !important;
            transform: translateY(0) !important;
            position: relative !important;
            isolation: isolate !important;
        }
        
        /* Ensure sidebar logo section starts from top */
        aside[class*="lg:fixed"] > div > div:first-child {
            margin: 0 !important;
            padding: 0 !important;
            margin-top: 0 !important;
            padding-top: 0 !important;
            position: relative !important;
            isolation: isolate !important;
        }
        
        /* Remove any spacing from logo section padding */
        aside[class*="lg:fixed"] > div > div:first-child.p-6 {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
            padding-right: 1.5rem !important;
            padding-left: 1.5rem !important;
            margin-top: 0 !important;
        }
        
        /* Ensure main content area is separate */
        body > div.flex.h-screen > div.flex.flex-col.flex-1 {
            position: relative !important;
            z-index: 10 !important;
            isolation: isolate !important;
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
            display: flex !important;
            flex-direction: column !important;
        }
        
        /* Ensure header is separate */
        header.sticky {
            position: sticky !important;
            z-index: 30 !important;
            isolation: isolate !important;
            flex-shrink: 0 !important;
        }
        
        /* Ensure main content is separate and scrollable - optimized for smooth scroll */
        main {
            position: relative !important;
            z-index: 1 !important;
            isolation: isolate !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            flex: 1 1 auto !important;
            min-height: 0 !important;
            max-height: 100% !important;
            -webkit-overflow-scrolling: touch !important;
            pointer-events: auto !important;
            touch-action: pan-y !important;
        }
        
        /* إصلاح التمرير - التأكد من أن main يستقبل wheel events */
        main,
        main * {
            pointer-events: auto !important;
        }
        
        /* منع أي عنصر من منع التمرير */
        main {
            overscroll-behavior: contain !important;
        }
        
        /* إصلاح إضافي - التأكد من أن main قابل للتمرير */
        main {
            height: auto !important;
            min-height: 100% !important;
        }
        
        /* التأكد من أن الحاوية الرئيسية لا تمنع التمرير */
        body > div.flex.h-screen > div.flex.flex-col.flex-1 > main {
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }
        
        /* Sidebar nav - allow scrolling */
        aside nav.sidebar {
            overflow-y: auto;
            overflow-x: hidden;
            flex: 1 1 auto;
            min-height: 0;
        }
        
        .sidebar {
            scrollbar-width: thin;
            scrollbar-color: rgba(59, 130, 246, 0.5) transparent;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #3b82f6, #2563eb);
            border-radius: 10px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #2563eb, #1d4ed8);
        }
        
        .nav-link {
            @apply flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-gray-700 hover:bg-gray-100 hover:text-gray-900;
        }
        
        .nav-link.active {
            @apply bg-blue-100 text-blue-700;
        }
        
        .btn-primary {
            @apply bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors;
        }
        
        .btn-secondary {
            @apply bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors;
        }
        
        .btn-success {
            @apply bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors;
        }
        
        .btn-danger {
            @apply bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors;
        }
        
        .btn-warning {
            @apply bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-colors;
        }
        
        /* Dashboard Cards Enhancement */
        .card-hover-effect {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover-effect:hover {
            transform: translateY(-4px);
        }
        
        /* Dashboard Background */
        main {
            background: #f8fafc;
        }
        
        /* Ensure content has enough bottom spacing */
        main > div:last-child {
            padding-bottom: 3rem !important;
        }
        
        /* Enhanced Card Styles - no backdrop-filter for scroll performance */
        .dashboard-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 255, 255, 0.95) 100%);
            border: 1px solid rgba(59, 130, 246, 0.2);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .dashboard-card:hover {
            border-color: rgba(59, 130, 246, 0.4);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Card Icon Enhancement */
        .card-icon {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);
            box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.4);
        }
        
        .card-icon:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 8px 20px 0 rgba(59, 130, 246, 0.5);
        }
        
        /* Section Headers */
        .section-header {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.05) 100%);
            border-bottom: 2px solid rgba(59, 130, 246, 0.2);
        }
        
        /* List Items Enhancement */
        .list-item-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(240, 249, 255, 0.8) 100%);
            border: 1px solid rgba(59, 130, 246, 0.15);
            transition: all 0.3s ease;
        }
        
        .list-item-card:hover {
            background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(224, 242, 254, 0.9) 100%);
            border-color: rgba(59, 130, 246, 0.3);
            transform: translateX(-4px);
        }
        
        /* Mobile optimization for cards */
        @media (max-width: 640px) {
            .dashboard-card {
                width: 100% !important;
                max-width: 100% !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                padding: 1rem !important;
            }
            
            main > div {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
            
            /* Ensure grid takes full width on mobile */
            .grid {
                width: 100% !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                gap: 1rem !important;
            }
            
            /* Larger text and icons on mobile */
            .dashboard-card .text-4xl {
                font-size: 2rem !important;
                line-height: 1.2 !important;
            }
            
            .dashboard-card .text-3xl {
                font-size: 1.75rem !important;
                line-height: 1.2 !important;
            }
            
            .dashboard-card .card-icon,
            .dashboard-card .w-16 {
                width: 3.5rem !important;
                height: 3.5rem !important;
            }
            
            .dashboard-card .text-xl {
                font-size: 1.25rem !important;
            }
            
            .dashboard-card .text-sm {
                font-size: 0.875rem !important;
            }
        }
        
        /* Mobile responsive fixes */
        @media (max-width: 1023px) {
            body > div.flex {
                flex-direction: column !important;
                min-height: 100vh !important;
                height: auto !important;
            }
            
            body > div.flex > div.flex.flex-col.flex-1 {
                width: 100% !important;
                padding-right: 0 !important;
            }
            
            /* Ensure header is full width on mobile */
            header.sticky {
                width: 100% !important;
            }
            
            /* Ensure main content is full width */
            main {
                width: 100% !important;
                overflow-x: hidden !important;
            }
            
            /* Fix dropdown on mobile */
            .relative.z-40 > div[x-show] {
                left: 0.5rem !important;
                right: auto !important;
                width: calc(100% - 1rem) !important;
                max-width: 20rem !important;
            }
        }
        
        /* Very small screens */
        @media (max-width: 375px) {
            header.sticky {
                height: 3.5rem !important;
            }
            
            header.sticky h1 {
                font-size: 0.875rem !important;
            }
            
            .dashboard-card {
                padding: 0.75rem !important;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50" style="margin: 0 !important; padding: 0 !important; margin-top: 0 !important; padding-top: 0 !important; top: 0 !important; position: relative !important;"
      x-data="{ 
          sidebarOpen: false
      }" 
      x-init="
          // إزالة الوضع المظلم من النظام - محسّن للأداء
          function removeDarkMode() {
              if (document.documentElement.classList.contains('dark')) {
                  document.documentElement.classList.remove('dark');
              }
          }
          removeDarkMode();
          
          // مراقبة وإزالة الوضع المظلم - محسّنة للأداء
          let darkModeObserver = null;
          if (typeof MutationObserver !== 'undefined') {
              darkModeObserver = new MutationObserver(function(mutations) {
                  for (let i = 0; i < mutations.length; i++) {
                      if (mutations[i].type === 'attributes' && mutations[i].attributeName === 'class') {
                          if (document.documentElement.classList.contains('dark')) {
                              removeDarkMode();
                          }
                          break;
                      }
                  }
              });
              darkModeObserver.observe(document.documentElement, {
                  attributes: true,
                  attributeFilter: ['class']
              });
          }
          
          // إغلاق السايدبار فوراً عند التهيئة على desktop فقط
          if (window.innerWidth >= 1024) {
              sidebarOpen = false;
          }
          
          // مراقبة تغيير القيمة لمنع فتح السايدبار على desktop فقط
          $watch('sidebarOpen', value => {
              if (window.innerWidth >= 1024 && value === true) {
                  sidebarOpen = false;
              }
          });
          
          // إغلاق السايدبار عند النقر على الروابط
          window.addEventListener('close-sidebar', () => {
              sidebarOpen = false;
          });
          
          // إغلاق السايدبار عند تغيير حجم النافذة إلى desktop
          let resizeTimeout;
          window.addEventListener('resize', () => {
              clearTimeout(resizeTimeout);
              resizeTimeout = setTimeout(() => {
                  if (window.innerWidth >= 1024) {
                      sidebarOpen = false;
                  }
              }, 150);
          });
      "
      @close-sidebar.window="sidebarOpen = false">
    <div class="flex min-h-screen lg:h-screen overflow-x-hidden" style="margin: 0 !important; padding: 0 !important; margin-top: 0 !important; padding-top: 0 !important; top: 0 !important; position: relative !important; isolation: isolate !important;">
        <!-- Sidebar - Fixed and isolated -->
        <aside class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:right-0 lg:z-20 flex-shrink-0 inset-y-0" style="position: fixed !important; z-index: 20 !important; isolation: isolate !important;">
            @include('layouts.admin-sidebar')
        </aside>

        <!-- Mobile sidebar -->
        <div x-show="sidebarOpen" 
             x-cloak
             @click.away="if (window.innerWidth < 1024) sidebarOpen = false"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 lg:hidden"
             style="display: none;"
             x-bind:style="sidebarOpen ? 'display: block !important;' : 'display: none !important;'">
            <div class="fixed inset-0 bg-black/60" @click="sidebarOpen = false" style="transition: opacity 0.15s cubic-bezier(0.4, 0, 0.2, 1);"></div>
            <div class="absolute inset-y-0 right-0 flex flex-col w-64 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 shadow-2xl transform transition-transform duration-150 ease-out border-l border-slate-700/50"
                 style="will-change: transform; backface-visibility: hidden; transform: translate3d(0, 0, 0);"
                 :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'">
                <div class="absolute top-4 left-4 z-50">
                    <button @click="sidebarOpen = false" class="flex items-center justify-center h-10 w-10 rounded-full bg-slate-700/50 hover:bg-slate-600/50 text-slate-200 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-400/50 shadow-lg">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <!-- Sidebar content for mobile -->
                @include('layouts.admin-sidebar')
            </div>
        </div>

        <!-- Main content area - Separate layer -->
        <div class="flex flex-col flex-1 min-w-0 lg:pr-64 w-full lg:h-screen" style="position: relative !important; z-index: 10 !important; isolation: isolate !important;">
            <!-- Top navigation - Sticky header inside main content -->
            <header class="sticky top-0 z-30 flex-shrink-0 flex h-14 sm:h-16 bg-gradient-to-r from-slate-50 via-blue-50 to-slate-100 shadow-lg border-b border-slate-200/50 bg-white/95 overflow-visible" style="position: sticky !important; z-index: 30 !important; isolation: isolate !important; overflow: visible !important;">
                <button @click="sidebarOpen = true" class="px-3 sm:px-4 border-l border-slate-200/50 text-slate-700 hover:bg-slate-100/50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-400 lg:hidden transition-colors">
                    <i class="fas fa-bars text-base sm:text-lg"></i>
                </button>
                
                <div class="flex-1 px-3 sm:px-6 flex justify-between items-center gap-2">
                    <div class="flex-1 flex items-center gap-2 sm:gap-4 min-w-0">
                        <h1 class="text-sm sm:text-lg font-black text-slate-800 drop-shadow-sm truncate">
                            @hasSection('header')
                                @yield('header')
                            @else
                                @yield('page_title', 'لوحة الإدارة')
                            @endif
                        </h1>
                    </div>
                    
                    <div class="flex items-center gap-2 sm:gap-4 flex-shrink-0">
                        <!-- User dropdown -->
                        <div class="relative z-40" x-data="{ open: false }" @click.outside="open = false">
                            <div>
                                <button @click.stop="open = !open" type="button" class="max-w-xs bg-white hover:bg-gray-50 flex items-center gap-1.5 sm:gap-2 px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg sm:rounded-xl border border-slate-200/50 shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400"
                                        :aria-expanded="open" aria-haspopup="true">
                                    @if(auth()->user()->profile_image)
                                        <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="{{ auth()->user()->name }}" class="w-7 h-7 sm:w-9 sm:h-9 rounded-full object-cover shadow-md flex-shrink-0 ring-2 ring-slate-200/50" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                                        <div class="w-7 h-7 sm:w-9 sm:h-9 bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 rounded-full hidden flex items-center justify-center text-white font-black text-xs sm:text-sm shadow-md flex-shrink-0">{{ substr(auth()->user()->name, 0, 1) }}</div>
                                    @else
                                        <div class="w-7 h-7 sm:w-9 sm:h-9 bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 rounded-full flex items-center justify-center text-white font-black text-xs sm:text-sm shadow-md flex-shrink-0">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <span class="hidden sm:block text-slate-700 text-xs sm:text-sm font-bold truncate max-w-[100px] sm:max-w-none">{{ auth()->user()->name }}</span>
                                    <i class="fas fa-chevron-down text-slate-600 text-xs transition-transform duration-200 flex-shrink-0" :class="open ? 'rotate-180' : ''"></i>
                                </button>
                            </div>
                            
                            <div x-show="open"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="origin-top-right absolute left-0 right-auto mt-2 w-56 rounded-2xl shadow-2xl bg-white border border-slate-200/50 ring-1 ring-black ring-opacity-5 overflow-hidden"
                                 style="z-index: 9999;">
                                <div class="py-2">
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                        <i class="fas fa-home w-4 text-slate-500"></i>
                                        لوحة التحكم
                                    </a>
                                    <a href="{{ route('admin.profile') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                        <i class="fas fa-user w-4 text-slate-500"></i>
                                        الملف الشخصي
                                    </a>
                                    <a href="{{ route('settings') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                        <i class="fas fa-cog w-4 text-slate-500"></i>
                                        الإعدادات
                                    </a>
                                    <div class="border-t border-slate-200 my-2"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-3 w-full text-right px-4 py-3 text-sm text-slate-700 hover:bg-red-50 hover:text-red-600 transition-colors">
                                            <i class="fas fa-sign-out-alt w-4 text-slate-500"></i>
                                            تسجيل الخروج
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page content - Scrollable area -->
            <main class="flex-1 overflow-y-auto overflow-x-hidden bg-gradient-to-br from-gray-50 via-white to-gray-50" style="position: relative !important; z-index: 1 !important; isolation: isolate !important; flex: 1 1 auto !important; min-height: 0 !important;">
                <!-- Flash Messages -->
                <div class="px-3 sm:px-6 pt-4 sm:pt-6 space-y-3">
                    @if(session('success'))
                        <div class="bg-gradient-to-r from-emerald-500 to-green-600 border-2 border-emerald-400 text-white px-6 py-4 rounded-2xl shadow-xl relative" role="alert">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-check-circle text-xl"></i>
                                    <span class="font-semibold">{{ session('success') }}</span>
                                </div>
                                <button onclick="this.parentElement.parentElement.style.display='none'" class="text-white/80 hover:text-white transition-colors">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-gradient-to-r from-rose-500 to-red-600 border-2 border-rose-400 text-white px-6 py-4 rounded-2xl shadow-xl relative" role="alert">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-exclamation-circle text-xl"></i>
                                    <span class="font-semibold">{{ session('error') }}</span>
                                </div>
                                <button onclick="this.parentElement.parentElement.style.display='none'" class="text-white/80 hover:text-white transition-colors">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="bg-gradient-to-r from-amber-500 to-yellow-600 border-2 border-amber-400 text-white px-6 py-4 rounded-2xl shadow-xl relative" role="alert">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-exclamation-triangle text-xl"></i>
                                    <span class="font-semibold">{{ session('warning') }}</span>
                                </div>
                                <button onclick="this.parentElement.parentElement.style.display='none'" class="text-white/80 hover:text-white transition-colors">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-3 sm:px-6 pb-8 sm:pb-12">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
    
    <script>
        // إزالة الوضع المظلم - محسّن للأداء (بدون intervals)
        (function() {
            function forceLightMode() {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                }
            }
            
            // إزالة فوراً
            forceLightMode();
            
            // إزالة عند تحميل الصفحة
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', forceLightMode);
            } else {
                forceLightMode();
            }
            
            // مراقبة التغييرات وإزالة كلاس dark - محسّنة للأداء
            if (typeof MutationObserver !== 'undefined') {
                const observer = new MutationObserver(function(mutations) {
                    for (let i = 0; i < mutations.length; i++) {
                        if (mutations[i].type === 'attributes' && mutations[i].attributeName === 'class') {
                            if (document.documentElement.classList.contains('dark')) {
                                forceLightMode();
                            }
                            break;
                        }
                    }
                });
                
                observer.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            }
            
            // إزالة عند تغيير الصفحة
            window.addEventListener('pageshow', forceLightMode);
        })();
        
        // رفع السايدبار للأعلى تلقائياً فقط عند تحميل الصفحة (مرة واحدة)
        (function() {
            let hasScrolled = false;
            let isUserScrolling = false;
            
            // تتبع التمرير من المستخدم
            const sidebarNav = document.querySelector('aside nav.sidebar');
            if (sidebarNav) {
                sidebarNav.addEventListener('scroll', function() {
                    isUserScrolling = true;
                    hasScrolled = true;
                });
                
                sidebarNav.addEventListener('wheel', function() {
                    isUserScrolling = true;
                });
                
                sidebarNav.addEventListener('touchstart', function() {
                    isUserScrolling = true;
                });
            }
            
            function scrollSidebarToTop() {
                // لا ترفع إذا كان المستخدم يقوم بالتمرير
                if (isUserScrolling || hasScrolled) {
                    return;
                }
                
                const sidebar = document.querySelector('aside[class*="lg:fixed"]');
                const nav = document.querySelector('aside nav.sidebar');
                
                if (sidebar && sidebar.scrollTop === 0) {
                    sidebar.scrollTop = 0;
                }
                
                if (nav && nav.scrollTop === 0) {
                    nav.scrollTop = 0;
                }
            }
            
            // رفع فقط عند تحميل الصفحة (مرة واحدة)
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(scrollSidebarToTop, 100);
                });
            } else {
                setTimeout(scrollSidebarToTop, 100);
            }
            
            // رفع فقط عند تحميل الصفحة بالكامل (مرة واحدة)
            window.addEventListener('load', function() {
                if (!hasScrolled) {
                    setTimeout(scrollSidebarToTop, 100);
                }
            });
        })();
    </script>
    
    <script>
        // إصلاح المسافة البيضاء - محسّن للأداء
        (function() {
            function fixSidebarPosition() {
                // إصلاح شامل للمسافة البيضاء
                const html = document.documentElement;
                const body = document.body;
                const mainContainer = document.querySelector('body > div.flex.h-screen');
                const sidebar = document.querySelector('aside[class*="lg:fixed"]');
                const sidebarContainer = document.querySelector('aside[class*="lg:fixed"] > div');
                const sidebarLogo = document.querySelector('aside[class*="lg:fixed"] > div > div:first-child');
                
                // إصلاح html و body
                if (html) {
                    html.style.marginTop = '0';
                    html.style.paddingTop = '0';
                    html.style.top = '0';
                }
                
                if (body) {
                    body.style.marginTop = '0';
                    body.style.paddingTop = '0';
                    body.style.top = '0';
                }
                
                // إصلاح الحاوية الرئيسية
                if (mainContainer) {
                    mainContainer.style.marginTop = '0';
                    mainContainer.style.paddingTop = '0';
                    mainContainer.style.top = '0';
                }
                
                // إصلاح موضع السايدبار
                if (sidebar) {
                    sidebar.style.top = '0';
                    sidebar.style.bottom = '0';
                    sidebar.style.marginTop = '0';
                    sidebar.style.paddingTop = '0';
                    sidebar.style.transform = 'translateY(0)';
                }
                
                // إصلاح موضع الحاوية الداخلية
                if (sidebarContainer) {
                    sidebarContainer.style.marginTop = '0';
                    sidebarContainer.style.paddingTop = '0';
                    sidebarContainer.style.top = '0';
                    sidebarContainer.style.transform = 'translateY(0)';
                }
                
                // إصلاح موضع اللوجو
                if (sidebarLogo) {
                    sidebarLogo.style.marginTop = '0';
                    sidebarLogo.style.paddingTop = '1.5rem';
                }
            }
            
            // إصلاح فوراً
            fixSidebarPosition();
            
            // إصلاح عند تحميل الصفحة - مرة واحدة فقط
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(fixSidebarPosition, 50);
                });
            } else {
                setTimeout(fixSidebarPosition, 50);
            }
            
            // إصلاح عند تحميل الصفحة بالكامل - مرة واحدة فقط
            window.addEventListener('load', function() {
                setTimeout(fixSidebarPosition, 100);
            }, { once: true });
            
            // إصلاح عند تغيير الصفحة
            window.addEventListener('pageshow', function() {
                setTimeout(fixSidebarPosition, 50);
            });
        })();
        
        // إغلاق السايدبار عند النقر على أي رابط في السايدبار على الموبايل - محسّن للأداء
        document.addEventListener('DOMContentLoaded', function() {
            let resizeTimeout;
            
            // إغلاق السايدبار عند النقر على أي رابط في السايدبار على الموبايل
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && window.innerWidth < 1024) {
                    const sidebar = link.closest('nav, [class*="sidebar"], aside');
                    if (sidebar) {
                        // إرسال event لإغلاق السايدبار
                        window.dispatchEvent(new CustomEvent('close-sidebar'));
                    }
                }
            }, true);
            
            // إغلاق السايدبار عند تغيير حجم النافذة إلى desktop - مع debounce
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    if (window.innerWidth >= 1024) {
                        window.dispatchEvent(new CustomEvent('close-sidebar'));
                    }
                }, 150);
            });
            
            // التأكد من أن main قابل للتمرير (بدون اعتراض wheel - التمرير الأصلي أسرع)
            function ensureMainScrollable() {
                const mainElement = document.querySelector('main');
                if (!mainElement) {
                    setTimeout(ensureMainScrollable, 100);
                    return;
                }
                mainElement.style.setProperty('overflow-y', 'auto', 'important');
                mainElement.style.setProperty('overflow-x', 'hidden', 'important');
                mainElement.style.setProperty('pointer-events', 'auto', 'important');
                mainElement.style.setProperty('touch-action', 'pan-y', 'important');
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() { ensureMainScrollable(); });
            } else {
                ensureMainScrollable();
            }
            window.addEventListener('load', function() { ensureMainScrollable(); });
        });
    </script>
    
    <style>
        [x-cloak] {
            display: none !important;
        }
        
        /* Mobile sidebar - allow it to work on mobile */
        @media (max-width: 1023px) {
            [x-show*="sidebarOpen"] {
                z-index: 50 !important;
                isolation: isolate !important;
            }
        }
        
        /* Prevent any layout interference */
        body > div.flex.h-screen {
            position: relative !important;
            isolation: isolate !important;
            contain: layout style paint !important;
        }
        
        /* Ensure sidebar is always on top layer */
        aside[class*="lg:fixed"] {
            contain: layout style paint !important;
        }
        
        /* Prevent content from overlapping sidebar */
        @media (min-width: 1024px) {
            .lg\:pr-64 {
                padding-right: 16rem !important;
            }
        }
        
        @media (max-width: 1023px) {
            .lg\:pr-64 {
                padding-right: 0 !important;
            }
        }
        
        /* Ensure all layers are properly isolated */
        * {
            box-sizing: border-box !important;
        }
        
        /* Force sidebar to stay in place */
        aside[class*="lg:fixed"] {
            transform: none !important;
        }
        
        /* Ensure main content doesn't interfere */
        body > div.flex.h-screen > div.flex.flex-col.flex-1 {
            contain: layout style paint !important;
        }
        
        /* Isolate header - لا نستخدم contain paint حتى لا يُقصّ الدروب داون */
        header.sticky {
            contain: layout style !important;
        }
        
        /* Main - no contain for smooth native scroll */
        main {
            contain: none !important;
        }
        
        /* Dropdown menu styles */
        .relative.z-40 {
            z-index: 40 !important;
        }
        
        .relative.z-40 > div[x-show] {
            z-index: 9999 !important;
            position: absolute !important;
        }
        
        /* Ensure dropdown is visible */
        [x-cloak] {
            display: none !important;
        }
        
        /* Dropdown animation */
        .origin-top-right {
            transform-origin: top right;
        }
    </style>
</body>
</html>

