@extends('sales::crm.layout')
@section('title','إدارة CRM')
@section('content')
@include('sales::crm.partials.module-header',[
  'title'=>'إدارة المبيعات',
  'subtitle'=>'كل أدوات التشغيل والصلاحيات من مكان واحد'
])

@if($crm->isAdmin() && \Illuminate\Support\Facades\Route::has($crm->route('access.index')))
<div style="margin-bottom:14px">
  <a href="{{ route($crm->route('access.index')) }}" class="crm-card" style="padding:16px 18px;text-decoration:none;color:inherit;display:flex;gap:14px;align-items:center;border:1px solid #D9DEE5">
    <span style="width:44px;height:44px;border-radius:8px;background:var(--crm-nav);color:#fff;display:flex;align-items:center;justify-content:center;flex:none"><i class="fas fa-user-shield"></i></span>
    <span style="flex:1;min-width:0">
      <strong style="display:block">صلاحيات CRM</strong>
      <span class="crm-muted" style="font-size:12px">شوف كل موظف بيظهر له إيه في الشريط والأدوات، واضيف أو شيل حسب الحاجة</span>
    </span>
    <span class="crm-btn crm-btn-primary crm-btn-sm">فتح</span>
  </a>
</div>
@endif

<div style="display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr))">
@foreach($cards as $card)
  @continue(($card['icon'] ?? '') === 'fa-user-shield')
<a href="{{ $card['url'] }}" class="crm-card" style="padding:18px;text-decoration:none;color:inherit;display:flex;gap:12px;align-items:center">
  <span style="width:40px;height:40px;border-radius:8px;background:var(--crm-nav);color:#fff;display:flex;align-items:center;justify-content:center;flex:none"><i class="fas {{ $card['icon'] }}"></i></span>
  <strong>{{ $card['title'] }}</strong>
</a>
@endforeach
</div>
@endsection
