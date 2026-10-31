@extends('sales::crm.layout')
@section('title', $account->name)
@section('content')
@include('sales::crm.partials.module-header', ['title' => $account->name, 'subtitle' => 'Account', 'actions' => [
  ['label' => 'تعديل', 'url' => route($crm->route('accounts.edit'), $account), 'class' => 'crm-btn-slate', 'icon' => 'fas fa-pen'],
  ['label' => '＋ جهة اتصال', 'url' => route($crm->route('contacts.create'), ['crm_account_id' => $account->id]), 'class' => 'crm-btn-primary'],
]])
<div class="crm-record-grid">
  <section class="crm-card">
    <h2 class="crm-panel-title">التفاصيل</h2>
    <div style="padding:12px 16px">
      <div class="crm-field"><span class="k">الهاتف</span><span class="v">{{ $account->phone ?: '—' }}</span></div>
      <div class="crm-field"><span class="k">البريد</span><span class="v">{{ $account->email ?: '—' }}</span></div>
      <div class="crm-field"><span class="k">النوع</span><span class="v">{{ \App\Domains\Sales\Models\CrmAccount::typeLabel($account->account_type) }}</span></div>
      <div class="crm-field"><span class="k">الصناعة</span><span class="v">{{ $account->industry ?: '—' }}</span></div>
      <div class="crm-field"><span class="k">الموقع</span><span class="v">{{ $account->website ?: '—' }}</span></div>
      <div class="crm-field"><span class="k">الوصف</span><span class="v">{{ $account->description ?: '—' }}</span></div>
    </div>
    <div class="crm-subpanel">
      <div class="crm-subpanel-head"><h3>جهات الاتصال ({{ $account->contacts->count() }})</h3></div>
      <table class="crm-table"><thead><tr><th>الاسم</th><th>الهاتف</th><th>البريد</th></tr></thead><tbody>
        @forelse($account->contacts as $c)
          <tr><td><a class="crm-link" href="{{ route($crm->route('contacts.show'), $c) }}">{{ $c->name }}</a></td><td>{{ $c->phone?:'—' }}</td><td>{{ $c->email?:'—' }}</td></tr>
        @empty<tr><td colspan="3" class="crm-muted" style="padding:12px;text-align:center">لا جهات اتصال</td></tr>@endforelse
      </tbody></table>
    </div>
    <div class="crm-subpanel">
      <div class="crm-subpanel-head"><h3>عملاء محتملون مرتبطون</h3></div>
      <table class="crm-table"><thead><tr><th>الاسم</th><th>المرحلة</th></tr></thead><tbody>
        @forelse($leads as $l)
          <tr><td><a class="crm-link" href="{{ route($crm->route('leads.show'), $l) }}">{{ $l->name }}</a></td><td>{{ \App\Domains\Sales\Models\SalesLead::stageLabel((string)$l->stage) }}</td></tr>
        @empty<tr><td colspan="2" class="crm-muted" style="padding:12px;text-align:center">لا يوجد</td></tr>@endforelse
      </tbody></table>
    </div>
    <div class="crm-subpanel">
      <div class="crm-subpanel-head"><h3>التذاكر ({{ $account->cases->count() }})</h3>
        <a class="crm-btn crm-btn-primary crm-btn-sm" href="{{ route($crm->route('cases.create')) }}">＋ تذكرة</a>
      </div>
      <table class="crm-table"><thead><tr><th>الموضوع</th><th>الحالة</th></tr></thead><tbody>
        @forelse($account->cases as $caseRow)
          <tr><td><a class="crm-link" href="{{ route($crm->route('cases.show'), $caseRow) }}">{{ $caseRow->subject }}</a></td><td>{{ \App\Domains\Sales\Models\CrmCase::STATUSES[$caseRow->status] ?? $caseRow->status }}</td></tr>
        @empty<tr><td colspan="2" class="crm-muted" style="padding:12px;text-align:center">لا تذاكر</td></tr>@endforelse
      </tbody></table>
    </div>
  </section>
  <aside class="crm-card" style="padding:14px">
    <div class="crm-field"><span class="k">المسؤول</span><span class="v">{{ $account->assignee?->name }}</span></div>
    <div class="crm-field"><span class="k">التذاكر</span><span class="v">{{ $account->cases->count() }}</span></div>
    <div class="crm-field"><span class="k">جهات الاتصال</span><span class="v">{{ $account->contacts->count() }}</span></div>
  </aside>
</div>
@endsection
