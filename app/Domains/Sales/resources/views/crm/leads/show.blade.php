@extends('sales::crm.layout')

@section('title', $lead->name)

@use('App\Domains\Sales\Models\SalesLead')
@use('App\Domains\Sales\Models\SalesActivity')
@php
    $label = fn (array $map, $key) => $key === null || $key === '' ? '—' : ($map[$key] ?? $key);
    $editUrl = $crm->isAdmin()
        ? (Route::has('admin.sales.leads.edit') ? route('admin.sales.leads.edit', $lead) : null)
        : ($crm->canWorkOn($lead) && Route::has('employee.sales.leads.edit') ? route('employee.sales.leads.edit', $lead) : null);
    $classicUrl = match (true) {
        $crm->isAdmin() => Route::has('admin.sales.leads.show') ? route('admin.sales.leads.show', $lead) : null,
        $crm->isManager() => Route::has('employee.sales-manager.leads.show') ? route('employee.sales-manager.leads.show', $lead) : null,
        default => Route::has('employee.sales.leads.show') ? route('employee.sales.leads.show', $lead) : null,
    };
    $transferAction = $crm->isAdmin()
        ? (Route::has('admin.sales.distribution.assign') ? route('admin.sales.distribution.assign', $lead) : null)
        : ($crm->isManager() && Route::has('employee.sales-manager.leads.transfer') ? route('employee.sales-manager.leads.transfer', $lead) : null);
    $canRepAct = $crm->isRep() && $crm->canWorkOn($lead);
    $isLate = $lead->next_follow_up_at && $lead->next_follow_up_at->isPast() && $lead->isOpen();
    $dealLike = in_array($lead->stage, \App\Domains\Sales\Http\Controllers\Crm\SalesCrmController::DEAL_STAGES, true) || $order;
@endphp

@section('content')
<div x-data="{ tab: 'overview', actions: false, transfer: false, rel: { activities: true, history: true, conversations: false, order: {{ $order ? 'true' : 'false' }}, transfers: false, cases: false, documents: false, commission: false } }" style="display: flex; flex-direction: column; gap: 14px">
    <div style="position: sticky; top: 0; z-index: 30; background: #F5F5F5; padding: 6px 0; display: flex; flex-wrap: wrap; align-items: center; gap: 10px">
        <a href="{{ route($crm->route('leads.index')) }}" aria-label="رجوع للقائمة" style="width: 28px; height: 28px; border: 1px solid #534D64; border-radius: 3px; display: flex; align-items: center; justify-content: center; color: #534D64"><i class="fas fa-chevron-right"></i></a>
        <h1 style="margin: 0; font-size: 26px; font-weight: 400">{{ $lead->name }}</h1>
        <span class="crm-chip" style="cursor: default">{{ SalesLead::stageLabel((string) $lead->stage) }}</span>
        <span style="flex: 1"></span>
        @if($editUrl)<a href="{{ $editUrl }}" class="crm-btn crm-btn-muted">تعديل</a>@endif
        @if($dealLike)<a href="{{ route($crm->route('opportunities.show'), $lead->id) }}" class="crm-btn crm-btn-dark">الفرصة</a>@endif
        @if(Route::has($crm->route('cases.create')))
            <a href="{{ route($crm->route('cases.create'), ['sales_lead_id' => $lead->id]) }}" class="crm-btn crm-btn-outline">＋ تذكرة</a>
        @endif
        @if(Route::has($crm->route('activities.create')))
            <a href="{{ route($crm->route('activities.create'), ['type' => 'call']) }}" class="crm-btn crm-btn-outline">＋ مكالمة</a>
        @endif
        <div style="position: relative" @click.outside="actions = false">
            <button type="button" class="crm-btn crm-btn-slate" @click="actions = ! actions" :aria-expanded="actions">إجراءات <i class="fas fa-caret-down"></i></button>
            <div x-cloak x-show="actions" role="menu" style="position: absolute; left: 0; top: 34px; width: 220px; background: #627587; box-shadow: 0 4px 10px rgba(0,0,0,.3); padding: 4px 0; z-index: 40">
                @if($crm->canManage())
                    <a role="menuitem" href="{{ route($crm->route('leads.convert'), $lead->id) }}" style="display: block; padding: 8px 14px; color: #fff; font-size: 13px; font-weight: 700">تحويل لطالب (ربط بطلب)</a>
                    @if($transferAction)<button type="button" role="menuitem" @click="transfer = true; actions = false" style="display: block; width: 100%; text-align: start; padding: 8px 14px; color: #fff; font-size: 13px; background: transparent; border: 0; cursor: pointer">تحويل لموظف آخر</button>@endif
                @endif
                @if($canRepAct && $classicUrl)<a role="menuitem" href="{{ $classicUrl }}#pipeline" style="display: block; padding: 8px 14px; color: #fff; font-size: 13px">تحريك المرحلة (Pipeline)</a>@endif
                @if($crm->isAdmin() && $lead->isPendingWinApproval() && Route::has('admin.sales.win-approvals.index'))<a role="menuitem" href="{{ route('admin.sales.win-approvals.index') }}" style="display: block; padding: 8px 14px; color: #fff; font-size: 13px">اعتماد الفوز والعمولة</a>@endif
                @if($classicUrl)<a role="menuitem" href="{{ $classicUrl }}" style="display: block; padding: 8px 14px; color: #fff; font-size: 13px">فتح في الواجهة الكلاسيكية</a>@endif
            </div>
        </div>
    </div>

    @if($transferAction)
        <form method="post" action="{{ $transferAction }}" class="crm-card" x-cloak x-show="transfer" style="padding: 14px; display: flex; flex-wrap: wrap; gap: 10px; align-items: end">
            @csrf
            <label class="crm-label">تحويل لـ
                <select class="crm-select" name="to_user_id" required><option value="">— اختر —</option>@foreach($members as $m)@if((int) $m->id !== (int) $lead->assigned_to)<option value="{{ $m->id }}">{{ $m->name }}</option>@endif @endforeach</select>
            </label>
            <label class="crm-label" style="flex: 1 1 260px">السبب<input class="crm-input" type="text" name="reason" maxlength="500"></label>
            <button type="submit" class="crm-btn crm-btn-primary">تحويل</button>
            <button type="button" class="crm-btn crm-btn-outline" @click="transfer = false">إلغاء</button>
        </form>
    @endif

    <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start">
        <div style="flex: 999 1 680px; min-width: 0; display: flex; flex-direction: column; gap: 16px">
            <section class="crm-card">
                <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 8px; padding: 8px 10px 0">
                    <div class="crm-rtabs" role="tablist" aria-label="أقسام السجل" style="display: flex; gap: 2px; flex-wrap: wrap">
                        <button type="button" role="tab" :class="tab === 'overview' && 'is-active'" :aria-selected="tab === 'overview'" @click="tab = 'overview'">نظرة عامة</button>
                        <button type="button" role="tab" :class="tab === 'qualify' && 'is-active'" :aria-selected="tab === 'qualify'" @click="tab = 'qualify'">التأهيل</button>
                        <button type="button" role="tab" :class="tab === 'offer' && 'is-active'" :aria-selected="tab === 'offer'" @click="tab = 'offer'">العرض والدفع</button>
                        <button type="button" role="tab" :class="tab === 'other' && 'is-active'" :aria-selected="tab === 'other'" @click="tab = 'other'">أخرى</button>
                    </div>
                    @if($position)
                        <div style="display: flex; align-items: center; gap: 6px; font-size: 12px">
                            @if($position['prev'])<a href="{{ route($crm->route('leads.show'), $position['prev']) }}" aria-label="العميل السابق" class="crm-btn crm-btn-outline crm-btn-sm"><i class="fas fa-chevron-right"></i></a>@endif
                            <span>{{ $position['index'] }} من {{ $position['total'] }}</span>
                            @if($position['next'])<a href="{{ route($crm->route('leads.show'), $position['next']) }}" aria-label="العميل التالي" class="crm-btn crm-btn-outline crm-btn-sm"><i class="fas fa-chevron-left"></i></a>@endif
                        </div>
                    @endif
                </div>

                <div x-show="tab === 'overview'" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); column-gap: 28px; padding: 6px 18px 14px">
                    <div class="crm-field"><span class="k">الاسم</span><span class="v">{{ $lead->name }}</span>@if($editUrl)<a class="e" href="{{ $editUrl }}" aria-label="تعديل الاسم"><i class="fas fa-pen"></i></a>@endif</div>
                    <div class="crm-field"><span class="k">الموظف المسؤول</span><span class="v">{{ $lead->assignee?->name ?? 'بدون موظف' }}</span></div>
                    <div class="crm-field"><span class="k">الهاتف</span><span class="v" dir="ltr" style="text-align: end"><a class="crm-link" href="tel:{{ preg_replace('/[^\d+]/', '', (string) $lead->phone) }}">{{ $lead->phone }}</a></span>@if($editUrl)<a class="e" href="{{ $editUrl }}" aria-label="تعديل الهاتف"><i class="fas fa-pen"></i></a>@endif</div>
                    <div class="crm-field"><span class="k">الحالة</span><span class="v">{{ SalesLead::stageLabel((string) $lead->stage) }}</span></div>
                    <div class="crm-field"><span class="k">الإيميل</span><span class="v">{{ $lead->email ?: '—' }}</span>@if($editUrl)<a class="e" href="{{ $editUrl }}" aria-label="تعديل الإيميل"><i class="fas fa-pen"></i></a>@endif</div>
                    <div class="crm-field"><span class="k">الاهتمام</span><span class="v">{{ $lead->interestType?->name_ar ?? '—' }}</span>@if($editUrl)<a class="e" href="{{ $editUrl }}" aria-label="تعديل الاهتمام"><i class="fas fa-pen"></i></a>@endif</div>
                    <div class="crm-field"><span class="k">المصدر</span><span class="v">{{ SalesLead::sourceLabel((string) $lead->source) }}</span></div>
                    <div class="crm-field" @if($isLate) style="background: #FDF3F1" @endif><span class="k">المتابعة التالية</span><span class="v {{ $isLate ? 'crm-bad' : '' }}">{{ $lead->next_follow_up_at ? $lead->next_follow_up_at->format('d/m/Y H:i') : '—' }} · {{ $label(SalesLead::FOLLOW_UP_CHANNELS, $lead->follow_up_channel) }}{{ $isLate ? ' (متأخر)' : '' }}</span></div>
                    <div class="crm-field"><span class="k">الأولوية</span><span class="v">{{ SalesLead::priorityLabel($lead->priority) }}</span></div>
                    <div class="crm-field"><span class="k">التصنيف</span><span class="v">{{ $lead->category?->name ?? '—' }}</span></div>
                    <div class="crm-field"><span class="k">الكورس</span><span class="v">{{ $lead->linkedCourseTitle() ?? '—' }}</span></div>
                    <div class="crm-field"><span class="k">المجموعة</span><span class="v">{{ $lead->group?->name ?? '—' }}</span></div>
                    <div class="crm-field" style="grid-column: 1 / -1"><span class="k">ملاحظات</span><span class="v" style="white-space: pre-line; line-height: 1.7">{{ $lead->notes ?: '—' }}</span>@if($editUrl)<a class="e" href="{{ $editUrl }}" aria-label="تعديل الملاحظات"><i class="fas fa-pen"></i></a>@endif</div>
                </div>
                <div x-cloak x-show="tab === 'qualify'" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); column-gap: 28px; padding: 6px 18px 14px">
                    <div class="crm-field"><span class="k">النوع</span><span class="v">{{ $label(SalesLead::PROFILE_TYPES, $lead->profile_type) }}</span></div>
                    <div class="crm-field"><span class="k">الفئة العمرية</span><span class="v">{{ $label(SalesLead::AGE_RANGES, $lead->age_range) }}</span></div>
                    <div class="crm-field"><span class="k">المجال</span><span class="v">{{ $lead->field_domain ?: '—' }}</span></div>
                    <div class="crm-field"><span class="k">الخبرة</span><span class="v">{{ $lead->experience_level ?: '—' }}</span></div>
                    <div class="crm-field"><span class="k">نسبة الاهتمام</span><span class="v">{{ $lead->interest_pct ? $lead->interest_pct.'٪' : '—' }}</span></div>
                    <div class="crm-field"><span class="k">يقدر يدفع</span><span class="v">{{ $lead->can_pay === null ? '—' : ($lead->can_pay ? 'نعم' : 'لا') }}</span></div>
                    <div class="crm-field"><span class="k">تفضيل البداية</span><span class="v">{{ $lead->start_preference ?: '—' }}</span></div>
                    <div class="crm-field"><span class="k">الاعتراض</span><span class="v">{{ $label(SalesLead::OBJECTION_REASONS, $lead->objection_reason) }}</span></div>
                    <div class="crm-field" style="grid-column: 1 / -1"><span class="k">الدافع</span><span class="v" style="white-space: pre-line">{{ $lead->course_motivation ?: '—' }}</span></div>
                </div>
                <div x-cloak x-show="tab === 'offer'" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); column-gap: 28px; padding: 6px 18px 14px">
                    <div class="crm-field"><span class="k">السعر المعروض</span><span class="v">{{ $lead->offer_price !== null ? number_format((float) $lead->offer_price, 2).' ج.م' : '—' }}</span></div>
                    <div class="crm-field"><span class="k">الخصم</span><span class="v">{{ $lead->offer_discount ?: '—' }}</span></div>
                    <div class="crm-field"><span class="k">التقسيط</span><span class="v">{{ $lead->offer_installment_plan ?: '—' }}</span></div>
                    <div class="crm-field"><span class="k">طريقة الدفع</span><span class="v">{{ $label(SalesLead::PAYMENT_METHODS, $lead->payment_method) }}</span></div>
                    <div class="crm-field"><span class="k">قيمة الصفقة</span><span class="v">{{ $lead->expected_value !== null ? number_format((float) $lead->expected_value, 2).' ج.م' : '—' }}</span></div>
                    <div class="crm-field"><span class="k">الطلب المرتبط</span><span class="v">{{ $order ? '#'.$order->id.' — '.($order->status === 'approved' ? 'معتمد' : $order->status) : 'لا يوجد' }}</span></div>
                </div>
                <div x-cloak x-show="tab === 'other'" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); column-gap: 28px; padding: 6px 18px 14px">
                    <div class="crm-field"><span class="k">رقم العميل</span><span class="v">#{{ $lead->id }}</span></div>
                    <div class="crm-field"><span class="k">تاريخ الإنشاء</span><span class="v">{{ $lead->created_at?->format('d/m/Y H:i') }}</span></div>
                    <div class="crm-field"><span class="k">آخر تواصل</span><span class="v">{{ $lead->last_contacted_at?->format('d/m/Y H:i') ?? '—' }}</span></div>
                    <div class="crm-field"><span class="k">محاولات التواصل</span><span class="v">{{ (int) $lead->contact_attempts }}</span></div>
                    <div class="crm-field"><span class="k">الشركة</span><span class="v">{{ $lead->company ?: '—' }}</span></div>
                    <div class="crm-field"><span class="k">سبب الخسارة</span><span class="v">{{ $lead->lost_reason ?: '—' }}</span></div>
                </div>
            </section>

            @if($canRepAct)
                <section class="crm-card" style="padding: 14px; display: flex; flex-direction: column; gap: 12px">
                    <h2 style="margin: 0; font-size: 14px; font-weight: 700">تسجيل سريع</h2>
                    <form method="post" action="{{ route('employee.sales.leads.quick-activity', $lead) }}" style="display: flex; flex-wrap: wrap; gap: 6px; align-items: center">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ url()->current() }}">
                        <input type="hidden" name="type" value="call">
                        <span class="crm-muted" style="font-size: 13px">نتيجة مكالمة:</span>
                        @foreach(['interested', 'follow_up', 'no_answer', 'not_interested'] as $outcome)
                            <button type="submit" name="outcome" value="{{ $outcome }}" class="crm-btn crm-btn-outline crm-btn-sm">{{ SalesActivity::OUTCOMES[$outcome] ?? $outcome }}</button>
                        @endforeach
                    </form>
                    <form method="post" action="{{ route('employee.sales.leads.next-follow', $lead) }}" style="display: flex; flex-wrap: wrap; gap: 8px; align-items: end">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ url()->current() }}">
                        <label class="crm-label">المتابعة الجاية<input class="crm-input" type="datetime-local" name="next_follow_up_at" required value="{{ now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i') }}"></label>
                        <label class="crm-label">الإجراء
                            <select class="crm-select" name="follow_up_channel" required>@foreach(SalesLead::FOLLOW_UP_CHANNELS as $k => $v)<option value="{{ $k }}" @selected($lead->follow_up_channel === $k)>{{ $v }}</option>@endforeach</select>
                        </label>
                        <label class="crm-label" style="flex: 1 1 200px">ملاحظة<input class="crm-input" type="text" name="note" maxlength="500"></label>
                        <button type="submit" class="crm-btn crm-btn-primary">حفظ المتابعة</button>
                    </form>
                    @if($classicUrl)<a class="crm-link" style="font-size: 13px" href="{{ $classicUrl }}#pipeline">تحريك المرحلة بالتفاصيل (عرض، دفع، خسارة…) ←</a>@endif
                </section>
            @endif

            <section class="crm-card">
                <div class="crm-rel" style="display: flex; flex-wrap: wrap; align-items: center; gap: 6px; padding: 10px 14px; border-bottom: 1px solid #E5E7EB">
                    <h2 style="margin: 0; font-size: 14px; font-weight: 700; flex: 1 1 100px">العلاقات</h2>
                    <button type="button" :class="rel.activities && 'is-active'" @click="rel.activities = ! rel.activities" :aria-pressed="rel.activities">الأنشطة {{ $lead->next_follow_up_at ? 1 : 0 }}</button>
                    <button type="button" :class="rel.history && 'is-active'" @click="rel.history = ! rel.history" :aria-pressed="rel.history">السجل {{ $historyTotal }}</button>
                    <button type="button" :class="rel.conversations && 'is-active'" @click="rel.conversations = ! rel.conversations" :aria-pressed="rel.conversations">المحادثات {{ $conversations->count() }}</button>
                    <button type="button" :class="rel.order && 'is-active'" @click="rel.order = ! rel.order" :aria-pressed="rel.order">الطلبات {{ $order ? 1 : 0 }}</button>
                    <button type="button" :class="rel.transfers && 'is-active'" @click="rel.transfers = ! rel.transfers" :aria-pressed="rel.transfers">التحويلات {{ $transfers->count() }}</button>
                    <button type="button" :class="rel.cases && 'is-active'" @click="rel.cases = ! rel.cases" :aria-pressed="rel.cases">التذاكر {{ $cases->count() }}</button>
                    <button type="button" :class="rel.documents && 'is-active'" @click="rel.documents = ! rel.documents" :aria-pressed="rel.documents">المستندات {{ $documents->count() }}</button>
                    <button type="button" :class="rel.commission && 'is-active'" @click="rel.commission = ! rel.commission" :aria-pressed="rel.commission">العمولة</button>
                </div>

                <div class="crm-subpanel" x-show="rel.activities">
                    <div class="crm-subpanel-head"><h3>الأنشطة (اللي جاي)</h3></div>
                    <table class="crm-table crm-table-plain">
                        <thead><tr><th>الموضوع</th><th>القناة</th><th>الموعد</th><th>الموظف</th></tr></thead>
                        <tbody>
                            @if($lead->next_follow_up_at)
                                <tr><td>متابعة</td><td>{{ $label(SalesLead::FOLLOW_UP_CHANNELS, $lead->follow_up_channel) }}</td><td class="{{ $isLate ? 'crm-bad' : '' }}">{{ $lead->next_follow_up_at->format('d/m/Y H:i') }}</td><td>{{ $lead->assignee?->name ?? '—' }}</td></tr>
                            @else
                                <tr><td colspan="4" class="crm-muted" style="text-align: center">مفيش متابعة محددة.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="crm-subpanel" x-show="rel.history">
                    <div class="crm-subpanel-head"><h3>السجل (اللي حصل)</h3><span class="crm-muted" style="font-size: 12px">آخر {{ $history->count() }} من {{ $historyTotal }}</span></div>
                    <div style="overflow-x: auto">
                        <table class="crm-table crm-table-plain" style="min-width: 520px">
                            <thead><tr><th>الموضوع</th><th>النوع</th><th>النتيجة / التفاصيل</th><th>بواسطة</th><th>التاريخ</th></tr></thead>
                            <tbody>
                                @forelse($history as $act)
                                    <tr><td style="font-weight: 700">{{ $act->title ?: SalesActivity::typeLabel((string) $act->type) }}</td><td>{{ SalesActivity::typeLabel((string) $act->type) }}</td><td style="max-width: 320px">{{ $act->outcome ? (SalesActivity::OUTCOMES[$act->outcome] ?? $act->outcome) : \Illuminate\Support\Str::limit((string) $act->body, 90) }}</td><td>{{ $act->user?->name ?? 'النظام' }}</td><td class="crm-muted">{{ $act->created_at?->format('d/m H:i') }}</td></tr>
                                @empty
                                    <tr><td colspan="5" class="crm-muted" style="text-align: center">لا يوجد سجل بعد.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="crm-subpanel" x-cloak x-show="rel.conversations">
                    <div class="crm-subpanel-head"><h3>المحادثات</h3></div>
                    <table class="crm-table crm-table-plain">
                        <thead><tr><th>القناة</th><th>آخر رسالة</th><th>الحالة</th></tr></thead>
                        <tbody>
                            @forelse($conversations as $c)
                                <tr><td>{{ $c['channel'] }}</td><td>{{ $c['at']?->format('d/m H:i') ?? '—' }}</td><td>{{ $c['status'] ?: '—' }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="crm-muted" style="text-align: center">مفيش محادثات مربوطة.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="crm-subpanel" x-cloak x-show="rel.order">
                    <div class="crm-subpanel-head"><h3>الطلبات والمدفوعات</h3>@if(! $order && $crm->canManage())<a class="crm-btn crm-btn-primary crm-btn-sm" href="{{ route($crm->route('leads.convert'), $lead->id) }}">ربط بطلب</a>@endif</div>
                    <table class="crm-table crm-table-plain">
                        <thead><tr><th>الطلب</th><th>الكورس</th><th>المبلغ</th><th>المدفوع فعلاً</th><th>الحالة</th></tr></thead>
                        <tbody>
                            @if($order)
                                <tr><td style="font-weight: 700">#{{ $order->id }}</td><td>{{ $order->course?->title ?? '—' }}</td><td>{{ number_format((float) $order->amount, 2) }}</td><td class="crm-good">{{ number_format((float) $netPaid, 2) }}</td><td>{{ $order->status === 'approved' ? 'معتمد' : $order->status }}</td></tr>
                            @else
                                <tr><td colspan="5" class="crm-muted" style="text-align: center">لسه مفيش طلب مدفوع مرتبط — بيتربط تلقائي لما الطالب يدفع.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="crm-subpanel" x-cloak x-show="rel.transfers">
                    <div class="crm-subpanel-head"><h3>التحويلات</h3></div>
                    <table class="crm-table crm-table-plain">
                        <thead><tr><th>من</th><th>إلى</th><th>بواسطة</th><th>السبب</th><th>التاريخ</th></tr></thead>
                        <tbody>
                            @forelse($transfers as $t)
                                <tr><td>{{ $t->fromUser?->name ?? '—' }}</td><td>{{ $t->toUser?->name ?? '—' }}</td><td>{{ $t->transferredBy?->name ?? '—' }}</td><td>{{ $t->reason ?: '—' }}</td><td class="crm-muted">{{ $t->created_at?->format('d/m H:i') }}</td></tr>
                            @empty
                                <tr><td colspan="5" class="crm-muted" style="text-align: center">مفيش تحويلات.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="crm-subpanel" x-cloak x-show="rel.cases">
                    <div class="crm-subpanel-head"><h3>التذاكر</h3>
                        <a class="crm-btn crm-btn-primary crm-btn-sm" href="{{ route($crm->route('cases.create'), ['sales_lead_id' => $lead->id]) }}">＋ تذكرة</a>
                    </div>
                    <table class="crm-table crm-table-plain">
                        <thead><tr><th>الموضوع</th><th>الحالة</th><th>الأولوية</th></tr></thead>
                        <tbody>
                            @forelse($cases as $caseRow)
                                <tr>
                                    <td><a class="crm-link" href="{{ route($crm->route('cases.show'), $caseRow) }}">{{ $caseRow->subject }}</a></td>
                                    <td>{{ \App\Domains\Sales\Models\CrmCase::STATUSES[$caseRow->status] ?? $caseRow->status }}</td>
                                    <td>{{ \App\Domains\Sales\Models\CrmCase::PRIORITIES[$caseRow->priority] ?? $caseRow->priority }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="crm-muted" style="text-align: center">لا تذاكر.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="crm-subpanel" x-cloak x-show="rel.documents">
                    <div class="crm-subpanel-head"><h3>المستندات</h3>
                        <a class="crm-btn crm-btn-primary crm-btn-sm" href="{{ route($crm->route('documents.create'), ['sales_lead_id' => $lead->id]) }}">＋ مستند</a>
                    </div>
                    <table class="crm-table crm-table-plain">
                        <thead><tr><th>الاسم</th><th>النوع</th><th>التاريخ</th></tr></thead>
                        <tbody>
                            @forelse($documents as $doc)
                                <tr>
                                    <td><a class="crm-link" href="{{ route($crm->route('documents.show'), $doc) }}">{{ $doc->title }}</a></td>
                                    <td>{{ $doc->category ?: '—' }}</td>
                                    <td class="crm-muted">{{ $doc->created_at?->format('d/m H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="crm-muted" style="text-align: center">لا مستندات.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="crm-subpanel" x-cloak x-show="rel.commission">
                    <div class="crm-subpanel-head"><h3>العمولة</h3>
                        @if($dealLike)<a class="crm-btn crm-btn-dark crm-btn-sm" href="{{ route($crm->route('opportunities.show'), $lead->id) }}">عرض الفرصة</a>@endif
                    </div>
                    <table class="crm-table crm-table-plain">
                        <tbody>
                            <tr><td>المبلغ</td><td style="font-weight:700">{{ number_format($commissionAmount, 2) }}</td></tr>
                            <tr><td>مسترد</td><td>{{ number_format($commissionReversed, 2) }}</td></tr>
                            <tr><td>صافي</td><td class="crm-good">{{ number_format($commissionAmount - $commissionReversed, 2) }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <aside style="flex: 1 1 300px; display: flex; flex-direction: column; gap: 16px">
            <section class="crm-card" style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr))">
                <div class="crm-stat"><b>{{ $daysInStage ?? '—' }}</b><span>أيام في المرحلة الحالية</span></div>
                <div class="crm-stat" style="border-inline-start: 1px solid #E5E7EB"><b>{{ (int) $lead->contact_attempts }}</b><span>محاولات التواصل</span></div>
            </section>
            <section class="crm-card">
                <h2 style="margin: 0; padding: 10px 14px; font-size: 13px; font-weight: 700; border-bottom: 1px solid #E5E7EB">Timeline</h2>
                <ul style="margin: 0; padding: 6px 14px 12px; list-style: none">
                    @forelse($history->take(6) as $act)
                        <li style="display: flex; gap: 10px; padding: 9px 0; border-bottom: 1px solid #F0F1F3">
                            <span aria-hidden="true" style="flex: none; width: 28px; height: 28px; border: 1px solid #627587; border-radius: 4px; color: #627587; display: flex; align-items: center; justify-content: center; font-size: 12px"><i class="fas {{ match ($act->type) { 'call' => 'fa-phone', 'whatsapp' => 'fa-comment', 'meeting' => 'fa-handshake', 'stage_change' => 'fa-right-left', 'email' => 'fa-envelope', default => 'fa-note-sticky' } }}"></i></span>
                            <div style="display: flex; flex-direction: column; font-size: 12px; min-width: 0"><span style="font-weight: 700">{{ $act->title ?: SalesActivity::typeLabel((string) $act->type) }}</span><span class="crm-muted">{{ $act->user?->name ?? 'النظام' }} · {{ $act->created_at?->format('d/m H:i') }}</span></div>
                        </li>
                    @empty
                        <li class="crm-muted" style="padding: 12px 0; text-align: center; font-size: 13px">لا يوجد نشاط بعد.</li>
                    @endforelse
                </ul>
            </section>
        </aside>
    </div>
</div>
@endsection
