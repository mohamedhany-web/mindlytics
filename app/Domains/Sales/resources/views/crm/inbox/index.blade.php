@extends('sales::crm.layout')
@section('title','المحادثات')
@section('content')
@include('sales::crm.partials.module-header',['title'=>'المحادثات','subtitle'=>'Inbox'])
<div style="display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr))">
@if($waRoute)
<a href="{{ $waRoute }}" class="crm-card" style="padding:20px;text-decoration:none;color:inherit;display:flex;gap:12px;align-items:center">
  <span style="width:44px;height:44px;border-radius:8px;background:#25D366;color:#fff;display:flex;align-items:center;justify-content:center"><i class="fab fa-whatsapp"></i></span>
  <div><strong>واتساب</strong><div class="crm-muted" style="font-size:13px">فتح صندوق الوارد داخل CRM</div></div>
</a>
@endif
@if($metaRoute)
<a href="{{ $metaRoute }}" class="crm-card" style="padding:20px;text-decoration:none;color:inherit;display:flex;gap:12px;align-items:center">
  <span style="width:44px;height:44px;border-radius:8px;background:#0866FF;color:#fff;display:flex;align-items:center;justify-content:center"><i class="fab fa-facebook-messenger"></i></span>
  <div><strong>Messenger / Instagram</strong><div class="crm-muted" style="font-size:13px">فتح صندوق Meta داخل CRM</div></div>
</a>
@endif
</div>
@endsection
