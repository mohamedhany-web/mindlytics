@extends('layouts.app')

@section('title', __('student.wallet_title'))
@section('header', __('student.wallet_title'))

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    @if(session('success'))
    <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 rounded-xl border border-red-200 bg-red-50 text-red-700 text-sm">
        <ul class="space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4">{{ __('student.wallet_title') }}</h1>
            @if(isset($wallets) && $wallets->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($wallets as $wallet)
                <div class="flex items-center justify-between gap-4 p-4 sm:p-5 bg-sky-50 rounded-xl border border-sky-100">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">{{ $wallet->name ?: ('#' . $wallet->id) }}</p>
                        <p class="text-2xl sm:text-3xl font-bold text-sky-600">{{ number_format($wallet->balance ?? 0, 2) }} {{ __('public.currency_egp') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                </div>
                @endforeach
            </div>
            @if($wallets->count() > 1)
            <div class="mt-6 border-t border-gray-100 pt-6">
                <h2 class="text-base font-bold text-gray-900 mb-4">{{ __('student.wallet_transfer_title') }}</h2>
                <form action="{{ route('student.wallet.transfer') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label for="from_wallet_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('student.wallet_transfer_from') }}</label>
                        <select id="from_wallet_id" name="from_wallet_id" class="w-full rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500" required>
                            <option value="">{{ __('student.wallet_transfer_select_wallet') }}</option>
                            @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}" @selected(old('from_wallet_id') == $wallet->id)>
                                {{ $wallet->name ?: ('#' . $wallet->id) }} - {{ number_format($wallet->balance ?? 0, 2) }} {{ __('public.currency_egp') }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="to_wallet_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('student.wallet_transfer_to') }}</label>
                        <select id="to_wallet_id" name="to_wallet_id" class="w-full rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500" required>
                            <option value="">{{ __('student.wallet_transfer_select_wallet') }}</option>
                            @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}" @selected(old('to_wallet_id') == $wallet->id)>
                                {{ $wallet->name ?: ('#' . $wallet->id) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">{{ __('student.amount_label') }}</label>
                        <input id="amount" name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount') }}" class="w-full rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500" required>
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('student.your_notes') }}</label>
                        <input id="notes" name="notes" type="text" value="{{ old('notes') }}" maxlength="500" class="w-full rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg px-4 py-2.5 bg-sky-600 text-white font-semibold hover:bg-sky-700 transition-colors">
                            <i class="fas fa-right-left mr-2"></i>
                            {{ __('student.wallet_transfer_button') }}
                        </button>
                    </div>
                </form>
            </div>
            @else
            <div class="mt-6 p-4 rounded-xl border border-amber-200 bg-amber-50 text-amber-700 text-sm">
                {{ __('student.wallet_transfer_need_two_wallets') }}
            </div>
            @endif
            @else
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 text-gray-600 text-sm">{{ __('student.no_wallet_message') }}</div>
            @endif
        </div>
    </div>

    {{-- خصوماتي: كوبونات شخصية (استبيان العملاء، الإحالات، الورش) --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-tags text-emerald-600"></i>
                خصوماتي
            </h2>
            @if(isset($discountCoupons) && $discountCoupons->count() > 0)
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                    {{ $discountCoupons->count() }} خصم متاح
                </span>
            @endif
        </div>

        @if(isset($discountCoupons) && $discountCoupons->count() > 0)
        <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($discountCoupons as $coupon)
            <div x-data="{ copied: false }" class="rounded-xl border-2 border-dashed border-emerald-300 bg-gradient-to-br from-emerald-50 to-sky-50 p-4">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 text-sm truncate">{{ $coupon->title ?: $coupon->name }}</p>
                        @if($coupon->description)
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $coupon->description }}</p>
                        @endif
                    </div>
                    <span class="flex-shrink-0 text-lg font-black text-emerald-700">
                        @if($coupon->discount_type === 'percentage')
                            {{ (int) $coupon->discount_value }}%
                        @else
                            {{ number_format($coupon->discount_value, 0) }} {{ __('public.currency_egp') }}
                        @endif
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <code class="flex-1 min-w-0 truncate bg-white border border-emerald-200 rounded-lg px-3 py-2 text-sm font-bold tracking-widest text-emerald-800" x-ref="code{{ $coupon->id }}">{{ $coupon->code }}</code>
                    <button type="button"
                            @click="navigator.clipboard.writeText($refs.code{{ $coupon->id }}.innerText.trim()); copied = true; setTimeout(() => copied = false, 2000)"
                            class="flex-shrink-0 px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors">
                        <i class="fas" :class="copied ? 'fa-check' : 'fa-copy'"></i>
                    </button>
                </div>

                <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-xs">
                    <span class="text-gray-500">
                        @if($coupon->expires_at)
                            <i class="fas fa-clock me-1"></i>صالح حتى {{ $coupon->expires_at->format('Y-m-d') }}
                        @else
                            <i class="fas fa-infinity me-1"></i>بدون تاريخ انتهاء
                        @endif
                    </span>
                    <a href="{{ route('public.courses') }}" class="font-bold text-sky-700 hover:text-sky-900">
                        استخدمه الآن <i class="fas fa-arrow-left ms-1"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        <div class="px-4 sm:px-5 pb-5">
            <p class="text-xs text-gray-500 leading-relaxed">
                خصوماتك تُطبَّق تلقائياً في صفحة الدفع عند شراء أي كورس، ومش محتاج تكتب الكود بنفسك.
            </p>
        </div>
        @else
        <div class="p-6 sm:p-8 text-center">
            <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3 text-gray-400">
                <i class="fas fa-tag text-xl"></i>
            </div>
            <p class="text-sm text-gray-500 mb-4">لا توجد خصومات متاحة لك حالياً.</p>
            <a href="{{ route('public.customer-survey.show') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition-colors">
                <i class="fas fa-comment-dots"></i>
                شاركنا رأيك واحصل على خصم 20%
            </a>
        </div>
        @endif
    </div>

    @if(isset($transactions) && $transactions->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-5 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900">{{ __('student.transactions_log') }}</h2>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($transactions as $transaction)
            <div class="flex justify-between items-center p-4 sm:p-5 hover:bg-gray-50/50 transition-colors">
                <div class="min-w-0">
                    <p class="font-medium text-gray-900 truncate">{{ $transaction->description ?? __('student.transaction_default') }}</p>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $transaction->created_at ? $transaction->created_at->format('Y-m-d H:i') : '—' }}</p>
                </div>
                <p class="text-lg font-bold flex-shrink-0 {{ ($transaction->type == 'deposit' || $transaction->type == 'إيداع') ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ ($transaction->type == 'deposit' || $transaction->type == 'إيداع') ? '+' : '−' }}{{ number_format($transaction->amount ?? 0, 2) }} {{ __('public.currency_egp') }}
                </p>
            </div>
            @endforeach
        </div>
        @if($transactions->hasPages())
        <div class="p-4 border-t border-gray-100">{{ $transactions->links() }}</div>
        @endif
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 text-center">
        <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3 text-gray-400">
            <i class="fas fa-exchange-alt text-xl"></i>
        </div>
        <p class="text-sm text-gray-500">{{ __('student.no_transactions') }}</p>
    </div>
    @endif
</div>
@endsection
