@extends('sales::crm.layout')

@section('title', 'حالة العملاء')

@php
    $listRoute = $crm->route('leads.index');
    $link = fn (array $p) => route($listRoute, $p);
    $severity = ['untouched' => 'bad', 'overdue' => 'bad', 'no_next' => 'warn', 'stale' => 'warn', 'pending_win' => 'info'];
    $pctClass = fn (?int $pct) => $pct === null ? 'crm-muted' : ($pct >= 80 ? 'crm-good' : ($pct >= 50 ? 'crm-warn' : 'crm-bad'));
@endphp

@section('content')
<style>
    .lh-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; }
    .lh-card { text-decoration: none; border-top: 4px solid #D0D5DD; transition: box-shadow .15s; }
    .lh-card:hover { box-shadow: 0 2px 10px rgba(16, 24, 40, .12); }
    .lh-card.is-bad { border-top-color: var(--crm-bad); }
    .lh-card.is-warn { border-top-color: var(--crm-warn); }
    .lh-card.is-info { border-top-color: #2563EB; }
    .lh-card.is-good { border-top-color: var(--crm-good); }
    .lh-card small { font-size: 11px; color: #6B7280; line-height: 1.4; }
    .lh-table td, .lh-table th { text-align: center; white-space: nowrap; }
    .lh-table td:first-child, .lh-table th:first-child { text-align: start; }
    .lh-n { display: inline-block; min-width: 30px; padding: 2px 8px; border-radius: 10px; font-weight: 700; text-decoration: none; }
    .lh-n.is-bad { background: #FDECEA; color: var(--crm-bad); }
    .lh-n.is-warn { background: #FEF3E2; color: var(--crm-warn); }
    .lh-n.is-info { background: #E8F0FE; color: #1D4ED8; }
    .lh-n.is-zero { color: #98A2B3; font-weight: 400; }
    .lh-bar { height: 6px; border-radius: 3px; background: #EEF0F3; overflow: hidden; margin-top: 4px; min-width: 70px; }
    .lh-bar > i { display: block; height: 100%; }
    .lh-total td { font-weight: 700; background: #F8F9FB; }
</style>

<div style="display: flex; flex-direction: column; gap: 16px">
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; gap: 10px">
        <div>
            <h1 class="crm-title">حالة العملاء</h1>
            <p class="crm-muted" style="margin: 4px 0 0; font-size: 13px">كل عميل مفتوح {{ $crm->isAdmin() ? 'عند كل الموظفين' : 'في فريقك' }}: سليم ولا عنده مشكلة. اضغط على أي رقم يفتحلك العملاء دول بالظبط.</p>
        </div>
        <span class="crm-muted" style="font-size: 12px">محدّث {{ now()->format('d/m H:i') }}</span>
    </div>

    <div class="lh-cards">
        <a href="{{ $link(['quick' => 'all']) }}" class="crm-card crm-stat lh-card is-good">
            <b class="{{ $pctClass($totals['healthy_pct']) }}">{{ $totals['healthy_pct'] === null ? '—' : $totals['healthy_pct'].'٪' }}</b>
            <span>عملاء سليمين</span>
            <small>{{ number_format($totals['open'] - $totals['problem']) }} من {{ number_format($totals['open']) }} عميل مفتوح</small>
        </a>
        @foreach($buckets as $key => [$label, $hint])
            <a href="{{ $link(['quick' => $key]) }}" class="crm-card crm-stat lh-card is-{{ $severity[$key] }}" title="{{ $hint }}">
                <b class="{{ $totals[$key] > 0 && $severity[$key] !== 'info' ? 'crm-'.$severity[$key] : '' }}">{{ number_format($totals[$key]) }}</b>
                <span>{{ $label }}</span>
                <small>{{ $hint }}</small>
            </a>
        @endforeach
        @if($unassigned !== null)
            <a href="{{ $link(['quick' => 'unassigned']) }}" class="crm-card crm-stat lh-card {{ $unassigned ? 'is-bad' : '' }}">
                <b class="{{ $unassigned ? 'crm-bad' : '' }}">{{ number_format($unassigned) }}</b>
                <span>من غير موظف</span>
                <small>عملاء مفتوحين مش متعيّنين لحد.</small>
            </a>
        @endif
    </div>

    <div class="crm-card">
        <div class="crm-dashlet-head"><h3 style="margin: 0; font-size: 14px">حالة كل موظف</h3></div>
        <div style="overflow-x: auto">
            <table class="crm-table lh-table" style="min-width: 980px">
                <thead>
                    <tr>
                        <th>الموظف</th>
                        <th>عملاء مفتوحين</th>
                        <th>سليم</th>
                        @foreach($buckets as $key => [$label, $hint])<th title="{{ $hint }}">{{ $label }}</th>@endforeach
                        <th title="مكالمات ورسايل واجتماعات اتسجّلت النهارده ومربوطة بعميل">تواصل النهارده</th>
                        <th>آخر نشاط</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td><a class="crm-link" href="{{ $link(['quick' => 'all', 'assigned_to' => $r['id']]) }}">{{ $r['name'] }}</a></td>
                            <td><a class="crm-link" href="{{ $link(['quick' => 'all', 'assigned_to' => $r['id']]) }}">{{ number_format($r['open']) }}</a></td>
                            <td>
                                @if($r['healthy_pct'] === null)
                                    <span class="crm-muted">—</span>
                                @else
                                    <span class="{{ $pctClass($r['healthy_pct']) }}">{{ $r['healthy_pct'] }}٪</span>
                                    <div class="lh-bar"><i style="width: {{ $r['healthy_pct'] }}%; background: {{ $r['healthy_pct'] >= 80 ? 'var(--crm-good)' : ($r['healthy_pct'] >= 50 ? '#D97706' : 'var(--crm-bad)') }}"></i></div>
                                @endif
                            </td>
                            @foreach($buckets as $key => $_)
                                <td>
                                    @if($r[$key] > 0)
                                        <a class="lh-n is-{{ $severity[$key] }}" href="{{ $link(['quick' => $key, 'assigned_to' => $r['id']]) }}">{{ number_format($r[$key]) }}</a>
                                    @else
                                        <span class="lh-n is-zero">0</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="{{ $r['contacts_today'] === 0 && $r['open'] > 0 ? 'crm-bad' : '' }}">{{ $r['contacts_today'] }}</td>
                            <td class="{{ ! $r['last_activity_at'] || $r['last_activity_at']->lt(now()->subDay()) ? 'crm-warn' : '' }}" title="{{ $r['last_activity_at']?->format('d/m/Y H:i') }}">{{ $r['last_activity_at'] ? $r['last_activity_at']->locale('ar')->diffForHumans() : 'مفيش' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ 5 + count($buckets) }}" class="crm-muted" style="text-align: center; padding: 20px">مفيش موظفين مبيعات {{ $crm->isAdmin() ? 'نشطين' : 'في فريقك' }}.</td></tr>
                    @endforelse
                    @if(count($rows) > 1)
                        <tr class="lh-total">
                            <td>الإجمالي</td>
                            <td>{{ number_format($totals['open']) }}</td>
                            <td class="{{ $pctClass($totals['healthy_pct']) }}">{{ $totals['healthy_pct'] === null ? '—' : $totals['healthy_pct'].'٪' }}</td>
                            @foreach($buckets as $key => $_)<td>{{ number_format($totals[$key]) }}</td>@endforeach
                            <td>{{ $totals['contacts_today'] }}</td>
                            <td></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    @if($stages !== [])
        <div class="crm-card">
            <div class="crm-dashlet-head"><h3 style="margin: 0; font-size: 14px">العملاء في كل مرحلة</h3></div>
            <div style="overflow-x: auto">
                <table class="crm-table lh-table" style="min-width: {{ 220 + 90 * count($stages) }}px">
                    <thead><tr><th>الموظف</th>@foreach($stages as $label)<th>{{ $label }}</th>@endforeach</tr></thead>
                    <tbody>
                        @foreach($rows as $r)
                            <tr>
                                <td>{{ $r['name'] }}</td>
                                @foreach($stages as $stage => $label)
                                    @php($n = $r['stages'][$stage] ?? 0)
                                    <td>@if($n)<a class="crm-link" href="{{ $link(['quick' => 'all', 'stage' => $stage, 'assigned_to' => $r['id']]) }}">{{ number_format($n) }}</a>@else<span class="lh-n is-zero">0</span>@endif</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <p class="crm-muted" style="font-size: 12px; margin: 0">
        «سليم» = عميل مفتوح اتكلمنا معاه، وليه ميعاد متابعة لسه ما عدّاش، وفيه تواصل حقيقي آخر {{ \App\Domains\Sales\Models\SalesLead::STALE_CONTACT_DAYS }} أيام.
        التواصل الحقيقي = مكالمة أو اجتماع أو متابعة أو واتساب أو ماسنجر أو إيميل اتعمل فعلاً (مش الملاحظة ولا تغيير المرحلة ولا النشاط المخطط).
    </p>
</div>
@endsection
