@extends('sales::crm.layout')
@section('title','اعتماد الفوز')
@section('content')
@include('sales::crm.partials.module-header',['title'=>'اعتماد صفقات الفوز','subtitle'=>'Win Approvals'])
<div class="crm-card">
<table class="crm-table"><thead><tr><th>العميل</th><th>الموظف</th><th>القيمة</th><th>إجراء</th></tr></thead><tbody>
@forelse($leads as $lead)
<tr>
<td><a class="crm-link" href="{{ route($crm->route('leads.show'), $lead) }}">{{ $lead->name }}</a><div class="crm-muted" style="font-size:12px">{{ $lead->phone }}</div></td>
<td>{{ $lead->assignee?->name ?? '—' }}</td>
<td>{{ number_format((float)$lead->expected_value, 2) }}</td>
<td style="white-space:nowrap">
<form method="post" action="{{ route('admin.sales.win-approvals.approve', $lead) }}" style="display:inline">@csrf
<button class="crm-btn crm-btn-primary crm-btn-sm">اعتماد</button>
</form>
<form method="post" action="{{ route('admin.sales.win-approvals.reject', $lead) }}" style="display:inline-flex;gap:4px;align-items:center;margin-inline-start:6px">@csrf
<input class="crm-input" style="width:140px;padding:4px 8px" name="rejection_reason" placeholder="سبب الرفض" required>
<button class="crm-btn crm-btn-outline crm-btn-sm">رفض</button>
</form>
</td>
</tr>
@empty
<tr><td colspan="4" class="crm-muted" style="text-align:center;padding:16px">لا صفقات بانتظار الاعتماد.</td></tr>
@endforelse
</tbody></table>
@if(method_exists($leads,'links'))<div style="padding:12px">{{ $leads->links() }}</div>@endif
</div>
@endsection
