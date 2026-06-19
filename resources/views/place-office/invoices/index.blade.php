@extends('layouts.place-manager')

@section('title', 'فواتير الدفع')
@section('header', 'فواتير الدفع — ' . $location->name)

@section('content')
<div class="p-4 md:p-6 space-y-6">
    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="px-4 py-3 text-right">رقم الفاتورة</th>
                <th class="px-4 py-3 text-right">الشهر</th>
                <th class="px-4 py-3 text-right">المبلغ</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3 text-right"></th>
            </tr></thead>
            <tbody class="divide-y">
                @forelse($invoices as $inv)
                    <tr>
                        <td class="px-4 py-3 font-mono" dir="ltr">{{ $inv->invoice_number }}</td>
                        <td class="px-4 py-3">{{ $inv->period_month }}</td>
                        <td class="px-4 py-3">{{ number_format((float) $inv->amount, 2) }} {{ $inv->currency }}</td>
                        <td class="px-4 py-3">{{ $inv->status_label }}</td>
                        <td class="px-4 py-3"><a href="{{ route('place.office.invoices.show', $inv) }}" class="text-violet-600">عرض</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">لا توجد فواتير بعد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $invoices->links() }}
</div>
@endsection
