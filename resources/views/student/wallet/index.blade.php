@extends('layouts.student-dashboard')

@section('title', __('student.wallet_title'))

@push('styles')
@include('student.offline-courses.partials.los-styles')
<style>
    .wl-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 260px), 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }
    .wl-card {
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        padding: 16px; background: var(--ml-surface);
        border: 1px solid var(--ml-line); border-radius: var(--ml-r);
    }
    .wl-card .lbl { display: block; font-size: 12px; font-weight: 700; color: var(--ml-muted); margin-bottom: 4px; }
    .wl-card .bal { font-size: 1.35rem; font-weight: 700; color: var(--ml-teal-deep); letter-spacing: -0.02em; }
    .wl-card .ico {
        width: 48px; height: 48px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(73,164,162,0.12); color: var(--ml-teal-deep); font-size: 1.1rem;
    }
    .wl-form label {
        display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; color: var(--ml-ink);
    }
    .wl-form select, .wl-form input {
        width: 100%; min-height: 40px; padding: 0 12px;
        border-radius: 12px; border: 1px solid var(--ml-line);
        background: var(--ml-surface); color: var(--ml-ink); font-family: inherit; font-size: 13px;
    }
    .wl-form select:focus, .wl-form input:focus {
        outline: none; border-color: rgba(73,164,162,0.55);
        box-shadow: 0 0 0 3px rgba(73,164,162,0.12);
    }
    .wl-form .grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;
    }
    @media (max-width: 640px) {
        .wl-form .grid { grid-template-columns: 1fr; }
    }
    .wl-tx {
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        padding: 12px 0; border-bottom: 1px solid var(--ml-line);
    }
    .wl-tx:last-child { border-bottom: 0; }
    .wl-tx strong { display: block; font-size: 13px; font-weight: 700; line-height: 1.35; }
    .wl-tx .when { font-size: 11px; color: var(--ml-muted); margin-top: 2px; }
    .wl-tx .amt { font-size: 14px; font-weight: 700; white-space: nowrap; }
    .wl-tx .amt.plus { color: #047857; }
    .wl-tx .amt.minus { color: #b91c1c; }
</style>
@endpush

@section('content')
<div class="oc">
    @if(session('success'))
        <div class="oc-panel" style="border-color:rgba(16,185,129,0.35);background:rgba(16,185,129,0.08);margin-bottom:16px;color:#047857;font-size:13px;font-weight:600">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="oc-panel" style="border-color:rgba(239,68,68,0.35);background:rgba(239,68,68,0.08);margin-bottom:16px;color:#b91c1c;font-size:13px">
            <ul style="margin:0;padding-inline-start:18px">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="{{ __('student.wallet_title') }}">
                <a href="{{ route('dashboard') }}">{{ __('student.learning_center') }}</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700">{{ __('student.wallet_title') }}</span>
            </nav>
            <h1>{{ __('student.wallet_title') }}</h1>
            <p class="sub">{{ __('student.wallet_financial') }}</p>
        </div>
        @if(isset($wallets) && $wallets->count() > 0)
            <div class="oc-signals">
                <span class="oc-signal oc-signal-live">{{ $wallets->count() }}</span>
                <span class="oc-signal oc-signal-hot">
                    {{ number_format($wallets->sum('balance'), 2) }} {{ __('public.currency_egp') }}
                </span>
            </div>
        @endif
    </header>

    @if(isset($wallets) && $wallets->count() > 0)
        <div class="wl-grid">
            @foreach($wallets as $wallet)
                <div class="wl-card">
                    <div>
                        <span class="lbl">{{ $wallet->name ?: ('#' . $wallet->id) }}</span>
                        <span class="bal">{{ number_format($wallet->balance ?? 0, 2) }} {{ __('public.currency_egp') }}</span>
                    </div>
                    <div class="ico" aria-hidden="true"><i class="fas fa-wallet"></i></div>
                </div>
            @endforeach
        </div>

        @if($wallets->count() > 1)
            <section class="oc-panel">
                <p class="oc-label">{{ __('student.wallet_transfer_title') }}</p>
                <form action="{{ route('student.wallet.transfer') }}" method="POST" class="wl-form">
                    @csrf
                    <div class="grid">
                        <div>
                            <label for="from_wallet_id">{{ __('student.wallet_transfer_from') }}</label>
                            <select id="from_wallet_id" name="from_wallet_id" required>
                                <option value="">{{ __('student.wallet_transfer_select_wallet') }}</option>
                                @foreach($wallets as $wallet)
                                    <option value="{{ $wallet->id }}" @selected(old('from_wallet_id') == $wallet->id)>
                                        {{ $wallet->name ?: ('#' . $wallet->id) }} — {{ number_format($wallet->balance ?? 0, 2) }} {{ __('public.currency_egp') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="to_wallet_id">{{ __('student.wallet_transfer_to') }}</label>
                            <select id="to_wallet_id" name="to_wallet_id" required>
                                <option value="">{{ __('student.wallet_transfer_select_wallet') }}</option>
                                @foreach($wallets as $wallet)
                                    <option value="{{ $wallet->id }}" @selected(old('to_wallet_id') == $wallet->id)>
                                        {{ $wallet->name ?: ('#' . $wallet->id) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="amount">{{ __('student.amount_label') }}</label>
                            <input id="amount" name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount') }}" required>
                        </div>
                        <div>
                            <label for="notes">{{ __('student.your_notes') }}</label>
                            <input id="notes" name="notes" type="text" value="{{ old('notes') }}" maxlength="500">
                        </div>
                    </div>
                    <button type="submit" class="oc-btn">
                        <i class="fas fa-right-left text-xs"></i>
                        {{ __('student.wallet_transfer_button') }}
                    </button>
                </form>
            </section>
        @else
            <div class="oc-panel" style="border-color:rgba(245,158,11,0.35);background:rgba(245,158,11,0.1);color:#92400e;font-size:13px">
                {{ __('student.wallet_transfer_need_two_wallets') }}
            </div>
        @endif
    @else
        <div class="oc-empty">
            <div class="icon"><i class="fas fa-wallet"></i></div>
            <h3>{{ __('student.wallet_title') }}</h3>
            <p>{{ __('student.no_wallet_message') }}</p>
        </div>
    @endif

    <section class="oc-panel" style="margin-top:16px">
        <p class="oc-label">{{ __('student.transactions_log') }}</p>
        @if(isset($transactions) && $transactions->count() > 0)
            @foreach($transactions as $transaction)
                @php $isDeposit = in_array($transaction->type, ['deposit', 'إيداع'], true); @endphp
                <div class="wl-tx">
                    <div class="min-w-0">
                        <strong>{{ $transaction->description ?? __('student.transaction_default') }}</strong>
                        <div class="when">{{ $transaction->created_at?->format('Y-m-d H:i') ?? '—' }}</div>
                    </div>
                    <div class="amt {{ $isDeposit ? 'plus' : 'minus' }}">
                        {{ $isDeposit ? '+' : '−' }}{{ number_format($transaction->amount ?? 0, 2) }} {{ __('public.currency_egp') }}
                    </div>
                </div>
            @endforeach
            @if($transactions->hasPages())
                <div style="margin-top:16px;display:flex;justify-content:center">{{ $transactions->links() }}</div>
            @endif
        @else
            <div class="oc-empty" style="padding:28px 12px;border:0;background:transparent">
                <div class="icon" style="width:44px;height:44px;font-size:18px"><i class="fas fa-exchange-alt"></i></div>
                <p style="margin:0">{{ __('student.no_transactions') }}</p>
            </div>
        @endif
    </section>
</div>
@endsection
