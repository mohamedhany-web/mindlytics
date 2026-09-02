@php
    $locale = app()->getLocale();
    $urlAr = request()->fullUrlWithQuery(['lang' => 'ar']);
    $urlEn = request()->fullUrlWithQuery(['lang' => 'en']);
@endphp
<div {{ $attributes->class(['sp-lang']) }} role="group" aria-label="{{ __('student.language') }}">
    <a href="{{ $urlAr }}"
       class="sp-lang-btn {{ $locale === 'ar' ? 'is-active' : '' }}"
       hreflang="ar"
       @if($locale === 'ar') aria-current="true" @endif>
        {{ __('landing.language_switcher.ar') }}
    </a>
    <a href="{{ $urlEn }}"
       class="sp-lang-btn {{ $locale === 'en' ? 'is-active' : '' }}"
       hreflang="en"
       @if($locale === 'en') aria-current="true" @endif>
        {{ __('landing.language_switcher.en') }}
    </a>
</div>
