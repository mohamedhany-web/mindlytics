{{--
    إطار صفحات الدخول وإنشاء الحساب واستعادة كلمة المرور (docs/SITE_DESIGN.md).
    الفورم في ناحية، وصورة الأكاديمية الحقيقية في الناحية التانية بنفس ألوان الأطراف بتاعة الرئيسية.
    الأقسام: title، auth_title، auth_subtitle، content (الفورم)، aside_title، aside_text.
--}}
@php
    $siteAr = app()->getLocale() === 'ar';
    $t = fn (string $ar, string $en) => $siteAr ? $ar : $en;
    $authPhoto = asset('images/home/classroom-1600.jpg');
    $authPhotoSm = asset('images/home/classroom-900.jpg');
    $logoUrl = $platformLogoUrl ?? \App\Support\SiteBranding::logoUrl();
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $siteAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, follow">
    <title>@yield('title') - {{ config('app.name') }}</title>
    @include('components.favicon-meta')
    <x-tracking-tags placement="head" />
    <link rel="preload" as="image" href="{{ $authPhoto }}" imagesrcset="{{ $authPhotoSm }} 900w, {{ $authPhoto }} 1600w" imagesizes="(max-width: 960px) 100vw, 45vw">
    @include('components.site.styles')
    <style>
        .a-shell { min-height: 100vh; display: flex; flex-wrap: wrap; background: #FFFFFF; }
        .a-main { flex: 1 1 560px; min-width: 0; display: flex; flex-direction: column; }
        .a-top { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 20px 40px; }
        .a-top-links { display: flex; gap: 18px; font-size: 14px; }
        .a-top-links a { color: var(--h-muted); text-decoration: none; }
        .a-top-links a:hover { color: var(--h-ink); text-decoration: underline; }
        .a-body { flex: 1; display: flex; align-items: center; justify-content: center; padding: 24px 40px 56px; }
        .a-form { width: 100%; max-width: 460px; display: flex; flex-direction: column; gap: 18px; }
        .a-form h1 { margin: 0; font-family: var(--h-display); font-size: 30px; line-height: 1.45; }
        .a-form .a-sub { margin: -10px 0 4px; color: var(--h-text-2); }
        .a-row { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; font-size: 14px; }
        .a-check { display: inline-flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 500; cursor: pointer; }
        .a-check input { width: 18px; height: 18px; accent-color: var(--h-brand); }
        .a-pass { position: relative; }
        .a-pass input { padding-inline-end: 52px; }
        .a-pass button { position: absolute; inset-inline-end: 0; top: 0; height: 48px; min-width: 48px; border: 0; background: transparent; color: var(--h-brand); font: inherit; font-size: 13px; font-weight: 600; cursor: pointer; padding: 0 12px; }
        .a-phone { display: flex; border: 1px solid var(--h-field); }
        .a-phone:focus-within { border-color: var(--h-brand); box-shadow: 0 0 0 3px rgba(63, 51, 86, .15); }
        .a-phone select, .a-phone input { border: 0 !important; box-shadow: none !important; height: 46px !important; }
        .a-phone select { flex: 0 0 auto; width: 132px; border-inline-end: 1px solid var(--h-field) !important; padding: 0 8px; }
        .a-note { border: 1px solid var(--h-line); background: var(--h-mist); padding: 12px 14px; font-size: 14px; }
        .a-note-warn { border-color: #F1D49A; background: #FFF6E5; color: #6B4100; }
        .a-alert { padding: 12px 14px; font-size: 14px; border: 1px solid; }
        .a-alert-ok { background: #E8F4EE; border-color: #B9DCC9; color: #0E4F33; }
        .a-alert-err { background: #FDECEA; border-color: #F2C9C4; color: #7A271A; }
        .a-switch { text-align: center; padding-top: 18px; border-top: 1px solid var(--h-line); font-size: 15px; color: var(--h-text-2); }
        .a-codes summary { cursor: pointer; font-size: 14px; font-weight: 600; color: var(--h-brand); list-style: none; }
        .a-codes summary::-webkit-details-marker { display: none; }
        .a-codes summary::before { content: '+ '; }
        .a-codes[open] summary::before { content: '− '; }
        .a-codes-body { display: grid; gap: 14px; margin-top: 14px; }
        .a-honeypot { position: absolute !important; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }

        /* الصورة: نفس تداخل الألوان على الأطراف زي الرئيسية */
        .a-photo { flex: 1 1 440px; min-width: 0; margin: 0; position: relative; overflow: hidden; isolation: isolate; background: var(--h-ink); color: #FFFFFF; display: flex; align-items: flex-end; min-height: 100vh; }
        .a-photo img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: -2; }
        .a-photo::before { content: ''; position: absolute; inset: 0; z-index: -1;
            background: linear-gradient(to top, var(--h-ink) 0%, rgba(23, 20, 42, .82) 26%, rgba(23, 20, 42, .2) 55%, rgba(23, 20, 42, 0) 70%),
                        linear-gradient(to {{ $siteAr ? 'left' : 'right' }}, rgba(23, 20, 42, .55) 0%, rgba(23, 20, 42, 0) 22%); }
        .a-photo::after { content: ''; position: absolute; inset: 0; z-index: -1; pointer-events: none;
            background: radial-gradient(50% 40% at {{ $siteAr ? '0%' : '100%' }} 0%, rgba(126, 104, 214, .5) 0%, rgba(126, 104, 214, 0) 72%),
                        radial-gradient(45% 35% at {{ $siteAr ? '0%' : '100%' }} 100%, rgba(242, 182, 48, .35) 0%, rgba(242, 182, 48, 0) 70%),
                        radial-gradient(40% 30% at {{ $siteAr ? '100%' : '0%' }} 20%, rgba(126, 104, 214, .32) 0%, rgba(126, 104, 214, 0) 70%); }
        .a-photo-copy { padding: 48px; max-width: 560px; display: flex; flex-direction: column; gap: 12px; }
        .a-photo-copy h2 { margin: 0; font-family: var(--h-display); font-size: 28px; line-height: 1.45; color: #FFFFFF; }
        .a-photo-copy p { margin: 0; color: var(--h-on-dark); }
        .a-photo-copy ul { list-style: none; margin: 8px 0 0; padding: 0; display: flex; flex-wrap: wrap; gap: 8px 22px; font-size: 14px; color: var(--h-on-dark-2); }
        .a-photo-copy li { display: flex; align-items: center; gap: 8px; }
        .a-photo-copy li::before { content: ''; width: 6px; height: 6px; background: var(--h-accent); }

        @media (max-width: 960px) {
            .a-shell { flex-direction: column-reverse; flex-wrap: nowrap; }
            .a-photo { flex: none; min-height: 0; height: 168px; }
            .a-photo::before { background: linear-gradient(to top, rgba(23, 20, 42, .7) 0%, rgba(23, 20, 42, .1) 60%); }
            .a-photo-copy { display: none; }
            .a-top { padding: 16px; }
            .a-body { padding: 8px 16px 40px; align-items: flex-start; }
            .a-form h1 { font-size: 25px; }
        }
    </style>
    @stack('styles')
</head>
<body class="h-site">
    <x-tracking-tags placement="body" />
    <div class="a-shell">
        <main class="a-main">
            <div class="a-top">
                <a href="{{ route('home') }}" class="h-logo" aria-label="Mindlytics — {{ $t('الرئيسية', 'Home') }}">
                    @if($logoUrl)<img src="{{ $logoUrl }}" alt="" width="38" height="38" style="height: 38px">@else<span class="h-logo-mark" aria-hidden="true">M</span>@endif
                    <span class="h-logo-text"><b>Mindlytics</b></span>
                </a>
                <div class="a-top-links">
                    <a href="{{ request()->fullUrlWithQuery(['lang' => $siteAr ? 'en' : 'ar']) }}">{{ $siteAr ? 'English' : 'العربية' }}</a>
                    <a href="{{ route('home') }}">{{ $t('العودة للموقع', 'Back to site') }}</a>
                </div>
            </div>
            <div class="a-body">
                <div class="a-form">
                    @yield('content')
                </div>
            </div>
        </main>
        <figure class="a-photo" aria-hidden="true">
            <img src="{{ $authPhoto }}" srcset="{{ $authPhotoSm }} 900w, {{ $authPhoto }} 1600w" sizes="(max-width: 960px) 100vw, 45vw" alt="">
            <div class="a-photo-copy">
                <h2>@yield('aside_title', $t('تعلّم مع محاضرين من السوق، وفي دفعة حقيقية', 'Learn with industry instructors, in a real cohort'))</h2>
                <p>@yield('aside_text', $t('محاضرات مباشرة، ومتابعة لتقدّمك، ومشاريع تطبيقية، وشهادة يمكن التحقق منها.', 'Live sessions, progress tracking, real projects and a verifiable certificate.'))</p>
                <ul>
                    <li>{{ $t('أونلاين مباشر أو حضوري', 'Live online or on campus') }}</li>
                    <li>{{ $t('شهادة برقم تحقق', 'Verifiable certificate') }}</li>
                </ul>
            </div>
        </figure>
    </div>
    <script>
        // إظهار/إخفاء كلمة المرور
        document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.dataset.togglePassword);
                if (! input) { return; }
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.textContent = show ? btn.dataset.hide : btn.dataset.show;
                btn.setAttribute('aria-pressed', show ? 'true' : 'false');
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
