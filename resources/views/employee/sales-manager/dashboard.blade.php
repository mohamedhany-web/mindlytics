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
    .sales-hub .panel-card { border-radius: 12px; border: 1px solid #e2e8f0; background: #fff; overflow: hidden; }
    .sales-hub .panel-card-head { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 0.875rem 1.25rem; }
    .sales-hub .kpi-grid { display: grid; gap: 0.75rem; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    @media (min-width: 768px) { .sales-hub .kpi-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    @media (min-width: 1280px) { .sales-hub .kpi-grid { grid-template-columns: repeat(6, minmax(0, 1fr)); } }
    .sales-hub .dot { width: 0.55rem; height: 0.55rem; border-radius: 9999px; display: inline-block; }
    .sales-hub .spark { display: flex; align-items: flex-end; gap: 4px; height: 72px; }
    .sales-hub .spark span { flex: 1; border-radius: 4px 4px 0 0; background: #0d9488; min-height: 4px; }
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    $kpis = $hub['kpis'] ?? [];
    $live = $hub['live_status'] ?? [];
    $ranking = $hub['ranking'] ?? [];
    $pipeline = $hub['pipeline'] ?? [];
    $tasks = $hub['tasks'] ?? [];
    $timeline = $hub['timeline'] ?? [];
    $alerts = $hub['alerts'] ?? [];
    $approvals = $hub['approvals'] ?? [];
    $attendance = $hub['attendance'] ?? [];
    $analytics = $hub['analytics'] ?? [];
    $leaderboard = $hub['leaderboard'] ?? [];
    $compare = $hub['compare'] ?? null;
    $shiftLive = $hub['shift_live'] ?? [];
    $shiftBoard = $hub['shift_board'] ?? null;
    $pendingShiftSwaps = $hub['pending_shift_swaps'] ?? 0;
    $members = $hub['members'] ?? collect();
    $fmtMoney = fn ($v) => number_format((float) $v, 0);
    $statusDot = [
        'on_call' => 'bg-emerald-500',
        'meeting' => 'bg-sky-500',
        'online' => 'bg-emerald-400',
        'away' => 'bg-amber-400',
        'offline' => 'bg-slate-400',
    ];
@endphp

<div class="space-y-6 sales-hub">
    {{-- Header --}}
    <div class="dashboard-card flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                <i class="fas fa-users-cog text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-slate-900">مرحباً، {{ $user->name }}</h2>
                <p class="text-slate-600 text-sm mt-1">
                    فريق: <strong>{{ $team->name }}</strong>
                    — {{ $kpis['team_members'] ?? 0 }} موظف
                    · {{ $date->copy()->locale('ar')->translatedFormat('l d M Y') }}
                </p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 shrink-0 items-center">
            <form method="get" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ $date->toDateString() }}"
                       class="rounded-lg border border-slate-300 px-3 py-2 text-sm"
                       onchange="this.form.submit()">
                @if($compareA)<input type="hidden" name="compare_a" value="{{ $compareA }}">@endif
                @if($compareB)<input type="hidden" name="compare_b" value="{{ $compareB }}">@endif
            </form>
            <a href="{{ route('employee.sales-manager.scorecard.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold"><i class="fas fa-shield-halved"></i> مركز الرقابة</a>
            <a href="{{ route('employee.sales-manager.live-board') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50"><i class="fas fa-bolt"></i> SOS Live</a>
            <a href="{{ route('employee.sales-manager.leads.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold"><i class="fas fa-user-plus"></i> عملاء الفريق</a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(count($alerts) > 0)
    <section class="dashboard-card border-rose-200 bg-rose-50/40">
        <h3 class="text-sm font-black text-rose-900 mb-3"><i class="fas fa-bell ml-1"></i> تنبيهات الآن</h3>
        <ul class="space-y-2">
            @foreach($alerts as $alert)
                <li class="flex items-start gap-2 text-sm {{ ($alert['level'] ?? '') === 'danger' ? 'text-rose-800' : 'text-amber-900' }}">
                    <i class="fas {{ ($alert['level'] ?? '') === 'danger' ? 'fa-circle-exclamation text-rose-600' : 'fa-triangle-exclamation text-amber-600' }} mt-0.5"></i>
                    <span class="flex-1">{{ $alert['message'] }}</span>
                    @if(!empty($alert['user_id']))
                        <a href="{{ route('employee.sales-manager.team.show', $alert['user_id']) }}" class="text-xs font-bold text-slate-600 hover:underline">الملف</a>
                    @endif
                </li>
            @endforeach
        </ul>
    </section>
    @endif

    {{-- Quick jump --}}
    <nav class="flex flex-wrap gap-2 text-xs font-semibold">
        <a href="#hub-kpis" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200">المؤشرات</a>
        <a href="#hub-live" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200">المراقبة</a>
        <a href="#hub-ranking" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200">الترتيب</a>
        <a href="#hub-pipeline" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200">البايبلاين</a>
        <a href="#hub-timeline" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200">الجدول الزمني</a>
        <a href="#hub-compare" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200">مقارنة</a>
    </nav>

    {{-- 1. Executive KPIs --}}
    <section id="hub-kpis">
        <h3 class="text-sm font-black text-slate-800 mb-3">نظرة تنفيذية — اليوم</h3>
        <div class="kpi-grid">
            @php
                $kpiCards = [
                    ['label' => 'أفراد الفريق', 'value' => $kpis['team_members'] ?? 0, 'icon' => 'fa-users'],
                    ['label' => 'متصل الآن', 'value' => $kpis['online_now'] ?? 0, 'icon' => 'fa-circle text-emerald-500'],
                    ['label' => 'مكالمات اليوم', 'value' => $kpis['calls_today'] ?? 0, 'icon' => 'fa-phone'],
                    ['label' => 'محادثات مؤهلة', 'value' => $kpis['qualified_today'] ?? 0, 'icon' => 'fa-comments'],
                    ['label' => 'اجتماعات', 'value' => $kpis['meetings_today'] ?? 0, 'icon' => 'fa-handshake'],
                    ['label' => 'عروض أسعار', 'value' => $kpis['proposals_today'] ?? 0, 'icon' => 'fa-file-invoice'],
                    ['label' => 'صفقات مقفولة', 'value' => $kpis['won_today'] ?? 0, 'icon' => 'fa-check'],
                    ['label' => 'صفقات ضائعة', 'value' => $kpis['lost_today'] ?? 0, 'icon' => 'fa-times'],
                    ['label' => 'مبيعات اليوم', 'value' => $fmtMoney($kpis['revenue_today'] ?? 0), 'icon' => 'fa-coins'],
                    ['label' => 'مبيعات الشهر', 'value' => $fmtMoney($kpis['revenue_month'] ?? 0), 'icon' => 'fa-chart-line'],
                    ['label' => 'تحقيق التارجت', 'value' => ($kpis['target_pct'] ?? 0).'%', 'icon' => 'fa-bullseye'],
                    ['label' => 'نسبة التحويل', 'value' => ($kpis['conversion_pct'] ?? '—').(isset($kpis['conversion_pct']) ? '%' : ''), 'icon' => 'fa-percent'],
                    ['label' => 'متوسط زمن الرد', 'value' => isset($kpis['avg_response_minutes']) ? $kpis['avg_response_minutes'].' د' : '—', 'icon' => 'fa-clock'],
                    ['label' => 'يعملون الآن', 'value' => $kpis['working_now'] ?? 0, 'icon' => 'fa-briefcase'],
                ];
            @endphp
            @foreach($kpiCards as $card)
                <div class="dashboard-card !p-3">
                    <p class="text-[11px] font-medium text-slate-500 mb-1">{{ $card['label'] }}</p>
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-xl font-bold text-slate-800 tabular-nums truncate">{{ $card['value'] }}</p>
                        <i class="fas {{ $card['icon'] }} text-slate-400 text-sm"></i>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Approvals + Attendance strip --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <section class="dashboard-card lg:col-span-2">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-black text-slate-900">موافقات معلّقة</h3>
                <span class="text-xs font-bold bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full">{{ $approvals['total'] ?? 0 }}</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <a href="{{ route('employee.sales-manager.attendance.index') }}" class="rounded-xl border border-slate-200 p-3 hover:bg-slate-50">
                    <p class="text-xs text-slate-500">حضور</p>
                    <p class="text-2xl font-black text-slate-900">{{ $approvals['attendance'] ?? 0 }}</p>
                </a>
                <a href="{{ route('employee.sales-manager.shift-swaps.index') }}" class="rounded-xl border border-slate-200 p-3 hover:bg-slate-50">
                    <p class="text-xs text-slate-500">تبديل شيفت</p>
                    <p class="text-2xl font-black text-slate-900">{{ $approvals['shift_swaps'] ?? 0 }}</p>
                </a>
                <a href="{{ route('employee.sales-manager.whatsapp.queue.index') }}" class="rounded-xl border border-slate-200 p-3 hover:bg-slate-50">
                    <p class="text-xs text-slate-500">طابور واتساب</p>
                    <p class="text-2xl font-black text-slate-900">{{ $approvals['whatsapp_queue'] ?? 0 }}</p>
                </a>
            </div>
        </section>
        <section class="dashboard-card">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-black text-slate-900">الحضور والإنتاجية</h3>
                <a href="{{ route('employee.sales-manager.attendance.index') }}" class="text-xs font-semibold text-emerald-700">التفاصيل</a>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-slate-500">وقت العمل</span><b class="tabular-nums">{{ $attendance['working_label'] ?? '0س 0د' }}</b></div>
                <div class="flex justify-between"><span class="text-slate-500">وقت منتج</span><b class="tabular-nums text-emerald-700">{{ $attendance['productive_label'] ?? '0س 0د' }}</b></div>
                <div class="flex justify-between"><span class="text-slate-500">خمول / بعيد</span><b class="tabular-nums text-amber-700">{{ $attendance['idle_label'] ?? '0س 0د' }}</b></div>
                <p class="text-[11px] text-slate-500 pt-1">يعملون: {{ $attendance['working_count'] ?? 0 }} · لم يسجّلوا: {{ $attendance['not_clocked_in'] ?? 0 }}</p>
            </div>
        </section>
    </div>

    {{-- Shift live --}}
    @if($shiftBoard)
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
            </div>
            <div class="flex flex-wrap gap-2 shrink-0">
                <a href="{{ route('employee.sales-manager.shifts.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-semibold px-4 py-2 text-sm">
                    <i class="fas fa-calendar-week"></i> جدول الأسبوع
                </a>
                <a href="{{ route('employee.sales-manager.shift-swaps.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-amber-300 bg-amber-50 text-amber-900 font-semibold px-4 py-2 text-sm">
                    تبديلات
                    @if($pendingShiftSwaps > 0)
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

    {{-- 2. Live Activity + Tasks --}}
    <div id="hub-live" class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <section class="panel-card xl:col-span-2">
            <div class="panel-card-head flex items-center justify-between">
                <h3 class="font-bold text-slate-900">مراقبة مباشرة — نشاط الفريق</h3>
                <a href="{{ route('employee.sales-manager.presence.index') }}" class="text-xs font-semibold text-emerald-700">التواجد</a>
            </div>
            <div class="divide-y divide-slate-100 max-h-[520px] overflow-y-auto">
                @forelse($live as $row)
                    <div class="p-4">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="dot {{ $statusDot[$row['display_status']] ?? 'bg-slate-400' }}"></span>
                                <a href="{{ route('employee.sales-manager.team.show', $row['user_id']) }}" class="font-bold text-slate-900 truncate hover:underline">{{ $row['name'] }}</a>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border border-slate-200 text-slate-600">{{ $row['display_label'] }}</span>
                            </div>
                            @if($row['shift_channels'] ?? null)
                                <span class="text-[10px] text-violet-700 font-semibold truncate">{{ $row['shift_channels'] }}</span>
                            @endif
                        </div>
                        @if(count($row['events'] ?? []) > 0)
                            <ul class="space-y-1 pr-4 border-r-2 border-slate-100">
                                @foreach($row['events'] as $ev)
                                    <li class="text-xs text-slate-600">
                                        <span class="tabular-nums font-semibold text-slate-800">{{ $ev['time'] }}</span>
                                        {{ $ev['type_label'] }}
                                        @if($ev['lead_name'] ?? null)
                                            · {{ $ev['lead_name'] }}
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-xs text-slate-400">لا نشاط CRM مسجّل اليوم بعد.</p>
                        @endif
                    </div>
                @empty
                    <p class="p-6 text-center text-slate-500 text-sm">لا أعضاء في الفريق.</p>
                @endforelse
            </div>
        </section>

        <div class="space-y-4">
            <section class="panel-card">
                <div class="panel-card-head flex justify-between items-center">
                    <h3 class="font-bold text-slate-900">المهام اليوم</h3>
                    <a href="{{ route('employee.sales-manager.follow-ups.index') }}" class="text-xs font-semibold text-teal-700">المتابعات</a>
                </div>
                <div class="p-4 grid grid-cols-2 gap-3">
                    <div class="rounded-lg bg-slate-50 p-3"><p class="text-[11px] text-slate-500">متابعات اليوم</p><p class="text-xl font-black">{{ $tasks['followups_today'] ?? 0 }}</p></div>
                    <div class="rounded-lg bg-emerald-50 p-3"><p class="text-[11px] text-emerald-700">مكتمل</p><p class="text-xl font-black text-emerald-800">{{ $tasks['completed_today'] ?? 0 }}</p></div>
                    <div class="rounded-lg bg-amber-50 p-3"><p class="text-[11px] text-amber-700">قادم</p><p class="text-xl font-black text-amber-800">{{ $tasks['pending'] ?? 0 }}</p></div>
                    <div class="rounded-lg bg-rose-50 p-3"><p class="text-[11px] text-rose-700">متأخر</p><p class="text-xl font-black text-rose-800">{{ $tasks['overdue'] ?? 0 }}</p></div>
                </div>
            </section>

            <section class="panel-card">
                <div class="panel-card-head"><h3 class="font-bold text-slate-900">لوحة الصدارة</h3></div>
                <div class="p-4 space-y-3">
                    @if($leaderboard['day'] ?? null)
                        <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-3">
                            <p class="text-[11px] font-bold text-amber-800">موظف اليوم</p>
                            <p class="text-lg font-black text-slate-900"><i class="fas fa-trophy text-amber-500 ml-1"></i>{{ $leaderboard['day']['name'] }}</p>
                            <p class="text-xs text-slate-600">تارجت {{ $leaderboard['day']['target_pct'] }}% · {{ $leaderboard['day']['calls'] }} مكالمة · {{ $leaderboard['day']['deals'] }} صفقة</p>
                        </div>
                    @endif
                    @if($leaderboard['month'] ?? null)
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-3">
                            <p class="text-[11px] font-bold text-emerald-800">أعلى إيراد الشهر</p>
                            <p class="text-lg font-black text-slate-900">{{ $leaderboard['month']['name'] }}</p>
                            <p class="text-xs text-slate-600">{{ $fmtMoney($leaderboard['month']['revenue']) }} · تحويل {{ $leaderboard['month']['conversion_pct'] ?? '—' }}%</p>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>

    {{-- 3. Team Ranking --}}
    <section id="hub-ranking" class="panel-card">
        <div class="panel-card-head flex flex-wrap items-center justify-between gap-2">
            <h3 class="font-bold text-slate-900">ترتيب أداء الفريق (اليوم)</h3>
            <a href="{{ route('employee.sales-manager.kpi.index') }}" class="text-xs font-semibold text-teal-700">KPI التفصيلي</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs">
                    <tr>
                        <th class="text-right px-3 py-2 font-bold">#</th>
                        <th class="text-right px-3 py-2 font-bold">الموظف</th>
                        <th class="text-right px-3 py-2 font-bold">مكالمات</th>
                        <th class="text-right px-3 py-2 font-bold">مؤهل</th>
                        <th class="text-right px-3 py-2 font-bold">اجتماعات</th>
                        <th class="text-right px-3 py-2 font-bold">صفقات</th>
                        <th class="text-right px-3 py-2 font-bold">إيراد الشهر</th>
                        <th class="text-right px-3 py-2 font-bold">التارجت</th>
                        <th class="text-right px-3 py-2 font-bold">التقييم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($ranking as $row)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-3 py-2.5 tabular-nums text-slate-500">{{ $row['rank'] }}</td>
                            <td class="px-3 py-2.5">
                                <a href="{{ route('employee.sales-manager.team.show', $row['user_id']) }}" class="font-bold text-slate-900 hover:underline">{{ $row['name'] }}</a>
                            </td>
                            <td class="px-3 py-2.5 tabular-nums">{{ $row['calls'] }}</td>
                            <td class="px-3 py-2.5 tabular-nums">{{ $row['qualified'] }}</td>
                            <td class="px-3 py-2.5 tabular-nums">{{ $row['meetings'] }}</td>
                            <td class="px-3 py-2.5 tabular-nums">{{ $row['deals'] }}</td>
                            <td class="px-3 py-2.5 tabular-nums">{{ $fmtMoney($row['revenue']) }}</td>
                            <td class="px-3 py-2.5 tabular-nums font-semibold {{ $row['target_pct'] >= 100 ? 'text-emerald-700' : ($row['target_pct'] >= 70 ? 'text-amber-700' : 'text-rose-700') }}">{{ $row['target_pct'] }}%</td>
                            <td class="px-3 py-2.5 text-amber-500 tracking-tight">{{ str_repeat('★', $row['stars']).str_repeat('☆', 5 - $row['stars']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    {{-- Pipeline + Analytics + Compare --}}
    <div id="hub-pipeline" class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <section class="panel-card">
            <div class="panel-card-head flex justify-between">
                <h3 class="font-bold text-slate-900">مسار العملاء (Pipeline)</h3>
                <a href="{{ route('employee.sales-manager.pipeline') }}" class="text-xs font-semibold text-teal-700">التفاصيل</a>
            </div>
            <div class="p-4 grid grid-cols-2 gap-2 text-sm">
                @foreach([
                    'new' => 'جديد',
                    'contacted' => 'تم التواصل',
                    'qualified' => 'مؤهل',
                    'meeting' => 'اجتماع',
                    'proposal' => 'عرض سعر',
                    'negotiation' => 'تفاوض / دفع',
                    'won' => 'فوز',
                    'lost' => 'خسارة',
                ] as $key => $label)
                    <div class="rounded-lg border border-slate-200 px-3 py-2 flex justify-between">
                        <span class="text-slate-500 text-xs">{{ $label }}</span>
                        <b class="tabular-nums">{{ $pipeline[$key] ?? 0 }}</b>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="panel-card">
            <div class="panel-card-head"><h3 class="font-bold text-slate-900">المكالمات هذا الأسبوع</h3></div>
            <div class="p-4">
                @php
                    $callSeries = array_values($analytics['calls'] ?? []);
                    $maxCalls = max(1, ...($callSeries !== [] ? $callSeries : [1]));
                @endphp
                <div class="spark">
                    @foreach(($analytics['calls'] ?? []) as $c)
                        <span style="height: {{ max(8, ($c / $maxCalls) * 100) }}%" title="{{ $c }}"></span>
                    @endforeach
                </div>
                <div class="flex justify-between mt-2 text-[10px] text-slate-500">
                    @foreach(($analytics['labels'] ?? []) as $lb)
                        <span>{{ $lb }}</span>
                    @endforeach
                </div>
                <p class="text-xs text-slate-600 mt-3">
                    اجتماعات الأسبوع: <b>{{ array_sum($analytics['meetings'] ?? []) }}</b>
                    · إيراد الأسبوع: <b>{{ $fmtMoney(array_sum($analytics['revenue'] ?? [])) }}</b>
                </p>
            </div>
        </section>

        <section id="hub-compare" class="panel-card">
            <div class="panel-card-head"><h3 class="font-bold text-slate-900">مقارنة موظفين</h3></div>
            <div class="p-4">
                <form method="get" class="space-y-2 mb-3">
                    <input type="hidden" name="date" value="{{ $date->toDateString() }}">
                    <select name="compare_a" class="w-full rounded-lg border border-slate-300 px-2 py-2 text-sm" required>
                        <option value="">الموظف أ</option>
                        @foreach($members as $m)
                            <option value="{{ $m->id }}" @selected($compareA == $m->id)>{{ $m->name }}</option>
                        @endforeach
                    </select>
                    <select name="compare_b" class="w-full rounded-lg border border-slate-300 px-2 py-2 text-sm" required>
                        <option value="">الموظف ب</option>
                        @foreach($members as $m)
                            <option value="{{ $m->id }}" @selected($compareB == $m->id)>{{ $m->name }}</option>
                        @endforeach
                    </select>
                    <button class="w-full rounded-lg bg-slate-800 text-white text-sm font-semibold py-2">قارن</button>
                </form>
                @if($compare)
                    <p class="text-xs font-bold text-slate-700 mb-2">{{ $compare['a']['name'] }} vs {{ $compare['b']['name'] }}</p>
                    <ul class="space-y-1.5 text-xs">
                        @foreach($compare['metrics'] as $metric)
                            <li class="flex justify-between gap-2 border-b border-slate-100 pb-1">
                                <span class="text-slate-500">{{ $metric['label'] }}</span>
                                <span class="tabular-nums font-semibold">
                                    {{ is_numeric($metric['a']) ? (str_contains($metric['key'], 'revenue') ? $fmtMoney($metric['a']) : $metric['a']) : ($metric['a'] ?? '—') }}
                                    <span class="text-slate-400">vs</span>
                                    {{ is_numeric($metric['b']) ? (str_contains($metric['key'], 'revenue') ? $fmtMoney($metric['b']) : $metric['b']) : ($metric['b'] ?? '—') }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-xs text-slate-500">اختر موظفين لعرض المقارنة اليومية.</p>
                @endif
            </div>
        </section>
    </div>

    {{-- Timeline + Member cards --}}
    <div id="hub-timeline" class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <section class="panel-card">
            <div class="panel-card-head"><h3 class="font-bold text-slate-900">الجدول الزمني اليومي — الفريق</h3></div>
            <ul class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                @forelse($timeline as $ev)
                    <li class="px-4 py-2.5 text-sm flex gap-3">
                        <span class="tabular-nums text-xs font-bold text-slate-500 w-12 shrink-0">{{ $ev['time'] }}</span>
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-900 truncate">{{ $ev['user_name'] }} · {{ $ev['type_label'] }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $ev['lead_name'] ?? $ev['title'] }}</p>
                        </div>
                    </li>
                @empty
                    <li class="px-4 py-8 text-center text-slate-500 text-sm">لا أحداث اليوم بعد.</li>
                @endforelse
            </ul>
        </section>

        <section class="panel-card">
            <div class="panel-card-head flex justify-between">
                <h3 class="font-bold text-slate-900">ملفات الفريق</h3>
                <a href="{{ route('employee.sales-manager.ops-board') }}" class="text-xs font-semibold text-emerald-700">لوحة المتابعة</a>
            </div>
            <ul class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                @foreach($ranking as $row)
                    <li class="px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <p class="font-bold text-slate-900">{{ $row['name'] }}</p>
                            <p class="text-[11px] text-slate-500">
                                {{ $row['calls'] }} مكالمة · {{ $row['qualified'] }} مؤهل · {{ $row['meetings'] }} اجتماع · تارجت {{ $row['target_pct'] }}%
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <a href="{{ route('employee.sales-manager.team.show', $row['user_id']) }}" class="px-2.5 py-1 rounded-lg bg-slate-800 text-white text-[11px] font-bold">الملف</a>
                            <a href="{{ route('employee.sales-manager.scorecard.show', $row['user_id']) }}" class="px-2.5 py-1 rounded-lg border border-teal-200 text-teal-800 text-[11px] font-bold">رقابة</a>
                            <a href="{{ route('employee.sales-manager.shifts.show', $row['user_id']) }}" class="px-2.5 py-1 rounded-lg border border-violet-200 text-violet-800 text-[11px] font-bold">الشيفت</a>
                            <a href="{{ route('employee.sales-manager.team.report', $row['user_id']) }}" class="px-2.5 py-1 rounded-lg border border-emerald-200 text-emerald-800 text-[11px] font-bold">تقرير</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

    {{-- Shortcuts --}}
    <section class="panel-card">
        <div class="panel-card-head"><h3 class="font-bold text-slate-900">اختصارات وصلاحيات</h3></div>
        <div class="p-4 grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-2">
            <a href="{{ route('employee.sales-manager.distribution.index') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold hover:bg-slate-50"><i class="fas fa-share-nodes text-sky-600 ml-1"></i> توزيع Leads</a>
            <a href="{{ route('employee.sales-manager.transfer.index') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold hover:bg-slate-50"><i class="fas fa-right-left text-indigo-600 ml-1"></i> نقل Leads</a>
            <a href="{{ route('employee.sales-manager.pipeline') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold hover:bg-slate-50"><i class="fas fa-diagram-project text-violet-600 ml-1"></i> Pipeline</a>
            <a href="{{ route('employee.sales-manager.scorecard.index') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold hover:bg-slate-50"><i class="fas fa-clipboard-check text-teal-600 ml-1"></i> مراجعة يومية</a>
            <a href="{{ route('employee.sales-manager.commissions.index') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold hover:bg-slate-50"><i class="fas fa-coins text-amber-600 ml-1"></i> كوميشن</a>
            <a href="{{ route('employee.sales-manager.daily-reports.index') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold hover:bg-slate-50"><i class="fas fa-file-alt text-slate-600 ml-1"></i> تقارير يومية</a>
            <a href="{{ route('employee.sales-manager.whatsapp.inbox.index') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold hover:bg-slate-50"><i class="fab fa-whatsapp text-emerald-600 ml-1"></i> واتساب الفريق</a>
            <a href="{{ route('employee.sales-manager.kpi.index') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold hover:bg-slate-50"><i class="fas fa-chart-pie text-rose-600 ml-1"></i> تقارير KPI</a>
        </div>
    </section>
</div>
@endsection
