@extends('sales::crm.layout')
@section('title',$campaign->exists?'تعديل حملة':'حملة جديدة')
@section('content')
@include('sales::crm.partials.module-header',['title'=>$campaign->exists?'تعديل حملة':'حملة جديدة'])
<form method="post" action="{{ $campaign->exists?route($crm->route('campaigns.update'),$campaign):route($crm->route('campaigns.store')) }}" class="crm-card" style="padding:16px;max-width:720px">
@csrf @if($campaign->exists) @method('PUT') @endif
<div style="display:grid;gap:12px;grid-template-columns:1fr 1fr">
<label class="crm-label" style="grid-column:1/-1">الاسم<input class="crm-input" name="name" value="{{ old('name',$campaign->name) }}" required></label>
<label class="crm-label">الحالة<select class="crm-select" name="status">@foreach(\App\Domains\Sales\Models\CrmCampaign::STATUSES as $k=>$v)<option value="{{ $k }}" @selected(old('status',$campaign->status)===$k)>{{ $v }}</option>@endforeach</select></label>
<label class="crm-label">النوع<select class="crm-select" name="campaign_type"><option value="">—</option>@foreach(\App\Domains\Sales\Models\CrmCampaign::TYPES as $k=>$v)<option value="{{ $k }}" @selected(old('campaign_type',$campaign->campaign_type)===$k)>{{ $v }}</option>@endforeach</select></label>
<label class="crm-label">من<input class="crm-input" type="date" name="start_date" value="{{ old('start_date',optional($campaign->start_date)->format('Y-m-d')) }}"></label>
<label class="crm-label">إلى<input class="crm-input" type="date" name="end_date" value="{{ old('end_date',optional($campaign->end_date)->format('Y-m-d')) }}"></label>
<label class="crm-label">الميزانية<input class="crm-input" type="number" step="0.01" name="budget" value="{{ old('budget',$campaign->budget) }}"></label>
<label class="crm-label">المسؤول<select class="crm-select" name="assigned_to">@foreach($members as $m)<option value="{{ $m->id }}" @selected((int)old('assigned_to',$campaign->assigned_to)===(int)$m->id)>{{ $m->name }}</option>@endforeach</select></label>
<label class="crm-label" style="grid-column:1/-1">الوصف<textarea class="crm-textarea" name="description">{{ old('description',$campaign->description) }}</textarea></label>
</div>
<div style="display:flex;gap:8px;justify-content:flex-end;margin-top:14px"><a class="crm-btn crm-btn-outline" href="{{ route($crm->route('campaigns.index')) }}">إلغاء</a><button class="crm-btn crm-btn-primary">حفظ</button></div>
</form>
@endsection
