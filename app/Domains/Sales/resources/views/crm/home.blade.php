@extends('sales::crm.layout')

@section('title', $crm->isRep() ? 'يومي' : 'لوحة الفريق')

@php
    $leadUrl = fn ($id) => route($crm->route('leads.show'), $id);
    $dealUrl = fn ($id) => route($crm->route('deals.show'), $id);
    $fmtFollow = function ($lead) {
        if (! $lead->next_follow_up_at) {
            return ['—', ''];
        }
        $at = $lead->next_follow_up_at;
        $label = $at->isToday() ? 'النهارده '.$at->format('H:i') : $at->format('d/m H:i');

        return [$label, $at->isPast() ? 'crm-bad' : ''];
    };
@endphp

@section('content')
<div style="display: flex; flex-direction: column; gap: 16px">
    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px">
        <h1 class="crm-title">{{ $crm->isRep() ? 'يومي' : ($crm->isAdmin() ? 'لوحة المبيعات' : 'لوحة الفريق') }}</h1>
        <span style="flex: 1"></span>
        <span class="crm-muted" style="font-size: 13px">{{ $today->translatedFormat('l j F Y') }}</span>
    </div>

    @unless($crm->isRep())
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 12px">
            <div class="crm-card crm-stat"><b class="crm-good">{{ number_format($headline['revenue'], 0) }}</b><span>إيراد طلبات معتمدة النهارده (ج.م)</span></div>
            <div class="crm-card crm-stat"><b>{{ $headline['wins'] }}</b><span>صفقات فازت النهارده</span></div>
            <div class="crm-card crm-stat"><b>{{ array_sum(array_column($team, 'worked')) }}</b><span>أشخاص اتعمل عليهم</span></div>
            <div class="crm-card crm-stat"><b class="{{ $headline['overdue'] ? 'crm-bad' : '' }}">{{ $headline['overdue'] }}</b><span>متابعات متأخرة</span></div>
            <div class="crm-card crm-stat"><b class="crm-warn">{{ collect($decisions)->sum('count') }}</b><span>مستني قرارك</span></div>
        </div>
    @endunless

    <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start">
        <div style="flex: 1 1 600px; min-width: 0; display: flex; flex-direction: column; gap: 16px">

            @unless($crm->isRep())
                <section class="crm-card">
                    <div class="crm-dashlet-head"><i class="fas fa-users"></i><h2>{{ $crm->isAdmin() ? 'موظفو المبيعات النهارده' : 'الفريق النهارده' }}</h2></div>
                    <div style="overflow-x: auto">
                        <table class="crm-table" style="min-width: 720px">
                            <thead><tr><th>الموظف</th><th>اتعمل عليهم</th><th>رد / محاولات</th><th>مؤهل</th><th>عروض</th><th>مدفوع</th><th>متأخر</th><th>الأداء</th></tr></thead>
                            <tbody>
                                @forelse($team as $row)
                                    <tr @if($row['overall'] < 70) style="background: #FDF3F1" @endif>
                                        <td><a class="crm-link" href="{{ route($crm->route('leads.index'), ['assigned_to' => $row['user']->id, 'quick' => 'all']) }}">{{ $row['user']->name }}</a></td>
                                        <td>{{ $row['worked'] }} / {{ $row['worked_target'] }}</td>
                                        <td>{{ $row['answered'] }} / {{ $row['attempts'] }}</td>
                                        <td>{{ $row['qualified'] }}</td>
                                        <td>{{ $row['offers'] }}</td>
                                        <td class="crm-good">{{ $row['paid'] }}</td>
                                        <td class="{{ $row['overdue'] ? 'crm-bad' : '' }}">{{ $row['overdue'] }}</td>
                                        <td class="{{ $row['overall'] >= 100 ? 'crm-good' : ($row['overall'] >= 70 ? 'crm-warn' : 'crm-bad') }}">{{ number_format($row['overall'], 0) }}٪</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="crm-muted" style="text-align: center; padding: 18px">لا يوجد موظفو مبيعات في نطاقك بعد.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @endunless

            <section class="crm-card">
                <div class="crm-dashlet-head"><i class="fas fa-phone"></i><h2>{{ $crm->isRep() ? 'متابعاتي النهارده' : 'متابعات الفريق المستحقة' }}</h2></div>
                <div style="overflow-x: auto">
                    <table class="crm-table" style="min-width: 560px">
                        <thead><tr><th>العميل</th><th>المرحلة</th><th>الموعد</th><th>القناة</th>@unless($crm->isRep())<th>الموظف</th>@endunless</tr></thead>
                        <tbody>
                            @forelse($followUps as $lead)
                                @php([$when, $cls] = $fmtFollow($lead))
                                <tr @if($cls) style="background: #FDF3F1" @endif>
                                    <td><a class="crm-link" href="{{ $leadUrl($lead->id) }}">{{ $lead->name }}</a></td>
                                    <td>{{ \App\Domains\Sales\Models\SalesLead::stageLabel((string) $lead->stage) }}</td>
                                    <td class="{{ $cls }}">{{ $when }}</td>
                                    <td>{{ \App\Domains\Sales\Models\SalesLead::FOLLOW_UP_CHANNELS[$lead->follow_up_channel] ?? '—' }}</td>
                                    @unless($crm->isRep())<td>{{ $lead->assignee?->name ?? '—' }}</td>@endunless
                                </tr>
                            @empty
                                <tr><td colspan="5" class="crm-muted" style="text-align: center; padding: 18px">مفيش متابعات مستحقة النهارده.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="crm-dashlet-foot">
                    <span>{{ $followUpsTotal }} متابعة · {{ $overdueTotal }} متأخرة</span>
                    <a href="{{ route($crm->route('leads.index'), ['quick' => 'overdue']) }}">المتأخر</a>
                    <a href="{{ route($crm->route('leads.index'), ['quick' => 'today']) }}">كل متابعات النهارده</a>
                </div>
            </section>

            <section class="crm-card">
                <div class="crm-dashlet-head"><i class="fas fa-tasks"></i><h2>{{ $crm->isRep() ? 'أنشطتي' : 'أنشطة الفريق' }} — المتأخر و7 أيام</h2></div>
                <div style="overflow-x: auto">
                    <table class="crm-table" style="min-width: 560px">
                        <thead><tr><th>النشاط</th><th>على</th><th>الموعد</th>@unless($crm->isRep())<th>المكلَّف</th>@endunless</tr></thead>
                        <tbody>
                            @forelse($planned as $a)
                                <tr @if($a->isOverdue()) style="background: #FDF3F1" @endif>
                                    <td><i class="fas {{ \App\Domains\Sales\Models\SalesPlannedActivity::KIND_ICONS[$a->kind] ?? 'fa-circle' }}" style="color: #534D64"></i> <a class="crm-link" href="{{ route($crm->route('activities.show'), $a->id) }}">{{ $a->title }}</a></td>
                                    <td>@if($a->lead)<a class="crm-link" style="font-weight: 400" href="{{ $leadUrl($a->lead->id) }}">{{ $a->lead->name }}</a>@else{{ $a->student?->name ?? '—' }}@endif</td>
                                    <td class="{{ $a->isOverdue() ? 'crm-bad' : '' }}">{{ $a->starts_at->format('d/m H:i') }}</td>
                                    @unless($crm->isRep())<td>{{ $a->assignee?->name ?? '—' }}</td>@endunless
                                </tr>
                            @empty
                                <tr><td colspan="4" class="crm-muted" style="text-align: center; padding: 18px">مفيش أنشطة مجدولة. جدول مكالمة أو اجتماع من ملف العميل.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="crm-dashlet-foot">
                    <span>{{ $plannedOverdue }} متأخرة</span>
                    <a href="{{ route($crm->route('activities.index'), ['quick' => 'overdue']) }}">المتأخر</a>
                    <a href="{{ route($crm->route('calendar')) }}">الكاليندر</a>
                </div>
            </section>

            <section class="crm-card">
                <div class="crm-dashlet-head"><i class="fas fa-lightbulb"></i><h2>{{ $crm->isRep() ? 'صفقاتي المفتوحة' : 'الصفقات المفتوحة' }}</h2></div>
                <div style="overflow-x: auto">
                    <table class="crm-table" style="min-width: 520px">
                        <thead><tr><th>الصفقة</th><th>المرحلة</th><th>المبلغ</th>@unless($crm->isRep())<th>الموظف</th>@endunless</tr></thead>
                        <tbody>
                            @forelse($openDeals as $lead)
                                <tr>
                                    <td><a class="crm-link" href="{{ $dealUrl($lead->id) }}">{{ $lead->name }}{{ $lead->linkedCourseTitle() ? ' — '.$lead->linkedCourseTitle() : '' }}</a></td>
                                    <td>{{ \App\Domains\Sales\Models\SalesLead::stageLabel((string) $lead->stage) }}{{ $lead->stage === \App\Domains\Sales\Models\SalesLead::WON_STAGE ? ' (بانتظار الاعتماد)' : '' }}</td>
                                    <td>{{ $lead->expected_value !== null ? number_format((float) $lead->expected_value, 0).' ج.م' : '—' }}</td>
                                    @unless($crm->isRep())<td>{{ $lead->assignee?->name ?? '—' }}</td>@endunless
                                </tr>
                            @empty
                                <tr><td colspan="4" class="crm-muted" style="text-align: center; padding: 18px">مفيش صفقات مفتوحة.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="crm-dashlet-foot"><span>{{ $openDealsTotal }} صفقة</span><a href="{{ route($crm->route('deals.index')) }}">كل الصفقات</a></div>
            </section>

            @if($meetings->isNotEmpty())
                <section class="crm-card">
                    <div class="crm-dashlet-head"><i class="fas fa-calendar"></i><h2>{{ $crm->isRep() ? 'اجتماعاتي' : 'اجتماعات الفريق' }} (7 أيام)</h2></div>
                    <table class="crm-table">
                        <thead><tr><th>العميل</th><th>الموعد</th>@unless($crm->isRep())<th>الموظف</th>@endunless</tr></thead>
                        <tbody>
                            @foreach($meetings as $lead)
                                <tr><td><a class="crm-link" href="{{ $leadUrl($lead->id) }}">{{ $lead->name }}</a></td><td>{{ $lead->next_follow_up_at?->format('d/m H:i') }}</td>@unless($crm->isRep())<td>{{ $lead->assignee?->name ?? '—' }}</td>@endunless</tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>
            @endif
        </div>

        <div style="flex: 1 1 360px; min-width: 0; display: flex; flex-direction: column; gap: 16px">
            @if($crm->isRep())
                <section class="crm-card">
                    <div class="crm-dashlet-head"><i class="fas fa-bullseye"></i><h2>هدفي النهارده</h2>@if(Route::has('employee.sales.kpi.index'))<a href="{{ route('employee.sales.kpi.index') }}" style="color: #fff; font-size: 12px">التفاصيل</a>@endif</div>
                    <div style="padding: 14px; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; font-size: 13px">
                        @foreach($kpi['lines'] as $line)
                            <div style="display: flex; flex-direction: column; gap: 4px">
                                <span class="crm-muted" style="font-size: 12px">{{ $line['label'] }}</span>
                                <b style="font-size: 18px" class="{{ $line['status'] === 'met' ? 'crm-good' : '' }}">{{ $line['actual'] }} / {{ (int) $line['target'] }}</b>
                                <span class="crm-bar" style="height: 6px"><span style="height: 6px; width: {{ min(100, $line['pct']) }}%; background: {{ $line['status'] === 'met' ? '#0E6B44' : ($line['status'] === 'near' ? '#627587' : '#534D64') }}"></span></span>
                            </div>
                        @endforeach
                    </div>
                    <div class="crm-dashlet-foot"><span>الإجمالي {{ number_format($kpi['overall_pct'], 0) }}٪ — {{ $kpi['status_label'] }}</span></div>
                </section>
                <section class="crm-card">
                    <div class="crm-dashlet-head"><i class="fas fa-coins"></i><h2>عمولتي — هذا الشهر</h2></div>
                    <div style="padding: 14px; display: flex; flex-direction: column; gap: 6px">
                        <b class="crm-good" style="font-size: 24px">{{ number_format($commission['earned'], 2) }} ج.م</b>
                        <span class="crm-muted" style="font-size: 13px">{{ $commission['count'] }} صفقة معتمدة · {{ $commission['pending'] }} بانتظار اعتماد الإدارة @if($commission['reversed'] > 0) · <span class="crm-bad">{{ number_format($commission['reversed'], 2) }} مسترجع</span>@endif</span>
                        @if(Route::has('employee.sales.commissions.index'))<a class="crm-link" style="font-size: 13px" href="{{ route('employee.sales.commissions.index') }}">تفاصيل العمولات ←</a>@endif
                    </div>
                </section>
            @else
                <section class="crm-card">
                    <div class="crm-dashlet-head"><i class="fas fa-gavel"></i><h2>مستني قرارك</h2></div>
                    <table class="crm-table">
                        <tbody>
                            @foreach($decisions as $d)
                                <tr><td>{{ $d['label'] }}</td><td style="font-weight: 700; width: 60px" class="{{ $d['count'] ? 'crm-warn' : 'crm-muted' }}">{{ $d['count'] }}</td><td style="width: 60px">@if($d['url'])<a class="crm-link" href="{{ $d['url'] }}">افتح</a>@endif</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>
                <section class="crm-card">
                    <div class="crm-dashlet-head"><i class="fas fa-filter"></i><h2>قمع النهارده</h2></div>
                    @php($top = max(1, $funnel[0]['value'] ?? 1))
                    <div style="padding: 14px; display: flex; flex-direction: column; gap: 9px; font-size: 12.5px">
                        @foreach($funnel as $i => $step)
                            <div style="display: flex; align-items: center; gap: 10px"><span style="width: 100px">{{ $step['label'] }}</span><span class="crm-bar" style="flex: 1"><span style="width: {{ min(100, round($step['value'] / $top * 100)) }}%; background: {{ $i === count($funnel) - 1 ? '#0E6B44' : ($i < 3 ? '#534D64' : '#627587') }}"></span></span><b style="width: 40px; text-align: end">{{ $step['value'] }}</b></div>
                        @endforeach
                    </div>
                </section>
                @if(count($duplicates))
                    <section class="crm-card">
                        <div class="crm-dashlet-head"><i class="fas fa-clone"></i><h2>أرقام مسجّلة عند أكتر من موظف ({{ count($duplicates) }})</h2></div>
                        <table class="crm-table crm-table-plain">
                            <tbody>
                                @foreach($duplicates as $dup)
                                    <tr><td dir="ltr" style="text-align: end">…{{ substr($dup['key'], -6) }}</td><td>@foreach($dup['leads'] as $l)<a class="crm-link" href="{{ $leadUrl($l->id) }}">{{ $l->name }}</a>@if(! $loop->last)، @endif @endforeach</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="crm-dashlet-foot"><span>اختار مين يكمل وحوّل الباقي من ملف العميل.</span></div>
                    </section>
                @endif
            @endif

            <section class="crm-card">
                <div class="crm-dashlet-head"><i class="fas fa-stream"></i><h2>Activity Stream</h2></div>
                <ul style="margin: 0; padding: 0; list-style: none; font-size: 13px">
                    @forelse($stream as $act)
                        <li style="padding: 10px 12px; border-bottom: 1px solid #E5E7EB; line-height: 1.7">
                            <strong>{{ $act->user?->name ?? 'النظام' }}</strong> — {{ $act->title ?: \App\Domains\Sales\Models\SalesActivity::typeLabel((string) $act->type) }}
                            @if($act->lead) · <a class="crm-link" href="{{ $leadUrl($act->lead->id) }}">{{ $act->lead->name }}</a>@endif
                            <div class="crm-muted" style="font-size: 12px">{{ $act->created_at?->diffForHumans() }}</div>
                        </li>
                    @empty
                        <li class="crm-muted" style="padding: 14px; text-align: center">لا يوجد نشاط بعد.</li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>
</div>
@endsection
