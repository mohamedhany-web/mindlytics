@extends('layouts.admin')

@section('title', 'كوميشن — '.$user->name)
@section('header', 'كوميشن — '.$user->name)

@section('content')
@php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500';
    $hasFilters = request()->hasAny(['view', 'year_month']);
    $statCards = [
        ['label' => 'عملاء معتمدون بالكامل', 'value' => number_format($stats['confirmed_wins']), 'icon' => 'fas fa-user-check', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => $periodLabel],
        ['label' => 'كوميشن محقّق', 'value' => number_format($stats['commission_from_leads'], 2).' ج.م', 'icon' => 'fas fa-coins', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => $stats['rate_pct'] !== null ? 'نسبة '.number_format($stats['rate_pct'], 2).'%' : '—'],
        ['label' => 'قيمة الصفقات', 'value' => number_format($stats['expected_confirmed'], 2).' ج.م', 'icon' => 'fas fa-chart-line', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => 'expected_value'],
        ['label' => 'معلّق بانتظار الاعتماد', 'value' => number_format($stats['pending_wins']), 'icon' => 'fas fa-clock', 'bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'description' => number_format($stats['pending_estimated'], 2).' ج.م تقدير'],
    ];
@endphp

<div class="w-full space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="min-w-0">
                    <h2 class="text-xl font-black text-slate-900 truncate">{{ $user->name }}</h2>
                    <p class="text-xs text-slate-600">
                        إعداد الكوميشن:
                        <span class="font-semibold text-slate-800">{{ $user->salesCommissionLabel() }}</span>
                        · العملاء المعتمدون بالكامل بعد موافقة الإدارة على الفوز
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.sales.commissions.index', request()->only(['view', 'year_month'])) }}"
                   class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-arrow-right"></i>
                    رجوع للكوميشن
                </a>
                <a href="{{ route('admin.sales.win-approvals.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-trophy text-amber-600"></i>
                    موافقة Win
                </a>
                <a href="{{ route('admin.sales.leads.index', ['assigned_to' => $user->id]) }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-user-tag text-emerald-600"></i>
                    كل عملاء الموظف
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
            @foreach($statCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate">{{ $card['label'] }}</p>
                            <p class="text-lg font-black text-slate-900 truncate tabular-nums">{{ $card['value'] }}</p>
                        </div>
                        <div class="w-9 h-9 rounded-lg {{ $card['bg'] }} flex items-center justify-center {{ $card['text'] }} flex-shrink-0">
                            <i class="{{ $card['icon'] }} text-sm"></i>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1 truncate">{{ $card['description'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    @if($tierBreakdown)
        <section class="rounded-2xl bg-white border border-violet-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-violet-200 bg-violet-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h3 class="text-base font-black text-violet-950 flex items-center gap-2">
                        <i class="fas fa-layer-group text-violet-600"></i>
                        حسابات Tier System
                    </h3>
                    <p class="text-xs text-violet-800">
                        عدّ الـ Wins:
                        {{ ($tierBreakdown['period'] ?? 'month') === 'all' ? 'تراكمي' : 'شهري' }}
                        · حسب ترتيب الاعتماد
                    </p>
                </div>
                <a href="{{ route('admin.sales.kpi.targets', ['user_id' => $user->id]) }}"
                   class="text-xs font-semibold text-violet-800 bg-white px-2.5 py-1 rounded-lg border border-violet-200 hover:bg-violet-50">
                    تعديل الشرائح
                </a>
            </div>
            <div class="p-4 grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="rounded-xl border border-slate-200 bg-white p-3">
                    <p class="text-[11px] text-slate-500">Wins معتمدة (الفترة المعروضة)</p>
                    <p class="text-lg font-black text-slate-900 tabular-nums">{{ $tierBreakdown['wins'] }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-3">
                    <p class="text-[11px] text-slate-500">عمولة تدريجية</p>
                    <p class="text-lg font-black text-emerald-700 tabular-nums">{{ number_format($tierBreakdown['progressive_commission'], 2) }} ج.م</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-3">
                    <p class="text-[11px] text-slate-500">بونص معالم</p>
                    <p class="text-lg font-black text-amber-700 tabular-nums">{{ number_format($tierBreakdown['milestones_bonus'], 2) }} ج.م</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-3">
                    <p class="text-[11px] text-slate-500">الإجمالي المتوقع (Tier)</p>
                    <p class="text-lg font-black text-violet-700 tabular-nums">{{ number_format($tierBreakdown['progressive_total'], 2) }} ج.م</p>
                </div>
            </div>
            <div class="px-4 pb-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="rounded-xl border border-slate-200 overflow-hidden">
                    <div class="px-3 py-2 bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-700">جدول الشرائح</div>
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-slate-600 border-b border-slate-100">
                                <th class="px-3 py-2 text-right">المبيعات</th>
                                <th class="px-3 py-2 text-center">عمولة</th>
                                <th class="px-3 py-2 text-center">بونص</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($tierBreakdown['tiers'] as $tier)
                                @php
                                    $label = $tier['max'] === null
                                        ? ($tier['min'].'+')
                                        : ($tier['min'].' – '.$tier['max']);
                                    $isCurrent = $tierBreakdown['current_tier']
                                        && (int) $tierBreakdown['current_tier']['min'] === (int) $tier['min'];
                                @endphp
                                <tr class="{{ $isCurrent ? 'bg-violet-50' : '' }}">
                                    <td class="px-3 py-2 font-semibold {{ $isCurrent ? 'text-violet-800' : 'text-slate-800' }}">
                                        {{ $label }}
                                        @if($isCurrent)<span class="text-[10px] text-violet-600">(الحالية)</span>@endif
                                    </td>
                                    <td class="px-3 py-2 text-center tabular-nums">{{ number_format($tier['rate'], 2) }}</td>
                                    <td class="px-3 py-2 text-center tabular-nums text-xs">
                                        @if(($tier['bonus'] ?? 0) > 0 && $tier['bonus_at'])
                                            {{ number_format($tier['bonus'], 2) }} عند {{ $tier['bonus_at'] }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 space-y-2">
                    <p class="text-sm font-black text-emerald-900">البيع التالي (#{{ $tierBreakdown['next_sale_number'] }})</p>
                    <p class="text-xs text-emerald-800">
                        عمولة: <strong>{{ number_format($tierBreakdown['next_rate'], 2) }} ج.م</strong>
                        @if($tierBreakdown['next_bonus'] > 0)
                            + بونص <strong>{{ number_format($tierBreakdown['next_bonus'], 2) }} ج.م</strong>
                        @endif
                        = <strong>{{ number_format($tierBreakdown['next_rate'] + $tierBreakdown['next_bonus'], 2) }} ج.م</strong>
                    </p>
                    <p class="text-[11px] text-slate-600">
                        المدفوع فعلياً على الـ leads المعروضة: {{ number_format($tierBreakdown['paid_commission'], 2) }} ج.م
                    </p>
                </div>
            </div>
        </section>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-sky-600"></i>
                الفترة
            </h3>
        </div>
        <div class="p-4">
            <form method="get" action="{{ route('admin.sales.commissions.show', $user) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">العرض</label>
                    <select name="view" id="view_sel" class="{{ $inputClass }}">
                        <option value="all" @selected($view === 'all')>كل الفترات</option>
                        <option value="month" @selected($view === 'month')>حسب شهر الاعتماد</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الشهر</label>
                    <input type="month" name="year_month" id="year_month" value="{{ $yearMonth }}" class="{{ $inputClass }}" {{ $view === 'all' ? 'disabled' : '' }}>
                </div>
                <div class="flex items-center gap-2 sm:col-span-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 px-4 py-2 text-sm font-semibold text-white">
                        <i class="fas fa-search"></i>
                        تطبيق
                    </button>
                    @if($hasFilters)
                        <a href="{{ route('admin.sales.commissions.show', $user) }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
            <p class="text-xs text-slate-500 mt-3">الفترة المعروضة: <strong>{{ $periodLabel }}</strong></p>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900">العملاء المعتمدون بالكامل</h3>
                <p class="text-xs text-slate-600">اضغط على اسم العميل لفتح بياناته الكاملة.</p>
            </div>
            <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">{{ $confirmedLeads->count() }} عميل</span>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="min-w-full w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                        @if($tierBreakdown)
                            <th class="px-4 py-3 text-center font-semibold"># بيع</th>
                        @endif
                        <th class="px-4 py-3 text-right font-semibold">العميل</th>
                        <th class="px-4 py-3 text-center font-semibold">الكورس</th>
                        <th class="px-4 py-3 text-center font-semibold">الهاتف</th>
                        <th class="px-4 py-3 text-center font-semibold">التصنيف</th>
                        <th class="px-4 py-3 text-center font-semibold">قيمة الصفقة</th>
                        @if($tierBreakdown)
                            <th class="px-4 py-3 text-center font-semibold">سعر الشريحة</th>
                            <th class="px-4 py-3 text-center font-semibold">بونص</th>
                        @endif
                        <th class="px-4 py-3 text-center font-semibold">الكوميشن</th>
                        <th class="px-4 py-3 text-center font-semibold">تاريخ الاعتماد</th>
                        <th class="px-4 py-3 text-center font-semibold">معاملة</th>
                        <th class="px-4 py-3 text-center font-semibold">عرض</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($confirmedLeads as $lead)
                        @php $tierLine = $tierLineByLeadId[$lead->id] ?? null; @endphp
                        <tr class="hover:bg-emerald-50/40">
                            @if($tierBreakdown)
                                <td class="px-4 py-3 text-center font-black text-violet-700 tabular-nums">
                                    {{ $tierLine['sale_number'] ?? '—' }}
                                </td>
                            @endif
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.sales.leads.show', $lead) }}" class="font-bold text-emerald-700 hover:text-emerald-900 hover:underline">
                                    {{ $lead->name }}
                                </a>
                                @if($lead->company)
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $lead->company }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-xs">
                                @if($lead->linkedCourseTitle())
                                    <span class="font-semibold text-slate-800">{{ $lead->linkedCourseTitle() }}</span>
                                    <p class="text-[10px] text-slate-500">{{ $lead->linkedCourseTypeLabel() }}</p>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center font-mono text-xs dir-ltr">{{ $lead->phone ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">{{ $lead->category?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-center tabular-nums font-semibold">{{ number_format((float) ($lead->expected_value ?? 0), 2) }}</td>
                            @if($tierBreakdown)
                                <td class="px-4 py-3 text-center tabular-nums">{{ $tierLine ? number_format($tierLine['rate'], 2) : '—' }}</td>
                                <td class="px-4 py-3 text-center tabular-nums text-amber-700">
                                    {{ $tierLine && $tierLine['milestone_bonus'] > 0 ? number_format($tierLine['milestone_bonus'], 2) : '—' }}
                                </td>
                            @endif
                            <td class="px-4 py-3 text-center tabular-nums font-bold text-emerald-700">{{ number_format((float) ($lead->commission_amount ?? 0), 2) }}</td>
                            <td class="px-4 py-3 text-center tabular-nums text-xs text-slate-600">{{ $lead->won_confirmed_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-xs text-slate-500">
                                @if($lead->commission_transaction_id)
                                    #{{ $lead->commission_transaction_id }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.sales.leads.show', $lead) }}"
                                   class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 text-xs font-semibold text-white">
                                    <i class="fas fa-external-link-alt"></i>
                                    التفاصيل
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $tierBreakdown ? 12 : 9 }}" class="px-4 py-12 text-center">
                                <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                                    <i class="fas fa-user-check text-xl"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-900">لا يوجد عملاء معتمدون في هذه الفترة</p>
                                <p class="text-xs text-slate-500 mt-1">بعد موافقة الإدارة على الفوز يظهر العميل هنا.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($confirmedLeads->isNotEmpty())
                    <tfoot>
                        <tr class="bg-slate-50 border-t border-slate-200 font-bold text-slate-900">
                            <td class="px-4 py-3" colspan="{{ $tierBreakdown ? 5 : 4 }}">الإجمالي ({{ $confirmedLeads->count() }})</td>
                            <td class="px-4 py-3 text-center tabular-nums">{{ number_format($stats['expected_confirmed'], 2) }}</td>
                            @if($tierBreakdown)
                                <td class="px-4 py-3 text-center tabular-nums">{{ number_format($tierBreakdown['progressive_commission'], 2) }}</td>
                                <td class="px-4 py-3 text-center tabular-nums text-amber-700">{{ number_format($tierBreakdown['milestones_bonus'], 2) }}</td>
                            @endif
                            <td class="px-4 py-3 text-center tabular-nums text-emerald-700">{{ number_format($stats['commission_from_leads'], 2) }}</td>
                            <td class="px-4 py-3" colspan="3"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </section>

    @if($pendingLeads->isNotEmpty())
        <section class="rounded-2xl bg-white border border-amber-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-amber-200 bg-amber-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h3 class="text-base font-black text-amber-950">صفقات won بانتظار الاعتماد</h3>
                    <p class="text-xs text-amber-800">لم تُعتمد بعد — لا تُحسب ضمن العملاء المعتمدين بالكامل.</p>
                </div>
                <span class="text-xs font-semibold text-amber-800 bg-white px-2.5 py-1 rounded-lg border border-amber-200">{{ $pendingLeads->count() }} صفقة</span>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="min-w-full w-full text-sm">
                    <thead>
                        <tr class="bg-amber-50/80 text-amber-950 border-b border-amber-200">
                            <th class="px-4 py-3 text-right font-semibold">العميل</th>
                            <th class="px-4 py-3 text-center font-semibold">القيمة</th>
                            <th class="px-4 py-3 text-center font-semibold">تقدير الكوميشن</th>
                            <th class="px-4 py-3 text-center font-semibold">عرض</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-100">
                        @foreach($pendingLeads as $idx => $pl)
                            <tr class="hover:bg-amber-50/50">
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.sales.leads.show', $pl) }}" class="font-semibold text-amber-900 hover:underline">{{ $pl->name }}</a>
                                </td>
                                <td class="px-4 py-3 text-center tabular-nums">{{ number_format((float) ($pl->expected_value ?? 0), 2) }}</td>
                                <td class="px-4 py-3 text-center tabular-nums font-semibold text-amber-800">
                                    @if(($user->sales_commission_mode ?? '') === 'tier')
                                        {{-- تقدير جماعي في الهيدر؛ هنا نعرض البيع التالي كمرجع --}}
                                        @if($idx === 0 && $tierBreakdown)
                                            التالي ≈ {{ number_format($tierBreakdown['next_rate'] + $tierBreakdown['next_bonus'], 2) }}
                                        @else
                                            —
                                        @endif
                                    @else
                                        {{ number_format($user->calculateSalesCommissionAmount((float) ($pl->expected_value ?? 0)), 2) }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('admin.sales.leads.show', $pl) }}" class="text-xs font-semibold text-sky-700 hover:underline">التفاصيل</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</div>

@push('scripts')
<script>
(function () {
    const viewSel = document.getElementById('view_sel');
    const monthInput = document.getElementById('year_month');
    if (!viewSel || !monthInput) return;
    function toggleMonth() {
        monthInput.disabled = viewSel.value === 'all';
    }
    viewSel.addEventListener('change', toggleMonth);
    toggleMonth();
})();
</script>
@endpush
@endsection
