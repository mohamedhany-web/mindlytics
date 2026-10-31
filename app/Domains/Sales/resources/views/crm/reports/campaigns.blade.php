@extends('sales::crm.layout')
@section('title','تقارير الحملات')
@section('content')
@include('sales::crm.partials.module-header',['title'=>'الحملات','subtitle'=>'Campaigns','actions'=>[['label'=>'＋ حملة','url'=>route($crm->route('campaigns.create')),'class'=>'crm-btn-primary']]])
<div class="crm-card">
<table class="crm-table"><thead><tr><th>الاسم</th><th>الحالة</th><th>النوع</th><th>العملاء</th></tr></thead><tbody>
@forelse($campaigns as $c)
<tr>
<td><a class="crm-link" href="{{ route($crm->route('campaigns.show'), $c) }}">{{ $c->name }}</a></td>
<td>{{ $c->status }}</td>
<td>{{ $c->campaign_type ?: '—' }}</td>
<td>{{ $c->leads_count }}</td>
</tr>
@empty
<tr><td colspan="4" class="crm-muted" style="text-align:center;padding:16px">لا حملات بعد.</td></tr>
@endforelse
</tbody></table>
</div>
@endsection
