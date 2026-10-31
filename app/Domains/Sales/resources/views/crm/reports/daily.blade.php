@extends('sales::crm.layout')
@section('title','التقرير اليومي')
@section('content')
@include('sales::crm.partials.module-header',['title'=>'نشاط اليوم','subtitle'=>'Daily'])
<div class="crm-card">
<table class="crm-table"><thead><tr><th>الوقت</th><th>النوع</th><th>العميل</th><th>الموظف</th><th>ملخص</th></tr></thead><tbody>
@forelse($activities as $a)
<tr>
<td class="crm-muted">{{ $a->created_at?->format('H:i') }}</td>
<td>{{ \App\Domains\Sales\Models\SalesActivity::typeLabel((string)$a->type) }}</td>
<td>@if($a->lead)<a class="crm-link" href="{{ route($crm->route('leads.show'), $a->lead) }}">{{ $a->lead->name }}</a>@else—@endif</td>
<td>{{ $a->user?->name ?? '—' }}</td>
<td>{{ \Illuminate\Support\Str::limit((string)($a->title ?: $a->body), 80) }}</td>
</tr>
@empty
<tr><td colspan="5" class="crm-muted" style="text-align:center;padding:16px">لا نشاط مسجّل اليوم.</td></tr>
@endforelse
</tbody></table>
</div>
@endsection
