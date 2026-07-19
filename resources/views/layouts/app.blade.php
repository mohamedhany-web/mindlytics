@php
    $appLocale = app()->getLocale();
    $appRtl = $appLocale === 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ $appLocale }}" dir="{{ $appRtl ? 'rtl' : 'ltr' }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mindlytics') }} - @yield('title', __('auth.dashboard'))</title>

    @include('components.favicon-meta')

    <!-- خط عربي أصيل -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&family=Noto+Sans+Arabic:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    {{-- Tailwind + Alpine محلي (بدون CDN) --}}
    <x-frontend-stack />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @php
        $showContentProtection = !empty(trim((string) ($__env->yieldContent('enable-content-protection') ?? '')));
    @endphp
    @if($showContentProtection)
    <script>
        window.Laravel = {
            user: {
                name: '{{ auth()->check() ? auth()->user()->name : "زائر" }}'
            }
        };
    </script>
    <script src="{{ asset('js/platform-protection.js') }}"></script>
    @endif

    <style>
        [x-cloak] { display: none !important; }

        * {
            font-family: 'Cairo', 'Noto Sans Arabic', sans-serif;
        }

        body {
            background: #f8fafc;
            overflow-x: hidden;
        }

        /* Clean Sidebar shell — inner content uses .los-sidebar from mindlytics-los.css */
        .student-sidebar {
            background: #ffffff;
            border-left: 1px solid #e2e8f0;
            width: 280px;
            max-width: 280px;
            box-shadow: -1px 0 6px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            align-self: stretch;
            height: 100%;
            min-height: 100vh;
            min-height: 100dvh;
            overflow: hidden;
        }
        .student-sidebar.los-sidebar-bridge {
            width: var(--ml-sidebar-w, 280px);
            max-width: var(--ml-sidebar-w, 280px);
        }

        .nav-card {
            background: transparent;
            border: none;
            border-radius: 12px;
            padding: 12px 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .nav-card::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, #2CA9BD, #65DBE4);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .nav-card:hover {
            background: linear-gradient(to left, rgba(44, 169, 189, 0.08), rgba(101, 219, 228, 0.05));
            transform: translateX(-2px);
        }

        .nav-card.active {
            background: linear-gradient(to left, rgba(44, 169, 189, 0.15), rgba(101, 219, 228, 0.08));
            box-shadow: 0 2px 8px rgba(44, 169, 189, 0.15);
        }

        .nav-card.active::before {
            opacity: 1;
        }

        .nav-card.active .nav-icon {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(44, 169, 189, 0.3);
        }

        .nav-card.active .font-black {
            color: #1C2C39;
        }

        .nav-card.active .text-xs {
            color: #1F3A56;
        }

        .nav-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
            line-height: 1;
            text-align: center;
        }
        
        .nav-icon i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            margin: 0;
            padding: 0;
        }

        .nav-card:hover .nav-icon {
            transform: scale(1.08) rotate(2deg);
        }

        /* النافبار - نفس أسلوب الكارد */
        .student-header {
            background: white;
            border-bottom: 1px solid rgb(226 232 240);
            min-height: 64px;
            box-shadow: 0 1px 2px rgb(0 0 0 / 0.04);
        }
        @media (max-width: 640px) {
            .student-header { min-height: 56px; padding-top: 0.5rem; padding-bottom: 0.5rem; }
        }
        .search-command {
            background: rgb(248 250 252);
            border: 1px solid rgb(226 232 240);
            border-radius: 12px;
            padding: 10px 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .search-command:focus-within {
            border-color: rgb(14 165 233);
            background: white;
            box-shadow: 0 0 0 3px rgb(14 165 233 / 0.1);
        }
        .quick-action-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, border-color 0.2s, color 0.2s;
            background: rgb(248 250 252);
            border: 1px solid rgb(226 232 240);
            color: rgb(100 116 139);
            line-height: 1;
        }
        .quick-action-btn:hover {
            background: rgb(241 245 249);
            border-color: rgb(148 163 184);
            color: rgb(14 165 233);
        }
        .quick-action-btn i { display: inline-flex; align-items: center; justify-content: center; line-height: 1; }
        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            background: rgb(239 68 68);
            border-radius: 9999px;
            font-size: 10px;
            color: white;
            font-weight: 600;
            border: 2px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }
        .user-menu-btn { transition: background 0.2s; }
        .user-menu-btn:hover { background: rgb(248 250 252); }
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgb(14 165 233);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
            transition: box-shadow 0.2s;
            line-height: 1;
        }
        .user-avatar img { object-fit: cover; border-radius: 10px; }
        .user-menu-btn:hover .user-avatar { box-shadow: 0 2px 8px rgb(14 165 233 / 0.3); }
        .dropdown-menu {
            background: white;
            border: 1px solid rgb(226 232 240);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgb(0 0 0 / 0.08);
            overflow: hidden;
        }
        .dropdown-item {
            display: flex;
            align-items: center;
            transition: background 0.15s;
        }
        .dropdown-item:hover { background: rgb(248 250 252); }
        .dropdown-item i { display: inline-flex; align-items: center; justify-content: center; line-height: 1; }

        .logo-section {
            background: rgb(248 250 252);
            border-bottom: 1px solid rgb(226 232 240);
        }

        /* Fix Logo Alignment */
        .logo-section img,
        .student-sidebar img[alt*="Logo"],
        .navbar-gradient img[alt*="Logo"] {
            transform: none !important;
            rotate: 0deg !important;
            object-fit: contain !important;
            object-position: center center !important;
            display: block !important;
            margin: 0 auto !important;
        }

        /* Stats Section Enhancement */
        .stats-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(44, 169, 189, 0.2);
        }

        /* Scrollbar — brand teal */
        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, var(--ml-teal, #49A4A2), var(--ml-teal-deep, #2f7f7d));
            border-radius: 3px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, var(--ml-teal-deep, #2f7f7d), #246663);
        }

        .sidebar-scroll {
            scrollbar-width: thin;
            scrollbar-color: var(--ml-teal, #49A4A2) transparent;
        }

        /* Animations */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
            }
            to {
                transform: translateX(0);
            }
        }

        .slide-in-right {
            animation: slideInRight 0.3s ease-out;
        }
    </style>

    @stack('styles')
</head>
<body x-data="{ 
    sidebarOpen: window.innerWidth >= 1024
}" 
x-init="
    // إزالة الوضع المظلم من النظام بشكل مستمر
    function removeDarkMode() {
        document.documentElement.classList.remove('dark');
    }
    removeDarkMode();
    
    // مراقبة وإزالة الوضع المظلم بشكل مستمر
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                if (document.documentElement.classList.contains('dark')) {
                    removeDarkMode();
                }
            }
        });
    });
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
    
    // إزالة الوضع المظلم بشكل دوري
    setInterval(removeDarkMode, 100);
    
    window.addEventListener('resize', () => {
        sidebarOpen = window.innerWidth >= 1024;
    });
">
    <div class="flex h-screen overflow-hidden">
        @auth
            <!-- Clean Sidebar: always visible on lg+; drawer on mobile (no x-show opacity traps) -->
            <aside class="student-sidebar los-sidebar-bridge flex-shrink-0 fixed lg:static inset-y-0 z-50 lg:z-auto
                          {{ $appRtl ? 'right-0' : 'left-0' }}
                          transition-transform duration-150 ease-out lg:!translate-x-0
                          {{ $appRtl ? 'translate-x-full' : '-translate-x-full' }}"
                   :class="sidebarOpen ? 'translate-x-0' : '{{ $appRtl ? 'translate-x-full' : '-translate-x-full' }}'">
                @if(auth()->user()->isInstructor() || auth()->user()->isTeacher())
                    @include('layouts.instructor-sidebar')
                @else
                    @include('layouts.student-sidebar')
                @endif
            </aside>

            <!-- Mobile Overlay -->
            <div x-show="sidebarOpen"
                 x-cloak
                 @click="sidebarOpen = false"
                 class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>
        @endauth

        <!-- Main Content Area -->
        <div class="flex flex-col flex-1 min-w-0">
            @auth
                <!-- Enhanced Header -->
                <header class="student-header flex items-center justify-between px-3 sm:px-4 md:px-6 flex-shrink-0 sticky top-0 z-30">
                    <div class="flex items-center gap-2 sm:gap-3 md:gap-4 flex-1 min-w-0">
                        <!-- Sidebar Toggle -->
                        <button @click="sidebarOpen = !sidebarOpen"
                                class="lg:hidden p-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 hover:bg-slate-200 hover:border-slate-300 flex-shrink-0 flex items-center justify-center transition-colors">
                            <i class="fas fa-bars text-sm"></i>
                        </button>

                        <!-- Search - Mobile -->
                        <div class="flex md:hidden items-center flex-1 min-w-0 ml-2">
                            <div class="search-command flex items-center gap-2 w-full">
                                <i class="fas fa-search text-sky-500 text-xs sm:text-sm flex-shrink-0"></i>
                                <input type="text" placeholder="{{ __('common.nav_search_placeholder') }}" class="flex-1 bg-transparent border-none outline-none text-xs sm:text-sm text-slate-700 placeholder-slate-400 font-medium min-w-0">
                            </div>
                        </div>

                        <!-- Search - Desktop -->
                        <div class="hidden md:flex items-center flex-1 max-w-xl min-w-0">
                            <div class="search-command flex items-center gap-3 w-full">
                                <i class="fas fa-search text-sky-500 text-sm flex-shrink-0"></i>
                                <input type="text" placeholder="{{ __('common.nav_search_placeholder_long') }}" class="flex-1 bg-transparent border-none outline-none text-sm text-slate-700 placeholder-slate-400 font-medium min-w-0">
                                <kbd class="hidden lg:inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-600">Ctrl K</kbd>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 sm:gap-2 md:gap-3 flex-shrink-0">
                        <!-- Language Switcher -->
                        <x-language-switcher class="hidden sm:inline-flex" />
                        <!-- Quick Actions - Desktop Only -->
                        <div class="hidden lg:flex items-center gap-2">
                            <a href="{{ route('academic-years') }}" class="quick-action-btn" title="{{ __('common.browse_courses') }}">
                                <i class="fas fa-search text-sm"></i>
                            </a>
                            <a href="{{ route('my-courses.index') }}" class="quick-action-btn" title="{{ __('common.my_courses_title') }}">
                                <i class="fas fa-book-open text-sm"></i>
                            </a>
                        </div>

                        <!-- Notifications -->
                        <div class="relative" x-data="window.__navNotifications()">
                            <button @click="toggle()"
                                    class="quick-action-btn relative"
                                    aria-label="{{ __('student.notifications') }}">
                                <i class="fas fa-bell text-xs sm:text-sm"></i>
                                <template x-if="unreadCount > 0">
                                    <span class="notification-badge text-[9px] sm:text-[10px]" x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                                </template>
                            </button>
                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute end-0 mt-3 w-72 sm:w-80 md:w-96 dropdown-menu z-50 overflow-hidden text-start">
                                <div class="p-3 sm:p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between gap-3">
                                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                        <i class="fas fa-bell text-sky-500"></i>
                                        <span>{{ __('student.notifications') }}</span>
                                    </h3>
                                    <button type="button" class="text-xs font-bold text-sky-700 hover:text-sky-900"
                                            @click="markAllRead()" x-show="unreadCount > 0">
                                        {{ __('student.mark_all_read') }}
                                    </button>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    <template x-if="loading">
                                        <div class="p-4 sm:p-6 text-center text-slate-500 text-sm">{{ __('common.loading') }}</div>
                                    </template>
                                    <template x-if="!loading && items.length === 0">
                                        <div class="p-4 sm:p-6 text-center text-slate-500 text-sm">
                                            <i class="fas fa-bell-slash text-2xl mb-2 text-slate-300 inline-block"></i>
                                            <p>{{ __('student.no_notifications') }}</p>
                                        </div>
                                    </template>
                                    <div x-show="!loading && items.length > 0" class="divide-y divide-slate-100">
                                        <template x-for="n in items" :key="n.id">
                                            <a :href="n.action_url || '#'"
                                               @click.prevent="onClickItem(n)"
                                               class="block px-4 sm:px-5 py-3 hover:bg-slate-50/70 transition-colors">
                                                <div class="flex items-start gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-sky-50 border border-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0">
                                                        <i :class="n.type_icon"></i>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex items-center gap-2">
                                                            <div class="font-bold text-slate-900 text-sm truncate" x-text="n.title"></div>
                                                            <div class="w-2 h-2 rounded-full bg-sky-500 flex-shrink-0" x-show="!n.is_read"></div>
                                                        </div>
                                                        <div class="text-xs text-slate-600 mt-0.5 line-clamp-2" x-text="n.message"></div>
                                                        <div class="text-[11px] text-slate-400 mt-1" x-text="n.created_human"></div>
                                                    </div>
                                                </div>
                                            </a>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced User Profile -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="user-menu-btn flex items-center gap-1.5 sm:gap-2 md:gap-3 p-1 sm:p-1.5 md:p-2 rounded-xl"
                                    aria-label="{{ __('common.account') }}">
                                <div class="user-avatar flex-shrink-0">
                                    @if(auth()->user()->profile_image)
                                        <img src="{{ auth()->user()->profile_image_url }}" alt="" class="w-full h-full rounded-lg object-cover">
                                    @else
                                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                                    @endif
                                </div>
                                <div class="hidden sm:block md:hidden lg:block text-start min-w-0">
                                    <div class="text-xs sm:text-sm font-bold text-slate-800 truncate">{{ auth()->user()->name }}</div>
                                    <div class="text-[10px] sm:text-xs text-slate-500">
                                        @if(auth()->user()->isInstructor() || auth()->user()->isTeacher() || in_array(strtolower(auth()->user()->role ?? ''), ['instructor', 'teacher']))
                                            {{ __('student.instructor_role') }}
                                        @else
                                            {{ __('student.student_role') }}
                                        @endif
                                    </div>
                                </div>
                                <i class="fas fa-chevron-down text-[10px] sm:text-xs text-gray-400 hidden sm:block transition-transform flex-shrink-0" :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute end-0 mt-3 w-56 sm:w-64 dropdown-menu z-50 overflow-hidden text-start">
                                <div class="p-3 sm:p-4 border-b border-slate-200 bg-slate-50">
                                    <div class="flex items-center gap-3">
                                        <div class="user-avatar w-10 h-10 sm:w-12 sm:h-12 flex-shrink-0">
                                            @if(auth()->user()->profile_image)
                                                <img src="{{ auth()->user()->profile_image_url }}" alt="" class="w-full h-full rounded-xl object-cover">
                                            @else
                                                {{ mb_substr(auth()->user()->name, 0, 1) }}
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->name }}</div>
                                            <div class="text-xs text-slate-500 truncate">{{ auth()->user()->email ?? '—' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-2">
                                    @php
                                        $profileRoute = (auth()->user()->isInstructor() || auth()->user()->isTeacher() || in_array(strtolower(auth()->user()->role ?? ''), ['instructor', 'teacher'])) ? route('instructor.profile') : route('profile');
                                    @endphp
                                    <a href="{{ $profileRoute }}" class="dropdown-item px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700">
                                        <i class="fas fa-user w-5 text-slate-400 me-2"></i>
                                        {{ __('student.profile') }}
                                    </a>
                                    <a href="{{ route('settings') }}" class="dropdown-item px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700">
                                        <i class="fas fa-cog w-5 text-slate-400 me-2"></i>
                                        {{ __('student.settings') }}
                                    </a>
                                    <hr class="my-2 border-slate-200">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full dropdown-item px-3 py-2.5 rounded-lg text-sm font-medium text-red-600 text-start">
                                            <i class="fas fa-sign-out-alt w-5 me-2"></i>
                                            {{ __('auth.logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
            @endauth

            <!-- Main Content -->
            <main class="flex-1 overflow-auto bg-gray-50">
                <div class="p-6">
                    @if(session('success'))
                        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                            {{ session('error') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
    
    <script>
        window.__navNotifications = window.__navNotifications || function () {
            return {
                open: false,
                loading: false,
                unreadCount: 0,
                items: [],
                async refresh() {
                    try {
                        this.loading = true;
                        const res = await fetch(`{{ route('nav-notifications.recent') }}`, { headers: { 'Accept': 'application/json' } });
                        const data = await res.json();
                        this.unreadCount = Number(data.unread_count || 0);
                        this.items = Array.isArray(data.items) ? data.items : [];
                    } catch (e) {
                        // silent
                    } finally {
                        this.loading = false;
                    }
                },
                toggle() {
                    this.open = !this.open;
                    if (this.open) this.refresh();
                },
                async markAllRead() {
                    try {
                        await fetch(`{{ route('nav-notifications.mark-all-read') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({})
                        });
                    } catch (e) {
                        // silent
                    }
                    await this.refresh();
                },
                async onClickItem(n) {
                    try {
                        if (n && n.id && !n.is_read) {
                            await fetch(`{{ url('/api/nav-notifications') }}/${n.id}/mark-read`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({})
                            });
                        }
                    } catch (e) {
                        // silent
                    }
                    if (n && n.action_url) {
                        window.location.href = n.action_url;
                    }
                },
                init() {
                    this.refresh();
                    setInterval(() => this.refresh(), 30000);
                }
            }
        }
        // إزالة الوضع المظلم من النظام بشكل مستمر
        function removeDarkMode() {
            document.documentElement.classList.remove('dark');
        }
        
        // إزالة dark class فوراً
        removeDarkMode();
        
        // مراقبة وإزالة الوضع المظلم بشكل مستمر
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    if (document.documentElement.classList.contains('dark')) {
                        removeDarkMode();
                    }
                }
            });
        });
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });
        
        // إزالة الوضع المظلم بشكل دوري
        setInterval(removeDarkMode, 50);
        
        // إزالة dark class عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', removeDarkMode);
        window.addEventListener('load', removeDarkMode);
        window.addEventListener('pageshow', removeDarkMode);
    </script>
</body>
</html>