@extends('layouts.place-manager')

@section('title', 'تفاصيل المخالصة')
@section('header', 'مخالصة ' . $settlement->period_month)

@section('content')
<div class="p-4 md:p-6 space-y-6 max-w-4xl">
    @if(session('success'))<div class="rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-emerald-800">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-lg bg-rose-50 border border-rose-200 p-3 text-rose-800">{{ $errors->first() }}</div>@endif

    <div class="bg-white rounded-xl border p-6 grid md:grid-cols-2 gap-4">
        <div><span class="text-slate-500 text-sm">رقم المخالصة</span><p class="font-bold">{{ $settlement->settlement_number }}</p></div>
        <div><span class="text-slate-500 text-sm">الحالة</span><p class="font-bold">{{ $settlement->status_label }}</p></div>
        <div><span class="text-slate-500 text-sm">إجمالي الساعات المعتمدة</span><p class="font-bold text-violet-700">{{ number_format((float) $settlement->total_hours, 2) }}</p></div>
        <div><span class="text-slate-500 text-sm">المبلغ</span><p class="font-bold">{{ number_format((float) $settlement->total_amount, 2) }} {{ $settlement->currency }}</p></div>
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <h2 class="px-4 py-3 font-bold border-b bg-slate-50">سجلات الشهر</h2>
        <table class="min-w-full text-sm">
            <thead><tr class="bg-slate-50"><th class="px-4 py-2 text-right">التاريخ</th><th class="px-4 py-2 text-right">ساعات</th><th class="px-4 py-2 text-right">الحالة</th></tr></thead>
            <tbody class="divide-y">
                @foreach($settlement->usageLogs as $log)
                    <tr>
                        <td class="px-4 py-2">{{ $log->usage_date->format('Y-m-d') }}</td>
                        <td class="px-4 py-2">{{ number_format((float) $log->hours, 2) }}</td>
                        <td class="px-4 py-2">{{ $log->status_label }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($settlement->status === 'open')
        <form action="{{ route('place.office.settlements.submit', $settlement) }}" method="POST" onsubmit="return confirm('إرسال المخالصة للمراجعة؟');">
            @csrf
            <button type="submit" class="px-5 py-2.5 bg-violet-600 text-white rounded-lg font-semibold">إرسال للمراجعة</button>
        </form>
    @endif

    @if($settlement->invoice)
        <a href="{{ route('place.office.invoices.show', $settlement->invoice) }}" class="inline-block text-violet-600 font-medium">عرض فاتورة الدفع</a>
    @endif
</div>
@endsection
