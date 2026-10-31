@extends('sales::crm.layout')
@section('title', $contact->exists ? 'تعديل جهة اتصال' : 'جهة اتصال جديدة')
@section('content')
@include('sales::crm.partials.module-header', ['title' => $contact->exists ? 'تعديل جهة اتصال' : 'جهة اتصال جديدة'])
<form method="post" action="{{ $contact->exists ? route($crm->route('contacts.update'), $contact) : route($crm->route('contacts.store')) }}" class="crm-card" style="padding:16px;max-width:720px">
@csrf @if($contact->exists) @method('PUT') @endif
<div style="display:grid;gap:12px;grid-template-columns:1fr 1fr">
<label class="crm-label">الاسم الأول<input class="crm-input" name="first_name" value="{{ old('first_name',$contact->first_name) }}"></label>
<label class="crm-label">اسم العائلة<input class="crm-input" name="last_name" value="{{ old('last_name',$contact->last_name) }}"></label>
<label class="crm-label" style="grid-column:1/-1">الاسم الظاهر<input class="crm-input" name="name" value="{{ old('name',$contact->name) }}"></label>
<label class="crm-label">المسمى<input class="crm-input" name="title" value="{{ old('title',$contact->title) }}"></label>
<label class="crm-label">الحساب<select class="crm-select" name="crm_account_id"><option value="">—</option>@foreach($accounts as $a)<option value="{{ $a->id }}" @selected((int)old('crm_account_id',$contact->crm_account_id)===(int)$a->id)>{{ $a->name }}</option>@endforeach</select></label>
<label class="crm-label">الهاتف<input class="crm-input" name="phone" value="{{ old('phone',$contact->phone) }}"></label>
<label class="crm-label">البريد<input class="crm-input" type="email" name="email" value="{{ old('email',$contact->email) }}"></label>
<label class="crm-label">المسؤول<select class="crm-select" name="assigned_to">@foreach($members as $m)<option value="{{ $m->id }}" @selected((int)old('assigned_to',$contact->assigned_to)===(int)$m->id)>{{ $m->name }}</option>@endforeach</select></label>
<label class="crm-label" style="grid-column:1/-1">الوصف<textarea class="crm-textarea" name="description">{{ old('description',$contact->description) }}</textarea></label>
</div>
<div style="display:flex;gap:8px;justify-content:flex-end;margin-top:14px"><a class="crm-btn crm-btn-outline" href="{{ route($crm->route('contacts.index')) }}">إلغاء</a><button class="crm-btn crm-btn-primary">حفظ</button></div>
</form>
@endsection
