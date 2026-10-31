@extends('sales::crm.layout')
@section('title','الحملات')
@section('content')
@include('sales::crm.partials.module-header',['title'=>'الحملات','subtitle'=>'Campaigns','actions'=>[['label'=>'＋ حملة','url'=>route($crm->route('campaigns.create')),'class'=>'crm-btn-primary']]])
<section class="crm-card"><div class="crm-toolbar"><span style="flex:1"></span>@include('sales::crm.partials.pager',['paginator'=>$rows])</div>
<table class="crm-table"><thead><tr><th>الاسم</th><th>الحالة</th><th>النوع</th><th>الفترة</th><th>المسؤول</th></tr></thead><tbody>
@forelse($rows as $row)<tr>
<td><a class="crm-link" href="{{ route($crm->route('campaigns.show'),$row) }}">{{ $row->name }}</a></td>
<td>{{ \App\Domains\Sales\Models\CrmCampaign::statusLabel($row->status) }}</td>
<td>{{ \App\Domains\Sales\Models\CrmCampaign::typeLabel($row->campaign_type) }}</td>
<td>{{ optional($row->start_date)->format('Y-m-d') ?: '—' }} → {{ optional($row->end_date)->format('Y-m-d') ?: '—' }}</td>
<td>{{ $row->assignee?->name }}</td>
</tr>@empty<tr><td colspan="5" class="crm-muted" style="text-align:center;padding:20px">لا حملات</td></tr>@endforelse
</tbody></table></section>
@endsection
