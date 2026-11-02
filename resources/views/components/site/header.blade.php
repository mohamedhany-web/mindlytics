{{-- هيدر الموقع العام: اللوجو من إعدادات المنصة (SiteBranding)، عربي وإنجليزي. docs/SITE_DESIGN.md --}}
@php
    $siteAr = app()->getLocale() === 'ar';
    $t = fn (string $ar, string $en) => $siteAr ? $ar : $en;
    $current = (string) request()->route()?->getName();
    $is = fn (string ...$prefixes) => collect($prefixes)->contains(fn ($p) => $current === $p || str_starts_with($current, $p.'.'));
    $navItems = [
        ['label' => $t('المسارات', 'Learning paths'), 'url' => route('public.learning-paths.index'), 'on' => $is('public.learning-paths', 'public.learning-path')],
        ['label' => $t('الكورسات', 'Courses'), 'url' => route('public.courses'), 'on' => $is('public.courses', 'public.course')],
        ['label' => $t('الدفعات القادمة', 'Upcoming intakes'), 'url' => route('home').'#intakes', 'on' => false],
        ['label' => $t('المحاضرون', 'Instructors'), 'url' => route('public.instructors.index'), 'on' => $is('public.instructors')],
        ['label' => $t('أعمال الطلاب', 'Student work'), 'url' => route('public.portfolio.index'), 'on' => $is('public.portfolio')],
    ];
    $otherLocaleUrl = request()->fullUrlWithQuery(['lang' => $siteAr ? 'en' : 'ar']);
    $logoUrl = $platformLogoUrl ?? null;
@endphp
<div class="h-topbar">
    <div class="h-wrap">
        <span>{{ $t('القبول مفتوح للدفعات القادمة', 'Admissions are open for upcoming intakes') }} — <a href="{{ route('home') }}#intakes" style="color: var(--h-accent); font-weight: 600">{{ $t('اعرض المواعيد', 'See dates') }}</a></span>
        <div class="h-topbar-links">
            <a href="{{ route('public.certificates.verify') }}" class="h-hide-sm">{{ $t('التحقق من شهادة', 'Verify a certificate') }}</a>
            <a href="{{ $otherLocaleUrl }}" hreflang="{{ $siteAr ? 'en' : 'ar' }}">{{ $siteAr ? 'English' : 'العربية' }}</a>
            @auth
                <a href="{{ route('dashboard') }}" style="font-weight: 600">{{ $t('حسابي', 'My account') }}</a>
            @else
                <a href="{{ route('login') }}" style="font-weight: 600">{{ $t('تسجيل الدخول', 'Sign in') }}</a>
            @endauth
        </div>
    </div>
</div>

<header class="h-header">
    <div class="h-wrap">
        <a href="{{ route('home') }}" class="h-logo" aria-label="Mindlytics — {{ $t('الرئيسية', 'Home') }}">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="" width="42" height="42">
            @else
                <span class="h-logo-mark" aria-hidden="true">M</span>
            @endif
            <span class="h-logo-text"><b>Mindlytics</b><span>{{ $t('أكاديمية البرمجة وتطوير البرمجيات', 'Software development academy') }}</span></span>
        </a>
        <nav class="h-nav" aria-label="{{ $t('القائمة الرئيسية', 'Main menu') }}">
            @foreach($navItems as $item)
                <a href="{{ $item['url'] }}" @if($item['on']) aria-current="page" @endif>{{ $item['label'] }}</a>
            @endforeach
        </nav>
        <div class="h-header-actions">
            @guest
                <a href="{{ route('register') }}" class="h-link h-hide-sm" style="font-size: 15px">{{ $t('إنشاء حساب', 'Create account') }}</a>
            @endguest
            <a href="{{ route('advising.show') }}" data-advising-open class="h-btn h-btn-primary h-header-cta">{{ $t('احجز استشارة', 'Book a consultation') }}</a>
            <button type="button" class="h-menu-btn" aria-expanded="false" aria-controls="h-mobile-menu" aria-label="{{ $t('فتح القائمة', 'Open menu') }}" data-label-open="{{ $t('فتح القائمة', 'Open menu') }}" data-label-close="{{ $t('إغلاق القائمة', 'Close menu') }}" data-menu-toggle>
                <svg width="20" height="14" viewBox="0 0 20 14" aria-hidden="true"><path d="M0 1H20M0 7H20M0 13H20" stroke="#17142A" stroke-width="2" fill="none"/></svg>
            </button>
        </div>
    </div>
    <nav id="h-mobile-menu" class="h-mobile-menu" aria-label="{{ $t('القائمة', 'Menu') }}">
        @foreach($navItems as $item)
            <a href="{{ $item['url'] }}" data-menu-close>{{ $item['label'] }}</a>
        @endforeach
        <a href="{{ route('public.certificates.verify') }}">{{ $t('التحقق من شهادة', 'Verify a certificate') }}</a>
        <a href="{{ route('public.contact') }}">{{ $t('تواصل معنا', 'Contact us') }}</a>
        @auth
            <a href="{{ route('dashboard') }}" style="color: var(--h-brand)">{{ $t('حسابي', 'My account') }}</a>
        @else
            <a href="{{ route('login') }}" style="color: var(--h-brand)">{{ $t('تسجيل الدخول', 'Sign in') }}</a>
            <a href="{{ route('register') }}" style="color: var(--h-brand)">{{ $t('إنشاء حساب', 'Create account') }}</a>
        @endauth
    </nav>
</header>
