@extends('sales::crm.layout')

@section('title', 'المحادثات')

@use('App\Domains\Sales\Http\Controllers\Crm\SalesCrmInboxController')
@php
    $indexRoute = $crm->route('inbox');
    $q = fn (array $extra) => route($indexRoute, array_filter(array_merge(request()->except('page'), $extra), fn ($v) => $v !== null && $v !== ''));
@endphp

@section('content')
<div style="display: flex; flex-direction: column; gap: 14px">
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 10px">
        <h1 class="crm-title">المحادثات</h1>
        <div style="display: flex; flex-wrap: wrap; gap: 6px">
            @foreach($classic as $c)<a href="{{ $c['url'] }}" class="crm-btn crm-btn-outline">{{ $c['label'] }}</a>@endforeach
        </div>
    </div>

    <form method="get" action="{{ route($indexRoute) }}" class="crm-card" style="padding: 12px 14px; display: flex; flex-wrap: wrap; gap: 10px; align-items: end">
        <input type="hidden" name="quick" value="{{ $filters['quick'] }}">
        <label class="crm-label" style="flex: 1 1 220px">بحث (الاسم، الرقم، العميل)<input class="crm-input" type="search" name="q" value="{{ $filters['q'] }}"></label>
        <label class="crm-label">القناة
            <select class="crm-select" name="channel">@foreach(SalesCrmInboxController::CHANNELS as $k => $v)<option value="{{ $k }}" @selected($filters['channel'] === $k)>{{ $v }}</option>@endforeach</select>
        </label>
        @if($members->isNotEmpty())
            <label class="crm-label">الموظف
                <select class="crm-select" name="assigned_to"><option value="">{{ $crm->isAdmin() ? 'الكل' : 'كل الفريق' }}</option>@foreach($members as $m)<option value="{{ $m->id }}" @selected((int) $filters['assigned_to'] === (int) $m->id)>{{ $m->name }}</option>@endforeach</select>
            </label>
        @endif
        <a href="{{ route($indexRoute) }}" class="crm-btn crm-btn-outline">مسح</a>
        <button type="submit" class="crm-btn crm-btn-primary">بحث</button>
    </form>

    <nav aria-label="فلاتر سريعة" style="display: flex; flex-wrap: wrap; gap: 6px; align-items: center; font-size: 13px">
        <span class="crm-muted">فلاتر سريعة:</span>
        @foreach(SalesCrmInboxController::QUICK as $key => $label)
            @continue(! array_key_exists($key, $counts))
            <a href="{{ $q(['quick' => $key]) }}" class="crm-chip {{ $filters['quick'] === $key ? 'is-active' : '' }}">{{ $label }} ({{ $counts[$key] }})</a>
        @endforeach
    </nav>

    <section class="crm-card">
        <div class="crm-toolbar"><span style="flex: 1"></span>@include('sales::crm.partials.pager', ['paginator' => $rows])</div>
        <div style="overflow-x: auto">
            <table class="crm-table" style="min-width: 900px">
                <thead><tr><th>القناة</th><th>جهة الاتصال</th><th>آخر رسالة</th><th>الوقت</th><th>العميل</th><th>الحالة</th>@unless($crm->isRep())<th>الموظف</th>@endunless<th></th></tr></thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr @if($r['unread'] > 0) style="background: #FFF8F6" @endif>
                            <td style="white-space: nowrap"><i class="{{ $r['icon'] }}" style="color: {{ $r['color'] }}"></i> {{ $r['channel'] }}</td>
                            <td><span style="font-weight: 700">{{ $r['name'] }}</span>@if($r['contact'])<div class="crm-muted" dir="ltr" style="text-align: end; font-size: 12px">{{ $r['contact'] }}</div>@endif</td>
                            <td style="max-width: 320px">
                                @if($r['unread'] > 0)<span class="crm-badge" style="position: static; display: inline-flex; margin-inline-end: 4px">{{ $r['unread'] }}</span>@endif
                                <span class="{{ $r['incoming'] ? '' : 'crm-muted' }}">{{ $r['incoming'] ? '' : 'أنت: ' }}{{ \Illuminate\Support\Str::limit((string) $r['preview'], 90) ?: '—' }}</span>
                            </td>
                            <td class="crm-muted" style="white-space: nowrap">{{ $r['at'] ? ($r['at']->isToday() ? $r['at']->format('H:i') : $r['at']->format('d/m H:i')) : '—' }}</td>
                            <td>@if($r['lead'])<a class="crm-link" href="{{ route($crm->route('leads.show'), $r['lead']->id) }}">{{ $r['lead']->name }}</a>@else<span class="crm-muted">—</span>@endif</td>
                            <td>{{ $r['status'] }}</td>
                            @unless($crm->isRep())<td>{{ $r['assignee'] ?? '—' }}</td>@endunless
                            <td>@if($r['url'])<a class="crm-btn crm-btn-primary crm-btn-sm" href="{{ $r['url'] }}">فتح والرد</a>@endif</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="crm-muted" style="text-align: center; padding: 22px">مفيش محادثات هنا.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <p class="crm-muted" style="margin: 0; font-size: 12px">القراءة والرد وتحويل المحادثة لعميل بتتم في صندوق الوارد نفسه (زرار «فتح والرد»). المحادثة بتخصك لو مكلَّف بيها أو العميل المربوط بيها بتاعك.</p>
</div>
@endsection
