@extends('sales::crm.layout')
@section('title',$campaign->name)
@section('content')
@include('sales::crm.partials.module-header',['title'=>$campaign->name,'subtitle'=>'Campaign','actions'=>[['label'=>'تعديل','url'=>route($crm->route('campaigns.edit'),$campaign),'class'=>'crm-btn-slate']]])
<div class="crm-record-grid"><section class="crm-card"><h2 class="crm-panel-title">التفاصيل</h2><div style="padding:12px 16px">
<div class="crm-field"><span class="k">الحالة</span><span class="v">{{ \App\Domains\Sales\Models\CrmCampaign::statusLabel($campaign->status) }}</span></div>
<div class="crm-field"><span class="k">النوع</span><span class="v">{{ \App\Domains\Sales\Models\CrmCampaign::typeLabel($campaign->campaign_type) }}</span></div>
<div class="crm-field"><span class="k">الفترة</span><span class="v">{{ optional($campaign->start_date)->format('Y-m-d')?:'—' }} → {{ optional($campaign->end_date)->format('Y-m-d')?:'—' }}</span></div>
<div class="crm-field"><span class="k">الميزانية</span><span class="v">{{ $campaign->budget!==null?number_format((float)$campaign->budget,2):'—' }}</span></div>
<div class="crm-field"><span class="k">الوصف</span><span class="v">{{ $campaign->description?:'—' }}</span></div>
</div>
<div class="crm-subpanel"><div class="crm-subpanel-head"><h3>عملاء الحملة</h3></div>
<table class="crm-table"><thead><tr><th>الاسم</th><th>المرحلة</th></tr></thead><tbody>
@forelse($leads as $l)<tr><td><a class="crm-link" href="{{ route($crm->route('leads.show'),$l) }}">{{ $l->name }}</a></td><td>{{ \App\Domains\Sales\Models\SalesLead::stageLabel((string)$l->stage) }}</td></tr>
@empty<tr><td colspan="2" class="crm-muted" style="padding:12px;text-align:center">لا عملاء مرتبطين بعد</td></tr>@endforelse
</tbody></table></div></section>
<aside class="crm-card" style="padding:14px"><div class="crm-field"><span class="k">المسؤول</span><span class="v">{{ $campaign->assignee?->name }}</span></div></aside></div>
@endsection
