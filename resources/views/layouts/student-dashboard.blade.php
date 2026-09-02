@php
    $studentLocale = app()->getLocale();
    $studentRtl = $studentLocale === 'ar';
    $spIcons = \App\Support\StudentFigmaAssets::urls();
    $isStudentUser = auth()->check() && (auth()->user()->role === 'student' || strtolower((string) auth()->user()->role) === 'student');
    $scholarshipOnlyPortal = $isStudentUser && auth()->user()->usesScholarshipOnlyPortal();
    $immersive = trim((string) ($__env->yieldContent('immersive'))) === '1'
        || trim((string) ($__env->yieldContent('immersive'))) === 'true';
@endphp
<!DOCTYPE html>
<html lang="{{ $studentLocale }}" dir="{{ $studentRtl ? 'rtl' : 'ltr' }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Mindlytics') }} - @yield('title', __('auth.dashboard'))</title>
    @include('components.favicon-meta')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @include('components.student.portal-theme')
    <style>[x-cloak]{display:none!important}</style>
    @stack('styles')
</head>
<body class="student-portal student-portal-body antialiased {{ $immersive ? 'sp-immersive' : '' }}"
      x-data="{ sidebarOpen: {{ $immersive ? 'false' : 'window.innerWidth >= 1024' }} }"
      x-init="@unless($immersive) window.addEventListener('resize', () => { sidebarOpen = window.innerWidth >= 1024 }) @endunless">
    <div class="sp-shell flex h-screen overflow-hidden {{ $immersive ? 'sp-shell--immersive' : '' }}">
        @auth
            @unless($immersive)
            <aside class="fixed lg:static inset-y-0 z-50 lg:z-auto flex-shrink-0"
                   :class="sidebarOpen || window.innerWidth >= 1024 ? 'block' : 'hidden'"
                   style="{{ $studentRtl ? 'right:0' : 'left:0' }}">
                <div class="h-full"
                     x-show="sidebarOpen || window.innerWidth >= 1024"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 {{ $studentRtl ? 'translate-x-8' : '-translate-x-8' }}"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 {{ $studentRtl ? 'translate-x-8' : '-translate-x-8' }}">
                    @include('layouts.student-sidebar')
                </div>
            </aside>

            <div x-show="sidebarOpen && window.innerWidth < 1024"
                 @click="sidebarOpen = false"
                 x-cloak
                 class="fixed inset-0 bg-black/45 z-40 lg:hidden"></div>
            @endunless
        @endauth

        <div class="sp-main {{ $immersive ? 'sp-main--immersive' : '' }}">
            @auth
                @unless($immersive)
                <header class="sp-header">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <button type="button" @click="sidebarOpen = !sidebarOpen"
                                class="lg:hidden size-10 rounded-full bg-white shadow flex items-center justify-center text-[var(--sp-sidebar)]">
                            <x-student.figma-icon name="icon-courses.svg" box="size-5" />
                        </button>
                        <h1 class="sp-welcome-title truncate">
                            @hasSection('page_heading')
                                @yield('page_heading')
                            @elseif(trim($__env->yieldContent('header')) !== '')
                                @yield('header')
                            @else
                                {{ __('student.welcome_back_name', ['name' => auth()->user()->name]) }}
                            @endif
                        </h1>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                        <form action="{{ $scholarshipOnlyPortal ? route('my-courses.index') : route('academic-years') }}" method="GET" class="sp-search hidden sm:flex">
                            <x-student.figma-icon name="icon-search.svg" box="size-5" />
                            <input type="search" name="q" placeholder="{{ __('student.search_courses') }}" value="{{ request('q') }}">
                        </form>

                        <x-student.language-switcher />

                        <div class="relative" x-data="window.__navNotifications()">
                            <button type="button" @click="toggle()" class="size-10 rounded-full bg-white shadow flex items-center justify-center relative text-[var(--sp-sidebar)]">
                                <x-student.figma-icon name="icon-notifications.svg" box="size-5" />
                                <template x-if="unreadCount > 0">
                                    <span class="absolute -top-0.5 -end-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-[#f4a89a] text-[10px] font-extrabold text-[#1f1e31] flex items-center justify-center" x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                                </template>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak x-transition
                                 class="absolute end-0 mt-3 w-80 max-w-[90vw] bg-white rounded-2xl shadow-xl border border-black/5 z-50 overflow-hidden">
                                <div class="p-3 border-b flex items-center justify-between gap-2">
                                    <span class="font-bold text-sm">{{ __('student.notifications') }}</span>
                                    <button type="button" class="text-xs font-bold text-[var(--sp-accent-text)]" @click="markAllRead()" x-show="unreadCount > 0">{{ __('student.mark_all_read') }}</button>
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    <template x-if="loading"><div class="p-4 text-center text-sm text-gray-500">…</div></template>
                                    <template x-if="!loading && items.length === 0"><div class="p-6 text-center text-sm text-gray-500">{{ __('student.no_notifications') }}</div></template>
                                    <template x-for="n in items" :key="n.id">
                                        <a :href="n.action_url || '#'" @click.prevent="onClickItem(n)" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-50">
                                            <div class="font-bold text-sm truncate" x-text="n.title"></div>
                                            <div class="text-xs text-gray-600 mt-0.5 line-clamp-2" x-text="n.message"></div>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="flex items-center">
                                <x-student.avatar />
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak x-transition
                                 class="absolute end-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-black/5 z-50 overflow-hidden">
                                <a href="{{ route('profile') }}" class="block px-4 py-3 text-sm font-bold hover:bg-gray-50">{{ __('student.profile') }}</a>
                                <a href="{{ route('settings') }}" class="block px-4 py-3 text-sm font-bold hover:bg-gray-50">{{ __('student.settings') }}</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-start px-4 py-3 text-sm font-bold text-red-600 hover:bg-red-50">{{ __('student.logout') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>
                @endunless
            @endauth

            <main class="{{ $immersive ? 'sp-content sp-content--immersive' : 'sp-content' }}">
                @unless($immersive)
                    @if(session('workshop_promo_welcome_modal'))
                        @include('components.workshop-promo-welcome-modal')
                    @endif
                    @if(session('success') && !session('payment_success_modal') && !session('workshop_promo_welcome_modal'))
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl text-sm font-medium">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl text-sm font-medium">{{ session('error') }}</div>
                    @endif
                @endunless
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
    <script>
        window.__navNotifications = function () {
            return {
                open: false, loading: false, unreadCount: 0, items: [],
                async refresh() {
                    try {
                        this.loading = true;
                        const res = await fetch(`{{ route('nav-notifications.recent') }}`, { headers: { 'Accept': 'application/json' } });
                        const data = await res.json();
                        this.unreadCount = Number(data.unread_count || 0);
                        this.items = Array.isArray(data.items) ? data.items : [];
                    } catch (e) {} finally { this.loading = false; }
                },
                toggle() { this.open = !this.open; if (this.open) this.refresh(); },
                async markAllRead() {
                    try {
                        await fetch(`{{ route('nav-notifications.mark-all-read') }}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                            body: JSON.stringify({})
                        });
                    } catch (e) {}
                    await this.refresh();
                },
                async onClickItem(n) {
                    try {
                        if (n && n.id && !n.is_read) {
                            await fetch(`{{ url('/api/nav-notifications') }}/${n.id}/mark-read`, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                                body: JSON.stringify({})
                            });
                        }
                    } catch (e) {}
                    if (n && n.action_url) window.location.href = n.action_url;
                },
                init() { this.refresh(); setInterval(() => this.refresh(), 30000); }
            }
        }
    </script>
</body>
</html>
