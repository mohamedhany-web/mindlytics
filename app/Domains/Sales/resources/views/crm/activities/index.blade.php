@extends('sales::crm.layout')

@section('title', 'الأنشطة')

@use('App\Domains\Sales\Models\SalesPlannedActivity')
@use('App\Domains\Sales\Http\Controllers\Crm\SalesCrmActivityController')
@php
    $indexRoute = $crm->route('activities.index');
    $q = fn (array $extra) => route($indexRoute, array_filter(array_merge(request()->except('page'), $extra), fn ($v) => $v !== null && $v !== ''));
@endphp

@section('content')
<div x-data="{ filter: {{ request()->filled('q') || request()->filled('assigned_to') || request()->filled('kind') ? 'true' : 'false' }} }" style="display: flex; flex-direction: column; gap: 14px">
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 10px">
        <h1 class="crm-title">الأنشطة</h1>
        <div style="display: flex; flex-wrap: wrap; gap: 6px">
            @foreach(SalesPlannedActivity::KINDS as $k => $v)
                <a href="{{ route($crm->route('activities.create'), ['kind' => $k]) }}" class="crm-btn crm-btn-primary"><i class="fas {{ SalesPlannedActivity::KIND_ICONS[$k] }}"></i> جدولة {{ $v }}</a>
            @endforeach
            <a href="{{ route($crm->route('calendar')) }}" class="crm-btn crm-btn-dark"><i class="fas fa-calendar"></i> الكاليندر</a>
            <button type="button" class="crm-btn crm-btn-slate" @click="filter = ! filter" :aria-expanded="filter">فلتر</button>
        </div>
    </div>

    <form method="get" action="{{ route($indexRoute) }}" class="crm-card" x-cloak x-show="filter" style="padding: 14px; display: flex; flex-wrap: wrap; gap: 12px; align-items: end">
        <input type="hidden" name="quick" value="{{ $filters['quick'] }}">
        <label class="crm-label" style="flex: 1 1 240px">بحث (الموضوع، العميل، الرقم)<input class="crm-input" type="search" name="q" value="{{ $filters['q'] }}"></label>
        <label class="crm-label">النوع
            <select class="crm-select" name="kind"><option value="">الكل</option>@foreach(SalesPlannedActivity::KINDS as $k => $v)<option value="{{ $k }}" @selected($filters['kind'] === $k)>{{ $v }}</option>@endforeach</select>
        </label>
        @if($members->isNotEmpty())
            <label class="crm-label">المكلَّف
                <select class="crm-select" name="assigned_to"><option value="">الكل</option>@foreach($members as $m)<option value="{{ $m->id }}" @selected((int) $filters['assigned_to'] === (int) $m->id)>{{ $m->name }}</option>@endforeach</select>
            </label>
        @endif
        <a href="{{ route($indexRoute) }}" class="crm-btn crm-btn-outline">مسح</a>
        <button type="submit" class="crm-btn crm-btn-primary">بحث</button>
    </form>

    <nav aria-label="فلاتر سريعة" style="display: flex; flex-wrap: wrap; gap: 6px; align-items: center; font-size: 13px">
        <span class="crm-muted">فلاتر سريعة:</span>
        @foreach(SalesCrmActivityController::QUICK as $key => $label)
            <a href="{{ $q(['quick' => $key]) }}" class="crm-chip {{ $filters['quick'] === $key ? 'is-active' : '' }}">{{ $label }} ({{ $counts[$key] }})</a>
        @endforeach
    </nav>

    <section class="crm-card">
        <div class="crm-toolbar"><span style="flex: 1"></span>@include('sales::crm.partials.pager', ['paginator' => $activities])</div>
        <div style="overflow-x: auto">
            <table class="crm-table" style="min-width: 860px">
                <thead><tr><th>النوع</th><th>الموضوع</th><th>على</th><th>الموعد</th><th>الحالة</th><th>الأولوية</th>@unless($crm->isRep())<th>المكلَّف</th>@endunless<th></th></tr></thead>
                <tbody>
                    @forelse($activities as $a)
                        <tr @if($a->isOverdue()) style="background: #FDF3F1" @endif>
                            <td><i class="fas {{ SalesPlannedActivity::KIND_ICONS[$a->kind] ?? 'fa-circle' }}" style="color: #534D64"></i> {{ SalesPlannedActivity::kindLabel($a->kind) }}</td>
                            <td><a class="crm-link" href="{{ route($crm->route('activities.show'), $a->id) }}">{{ $a->title }}</a></td>
                            <td>
                                @if($a->lead)<a class="crm-link" style="font-weight: 400" href="{{ route($crm->route('leads.show'), $a->lead->id) }}">{{ $a->lead->name }}</a>
                                @elseif($a->student)<a class="crm-link" style="font-weight: 400" href="{{ route($crm->route('students.show'), $a->student->id) }}">{{ $a->student->name }}</a>
                                @else<span class="crm-muted">—</span>@endif
                            </td>
                            <td class="{{ $a->isOverdue() ? 'crm-bad' : '' }}">{{ $a->starts_at->format('d/m/Y H:i') }}{{ $a->isOverdue() ? ' (متأخر)' : '' }}</td>
                            <td class="{{ $a->status === 'done' ? 'crm-good' : ($a->status === 'missed' ? 'crm-bad' : '') }}">{{ SalesPlannedActivity::statusLabel($a->status) }}{{ $a->outcome ? ' · '.\App\Domains\Sales\Models\SalesActivity::outcomeLabel($a->outcome) : '' }}</td>
                            <td class="{{ $a->priority === 'high' ? 'crm-warn' : '' }}">{{ SalesPlannedActivity::PRIORITIES[$a->priority] ?? '—' }}</td>
                            @unless($crm->isRep())<td>{{ $a->assignee?->name ?? '—' }}</td>@endunless
                            <td style="white-space: nowrap">
                                @if($crm->canComplete($a))<a class="crm-btn crm-btn-outline crm-btn-sm" href="{{ route($crm->route('activities.show'), $a->id) }}#complete">تنفيذ</a>@endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="crm-muted" style="text-align: center; padding: 22px">مفيش أنشطة هنا. جدول مكالمة أو اجتماع من ملف العميل أو من الأزرار فوق.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
