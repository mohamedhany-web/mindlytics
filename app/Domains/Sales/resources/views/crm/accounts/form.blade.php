@extends('sales::crm.layout')
@section('title', $account->exists ? 'تعديل حساب' : 'حساب جديد')
@section('content')
@include('sales::crm.partials.module-header', ['title' => $account->exists ? 'تعديل حساب' : 'حساب جديد'])
<form method="post" action="{{ $account->exists ? route($crm->route('accounts.update'), $account) : route($crm->route('accounts.store')) }}" class="crm-card" style="padding:16px;max-width:720px">
  @csrf
  @if($account->exists) @method('PUT') @endif
  <div style="display:grid;gap:12px;grid-template-columns:1fr 1fr">
    <label class="crm-label" style="grid-column:1/-1">الاسم<input class="crm-input" name="name" value="{{ old('name', $account->name) }}" required></label>
    <label class="crm-label">الهاتف<input class="crm-input" name="phone" value="{{ old('phone', $account->phone) }}"></label>
    <label class="crm-label">البريد<input class="crm-input" type="email" name="email" value="{{ old('email', $account->email) }}"></label>
    <label class="crm-label">الموقع<input class="crm-input" name="website" value="{{ old('website', $account->website) }}"></label>
    <label class="crm-label">الصناعة<input class="crm-input" name="industry" value="{{ old('industry', $account->industry) }}"></label>
    <label class="crm-label">النوع<select class="crm-select" name="account_type"><option value="">—</option>@foreach(\App\Domains\Sales\Models\CrmAccount::TYPES as $k=>$v)<option value="{{ $k }}" @selected(old('account_type',$account->account_type)===$k)>{{ $v }}</option>@endforeach</select></label>
    <label class="crm-label">المسؤول<select class="crm-select" name="assigned_to">@foreach($members as $m)<option value="{{ $m->id }}" @selected((int)old('assigned_to',$account->assigned_to)===(int)$m->id)>{{ $m->name }}</option>@endforeach</select></label>
    <label class="crm-label" style="grid-column:1/-1">العنوان<textarea class="crm-textarea" name="billing_address">{{ old('billing_address', $account->billing_address) }}</textarea></label>
    <label class="crm-label" style="grid-column:1/-1">الوصف<textarea class="crm-textarea" name="description">{{ old('description', $account->description) }}</textarea></label>
  </div>
  <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:14px">
    <a class="crm-btn crm-btn-outline" href="{{ route($crm->route('accounts.index')) }}">إلغاء</a>
    <button class="crm-btn crm-btn-primary" type="submit">حفظ</button>
  </div>
</form>
@endsection
