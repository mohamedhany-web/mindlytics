@extends('layouts.employee')

@section('title', 'مركز المبيعات')
@section('header', 'مركز المبيعات')

@push('styles')
@include('employee.sales._styles')
<style>
    /* تخفيف ألوان لوحة مركز المبيعات — بدون إفراط */
    .sales-hub .dashboard-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        padding: 1rem 1.25rem;
    }
    .sales-hub .dashboard-card::before { display: none; }
    .sales-hub .dashboard-card:hover {
        transform: none;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
        border-color: #cbd5e1;
    }
    .sales-hub .panel-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .sales-hub .panel-card-head {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .sales-hub .panel-card-accent-warn { border-right: 3px solid #f59e0b; }
    .sales-hub .panel-card-accent-alert { border-right: 3px solid #e11d48; }
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    $dailyReportService = app(\App\Services\SalesDailyReportService::class);
    $todayDailyReport = $dailyReportService->todayReportFor($user);
    $dailySettings = \App\Support\SalesDailyReportSettings::all();
    $heroActions = '<a href="'.route('employee.sales.leads.create').'" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold transition-colors"><i class="fas fa-plus"></i> عميل محتمل جديد</a>';
@endphp

<div class="space-y-6 sales-hub">
    @if(($dailySettings['enabled'] ?? true) && $dailyReportService->isWorkDay(today(), $user) && !($todayDailyReport?->isSubmitted()))
        <div class="rounded-xl border border-amber-200 bg-amber-50/80 p-4 text-amber-950">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <p class="font-bold"><i class="fas fa-exclamation-circle ml-2"></i> التقرير اليومي الإلزامي لم يُسلَّم بعد</p>
                    <p class="text-sm mt-1 text-amber-900/80">يؤثر على KPI — عدم التسليم قبل {{ $dailySettings['deadline_time'] ?? '23:59' }} قد يُنشئ خصماً تلقائياً.</p>
                </div>
                <a href="{{ route('employee.sales.daily-reports.edit') }}" class="shrink-0 inline-flex items-center justify-center px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-semibold text-sm">تعبئة التقرير الآن</a>
            </div>
        </div>
    @endif

    <div class="dashboard-card flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 shrink-0">
                <i class="fas fa-handshake text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-slate-900">مرحباً، {{ $user->name }}</h2>
                <p class="text-slate-600 text-sm mt-1">مركز المبيعات — مؤشرات سريعة، قمع المراحل، وقوائم تحتاج حركة اليوم</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 shrink-0">{!! $heroActions !!}</div>
    </div>

    @php $kq = $kpiQuick ?? null; @endphp
    @if($kq)
    <div class="dashboard-card border-slate-200 bg-slate-50/50">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">المؤشر المركّب (الشهر الحالي)</p>
                <p class="text-4xl font-black tabular-nums text-slate-900">{{ $kq['composite_month'] }}<span class="text-lg font-bold text-slate-500">/100</span></p>
                <p class="text-xs text-slate-500 mt-2 max-w-xl">40٪ نتائج · 30٪ نشاط · 20٪ جودة · 10٪ التزام CRM</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach(($kq['month']['pillars'] ?? []) as $pk => $pv)
                    <span class="inline-flex flex-col rounded-lg bg-white border border-slate-200 px-3 py-2 text-center min-w-[100px]">
                        <span class="text-[10px] text-slate-500">{{ $pk }}</span>
                        <span class="text-lg font-bold text-slate-800">{{ $pv['score'] ?? '—' }}</span>
                    </span>
                @endforeach
                <a href="{{ route('employee.sales.kpi.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-800 hover:bg-slate-900 text-white font-semibold px-5 py-3 text-sm">
                    <i class="fas fa-bullseye"></i> لوحة KPIs
                </a>
            </div>
        </div>
        @if(!empty($kq['alert_flags']))
            <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50/70 px-4 py-3 text-sm text-amber-950 space-y-1">
                @foreach($kq['alert_flags'] as $f)<p><i class="fas fa-exclamation-triangle ml-1 text-amber-600"></i>{{ $f }}</p>@endforeach
            </div>
        @endif
    </div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
        @php
            $statCards = [
                ['label' => 'الإجمالي', 'value' => $stats['total'], 'icon' => 'fa-users'],
                ['label' => 'نشط (قيد المعالجة)', 'value' => $stats['active'], 'icon' => 'fa-fire'],
                ['label' => 'متابعات متأخرة', 'value' => $stats['followups_overdue'], 'icon' => 'fa-bell', 'emphasis' => 'warn'],
                ['label' => 'متابعات اليوم', 'value' => $stats['followups_today'], 'icon' => 'fa-calendar-day'],
                ['label' => 'بلا تواصل '.(\App\Models\SalesLead::STALE_CONTACT_DAYS).'+ يوم', 'value' => $stats['stale'], 'icon' => 'fa-hourglass-end', 'emphasis' => 'muted-warn'],
                ['label' => 'أولوية عاجلة', 'value' => $stats['urgent_open'], 'icon' => 'fa-bolt', 'emphasis' => 'warn'],
            ];
        @endphp
        @foreach($statCards as $card)
            @php
                $valueClass = match ($card['emphasis'] ?? null) {
                    'warn' => 'text-rose-700',
                    'muted-warn' => 'text-amber-800',
                    default => 'text-slate-800',
                };
            @endphp
            <div class="dashboard-card">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-500 mb-1 leading-snug">{{ $card['label'] }}</p>
                        <p class="text-2xl sm:text-3xl font-bold {{ $valueClass }} tabular-nums">{{ $card['value'] }}</p>
                    </div>
                    <div class="w-10 h-10 sm:w-11 sm:h-11 bg-slate-100 rounded-lg flex items-center justify-center text-slate-500 shrink-0">
                        <i class="fas {{ $card['icon'] }} text-sm"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="dashboard-card md:col-span-2">
            <p class="text-sm font-medium text-slate-500 mb-1">قيمة الأنابيب (مفتوحة)</p>
            <p class="text-3xl font-bold text-slate-900 tabular-nums">{{ number_format($stats['pipeline_value'], 0) }} <span class="text-lg font-semibold text-slate-500">ج.م</span></p>
            <p class="text-xs text-slate-500 mt-2">مجموع «قيمة متوقعة» للعملاء غير المكتمل/الخسارة.</p>
        </div>
        <div class="dashboard-card">
            <p class="text-sm font-medium text-slate-500 mb-1">فوز — هذا الشهر</p>
            <p class="text-2xl font-bold text-slate-900 tabular-nums">{{ number_format($stats['won_month_value'], 0) }} ج.م</p>
        </div>
        <div class="dashboard-card">
            <div class="flex justify-around items-center gap-3 text-center">
                <div><p class="text-xs text-slate-500">مكتمل</p><p class="text-xl font-bold text-slate-800">{{ $stats['won'] }}</p></div>
                <div><p class="text-xs text-slate-500">خسارة</p><p class="text-xl font-bold text-slate-600">{{ $stats['lost'] }}</p></div>
                <div><p class="text-xs text-slate-500">جديد</p><p class="text-xl font-bold text-slate-800">{{ $stats['new'] }}</p></div>
            </div>
        </div>
    </div>

    <div class="dashboard-card">
        <h2 class="text-xl font-bold text-slate-900 mb-1">قمع المراحل</h2>
        <p class="text-sm text-slate-500 mb-4">عدد العملاء في كل مرحلة — يساعد على معرفة أين يتراكم العمل.</p>
        @php $maxF = max($funnel) ?: 1; @endphp
        <div class="space-y-3">
            @foreach(\App\Models\SalesLead::STAGES as $key => $label)
                @php $c = $funnel[$key] ?? 0; $pct = round(($c / $maxF) * 100); @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-slate-800">{{ $label }}</span>
                        <span class="text-slate-500 tabular-nums">{{ $c }}</span>
                    </div>
                    <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full bg-slate-500" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="panel-card">
            <div class="panel-card-head flex justify-between items-center">
                <h2 class="font-bold text-slate-900">Task Queue اليومية</h2>
                <span class="text-xs text-slate-500 font-medium">Next Best Action</span>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse(($taskQueue ?? collect()) as $item)
                    @php $lead = $item['lead']; @endphp
                    <li class="px-5 py-3 hover:bg-slate-50">
                        <a href="{{ route('employee.sales.leads.show', $lead) }}" class="block">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold text-slate-900">{{ $lead->name }}</span>
                                <span class="text-[11px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium">{{ $item['reason'] }}</span>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">{{ $item['next_action'] }}</p>
                        </a>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-slate-500 text-sm">لا توجد عناصر حرجة في القائمة الآن.</li>
                @endforelse
            </ul>
        </div>

        <div class="panel-card panel-card-accent-alert">
            <div class="panel-card-head flex justify-between items-center">
                <h2 class="font-bold text-slate-900">متابعات متأخرة</h2>
                <a href="{{ route('employee.sales.leads.index', ['follow_up' => 'overdue', 'sort' => 'follow_up']) }}" class="text-sm text-slate-600 font-medium hover:underline">عرض الكل</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse($overdueLeads as $l)
                <li class="px-5 py-3 hover:bg-slate-50">
                    <a href="{{ route('employee.sales.leads.show', $l) }}" class="flex justify-between gap-2">
                        <span class="font-medium text-slate-900">{{ $l->name }}</span>
                        <span class="text-xs text-rose-700 font-medium whitespace-nowrap">{{ $l->next_follow_up_at?->format('Y-m-d H:i') }}</span>
                    </a>
                </li>
                @empty
                <li class="px-5 py-8 text-center text-slate-500 text-sm">لا توجد متابعات متأخرة — ممتاز.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="panel-card">
            <div class="panel-card-head">
                <h2 class="font-bold text-slate-900">تنبيه SLA: أول رد متأخر ({{ $slaCutoffHours ?? 24 }} ساعة+)</h2>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse(($noFirstResponseLeads ?? collect()) as $l)
                <li class="px-5 py-3 hover:bg-slate-50">
                    <a href="{{ route('employee.sales.leads.show', $l) }}" class="flex justify-between gap-2">
                        <span class="font-medium text-slate-900">{{ $l->name }}</span>
                        <span class="text-xs text-slate-500 font-medium">{{ $l->created_at?->diffForHumans() }}</span>
                    </a>
                </li>
                @empty
                <li class="px-5 py-8 text-center text-slate-500 text-sm">ممتاز، لا يوجد تأخير في أول رد.</li>
                @endforelse
            </ul>
        </div>

        <div class="panel-card panel-card-accent-warn">
            <div class="panel-card-head flex justify-between items-center">
                <h2 class="font-bold text-slate-900">يحتاجون تواصلاً ({{ \App\Models\SalesLead::STALE_CONTACT_DAYS }}+ يوم)</h2>
                <a href="{{ route('employee.sales.leads.index', ['stale' => 1]) }}" class="text-sm text-slate-600 font-medium hover:underline">عرض الكل</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse($staleLeads as $l)
                <li class="px-5 py-3 hover:bg-slate-50">
                    <a href="{{ route('employee.sales.leads.show', $l) }}" class="flex justify-between gap-2">
                        <span class="font-medium text-slate-900">{{ $l->name }}</span>
                        <span class="text-xs text-slate-500">{{ $l->last_contacted_at?->diffForHumans() ?? 'لم يُسجَّل' }}</span>
                    </a>
                </li>
                @empty
                <li class="px-5 py-8 text-center text-slate-500 text-sm">لا يوجد عملاء راكدون.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="panel-card">
            <div class="panel-card-head flex justify-between items-center">
                <h2 class="font-bold text-slate-900">متابعات اليوم</h2>
                <a href="{{ route('employee.sales.leads.index', ['follow_up' => 'today']) }}" class="text-sm text-slate-600 font-medium hover:underline">القائمة</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse($followupsToday as $l)
                <li class="px-5 py-3 hover:bg-slate-50">
                    <a href="{{ route('employee.sales.leads.show', $l) }}" class="flex justify-between gap-2">
                        <span class="font-medium text-slate-900">{{ $l->name }}</span>
                        <span class="text-xs text-slate-500">{{ $l->next_follow_up_at?->format('H:i') }}</span>
                    </a>
                </li>
                @empty
                <li class="px-5 py-8 text-center text-slate-500 text-sm">لا توجد متابعات مجدولة اليوم</li>
                @endforelse
            </ul>
        </div>
        <div class="panel-card">
            <div class="panel-card-head flex justify-between items-center">
                <h2 class="font-bold text-slate-900">آخر التحديثات</h2>
                <a href="{{ route('employee.sales.leads.index') }}" class="text-sm text-slate-600 font-medium hover:underline">الكل</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse($recentLeads as $l)
                <li class="px-5 py-3 hover:bg-slate-50 flex justify-between items-center gap-2">
                    <a href="{{ route('employee.sales.leads.show', $l) }}" class="font-medium text-slate-900">{{ $l->name }}</a>
                    <span class="text-xs px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">{{ \App\Models\SalesLead::stageLabel($l->stage) }}</span>
                </li>
                @empty
                <li class="px-5 py-8 text-center text-slate-500 text-sm">ابدأ بإضافة عميل محتمل</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('employee.sales.leads.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">
            <i class="fas fa-users"></i> العملاء المحتملون
        </a>
        <a href="{{ route('employee.sales.daily-reports.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">
            <i class="fas fa-clipboard-check"></i> التقرير اليومي
        </a>
        <a href="{{ route('employee.sales.commissions.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">
            <i class="fas fa-coins"></i> العمولات
        </a>
    </div>
</div>
@endsection
