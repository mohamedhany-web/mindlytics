{{-- فوتر الموقع العام: بيانات التواصل والسوشيال من إعدادات النظام (PlatformSettings::contactPage). docs/SITE_DESIGN.md --}}
@php
    $siteAr = app()->getLocale() === 'ar';
    $t = fn (string $ar, string $en) => $siteAr ? $ar : $en;
    $contact = $platformContact ?? \App\Support\PlatformSettings::contactPage();
    $wa = preg_replace('/\D+/', '', (string) ($contact['whatsapp'] ?? ''));
    $socials = array_filter([
        'Facebook' => $contact['facebook'] ?? '',
        'Instagram' => $contact['instagram'] ?? '',
        'LinkedIn' => $contact['linkedin'] ?? '',
        'YouTube' => $contact['youtube'] ?? '',
        'TikTok' => $contact['tiktok'] ?? '',
    ]);
@endphp
<footer class="h-footer">
    <div class="h-wrap">
        <div class="h-footer-grid">
            <div class="h-footer-col">
                <b class="h-kufi" style="font-size: 19px">Mindlytics</b>
                <span>{{ $t('أكاديمية متخصصة في البرمجة وتطوير البرمجيات.', 'A specialized software development academy.') }}</span>
                @if(filled($contact['address'] ?? ''))<span>{{ $contact['address'] }}</span>@endif
                @if(filled($contact['phone'] ?? ''))<a href="tel:{{ preg_replace('/[^\d+]/', '', $contact['phone']) }}" dir="ltr" style="text-align: start">{{ $contact['phone'] }}</a>@endif
                @if(filled($contact['email'] ?? ''))<a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a>@endif
                @if($socials !== [])
                    <span style="display: flex; flex-wrap: wrap; gap: 14px; margin-top: 6px">
                        @foreach($socials as $label => $url)<a href="{{ $url }}" target="_blank" rel="noopener noreferrer">{{ $label }}</a>@endforeach
                    </span>
                @endif
            </div>
            <nav aria-label="{{ $t('البرامج', 'Programs') }}">
                <b>{{ $t('البرامج', 'Programs') }}</b>
                <a href="{{ route('public.learning-paths.index') }}">{{ $t('المسارات التعليمية', 'Learning paths') }}</a>
                <a href="{{ route('public.courses') }}">{{ $t('الكورسات', 'Courses') }}</a>
                <a href="{{ route('home') }}#intakes">{{ $t('الدفعات القادمة', 'Upcoming intakes') }}</a>
                <a href="{{ route('public.instructors.index') }}">{{ $t('المحاضرون', 'Instructors') }}</a>
            </nav>
            <nav aria-label="{{ $t('الطلاب', 'Students') }}">
                <b>{{ $t('الطلاب', 'Students') }}</b>
                <a href="{{ route('login') }}">{{ $t('تسجيل الدخول', 'Sign in') }}</a>
                <a href="{{ route('public.certificates.verify') }}">{{ $t('التحقق من شهادة', 'Verify a certificate') }}</a>
                <a href="{{ route('public.portfolio.index') }}">{{ $t('أعمال الطلاب', 'Student work') }}</a>
                <a href="{{ route('public.faq') }}">{{ $t('الأسئلة الشائعة', 'FAQ') }}</a>
            </nav>
            <nav aria-label="Mindlytics">
                <b>Mindlytics</b>
                <a href="{{ route('public.about') }}">{{ $t('عن الأكاديمية', 'About') }}</a>
                <a href="{{ route('careers.index') }}">{{ $t('الوظائف', 'Careers') }}</a>
                <a href="{{ route('public.internships.index') }}">{{ $t('التدريب الصيفي', 'Internships') }}</a>
                <a href="{{ route('public.blog.index') }}">{{ $t('المدونة', 'Blog') }}</a>
                <a href="{{ route('public.contact') }}">{{ $t('تواصل معنا', 'Contact us') }}</a>
            </nav>
        </div>
        <div class="h-footer-bottom">
            <span>© {{ date('Y') }} Mindlytics</span>
            <span style="display: flex; flex-wrap: wrap; gap: 16px">
                <a href="{{ route('public.terms') }}">{{ $t('الشروط والأحكام', 'Terms') }}</a>
                <a href="{{ route('public.privacy') }}">{{ $t('سياسة الخصوصية', 'Privacy') }}</a>
                <a href="{{ route('public.refund') }}">{{ $t('سياسة الاسترداد', 'Refund policy') }}</a>
            </span>
        </div>
    </div>
</footer>

@if(! ($siteHideMobileBar ?? false))
    <div class="h-mobile-bar">
        <a href="{{ route('home') }}#consult" class="h-btn h-btn-primary">{{ $t('احجز استشارة مجانية', 'Free consultation') }}</a>
        @if($wa !== '')
            <a href="https://wa.me/{{ $wa }}" class="h-btn h-btn-outline" target="_blank" rel="noopener noreferrer">{{ $t('واتساب', 'WhatsApp') }}</a>
        @endif
    </div>
@endif

<script>
(function () {
    var toggle = document.querySelector('[data-menu-toggle]');
    var menu = document.getElementById('h-mobile-menu');
    if (! toggle || ! menu) { return; }
    var setMenu = function (open) {
        menu.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        toggle.setAttribute('aria-label', open ? toggle.dataset.labelClose : toggle.dataset.labelOpen);
    };
    toggle.addEventListener('click', function () { setMenu(! menu.classList.contains('is-open')); });
    menu.querySelectorAll('[data-menu-close]').forEach(function (a) { a.addEventListener('click', function () { setMenu(false); }); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { setMenu(false); } });
})();
</script>
