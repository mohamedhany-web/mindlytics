@extends('sales::crm.layout')
@section('title','طلب #'.($order->order_number ?? $order->id))
@section('content')
@include('sales::crm.partials.module-header',['title'=>'طلب #'.($order->order_number ?? $order->id),'subtitle'=>'Quote / Order'])
<section class="crm-card" style="padding:16px;max-width:800px">
<div class="crm-field"><span class="k">الحالة</span><span class="v">{{ $order->status }}</span></div>
<div class="crm-field"><span class="k">الطالب</span><span class="v">{{ $order->user?->name ?: '—' }}</span></div>
<div class="crm-field"><span class="k">الكورس</span><span class="v">{{ $order->course?->title ?: '—' }}</span></div>
<div class="crm-field"><span class="k">المبلغ</span><span class="v">{{ number_format((float)($order->final_amount ?? $order->total_amount ?? 0),2) }}</span></div>
<div class="crm-field"><span class="k">Lead</span><span class="v">@if($lead)<a class="crm-link" href="{{ route($crm->route('leads.show'),$lead) }}">{{ $lead->name }}</a>@else—@endif</span></div>
</section>
@endsection
