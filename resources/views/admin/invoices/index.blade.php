@extends('layouts.admin')

@section('title', 'إدارة الفواتير - Mindlytics')
@section('header', 'إدارة الفواتير')

@section('content')
@php
    $statCards = [
        ['label' => 'إجمالي الفواتير', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-file-invoice', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'description' => 'كل الفواتير المسجلة'],
        ['label' => 'معلقة', 'value' => number_format($stats['pending'] ?? 0), 'icon' => 'fas fa-hourglass-half', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => 'بإنتظار الدفع'],
        ['label' => 'مدفوعة', 'value' => number_format($stats['paid'] ?? 0), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'تم دفعها'],
        ['label' => 'متأخرة', 'value' => number_format($stats['overdue'] ?? 0), 'icon' => 'fas fa-exclamation-triangle', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'description' => 'تجاوزت الاستحقاق'],
    ];
    $statusBadges = [
        'paid' => ['label' => 'مدفوعة', 'classes' => 'bg-emerald-100 text-emerald-700 border border-emerald-200'],
        'pending' => ['label' => 'معلقة', 'classes' => 'bg-amber-100 text-amber-700 border border-amber-200'],
        'overdue' => ['label' => 'متأخرة', 'classes' => 'bg-rose-100 text-rose-700 border border-rose-200'],
        'partial' => ['label' => 'مدفوعة جزئياً', 'classes' => 'bg-sky-100 text-sky-700 border border-sky-200'],
        'cancelled' => ['label' => 'ملغاة', 'classes' => 'bg-slate-100 text-slate-700 border border-slate-200'],
        'refunded' => ['label' => 'مستردة', 'classes' => 'bg-violet-100 text-violet-700 border border-violet-200'],
        'draft' => ['label' => 'مسودة', 'classes' => 'bg-slate-100 text-slate-600 border border-slate-200'],
    ];
@endphp

<div class="space-y-6">
    {{-- 1. الهيدر + إحصائيات --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">لوحة إدارة الفواتير</h2>
                    <p class="text-xs text-slate-600">متابعة الفواتير والمدفوعات وحالة الاستحقاق.</p>
                </div>
            </div>
            <a href="{{ route('admin.invoices.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-500 rounded-xl shadow hover:from-blue-700 hover:to-blue-600 transition-all">
                <i class="fas fa-plus"></i>
                إنشاء فاتورة
            </a>
        </div>
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
            @foreach ($statCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow transition-shadow">
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
                </div>
            @endforeach
        </div>
    </section>

    {{-- 2. البحث والفلترة --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-blue-600"></i>
                البحث والفلترة
            </h3>
            <p class="text-xs text-slate-600">حسب الحالة أو رقم الفاتورة أو بيانات العميل.</p>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('admin.invoices.index') }}" id="filterForm" class="flex flex-col gap-3 sm:flex-row sm:items-end sm:flex-wrap">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">البحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" maxlength="255" placeholder="رقم الفاتورة، اسم العميل، هاتف" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                </div>
                <div class="w-full sm:w-auto min-w-[160px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الحالة</label>
                    <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>متأخرة</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition-colors">
                        <i class="fas fa-search"></i>
                        تطبيق
                    </button>
                    @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.invoices.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" title="مسح الفلتر">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    {{-- 3. قائمة الفواتير --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900">الفواتير</h3>
                <p class="text-xs text-slate-600">من الأحدث إلى الأقدم.</p>
            </div>
            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200">{{ $invoices->total() }} فاتورة</span>
        </div>
        <div class="overflow-x-auto">
            <div class="p-3 space-y-1.5">
                @forelse ($invoices as $invoice)
                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 hover:border-blue-200 hover:bg-white transition-all overflow-hidden">
                        <div class="px-3 py-2.5">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                                <div class="w-9 h-9 flex-shrink-0 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                                    <i class="fas fa-file-invoice text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0 space-y-1">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">{{ $invoice->invoice_number }}</p>
                                            <p class="text-xs text-slate-600">{{ $invoice->user->name ?? '—' }} · {{ $invoice->user->phone ?? $invoice->user->email ?? '—' }}</p>
                                        </div>
                                        @php $badge = $statusBadges[$invoice->status] ?? ['label' => $invoice->status, 'classes' => 'bg-slate-100 text-slate-700 border border-slate-200']; @endphp
                                        <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-semibold {{ $badge['classes'] }}">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                            {{ $badge['label'] }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600">
                                        <span><i class="fas fa-coins text-blue-500 ml-0.5"></i> <strong>{{ number_format($invoice->total_amount, 2) }}</strong> ج.م</span>
                                        <span><i class="fas fa-calendar text-slate-500 ml-0.5"></i> {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '—' }}</span>
                                        @if($invoice->due_date && $invoice->due_date->isPast() && $invoice->status != 'paid')
                                            <span class="text-rose-600"><i class="fas fa-exclamation-triangle ml-0.5"></i> متأخرة</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <a href="{{ route('admin.invoices.show', $invoice) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 bg-white text-blue-600 hover:bg-blue-50 text-sm" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-slate-200 bg-white p-10 text-center">
                        <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                            <i class="fas fa-file-invoice text-xl"></i>
                        </div>
                        <p class="text-sm font-semibold text-slate-900">لا توجد فواتير</p>
                        <p class="text-xs text-slate-500 mt-1">لم يتم إنشاء أي فواتير أو لا توجد نتائج للفلتر.</p>
                        <a href="{{ route('admin.invoices.create') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition-colors">
                            <i class="fas fa-plus"></i>
                            إنشاء فاتورة
                        </a>
                    </div>
                @endforelse
            </div>
            @if($invoices->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $invoices->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </section>
</div>

<script>
document.getElementById('filterForm') && document.getElementById('filterForm').addEventListener('submit', function() {
    var q = this.querySelector('input[name="search"]');
    if (q) q.value = (q.value || '').replace(/[<>'"&]/g, '').trim();
});
</script>
@endsection
