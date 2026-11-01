@extends('sales::crm.layout')

@section('title', 'التقارير')

@section('content')
<div style="display: flex; flex-direction: column; gap: 14px">
    <h1 class="crm-title">التقارير</h1>
    <p class="crm-muted" style="margin: 0; font-size: 13px">
        @if($crm->isRep()) أرقامك أنت بس.
        @elseif($crm->isManager()) أرقام فريقك، وتقدر تفلتر بموظف.
        @else كل موظفي المبيعات، وتقدر تفلتر بموظف.
        @endif
        كل تقرير له فترة (النهارده، الأسبوع، الشهر، أو فترة محددة)@if($crm->can('reports.export'))<span> ويتصدّر CSV</span>@endif.
    </p>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 14px">
        @foreach($reports as $key => $r)
            <a href="{{ route($crm->route('reports.show'), $key) }}" class="crm-card" style="display: flex; flex-direction: column; text-decoration: none; color: inherit">
                <div class="crm-dashlet-head"><i class="fas {{ $r['icon'] }}"></i><h2>{{ $r['label'] }}</h2></div>
                <p style="margin: 0; padding: 12px 14px; font-size: 13px; line-height: 1.8; flex: 1">{{ $r['desc'] }}</p>
                <div class="crm-dashlet-foot"><span>فتح التقرير</span></div>
            </a>
        @endforeach
    </div>

    @if($legacy)
        <section class="crm-card">
            <div class="crm-dashlet-head"><i class="fas fa-folder-open"></i><h2>تقارير تفصيلية (الشاشات الحالية)</h2></div>
            <div style="display: flex; flex-wrap: wrap; gap: 8px; padding: 12px 14px">
                @foreach($legacy as $l)<a class="crm-chip" href="{{ $l['url'] }}">{{ $l['label'] }}</a>@endforeach
            </div>
        </section>
    @endif
</div>
@endsection
