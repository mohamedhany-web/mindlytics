@extends('sales::crm.layout')
@section('title',$caseRow->subject)
@section('content')
@include('sales::crm.partials.module-header',['title'=>$caseRow->subject,'subtitle'=>'Case','actions'=>[['label'=>'تعديل','url'=>route($crm->route('cases.edit'),$caseRow),'class'=>'crm-btn-slate']]])
<section class="crm-card" style="padding:16px;max-width:860px">
<div class="crm-field"><span class="k">الحالة</span><span class="v"><span class="crm-status-pill {{ $caseRow->isOpen()?'is-open':'is-closed' }}">{{ \App\Domains\Sales\Models\CrmCase::statusLabel($caseRow->status) }}</span></span></div>
<div class="crm-field"><span class="k">الأولوية</span><span class="v">{{ \App\Domains\Sales\Models\CrmCase::priorityLabel($caseRow->priority) }}</span></div>
<div class="crm-field"><span class="k">Lead</span><span class="v">@if($caseRow->lead)<a class="crm-link" href="{{ route($crm->route('leads.show'),$caseRow->lead) }}">{{ $caseRow->lead->name }}</a>@else—@endif</span></div>
<div class="crm-field"><span class="k">جهة اتصال</span><span class="v">@if($caseRow->contact)<a class="crm-link" href="{{ route($crm->route('contacts.show'),$caseRow->contact) }}">{{ $caseRow->contact->name }}</a>@else—@endif</span></div>
<div class="crm-field"><span class="k">حساب</span><span class="v">@if($caseRow->account)<a class="crm-link" href="{{ route($crm->route('accounts.show'),$caseRow->account) }}">{{ $caseRow->account->name }}</a>@else—@endif</span></div>
<div class="crm-field"><span class="k">المسؤول</span><span class="v">{{ $caseRow->assignee?->name }}</span></div>
<div class="crm-field"><span class="k">الوصف</span><span class="v">{{ $caseRow->description?:'—' }}</span></div>
<div class="crm-field"><span class="k">الحل</span><span class="v">{{ $caseRow->resolution?:'—' }}</span></div>
</section>
@endsection
