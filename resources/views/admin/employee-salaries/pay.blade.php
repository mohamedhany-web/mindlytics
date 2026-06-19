@extends('layouts.admin')

@section('title', 'دفع راتب موظف')
@section('header', 'دفع راتب موظف')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-6" style="background:#f8fafc;min-height:100vh;">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.employee-salaries.index', ['month'=>$payment->period_month,'year'=>$payment->period_year]) }}" class="text-sm font-semibold text-slate-600 hover:text-blue-700">
            <i class="fas fa-arrow-right ml-1"></i> العودة لمسير الرواتب
        </a>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <section class="rounded-2xl bg-white border shadow-lg p-6">
            <h2 class="text-lg font-black text-slate-900 mb-4">تفاصيل الراتب</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between border-b pb-2"><dt class="text-slate-500">رقم الدفعة</dt><dd class="font-mono font-bold">{{ $payment->payment_number }}</dd></div>
                <div class="flex justify-between border-b pb-2"><dt class="text-slate-500">الموظف</dt><dd class="font-bold">{{ $payment->employee?->name }}</dd></div>
                <div class="flex justify-between border-b pb-2"><dt class="text-slate-500">الفترة</dt><dd>{{ $payment->period_month }}/{{ $payment->period_year }}</dd></div>
                <div class="flex justify-between border-b pb-2"><dt class="text-slate-500">الأساسي</dt><dd class="tabular-nums">{{ number_format($payment->base_salary,2) }} ج.م</dd></div>
                <div class="flex justify-between border-b pb-2"><dt class="text-slate-500">الخصومات</dt><dd class="tabular-nums text-rose-700">-{{ number_format($payment->total_deductions,2) }} ج.م</dd></div>
                <div class="flex justify-between border-b pb-2"><dt class="text-slate-500">الإضافات (أوفر تايم)</dt><dd class="tabular-nums text-emerald-700">+{{ number_format($payment->total_additions ?? 0,2) }} ج.م</dd></div>
                <div class="flex justify-between pt-1"><dt class="text-slate-700 font-bold">الصافي للدفع</dt><dd class="text-2xl font-black text-blue-700 tabular-nums">{{ number_format($payment->net_salary,2) }} ج.م</dd></div>
            </dl>
        </section>

        <section class="rounded-2xl bg-white border shadow-lg p-6">
            <h2 class="text-lg font-black text-slate-900 mb-2">بيانات التحويل</h2>
            @php $e = $payment->employee; @endphp
            @if($e && ($e->bank_account_number || $e->bank_iban))
                <dl class="text-sm space-y-2 mb-4">
                    @if($e->bank_name)<div><span class="text-slate-500">البنك:</span> {{ $e->bank_name }}</div>@endif
                    @if($e->bank_account_holder_name)<div><span class="text-slate-500">صاحب الحساب:</span> {{ $e->bank_account_holder_name }}</div>@endif
                    @if($e->bank_account_number)<div><span class="text-slate-500">الحساب:</span> <span class="font-mono">{{ $e->bank_account_number }}</span></div>@endif
                    @if($e->bank_iban)<div><span class="text-slate-500">IBAN:</span> <span class="font-mono">{{ $e->bank_iban }}</span></div>@endif
                </dl>
            @else
                <p class="text-amber-700 bg-amber-50 border border-amber-200 rounded-xl p-3 text-sm mb-4">لم يُسجَّل حساب بنكي للموظف — يمكنك الدفع ورفع الإيصال.</p>
            @endif

            <form method="post" action="{{ route('admin.employee-salaries.mark-paid', $payment) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold mb-1">المحفظة *</label>
                    <select name="wallet_id" required class="w-full rounded-xl border px-3 py-2.5 text-sm @error('wallet_id') border-rose-500 @enderror">
                        <option value="">— اختر المحفظة —</option>
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}" @selected(old('wallet_id') == $w->id)>{{ $w->name }} — رصيد: {{ number_format($w->balance,2) }} ج.م</option>
                        @endforeach
                    </select>
                    @error('wallet_id')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                    <p class="text-xs text-slate-500 mt-1">يُخصم المبلغ من المحفظة ويُسجَّل مصروف «رواتب» معتمد تلقائياً.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">إيصال التحويل (اختياري)</label>
                    <input type="file" name="transfer_receipt" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">ملاحظات</label>
                    <textarea name="notes" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm">{{ old('notes') }}</textarea>
                </div>
                <button type="submit" class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm" onclick="return confirm('تأكيد الدفع وتسجيل المصروف؟');">
                    <i class="fas fa-check-circle ml-1"></i> تنفيذ الدفع وتسجيل المصروف
                </button>
            </form>
        </section>
    </div>
</div>
@endsection
