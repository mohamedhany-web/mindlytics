@extends('sales::crm.layout')
@section('title', $activity->title ?: 'نشاط')
@section('content')
@include('sales::crm.partials.module-header',['title'=>$activity->title ?: \App\Domains\Sales\Models\SalesActivity::typeLabel($activity->type),'subtitle'=>'Activity'])
<section class="crm-card" style="padding:16px;max-width:800px">
<div class="crm-field"><span class="k">النوع</span><span class="v">{{ \App\Domains\Sales\Models\SalesActivity::typeLabel($activity->type) }}</span></div>
<div class="crm-field"><span class="k">الحالة</span><span class="v">{{ \App\Domains\Sales\Models\SalesActivity::STATUSES[$activity->status] ?? ($activity->status?:'—') }}</span></div>
<div class="crm-field"><span class="k">العميل</span><span class="v">@if($activity->lead)<a class="crm-link" href="{{ route($crm->route('leads.show'),$activity->lead) }}">{{ $activity->lead->name }}</a>@else—@endif</span></div>
<div class="crm-field"><span class="k">الموعد</span><span class="v">{{ optional($activity->scheduled_at)->format('Y-m-d H:i') ?: '—' }}</span></div>
<div class="crm-field"><span class="k">الموظف</span><span class="v">{{ $activity->user?->name }}</span></div>
<div class="crm-field"><span class="k">التفاصيل</span><span class="v">{{ $activity->body ?: '—' }}</span></div>
</section>
@endsection
