@extends('sales::crm.layout')
@section('title', $contact->name)
@section('content')
@include('sales::crm.partials.module-header', ['title'=>$contact->name,'subtitle'=>'Contact','actions'=>[['label'=>'تعديل','url'=>route($crm->route('contacts.edit'),$contact),'class'=>'crm-btn-slate']]])
<div class="crm-record-grid"><section class="crm-card"><h2 class="crm-panel-title">التفاصيل</h2><div style="padding:12px 16px">
<div class="crm-field"><span class="k">الحساب</span><span class="v">@if($contact->account)<a class="crm-link" href="{{ route($crm->route('accounts.show'),$contact->account) }}">{{ $contact->account->name }}</a>@else—@endif</span></div>
<div class="crm-field"><span class="k">الهاتف</span><span class="v">{{ $contact->phone?:'—' }}</span></div>
<div class="crm-field"><span class="k">البريد</span><span class="v">{{ $contact->email?:'—' }}</span></div>
<div class="crm-field"><span class="k">المسمى</span><span class="v">{{ $contact->title?:'—' }}</span></div>
<div class="crm-field"><span class="k">الوصف</span><span class="v">{{ $contact->description?:'—' }}</span></div>
</div></section>
<aside class="crm-card" style="padding:14px"><div class="crm-field"><span class="k">المسؤول</span><span class="v">{{ $contact->assignee?->name }}</span></div>
@if($contact->lead)<div class="crm-field"><span class="k">Lead</span><span class="v"><a class="crm-link" href="{{ route($crm->route('leads.show'),$contact->lead) }}">{{ $contact->lead->name }}</a></span></div>@endif
</aside></div>
@endsection
