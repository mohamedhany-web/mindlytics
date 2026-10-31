@extends('sales::crm.layout')
@section('title', $lead->exists ? 'تعديل عميل' : 'عميل محتمل جديد')
@section('content')
@include('sales::crm.partials.module-header', ['title' => $lead->exists ? 'تعديل: '.$lead->name : 'عميل محتمل جديد', 'subtitle' => 'Leads'])
<form method="post" action="{{ $storeRoute }}" class="crm-card" style="padding:16px;max-width:820px">
@csrf
@if($lead->exists) @method('PUT') @endif
<input type="hidden" name="stage" value="{{ old('stage', $lead->stage ?: 'new_lead') }}">
<input type="hidden" name="priority" value="{{ old('priority', $lead->priority ?: 'normal') }}">
<div style="display:grid;gap:12px;grid-template-columns:1fr 1fr">
<label class="crm-label">الاسم *<input class="crm-input" name="name" value="{{ old('name', $lead->name) }}" required autofocus></label>
<label class="crm-label">الهاتف *<input class="crm-input" name="phone" value="{{ old('phone', $lead->phone) }}" required dir="ltr"></label>
<label class="crm-label">البريد<input class="crm-input" type="email" name="email" value="{{ old('email', $lead->email) }}"></label>
<label class="crm-label">الشركة<input class="crm-input" name="company" value="{{ old('company', $lead->company) }}"></label>
<label class="crm-label">المصدر *
<select class="crm-select" name="source" required>
@foreach(\App\Domains\Sales\Models\SalesLead::SOURCES as $k=>$v)
<option value="{{ $k }}" @selected(old('source', $lead->source)===$k)>{{ $v }}</option>
@endforeach
</select>
</label>
<label class="crm-label">نوع الاهتمام *
<select class="crm-select" name="interest_type_id" required>
<option value="">— اختر —</option>
@foreach($interestTypes as $t)
<option value="{{ $t->id }}" @selected((int)old('interest_type_id', $lead->interest_type_id)===(int)$t->id)>{{ $t->name_ar }}</option>
@endforeach
</select>
</label>
<label class="crm-label">قناة المتابعة
<select class="crm-select" name="follow_up_channel">
@foreach(\App\Domains\Sales\Models\SalesLead::FOLLOW_UP_CHANNELS as $k=>$v)
<option value="{{ $k }}" @selected(old('follow_up_channel', $lead->follow_up_channel)===$k)>{{ $v }}</option>
@endforeach
</select>
</label>
<label class="crm-label">موعد المتابعة<input class="crm-input" type="datetime-local" name="next_follow_up_at" value="{{ old('next_follow_up_at', optional($lead->next_follow_up_at)->format('Y-m-d\\TH:i')) }}"></label>
@if(! $crm->isRep())
<label class="crm-label">المسؤول
<select class="crm-select" name="assigned_to">
@foreach($members as $m)
<option value="{{ $m->id }}" @selected((int)old('assigned_to', $lead->assigned_to ?? $crm->user->id)===(int)$m->id)>{{ $m->name }}</option>
@endforeach
</select>
</label>
@endif
<label class="crm-label" style="grid-column:1/-1">ملاحظات<textarea class="crm-textarea" name="notes" rows="3">{{ old('notes', $lead->notes) }}</textarea></label>
<label class="crm-label" style="grid-column:1/-1">الاهتمام / التفاصيل<textarea class="crm-textarea" name="interest" rows="2">{{ old('interest', $lead->interest) }}</textarea></label>
</div>
<div style="display:flex;gap:8px;justify-content:flex-end;margin-top:14px;flex-wrap:wrap">
<a class="crm-btn crm-btn-outline" href="{{ route($crm->route('leads.index')) }}">إلغاء</a>
@if(! $lead->exists)
<button class="crm-btn crm-btn-slate" name="save_action" value="another" type="submit">حفظ و＋ آخر</button>
@endif
<button class="crm-btn crm-btn-primary" type="submit">حفظ</button>
</div>
</form>
@endsection
