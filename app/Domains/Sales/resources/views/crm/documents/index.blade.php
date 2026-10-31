@extends('sales::crm.layout')
@section('title','المستندات')
@section('content')
@include('sales::crm.partials.module-header',['title'=>'المستندات','subtitle'=>'Documents','actions'=>[['label'=>'＋ مستند','url'=>route($crm->route('documents.create')),'class'=>'crm-btn-primary']]])
<section class="crm-card"><div class="crm-toolbar"><span style="flex:1"></span>@include('sales::crm.partials.pager',['paginator'=>$rows])</div>
<table class="crm-table"><thead><tr><th>العنوان</th><th>التصنيف</th><th>الحجم</th><th>المسؤول</th><th>التاريخ</th></tr></thead><tbody>
@forelse($rows as $row)<tr>
<td><a class="crm-link" href="{{ route($crm->route('documents.show'),$row) }}">{{ $row->title }}</a></td>
<td>{{ $row->category?:'—' }}</td>
<td>{{ $row->size ? number_format($row->size/1024,1).' KB' : '—' }}</td>
<td>{{ $row->assignee?->name }}</td>
<td>{{ $row->created_at?->format('Y-m-d') }}</td>
</tr>@empty<tr><td colspan="5" class="crm-muted" style="text-align:center;padding:20px">لا مستندات</td></tr>@endforelse
</tbody></table></section>
@endsection
