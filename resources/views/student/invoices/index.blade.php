@extends('layouts.app')

@section('title', 'فواتيري')
@section('header', 'فواتيري')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900">فواتيري</h1>
        <p class="text-gray-600 mt-1">جميع فواتيري</p>
    </div>

    @if(isset($invoices) && $invoices->count() > 0)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">رقم الفاتورة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المبلغ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($invoices as $invoice)
                    <tr>
                        <td class="px-6 py-4">{{ $invoice->invoice_number }}</td>
                        <td class="px-6 py-4">{{ number_format($invoice->total_amount, 2) }} ج.م</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($invoice->status == 'paid') bg-green-100 text-green-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ $invoice->status == 'paid' ? 'مدفوعة' : 'معلقة' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('student.invoices.show', $invoice) }}" class="text-sky-600 hover:text-sky-900">عرض</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $invoices->links() }}</div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-lg p-12 text-center">
        <p class="text-gray-600">لا توجد فواتير</p>
    </div>
    @endif
</div>
@endsection
