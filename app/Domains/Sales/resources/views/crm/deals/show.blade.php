@extends('sales::crm.layout')

@section('title', 'صفقة — '.$lead->name)

@use('App\Domains\Sales\Models\SalesLead')
@php
    $won = in_array($lead->stage, SalesLead::WON_LIKE_STAGES, true);
    $approveUrl = $crm->isAdmin() && $lead->isPendingWinApproval() && Route::has($crm->route('win-approvals.index')) ? route($crm->route('win-approvals.index')) : null;
    $commission = (float) ($lead->commission_amount ?? 0);
    $reversed = (float) ($lead->commission_reversed_amount ?? 0);
    $commissionStatus = match (true) {
        $reversed > 0 => ['مسترجع '.number_format($reversed, 2), 'crm-bad'],
        (bool) $lead->commission_settled_at => ['مصروفة', 'crm-good'],
        (bool) $lead->won_confirmed_at => ['معتمدة', 'crm-good'],
        $lead->isPendingWinApproval() => ['بانتظار اعتماد الإدارة', 'crm-warn'],
        default => ['لسه الصفقة ما فازتش', 'crm-muted'],
    };
@endphp

@section('content')
<div x-data="{ tab: 'basic' }" style="display: flex; flex-direction: column; gap: 14px">
    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px">
        <a href="{{ route($crm->route('deals.index')) }}" aria-label="رجوع للصفقات" style="width: 28px; height: 28px; border: 1px solid #534D64; border-radius: 3px; display: flex; align-items: center; justify-content: center; color: #534D64"><i class="fas fa-chevron-right"></i></a>
        <h1 style="margin: 0; font-size: 26px; font-weight: 400">{{ $lead->linkedCourseTitle() ? $lead->linkedCourseTitle().' — ' : '' }}{{ $lead->name }}</h1>
        <span style="flex: 1"></span>
        <a href="{{ route($crm->route('leads.show'), $lead->id) }}" class="crm-btn crm-btn-muted">ملف العميل</a>
        @if(! $order && $crm->canManage())<a href="{{ route($crm->route('leads.convert'), $lead->id) }}" class="crm-btn crm-btn-slate">ربط بطلب</a>@endif
        @if($approveUrl)<a href="{{ $approveUrl }}" class="crm-btn crm-btn-primary">اعتماد وصرف العمولة</a>@endif
    </div>

    <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start">
        <div style="flex: 999 1 680px; min-width: 0; display: flex; flex-direction: column; gap: 16px">
            <section class="crm-card">
                <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 8px; padding: 8px 10px 0">
                    <div class="crm-rtabs" role="tablist" aria-label="أقسام الصفقة" style="display: flex; gap: 2px">
                        <button type="button" role="tab" :class="tab === 'basic' && 'is-active'" :aria-selected="tab === 'basic'" @click="tab = 'basic'">الأساسي</button>
                        <button type="button" role="tab" :class="tab === 'pay' && 'is-active'" :aria-selected="tab === 'pay'" @click="tab = 'pay'">الدفع والعمولة</button>
                    </div>
                    @if($position)
                        <div style="display: flex; align-items: center; gap: 6px; font-size: 12px">
                            @if($position['prev'])<a href="{{ route($crm->route('deals.show'), $position['prev']) }}" aria-label="الصفقة السابقة" class="crm-btn crm-btn-outline crm-btn-sm"><i class="fas fa-chevron-right"></i></a>@endif
                            <span>{{ $position['index'] }} من {{ $position['total'] }}</span>
                            @if($position['next'])<a href="{{ route($crm->route('deals.show'), $position['next']) }}" aria-label="الصفقة التالية" class="crm-btn crm-btn-outline crm-btn-sm"><i class="fas fa-chevron-left"></i></a>@endif
                        </div>
                    @endif
                </div>
                <div x-show="tab === 'basic'" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); column-gap: 28px; padding: 6px 18px 14px">
                    <div class="crm-field"><span class="k">العميل</span><span class="v"><a class="crm-link" href="{{ route($crm->route('leads.show'), $lead->id) }}">{{ $lead->name }}</a></span></div>
                    <div class="crm-field"><span class="k">الطالب</span><span class="v">{{ $lead->student?->name ? $lead->student->name.' (#'.$lead->student->id.')' : 'لسه ما اتحوّلش' }}</span></div>
                    <div class="crm-field"><span class="k">الكورس</span><span class="v">{{ $lead->linkedCourseTitle() ?? '—' }}</span></div>
                    <div class="crm-field"><span class="k">المرحلة</span><span class="v {{ $won ? 'crm-good' : '' }}">{{ SalesLead::stageLabel((string) $lead->stage) }}</span></div>
                    <div class="crm-field"><span class="k">قيمة الصفقة</span><span class="v">{{ $lead->expected_value !== null ? number_format((float) $lead->expected_value, 2).' ج.م' : '—' }}</span></div>
                    <div class="crm-field"><span class="k">السعر المعروض</span><span class="v">{{ $lead->offer_price !== null ? number_format((float) $lead->offer_price, 2).' ج.م' : '—' }}</span></div>
                    <div class="crm-field"><span class="k">الإغلاق</span><span class="v">{{ $lead->closed_at?->format('d/m/Y') ?? '—' }}</span></div>
                    <div class="crm-field"><span class="k">الموظف</span><span class="v">{{ $lead->assignee?->name ?? '—' }}</span></div>
                    <div class="crm-field" style="grid-column: 1 / -1"><span class="k">جاي من</span><span class="v">{{ $order ? ($lead->order_linked_at ? 'اتربط بالطلب #'.$order->id.' في '.$lead->order_linked_at->format('d/m/Y H:i') : 'مربوط بالطلب #'.$order->id) : 'لسه مفيش طلب مرتبط — بيتربط تلقائي لما الطالب يدفع.' }}</span></div>
                    @if($lead->won_override_reason)
                        <div class="crm-field" style="grid-column: 1 / -1"><span class="k">سبب الاعتماد اليدوي</span><span class="v">{{ $lead->won_override_reason }}</span></div>
                    @endif
                </div>
                <div x-cloak x-show="tab === 'pay'" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); column-gap: 28px; padding: 6px 18px 14px">
                    <div class="crm-field"><span class="k">مبلغ الطلب</span><span class="v">{{ $order ? number_format((float) $order->amount, 2).' ج.م' : '—' }}</span></div>
                    <div class="crm-field"><span class="k">المدفوع فعلاً</span><span class="v crm-good">{{ $netPaid !== null ? number_format($netPaid, 2).' ج.م' : '—' }}</span></div>
                    <div class="crm-field"><span class="k">العمولة</span><span class="v">{{ $commission > 0 ? number_format($commission, 2).' ج.م' : '—' }}</span></div>
                    <div class="crm-field"><span class="k">حالة العمولة</span><span class="v {{ $commissionStatus[1] }}">{{ $commissionStatus[0] }}</span></div>
                </div>
            </section>

            <section class="crm-card">
                <div class="crm-subpanel">
                    <div class="crm-subpanel-head"><h3>المدفوعات</h3></div>
                    <div style="overflow-x: auto">
                        <table class="crm-table crm-table-plain" style="min-width: 560px">
                            <thead><tr><th>الطلب</th><th>الفاتورة</th><th>المبلغ</th><th>المدفوع فعلاً</th><th>طريقة الدفع</th><th>الحالة</th></tr></thead>
                            <tbody>
                                @if($order)
                                    <tr>
                                        <td style="font-weight: 700">#{{ $order->id }}</td>
                                        <td>{{ $invoice?->invoice_number ?? '—' }}</td>
                                        <td>{{ number_format((float) $order->amount, 2) }}</td>
                                        <td class="crm-good">{{ number_format((float) $netPaid, 2) }}</td>
                                        <td>{{ $payment?->payment_method ?? $order->payment_method ?? '—' }}</td>
                                        <td>{{ $payment?->status === 'refunded' ? 'مسترد' : ($order->status === 'approved' ? 'مدفوع' : $order->status) }}</td>
                                    </tr>
                                @else
                                    <tr><td colspan="6" class="crm-muted" style="text-align: center">مفيش طلب مدفوع مرتبط.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="crm-subpanel" style="margin-top: 0">
                    <div class="crm-subpanel-head"><h3>العمولة</h3>@if($approveUrl)<a class="crm-btn crm-btn-primary crm-btn-sm" href="{{ $approveUrl }}">اعتماد وصرف</a>@endif</div>
                    <div style="overflow-x: auto">
                        <table class="crm-table crm-table-plain" style="min-width: 560px">
                            <thead><tr><th>رقم الحركة</th><th>النوع</th><th>المبلغ</th><th>الوصف</th><th>التاريخ</th></tr></thead>
                            <tbody>
                                @forelse($commissionTxns as $t)
                                    <tr><td style="font-weight: 700">{{ $t->transaction_number }}</td><td class="{{ $t->type === 'debit' ? 'crm-bad' : 'crm-good' }}">{{ $t->type === 'debit' ? 'استرجاع' : 'صرف' }}</td><td>{{ number_format((float) $t->amount, 2) }}</td><td style="max-width: 360px">{{ \Illuminate\Support\Str::limit((string) $t->description, 90) }}</td><td class="crm-muted">{{ $t->created_at?->format('d/m/Y') }}</td></tr>
                                @empty
                                    <tr><td colspan="5" class="crm-muted" style="text-align: center">{{ $commissionStatus[0] }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        <aside style="flex: 1 1 300px; display: flex; flex-direction: column; gap: 16px">
            <section class="crm-card" style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr))">
                <div class="crm-stat"><b class="crm-good">{{ $netPaid !== null ? number_format($netPaid, 0) : '—' }}</b><span>المدفوع فعلاً (ج.م)</span></div>
                <div class="crm-stat" style="border-inline-start: 1px solid #E5E7EB"><b>{{ $daysInStage ?? '—' }}</b><span>أيام في المرحلة</span></div>
            </section>
            <section class="crm-card">
                <h2 style="margin: 0; padding: 10px 14px; font-size: 13px; font-weight: 700; border-bottom: 1px solid #E5E7EB">Timeline</h2>
                <ul style="margin: 0; padding: 6px 14px 12px; list-style: none">
                    @forelse($history->take(6) as $act)
                        <li style="padding: 9px 0; border-bottom: 1px solid #F0F1F3; font-size: 12px"><div style="font-weight: 700">{{ $act->title ?: \App\Domains\Sales\Models\SalesActivity::typeLabel((string) $act->type) }}</div><div class="crm-muted">{{ $act->user?->name ?? 'النظام' }} · {{ $act->created_at?->format('d/m H:i') }}</div></li>
                    @empty
                        <li class="crm-muted" style="padding: 12px 0; text-align: center; font-size: 13px">لا يوجد نشاط بعد.</li>
                    @endforelse
                </ul>
            </section>
        </aside>
    </div>
</div>
@endsection
