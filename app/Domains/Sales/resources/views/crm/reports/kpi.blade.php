@extends('sales::crm.layout')
@section('title','KPI')
@section('content')
@include('sales::crm.partials.module-header',['title'=>'مؤشرات الأداء','subtitle'=>'KPI','actions'=>[['label'=>'مركز التقارير','url'=>route($crm->route('reports.index')),'class'=>'crm-btn-outline']]])
<div style="display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(160px,1fr))">
  <div class="crm-card" style="padding:18px"><div class="crm-muted" style="font-size:12px">مفتوحة</div><div style="font-size:28px;font-weight:700">{{ $open }}</div></div>
  <div class="crm-card" style="padding:18px"><div class="crm-muted" style="font-size:12px">متابعات اليوم</div><div style="font-size:28px;font-weight:700">{{ $today }}</div></div>
  <div class="crm-card" style="padding:18px"><div class="crm-muted" style="font-size:12px">متأخرة</div><div style="font-size:28px;font-weight:700;color:#b91c1c">{{ $overdue }}</div></div>
  <div class="crm-card" style="padding:18px"><div class="crm-muted" style="font-size:12px">فوز هذا الشهر</div><div style="font-size:28px;font-weight:700;color:#15803d">{{ $wonMonth }}</div></div>
</div>
<p class="crm-muted" style="margin-top:16px">التفاصيل التشغيلية تُدار من شاشات CRM (العملاء / الفرص / الأنشطة).</p>
@endsection
