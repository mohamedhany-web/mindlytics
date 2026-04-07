@extends('layouts.admin')

@section('title', 'إدارة المدفوعات - Mindlytics')
@section('header', 'إدارة المدفوعات')

@section('content')
@php
    $statCards = [
        ['label' => 'إجمالي المدفوعات', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-money-bill-wave', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'description' => 'كل المدفوعات', 'filter' => []],
        ['label' => 'مكتملة', 'value' => number_format($stats['completed'] ?? 0), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'تمت بنجاح', 'filter' => ['status' => 'completed']],
        ['label' => 'معلقة', 'value' => number_format($stats['pending'] ?? 0), 'icon' => 'fas fa-hourglass-half', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => 'في انتظار المعالجة', 'filter' => ['status' => 'pending']],
        ['label' => 'إجمالي المبلغ', 'value' => number_format($stats['total_amount'] ?? 0, 2) . ' ج.م', 'icon' => 'fas fa-coins', 'bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'description' => 'قيمة المكتملة', 'filter' => null],
    ];
    $statusBadges = [
        'completed' => ['label' => 'مكتملة', 'classes' => 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200/80'],
        'pending' => ['label' => 'معلقة', 'classes' => 'bg-amber-100 text-amber-900 ring-1 ring-amber-200/80'],
        'processing' => ['label' => 'قيد المعالجة', 'classes' => 'bg-sky-100 text-sky-800 ring-1 ring-sky-200/80'],
        'failed' => ['label' => 'فاشلة', 'classes' => 'bg-rose-100 text-rose-800 ring-1 ring-rose-200/80'],
        'cancelled' => ['label' => 'ملغاة', 'classes' => 'bg-slate-100 text-slate-700 ring-1 ring-slate-200/80'],
        'refunded' => ['label' => 'مستردة', 'classes' => 'bg-violet-100 text-violet-800 ring-1 ring-violet-200/80'],
    ];
    $paymentMethodLabels = [
        'cash' => 'نقدي',
        'card' => 'بطاقة',
        'bank_transfer' => 'تحويل بنكي',
        'online' => 'دفع إلكتروني',
        'wallet' => 'محفظة',
        'other' => 'أخرى',
    ];
    $sortKeys = ['created_at', 'payment_number', 'amount', 'paid_at'];
    $sortLabels = [
        'created_at' => 'تاريخ الإنشاء',
        'payment_number' => 'رقم الدفعة',
        'amount' => 'المبلغ',
        'paid_at' => 'تاريخ الدفع',
    ];
    $hasFilters = request()->filled('search')
        || request()->filled('status')
        || request()->filled('date_from')
        || request()->filled('date_to')
        || (int) request('per_page', 25) !== 25
        || (request('sort') && request('sort') !== 'created_at')
        || (request('dir') && request('dir') !== 'desc');
@endphp

<div class="space-y-6">
    {{-- الهيدر + إحصائيات --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">لوحة إدارة المدفوعات</h2>
                    <p class="text-xs text-slate-600">جدول منظم، بحث وفلترة متقدمة لأعداد كبيرة.</p>
                </div>
            </div>
            <a href="{{ route('admin.payments.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-emerald-500 rounded-xl shadow hover:from-emerald-700 hover:to-emerald-600 transition-all">
                <i class="fas fa-plus"></i>
                إضافة دفعة
            </a>
        </div>
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
            @foreach ($statCards as $card)
                @if($card['filter'] === null)
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm text-right">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-slate-600 truncate">{{ $card['label'] }}</p>
                                <p class="text-xl font-black text-slate-900 truncate">{{ $card['value'] }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-lg {{ $card['bg'] }} flex items-center justify-center {{ $card['text'] }} flex-shrink-0">
                                <i class="{{ $card['icon'] }} text-sm"></i>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 truncate">{{ $card['description'] }}</p>
                    </div>
                @else
                    <a href="{{ route('admin.payments.index', $card['filter']) }}" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all block text-right">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-slate-600 truncate">{{ $card['label'] }}</p>
                                <p class="text-xl font-black text-slate-900">{{ $card['value'] }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-lg {{ $card['bg'] }} flex items-center justify-center {{ $card['text'] }} flex-shrink-0">
                                <i class="{{ $card['icon'] }} text-sm"></i>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 truncate">{{ $card['description'] }}</p>
                    </a>
                @endif
            @endforeach
        </div>
    </section>

    {{-- بحث وفلترة --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-emerald-600"></i>
                البحث والفلترة
            </h3>
            <p class="text-xs text-slate-600">رقم الدفعة، المرجع، اسم العميل، البريد، الهاتف — بالإضافة إلى الحالة والفترة والترتيب.</p>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('admin.payments.index') }}" id="filterForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
                    <div class="lg:col-span-2 xl:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">بحث</label>
                        <input type="text" name="search" value="{{ request('search') }}" maxlength="255" placeholder="رقم دفعة، مرجع، اسم، بريد، هاتف"
                               class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">الحالة</label>
                        <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500">
                            <option value="">كل الحالات</option>
                            <option value="completed" @selected(request('status') === 'completed')>مكتملة</option>
                            <option value="pending" @selected(request('status') === 'pending')>معلقة</option>
                            <option value="processing" @selected(request('status') === 'processing')>قيد المعالجة</option>
                            <option value="failed" @selected(request('status') === 'failed')>فاشلة</option>
                            <option value="cancelled" @selected(request('status') === 'cancelled')>ملغاة</option>
                            <option value="refunded" @selected(request('status') === 'refunded')>مستردة</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">من تاريخ (إنشاء)</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">إلى تاريخ (إنشاء)</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500" />
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-end gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">ترتيب حسب</label>
                        <select name="sort" class="w-full sm:w-44 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500">
                            @foreach($sortKeys as $sk)
                                <option value="{{ $sk }}" @selected(request('sort', 'created_at') === $sk)>{{ $sortLabels[$sk] ?? $sk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">الاتجاه</label>
                        <select name="dir" class="w-full sm:w-36 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500">
                            <option value="desc" @selected(request('dir', 'desc') === 'desc')>الأحدث أولاً</option>
                            <option value="asc" @selected(request('dir') === 'asc')>الأقدم أولاً</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">عدد الصفوف</label>
                        <select name="per_page" class="w-full sm:w-36 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500">
                            @foreach([25, 50, 100] as $n)
                                <option value="{{ $n }}" @selected((int) request('per_page', 25) === $n)>{{ $n }} / صفحة</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-2 sm:mr-auto pt-1">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition-colors">
                            <i class="fas fa-search"></i>
                            تطبيق
                        </button>
                        @if($hasFilters)
                            <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                <i class="fas fa-undo"></i>
                                إعادة ضبط
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </section>

    {{-- جدول المدفوعات --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 bg-slate-50/80">
            <div>
                <h3 class="text-base font-black text-slate-900">قائمة المدفوعات</h3>
                <p class="text-xs text-slate-600">عرض {{ $payments->firstItem() ?? 0 }}–{{ $payments->lastItem() ?? 0 }} من إجمالي {{ number_format($payments->total()) }}</p>
            </div>
            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200">{{ number_format($payments->total()) }} دفعة</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[960px] w-full text-sm text-slate-800">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wide border-b border-slate-200">
                        <th class="px-3 py-3 text-right w-12 text-slate-500 font-mono">#</th>
                        <th class="px-3 py-3 text-right min-w-[130px]">رقم الدفعة</th>
                        <th class="px-3 py-3 text-right min-w-[180px]">العميل</th>
                        <th class="px-3 py-3 text-right min-w-[110px]">طريقة الدفع</th>
                        <th class="px-3 py-3 text-left min-w-[110px]">المبلغ (ج.م)</th>
                        <th class="px-3 py-3 text-center min-w-[100px]">الحالة</th>
                        <th class="px-3 py-3 text-center min-w-[100px]">تاريخ الدفع</th>
                        <th class="px-3 py-3 text-center min-w-[100px]">الإنشاء</th>
                        <th class="px-3 py-3 text-center w-24">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($payments as $payment)
                        @php
                            $badge = $statusBadges[$payment->status] ?? ['label' => $payment->status, 'classes' => 'bg-slate-100 text-slate-700 ring-1 ring-slate-200'];
                            $methodLabel = $paymentMethodLabels[$payment->payment_method] ?? ($payment->payment_method ?? '—');
                        @endphp
                        <tr class="hover:bg-emerald-50/40 transition-colors {{ $loop->even ? 'bg-slate-50/30' : 'bg-white' }}">
                            <td class="px-3 py-3 text-slate-400 font-mono text-xs align-middle">{{ $payments->firstItem() + $loop->index }}</td>
                            <td class="px-3 py-3 align-middle">
                                <span class="font-bold text-slate-900">{{ $payment->payment_number }}</span>
                                @if($payment->reference_number)
                                    <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2 max-w-[220px]" title="{{ $payment->reference_number }}">مرجع: {{ $payment->reference_number }}</p>
                                @endif
                                @if($payment->invoice)
                                    <p class="text-[11px] text-slate-500 mt-0.5">فاتورة: {{ $payment->invoice->invoice_number ?? '—' }}</p>
                                @endif
                            </td>
                            <td class="px-3 py-3 align-middle">
                                <p class="font-semibold text-slate-900">{{ $payment->user->name ?? '—' }}</p>
                                <p class="text-xs text-slate-500 truncate max-w-[200px]" title="{{ $payment->user->email ?? '' }}">{{ $payment->user->email ?? '—' }}</p>
                                @if(!empty($payment->user->phone))
                                    <p class="text-[11px] text-slate-400 font-mono dir-ltr text-right">{{ $payment->user->phone }}</p>
                                @endif
                            </td>
                            <td class="px-3 py-3 align-middle">
                                <span class="inline-flex rounded-lg bg-slate-100 text-slate-700 px-2 py-1 text-xs font-medium">{{ $methodLabel }}</span>
                            </td>
                            <td class="px-3 py-3 align-middle text-left font-bold tabular-nums text-slate-900">{{ number_format((float) $payment->amount, 2) }}</td>
                            <td class="px-3 py-3 align-middle text-center">
                                <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-bold {{ $badge['classes'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="px-3 py-3 align-middle text-center text-slate-700 tabular-nums text-xs">
                                {{ $payment->paid_at ? $payment->paid_at->format('Y-m-d') : '—' }}
                                @if($payment->paid_at)
                                    <span class="block text-[10px] text-slate-400">{{ $payment->paid_at->format('H:i') }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 align-middle text-center text-slate-600 tabular-nums text-xs">
                                {{ $payment->created_at->format('Y-m-d') }}
                                <span class="block text-[10px] text-slate-400">{{ $payment->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-3 py-3 align-middle text-center">
                                <a href="{{ route('admin.payments.show', $payment) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-emerald-600 hover:bg-emerald-50 hover:border-emerald-200 transition-colors" title="عرض التفاصيل">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                                    <i class="fas fa-money-bill-wave text-2xl"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-900">لا توجد مدفوعات</p>
                                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">لا توجد نتائج مطابقة للبحث أو الفلتر. جرّب تغيير المعايير أو أضف دفعة جديدة.</p>
                                <a href="{{ route('admin.payments.create') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white">
                                    <i class="fas fa-plus"></i>
                                    إضافة دفعة
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="border-t border-slate-200 px-4 py-3 bg-slate-50/50">
                {{ $payments->links() }}
            </div>
        @endif
    </section>
</div>

<script>
document.getElementById('filterForm')?.addEventListener('submit', function () {
    var q = this.querySelector('input[name="search"]');
    if (q) q.value = (q.value || '').replace(/[<>'"&]/g, '').trim();
});
</script>
@endsection
