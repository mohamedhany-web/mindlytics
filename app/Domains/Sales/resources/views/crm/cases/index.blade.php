@extends('sales::crm.layout')
@section('title','التذاكر')
@section('content')
@include('sales::crm.partials.module-header',['title'=>'التذاكر','subtitle'=>'Cases','actions'=>[['label'=>'＋ تذكرة','url'=>route($crm->route('cases.create')),'class'=>'crm-btn-primary']]])
<nav style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px">
@foreach([''=> 'الكل'] + \App\Domains\Sales\Models\CrmCase::STATUSES as $k=>$v)
<a class="crm-chip {{ (string)($status??'')===(string)$k ? 'is-active':'' }}" href="{{ route($crm->route('cases.index'), array_filter(['status'=>$k?:null])) }}">{{ $v }}</a>
@endforeach
</nav>
<section class="crm-card"><div class="crm-toolbar"><span style="flex:1"></span>@include('sales::crm.partials.pager',['paginator'=>$rows])</div>
<table class="crm-table"><thead><tr><th>الموضوع</th><th>الحالة</th><th>الأولوية</th><th>العميل</th><th>المسؤول</th></tr></thead><tbody>
@forelse($rows as $row)<tr>
<td><a class="crm-link" href="{{ route($crm->route('cases.show'),$row) }}">{{ $row->subject }}</a></td>
<td>{{ \App\Domains\Sales\Models\CrmCase::statusLabel($row->status) }}</td>
<td>{{ \App\Domains\Sales\Models\CrmCase::priorityLabel($row->priority) }}</td>
<td>{{ $row->lead?->name ?? $row->contact?->name ?? '—' }}</td>
<td>{{ $row->assignee?->name }}</td>
</tr>@empty<tr><td colspan="5" class="crm-muted" style="text-align:center;padding:20px">لا تذاكر</td></tr>@endforelse
</tbody></table></section>
@endsection
