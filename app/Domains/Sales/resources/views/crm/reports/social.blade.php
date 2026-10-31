@extends('sales::crm.layout')
@section('title', 'تقرير المراسلة')
@section('content')
@include('sales::crm.partials.module-header', [
    'title' => 'تقرير ماسنجر وواتساب',
    'subtitle' => 'ردود الموظفين التلقائية + العملاء ومراحلهم في القنوات',
])

<form method="get" class="crm-card" style="padding:14px;margin-bottom:14px;display:flex;flex-wrap:wrap;gap:10px;align-items:end">
    <label class="crm-label" style="min-width:140px">من
        <input type="date" name="from" value="{{ $from->toDateString() }}" class="crm-input">
    </label>
    <label class="crm-label" style="min-width:140px">إلى
        <input type="date" name="to" value="{{ $to->toDateString() }}" class="crm-input">
    </label>
    <label class="crm-label" style="min-width:180px">بحث عميل
        <input type="search" name="q" value="{{ $q }}" placeholder="اسم أو رقم…" class="crm-input">
    </label>
    <label class="crm-label" style="min-width:160px">مرحلة ماسنجر
        <select name="stage" class="crm-select">
            <option value="">الكل</option>
            @foreach($stages as $key => $label)
                <option value="{{ $key }}" @selected($filterStage === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </label>
    <button class="crm-btn crm-btn-primary" type="submit">تطبيق</button>
</form>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-bottom:14px">
    <div class="crm-card crm-stat"><b>{{ number_format($totals['whatsapp'] + $totals['messenger']) }}</b><span>إجمالي الردود في الفترة</span></div>
    <div class="crm-card crm-stat"><b>{{ number_format($totals['whatsapp']) }}</b><span>ردود واتساب</span></div>
    <div class="crm-card crm-stat"><b>{{ number_format($totals['messenger']) }}</b><span>ردود ماسنجر / إنستجرام</span></div>
    <div class="crm-card crm-stat"><b>{{ number_format($totals['messenger_customers']) }}</b><span>عملاء ماسنجر (صفحة)</span></div>
    <div class="crm-card crm-stat"><b>{{ number_format($totals['whatsapp_customers']) }}</b><span>عملاء واتساب (صفحة)</span></div>
</div>

<section class="crm-card" style="margin-bottom:16px">
    <div class="crm-dashlet-head"><i class="fas fa-comments"></i><h2>ردود الموظفين (تلقائي من الإنبوكس)</h2></div>
    <div style="overflow-x:auto">
        <table class="crm-table">
            <thead>
                <tr>
                    <th>الموظف</th>
                    <th>واتساب</th>
                    <th>ماسنجر / إنستجرام</th>
                    <th>الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @forelse($replyRows as $row)
                    <tr>
                        <td><strong>{{ $row['user']->name }}</strong></td>
                        <td>{{ $row['whatsapp'] }}</td>
                        <td>{{ $row['messenger'] }}</td>
                        <td class="crm-good">{{ $row['total'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="crm-muted" style="text-align:center;padding:18px">لا ردود في الفترة المحددة.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="crm-card" style="margin-bottom:16px">
    <div class="crm-dashlet-head">
        <i class="fab fa-facebook-messenger"></i>
        <h2>عملاء ماسنجر / إنستجرام — لحد فين واصلين</h2>
        @if(Route::has('admin.meta-social.leads.index'))
            <a href="{{ route('admin.meta-social.leads.index') }}" class="crm-btn crm-btn-sm crm-btn-slate">مركز ليدات Meta</a>
        @endif
    </div>
    <div style="overflow-x:auto">
        <table class="crm-table">
            <thead>
                <tr>
                    <th>العميل</th>
                    <th>الهاتف</th>
                    <th>المنصة</th>
                    <th>المرحلة</th>
                    <th>الموظف</th>
                    <th>CRM</th>
                    <th>آخر رسالة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($messengerCustomers as $c)
                    @php
                        $stageKey = (string) ($c->lead_stage ?? 'intake');
                        $stageLabel = $stages[$stageKey] ?? $stageKey;
                        $lead = $c->salesLead;
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $c->displayName() }}</strong>
                            <div class="crm-muted" style="font-size:11px">{{ $c->page?->page_name }}</div>
                        </td>
                        <td dir="ltr">{{ $c->phone && ! str_starts_with((string)$c->phone, 'meta_') ? $c->phone : '—' }}</td>
                        <td>{{ $c->platform === 'instagram' ? 'Instagram' : 'Messenger' }}</td>
                        <td><span class="crm-status-pill">{{ $stageLabel }}</span></td>
                        <td>{{ $c->assignee?->name ?? '—' }}</td>
                        <td>
                            @if($lead)
                                <a class="crm-link" href="{{ route($crm->route('leads.show'), $lead) }}">{{ $lead->name }}</a>
                                <div class="crm-muted" style="font-size:11px">{{ \App\Domains\Sales\Models\SalesLead::stageLabel((string)$lead->stage) }}</div>
                            @else
                                <span class="crm-muted">غير مربوط</span>
                            @endif
                        </td>
                        <td>{{ $c->last_message_at?->format('d/m H:i') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="crm-muted" style="text-align:center;padding:18px">لا محادثات ماسنجر في النطاق.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($messengerCustomers, 'links'))
        <div style="padding:10px">{{ $messengerCustomers->links() }}</div>
    @endif
</section>

<section class="crm-card">
    <div class="crm-dashlet-head"><i class="fab fa-whatsapp"></i><h2>عملاء واتساب — مرحلة CRM</h2></div>
    <div style="overflow-x:auto">
        <table class="crm-table">
            <thead>
                <tr>
                    <th>العميل</th>
                    <th>الهاتف</th>
                    <th>المرحلة</th>
                    <th>الموظف</th>
                    <th>CRM</th>
                    <th>آخر رسالة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($whatsappCustomers as $c)
                    @php $lead = $c->salesLead; @endphp
                    <tr>
                        <td><strong>{{ $c->displayName() }}</strong></td>
                        <td dir="ltr">{{ $c->formattedPhone() }}</td>
                        <td>
                            @if($lead)
                                <span class="crm-status-pill">{{ \App\Domains\Sales\Models\SalesLead::stageLabel((string)$lead->stage) }}</span>
                            @else
                                <span class="crm-muted">بدون Lead</span>
                            @endif
                        </td>
                        <td>{{ $c->assignee?->name ?? '—' }}</td>
                        <td>
                            @if($lead)
                                <a class="crm-link" href="{{ route($crm->route('leads.show'), $lead) }}">فتح</a>
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $c->last_message_at?->format('d/m H:i') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="crm-muted" style="text-align:center;padding:18px">لا محادثات واتساب في النطاق.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($whatsappCustomers, 'links'))
        <div style="padding:10px">{{ $whatsappCustomers->links() }}</div>
    @endif
</section>
@endsection
