@extends('sales::crm.layout')
@section('title','إدارة CRM')
@section('content')
@include('sales::crm.partials.module-header',[
  'title'=>'إدارة المبيعات',
  'subtitle'=>'أدوات التشغيل والصلاحيات والقنوات من شِل CRM واحد'
])

@if($crm->isAdmin() && \Illuminate\Support\Facades\Route::has($crm->route('access.index')))
<div class="crm-ops-group" style="margin-bottom:16px">
  <div class="crm-ops-group-head"><i class="fas fa-user-shield"></i> صلاحيات الوصول</div>
  <div class="crm-ops-group-body">
    <a href="{{ route($crm->route('access.index')) }}" class="crm-ops-link" style="grid-column:1/-1">
      <i class="fas fa-sliders"></i>
      <span>
        <strong style="display:block">صلاحيات CRM</strong>
        <span class="crm-muted" style="font-size:12px;font-weight:500">شوف كل موظف بيظهر له إيه في الشريط والأدوات، واضيف أو شيل حسب الحاجة</span>
      </span>
    </a>
  </div>
</div>
@endif

<div class="crm-ops-grid">
@foreach($groups as $group)
  <section class="crm-ops-group">
    <div class="crm-ops-group-head">
      <i class="fas {{ $group['icon'] }}"></i>
      {{ $group['title'] }}
    </div>
    <div class="crm-ops-group-body">
      @foreach($group['items'] as $item)
        @continue(($item['icon'] ?? '') === 'fa-user-shield')
        <a href="{{ $item['url'] }}" class="crm-ops-link">
          <i class="fas {{ $item['icon'] }}"></i>
          <span>{{ $item['title'] }}</span>
        </a>
      @endforeach
    </div>
  </section>
@endforeach
</div>
@endsection
