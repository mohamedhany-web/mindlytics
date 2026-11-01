@extends('sales::crm.layout')

@section('title', 'اعتماد صفقات الفوز')

@use('App\Domains\Sales\Services\SalesLeadOrderLinker')
@use('App\Domains\Sales\Services\SalesWinCommissionService')
@php
    $indexRoute = 'admin.sales.win-approvals.index';
    $linkerReady = SalesLeadOrderLinker::ready();
    $money = fn ($v) => number_format((float) $v, 2);
@endphp

@section('content')
<div style="display: flex; flex-direction: column; gap: 14px">
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 10px">
        <div>
            <h1 class="crm-title">اعتماد صفقات الفوز</h1>
            <p class="crm-muted" style="margin: 4px 0 0; font-size: 12px">لما موظف المبيعات يقفل صفقة «فوز» بتظهر هنا لحد ما تعتمدها وتصرف العمولة أو ترفضها.</p>
        </div>
        @if(Route::has('admin.sales.commissions.index'))
            <a href="{{ route('admin.sales.commissions.index') }}" class="crm-btn crm-btn-outline"><i class="fas fa-coins"></i> كوميشن المبيعات</a>
        @endif
    </div>

    <section class="crm-card" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr))">
        <div class="crm-stat" style="border-inline-end: 1px solid var(--crm-line)"><b class="crm-warn">{{ number_format($stats['pending']) }}</b><span>بانتظار الاعتماد</span></div>
        <div class="crm-stat" style="border-inline-end: 1px solid var(--crm-line)"><b>{{ $money($stats['pending_value']) }} <small style="font-size: 12px">ج.م</small></b><span>إجمالي قيمة الصفقات</span></div>
        <div class="crm-stat"><b class="crm-good">{{ $money($stats['pending_commission_est']) }} <small style="font-size: 12px">ج.م</small></b><span>العمولة المقدّرة</span></div>
    </section>

    <form method="get" action="{{ route($indexRoute) }}" class="crm-card" style="padding: 14px; display: flex; flex-wrap: wrap; gap: 12px; align-items: end">
        <label class="crm-label" style="flex: 1 1 240px">بحث<input class="crm-input" type="search" name="search" value="{{ request('search') }}" placeholder="اسم، رقم، شركة…"></label>
        <label class="crm-label">الموظف
            <select class="crm-select" name="assigned_to"><option value="">الكل</option>@foreach($reps as $rep)<option value="{{ $rep->id }}" @selected((int) request('assigned_to') === (int) $rep->id)>{{ $rep->name }}</option>@endforeach</select>
        </label>
        <a href="{{ route($indexRoute) }}" class="crm-btn crm-btn-outline">مسح</a>
        <button type="submit" class="crm-btn crm-btn-primary">بحث</button>
    </form>

    <section class="crm-card">
        <div class="crm-toolbar"><span style="flex: 1; font-size: 13px; font-weight: 700">الصفقات بانتظار الاعتماد</span>@include('sales::crm.partials.pager', ['paginator' => $leads])</div>
        <div style="overflow-x: auto">
            <table class="crm-table" style="min-width: 900px">
                <thead><tr><th>الصفقة</th><th>الموظف</th><th>القيمة</th><th>العمولة المقدّرة</th>@if($linkerReady)<th>الطلب</th>@endif<th>تاريخ الفوز</th><th></th></tr></thead>
                <tbody x-data="{ open: null }">
                    @forelse($leads as $lead)
                        @php
                            $defaultCommission = SalesWinCommissionService::defaultCommissionForLead($lead);
                            $rep = $lead->assignee;
                            $order = $linkerReady && $lead->order_id ? $lead->order : null;
                        @endphp
                        <tr>
                            <td>
                                <a class="crm-link" href="{{ route('admin.crm.deals.show', $lead->id) }}">{{ $lead->name }}</a>
                                <div class="crm-muted" style="font-size: 11px">{{ $lead->phone ?? '—' }}@if($lead->company) · {{ $lead->company }}@endif</div>
                            </td>
                            <td>{{ $rep?->name ?? '—' }}@if($rep)<div class="crm-muted" style="font-size: 11px">{{ $rep->salesCommissionLabel() }}</div>@endif</td>
                            <td>{{ $money($lead->expected_value ?? 0) }}</td>
                            <td class="crm-good">{{ $money($defaultCommission) }}</td>
                            @if($linkerReady)
                                <td>@if($order)<span class="crm-good">#{{ $order->id }}</span>@else<span class="crm-warn">من غير طلب</span>@endif</td>
                            @endif
                            <td>{{ ($lead->closed_at ?? $lead->updated_at)?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td style="text-align: end">
                                <button type="button" class="crm-btn crm-btn-sm" :class="open === {{ $lead->id }} ? 'crm-btn-dark' : 'crm-btn-primary'" @click="open = open === {{ $lead->id }} ? null : {{ $lead->id }}" :aria-expanded="open === {{ $lead->id }}">
                                    مراجعة <i class="fas" :class="open === {{ $lead->id }} ? 'fa-caret-up' : 'fa-caret-down'"></i>
                                </button>
                            </td>
                        </tr>
                        <tr x-cloak x-show="open === {{ $lead->id }}">
                            <td colspan="{{ $linkerReady ? 7 : 6 }}" style="background: #F7F7F9; padding: 0">
                                <div style="display: flex; flex-wrap: wrap; gap: 12px; padding: 12px">
                                    <form method="post" action="{{ route('admin.sales.win-approvals.approve', $lead) }}" class="crm-card" style="flex: 2 1 420px; box-shadow: none; border: 1px solid #B9DCC9">
                                        @csrf
                                        <div class="crm-subpanel-head" style="border-bottom-color: var(--crm-good)"><h3><i class="fas fa-check" style="color: var(--crm-good)"></i> اعتماد وصرف العمولة</h3></div>
                                        <div style="padding: 12px; display: flex; flex-direction: column; gap: 10px">
                                            @if($linkerReady)
                                                @if($order)
                                                    @php($netPaid = app(SalesLeadOrderLinker::class)->netPaid($order))
                                                    <div class="crm-alert crm-alert-ok">
                                                        <i class="fas fa-receipt"></i> مربوطة بالطلب
                                                        @if(Route::has('admin.orders.show'))<a class="crm-link" href="{{ route('admin.orders.show', $order) }}">#{{ $order->id }}</a>@else<b>#{{ $order->id }}</b>@endif
                                                        — المدفوع فعلاً: <b>{{ $money($netPaid) }} ج.م</b>@if($netPaid < (float) $order->amount) <span class="crm-bad">(بعد استرداد)</span>@endif
                                                    </div>
                                                @else
                                                    <div class="crm-alert" style="background: #FFF6E5; border-color: #F1D49A; color: #6B4100"><i class="fas fa-triangle-exclamation"></i> مفيش طلب مدفوع مربوط. اربطها برقم الطلب، أو اكتب سبب الاعتماد اليدوي.</div>
                                                    <div style="display: flex; flex-wrap: wrap; gap: 10px">
                                                        <label class="crm-label" style="flex: 1 1 140px">رقم الطلب (اختياري)<input class="crm-input" type="number" min="1" name="order_id"></label>
                                                        <label class="crm-label" style="flex: 3 1 240px">سبب الاعتماد من غير طلب<input class="crm-input" type="text" name="override_reason" maxlength="2000" placeholder="دفع كاش في المقر مثلاً"></label>
                                                    </div>
                                                @endif
                                            @endif
                                            <div style="display: flex; flex-wrap: wrap; gap: 10px">
                                                <label class="crm-label" style="flex: 1 1 140px">مبلغ العمولة (ج.م)<input class="crm-input" type="number" step="0.01" min="0" name="commission_amount" value="{{ number_format($defaultCommission, 2, '.', '') }}"></label>
                                                <label class="crm-label" style="flex: 3 1 240px">ملاحظات (اختياري)<input class="crm-input" type="text" name="commission_notes" maxlength="2000" placeholder="ملاحظة على الاعتماد"></label>
                                            </div>
                                            <div><button type="submit" class="crm-btn" style="background: var(--crm-good)"><i class="fas fa-check"></i> اعتماد وصرف العمولة</button></div>
                                        </div>
                                    </form>
                                    <form method="post" action="{{ route('admin.sales.win-approvals.reject', $lead) }}" class="crm-card" style="flex: 1 1 260px; box-shadow: none; border: 1px solid #F2C9C4">
                                        @csrf
                                        <div class="crm-subpanel-head" style="border-bottom-color: var(--crm-bad)"><h3><i class="fas fa-xmark" style="color: var(--crm-bad)"></i> رفض الفوز</h3></div>
                                        <div style="padding: 12px; display: flex; flex-direction: column; gap: 10px">
                                            <label class="crm-label">سبب الرفض (بيوصل للموظف)<textarea class="crm-input" name="rejection_reason" rows="3" required maxlength="2000" style="height: auto; padding: 6px 8px"></textarea></label>
                                            <p class="crm-muted" style="margin: 0; font-size: 12px">الصفقة بترجع لمرحلة «عرض سعر».</p>
                                            <div><button type="submit" class="crm-btn" style="background: var(--crm-bad)" onclick="return confirm('رفض الفوز وإرجاع الصفقة لـ «عرض سعر»؟')">رفض</button></div>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $linkerReady ? 7 : 6 }}" class="crm-muted" style="text-align: center; padding: 28px"><i class="fas fa-trophy" style="font-size: 22px; color: #C9CDD2; display: block; margin-bottom: 6px"></i>مفيش صفقات فوز بانتظار الاعتماد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
