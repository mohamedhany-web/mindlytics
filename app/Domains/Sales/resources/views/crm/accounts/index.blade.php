@extends('sales::crm.layout')
@section('title', 'الحسابات')
@section('content')
@include('sales::crm.partials.module-header', ['title' => 'الحسابات', 'subtitle' => 'Accounts', 'actions' => [
    ['label' => '＋ حساب جديد', 'url' => route($crm->route('accounts.create')), 'class' => 'crm-btn-primary', 'icon' => 'fas fa-plus'],
]])
<form method="get" class="crm-card" style="padding:12px;margin-bottom:12px;display:flex;gap:8px;align-items:center">
  <input class="crm-input" style="max-width:320px" type="search" name="q" value="{{ $q }}" placeholder="بحث…">
  <button class="crm-btn crm-btn-primary" type="submit">بحث</button>
</form>
<section class="crm-card">
  <div class="crm-toolbar"><span style="flex:1"></span>@include('sales::crm.partials.pager', ['paginator' => $rows])</div>
  <div style="overflow-x:auto">
    <table class="crm-table"><thead><tr><th>الاسم</th><th>الهاتف</th><th>البريد</th><th>النوع</th><th>المسؤول</th></tr></thead>
    <tbody>
    @forelse($rows as $row)
      <tr>
        <td><a class="crm-link" href="{{ route($crm->route('accounts.show'), $row) }}">{{ $row->name }}</a></td>
        <td>{{ $row->phone ?: '—' }}</td>
        <td>{{ $row->email ?: '—' }}</td>
        <td>{{ \App\Domains\Sales\Models\CrmAccount::typeLabel($row->account_type) }}</td>
        <td>{{ $row->assignee?->name }}</td>
      </tr>
    @empty
      <tr><td colspan="5" class="crm-muted" style="text-align:center;padding:20px">لا توجد حسابات بعد.</td></tr>
    @endforelse
    </tbody></table>
  </div>
</section>
@endsection
