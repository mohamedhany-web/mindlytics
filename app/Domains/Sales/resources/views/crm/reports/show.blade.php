@extends('sales::crm.layout')

@section('title', $meta['label'])

@use('App\Domains\Sales\Http\Controllers\Crm\SalesCrmReportController')
@php
    $showRoute = $crm->route('reports.show');
    $params = fn (array $extra = []) => array_filter(array_merge(['report' => $key, 'range' => $range, 'user' => $userId], $range === 'custom' ? ['from' => $from->toDateString(), 'to' => $to->toDateString()] : [], $extra), fn ($v) => $v !== null && $v !== '');
    $money = ['won_value', 'value', 'commission', 'reversed'];
    $fmt = fn ($k, $v) => is_float($v) ? (in_array($k, $money, true) ? number_format($v, 0) : number_format($v, 1)) : (is_int($v) ? number_format($v) : $v);
    $chart = $data['chart'];
    $chartRows = collect($data['rows'])->filter(fn ($r) => ($r[$chart['key']] ?? 0) > 0)->sortByDesc($chart['key'])->take(12);
    $max = max(1, (float) $chartRows->max($chart['key']));
    if ($key === 'daily') {
        $chartRows = collect($data['rows']);
        $max = max(1, (float) $chartRows->max($chart['key']));
    }
@endphp

@section('content')
<div style="display: flex; flex-direction: column; gap: 14px">
    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px">
        <a href="{{ route($crm->route('reports.index')) }}" aria-label="رجوع للتقارير" style="width: 28px; height: 28px; border: 1px solid #534D64; border-radius: 3px; display: flex; align-items: center; justify-content: center; color: #534D64"><i class="fas fa-chevron-right"></i></a>
        <h1 style="margin: 0; font-size: 26px; font-weight: 400"><i class="fas {{ $meta['icon'] }}" style="color: #534D64; font-size: 20px"></i> {{ $meta['label'] }}</h1>
        <span style="flex: 1"></span>
        <label class="crm-sr" for="report-switch">تقرير تاني</label>
        <select id="report-switch" class="crm-select" onchange="location.href = this.value">
            @foreach($reports as $k => $r)<option value="{{ route($showRoute, $params(['report' => $k])) }}" @selected($k === $key)>{{ $r['label'] }}</option>@endforeach
        </select>
        @if($crm->can('reports.export'))<a href="{{ route($showRoute, $params(['export' => 'csv'])) }}" class="crm-btn crm-btn-slate"><i class="fas fa-download"></i> تصدير CSV</a>@endif
    </div>

    <form method="get" action="{{ route($showRoute, $key) }}" class="crm-card" x-data="{ range: '{{ $range }}' }" style="padding: 12px 14px; display: flex; flex-wrap: wrap; gap: 10px; align-items: end">
        <fieldset style="border: 0; margin: 0; padding: 0; display: flex; flex-wrap: wrap; gap: 6px; align-items: center">
            <legend class="crm-sr">الفترة</legend>
            @foreach(SalesCrmReportController::RANGES as $k => $v)
                <label class="crm-chip" :class="range === '{{ $k }}' && 'is-active'" style="cursor: pointer"><input class="crm-sr" type="radio" name="range" value="{{ $k }}" x-model="range" @if($k !== 'custom') @change="$el.form.submit()" @endif> {{ $v }}</label>
            @endforeach
        </fieldset>
        <template x-if="range === 'custom'">
            <div style="display: flex; gap: 8px; align-items: end">
                <label class="crm-label">من<input class="crm-input" type="date" name="from" value="{{ $from->toDateString() }}"></label>
                <label class="crm-label">إلى<input class="crm-input" type="date" name="to" value="{{ $to->toDateString() }}"></label>
            </div>
        </template>
        @if($members->isNotEmpty())
            <label class="crm-label">الموظف
                <select class="crm-select" name="user" onchange="this.form.submit()"><option value="">{{ $crm->isAdmin() ? 'كل الموظفين' : 'كل الفريق' }}</option>@foreach($members as $m)<option value="{{ $m->id }}" @selected((int) $userId === (int) $m->id)>{{ $m->name }}</option>@endforeach</select>
            </label>
        @endif
        <button type="submit" class="crm-btn crm-btn-primary">عرض</button>
        <span class="crm-muted" style="font-size: 12px; margin-inline-start: auto">{{ $from->format('d/m/Y') }} — {{ $to->format('d/m/Y') }}</span>
    </form>

    <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start">
        <section class="crm-card" style="flex: 999 1 640px; min-width: 0">
            <div style="overflow-x: auto">
                <table class="crm-table" style="min-width: {{ count($data['columns']) * 96 }}px">
                    <thead><tr>@foreach($data['columns'] as $c => $label)<th>{{ $label }}</th>@endforeach</tr></thead>
                    <tbody>
                        @forelse($data['rows'] as $row)
                            <tr>@foreach($data['columns'] as $c => $label)<td @if($loop->first) style="font-weight: 700" @endif>{{ $fmt($c, $row[$c] ?? '') }}</td>@endforeach</tr>
                        @empty
                            <tr><td colspan="{{ count($data['columns']) }}" class="crm-muted" style="text-align: center; padding: 22px">مفيش بيانات في الفترة دي.</td></tr>
                        @endforelse
                    </tbody>
                    @if($data['totals'] && count($data['rows']) > 1)
                        <tfoot><tr style="background: #EEF0F2; font-weight: 700">@foreach($data['columns'] as $c => $label)<td style="padding: 8px 10px">{{ $fmt($c, $data['totals'][$c] ?? '') }}</td>@endforeach</tr></tfoot>
                    @endif
                </table>
            </div>
            <p class="crm-muted" style="margin: 0; padding: 10px 14px; font-size: 12px; border-top: 1px solid #E5E7EB">{{ $data['note'] }}</p>
        </section>

        <aside class="crm-card" style="flex: 1 1 300px; padding: 14px; display: flex; flex-direction: column; gap: 8px">
            <h2 style="margin: 0 0 4px; font-size: 14px; font-weight: 700">رسم سريع — {{ $chart['label'] }}</h2>
            @forelse($chartRows as $r)
                <div style="display: flex; align-items: center; gap: 8px; font-size: 12px">
                    <span style="width: 110px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap" title="{{ $r[$chart['by']] }}">{{ $r[$chart['by']] }}</span>
                    <span class="crm-bar" style="flex: 1"><span style="width: {{ round(((float) $r[$chart['key']]) / $max * 100) }}%; background: #534D64"></span></span>
                    <b style="width: 60px; text-align: end">{{ $fmt($chart['key'], $r[$chart['key']]) }}</b>
                </div>
            @empty
                <p class="crm-muted" style="margin: 0; font-size: 12px">مفيش أرقام للرسم.</p>
            @endforelse
        </aside>
    </div>
</div>
@endsection
