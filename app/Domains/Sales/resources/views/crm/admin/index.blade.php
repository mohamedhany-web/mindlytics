@extends('sales::crm.layout')
@section('title','إدارة CRM')
@section('content')
@include('sales::crm.partials.module-header',['title'=>'إدارة المبيعات','subtitle'=>'Admin'])
<div style="display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr))">
@foreach($cards as $card)
<a href="{{ $card['url'] }}" class="crm-card" style="padding:18px;text-decoration:none;color:inherit;display:flex;gap:12px;align-items:center">
  <span style="width:40px;height:40px;border-radius:8px;background:var(--crm-nav);color:#fff;display:flex;align-items:center;justify-content:center"><i class="fas {{ $card['icon'] }}"></i></span>
  <strong>{{ $card['title'] }}</strong>
</a>
@endforeach
</div>
@endsection
