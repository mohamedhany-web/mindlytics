@extends('sales::crm.layout')
@section('title','عروض الأسعار / الطلبات')
@section('content')
@include('sales::crm.partials.module-header',['title'=>'عروض الأسعار','subtitle'=>'Quotes & Orders'])
<form method="get" class="crm-card" style="padding:12px;margin-bottom:12px;display:flex;gap:8px"><input class="crm-input" style="max-width:280px" name="q" value="{{ $q }}" placeholder="رقم الطلب…"><button class="crm-btn crm-btn-primary">بحث</button></form>
<section class="crm-card"><div class="crm-toolbar"><span style="flex:1"></span>@include('sales::crm.partials.pager',['paginator'=>$rows])</div>
<table class="crm-table"><thead><tr><th>#</th><th>الطالب</th><th>الكورس</th><th>المبلغ</th><th>الحالة</th><th>Lead</th></tr></thead><tbody>
@forelse($rows as $row)
@php $lead = $leadByOrder[$row->id] ?? null; @endphp
<tr>
<td><a class="crm-link" href="{{ route($crm->route('quotes.show'),$row) }}">{{ $row->order_number ?? $row->id }}</a></td>
<td>{{ $row->user?->name ?: '—' }}</td>
<td>{{ $row->course?->title ?: '—' }}</td>
<td>{{ number_format((float)($row->final_amount ?? $row->total_amount ?? 0),2) }}</td>
<td>{{ $row->status }}</td>
<td>@if($lead)<a class="crm-link" href="{{ route($crm->route('leads.show'),$lead) }}">{{ $lead->name }}</a>@else—@endif</td>
</tr>
@empty<tr><td colspan="6" class="crm-muted" style="text-align:center;padding:20px">لا طلبات مربوطة بنطاقك</td></tr>@endforelse
</tbody></table></section>
@endsection
