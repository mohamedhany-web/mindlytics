@extends('layouts.app')

@section('title', 'محفظتي')
@section('header', 'محفظتي')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <!-- الهيدر والرصيد -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4">محفظتي</h1>
            @if(isset($wallet))
            <div class="flex items-center justify-between gap-4 p-4 sm:p-5 bg-sky-50 rounded-xl border border-sky-100">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">الرصيد الحالي</p>
                    <p class="text-2xl sm:text-3xl font-bold text-sky-600">{{ number_format($wallet->balance ?? 0, 2) }} ج.م</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
            </div>
            @else
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 text-gray-600 text-sm">لا توجد محفظة مرتبطة بحسابك.</div>
            @endif
        </div>
    </div>

    @if(isset($transactions) && $transactions->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-5 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900">سجل المعاملات</h2>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($transactions as $transaction)
            <div class="flex justify-between items-center p-4 sm:p-5 hover:bg-gray-50/50 transition-colors">
                <div class="min-w-0">
                    <p class="font-medium text-gray-900 truncate">{{ $transaction->description ?? 'معاملة' }}</p>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $transaction->created_at ? $transaction->created_at->format('Y-m-d H:i') : '—' }}</p>
                </div>
                <p class="text-lg font-bold flex-shrink-0 {{ ($transaction->type == 'deposit' || $transaction->type == 'إيداع') ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ ($transaction->type == 'deposit' || $transaction->type == 'إيداع') ? '+' : '−' }}{{ number_format($transaction->amount ?? 0, 2) }} ج.م
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
        <p class="text-sm text-gray-500">لا توجد معاملات حتى الآن</p>
    </div>
    @endif
</div>
@endsection
