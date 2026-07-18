@php
    $locale = app()->getLocale();
    $urlAr = request()->fullUrlWithQuery(['lang' => 'ar']);
    $urlEn = request()->fullUrlWithQuery(['lang' => 'en']);
    $currentLabel = $locale === 'ar'
        ? __('landing.language_switcher.ar')
        : __('landing.language_switcher.en');
@endphp
<div class="relative inline-flex {{ $attributes->get('class') }}" x-data="{ open: false }" @keydown.escape.window="open = false">
    <button type="button"
            @click="open = !open"
            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 border border-gray-200 bg-white transition-colors"
            :aria-expanded="open.toString()"
            aria-haspopup="listbox"
            aria-label="{{ __('landing.language_switcher.ar') }} / {{ __('landing.language_switcher.en') }}">
        <span>{{ $currentLabel }}</span>
        <i class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform" :class="{ 'rotate-180': open }"></i>
    </button>
    <div x-show="open"
         x-cloak
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         class="absolute top-full mt-1 end-0 z-50 min-w-[9rem] rounded-lg border border-gray-200 bg-white py-1 shadow-lg"
         role="listbox">
        <a href="{{ $urlAr }}"
           role="option"
           @click="open = false"
           class="block px-3 py-2 text-sm {{ $locale === 'ar' ? 'bg-gray-100 font-semibold text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}"
           @if($locale === 'ar') aria-selected="true" @endif>
            {{ __('landing.language_switcher.ar') }}
        </a>
        <a href="{{ $urlEn }}"
           role="option"
           @click="open = false"
           class="block px-3 py-2 text-sm {{ $locale === 'en' ? 'bg-gray-100 font-semibold text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}"
           @if($locale === 'en') aria-selected="true" @endif>
            {{ __('landing.language_switcher.en') }}
        </a>
    </div>
</div>
