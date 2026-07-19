@php
    $locale = app()->getLocale();
    $currentUrl = request()->fullUrlWithQuery([]);
    $urlAr = request()->fullUrlWithQuery(['lang' => 'ar']);
    $urlEn = request()->fullUrlWithQuery(['lang' => 'en']);
@endphp
<div class="inline-flex items-center gap-1 {{ $attributes->get('class') }}" dir="ltr">
    <a href="{{ $urlAr }}" class="px-2 py-1 rounded text-sm font-medium {{ $locale === 'ar' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">{{ __('landing.language_switcher.ar') }}</a>
    <span class="text-gray-400">|</span>
    <a href="{{ $urlEn }}" class="px-2 py-1 rounded text-sm font-medium {{ $locale === 'en' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">{{ __('landing.language_switcher.en') }}</a>
</div>
