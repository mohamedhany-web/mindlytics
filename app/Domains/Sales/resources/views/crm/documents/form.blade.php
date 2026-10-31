@extends('sales::crm.layout')
@section('title','مستند جديد')
@section('content')
@include('sales::crm.partials.module-header',['title'=>'مستند جديد'])
<form method="post" action="{{ route($crm->route('documents.store')) }}" enctype="multipart/form-data" class="crm-card" style="padding:16px;max-width:720px">@csrf
<div style="display:grid;gap:12px">
<label class="crm-label">العنوان<input class="crm-input" name="title" required></label>
<label class="crm-label">التصنيف<input class="crm-input" name="category"></label>
<label class="crm-label">ربط بعميل محتمل<select class="crm-select" name="sales_lead_id"><option value="">—</option>@foreach($leads as $l)<option value="{{ $l->id }}" @selected((int)old('sales_lead_id', $selectedLeadId ?? 0)===(int)$l->id)>{{ $l->name }}</option>@endforeach</select></label>
<label class="crm-label">المسؤول<select class="crm-select" name="assigned_to">@foreach($members as $m)<option value="{{ $m->id }}" @selected((int)$m->id===(int)$crm->user->id)>{{ $m->name }}</option>@endforeach</select></label>
<label class="crm-label">الملف<input class="crm-input" type="file" name="file" required></label>
<label class="crm-label">الوصف<textarea class="crm-textarea" name="description"></textarea></label>
</div>
<div style="display:flex;gap:8px;justify-content:flex-end;margin-top:14px"><a class="crm-btn crm-btn-outline" href="{{ route($crm->route('documents.index')) }}">إلغاء</a><button class="crm-btn crm-btn-primary">رفع</button></div>
</form>
@endsection
