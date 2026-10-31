@extends('sales::crm.layout')
@section('title',$case->exists?'تعديل تذكرة':'تذكرة جديدة')
@section('content')
@include('sales::crm.partials.module-header',['title'=>$case->exists?'تعديل تذكرة':'تذكرة جديدة'])
<form method="post" action="{{ $case->exists?route($crm->route('cases.update'),$case):route($crm->route('cases.store')) }}" class="crm-card" style="padding:16px;max-width:780px">
@csrf @if($case->exists) @method('PUT') @endif
<div style="display:grid;gap:12px;grid-template-columns:1fr 1fr">
<label class="crm-label" style="grid-column:1/-1">الموضوع<input class="crm-input" name="subject" value="{{ old('subject',$case->subject) }}" required></label>
<label class="crm-label">الحالة<select class="crm-select" name="status">@foreach(\App\Domains\Sales\Models\CrmCase::STATUSES as $k=>$v)<option value="{{ $k }}" @selected(old('status',$case->status)===$k)>{{ $v }}</option>@endforeach</select></label>
<label class="crm-label">الأولوية<select class="crm-select" name="priority">@foreach(\App\Domains\Sales\Models\CrmCase::PRIORITIES as $k=>$v)<option value="{{ $k }}" @selected(old('priority',$case->priority)===$k)>{{ $v }}</option>@endforeach</select></label>
<label class="crm-label">Lead<select class="crm-select" name="sales_lead_id"><option value="">—</option>@foreach($leads as $l)<option value="{{ $l->id }}" @selected((int)old('sales_lead_id',$case->sales_lead_id)===(int)$l->id)>{{ $l->name }}</option>@endforeach</select></label>
<label class="crm-label">جهة اتصال<select class="crm-select" name="crm_contact_id"><option value="">—</option>@foreach($contacts as $c)<option value="{{ $c->id }}" @selected((int)old('crm_contact_id',$case->crm_contact_id)===(int)$c->id)>{{ $c->name }}</option>@endforeach</select></label>
<label class="crm-label">حساب<select class="crm-select" name="crm_account_id"><option value="">—</option>@foreach($accounts as $a)<option value="{{ $a->id }}" @selected((int)old('crm_account_id',$case->crm_account_id)===(int)$a->id)>{{ $a->name }}</option>@endforeach</select></label>
<label class="crm-label">المسؤول<select class="crm-select" name="assigned_to">@foreach($members as $m)<option value="{{ $m->id }}" @selected((int)old('assigned_to',$case->assigned_to)===(int)$m->id)>{{ $m->name }}</option>@endforeach</select></label>
<label class="crm-label" style="grid-column:1/-1">الوصف<textarea class="crm-textarea" name="description">{{ old('description',$case->description) }}</textarea></label>
<label class="crm-label" style="grid-column:1/-1">الحل<textarea class="crm-textarea" name="resolution">{{ old('resolution',$case->resolution) }}</textarea></label>
</div>
<div style="display:flex;gap:8px;justify-content:flex-end;margin-top:14px"><a class="crm-btn crm-btn-outline" href="{{ route($crm->route('cases.index')) }}">إلغاء</a><button class="crm-btn crm-btn-primary">حفظ</button></div>
</form>
@endsection
