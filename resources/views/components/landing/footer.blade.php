@php
    use App\Support\LandingAssets;
    $a = LandingAssets::urls();
    $logoUrl = filled($platformLogoUrl ?? null)
        ? $platformLogoUrl
        : \App\Support\SiteBranding::logoUrl();
@endphp

<footer class="lp-footer" data-node-id="40:1966">
    <div class="lp-container lp-footer__grid">
        <div class="lp-footer__brand">
            <a href="{{ route('home') }}" class="lp-header__brand mb-4">
                <img src="{{ $logoUrl }}" alt="Mindlytics" width="32" height="32" class="lp-header__logo" onerror="this.onerror=null;this.src='{{ asset('logo-fallback.svg') }}';">
                <span class="lp-header__name">Mindlytics</span>
            </a>
            <p class="lp-lead lp-footer__about">{{ __('landing.educo.footer.about') }}</p>
            <a href="{{ route('register') }}" class="lp-footer__cta">
                <span>{{ __('landing.educo.footer.get_started') }}</span>
                <img src="{{ $a['header_arrow'] }}" alt="" width="16" height="16">
            </a>
        </div>

        <div>
            <h3 class="lp-footer__title">{{ __('landing.educo.footer.company') }}</h3>
            <ul class="lp-footer__list">
                <li><a href="{{ route('home') }}">{{ __('landing.educo.header.home') }}</a></li>
                <li><a href="{{ route('public.about') }}">{{ __('landing.educo.header.about') }}</a></li>
                <li><a href="{{ route('public.courses') }}">{{ __('landing.educo.header.courses') }}</a></li>
                <li><a href="{{ route('public.instructors.index') }}">{{ __('landing.educo.header.instructors') }}</a></li>
                <li><a href="{{ route('public.contact') }}">{{ __('landing.educo.header.contact') }}</a></li>
            </ul>
        </div>

        <div>
            <h3 class="lp-footer__title">{{ __('landing.educo.footer.resources') }}</h3>
            <ul class="lp-footer__list">
                <li><a href="{{ route('public.courses') }}">{{ __('landing.educo.header.courses') }}</a></li>
                <li><a href="{{ route('public.learning-paths.index') }}">{{ __('landing.nav.learning_paths') }}</a></li>
                <li><a href="{{ route('public.portfolio.index') }}">{{ __('landing.nav.portfolio') }}</a></li>
                <li><a href="{{ route('public.internships.index') }}">{{ __('landing.nav.internships') }}</a></li>
                <li><a href="{{ route('public.community.index') }}">{{ __('landing.nav.community_short') }}</a></li>
            </ul>
        </div>

        <div>
            <h3 class="lp-footer__title">{{ __('landing.educo.footer.help') }}</h3>
            <ul class="lp-footer__list">
                <li><a href="{{ route('public.contact') }}">{{ __('landing.educo.footer.support') }}</a></li>
                @if (Route::has('public.terms'))
                    <li><a href="{{ route('public.terms') }}">{{ __('landing.educo.footer.terms') }}</a></li>
                @endif
                @if (Route::has('public.privacy'))
                    <li><a href="{{ route('public.privacy') }}">{{ __('landing.educo.footer.privacy') }}</a></li>
                @endif
                <li><a href="{{ route('register') }}">{{ __('landing.educo.header.signup') }}</a></li>
            </ul>
        </div>
    </div>

    <div class="lp-footer__bottom">
        <div class="lp-container lp-footer__bottom-inner">
            <p>{{ __('landing.educo.footer.copyright', ['year' => date('Y')]) }}</p>
            <p>{{ __('landing.educo.footer.tagline') }}</p>
        </div>
    </div>
</footer>
