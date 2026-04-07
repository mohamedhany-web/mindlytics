@extends('layouts.admin')

@section('title', 'تفاصيل الفاتورة ' . $invoice->invoice_number)
@section('header', 'تفاصيل الفاتورة')

@php
    $brandName = config('app.name', 'المنصة');
    $supportEmail = config('services.platform.support_email');
    $supportPhone = config('services.platform.support_phone');
    $appUrl = rtrim((string) config('app.url'), '/');
    $typeLabels = [
        'course' => 'كورس أونلاين',
        'subscription' => 'اشتراك',
        'membership' => 'عضوية',
        'learning_path' => 'مسار تعليمي',
        'offline_course' => 'كورس أوفلاين',
        'other' => 'أخرى',
    ];
    $statusMap = [
        'paid' => ['label' => 'مدفوعة بالكامل', 'class' => 'bg-emerald-100 text-emerald-900 ring-emerald-200'],
        'pending' => ['label' => 'معلقة', 'class' => 'bg-amber-100 text-amber-950 ring-amber-200'],
        'partial' => ['label' => 'مدفوعة جزئياً', 'class' => 'bg-sky-100 text-sky-900 ring-sky-200'],
        'overdue' => ['label' => 'متأخرة', 'class' => 'bg-rose-100 text-rose-900 ring-rose-200'],
        'cancelled' => ['label' => 'ملغاة', 'class' => 'bg-slate-200 text-slate-800 ring-slate-300'],
        'refunded' => ['label' => 'مستردة', 'class' => 'bg-violet-100 text-violet-900 ring-violet-200'],
        'draft' => ['label' => 'مسودة', 'class' => 'bg-slate-100 text-slate-700 ring-slate-200'],
    ];
    $st = $statusMap[$invoice->status] ?? ['label' => $invoice->status, 'class' => 'bg-slate-100 text-slate-800 ring-slate-200'];
    $typeLabel = $typeLabels[$invoice->type] ?? ($invoice->type ?? '—');
    $rawItems = is_array($invoice->items) ? $invoice->items : [];
    $lineItems = [];
    foreach ($rawItems as $row) {
        if (! is_array($row)) {
            continue;
        }
        $lineItems[] = [
            'description' => $row['description'] ?? $row['title'] ?? '—',
            'quantity' => $row['quantity'] ?? 1,
            'unit' => isset($row['unit_price']) ? (float) $row['unit_price'] : (isset($row['price']) ? (float) $row['price'] : null),
            'total' => isset($row['total']) ? (float) $row['total'] : (isset($row['line_total']) ? (float) $row['line_total'] : null),
        ];
    }
@endphp

@section('content')
<div class="space-y-6 invoice-print-wrapper w-full max-w-none">
    {{-- شريط إجراءات (مخفي عند الطباعة) --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden print:hidden" data-print-hide>
        <div class="px-4 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500">فاتورة</p>
                <h2 class="text-xl font-black text-slate-900 tracking-tight">{{ $invoice->invoice_number }}</h2>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.invoices.edit', $invoice) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-edit"></i>
                    تعديل
                </a>
                <button type="button" onclick="window.printInvoice()" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-sky-700">
                    <i class="fas fa-print"></i>
                    طباعة
                </button>
                <a href="{{ route('admin.invoices.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-arrow-right"></i>
                    القائمة
                </a>
            </div>
        </div>
    </section>

    {{-- محتوى الفاتورة (يُطبع) --}}
    <div id="invoice-print-area" class="w-full rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden print:shadow-none print:border-slate-300">
        {{-- ترويسة العلامة — من الإعدادات وليس نصوصاً ثابتة --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-sky-900 text-white px-6 py-8 sm:px-10">
            <div class="absolute inset-0 opacity-[0.07] pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex items-start gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                        <i class="fas fa-file-invoice text-2xl text-white/90"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-white/60">{{ $brandName }}</p>
                        <h1 class="mt-1 text-2xl sm:text-3xl font-black text-white leading-tight">فاتورة / إيصال</h1>
                        <div class="mt-3 space-y-1 text-sm text-white/80">
                            @if($supportEmail)
                                <p class="flex items-center gap-2"><i class="fas fa-envelope w-4 text-center text-white/50"></i> {{ $supportEmail }}</p>
                            @endif
                            @if($supportPhone)
                                <p class="flex items-center gap-2 font-mono dir-ltr text-right"><i class="fas fa-phone w-4 text-center text-white/50"></i> {{ $supportPhone }}</p>
                            @endif
                            @if($appUrl)
                                <p class="flex items-center gap-2 text-xs text-white/60 break-all"><i class="fas fa-link w-4 text-center text-white/40"></i> {{ $appUrl }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl bg-white/10 backdrop-blur-sm ring-1 ring-white/20 px-5 py-4 text-sm min-w-[240px]">
                    <dl class="space-y-2.5">
                        <div class="flex justify-between gap-6 border-b border-white/10 pb-2">
                            <dt class="text-white/60">رقم الفاتورة</dt>
                            <dd class="font-mono font-bold text-white">{{ $invoice->invoice_number }}</dd>
                        </div>
                        <div class="flex justify-between gap-6 border-b border-white/10 pb-2">
                            <dt class="text-white/60">تاريخ الإصدار</dt>
                            <dd class="font-semibold text-white tabular-nums">{{ $invoice->created_at->format('Y-m-d') }}</dd>
                        </div>
                        <div class="flex justify-between gap-6 border-b border-white/10 pb-2">
                            <dt class="text-white/60">الاستحقاق</dt>
                            <dd class="font-semibold text-white tabular-nums">{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '—' }}</dd>
                        </div>
                        @if($invoice->paid_at)
                            <div class="flex justify-between gap-6 border-b border-white/10 pb-2">
                                <dt class="text-white/60">تاريخ السداد</dt>
                                <dd class="font-semibold text-emerald-200 tabular-nums">{{ $invoice->paid_at->format('Y-m-d') }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between gap-6 items-center pt-1">
                            <dt class="text-white/60">الحالة</dt>
                            <dd>
                                <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold bg-white/20 text-white ring-1 ring-white/30">{{ $st['label'] }}</span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-8">
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center gap-2 rounded-xl px-3 py-1.5 text-xs font-bold ring-1 {{ $st['class'] }}">
                    <span class="h-2 w-2 rounded-full bg-current opacity-70"></span>
                    {{ $st['label'] }}
                </span>
                <span class="inline-flex items-center rounded-xl bg-slate-100 text-slate-800 px-3 py-1.5 text-xs font-bold">{{ $typeLabel }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5 sm:p-6">
                    <h3 class="text-sm font-black text-slate-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-user text-sky-600"></i>
                        بيانات العميل
                    </h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between gap-4 border-b border-slate-200/80 pb-2">
                            <dt class="text-slate-500">الاسم</dt>
                            <dd class="font-bold text-slate-900 text-left">{{ $invoice->user->name ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-slate-200/80 pb-2">
                            <dt class="text-slate-500">البريد</dt>
                            <dd class="font-semibold text-slate-800 text-left break-all">{{ $invoice->user->email ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-slate-200/80 pb-2">
                            <dt class="text-slate-500">الهاتف</dt>
                            <dd class="font-mono font-semibold text-slate-800 dir-ltr text-left">{{ $invoice->user->phone ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5 sm:p-6">
                    <h3 class="text-sm font-black text-slate-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-sky-600"></i>
                        تفاصيل إضافية
                    </h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between gap-4 border-b border-slate-200/80 pb-2">
                            <dt class="text-slate-500">آخر تحديث</dt>
                            <dd class="font-semibold text-slate-800 tabular-nums">{{ $invoice->updated_at->format('Y-m-d H:i') }}</dd>
                        </div>
                        @if($invoice->status === 'partial' || (float) $invoice->paid_amount > 0)
                            <div class="flex justify-between gap-4 border-b border-slate-200/80 pb-2">
                                <dt class="text-slate-500">المدفوع</dt>
                                <dd class="font-bold text-emerald-700 tabular-nums">{{ number_format((float) $invoice->paid_amount, 2) }} ج.م</dd>
                            </div>
                            <div class="flex justify-between gap-4 border-b border-slate-200/80 pb-2">
                                <dt class="text-slate-500">المتبقي</dt>
                                <dd class="font-bold text-amber-800 tabular-nums">{{ number_format(max(0, (float) $invoice->remaining_amount), 2) }} ج.م</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-slate-500 mb-1">ملاحظات</dt>
                            <dd class="text-slate-800 leading-relaxed rounded-xl bg-white border border-slate-100 p-3 text-sm">{{ $invoice->notes ? e($invoice->notes) : '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if($invoice->description && $invoice->description !== '-')
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <h3 class="text-sm font-black text-slate-900 mb-2">الوصف</h3>
                    <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap">{{ $invoice->description }}</p>
                </div>
            @endif

            {{-- بنود الفاتورة من JSON --}}
            <div class="rounded-2xl border border-slate-200 overflow-hidden">
                <div class="bg-slate-100 px-5 py-3 border-b border-slate-200">
                    <h3 class="text-sm font-black text-slate-900">البنود</h3>
                </div>
                <div class="overflow-x-auto">
                    @if(count($lineItems) > 0)
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 text-xs font-bold text-slate-600 uppercase tracking-wide border-b border-slate-200">
                                    <th class="px-4 py-3 text-right">البيان</th>
                                    <th class="px-4 py-3 text-center w-24">الكمية</th>
                                    <th class="px-4 py-3 text-left w-32">سعر الوحدة</th>
                                    <th class="px-4 py-3 text-left w-32">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($lineItems as $line)
                                    <tr class="hover:bg-slate-50/80">
                                        <td class="px-4 py-3 text-slate-900 font-medium">{{ $line['description'] }}</td>
                                        <td class="px-4 py-3 text-center tabular-nums text-slate-700">{{ $line['quantity'] }}</td>
                                        <td class="px-4 py-3 text-left tabular-nums text-slate-800">{{ $line['unit'] !== null ? number_format($line['unit'], 2) . ' ج.م' : '—' }}</td>
                                        <td class="px-4 py-3 text-left font-bold tabular-nums text-slate-900">{{ $line['total'] !== null ? number_format($line['total'], 2) . ' ج.م' : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="px-5 py-8 text-center text-sm text-slate-500">
                            لا توجد بنود مفصّلة في JSON — يُعرض الملخص المالي أدناه بناءً على حقول الفاتورة.
                        </div>
                    @endif
                </div>
            </div>

            {{-- ملخص المبالغ --}}
            <div class="rounded-2xl border-2 border-slate-200 bg-gradient-to-br from-slate-50 to-white p-6 sm:p-8">
                <h3 class="text-base font-black text-slate-900 mb-4">ملخص المبالغ</h3>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-slate-200">
                        <tr>
                            <td class="py-3 text-slate-600">المبلغ الفرعي</td>
                            <td class="py-3 text-left font-bold tabular-nums text-slate-900">{{ number_format((float) $invoice->subtotal, 2) }} ج.م</td>
                        </tr>
                        @if((float) $invoice->tax_amount > 0)
                            <tr>
                                <td class="py-3 text-slate-600">الضريبة</td>
                                <td class="py-3 text-left font-bold tabular-nums text-slate-900">{{ number_format((float) $invoice->tax_amount, 2) }} ج.م</td>
                            </tr>
                        @endif
                        @if((float) $invoice->discount_amount > 0)
                            <tr>
                                <td class="py-3 text-slate-600">الخصم</td>
                                <td class="py-3 text-left font-bold tabular-nums text-rose-600">− {{ number_format((float) $invoice->discount_amount, 2) }} ج.م</td>
                            </tr>
                        @endif
                        <tr class="bg-sky-50/80">
                            <td class="py-4 text-base font-black text-slate-900">الإجمالي</td>
                            <td class="py-4 text-left text-xl font-black text-sky-700 tabular-nums">{{ number_format((float) $invoice->total_amount, 2) }} ج.م</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($invoice->payments && $invoice->payments->count() > 0)
                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="bg-slate-100 px-5 py-3 border-b border-slate-200">
                        <h3 class="text-sm font-black text-slate-900">سجل المدفوعات</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-white text-xs font-bold text-slate-500 border-b border-slate-200">
                                    <th class="px-4 py-3 text-right">رقم الدفعة</th>
                                    <th class="px-4 py-3 text-center">التاريخ</th>
                                    <th class="px-4 py-3 text-center">الطريقة</th>
                                    <th class="px-4 py-3 text-center">الحالة</th>
                                    <th class="px-4 py-3 text-left">المبلغ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($invoice->payments as $payment)
                                    <tr class="hover:bg-slate-50/80">
                                        <td class="px-4 py-3 font-mono font-semibold text-slate-900">{{ $payment->payment_number }}</td>
                                        <td class="px-4 py-3 text-center tabular-nums text-slate-700">{{ $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : '—' }}</td>
                                        <td class="px-4 py-3 text-center text-slate-600">{{ $payment->payment_method ?? '—' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-xs font-semibold rounded-lg px-2 py-0.5 {{ $payment->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">{{ $payment->status ?? '—' }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-left font-bold tabular-nums">{{ number_format((float) $payment->amount, 2) }} ج.م</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <p class="text-center text-[11px] text-slate-400 print:mt-6">
                وثيقة مُولَّدة آلياً — {{ $brandName }} — {{ now()->format('Y-m-d H:i') }}
            </p>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body * { visibility: hidden; }
        .invoice-print-wrapper, .invoice-print-wrapper * { visibility: visible; }
        .invoice-print-wrapper { position: absolute; left: 0; top: 0; width: 100%; }
        [data-print-hide] { display: none !important; }
    }
    @page { margin: 12mm; size: A4 portrait; }
</style>
@endpush

@push('scripts')
<script>
window.printInvoice = function () {
    window.print();
};
</script>
@endpush
@endsection
