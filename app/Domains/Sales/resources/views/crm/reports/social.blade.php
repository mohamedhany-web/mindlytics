@extends('sales::crm.layout')
@section('title', 'تقرير المراسلة')
@section('content')
@include('sales::crm.partials.module-header', [
    'title' => 'تقرير ماسنجر وواتساب',
    'subtitle' => 'اختَر موظفًا وفترة — السيستم يجيب ردوده من الإنبوكس تلقائيًا',
])

@if(!empty($canSyncMeta) && \Illuminate\Support\Facades\Route::has('admin.meta-social.sync-history'))
<section class="crm-card" style="margin-bottom:14px;padding:14px">
    <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:end;justify-content:space-between">
        <div style="flex:1;min-width:220px">
            <strong style="display:block;margin-bottom:4px"><i class="fab fa-meta" style="margin-inline-end:6px"></i>مزامنة التاريخ من Meta</strong>
            <p class="crm-muted" style="margin:0;font-size:12px">بتجيب المحادثات والرسائل القديمة من ماسنجر/إنستجرام عشان التقرير والإنبوكس يبقوا كاملين. لو السيرفر بيقطع الطلب الطويل استخدم الأمر: <code style="font-size:11px">php artisan meta-social:sync-history</code></p>
        </div>
        <form method="post" action="{{ route('admin.meta-social.sync-history') }}" style="display:flex;flex-wrap:wrap;gap:8px;align-items:end"
              onsubmit="return confirm('هنسحب المحادثات والرسائل القديمة من Meta. العملية ممكن تاخد وقت — متابعة؟')">
            @csrf
            <input type="hidden" name="redirect_to" value="social">
            <label class="crm-label" style="min-width:200px">الصفحة
                <select name="page_id" class="crm-select">
                    <option value="">كل الصفحات النشطة</option>
                    @foreach($metaPages as $page)
                        <option value="{{ $page->id }}">{{ $page->page_name ?: $page->page_id }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="crm-btn crm-btn-dark"><i class="fas fa-cloud-download-alt"></i> مزامنة من Meta</button>
        </form>
    </div>
    @if($metaPages->isEmpty())
        <p class="crm-muted" style="margin:10px 0 0;font-size:12px">مفيش صفحات نشطة — من صفحات Meta زامن الصفحات وفعّلها الأول.</p>
    @endif
</section>
@endif

<form method="get" class="crm-card" style="padding:14px;margin-bottom:14px;display:flex;flex-wrap:wrap;gap:10px;align-items:end">
    <label class="crm-label" style="min-width:200px">الموظف
        @if($crm->isRep())
            <input type="hidden" name="user_id" value="{{ $filterUserId }}">
            <input type="text" class="crm-input" value="{{ $selectedUser?->name ?? auth()->user()->name }}" readonly>
        @else
            <select name="user_id" class="crm-select">
                <option value="">كل الفريق</option>
                @foreach($staffOptions as $staff)
                    <option value="{{ $staff->id }}" @selected($filterUserId === (int) $staff->id)>{{ $staff->name }}</option>
                @endforeach
            </select>
        @endif
    </label>
    <label class="crm-label" style="min-width:140px">من
        <input type="date" name="from" value="{{ $from->toDateString() }}" class="crm-input">
    </label>
    <label class="crm-label" style="min-width:140px">إلى
        <input type="date" name="to" value="{{ $to->toDateString() }}" class="crm-input">
    </label>
    <label class="crm-label" style="min-width:150px">القناة
        <select name="channel" class="crm-select">
            <option value="all" @selected($filterChannel === 'all')>الكل</option>
            <option value="whatsapp" @selected($filterChannel === 'whatsapp')>واتساب فقط</option>
            <option value="messenger" @selected($filterChannel === 'messenger')>ماسنجر / إنستجرام</option>
        </select>
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
    <button class="crm-btn crm-btn-primary" type="submit"><i class="fas fa-filter"></i> عرض الردود</button>
    @if($filterUserId || $q || $filterStage || $filterChannel !== 'all')
        <a class="crm-btn crm-btn-slate" href="{{ route($crm->route('reports.social')) }}">مسح الفلاتر</a>
    @endif
</form>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-bottom:14px">
    <div class="crm-card crm-stat"><b>{{ number_format($totals['whatsapp'] + $totals['messenger']) }}</b><span>إجمالي الردود في الفترة</span></div>
    <div class="crm-card crm-stat"><b>{{ number_format($totals['whatsapp']) }}</b><span>ردود واتساب</span></div>
    <div class="crm-card crm-stat"><b>{{ number_format($totals['messenger']) }}</b><span>ردود ماسنجر / إنستجرام</span></div>
    @if($filterUserId)
        <div class="crm-card crm-stat"><b>{{ number_format($totals['employee_replies']) }}</b><span>ردود {{ $selectedUser?->name }} المعروضة</span></div>
    @endif
    <div class="crm-card crm-stat"><b>{{ number_format($totals['messenger_customers']) }}</b><span>عملاء ماسنجر (صفحة)</span></div>
    <div class="crm-card crm-stat"><b>{{ number_format($totals['whatsapp_customers']) }}</b><span>عملاء واتساب (صفحة)</span></div>
</div>

@if($filterUserId && $selectedUser)
<section class="crm-card" style="margin-bottom:16px">
    <div class="crm-dashlet-head">
        <i class="fas fa-user-check"></i>
        <h2>ردود {{ $selectedUser->name }} — من {{ $from->format('d/m/Y') }} إلى {{ $to->format('d/m/Y') }}</h2>
        <span style="margin-inline-start:auto;font-size:12px;opacity:.9">{{ number_format($employeeReplies->count()) }} رسالة (حد أقصى 500 لكل قناة)</span>
    </div>
    <div style="overflow-x:auto">
        <table class="crm-table">
            <thead>
                <tr>
                    <th>الوقت</th>
                    <th>القناة</th>
                    <th>العميل</th>
                    <th>الهاتف</th>
                    <th>نص الرد</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employeeReplies as $reply)
                    @php
                        $channelLabel = match($reply['channel']) {
                            'whatsapp' => 'واتساب',
                            'instagram' => 'إنستجرام',
                            default => 'ماسنجر',
                        };
                    @endphp
                    <tr>
                        <td style="white-space:nowrap">{{ $reply['at']?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td><span class="crm-status-pill">{{ $channelLabel }}</span></td>
                        <td><strong>{{ $reply['customer'] }}</strong></td>
                        <td dir="ltr">{{ $reply['phone'] }}</td>
                        <td style="max-width:420px;white-space:pre-wrap;word-break:break-word">{{ \Illuminate\Support\Str::limit($reply['body'], 280) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="crm-muted" style="text-align:center;padding:18px">
                            مفيش ردود لـ {{ $selectedUser->name }} في الفترة دي
                            @if($filterChannel !== 'all') (حسب فلتر القناة) @endif.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endif

<section class="crm-card" style="margin-bottom:16px">
    <div class="crm-dashlet-head"><i class="fas fa-comments"></i><h2>ملخص ردود الموظفين (تلقائي من الإنبوكس)</h2></div>
    <div style="overflow-x:auto">
        <table class="crm-table">
            <thead>
                <tr>
                    <th>الموظف</th>
                    <th>واتساب</th>
                    <th>ماسنجر / إنستجرام</th>
                    <th>الإجمالي</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($replyRows as $row)
                    <tr>
                        <td><strong>{{ $row['user']->name }}</strong></td>
                        <td>{{ $row['whatsapp'] }}</td>
                        <td>{{ $row['messenger'] }}</td>
                        <td class="crm-good">{{ $row['total'] }}</td>
                        <td>
                            @unless($crm->isRep())
                                <a class="crm-link" href="{{ route($crm->route('reports.social'), ['user_id' => $row['user']->id, 'from' => $from->toDateString(), 'to' => $to->toDateString(), 'channel' => $filterChannel]) }}">عرض ردوده</a>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="crm-muted" style="text-align:center;padding:18px">لا ردود في الفترة المحددة.</td></tr>
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
