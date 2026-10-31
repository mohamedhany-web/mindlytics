@extends('sales::crm.layout')

@section('title', ($crmModule ?? '') === 'opportunities' ? 'الفرص' : 'الصفقات')

@use('App\Domains\Sales\Models\SalesLead')
@php
    $indexRoute = ($crmModule ?? '') === 'opportunities' ? $crm->route('opportunities.index') : $crm->route('deals.index');
    $q = fn (array $extra) => route($indexRoute, array_filter(array_merge(request()->except('page'), $extra), fn ($v) => $v !== null && $v !== ''));
    $quicks = ['open' => 'مفتوحة', 'won_pending' => 'فاز — بانتظار اعتماد العمولة', 'won_month' => 'فاز هذا الشهر', 'refunded' => 'عمولة مسترجعة', 'all' => 'الكل'];
    $stageOrder = \App\Domains\Sales\Http\Controllers\Crm\SalesCrmController::DEAL_STAGES;
    $maxV = max(1, (float) $byStage->max('v'));
    $commissionLabel = function ($l) {
        if ($l->commission_reversed_amount > 0) {
            return [number_format((float) $l->commission_amount, 0).' — مسترجع '.number_format((float) $l->commission_reversed_amount, 0), 'crm-bad'];
        }
        if ($l->commission_settled_at) {
            return [number_format((float) $l->commission_amount, 0).' — مصروفة', 'crm-good'];
        }
        if ($l->won_confirmed_at) {
            return [number_format((float) $l->commission_amount, 0).' — معتمدة', 'crm-good'];
        }

        return $l->stage === SalesLead::WON_STAGE ? ['بانتظار الاعتماد', 'crm-warn'] : ['—', 'crm-muted'];
    };
@endphp

@section('content')
<div x-data="{ filter: {{ request()->filled('q') || request()->filled('assigned_to') ? 'true' : 'false' }} }" style="display: flex; flex-direction: column; gap: 14px">
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 10px">
        <h1 class="crm-title">الصفقات</h1>
        <button type="button" class="crm-btn crm-btn-slate" @click="filter = ! filter" :aria-expanded="filter">فلتر</button>
    </div>

    <form method="get" action="{{ route($indexRoute) }}" class="crm-card" x-cloak x-show="filter" style="padding: 14px; display: flex; flex-wrap: wrap; gap: 12px; align-items: end">
        <input type="hidden" name="quick" value="{{ $quick }}">
        <label class="crm-label" style="flex: 1 1 240px">بحث<input class="crm-input" type="search" name="q" value="{{ request('q') }}"></label>
        @if($members->isNotEmpty())
            <label class="crm-label">الموظف
                <select class="crm-select" name="assigned_to"><option value="">الكل</option>@foreach($members as $m)<option value="{{ $m->id }}" @selected((int) request('assigned_to') === (int) $m->id)>{{ $m->name }}</option>@endforeach</select>
            </label>
        @endif
        <a href="{{ route($indexRoute) }}" class="crm-btn crm-btn-outline">مسح</a>
        <button type="submit" class="crm-btn crm-btn-primary">بحث</button>
    </form>

    <nav aria-label="فلاتر سريعة" style="display: flex; flex-wrap: wrap; gap: 6px; align-items: center; font-size: 13px">
        <span class="crm-muted">فلاتر سريعة:</span>
        @foreach($quicks as $key => $label)
            <a href="{{ $q(['quick' => $key]) }}" class="crm-chip {{ $quick === $key ? 'is-active' : '' }}">{{ $label }}</a>
        @endforeach
    </nav>

    <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start">
        <section class="crm-card" style="flex: 999 1 720px; min-width: 0">
            <div class="crm-toolbar"><span style="flex: 1"></span>@include('sales::crm.partials.pager', ['paginator' => $deals])</div>
            <div style="overflow-x: auto">
                <table class="crm-table" style="min-width: 900px">
                    <thead><tr><th>الصفقة</th><th>المرحلة</th><th>المبلغ</th><th>الطلب</th><th>الإغلاق</th><th>الموظف</th><th>العمولة</th></tr></thead>
                    <tbody>
                        @forelse($deals as $deal)
                            @php([$commText, $commClass] = $commissionLabel($deal))
                            <tr>
                                <td><a class="crm-link" href="{{ route($crm->route('deals.show'), $deal->id) }}">{{ $deal->name }}{{ $deal->linkedCourseTitle() ? ' — '.$deal->linkedCourseTitle() : '' }}</a></td>
                                <td class="{{ in_array($deal->stage, SalesLead::WON_LIKE_STAGES, true) ? 'crm-good' : '' }}">{{ SalesLead::stageLabel((string) $deal->stage) }}</td>
                                <td>{{ $deal->expected_value !== null ? number_format((float) $deal->expected_value, 2) : '—' }}</td>
                                <td>@if($deal->order)#{{ $deal->order->id }} {{ $deal->order->status === 'approved' ? 'معتمد' : $deal->order->status }}@else<span class="crm-muted">—</span>@endif</td>
                                <td>{{ $deal->closed_at?->format('d/m/Y') ?? '—' }}</td>
                                <td>{{ $deal->assignee?->name ?? '—' }}</td>
                                <td class="{{ $commClass }}">{{ $commText }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="crm-muted" style="text-align: center; padding: 22px">مفيش صفقات هنا.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="crm-card" style="flex: 1 1 280px; padding: 14px; display: flex; flex-direction: column; gap: 10px">
            <h2 style="margin: 0; font-size: 14px; font-weight: 700">رسوم سريعة — الصفقات حسب المرحلة</h2>
            @foreach($stageOrder as $stage)
                @php($row = $byStage->get($stage))
                <div style="display: flex; align-items: center; gap: 8px; font-size: 12px">
                    <span style="width: 96px">{{ SalesLead::stageLabel($stage) }}</span>
                    <span class="crm-bar" style="flex: 1; height: 16px"><span style="height: 16px; width: {{ $row ? round($row->v / $maxV * 100) : 0 }}%; background: {{ in_array($stage, SalesLead::WON_LIKE_STAGES, true) ? '#0E6B44' : '#534D64' }}"></span></span>
                    <b style="width: 70px; text-align: end">{{ $row ? number_format((float) $row->v / 1000, 1).'k' : '0' }}</b>
                    <span class="crm-muted" style="width: 26px; text-align: end">{{ $row->c ?? 0 }}</span>
                </div>
            @endforeach
            <p class="crm-muted" style="margin: 0; font-size: 12px">«فاز» يتحسب من الطلبات المعتمدة؛ القيمة = المدفوع فعلاً.</p>
        </aside>
    </div>
</div>
@endsection
