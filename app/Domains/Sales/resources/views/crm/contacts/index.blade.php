@extends('sales::crm.layout')
@section('title', 'جهات الاتصال')
@section('content')
@include('sales::crm.partials.module-header', ['title' => 'جهات الاتصال', 'subtitle' => 'Contacts', 'actions' => [
    ['label' => '＋ جهة اتصال', 'url' => route($crm->route('contacts.create')), 'class' => 'crm-btn-primary'],
]])
<form method="get" class="crm-card" style="padding:12px;margin-bottom:12px;display:flex;gap:8px"><input class="crm-input" style="max-width:320px" name="q" value="{{ $q }}" placeholder="بحث…"><button class="crm-btn crm-btn-primary">بحث</button></form>
<section class="crm-card"><div class="crm-toolbar"><span style="flex:1"></span>@include('sales::crm.partials.pager', ['paginator'=>$rows])</div>
<table class="crm-table"><thead><tr><th>الاسم</th><th>الحساب</th><th>الهاتف</th><th>البريد</th><th>المسؤول</th></tr></thead><tbody>
@forelse($rows as $row)<tr>
<td><a class="crm-link" href="{{ route($crm->route('contacts.show'), $row) }}">{{ $row->name }}</a></td>
<td>@if($row->account)<a class="crm-link" href="{{ route($crm->route('accounts.show'), $row->account) }}">{{ $row->account->name }}</a>@else—@endif</td>
<td>{{ $row->phone?:'—' }}</td><td>{{ $row->email?:'—' }}</td><td>{{ $row->assignee?->name }}</td>
</tr>@empty<tr><td colspan="5" class="crm-muted" style="text-align:center;padding:20px">لا توجد جهات اتصال</td></tr>@endforelse
</tbody></table></section>
@endsection
