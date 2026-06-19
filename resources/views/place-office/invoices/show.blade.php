@extends('layouts.place-manager')

@section('title', 'فاتورة ' . $invoice->invoice_number)
@section('header', 'فاتورة الدفع')

@section('content')
<div class="p-4 md:p-6 max-w-3xl">
    <div class="bg-white rounded-xl border p-8 shadow-sm">
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-2xl font-black text-slate-900">فاتورة دفع — مكان إداري</h1>
                <p class="text-slate-500 mt-1">{{ $location->name }}</p>
            </div>
            <div class="text-left" dir="ltr">
                <p class="font-mono font-bold">{{ $invoice->invoice_number }}</p>
                <p class="text-sm text-slate-500">{{ $invoice->issued_at?->format('Y-m-d') }}</p>
            </div>
        </div>
        <div class="mb-6 grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-slate-500">فترة المخالصة</span><p class="font-semibold">{{ $invoice->period_month }}</p></div>
            <div><span class="text-slate-500">الحالة</span><p class="font-semibold">{{ $invoice->status_label }}</p></div>
        </div>
        @if(is_array($invoice->line_items))
            <table class="w-full text-sm border-t border-b my-6">
                @foreach($invoice->line_items as $item)
                    <tr>
                        <td class="py-3">{{ $item['description'] ?? '' }}</td>
                        <td class="py-3 text-left" dir="ltr">{{ number_format((float) ($item['hours'] ?? 0), 2) }} h × {{ number_format((float) ($item['rate'] ?? 0), 2) }}</td>
                        <td class="py-3 text-left font-bold" dir="ltr">{{ number_format((float) ($item['amount'] ?? 0), 2) }} {{ $invoice->currency }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
        <p class="text-xl font-black text-violet-700 text-left" dir="ltr">الإجمالي: {{ number_format((float) $invoice->amount, 2) }} {{ $invoice->currency }}</p>
        @if($invoice->notes)<p class="text-xs text-slate-500 mt-4">{{ $invoice->notes }}</p>@endif
    </div>
    <a href="{{ route('place.office.invoices.index') }}" class="inline-block mt-4 text-violet-600">← العودة للفواتير</a>
</div>
@endsection
