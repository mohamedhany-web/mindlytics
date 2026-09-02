@props([
    'title',
    'whatsappUrl',
    'payUrl' => null,
    'bookUrl' => null,
    'fawaterakEnabled' => false,
    'priceLabel' => null,
    'hint' => null,
])

@php
    $payUrl = $payUrl ?: $bookUrl;
@endphp

<div class="ay-buy" x-data="{ open: false }" @keydown.escape.window="open = false">
    <button type="button" class="sp-promo-btn !mt-0 !py-2.5 !px-4 !text-sm w-full sm:w-auto" @click="open = true">
        {{ __('student.ay_buy_or_book') }}
    </button>

    <div x-show="open" x-cloak class="ay-buy-overlay" @click.self="open = false">
        <div class="ay-buy-sheet sp-card" role="dialog" aria-modal="true" @click.stop>
            <div class="flex items-start justify-between gap-3 mb-4">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.ay_choose_pay_method') }}</p>
                    <h3 class="text-lg font-extrabold m-0 truncate">{{ $title }}</h3>
                    @if($priceLabel)
                        <p class="text-sm font-bold text-[var(--sp-accent-text)] m-0 mt-1">{{ $priceLabel }}</p>
                    @endif
                </div>
                <button type="button" class="ay-buy-close" @click="open = false" aria-label="{{ __('common.close') }}">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            @if($hint)
                <p class="text-sm text-[var(--sp-muted)] m-0 mb-4">{{ $hint }}</p>
            @endif

            <div class="grid gap-3">
                @if($payUrl)
                    <a href="{{ $payUrl }}" class="ay-buy-option ay-buy-option--pay">
                        <span class="sp-icon-bubble" style="background:var(--sp-accent)">
                            <x-student.figma-icon name="icon-wallet.svg" box="size-5" />
                        </span>
                        <span class="min-w-0 flex-1 text-start">
                            <span class="block font-extrabold text-[var(--sp-accent-text)]">{{ __('student.ay_pay_online') }}</span>
                            <span class="block text-xs text-[var(--sp-muted)] mt-0.5">
                                {{ $fawaterakEnabled ? __('student.ay_pay_online_fawaterak_hint') : __('student.ay_pay_online_hint') }}
                            </span>
                        </span>
                        <i class="fas fa-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} text-[var(--sp-muted)]"></i>
                    </a>
                @endif

                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="ay-buy-option ay-buy-option--wa">
                    <span class="sp-icon-bubble" style="background:#dcfce7">
                        <i class="fab fa-whatsapp text-emerald-600 text-lg"></i>
                    </span>
                    <span class="min-w-0 flex-1 text-start">
                        <span class="block font-extrabold text-[var(--sp-text)]">{{ __('student.ay_pay_whatsapp') }}</span>
                        <span class="block text-xs text-[var(--sp-muted)] mt-0.5">{{ __('student.ay_pay_whatsapp_hint') }}</span>
                    </span>
                    <i class="fas fa-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} text-[var(--sp-muted)]"></i>
                </a>
            </div>
        </div>
    </div>
</div>
