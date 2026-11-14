@php
    use App\Support\LandingAssets;
    $a = LandingAssets::urls();
    $logoUrl = filled($platformLogoUrl ?? null)
        ? $platformLogoUrl
        : \App\Support\SiteBranding::logoUrl();
    $isRtl = app()->getLocale() === 'ar';
    $otherLang = $isRtl ? 'en' : 'ar';
    $otherLangLabel = $isRtl ? 'EN' : 'ع';
@endphp

<header
    class="lp-header bg-white"
    data-node-id="40:1898"
    x-data="{ open: false }"
    x-init="$watch('open', value => { document.body.style.overflow = value ? 'hidden' : ''; })"
    @keydown.escape.window="open = false"
>
    @include('components.landing.promo-bar')

    <div class="lp-header__nav">
        <div class="lp-container lp-header__inner">
            <a href="{{ route('home') }}" class="lp-header__brand" aria-label="Mindlytics">
                <img src="{{ $logoUrl }}" alt="Mindlytics" width="32" height="32" class="lp-header__logo" onerror="this.onerror=null;this.src='{{ asset('logo-fallback.svg') }}';">
                <span class="lp-header__name">Mindlytics</span>
            </a>

            <nav class="lp-header__links" aria-label="{{ __('landing.educo.header.nav_label') }}">
                <a href="{{ route('home') }}" class="is-active">{{ __('landing.educo.header.home') }}</a>
                <a href="{{ url('/').'#about' }}">{{ __('landing.educo.header.about') }}</a>
                <a href="{{ route('public.courses') }}">{{ __('landing.educo.header.courses') }}</a>
                <a href="{{ route('public.instructors.index') }}">{{ __('landing.educo.header.instructors') }}</a>
                <a href="{{ route('public.learning-paths.index') }}">{{ __('landing.educo.header.events') }}</a>
                <a href="{{ route('public.contact') }}">{{ __('landing.educo.header.contact') }}</a>
            </nav>

            <div class="lp-header__actions">
                <a href="{{ url('/').'?lang='.$otherLang }}" class="lp-header__lang" title="{{ __('landing.educo.header.lang') }}">
                    <img src="{{ $a['header_globe'] }}" alt="" width="20" height="20">
                    <span>{{ $otherLangLabel }}</span>
                </a>
                @auth
                    <a href="{{ url('/dashboard') }}" class="lp-header__login">{{ __('landing.nav.dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="lp-header__login">{{ __('landing.educo.header.login') }}</a>
                    <a href="{{ route('register') }}" class="lp-header__signup">{{ __('landing.educo.header.signup') }}</a>
                @endauth
            </div>

            <button
                type="button"
                class="lp-header__burger"
                @click="open = !open"
                :aria-expanded="open.toString()"
                aria-controls="lp-mobile-nav"
            >
                <span class="sr-only">{{ __('landing.nav.mobile_menu') }}</span>
                <i class="fas fa-bars" x-show="!open"></i>
                <i class="fas fa-times" x-cloak x-show="open"></i>
            </button>
        </div>
    </div>

    <div
        class="lp-header__backdrop"
        :class="{ 'is-open': open }"
        @click="open = false"
        aria-hidden="true"
    ></div>

    <nav
        id="lp-mobile-nav"
        class="lp-header__drawer"
        :class="{ 'is-open': open }"
        :aria-hidden="(!open).toString()"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('landing.nav.mobile_menu') }}"
    >
        <div class="lp-header__drawer-head">
            <a href="{{ route('home') }}" class="lp-header__brand" @click="open = false" aria-label="Mindlytics">
                <img src="{{ $logoUrl }}" alt="" width="28" height="28" class="lp-header__logo" onerror="this.onerror=null;this.src='{{ asset('logo-fallback.svg') }}';">
                <span class="lp-header__name">Mindlytics</span>
            </a>
            <button type="button" class="lp-header__drawer-close" @click="open = false">
                <span class="sr-only">{{ __('landing.nav.close_menu') }}</span>
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>

        <div class="lp-header__drawer-links">
            <a href="{{ route('home') }}" @click="open = false">{{ __('landing.educo.header.home') }}</a>
            <a href="{{ url('/').'#about' }}" @click="open = false">{{ __('landing.educo.header.about') }}</a>
            <a href="{{ route('public.courses') }}" @click="open = false">{{ __('landing.educo.header.courses') }}</a>
            <a href="{{ route('public.instructors.index') }}" @click="open = false">{{ __('landing.educo.header.instructors') }}</a>
            <a href="{{ route('public.learning-paths.index') }}" @click="open = false">{{ __('landing.educo.header.events') }}</a>
            <a href="{{ route('public.contact') }}" @click="open = false">{{ __('landing.educo.header.contact') }}</a>
        </div>

        <div class="lp-header__drawer-foot">
            <a href="{{ url('/').'?lang='.$otherLang }}" class="lp-header__lang" title="{{ __('landing.educo.header.lang') }}">
                <img src="{{ $a['header_globe'] }}" alt="" width="20" height="20">
                <span>{{ $otherLangLabel }}</span>
            </a>
            <div class="lp-header__mobile-cta">
                @auth
                    <a href="{{ url('/dashboard') }}" class="lp-header__signup" @click="open = false">{{ __('landing.nav.dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="lp-header__login" @click="open = false">{{ __('landing.educo.header.login') }}</a>
                    <a href="{{ route('register') }}" class="lp-header__signup" @click="open = false">{{ __('landing.educo.header.signup') }}</a>
                @endauth
            </div>
        </div>
    </nav>
</header>
