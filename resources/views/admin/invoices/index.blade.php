@extends('layouts.admin')

@section('title', 'إدارة الفواتير - Mindlytics')
@section('header', 'إدارة الفواتير')

@section('content')
@php
    $typeLabels = [
        'course' => 'كورس أونلاين',
        'subscription' => 'اشتراك',
        'membership' => 'عضوية',
        'learning_path' => 'مسار تعليمي',
        'offline_course' => 'كورس أوفلاين',
        'other' => 'أخرى',
    ];
    $statCards = [
        ['label' => 'إجمالي الفواتير', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-file-invoice', 'theme' => 'sky', 'desc' => 'كل الفواتير المسجلة', 'filter' => []],
        ['label' => 'معلقة', 'value' => number_format($stats['pending'] ?? 0), 'icon' => 'fas fa-hourglass-half', 'theme' => 'amber', 'desc' => 'بانتظار الدفع', 'filter' => ['status' => 'pending']],
        ['label' => 'مدفوعة', 'value' => number_format($stats['paid'] ?? 0), 'icon' => 'fas fa-check-circle', 'theme' => 'emerald', 'desc' => 'تم دفعها', 'filter' => ['status' => 'paid']],
        ['label' => 'متأخرة', 'value' => number_format($stats['overdue'] ?? 0), 'icon' => 'fas fa-exclamation-triangle', 'theme' => 'rose', 'desc' => 'تجاوزت الاستحقاق', 'filter' => ['status' => 'overdue']],
    ];
    $statusBadges = [
        'paid' => ['label' => 'مدفوعة', 'classes' => 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200/80'],
        'pending' => ['label' => 'معلقة', 'classes' => 'bg-amber-100 text-amber-900 ring-1 ring-amber-200/80'],
        'overdue' => ['label' => 'متأخرة', 'classes' => 'bg-rose-100 text-rose-800 ring-1 ring-rose-200/80'],
        'partial' => ['label' => 'جزئية', 'classes' => 'bg-sky-100 text-sky-800 ring-1 ring-sky-200/80'],
        'cancelled' => ['label' => 'ملغاة', 'classes' => 'bg-slate-100 text-slate-700 ring-1 ring-slate-200/80'],
        'refunded' => ['label' => 'مستردة', 'classes' => 'bg-violet-100 text-violet-800 ring-1 ring-violet-200/80'],
        'draft' => ['label' => 'مسودة', 'classes' => 'bg-slate-100 text-slate-600 ring-1 ring-slate-200/60'],
    ];
    $sortKeys = ['created_at', 'invoice_number', 'total_amount', 'due_date'];
    $sortLabels = [
        'created_at' => 'تاريخ الإنشاء',
        'invoice_number' => 'رقم الفاتورة',
        'total_amount' => 'المبلغ',
        'due_date' => 'الاستحقاق',
    ];
    $hasFilters = request()->filled('search')
        || request()->filled('status')
        || request()->filled('type')
        || request()->filled('date_from')
        || request()->filled('date_to')
        || (int) request('per_page', 25) !== 25
        || (request('sort') && request('sort') !== 'created_at')
        || (request('dir') && request('dir') !== 'desc');
@endphp

<div class="space-y-6">
    {{-- الهيدر + إحصائيات قابلة للنقر --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">لوحة إدارة الفواتير</h2>
                    <p class="text-xs text-slate-600">جدول منظم، بحث وفلترة متقدمة لأعداد كبيرة.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if(Route::has('admin.accounting.hub'))
                <a href="{{ route('admin.accounting.hub') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-calculator text-sky-600"></i>
                    مركز المحاسبة
                </a>
                @endif
                <a href="{{ route('admin.invoices.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-500 rounded-xl shadow hover:from-blue-700 hover:to-blue-600 transition-all">
                    <i class="fas fa-plus"></i>
                    إنشاء فاتورة
                </a>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        @php
            $cardThemes = [
                'sky' => ['border' => 'border-sky-200/70', 'bg' => 'from-white via-white to-sky-50/60', 'label' => 'text-sky-800/80', 'value' => 'from-sky-700 to-blue-600', 'icon' => 'from-sky-500 to-blue-600', 'desc' => 'text-sky-700/70'],
                'amber' => ['border' => 'border-amber-200/70', 'bg' => 'from-white via-white to-amber-50/60', 'label' => 'text-amber-800/80', 'value' => 'from-amber-700 to-orange-600', 'icon' => 'from-amber-500 to-orange-500', 'desc' => 'text-amber-700/70'],
                'emerald' => ['border' => 'border-emerald-200/70', 'bg' => 'from-white via-white to-emerald-50/60', 'label' => 'text-emerald-800/80', 'value' => 'from-emerald-700 to-teal-600', 'icon' => 'from-emerald-500 to-teal-600', 'desc' => 'text-emerald-700/70'],
                'rose' => ['border' => 'border-rose-200/70', 'bg' => 'from-white via-white to-rose-50/60', 'label' => 'text-rose-800/80', 'value' => 'from-rose-700 to-red-600', 'icon' => 'from-rose-500 to-red-500', 'desc' => 'text-rose-700/70'],
            ];
        @endphp
        @foreach($statCards as $card)
            @php $theme = $cardThemes[$card['theme']] ?? $cardThemes['sky']; @endphp
            <a href="{{ route('admin.invoices.index', $card['filter']) }}" class="dashboard-stat-card rounded-2xl border-2 {{ $theme['border'] }} bg-gradient-to-br {{ $theme['bg'] }} p-5 shadow-lg hover:shadow-xl transition-shadow block">
                <div class="flex items-center justify-between gap-3 mb-2">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold {{ $theme['label'] }} mb-1">{{ $card['label'] }}</p>
                        <p class="text-2xl font-black bg-gradient-to-r {{ $theme['value'] }} bg-clip-text text-transparent tabular-nums">{{ $card['value'] }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br {{ $theme['icon'] }} flex items-center justify-center text-white shadow-md flex-shrink-0">
                        <i class="{{ $card['icon'] }} text-sm"></i>
                    </div>
                </div>
                <p class="text-xs font-medium {{ $theme['desc'] }}">{{ $card['desc'] }}</p>
            </a>
        @endforeach
    </div>

    {{-- بحث وفلترة --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-blue-600"></i>
                البحث والفلترة
            </h3>
            <p class="text-xs text-slate-600">رقم الفاتورة، الوصف، اسم العميل، البريد، الهاتف — بالإضافة إلى النوع والفترة والترتيب.</p>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('admin.invoices.index') }}" id="filterForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
                    <div class="lg:col-span-2 xl:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">بحث</label>
                        <input type="text" name="search" value="{{ request('search') }}" maxlength="255" placeholder="رقم فاتورة، وصف، اسم، بريد، هاتف"
                               class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">الحالة</label>
                        <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">كل الحالات</option>
                            <option value="draft" @selected(request('status') === 'draft')>مسودة</option>
                            <option value="pending" @selected(request('status') === 'pending')>معلقة</option>
                            <option value="partial" @selected(request('status') === 'partial')>مدفوعة جزئياً</option>
                            <option value="paid" @selected(request('status') === 'paid')>مدفوعة</option>
                            <option value="overdue" @selected(request('status') === 'overdue')>متأخرة</option>
                            <option value="cancelled" @selected(request('status') === 'cancelled')>ملغاة</option>
                            <option value="refunded" @selected(request('status') === 'refunded')>مستردة</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">نوع الفاتورة</label>
                        <select name="type" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">كل الأنواع</option>
                            @foreach($typeLabels as $tVal => $tLabel)
                                <option value="{{ $tVal }}" @selected(request('type') === $tVal)>{{ $tLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">من تاريخ (إنشاء)</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">إلى تاريخ (إنشاء)</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500" />
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-end gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">ترتيب حسب</label>
                        <select name="sort" class="w-full sm:w-44 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                            @foreach($sortKeys as $sk)
                                <option value="{{ $sk }}" @selected(request('sort', 'created_at') === $sk)>{{ $sortLabels[$sk] ?? $sk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">الاتجاه</label>
                        <select name="dir" class="w-full sm:w-36 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="desc" @selected(request('dir', 'desc') === 'desc')>الأحدث أولاً</option>
                            <option value="asc" @selected(request('dir') === 'asc')>الأقدم أولاً</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">عدد الصفوف</label>
                        <select name="per_page" class="w-full sm:w-36 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                            @foreach([25, 50, 100] as $n)
                                <option value="{{ $n }}" @selected((int) request('per_page', 25) === $n)>{{ $n }} / صفحة</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-2 sm:mr-auto pt-1">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white transition-colors">
                            <i class="fas fa-search"></i>
                            تطبيق
                        </button>
                        @if($hasFilters)
                            <a href="{{ route('admin.invoices.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                <i class="fas fa-undo"></i>
                                إعادة ضبط
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </section>

    {{-- جدول الفواتير --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 bg-slate-50/80">
            <div>
                <h3 class="text-base font-black text-slate-900">قائمة الفواتير</h3>
                <p class="text-xs text-slate-600">عرض {{ $invoices->firstItem() ?? 0 }}–{{ $invoices->lastItem() ?? 0 }} من إجمالي {{ number_format($invoices->total()) }}</p>
            </div>
            <span class="text-xs font-bold text-blue-700 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-200">{{ number_format($invoices->total()) }} فاتورة</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[960px] w-full text-sm text-slate-800">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wide border-b border-slate-200">
                        <th class="px-3 py-3 text-right w-12 text-slate-500 font-mono">#</th>
                        <th class="px-3 py-3 text-right min-w-[130px]">رقم الفاتورة</th>
                        <th class="px-3 py-3 text-right min-w-[180px]">العميل</th>
                        <th class="px-3 py-3 text-right min-w-[100px]">النوع</th>
                        <th class="px-3 py-3 text-left min-w-[110px]">المبلغ (ج.م)</th>
                        <th class="px-3 py-3 text-center min-w-[100px]">الحالة</th>
                        <th class="px-3 py-3 text-center min-w-[100px]">الاستحقاق</th>
                        <th class="px-3 py-3 text-center min-w-[100px]">الإنشاء</th>
                        <th class="px-3 py-3 text-center min-w-[170px]">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($invoices as $invoice)
                        @php
                            $badge = $statusBadges[$invoice->status] ?? ['label' => $invoice->status, 'classes' => 'bg-slate-100 text-slate-700 ring-1 ring-slate-200'];
                            $isLate = $invoice->due_date && $invoice->due_date->isPast() && ! in_array($invoice->status, ['paid', 'cancelled', 'refunded'], true);
                            $typeLabel = $typeLabels[$invoice->type] ?? ($invoice->type ?? '—');
                        @endphp
                        <tr class="hover:bg-blue-50/40 transition-colors {{ $loop->even ? 'bg-slate-50/30' : 'bg-white' }}">
                            <td class="px-3 py-3 text-slate-400 font-mono text-xs align-middle">{{ $invoices->firstItem() + $loop->index }}</td>
                            <td class="px-3 py-3 align-middle">
                                <span class="font-bold text-slate-900">{{ $invoice->invoice_number }}</span>
                                @if($invoice->description && $invoice->description !== '-')
                                    <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2 max-w-[220px]" title="{{ $invoice->description }}">{{ $invoice->description }}</p>
                                @endif
                            </td>
                            <td class="px-3 py-3 align-middle">
                                <p class="font-semibold text-slate-900">{{ $invoice->user->name ?? '—' }}</p>
                                <p class="text-xs text-slate-500 truncate max-w-[200px]" title="{{ $invoice->user->email ?? '' }}">{{ $invoice->user->email ?? '—' }}</p>
                                @if(!empty($invoice->user->phone))
                                    <p class="text-[11px] text-slate-400 font-mono dir-ltr text-right">{{ $invoice->user->phone }}</p>
                                @endif
                            </td>
                            <td class="px-3 py-3 align-middle">
                                <span class="inline-flex rounded-lg bg-slate-100 text-slate-700 px-2 py-1 text-xs font-medium">{{ $typeLabel }}</span>
                            </td>
                            <td class="px-3 py-3 align-middle text-left font-bold tabular-nums text-slate-900">{{ number_format((float) $invoice->total_amount, 2) }}</td>
                            <td class="px-3 py-3 align-middle text-center">
                                <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-bold {{ $badge['classes'] }}">
                                    {{ $badge['label'] }}
                                </span>
                                @if($isLate)
                                    <span class="block text-[10px] text-rose-600 font-semibold mt-1">تجاوز الاستحقاق</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 align-middle text-center text-slate-700 tabular-nums">
                                {{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '—' }}
                            </td>
                            <td class="px-3 py-3 align-middle text-center text-slate-600 tabular-nums text-xs">
                                {{ $invoice->created_at->format('Y-m-d') }}
                                <span class="block text-[10px] text-slate-400">{{ $invoice->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-3 py-3 align-middle text-center">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.invoices.show', $invoice) }}"
                                       class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-blue-600 hover:bg-blue-50 hover:border-blue-200 transition-colors"
                                       title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.invoices.edit', $invoice) }}"
                                       class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-amber-600 hover:bg-amber-50 hover:border-amber-200 transition-colors"
                                       title="تعديل الفاتورة">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    <form action="{{ route('admin.invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفاتورة؟ لا يمكن التراجع بعد الحذف.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition-colors"
                                                title="حذف الفاتورة">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                                    <i class="fas fa-file-invoice text-2xl"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-900">لا توجد فواتير</p>
                                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">لا توجد نتائج مطابقة للبحث أو الفلتر. جرّب تغيير المعايير أو أنشئ فاتورة جديدة.</p>
                                <a href="{{ route('admin.invoices.create') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white">
                                    <i class="fas fa-plus"></i>
                                    إنشاء فاتورة
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="border-t border-slate-200 px-4 py-3 bg-slate-50/50">
                {{ $invoices->links() }}
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
