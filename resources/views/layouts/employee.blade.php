@php
    $empLocale = app()->getLocale();
    $empRtl = $empLocale === 'ar';
    $waImmersive = (bool) ($waImmersiveInbox ?? false);
@endphp
<!DOCTYPE html>
<html lang="{{ $empLocale }}" dir="{{ $empRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('auth.dashboard')) - {{ config('app.name') }}</title>
    
    @include('components.favicon-meta')
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <x-frontend-stack />
    
    <!-- Custom Styles -->
    <style>
        * {
            font-family: 'Tajawal', 'IBM Plex Sans Arabic', sans-serif;
        }
        /* إخفاء شريط التمرير في سايدبار الموظف مع بقاء التمرير يعمل */
        .employee-sidebar-nav {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .employee-sidebar-nav::-webkit-scrollbar {
            display: none;
        }
        .emp-sidebar-compact .emp-sidebar-label,
        .emp-sidebar-compact .emp-sidebar-logo-text,
        .emp-sidebar-compact .emp-sidebar-user-meta,
        .emp-sidebar-compact .emp-sidebar-logout-text,
        .emp-sidebar-compact .employee-sidebar-nav a > span,
        .emp-sidebar-compact .employee-sidebar-nav p {
            display: none !important;
        }
        .emp-sidebar-compact .emp-sidebar-logo-wrap {
            padding: 0.75rem;
            justify-content: center;
        }
        .emp-sidebar-compact .emp-sidebar-logo-wrap .w-16 {
            width: 2.5rem;
            height: 2.5rem;
        }
        .emp-sidebar-compact .emp-sidebar-link {
            justify-content: center;
            padding-left: 0.625rem;
            padding-right: 0.625rem;
            gap: 0;
        }
        .emp-sidebar-compact .employee-sidebar-nav a {
            justify-content: center;
            padding-left: 0.625rem;
            padding-right: 0.625rem;
            gap: 0;
        }
        .emp-sidebar-compact .employee-sidebar-nav {
            padding-left: 0.375rem;
            padding-right: 0.375rem;
        }
        .emp-sidebar-compact .emp-sidebar-user-row {
            justify-content: center;
        }
        .emp-sidebar-compact .emp-sidebar-footer {
            padding: 0.5rem;
        }
        body.employee-wa-immersive {
            overflow: hidden;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 {{ $waImmersive ? 'wa-immersive-inbox employee-wa-immersive' : '' }}">
    <div x-data="{ sidebarOpen: window.innerWidth >= 1024 }" 
         x-init="
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
      @close-sidebar.window="sidebarOpen = false"
      @open-sidebar.window="sidebarOpen = true">
    <div class="flex min-h-screen lg:h-screen overflow-x-hidden">
        <!-- Sidebar - Fixed -->
        <aside class="hidden lg:flex {{ $waImmersive ? 'lg:w-[4.25rem] emp-sidebar-compact' : 'lg:w-64' }} lg:flex-col lg:fixed lg:right-0 lg:z-20 flex-shrink-0 inset-y-0">
            @include('layouts.employee-sidebar')
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
             style="display: none;">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="sidebarOpen = false"></div>
            <div class="absolute inset-y-0 right-0 flex flex-col w-64 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 shadow-2xl transform transition-transform duration-150 ease-out border-l border-slate-700/50"
                 :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'">
                <div class="absolute top-4 left-4 z-50">
                    <button @click="sidebarOpen = false" class="flex items-center justify-center h-10 w-10 rounded-full bg-slate-700/50 hover:bg-slate-600/50 text-slate-200 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                @include('layouts.employee-sidebar')
            </div>
        </div>

        <!-- Main content area -->
        <div class="flex flex-col flex-1 min-w-0 {{ $waImmersive ? 'lg:pr-[4.25rem]' : 'lg:pr-64' }} w-full lg:h-screen">
            <!-- Top navigation -->
            <header class="sticky top-0 z-30 flex-shrink-0 flex h-14 sm:h-16 bg-gradient-to-r from-slate-50 via-blue-50 to-slate-100 shadow-lg border-b border-slate-200/50 backdrop-blur-sm {{ $waImmersive ? 'lg:hidden' : '' }}">
                <button @click="sidebarOpen = true" class="px-3 sm:px-4 border-l border-slate-200/50 text-slate-700 hover:bg-slate-100/50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-400 lg:hidden transition-colors">
                    <i class="fas fa-bars text-base sm:text-lg"></i>
                </button>
                
                <div class="flex-1 px-3 sm:px-6 flex justify-between items-center gap-2">
                    <div class="flex-1 flex items-center gap-2 sm:gap-4 min-w-0">
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 truncate">
                            @yield('header', 'لوحة الموظف')
                        </h1>
                    </div>
                    
                    <div class="flex items-center gap-2 sm:gap-4">
                        @include('components.employee-attendance-bar')
                        @php
                            try {
                                $navUnreadNotifications = (int) auth()->user()->notifications()->whereNull('read_at')->count();
                            } catch (\Throwable $e) {
                                $navUnreadNotifications = 0;
                            }
                        @endphp
                        <a href="{{ route('employee.notifications') }}"
                           class="relative p-2 text-gray-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors"
                           title="الإشعارات">
                            <i class="fas fa-bell text-lg"></i>
                            @if($navUnreadNotifications > 0)
                                <span class="absolute -top-0.5 -left-0.5 min-w-[1.15rem] h-[1.15rem] px-1 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center leading-none">
                                    {{ $navUnreadNotifications > 99 ? '99+' : $navUnreadNotifications }}
                                </span>
                            @endif
                        </a>
                        
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                @php
                                    $user = auth()->user();
                                    $profileImage = $user->profile_image_url;
                                @endphp
                                @if($profileImage)
                                    <img src="{{ $profileImage }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover border-2 border-blue-200">
                                @else
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                        {{ mb_substr($user->name, 0, 1, 'UTF-8') }}
                                    </div>
                                @endif
                                <span class="hidden sm:block">{{ $user->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                <a href="{{ route('employee.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-home mr-2"></i>لوحة التحكم
                                </a>
                                <a href="{{ route('employee.notifications') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-bell mr-2"></i>الإشعارات
                                    @if($navUnreadNotifications > 0)
                                        <span class="inline-flex mr-1 px-1.5 py-0.5 rounded-full bg-rose-100 text-rose-700 text-[10px] font-bold">{{ $navUnreadNotifications > 99 ? '99+' : $navUnreadNotifications }}</span>
                                    @endif
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i>تسجيل الخروج
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto {{ $waImmersive ? 'overflow-hidden bg-slate-50' : 'bg-[#f8fafc]' }}">
                <div class="{{ $waImmersive ? 'p-0 h-full flex flex-col min-h-0 overflow-hidden' : 'p-3 sm:p-4 md:p-6' }}">
                    @if(! $waImmersive)
                    @if(session('success'))
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    </div>

    @stack('scripts')
    @include('components.employee-presence-heartbeat')
    @include('components.sales-team-chat-widget')
</body>
</html>
