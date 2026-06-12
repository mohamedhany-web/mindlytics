@extends('layouts.admin')

@section('title', 'تعديل الفاتورة ' . $invoice->invoice_number)
@section('header', 'تعديل الفاتورة')

@section('content')
<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">تعديل {{ $invoice->invoice_number }}</h2>
                    <p class="text-xs text-slate-600">{{ $invoice->user->name ?? '—' }} · {{ number_format((float) $invoice->total_amount, 2) }} ج.م</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.invoices.show', $invoice) }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-eye text-blue-600"></i>
                    عرض
                </a>
                <a href="{{ route('admin.invoices.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-list"></i>
                    القائمة
                </a>
            </div>
        </div>
        <div class="p-4 sm:p-6">
            @include('admin.invoices.partials.form', ['users' => $users, 'invoice' => $invoice])
        </div>
    </section>
</div>
@endsection
