@extends('layouts.admin')

@section('title', 'عمليات بوابات الدفع')
@section('header', 'عمليات بوابات الدفع')

@section('content')
<div class="w-full space-y-8">
    <section class="rounded-3xl bg-gradient-to-br from-indigo-900 via-slate-900 to-slate-800 text-white shadow-xl overflow-hidden">
        <div class="px-6 py-8 sm:px-10 border-b border-white/10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">محاسبة عمليات بوابة الدفع</h1>
                    <p class="mt-2 text-sm text-white/70 max-w-2xl leading-relaxed">
                        كل عملية دفع أونلاين عبر بوابة (كاشير، فواتيرك، …) مرتبطة بـ <strong>الفاتورة</strong> و<strong>الطلب</strong> و<strong>معاملات الدفتر</strong> (قيد دائن للتحصيل + قيد مدين لعمولة البوابة عند تفعيلها من إعدادات النظام).
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.accounting.hub') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white/10 px-4 py-2.5 text-sm font-semibold border border-white/20 hover:bg-white/20">
                        <i class="fas fa-th-large"></i>
                        مركز المحاسبة
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white/10 px-4 py-2.5 text-sm font-semibold border border-white/20 hover:bg-white/20">
                        <i class="fas fa-shopping-cart"></i>
                        الطلبات
                    </a>
                    <a href="{{ route('admin.transactions.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:bg-emerald-400">
                        <i class="fas fa-exchange-alt"></i>
                        المعاملات
                    </a>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-px bg-white/10">
            <div class="bg-slate-900/80 p-5">
                <p class="text-xs font-semibold text-white/60">عدد العمليات (بعد الفلاتر)</p>
                <p class="mt-2 text-2xl font-black text-sky-200">{{ number_format($summary['operations_count']) }}</p>
            </div>
            <div class="bg-slate-900/80 p-5">
                <p class="text-xs font-semibold text-white/60">إجمالي المحصّل (إجمالي)</p>
                <p class="mt-2 text-xl font-black text-emerald-300">{{ number_format($summary['gross_total'], 2) }} <span class="text-sm text-white/50">ج.م</span></p>
            </div>
            <div class="bg-slate-900/80 p-5">
                <p class="text-xs font-semibold text-white/60">عمولات البوابة (مجموع)</p>
                <p class="mt-2 text-xl font-black text-amber-200">{{ number_format($summary['fees_total'], 2) }} <span class="text-sm text-white/50">ج.م</span></p>
            </div>
            <div class="bg-slate-900/80 p-5">
                <p class="text-xs font-semibold text-white/60">صافي بعد العمولة (تقديري)</p>
                <p class="mt-2 text-xl font-black text-violet-200">{{ number_format($summary['net_after_fees'], 2) }} <span class="text-sm text-white/50">ج.م</span></p>
            </div>
        </div>
    </section>

    @if($byGateway->isNotEmpty())
    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80">
            <h2 class="text-lg font-bold text-slate-900">ملخص حسب البوابة</h2>
            <p class="text-xs text-slate-500 mt-1">بعد تطبيق نفس فلاتر التاريخ والبحث أعلاه</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-right font-bold">البوابة</th>
                        <th class="px-4 py-3 text-right font-bold">العدد</th>
                        <th class="px-4 py-3 text-right font-bold">إجمالي</th>
                        <th class="px-4 py-3 text-right font-bold">عمولات</th>
                        <th class="px-4 py-3 text-right font-bold">صافي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($byGateway as $row)
                    @php
                        $gross = (float) $row->gross;
                        $fees = (float) $row->fees;
                    @endphp
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-4 py-3 font-semibold text-slate-900">{{ \App\Models\Payment::gatewayLabel($row->payment_gateway) }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ number_format((int) $row->cnt) }}</td>
                        <td class="px-4 py-3 text-emerald-700 font-mono">{{ number_format($gross, 2) }}</td>
                        <td class="px-4 py-3 text-amber-700 font-mono">{{ number_format($fees, 2) }}</td>
                        <td class="px-4 py-3 text-violet-800 font-mono font-semibold">{{ number_format($gross - $fees, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80">
            <h2 class="text-lg font-bold text-slate-900">فلترة</h2>
        </div>
        <form method="get" action="{{ route('admin.accounting.gateway-operations') }}" class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">البوابة</label>
                <select name="gateway" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <option value="">— الكل —</option>
                    @foreach($gatewayOptions as $g)
                        <option value="{{ $g }}" @selected($gateway === $g)>{{ \App\Models\Payment::gatewayLabel($g) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">من تاريخ</label>
                <input type="date" name="date_from" value="{{ $from }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">إلى تاريخ</label>
                <input type="date" name="date_to" value="{{ $to }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            </div>
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-600 mb-1">بحث (رقم دفعة، فاتورة، طلب، …)</label>
                <input type="text" name="q" value="{{ $q }}" placeholder="مثال: PAY- أو INV- أو رقم الطلب" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            </div>
            <div class="flex flex-wrap gap-2 md:col-span-2 lg:col-span-5">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 text-white px-5 py-2.5 text-sm font-bold hover:bg-indigo-700">
                    <i class="fas fa-filter"></i>
                    تطبيق
                </button>
                <a href="{{ route('admin.accounting.gateway-operations') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    إعادة ضبط
                </a>
                <a href="{{ route('admin.system-settings.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-5 py-2.5 text-sm font-semibold text-amber-900 hover:bg-amber-100">
                    <i class="fas fa-cog"></i>
                    إعدادات عمولة البوابة
                </a>
            </div>
        </form>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-900">سجل العمليات</h2>
            <p class="text-xs text-slate-500">معاملة دائن = تحصيل العميل · معاملة مدين (fee) = عمولة البوابة إن وُجدت</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-3 py-3 text-right font-bold">التاريخ</th>
                        <th class="px-3 py-3 text-right font-bold">البوابة</th>
                        <th class="px-3 py-3 text-right font-bold">الدفعة</th>
                        <th class="px-3 py-3 text-right font-bold">الإجمالي</th>
                        <th class="px-3 py-3 text-right font-bold">العمولة</th>
                        <th class="px-3 py-3 text-right font-bold">الصافي</th>
                        <th class="px-3 py-3 text-right font-bold">الفاتورة</th>
                        <th class="px-3 py-3 text-right font-bold">الطلب</th>
                        <th class="px-3 py-3 text-right font-bold">المعاملات</th>
                        <th class="px-3 py-3 text-right font-bold">العميل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $payment)
                    @php
                        $fee = (float) ($payment->gateway_fee_amount ?? 0);
                        $net = (float) $payment->amount - $fee;
                        $creditTx = $payment->transactions->firstWhere('type', 'credit');
                        $feeTx = $payment->transactions->firstWhere('type', 'debit');
                    @endphp
                    <tr class="hover:bg-slate-50/80 align-top">
                        <td class="px-3 py-3 text-slate-600 whitespace-nowrap">{{ $payment->paid_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td class="px-3 py-3">
                            <span class="inline-flex items-center rounded-lg bg-indigo-50 text-indigo-900 px-2 py-1 text-xs font-bold">{{ \App\Models\Payment::gatewayLabel($payment->payment_gateway) }}</span>
                        </td>
                        <td class="px-3 py-3">
                            <a href="{{ route('admin.payments.show', $payment) }}" class="font-mono text-sky-700 font-semibold hover:underline">{{ $payment->payment_number }}</a>
                        </td>
                        <td class="px-3 py-3 font-mono text-emerald-700">{{ number_format((float) $payment->amount, 2) }}</td>
                        <td class="px-3 py-3 font-mono text-amber-700">{{ $fee > 0 ? number_format($fee, 2) : '—' }}</td>
                        <td class="px-3 py-3 font-mono font-semibold text-violet-800">{{ number_format($net, 2) }}</td>
                        <td class="px-3 py-3">
                            @if($payment->invoice)
                                <a href="{{ route('admin.invoices.show', $payment->invoice) }}" class="text-sky-700 font-mono text-xs hover:underline">{{ $payment->invoice->invoice_number }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-3 py-3">
                            @if($payment->order_id)
                                <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="text-slate-800 font-mono text-xs hover:underline">#{{ $payment->order_id }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-3 py-3 text-xs space-y-1">
                            @if($creditTx)
                                <div><span class="text-emerald-600 font-bold">دائن</span> <a href="{{ route('admin.transactions.show', $creditTx) }}" class="font-mono text-sky-700 hover:underline">{{ $creditTx->transaction_number }}</a></div>
                            @endif
                            @if($feeTx)
                                <div><span class="text-amber-700 font-bold">مدين</span> <a href="{{ route('admin.transactions.show', $feeTx) }}" class="font-mono text-sky-700 hover:underline">{{ $feeTx->transaction_number }}</a></div>
                            @endif
                            @if(!$creditTx && !$feeTx)
                                —
                            @endif
                        </td>
                        <td class="px-3 py-3 text-slate-700 max-w-[140px] truncate" title="{{ $payment->user?->name }}">{{ $payment->user?->name ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-12 text-center text-slate-500">لا توجد عمليات مطابقة. جرّب توسيع نطاق التاريخ أو إزالة الفلاتر.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">{{ $payments->links() }}</div>
        @endif
    </section>
</div>
@endsection
