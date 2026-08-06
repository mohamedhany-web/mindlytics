@extends('layouts.employee')

@section('title', 'مركز مدير المبيعات')
@section('header', 'مركز مدير المبيعات')

@push('styles')
@include('employee.sales._styles')
<style>
    .sales-hub .dashboard-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04); padding: 1rem 1.25rem;
    }
    .sales-hub .panel-card { border-radius: 12px; border: 1px solid #e2e8f0; }
    .sales-hub .panel-card-head { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.25rem; }
</style>
@endpush

@section('content')
@php $user = auth()->user(); @endphp
<div class="space-y-6 sales-hub">
    <div class="dashboard-card flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                <i class="fas fa-users-cog text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-slate-900">مرحباً، {{ $user->name }}</h2>
                <p class="text-slate-600 text-sm mt-1">فريق: <strong>{{ $team->name }}</strong> — {{ $stats['team_members'] }} موظف مبيعات</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 shrink-0">
            <a href="{{ route('employee.sales-manager.leads.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold"><i class="fas fa-user-plus"></i> عملاء الفريق</a>
            <a href="{{ route('employee.sales-manager.commissions.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-amber-200 bg-amber-50 text-amber-900 text-sm font-semibold hover:bg-amber-100"><i class="fas fa-coins"></i> كوميشن الفريق</a>
            <a href="{{ route('employee.sales-manager.team-reports.edit') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50"><i class="fas fa-clipboard-check"></i> تقرير الفريق</a>
        </div>
    </div>

    @if(! empty($shiftLive) && ($shiftBoard ?? null))
    <section class="dashboard-card border-violet-200 bg-gradient-to-l from-violet-50/80 to-white">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-violet-700 uppercase">شيفتات الفريق — {{ $shiftLive['day_name'] ?? '' }} · {{ $shiftLive['hour_label'] ?? '' }}</p>
                <h3 class="text-lg font-black text-slate-900 mt-1">من على الشيفت الآن؟</h3>
                @if(count($shiftLive['active_now'] ?? []) > 0)
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($shiftLive['active_now'] as $row)
                            <span class="text-xs font-semibold rounded-lg bg-white border border-emerald-200 text-emerald-900 px-2.5 py-1">
                                {{ $row['user_name'] }}: {{ $row['channels_label'] }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-500 mt-2">لا أحد على شيفت نشط في هذه الساعة.</p>
                @endif
                @if(! empty($shiftLive['ownership']))
                    <p class="text-[11px] text-slate-500 mt-2">
                        @foreach(array_slice($shiftLive['ownership'], 0, 4) as $code => $own)
                            {{ config("sales_shifts.channels.{$code}.label", $code) }}→{{ $own['owner_name'] }}@if(! $loop->last) · @endif
                        @endforeach
                    </p>
                @endif
            </div>
            <div class="flex flex-wrap gap-2 shrink-0">
                <a href="{{ route('employee.sales-manager.shifts.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-semibold px-4 py-2 text-sm">
                    <i class="fas fa-calendar-week"></i> جدول الأسبوع
                </a>
                <a href="{{ route('employee.sales-manager.shift-swaps.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-amber-300 bg-amber-50 text-amber-900 font-semibold px-4 py-2 text-sm">
                    تبديلات
                    @if(($pendingShiftSwaps ?? 0) > 0)
                        <span class="bg-amber-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full">{{ $pendingShiftSwaps }}</span>
                    @endif
                </a>
            </div>
        </div>
    </section>
    @elseif($shiftBoard === null)
    <section class="dashboard-card border-amber-200 bg-amber-50/50 text-sm text-amber-900">
        <p><i class="fas fa-info-circle ml-1"></i> خطة الشيفتات غير مفعّلة — اطلب من الإدارة استيراد الجدول.</p>
    </section>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
        @php
            $statCards = [
                ['label' => 'إجمالي Leads', 'value' => $stats['total'], 'icon' => 'fa-users'],
                ['label' => 'نشط', 'value' => $stats['active'], 'icon' => 'fa-fire'],
                ['label' => 'متابعات متأخرة', 'value' => $stats['followups_overdue'], 'icon' => 'fa-bell'],
                ['label' => 'متابعات اليوم', 'value' => $stats['followups_today'], 'icon' => 'fa-calendar-day'],
                ['label' => 'بلا تواصل', 'value' => $stats['stale'], 'icon' => 'fa-hourglass-end'],
                ['label' => 'أعضاء الفريق', 'value' => $stats['team_members'], 'icon' => 'fa-user-friends'],
            ];
        @endphp
        @foreach($statCards as $card)
            <div class="dashboard-card">
                <p class="text-xs font-medium text-slate-500 mb-1">{{ $card['label'] }}</p>
                <div class="flex items-center justify-between">
                    <p class="text-2xl font-bold text-slate-800 tabular-nums">{{ $card['value'] }}</p>
                    <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center text-slate-500"><i class="fas {{ $card['icon'] }}"></i></div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="panel-card lg:col-span-2">
            <div class="panel-card-head flex flex-wrap items-center justify-between gap-2">
                <h2 class="font-bold text-slate-900">أعضاء الفريق</h2>
                <a href="{{ route('employee.sales-manager.attendance.index') }}" class="text-xs text-emerald-700 font-semibold hover:underline">حضور الفريق</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse($members as $member)
                    @php
                        $u = $member->user;
                        $uid = (int) ($u->id ?? $member->user_id);
                        $onLeave = in_array($uid, $onLeaveIds ?? [], true);
                        $schedule = $u?->workSchedule;
                    @endphp
                    <li class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-semibold text-slate-900">{{ $u->name ?? '—' }}</p>
                                @if($onLeave)
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-violet-50 text-violet-800 border border-violet-200">إجازة</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $u->email ?? '' }}</p>
                            <p class="text-[11px] text-slate-500 mt-1.5 flex flex-wrap gap-x-3 gap-y-1">
                                <span>
                                    <i class="fas fa-clock text-indigo-500 ml-0.5"></i>
                                    @if($schedule)
                                        {{ $schedule->timeRangeLabel() }}
                                    @else
                                        بدون جدول
                                    @endif
                                </span>
                                <span>
                                    <i class="fas fa-calendar-day text-amber-500 ml-0.5"></i>
                                    راحة: {{ $u?->weeklyOffDayLabel() ?? '—' }}
                                </span>
                                <span>
                                    <i class="fas fa-users text-slate-400 ml-0.5"></i>
                                    {{ (int) ($leadCounts[$uid] ?? 0) }} عميل
                                </span>
                                @php $mShift = $memberShiftToday[$uid] ?? null; @endphp
                                @if($mShift && ($mShift['is_working_today'] ?? false) && ($mShift['current'] ?? null))
                                    <span class="text-violet-700 font-semibold">
                                        <i class="fas fa-headset text-violet-500 ml-0.5"></i>
                                        الآن: {{ $mShift['current']['channels_label'] }}
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            <a href="{{ route('employee.sales-manager.team.show', $uid) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-800 text-white text-xs font-bold hover:bg-slate-900">
                                <i class="fas fa-id-card"></i> عرض الملف
                            </a>
                            <a href="{{ route('employee.sales-manager.team.report', $uid) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700">
                                <i class="fas fa-chart-line"></i> تقرير الأداء
                            </a>
                            <a href="{{ route('employee.sales-manager.shifts.show', $uid) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-violet-200 text-violet-800 text-xs font-bold hover:bg-violet-50">
                                <i class="fas fa-calendar-week"></i> الشيفت
                            </a>
                            <a href="{{ route('employee.sales-manager.attendance.employee', $uid) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50">
                                الحضور
                            </a>
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-slate-500">لا يوجد أعضاء في الفريق بعد.</li>
                @endforelse
            </ul>
        </div>
        <div class="panel-card">
            <div class="panel-card-head"><h2 class="font-bold text-slate-900">اختصارات</h2></div>
            <div class="p-5 space-y-2">
                <a href="{{ route('employee.sales-manager.shifts.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700"><i class="fas fa-calendar-week text-violet-600"></i> شيفتات وقنوات الفريق</a>
                <a href="{{ route('employee.sales-manager.shift-swaps.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700"><i class="fas fa-right-left text-amber-600"></i> تبديل الشيفتات @if(($pendingShiftSwaps ?? 0) > 0)<span class="text-[10px] bg-amber-500 text-white px-1.5 rounded-full">{{ $pendingShiftSwaps }}</span>@endif</a>
                <a href="{{ route('employee.sales-manager.scorecard.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700"><i class="fas fa-shield-halved text-teal-600"></i> مركز الرقابة</a>
                <a href="{{ route('employee.sales-manager.whatsapp.inbox.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700"><i class="fab fa-whatsapp text-emerald-600"></i> محادثات الفريق</a>
                <a href="{{ route('employee.sales-manager.follow-ups.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700"><i class="fas fa-clipboard-list text-teal-600"></i> رقابة المتابعات</a>
                <a href="{{ route('employee.sales-manager.daily-reports.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700"><i class="fas fa-clipboard-list text-sky-600"></i> تقارير الأعضاء</a>
                <a href="{{ route('employee.sales-manager.commissions.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700"><i class="fas fa-coins text-amber-600"></i> كوميشن الفريق</a>
                <a href="{{ route('employee.sales-manager.presence.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700"><i class="fas fa-satellite-dish text-rose-600"></i> مراقبة التواجد (Live)</a>
                <a href="{{ route('employee.sales-manager.live-board') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700"><i class="fas fa-tv text-teal-600"></i> اللوحة الحية SOS</a>
                <a href="{{ route('employee.sales-manager.pipeline') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700"><i class="fas fa-project-diagram text-violet-600"></i> Pipeline الرحلة</a>
                <a href="{{ route('employee.sales-manager.transfer.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700"><i class="fas fa-exchange-alt text-amber-600"></i> تحويل Leads</a>
                <a href="{{ route('employee.sales-manager.attendance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700"><i class="fas fa-clock text-violet-600"></i> حضور الفريق</a>
            </div>
        </div>
    </div>

    <div class="panel-card">
        <div class="panel-card-head flex justify-between items-center">
            <h2 class="font-bold text-slate-900">Task Queue — الفريق</h2>
            <a href="{{ route('employee.sales-manager.follow-ups.index', ['filter' => 'overdue']) }}" class="text-xs text-emerald-700 font-semibold">عرض الكل</a>
        </div>
        <ul class="divide-y divide-slate-100">
            @forelse(($taskQueue ?? collect()) as $item)
                @php $lead = $item['lead']; @endphp
                <li class="px-5 py-3 hover:bg-slate-50">
                    <a href="{{ route('employee.sales-manager.leads.show', $lead) }}" class="block">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-semibold text-slate-900">{{ $lead->name }}</span>
                            <span class="text-xs text-slate-500">{{ $lead->assignee->name ?? '—' }}</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">{{ $item['reason'] }} — {{ $item['next_action'] }}</p>
                    </a>
                </li>
            @empty
                <li class="px-5 py-8 text-center text-slate-500">لا توجد مهام عاجلة للفريق اليوم.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
