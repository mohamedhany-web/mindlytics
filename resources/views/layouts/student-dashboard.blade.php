@php $studentLocale = app()->getLocale(); $studentRtl = $studentLocale === 'ar'; @endphp
<!DOCTYPE html>
<html lang="{{ $studentLocale }}" dir="{{ $studentRtl ? 'rtl' : 'ltr' }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mindlytics') }} - @yield('title', __('auth.dashboard'))</title>

    <!-- Favicon -->
    @include('components.favicon-meta')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Tajawal:wght@400;500;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    {{-- Tailwind + Alpine + LOS CSS (production-safe paths) --}}
    <x-frontend-stack />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        [x-cloak] { display: none !important; }

        :root {
            --ml-teal: #49A4A2;
            --ml-yellow: #FFD23F;
            --ml-bg: #F7F9FC;
            --ml-surface: #FFFFFF;
            --ml-ink: #1A2238;
            --ml-ink-muted: #475569;
        }

        * {
            font-family: 'IBM Plex Sans Arabic', 'Tajawal', 'Cairo', sans-serif;
        }

        body {
            background: var(--ml-bg);
            overflow-x: hidden;
            color: var(--ml-ink);
        }

        /* Sidebar - يتناسب مع لوحة التحكم */
        .student-sidebar {
            background: #ffffff;
            border-left: 1px solid rgb(226 232 240);
            width: 280px;
            box-shadow: -1px 0 6px rgba(0, 0, 0, 0.04);
        }

        .nav-card {
            background: transparent;
            border: none;
            border-radius: 12px;
            padding: 10px 12px;
            transition: all 0.2s ease;
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
            background: var(--ml-teal);
            opacity: 0;
            border-radius: 0 3px 3px 0;
            transition: opacity 0.2s;
        }

        .nav-card:hover {
            background: rgb(241 245 249);
        }

        .nav-card.active {
            background: rgba(73, 164, 162, 0.12);
            box-shadow: none;
        }

        .nav-card.active::before {
            opacity: 1;
        }

        .nav-card.active .nav-icon {
            transform: scale(1.02);
            box-shadow: 0 2px 8px rgba(73, 164, 162, 0.2);
        }

        .nav-card.active .font-black { color: var(--ml-ink); }
        .nav-card.active .text-xs { color: var(--ml-ink-muted); }

        .nav-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.2s ease;
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
        .nav-card:hover .nav-icon { transform: scale(1.05); }

        /* Navbar - sticky Learning OS chrome */
        .student-header {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgb(226 232 240);
            min-height: 64px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            position: sticky;
            top: 0;
            z-index: 40;
        }
        @media (max-width: 640px) {
            .student-header {
                min-height: 56px;
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }
        }

        .search-command {
            background: rgb(248 250 252);
            border: 1px solid rgb(226 232 240);
            border-radius: 10px;
            padding: 10px 14px;
            transition: all 0.2s ease;
        }
        
        @media (max-width: 640px) {
            .search-command {
                padding: 8px 12px;
                border-radius: 10px;
            }
        }

        .search-command:focus-within {
            border-color: rgb(14 165 233);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1), 0 2px 8px rgba(14, 165, 233, 0.12);
        }

        .quick-action-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            background: rgb(248 250 252);
            border: 1px solid rgb(226 232 240);
            color: rgb(100 116 139);
            position: relative;
            line-height: 1;
            text-align: center;
        }
        .quick-action-btn:hover {
            background: rgb(224 242 254);
            border-color: rgb(186 230 253);
            color: rgb(14 165 233);
        }

        .quick-action-btn i {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            margin: 0;
            padding: 0;
            vertical-align: middle;
        }

        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            background: rgb(239 68 68);
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: white;
            font-weight: 700;
            border: 2px solid white;
            line-height: 1;
            text-align: center;
        }
        
        .notification-badge span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            margin: 0;
            padding: 0;
            vertical-align: middle;
        }

        .user-menu-btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .user-menu-btn:hover {
            background: rgb(248 250 252);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgb(14 165 233), rgb(2 132 199));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 13px;
            box-shadow: 0 1px 4px rgba(14, 165, 233, 0.25);
            transition: all 0.2s ease;
            line-height: 1;
            text-align: center;
        }
        
        .user-avatar img {
            object-fit: cover;
            object-position: center;
        }
        
        .user-avatar:not(:has(img)) {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-menu-btn:hover .user-avatar {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
        }

        /* Dropdown - نفس أسلوب بطاقات لوحة التحكم */
        .dropdown-menu {
            background: white;
            border: 1px solid rgb(226 232 240);
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .dropdown-item {
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
        }
        .dropdown-item:hover {
            background: rgb(248 250 252);
        }
        
        .dropdown-item i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            margin: 0;
            padding: 0;
            vertical-align: middle;
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

        .stats-card {
            transition: all 0.2s ease;
        }
        .stats-card:hover {
            box-shadow: 0 2px 8px rgba(14, 165, 233, 0.1);
        }

        .user-profile-card {
            background: rgb(248 250 252);
            border-top: 1px solid rgb(226 232 240);
        }
        .user-profile-inner {
            transition: all 0.2s ease;
        }
        .user-profile-inner:hover {
            border-color: rgb(186 230 253);
            box-shadow: 0 2px 8px rgba(14, 165, 233, 0.08);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .student-sidebar {
                width: 320px;
                max-width: 85vw;
                min-width: 280px;
            }

            .nav-card {
                padding: 12px 14px;
            }

            .nav-icon {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }
        }

        @media (max-width: 768px) {
            .student-sidebar {
                width: 300px;
                max-width: 80vw;
                min-width: 260px;
            }
            
            .student-header {
                padding-left: 1rem;
                padding-right: 1rem;
                height: auto;
                min-height: 64px;
            }
            
            .search-command {
                padding: 8px 12px;
            }
        }

        @media (max-width: 640px) {
            .student-sidebar {
                width: 280px;
                max-width: 85vw;
                min-width: 0;
            }

            .logo-section {
                padding: 0.875rem;
            }

            .logo-section .w-12 {
                width: 2.5rem;
                height: 2.5rem;
            }

            .stats-card {
                padding: 0.625rem;
            }

            .stats-card .text-lg {
                font-size: 1.125rem;
            }

            .nav-card {
                padding: 10px 12px;
                margin-bottom: 4px;
            }

            .nav-icon {
                width: 32px;
                height: 32px;
                font-size: 13px;
            }

            .user-profile-card {
                padding: 0.625rem;
            }
            
            .student-header {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
                gap: 0.5rem;
            }
            
            .quick-action-btn {
                width: 38px;
                height: 38px;
            }
            
            .user-avatar {
                width: 34px;
                height: 34px;
                font-size: 13px;
            }
        }
        
        @media (max-width: 480px) {
            .student-sidebar {
                width: 260px;
                max-width: 90vw;
            }
            
            .student-header {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            
            .quick-action-btn {
                width: 36px;
                height: 36px;
            }
            
            .quick-action-btn i {
                font-size: 12px;
            }
            
            .user-avatar {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="los-shell" x-data="{ 
    sidebarOpen: window.innerWidth >= 1024
}" 
x-init="
    function removeDarkMode() {
        document.documentElement.classList.remove('dark');
    }
    removeDarkMode();
    
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
    
    setInterval(removeDarkMode, 100);
    
    window.addEventListener('resize', () => {
        sidebarOpen = window.innerWidth >= 1024;
    });
">
    <div class="los-app">
        @auth
            <!-- Clean Sidebar: CSS shell (works without Tailwind); Alpine toggles is-open on mobile -->
            <aside class="student-sidebar los-sidebar-bridge"
                   :class="sidebarOpen ? 'is-open' : 'is-closed'">
                @include('layouts.student-sidebar')
            </aside>

            <!-- Mobile Overlay -->
            <div x-show="sidebarOpen"
                 x-cloak
                 @click="sidebarOpen = false"
                 class="los-overlay los-overlay-mobile"></div>
        @endauth

        <!-- Main Content Area -->
        <div class="los-app-main">
            @auth
                <!-- Learning OS Top Navigation -->
                <header class="los-topnav student-header los-topnav-bridge">
                    <div class="los-topnav-start">
                        <button type="button" @click="sidebarOpen = !sidebarOpen"
                                class="los-icon-btn los-menu-toggle" aria-label="{{ __('common.menu') }}">
                            <i class="fas fa-bars text-sm"></i>
                        </button>

                        <button type="button" data-los-open-palette class="los-topnav-search" aria-label="{{ __('common.quick_search') }}">
                            <i class="fas fa-magnifying-glass text-xs" style="color:var(--ml-teal)"></i>
                            <span class="los-search-label-lg">{{ __('common.search_or_jump') }}</span>
                            <span class="los-search-label-sm">{{ __('common.search') }}</span>
                            <kbd class="los-kbd-hint">Ctrl K</kbd>
                        </button>
                    </div>

                    <div class="los-topnav-end">
                        @php
                            $navProgress = 0;
                            try {
                                $navEnroll = auth()->user()->courseEnrollments()->whereIn('status', ['active','completed'])->get();
                                $navProgress = $navEnroll->isEmpty() ? 0 : (int) round($navEnroll->avg('progress') ?? 0);
                            } catch (\Throwable $e) { $navProgress = 0; }
                        @endphp
                        <div class="los-progress-pill" title="{{ __('common.learning_progress') }}">
                            <span>{{ $navProgress }}{{ $studentRtl ? '٪' : '%' }}</span>
                            <span class="bar" aria-hidden="true"><i style="width:{{ min(100,$navProgress) }}%"></i></span>
                        </div>

                        <a href="{{ route('dashboard') }}#los-ai" class="los-icon-btn is-ai" title="{{ __('common.ai_guide') }}" aria-label="{{ __('common.ai_guide') }}">
                            <i class="fas fa-wand-magic-sparkles text-sm"></i>
                        </a>
                        <a href="{{ route('calendar') }}" class="los-icon-btn hidden sm:inline-flex" title="{{ __('student.calendar') }}" aria-label="{{ __('student.calendar') }}">
                            <i class="fas fa-calendar-days text-sm"></i>
                        </a>

                        <x-language-switcher class="hidden sm:inline-flex" />

                        <div class="relative" x-data="window.__navNotifications()">
                            <button type="button" @click="toggle()" class="los-icon-btn relative" aria-label="{{ __('student.notifications') }}">
                                <i class="fas fa-bell text-sm"></i>
                                <template x-if="unreadCount > 0">
                                    <span class="los-badge" x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                                </template>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute end-0 mt-2 w-80 sm:w-96 dropdown-menu z-50 overflow-hidden text-start">
                                <div class="p-3 border-b border-gray-100 flex items-center justify-between gap-2" style="background:rgba(73,164,162,0.08)">
                                    <h3 class="font-bold text-sm text-gray-900">{{ __('student.notifications') }}</h3>
                                    <button type="button" class="text-xs font-bold" style="color:var(--ml-teal-deep)"
                                            @click="markAllRead()" x-show="unreadCount > 0">{{ __('student.mark_all_read') }}</button>
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    <template x-if="loading"><div class="p-5 text-center text-sm text-gray-500">{{ __('common.loading') }}</div></template>
                                    <template x-if="!loading && items.length === 0">
                                        <div class="p-6 text-center text-sm text-gray-500">{{ __('student.no_notifications') }}</div>
                                    </template>
                                    <div x-show="!loading && items.length > 0" class="divide-y divide-gray-100">
                                        <template x-for="n in items" :key="n.id">
                                            <a :href="n.action_url || '#'" @click.prevent="onClickItem(n)" class="block px-4 py-3 hover:bg-gray-50">
                                                <div class="font-bold text-sm text-gray-900 truncate" x-text="n.title"></div>
                                                <div class="text-xs text-gray-600 mt-0.5 line-clamp-2" x-text="n.message"></div>
                                                <div class="text-[11px] text-gray-400 mt-1" x-text="n.created_human"></div>
                                            </a>
                                        </template>
                                    </div>
                                </div>
                                <div class="p-3 border-t border-gray-100">
                                    <a href="{{ route('notifications') }}" class="text-xs font-bold" style="color:var(--ml-teal-deep)">{{ __('common.all_notifications') }}</a>
                                </div>
                            </div>
                        </div>

                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="los-icon-btn" style="width:auto;padding:0 8px;gap:8px" aria-label="{{ __('common.account') }}">
                                <span class="user-avatar" style="width:28px;height:28px;font-size:11px;background:var(--ml-teal);box-shadow:none">
                                    @if(auth()->user()->profile_image)
                                        <img src="{{ auth()->user()->profile_image_url }}" alt="" class="w-full h-full rounded-lg object-cover">
                                    @else
                                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                                    @endif
                                </span>
                                <i class="fas fa-chevron-down text-[10px] hidden sm:inline"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute end-0 mt-2 w-56 dropdown-menu z-50 overflow-hidden text-start">
                                <div class="p-3 border-b border-gray-100">
                                    <div class="font-bold text-sm truncate">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</div>
                                </div>
                                <div class="p-1.5">
                                    <a href="{{ route('profile') }}" class="dropdown-item gap-2 px-3 py-2 rounded-lg text-sm">{{ __('student.profile') }}</a>
                                    <a href="{{ route('settings') }}" class="dropdown-item gap-2 px-3 py-2 rounded-lg text-sm">{{ __('student.settings') }}</a>
                                    <hr class="my-1 border-gray-100">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full dropdown-item gap-2 px-3 py-2 rounded-lg text-sm text-red-600 text-start">{{ __('auth.logout') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
            @endauth

            <!-- Main Content -->
            <main class="flex-1 overflow-auto los-main min-w-0 w-full">
                <div class="w-full max-w-none p-3 sm:p-4 lg:p-5 xl:p-6">
                    @if(session('workshop_promo_welcome_modal'))
                        @include('components.workshop-promo-welcome-modal')
                    @endif

                    @if(session('success') && !session('payment_success_modal') && !session('workshop_promo_welcome_modal'))
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm font-medium">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm font-medium">
                            {{ session('error') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @auth
        @include('components.learning-os.command-palette')
        <button type="button" class="los-fab-ai" data-los-open-palette aria-label="{{ __('common.command_palette_fab') }}" title="Ctrl K">
            <i class="fas fa-wand-magic-sparkles"></i>
        </button>
    @endauth

    <script src="{{ asset('js/mindlytics-los.js') }}?v=1"></script>
    @stack('scripts')
    
    <script>
        window.__navNotifications = function () {
            return {
                open: false,
                loading: false,
                unreadCount: 0,
                items: [],
                async refresh() {
                    try {
                        this.loading = true;
                        const res = await fetch(`{{ route('nav-notifications.recent') }}`, {
                            headers: { 'Accept': 'application/json' }
                        });
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
        function removeDarkMode() {
            document.documentElement.classList.remove('dark');
        }
        
        removeDarkMode();
        
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
        
        setInterval(removeDarkMode, 50);
        
        document.addEventListener('DOMContentLoaded', removeDarkMode);
        window.addEventListener('load', removeDarkMode);
        window.addEventListener('pageshow', removeDarkMode);
    </script>
</body>
</html>
