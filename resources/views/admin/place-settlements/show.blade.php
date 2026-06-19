@extends('layouts.admin')

@section('title', 'مراجعة مخالصة')
@section('header', 'مخالصة ' . $placeSettlement->period_month . ' — ' . ($placeSettlement->location?->name ?? ''))

@section('content')
<div class="p-4 md:p-6 space-y-6 max-w-5xl">
    @if(session('success'))<div class="rounded-lg bg-emerald-50 border p-3">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-lg bg-rose-50 border p-3">{{ $errors->first() }}</div>@endif

    <div class="bg-white rounded-xl border p-6 grid md:grid-cols-3 gap-4">
        <div><span class="text-slate-500 text-sm">رقم المخالصة</span><p class="font-bold">{{ $placeSettlement->settlement_number }}</p></div>
        <div><span class="text-slate-500 text-sm">الساعات</span><p class="font-bold">{{ number_format((float) $placeSettlement->total_hours, 2) }}</p></div>
        <div><span class="text-slate-500 text-sm">المبلغ</span><p class="font-bold text-lg">{{ number_format((float) $placeSettlement->total_amount, 2) }} {{ $placeSettlement->currency }}</p></div>
        <div><span class="text-slate-500 text-sm">سعر الساعة</span><p>{{ number_format((float) $placeSettlement->hourly_rate, 2) }}</p></div>
        <div><span class="text-slate-500 text-sm">الحالة</span><p class="font-semibold">{{ $placeSettlement->status_label }}</p></div>
        @if($placeSettlement->expense)
            <div><span class="text-slate-500 text-sm">المصروف</span>
                <p><a href="{{ route('admin.expenses.show', $placeSettlement->expense) }}" class="text-blue-600">{{ $placeSettlement->expense->expense_number }}</a></p>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50"><tr><th class="px-4 py-2 text-right">التاريخ</th><th class="px-4 py-2 text-right">ساعات</th><th class="px-4 py-2 text-right">الحالة</th></tr></thead>
            <tbody class="divide-y">
                @foreach($placeSettlement->usageLogs as $log)
                    <tr>
                        <td class="px-4 py-2">{{ $log->usage_date->format('Y-m-d') }}</td>
                        <td class="px-4 py-2">{{ number_format((float) $log->hours, 2) }}</td>
                        <td class="px-4 py-2">{{ $log->status_label }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($placeSettlement->status === 'submitted')
        <form action="{{ route('admin.place-settlements.approve', $placeSettlement) }}" method="POST" class="bg-white rounded-xl border p-6 space-y-4">
            @csrf
            <h3 class="font-bold">اعتماد المخالصة وإنشاء مصروف + فاتورة</h3>
            <div>
                <label class="block text-sm text-slate-600 mb-1">المحفظة للخصم</label>
                <select name="wallet_id" class="w-full max-w-md rounded-lg border-slate-300">
                    @foreach($wallets as $w)
                        <option value="{{ $w->id }}" @selected($placeSettlement->wallet_id == $w->id || $placeSettlement->location?->default_wallet_id == $w->id)>
                            {{ $w->name }} ({{ number_format((float) $w->balance, 2) }} ج.م)
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg font-semibold" onclick="return confirm('اعتماد المخالصة؟');">اعتماد</button>
        </form>
    @endif

    @if(in_array($placeSettlement->status, ['approved', 'paid'], true) && $placeSettlement->status !== 'closed')
        <form action="{{ route('admin.place-settlements.close', $placeSettlement) }}" method="POST">
            @csrf
            <button type="submit" class="px-5 py-2.5 bg-slate-800 text-white rounded-lg font-semibold" onclick="return confirm('إقفال الشهر؟ لن يُسمح بمزيد من التسجيل.');">إقفال الشهر</button>
        </form>
    @endif

    @if($placeSettlement->expense && $placeSettlement->expense->status === 'pending')
        <div class="rounded-lg bg-amber-50 border border-amber-200 p-4 text-sm">
            المصروف بانتظار الموافقة من <a href="{{ route('admin.expenses.show', $placeSettlement->expense) }}" class="text-blue-600 font-medium">صفحة المصروفات</a> لخصم المبلغ من المحفظة.
        </div>
    @endif
</div>
@endsection
