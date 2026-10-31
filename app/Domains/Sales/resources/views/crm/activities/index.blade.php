@extends('sales::crm.layout')
@section('title', $title)
@section('content')
@include('sales::crm.partials.module-header', ['title' => $title, 'subtitle' => 'Activities', 'actions' => [
    ['label' => '＋ نشاط', 'url' => route($crm->route('activities.create'), array_filter(['type' => $type])), 'class' => 'crm-btn-primary'],
]])
@php
    $chips = [
        '' => ['الكل', route($crm->route('activities.index'))],
        'call' => ['مكالمات', route($crm->route('calls.index'))],
        'meeting' => ['اجتماعات', route($crm->route('meetings.index'))],
        'follow_up' => ['مهام', route($crm->route('tasks.index'))],
        'note' => ['ملاحظات', route($crm->route('notes.index'))],
        'email' => ['بريد', route($crm->route('emails.index'))],
    ];
@endphp
<nav style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px">
    @foreach($chips as $key => [$lab, $url])
        <a class="crm-chip {{ (string) ($type ?? '') === (string) $key ? 'is-active' : '' }}" href="{{ $url }}">{{ $lab }}</a>
    @endforeach
</nav>
<section class="crm-card">
    <div class="crm-toolbar"><span style="flex:1"></span>@include('sales::crm.partials.pager', ['paginator' => $rows])</div>
    <table class="crm-table">
        <thead><tr><th>النوع</th><th>العنوان</th><th>العميل</th><th>الموعد</th><th>الموظف</th><th>الحالة</th></tr></thead>
        <tbody>
        @forelse($rows as $row)
            <tr>
                <td>{{ \App\Domains\Sales\Models\SalesActivity::typeLabel($row->type) }}</td>
                <td><a class="crm-link" href="{{ route($crm->route('activities.show'), $row) }}">{{ $row->title ?: ($row->body ? \Illuminate\Support\Str::limit($row->body, 40) : '#'.$row->id) }}</a></td>
                <td>@if($row->lead)<a class="crm-link" href="{{ route($crm->route('leads.show'), $row->lead) }}">{{ $row->lead->name }}</a>@else—@endif</td>
                <td>{{ optional($row->scheduled_at ?? $row->due_at)->format('Y-m-d H:i') ?: '—' }}</td>
                <td>{{ $row->user?->name }}</td>
                <td>{{ \App\Domains\Sales\Models\SalesActivity::STATUSES[$row->status] ?? ($row->status ?: '—') }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="crm-muted" style="text-align:center;padding:20px">لا أنشطة</td></tr>
        @endforelse
        </tbody>
    </table>
</section>
@endsection
