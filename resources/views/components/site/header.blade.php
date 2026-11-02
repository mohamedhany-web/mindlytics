{{-- هيدر الموقع العام: اللوجو من إعدادات المنصة (SiteBranding)، عربي وإنجليزي. docs/SITE_DESIGN.md --}}
@php
    $siteAr = app()->getLocale() === 'ar';
    $t = fn (string $ar, string $en) => $siteAr ? $ar : $en;
    $current = (string) request()->route()?->getName();
    $is = fn (string ...$prefixes) => collect($prefixes)->contains(fn ($p) => $current === $p || str_starts_with($current, $p.'.'));
    $navItems = [
        ['label' => $t('البرامج التدريبية', 'Programs'), 'url' => route('public.learning-paths.index'), 'on' => $is('public.learning-paths', 'public.learning-path'), 'icon' => 'M4 5h16v14H4zM8 9h8M8 13h5'],
        ['label' => $t('الكورسات', 'Courses'), 'url' => route('public.courses'), 'on' => $is('public.courses', 'public.course'), 'icon' => 'M5 4h10l4 4v12H5zM9 12h6M9 16h6'],
        ['label' => $t('الدفعات القادمة', 'Upcoming intakes'), 'url' => route('home').'#intakes', 'on' => false, 'icon' => 'M4 6h16v14H4zM4 10h16M9 3v5M15 3v5'],
        ['label' => $t('المحاضرون', 'Instructors'), 'url' => route('public.instructors.index'), 'on' => $is('public.instructors'), 'icon' => 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM4 21c1-4 4-6 8-6s7 2 8 6'],
        ['label' => $t('أعمال الطلاب', 'Student work'), 'url' => route('public.portfolio.index'), 'on' => $is('public.portfolio'), 'icon' => 'M4 5h16v11H4zM9 20h6M12 16v4'],
    ];
    $otherLocaleUrl = request()->fullUrlWithQuery(['lang' => $siteAr ? 'en' : 'ar']);
    $logoUrl = $platformLogoUrl ?? null;
    // «القبول مفتوح» بيظهر بس لو فيه دفعات نشطة في لوحة الدبلومات/الكورسات
    try {
        $siteHasIntakes = \Illuminate\Support\Facades\Cache::remember('site.has_intakes', 300, fn () => \App\Domains\Sales\Models\SalesDiplomaBoardEntry::query()->active()->exists()
            || \App\Domains\Sales\Models\SalesCourseBoardEntry::query()->active()->exists());
    } catch (\Throwable $e) {
        $siteHasIntakes = false;
    }
@endphp
<div class="h-topbar">
    <div class="h-wrap">
        @if($siteHasIntakes)
            <span>{{ $t('القبول مفتوح للدفعات القادمة', 'Admissions are open for upcoming intakes') }} — <a href="{{ route('home') }}#intakes" style="color: var(--h-accent); font-weight: 600">{{ $t('اعرض المواعيد', 'See dates') }}</a></span>
        @else
            <span>{{ $t('مش عارف تبدأ منين؟', 'Not sure where to start?') }} — <a href="{{ route('advising.show') }}" data-advising-open style="color: var(--h-accent); font-weight: 600">{{ $t('احجز جلسة إرشاد مجانية', 'Book a free guidance session') }}</a></span>
        @endif
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
            <button type="button" class="h-menu-btn" aria-expanded="false" aria-controls="h-drawer" aria-label="{{ $t('فتح القائمة', 'Open menu') }}" data-label-open="{{ $t('فتح القائمة', 'Open menu') }}" data-label-close="{{ $t('إغلاق القائمة', 'Close menu') }}" data-menu-toggle>
                <svg width="20" height="14" viewBox="0 0 20 14" aria-hidden="true"><path d="M0 1H20M0 7H20M0 13H20" stroke="#17142A" stroke-width="2" fill="none"/></svg>
            </button>
        </div>
    </div>
</header>

{{-- قائمة الموبايل: درج جانبي بيفتح من ناحية بداية الصفحة (يمين في العربي) --}}
<div id="h-drawer" class="h-drawer" hidden>
    <div class="h-drawer-backdrop" data-menu-close></div>
    <div class="h-drawer-panel" role="dialog" aria-modal="true" aria-label="{{ $t('القائمة', 'Menu') }}">
        <div class="h-drawer-head">
            <a href="{{ route('home') }}" class="h-logo">
                @if($logoUrl)<img src="{{ $logoUrl }}" alt="" width="36" height="36">@else<span class="h-logo-mark" aria-hidden="true">M</span>@endif
                <span class="h-logo-text"><b>Mindlytics</b></span>
            </a>
            <button type="button" class="h-drawer-close" data-menu-close aria-label="{{ $t('إغلاق القائمة', 'Close menu') }}">
                <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M2 2l12 12M14 2L2 14" stroke="currentColor" stroke-width="2"/></svg>
            </button>
        </div>
        <nav class="h-drawer-nav" aria-label="{{ $t('القائمة الرئيسية', 'Main menu') }}">
            @foreach($navItems as $item)
                <a href="{{ $item['url'] }}" data-menu-close @if($item['on']) aria-current="page" @endif>
                    <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $item['icon'] }}" stroke="currentColor" stroke-width="1.6" fill="none" stroke-linejoin="round" stroke-linecap="round"/></svg>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>
        <div class="h-drawer-more">
            <a href="{{ route('public.certificates.verify') }}">{{ $t('التحقق من شهادة', 'Verify a certificate') }}</a>
            <a href="{{ route('public.contact') }}">{{ $t('تواصل معنا', 'Contact us') }}</a>
            <a href="{{ $otherLocaleUrl }}" hreflang="{{ $siteAr ? 'en' : 'ar' }}">{{ $siteAr ? 'English' : 'العربية' }}</a>
        </div>
        <div class="h-drawer-foot">
            <a href="{{ route('advising.show') }}" data-advising-open data-menu-close class="h-btn h-btn-accent h-btn-block">{{ $t('احجز استشارة مجانية', 'Book a free consultation') }}</a>
            @auth
                <a href="{{ route('dashboard') }}" class="h-btn h-btn-ghost-light h-btn-block">{{ $t('حسابي', 'My account') }}</a>
            @else
                <div class="h-drawer-auth">
                    <a href="{{ route('login') }}" class="h-btn h-btn-ghost-light">{{ $t('تسجيل الدخول', 'Sign in') }}</a>
                    <a href="{{ route('register') }}" class="h-btn h-btn-ghost-light">{{ $t('إنشاء حساب', 'Create account') }}</a>
                </div>
            @endauth
        </div>
    </div>
</div>
<script>
(function () {
    if (window.__hDrawer) { return; }
    window.__hDrawer = true;
    var drawer = document.getElementById('h-drawer');
    var toggle = document.querySelector('[data-menu-toggle]');
    if (! drawer || ! toggle) { return; }
    var timer;
    var setMenu = function (open) {
        clearTimeout(timer);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        document.documentElement.classList.toggle('h-drawer-lock', open);
        if (open) {
            drawer.hidden = false;
            void drawer.offsetWidth; // يثبّت الحالة المقفولة قبل الحركة
            drawer.classList.add('is-open');
            var first = drawer.querySelector('.h-drawer-close');
            if (first) { first.focus(); }
        } else if (! drawer.hidden) {
            drawer.classList.remove('is-open');
            timer = setTimeout(function () { drawer.hidden = true; }, 280);
            toggle.focus({ preventScroll: true });
        }
    };
    toggle.addEventListener('click', function () { setMenu(! drawer.classList.contains('is-open')); });
    drawer.addEventListener('click', function (e) { if (e.target.closest('[data-menu-close]')) { setMenu(false); } });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && drawer.classList.contains('is-open')) { setMenu(false); } });
    window.matchMedia('(min-width: 961px)').addEventListener('change', function (m) { if (m.matches) { setMenu(false); } });
})();
</script>
