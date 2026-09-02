@extends('layouts.student-dashboard')

@php
    $totalBalance = isset($wallets) ? $wallets->sum(fn ($w) => (float) ($w->balance ?? 0)) : 0;
    $walletCount = $wallets->count() ?? 0;
    $couponCount = isset($discountCoupons) ? $discountCoupons->count() : 0;
    $transactionCount = isset($transactions) ? $transactions->total() : 0;
@endphp

@section('title', __('student.wallet_title'))
@section('header', __('student.wallet_title'))

@push('styles')
<style>
    .sp-wallet-hero {
        background: #2f2e43;
        border-radius: 30px;
        color: #fff;
        padding: 28px 24px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--sp-shadow);
    }
    .sp-wallet-hero::before {
        content: '';
        position: absolute;
        inset-inline-end: -40px;
        top: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(174,217,234,0.28), transparent 70%);
        pointer-events: none;
    }
    .sp-wallet-input {
        width: 100%;
        border-radius: 30px;
        border: 1px solid rgba(0,0,0,0.06);
        background: #f7f7f5;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--sp-text);
    }
    .sp-wallet-input:focus {
        outline: none;
        ring: 2px;
        box-shadow: 0 0 0 2px var(--sp-accent);
    }
</style>
@endpush

@section('content')
<div class="space-y-5">
    @if(session('success'))
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold" style="background:var(--sp-mint);color:var(--sp-accent-text)">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold bg-[#f9e4d7] text-[#7a3b2e]">
            <ul class="space-y-1 m-0 p-0 list-none">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Hero only — no duplicate stat cards below --}}
    <section class="sp-wallet-hero">
        <div class="relative z-[1] flex flex-col lg:flex-row lg:items-end gap-6 lg:gap-8">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white/60 m-0 mb-2">{{ __('student.wallet_index_eyebrow') }}</p>
                <h2 class="text-2xl sm:text-[28px] font-extrabold m-0 leading-tight">{{ __('student.wallet_title') }}</h2>
                <p class="text-sm text-white/70 m-0 mt-3 max-w-2xl leading-relaxed">{{ __('student.wallet_subtitle') }}</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 shrink-0 w-full lg:w-auto lg:min-w-[320px]">
                <div class="rounded-2xl bg-white/8 px-3 py-3 sm:px-4 border border-white/10 text-center sm:col-span-2">
                    <p class="text-xl sm:text-2xl font-black text-[var(--sp-accent)] m-0">{{ number_format($totalBalance, 2) }}</p>
                    <p class="text-[9px] sm:text-[10px] font-bold text-white/50 m-0 mt-1 uppercase tracking-wide">{{ __('student.wallet_total_balance') }}</p>
                </div>
                <div class="rounded-2xl bg-white/8 px-3 py-3 sm:px-4 border border-white/10 text-center">
                    <p class="text-xl sm:text-2xl font-black text-[var(--sp-accent)] m-0">{{ $walletCount }}</p>
                    <p class="text-[9px] sm:text-[10px] font-bold text-white/50 m-0 mt-1 uppercase tracking-wide">{{ __('student.wallet_count_label') }}</p>
                </div>
                <div class="rounded-2xl bg-white/8 px-3 py-3 sm:px-4 border border-white/10 text-center">
                    <p class="text-xl sm:text-2xl font-black text-[var(--sp-accent)] m-0">{{ $couponCount }}</p>
                    <p class="text-[9px] sm:text-[10px] font-bold text-white/50 m-0 mt-1 uppercase tracking-wide">{{ __('student.wallet_coupons_label') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Wallets --}}
    <section class="sp-card overflow-hidden">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-black/5 bg-[#f7f7f5]">
            <span class="sp-icon-bubble shrink-0" style="background:var(--sp-mint)">
                <x-student.figma-icon name="icon-wallet.svg" />
            </span>
            <div>
                <h3 class="font-extrabold text-base m-0">{{ __('student.wallet_title') }}</h3>
                <p class="text-xs text-[var(--sp-muted)] m-0 mt-0.5">{{ __('student.wallet_balances_hint') }}</p>
            </div>
        </div>

        <div class="p-4 sm:p-5 space-y-4">
            @if($walletCount > 0)
                <div class="space-y-2">
                    @foreach($wallets as $wallet)
                        <div class="sp-process-row !shadow-none border border-[#f0f0ec]">
                            <span class="sp-icon-bubble shrink-0 !w-10 !h-10" style="background:var(--sp-sky)">
                                <x-student.figma-icon name="icon-wallet.svg" box="size-5" />
                            </span>
                            <span class="flex-1 min-w-0">
                                <span class="block font-extrabold text-sm truncate">{{ $wallet->name ?: __('student.wallet_default_name', ['id' => $wallet->id]) }}</span>
                                @if(($wallet->pending_balance ?? 0) > 0)
                                    <span class="block text-xs font-bold text-[var(--sp-muted)] mt-0.5">{{ __('student.wallet_pending_balance', ['amount' => number_format($wallet->pending_balance, 2)]) }}</span>
                                @endif
                            </span>
                            <span class="text-base sm:text-lg font-black text-[var(--sp-accent-text)] shrink-0">
                                {{ number_format($wallet->balance ?? 0, 2) }} <span class="text-xs font-bold text-[var(--sp-muted)]">{{ __('public.currency_egp') }}</span>
                            </span>
                        </div>
                    @endforeach
                </div>

                @if($walletCount > 1)
                    <div class="pt-4 border-t border-black/5 space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="sp-icon-bubble shrink-0" style="background:var(--sp-lilac)">
                                <x-student.figma-icon name="icon-trend.svg" box="size-5" />
                            </span>
                            <h4 class="font-extrabold text-base m-0">{{ __('student.wallet_transfer_title') }}</h4>
                        </div>
                        <form action="{{ route('student.wallet.transfer') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @csrf
                            <div>
                                <label for="from_wallet_id" class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.wallet_transfer_from') }}</label>
                                <select id="from_wallet_id" name="from_wallet_id" class="sp-wallet-input" required>
                                    <option value="">{{ __('student.wallet_transfer_select_wallet') }}</option>
                                    @foreach($wallets as $wallet)
                                        <option value="{{ $wallet->id }}" @selected(old('from_wallet_id') == $wallet->id)>
                                            {{ $wallet->name ?: __('student.wallet_default_name', ['id' => $wallet->id]) }} — {{ number_format($wallet->balance ?? 0, 2) }} {{ __('public.currency_egp') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="to_wallet_id" class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.wallet_transfer_to') }}</label>
                                <select id="to_wallet_id" name="to_wallet_id" class="sp-wallet-input" required>
                                    <option value="">{{ __('student.wallet_transfer_select_wallet') }}</option>
                                    @foreach($wallets as $wallet)
                                        <option value="{{ $wallet->id }}" @selected(old('to_wallet_id') == $wallet->id)>
                                            {{ $wallet->name ?: __('student.wallet_default_name', ['id' => $wallet->id]) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="amount" class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.amount_label') }}</label>
                                <input id="amount" name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount') }}" class="sp-wallet-input" required>
                            </div>
                            <div>
                                <label for="notes" class="block text-xs font-bold text-[var(--sp-muted)] mb-1.5 uppercase tracking-wide">{{ __('student.your_notes') }}</label>
                                <input id="notes" name="notes" type="text" value="{{ old('notes') }}" maxlength="500" class="sp-wallet-input">
                            </div>
                            <div class="md:col-span-2">
                                <button type="submit" class="sp-promo-btn !mt-0 !text-[var(--sp-accent-text)]">
                                    {{ __('student.wallet_transfer_button') }}
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="rounded-[16px] px-4 py-3 text-sm font-bold" style="background:var(--sp-amber-soft);color:var(--sp-accent-text)">
                        {{ __('student.wallet_transfer_need_two_wallets') }}
                    </div>
                @endif
            @else
                <div class="rounded-[16px] bg-[#f7f7f5] px-4 py-6 text-center text-sm font-bold text-[var(--sp-muted)]">
                    {{ __('student.no_wallet_message') }}
                </div>
            @endif
        </div>
    </section>

    {{-- Discounts --}}
    <section class="sp-card overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-black/5 bg-[#f7f7f5]">
            <div class="flex items-center gap-3 min-w-0">
                <span class="sp-icon-bubble shrink-0" style="background:var(--sp-peach)">
                    <x-student.figma-icon name="icon-star.svg" />
                </span>
                <div>
                    <h3 class="font-extrabold text-base m-0">{{ __('student.wallet_discounts_title') }}</h3>
                    <p class="text-xs text-[var(--sp-muted)] m-0 mt-0.5">{{ __('student.wallet_discounts_subtitle') }}</p>
                </div>
            </div>
            @if($couponCount > 0)
                <span class="sp-pill sp-pill--progress shrink-0">{{ __('student.wallet_discounts_count', ['count' => $couponCount]) }}</span>
            @endif
        </div>

        @if($couponCount > 0)
            <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($discountCoupons as $coupon)
                    <article x-data="{ copied: false }" class="sp-card p-4 border border-[#f0f0ec] !shadow-none">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="min-w-0">
                                <p class="font-extrabold text-sm m-0 truncate">{{ $coupon->title ?: $coupon->name }}</p>
                                @if($coupon->description)
                                    <p class="text-xs text-[var(--sp-muted)] m-0 mt-1 line-clamp-2">{{ $coupon->description }}</p>
                                @endif
                            </div>
                            <span class="sp-pill sp-pill--done shrink-0">
                                @if($coupon->discount_type === 'percentage')
                                    {{ (int) $coupon->discount_value }}%
                                @else
                                    {{ number_format($coupon->discount_value, 0) }} {{ __('public.currency_egp') }}
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <code class="flex-1 min-w-0 truncate rounded-[14px] bg-[#f7f7f5] border border-black/5 px-3 py-2.5 text-sm font-extrabold tracking-wider text-[var(--sp-accent-text)]" x-ref="code{{ $coupon->id }}">{{ $coupon->code }}</code>
                            <button type="button"
                                    @click="navigator.clipboard.writeText($refs.code{{ $coupon->id }}.innerText.trim()); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="inline-flex items-center justify-center rounded-[14px] px-3 py-2.5 text-xs font-extrabold shrink-0 transition-colors"
                                    style="background:var(--sp-accent);color:var(--sp-accent-text)"
                                    :aria-label="copied ? @js(__('student.wallet_coupon_copied')) : @js(__('student.wallet_coupon_copy'))">
                                <span x-text="copied ? @js(__('student.wallet_coupon_copied')) : @js(__('student.wallet_coupon_copy'))"></span>
                            </button>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-xs font-bold">
                            <span class="text-[var(--sp-muted)]">
                                @if($coupon->expires_at)
                                    {{ __('student.wallet_coupon_expires', ['date' => $coupon->expires_at->format('Y/m/d')]) }}
                                @else
                                    {{ __('student.wallet_coupon_no_expiry') }}
                                @endif
                            </span>
                            <a href="{{ route('public.courses') }}" class="sp-link text-xs font-extrabold">
                                {{ __('student.wallet_coupon_use_now') }}
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="px-4 sm:px-5 pb-5">
                <p class="text-xs text-[var(--sp-muted)] m-0 leading-relaxed font-bold">{{ __('student.wallet_discounts_auto_hint') }}</p>
            </div>
        @else
            <div class="p-8 sm:p-10 text-center">
                <span class="sp-icon-bubble mx-auto mb-3 !w-14 !h-14" style="background:var(--sp-peach)">
                    <x-student.figma-icon name="icon-star.svg" box="size-6" />
                </span>
                <p class="text-sm font-extrabold m-0">{{ __('student.wallet_no_discounts') }}</p>
                <a href="{{ route('public.customer-survey.show') }}" class="sp-promo-btn !mt-4 inline-flex !text-[var(--sp-accent-text)]">
                    {{ __('student.wallet_survey_cta') }}
                </a>
            </div>
        @endif
    </section>

    {{-- Transactions --}}
    <section class="sp-card overflow-hidden">
        <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-black/5 bg-[#f7f7f5]">
            <div class="flex items-center gap-3 min-w-0">
                <span class="sp-icon-bubble shrink-0" style="background:var(--sp-lilac)">
                    <x-student.figma-icon name="icon-orders.svg" />
                </span>
                <div>
                    <h3 class="font-extrabold text-base m-0">{{ __('student.transactions_log') }}</h3>
                    @if($transactionCount > 0)
                        <p class="text-xs text-[var(--sp-muted)] m-0 mt-0.5">{{ __('student.wallet_transactions_count', ['count' => $transactionCount]) }}</p>
                    @endif
                </div>
            </div>
        </div>

        @if(isset($transactions) && $transactions->count() > 0)
            <div class="divide-y divide-black/5">
                @foreach($transactions as $transaction)
                    @php
                        $isDeposit = $transaction->type === 'deposit' || $transaction->type === 'إيداع';
                    @endphp
                    <div class="flex items-center justify-between gap-4 px-4 sm:px-5 py-4 hover:bg-[#fafaf8] transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="sp-icon-bubble shrink-0 !w-9 !h-9" style="background:{{ $isDeposit ? 'var(--sp-mint)' : 'var(--sp-peach)' }}">
                                <x-student.figma-icon name="{{ $isDeposit ? 'icon-trend.svg' : 'icon-wallet.svg' }}" box="size-4" />
                            </span>
                            <div class="min-w-0">
                                <p class="font-extrabold text-sm m-0 truncate">{{ $transaction->description ?? __('student.transaction_default') }}</p>
                                <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mt-0.5">{{ $transaction->created_at?->format('Y/m/d H:i') ?? '—' }}</p>
                            </div>
                        </div>
                        <p class="text-base font-black shrink-0 {{ $isDeposit ? 'text-[var(--sp-accent-text)]' : 'text-[#7a3b2e]' }}">
                            {{ $isDeposit ? '+' : '−' }}{{ number_format($transaction->amount ?? 0, 2) }} <span class="text-xs font-bold">{{ __('public.currency_egp') }}</span>
                        </p>
                    </div>
                @endforeach
            </div>
            @if($transactions->hasPages())
                <div class="p-4 border-t border-black/5 flex justify-center">{{ $transactions->links() }}</div>
            @endif
        @else
            <div class="p-8 sm:p-10 text-center">
                <span class="sp-icon-bubble mx-auto mb-3 !w-14 !h-14" style="background:var(--sp-lilac)">
                    <x-student.figma-icon name="icon-orders.svg" box="size-6" />
                </span>
                <p class="text-sm font-extrabold m-0 text-[var(--sp-muted)]">{{ __('student.no_transactions') }}</p>
            </div>
        @endif
    </section>
</div>
@endsection
