@extends('layouts.app')

@section('title', __('instructor.request_number') . ' ' . ($withdrawal->request_number ?? '#' . $withdrawal->id) . ' - Mindlytics')
@section('header', __('instructor.withdrawal_requests'))

@section('content')
<div class="space-y-6 max-w-3xl mx-auto w-full">
    <a href="{{ route('instructor.withdrawals.index') }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-sky-700 transition-colors">
        <i class="fas fa-arrow-right text-xs"></i>
        {{ __('instructor.withdrawal_requests') }}
    </a>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-5 sm:px-6 border-b border-slate-200 bg-slate-50/80">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ __('instructor.request_number') }}</p>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-mono">{{ $withdrawal->request_number ?? '#' . $withdrawal->id }}</h1>
                    <p class="text-sm text-slate-500 mt-2 tabular-nums">
                        <i class="fas fa-calendar-alt text-slate-400 text-xs ml-1"></i>
                        {{ $withdrawal->created_at->format('Y-m-d H:i') }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border
                        @if($withdrawal->status == 'completed') bg-emerald-50 text-emerald-800 border-emerald-100
                        @elseif($withdrawal->status == 'processing') bg-sky-50 text-sky-800 border-sky-100
                        @elseif($withdrawal->status == 'approved') bg-amber-50 text-amber-800 border-amber-100
                        @elseif($withdrawal->status == 'pending') bg-slate-100 text-slate-700 border-slate-200
                        @elseif($withdrawal->status == 'rejected') bg-rose-50 text-rose-800 border-rose-100
                        @elseif($withdrawal->status == 'cancelled') bg-slate-50 text-slate-600 border-slate-200
                        @else bg-slate-50 text-slate-700 border-slate-100
                        @endif">
                        @if($withdrawal->status == 'completed') {{ __('instructor.completed') }}
                        @elseif($withdrawal->status == 'processing') {{ __('instructor.processing') }}
                        @elseif($withdrawal->status == 'approved') {{ __('instructor.approved') }}
                        @elseif($withdrawal->status == 'pending') {{ __('instructor.pending_status') }}
                        @elseif($withdrawal->status == 'rejected') {{ __('instructor.rejected') }}
                        @elseif($withdrawal->status == 'cancelled') {{ __('instructor.cancelled') }}
                        @else {{ $withdrawal->status }}
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <div class="px-5 py-6 sm:px-6 space-y-6">
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 sm:p-5">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">{{ __('instructor.amount') }}</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-900 tabular-nums">{{ number_format($withdrawal->amount, 2) }} {{ __('public.currency_egp') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="rounded-xl border border-slate-100 bg-white p-4">
                    <p class="text-xs font-semibold text-slate-500 mb-1">{{ __('instructor.payment_method') }}</p>
                    <p class="text-sm font-bold text-slate-900">
                        @if($withdrawal->payment_method == 'bank_transfer')
                            <span class="inline-flex items-center gap-1.5"><i class="fas fa-university text-sky-600"></i> {{ __('instructor.bank_transfer') }}</span>
                        @elseif($withdrawal->payment_method == 'wallet')
                            <span class="inline-flex items-center gap-1.5"><i class="fas fa-wallet text-sky-600"></i> {{ __('instructor.wallet') }}</span>
                        @elseif($withdrawal->payment_method == 'cash')
                            <span class="inline-flex items-center gap-1.5"><i class="fas fa-money-bill text-sky-600"></i> {{ __('instructor.cash') }}</span>
                        @else
                            <span class="inline-flex items-center gap-1.5"><i class="fas fa-ellipsis-h text-sky-600"></i> {{ __('instructor.other') }}</span>
                        @endif
                    </p>
                </div>
                @if($withdrawal->processed_at)
                <div class="rounded-xl border border-slate-100 bg-white p-4">
                    <p class="text-xs font-semibold text-slate-500 mb-1">{{ __('instructor.processed_at') }}</p>
                    <p class="text-sm font-bold text-slate-900 tabular-nums">{{ $withdrawal->processed_at->format('Y-m-d H:i') }}</p>
                </div>
                @endif
            </div>

            @if($withdrawal->payment_method === 'bank_transfer' && ($withdrawal->bank_name || $withdrawal->account_holder_name || $withdrawal->account_number || $withdrawal->iban))
            <div class="rounded-xl border border-sky-100 bg-sky-50/40 p-4 sm:p-5 space-y-3">
                <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-university text-sky-600"></i>
                    {{ __('instructor.bank_transfer') }}
                </h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    @if($withdrawal->bank_name)
                    <div>
                        <dt class="text-xs font-semibold text-slate-500">{{ __('instructor.bank_name') }}</dt>
                        <dd class="font-medium text-slate-900 mt-0.5">{{ $withdrawal->bank_name }}</dd>
                    </div>
                    @endif
                    @if($withdrawal->account_holder_name)
                    <div>
                        <dt class="text-xs font-semibold text-slate-500">{{ __('instructor.account_holder_name') }}</dt>
                        <dd class="font-medium text-slate-900 mt-0.5">{{ $withdrawal->account_holder_name }}</dd>
                    </div>
                    @endif
                    @if($withdrawal->account_number)
                    <div>
                        <dt class="text-xs font-semibold text-slate-500">{{ __('instructor.account_number') }}</dt>
                        <dd class="font-medium text-slate-900 mt-0.5 font-mono" dir="ltr">{{ $withdrawal->account_number }}</dd>
                    </div>
                    @endif
                    @if($withdrawal->iban)
                    <div>
                        <dt class="text-xs font-semibold text-slate-500">{{ __('instructor.iban') }}</dt>
                        <dd class="font-medium text-slate-900 mt-0.5 font-mono" dir="ltr">{{ $withdrawal->iban }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif

            @if($withdrawal->notes)
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">{{ __('instructor.notes') }}</p>
                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 text-sm text-slate-800 whitespace-pre-wrap">{{ $withdrawal->notes }}</div>
            </div>
            @endif

            @if($withdrawal->admin_notes)
            <div class="rounded-xl border border-amber-100 bg-amber-50/50 p-4">
                <p class="text-xs font-bold text-amber-800 uppercase tracking-wide mb-2">{{ __('instructor.admin_notes_label') }}</p>
                <p class="text-sm text-slate-800 whitespace-pre-wrap">{{ $withdrawal->admin_notes }}</p>
            </div>
            @endif
        </div>

        @if(in_array($withdrawal->status, ['pending', 'approved']))
        <div class="px-5 py-4 sm:px-6 border-t border-slate-200 bg-slate-50/80 flex flex-wrap gap-3">
            <form action="{{ route('instructor.withdrawals.cancel', $withdrawal) }}" method="POST"
                  onsubmit="return confirm('{{ __('instructor.confirm_cancel_withdrawal') }}');">
                @csrf
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-800 font-semibold text-sm hover:bg-rose-100 transition-colors">
                    <i class="fas fa-times text-sm"></i>
                    {{ __('instructor.cancel') }}
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
