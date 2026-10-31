@extends('sales::crm.layout')
@section('title','التقارير')
@section('content')
@include('sales::crm.partials.module-header',['title'=>'مركز التقارير','subtitle'=>'Reports'])
<div style="display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr))">
@foreach($links as $link)
<a href="{{ $link['url'] }}" class="crm-card" style="padding:16px;text-decoration:none;color:inherit">
  <div style="font-weight:700;font-size:15px;margin-bottom:6px">{{ $link['title'] }}</div>
  <div class="crm-muted" style="font-size:13px">{{ $link['desc'] }}</div>
</a>
@endforeach
</div>
@endsection
