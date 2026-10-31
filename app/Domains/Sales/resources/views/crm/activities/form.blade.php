@extends('sales::crm.layout')
@section('title','نشاط جديد')
@section('content')
@include('sales::crm.partials.module-header',['title'=>'نشاط جديد'])
<form method="post" action="{{ route($crm->route('activities.store')) }}" class="crm-card" style="padding:16px;max-width:720px">@csrf
<div style="display:grid;gap:12px;grid-template-columns:1fr 1fr">
<label class="crm-label">النوع<select class="crm-select" name="type">@foreach(\App\Domains\Sales\Models\SalesActivity::TYPES as $k=>$v)<option value="{{ $k }}" @selected(old('type',$activity->type)===$k)>{{ $v }}</option>@endforeach</select></label>
<label class="crm-label">الحالة<select class="crm-select" name="status">@foreach(\App\Domains\Sales\Models\SalesActivity::STATUSES as $k=>$v)<option value="{{ $k }}" @selected(old('status',$activity->status)===$k)>{{ $v }}</option>@endforeach</select></label>
<label class="crm-label" style="grid-column:1/-1">العنوان<input class="crm-input" name="title" value="{{ old('title',$activity->title) }}"></label>
<label class="crm-label">العميل المحتمل<select class="crm-select" name="sales_lead_id"><option value="">—</option>@foreach($leads as $l)<option value="{{ $l->id }}" @selected((int)old('sales_lead_id')===(int)$l->id)>{{ $l->name }}</option>@endforeach</select></label>
<label class="crm-label">موعد<input class="crm-input" type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', optional($activity->scheduled_at)->format('Y-m-d\TH:i')) }}"></label>
<label class="crm-label">استحقاق<input class="crm-input" type="datetime-local" name="due_at" value="{{ old('due_at', optional($activity->due_at)->format('Y-m-d\TH:i')) }}"></label>
<label class="crm-label" style="grid-column:1/-1">التفاصيل<textarea class="crm-textarea" name="body">{{ old('body',$activity->body) }}</textarea></label>
</div>
<div style="display:flex;gap:8px;justify-content:flex-end;margin-top:14px"><a class="crm-btn crm-btn-outline" href="{{ route($crm->route('activities.index')) }}">إلغاء</a><button class="crm-btn crm-btn-primary">حفظ</button></div>
</form>
@endsection
