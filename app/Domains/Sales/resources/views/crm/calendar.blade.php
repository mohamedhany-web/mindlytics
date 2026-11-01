@extends('sales::crm.layout')

@section('title', 'الكاليندر')

@use('App\Domains\Sales\Http\Controllers\Crm\SalesCrmCalendarController')
@php
    $calRoute = $crm->route('calendar');
    $link = fn (array $extra) => route($calRoute, array_filter(array_merge(['view' => $mode, 'date' => $date->toDateString(), 'user' => $userId], $extra), fn ($v) => $v !== null && $v !== ''));
    $dayNames = ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'];
    $colors = SalesCrmCalendarController::COLORS;
    $heading = match ($mode) {
        'day' => $date->translatedFormat('l j F Y'),
        'month' => $date->translatedFormat('F Y'),
        default => $from->format('d/m').' — '.$to->format('d/m/Y'),
    };
    $createUrl = fn ($day) => route($crm->route('activities.create'), ['date' => $day->format('Y-m-d').' 10:00', 'redirect_to' => url()->full()]);
@endphp

@push('styles')
<style>
    .cal-ev { display: block; padding: 3px 6px; margin: 2px 0; border-inline-start: 4px solid; background: #F7F8F9; font-size: 11.5px; color: #1F2937; text-decoration: none; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
    .cal-ev:hover { background: #ECEEF1; }
    .cal-ev.is-done { opacity: .55; text-decoration: line-through; }
    .cal-ev.is-late { background: #FDF3F1; }
    .cal-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); }
    .cal-cell { min-height: 112px; border: 1px solid #E5E7EB; padding: 4px; background: #fff; }
    .cal-cell.is-out { background: #F7F8F9; }
    .cal-cell.is-today { box-shadow: inset 0 0 0 2px #F08377; }
    .cal-daynum { display: flex; justify-content: space-between; align-items: center; font-size: 12px; font-weight: 700; color: #4B5563; }
    .cal-add { color: #8A97A4; text-decoration: none; font-size: 13px; padding: 0 4px; }
    .cal-add:hover { color: #B8492F; }
    @media (max-width: 760px) { .cal-grid { grid-template-columns: 1fr; } .cal-cell { min-height: 0; } .cal-cell.is-out { display: none; } .cal-head { display: none; } }
</style>
@endpush

@section('content')
<div style="display: flex; flex-direction: column; gap: 14px">
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 10px">
        <h1 class="crm-title">الكاليندر</h1>
        <div style="display: flex; flex-wrap: wrap; gap: 6px">
            @if($crm->can('activities.create'))<a href="{{ route($crm->route('activities.create'), ['kind' => 'call', 'redirect_to' => url()->full()]) }}" class="crm-btn crm-btn-primary">＋ جدولة نشاط</a>@endif
            <a href="{{ route($crm->route('activities.index')) }}" class="crm-btn crm-btn-dark">قائمة الأنشطة</a>
        </div>
    </div>

    <section class="crm-card">
        <div class="crm-toolbar" style="gap: 10px">
            <a href="{{ $link(['date' => $prev]) }}" class="crm-btn crm-btn-outline crm-btn-sm" aria-label="السابق"><i class="fas fa-chevron-right"></i></a>
            <a href="{{ $link(['date' => today()->toDateString()]) }}" class="crm-btn crm-btn-outline crm-btn-sm">النهارده</a>
            <a href="{{ $link(['date' => $next]) }}" class="crm-btn crm-btn-outline crm-btn-sm" aria-label="التالي"><i class="fas fa-chevron-left"></i></a>
            <strong style="font-size: 15px">{{ $heading }}</strong>
            <span style="flex: 1"></span>
            @if($members->isNotEmpty())
                <form method="get" action="{{ route($calRoute) }}" style="display: flex; gap: 6px; align-items: center">
                    <input type="hidden" name="view" value="{{ $mode }}"><input type="hidden" name="date" value="{{ $date->toDateString() }}">
                    <label class="crm-sr" for="cal-user">الموظف</label>
                    <select id="cal-user" class="crm-select" name="user" onchange="this.form.submit()" style="height: 28px">
                        <option value="">{{ $crm->isAdmin() ? 'كل الموظفين' : 'كل الفريق' }}</option>
                        @foreach($members as $m)<option value="{{ $m->id }}" @selected((int) $userId === (int) $m->id)>{{ $m->name }}</option>@endforeach
                    </select>
                </form>
            @endif
            <nav aria-label="طريقة العرض" style="display: flex; gap: 2px">
                @foreach(SalesCrmCalendarController::VIEWS as $k => $v)
                    @continue($k === 'shared' && ($crm->isRep() || ! $crm->can('calendar.shared')))
                    <a href="{{ $link(['view' => $k]) }}" class="crm-btn crm-btn-sm {{ $mode === $k ? 'crm-btn-dark' : 'crm-btn-outline' }}" @if($mode === $k) aria-current="page" @endif>{{ $v }}</a>
                @endforeach
            </nav>
        </div>

        <div style="display: flex; flex-wrap: wrap; gap: 12px; padding: 8px 12px; font-size: 12px; border-bottom: 1px solid #E5E7EB">
            <span><i class="fas fa-square" style="color: {{ $colors['call'] }}"></i> مكالمة</span>
            <span><i class="fas fa-square" style="color: {{ $colors['meeting'] }}"></i> اجتماع</span>
            <span><i class="fas fa-square" style="color: {{ $colors['task'] }}"></i> مهمة</span>
            <span><i class="fas fa-square" style="color: {{ $colors['follow_up'] }}"></i> متابعة عميل</span>
            <span class="crm-muted">{{ $events->count() }} في الفترة دي</span>
        </div>

        @if($mode === 'month' || $mode === 'week')
            <div class="cal-grid cal-head" style="background: #627587; color: #fff; font-size: 12px; font-weight: 700">
                @foreach($dayNames as $n)<div style="padding: 6px 8px">{{ $n }}</div>@endforeach
            </div>
            <div class="cal-grid">
                @foreach($days as $day)
                    @php($list = $byDay->get($day->toDateString(), collect()))
                    <div class="cal-cell {{ $mode === 'month' && ! $day->isSameMonth($date) ? 'is-out' : '' }} {{ $day->isToday() ? 'is-today' : '' }}" @if($mode === 'week') style="min-height: 260px" @endif>
                        <div class="cal-daynum">
                            <a href="{{ $link(['view' => 'day', 'date' => $day->toDateString()]) }}" style="color: inherit; text-decoration: none">{{ $day->format('j') }}<span class="crm-hide-sm" style="font-weight: 400"> {{ $mode === 'week' ? $day->translatedFormat('M') : '' }}</span></a>
                            @if($crm->can('activities.create'))<a class="cal-add" href="{{ $createUrl($day) }}" aria-label="جدولة يوم {{ $day->format('d/m') }}">＋</a>@endif
                        </div>
                        @foreach($list->take($mode === 'month' ? 4 : 30) as $e)
                            <a href="{{ $e['url'] }}" class="cal-ev {{ in_array($e['status'], ['done', 'missed'], true) ? 'is-done' : '' }} {{ $e['late'] ? 'is-late' : '' }}" style="border-color: {{ $colors[$e['kind']] ?? '#534D64' }}" title="{{ $e['label'] }} — {{ $e['title'] }}{{ $e['user'] && ! $crm->isRep() ? ' · '.$e['user'] : '' }}">
                                {{ $e['at']->format('H:i') }} {{ $e['title'] }}
                            </a>
                        @endforeach
                        @if($mode === 'month' && $list->count() > 4)
                            <a href="{{ $link(['view' => 'day', 'date' => $day->toDateString()]) }}" class="crm-link" style="font-size: 11px">+{{ $list->count() - 4 }} كمان</a>
                        @endif
                    </div>
                @endforeach
            </div>
        @elseif($mode === 'day')
            <div style="overflow-x: auto">
                <table class="crm-table crm-table-plain" style="min-width: 560px">
                    <thead><tr><th style="width: 110px">الوقت</th><th>النشاط</th><th>على</th>@unless($crm->isRep())<th>الموظف</th>@endunless<th>الحالة</th></tr></thead>
                    <tbody>
                        @forelse($events as $e)
                            <tr @if($e['late']) style="background: #FDF3F1" @endif>
                                <td style="border-inline-start: 4px solid {{ $colors[$e['kind']] ?? '#534D64' }}">{{ $e['at']->format('H:i') }} – {{ $e['end']?->format('H:i') }}</td>
                                <td><a class="crm-link" href="{{ $e['url'] }}">{{ $e['label'] }}: {{ $e['title'] }}</a></td>
                                <td>{{ $e['who'] ?? '—' }}</td>
                                @unless($crm->isRep())<td>{{ $e['user'] ?? '—' }}</td>@endunless
                                <td class="{{ $e['late'] ? 'crm-bad' : '' }}">{{ $e['kind'] === 'follow_up' ? ($e['late'] ? 'متأخرة' : 'مستحقة') : \App\Domains\Sales\Models\SalesPlannedActivity::statusLabel($e['status']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="crm-muted" style="text-align: center; padding: 22px">مفيش حاجة في اليوم ده.@if($crm->can('activities.create')) <a class="crm-link" href="{{ $createUrl($date) }}">جدول نشاط</a>@endif</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div style="overflow-x: auto">
                <table class="crm-table crm-table-plain" style="min-width: 980px; table-layout: fixed">
                    <thead><tr><th style="width: 140px">الموظف</th>@foreach($days as $i => $day)<th class="{{ $day->isToday() ? 'crm-warn' : '' }}">{{ $dayNames[$i] ?? '' }} {{ $day->format('d/m') }}</th>@endforeach</tr></thead>
                    <tbody>
                        @forelse($sharedRows as $row)
                            <tr>
                                <td style="font-weight: 700; vertical-align: top">{{ $row['user']->name }}</td>
                                @foreach($days as $day)
                                    <td style="vertical-align: top; padding: 4px">
                                        @foreach($row['byDay']->get($day->toDateString(), collect()) as $e)
                                            <a href="{{ $e['url'] }}" class="cal-ev {{ in_array($e['status'], ['done', 'missed'], true) ? 'is-done' : '' }} {{ $e['late'] ? 'is-late' : '' }}" style="border-color: {{ $colors[$e['kind']] ?? '#534D64' }}" title="{{ $e['label'] }} — {{ $e['title'] }}">{{ $e['at']->format('H:i') }} {{ $e['title'] }}</a>
                                        @endforeach
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="8" class="crm-muted" style="text-align: center; padding: 22px">مفيش موظفين في نطاقك.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
