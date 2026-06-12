@extends('layouts.admin')

@section('title', 'لوحة الإدارة - Mindlytics')
@section('header', 'لوحة الإدارة')

@section('content')
@php
    $direction = $chartDashboard['direction'] ?? [];
    $monthly = $chartDashboard['monthly'] ?? [];
    $dirMetrics = $direction['metrics'] ?? [];
    $dirStatus = $direction['status'] ?? 'neutral';
    $dirBannerClass = match ($dirStatus) {
        'growth' => 'border-emerald-200 bg-emerald-50',
        'stable' => 'border-sky-200 bg-sky-50',
        'decline' => 'border-rose-200 bg-rose-50',
        default => 'border-slate-200 bg-slate-50',
    };
    $dirIconClass = match ($dirStatus) {
        'growth' => 'text-emerald-600',
        'stable' => 'text-sky-600',
        'decline' => 'text-rose-600',
        default => 'text-slate-600',
    };
    $walletAvailable = (float) ($stats['total_wallet_balance'] ?? 0);
    $walletPending = (float) ($stats['total_wallet_pending'] ?? 0);
    $walletTotal = $walletAvailable + $walletPending;
    $revenueArr = $monthly['revenue'] ?? [];
    $latestRevenue = $revenueArr !== [] ? (float) $revenueArr[array_key_last($revenueArr)] : 0;
    $enrollArr = $monthly['totalEnrollments'] ?? [];
    $latestEnrollments = $enrollArr !== [] ? (int) $enrollArr[array_key_last($enrollArr)] : 0;

    $statCards = [
        ['label' => 'الطلاب', 'value' => number_format($metrics['students']['total'] ?? 0), 'icon' => 'fas fa-user-graduate', 'theme' => 'emerald', 'desc' => '+' . number_format($metrics['students']['new_this_month'] ?? 0) . ' هذا الشهر', 'trend' => $metrics['students']['trend'] ?? null],
        ['label' => 'تسجيلات نشطة', 'value' => number_format($metrics['enrollments']['total'] ?? 0), 'icon' => 'fas fa-user-check', 'theme' => 'violet', 'desc' => '+' . number_format($metrics['enrollments']['new_this_month'] ?? 0) . ' هذا الشهر', 'trend' => $metrics['enrollments']['trend'] ?? null],
        ['label' => 'إيراد الشهر', 'value' => number_format($metrics['monthly_revenue']['current'] ?? 0, 0), 'icon' => 'fas fa-chart-line', 'theme' => 'sky', 'desc' => 'ج.م — مدفوعات مكتملة', 'trend' => $metrics['monthly_revenue']['trend'] ?? null],
        ['label' => 'إجمالي الإيراد', 'value' => number_format($stats['total_revenue'] ?? 0, 0), 'icon' => 'fas fa-money-bill-wave', 'theme' => 'green', 'desc' => 'ج.م — تراكمي', 'trend' => null],
        ['label' => 'الكورسات', 'value' => number_format($metrics['courses']['total'] ?? 0), 'icon' => 'fas fa-book', 'theme' => 'amber', 'desc' => number_format($stats['published_courses'] ?? 0) . ' منشور', 'trend' => $metrics['courses']['trend'] ?? null],
        ['label' => 'المدربون', 'value' => number_format($metrics['instructors']['total'] ?? 0), 'icon' => 'fas fa-chalkboard-teacher', 'theme' => 'indigo', 'desc' => '+' . number_format($metrics['instructors']['new_this_month'] ?? 0) . ' هذا الشهر', 'trend' => $metrics['instructors']['trend'] ?? null],
        ['label' => 'المستخدمون', 'value' => number_format($metrics['users']['total'] ?? 0), 'icon' => 'fas fa-users', 'theme' => 'blue', 'desc' => '+' . number_format($metrics['users']['new_this_month'] ?? 0) . ' هذا الشهر', 'trend' => $metrics['users']['trend'] ?? null],
        ['label' => 'فواتير معلّقة', 'value' => number_format($metrics['pending_invoices']['total'] ?? 0), 'icon' => 'fas fa-file-invoice', 'theme' => 'rose', 'desc' => number_format($walletTotal, 0) . ' ج.م في المحافظ', 'trend' => $metrics['pending_invoices']['trend'] ?? null],
    ];

    $cardThemes = [
        'emerald' => ['border' => 'border-emerald-200/70', 'bg' => 'from-white via-white to-emerald-50/60', 'label' => 'text-emerald-800/80', 'value' => 'from-emerald-700 to-teal-600', 'icon' => 'from-emerald-500 to-teal-600', 'desc' => 'text-emerald-700/70'],
        'violet'  => ['border' => 'border-violet-200/70', 'bg' => 'from-white via-white to-violet-50/60', 'label' => 'text-violet-800/80', 'value' => 'from-violet-700 to-purple-600', 'icon' => 'from-violet-500 to-purple-600', 'desc' => 'text-violet-700/70'],
        'sky'     => ['border' => 'border-sky-200/70', 'bg' => 'from-white via-white to-sky-50/60', 'label' => 'text-sky-800/80', 'value' => 'from-sky-700 to-blue-600', 'icon' => 'from-sky-500 to-blue-600', 'desc' => 'text-sky-700/70'],
        'green'   => ['border' => 'border-green-200/70', 'bg' => 'from-white via-white to-green-50/60', 'label' => 'text-green-800/80', 'value' => 'from-green-700 to-emerald-600', 'icon' => 'from-green-500 to-emerald-600', 'desc' => 'text-green-700/70'],
        'amber'   => ['border' => 'border-amber-200/70', 'bg' => 'from-white via-white to-amber-50/60', 'label' => 'text-amber-800/80', 'value' => 'from-amber-700 to-orange-600', 'icon' => 'from-amber-500 to-orange-500', 'desc' => 'text-amber-700/70'],
        'indigo'  => ['border' => 'border-indigo-200/70', 'bg' => 'from-white via-white to-indigo-50/60', 'label' => 'text-indigo-800/80', 'value' => 'from-indigo-700 to-violet-600', 'icon' => 'from-indigo-500 to-violet-600', 'desc' => 'text-indigo-700/70'],
        'blue'    => ['border' => 'border-blue-200/70', 'bg' => 'from-white via-white to-blue-50/60', 'label' => 'text-blue-800/80', 'value' => 'from-blue-700 to-sky-600', 'icon' => 'from-blue-500 to-sky-600', 'desc' => 'text-blue-700/70'],
        'rose'    => ['border' => 'border-rose-200/70', 'bg' => 'from-white via-white to-rose-50/60', 'label' => 'text-rose-800/80', 'value' => 'from-rose-700 to-red-600', 'icon' => 'from-rose-500 to-red-500', 'desc' => 'text-rose-700/70'],
    ];
@endphp

<div class="space-y-6">
    {{-- الهيدر --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-university"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">لوحة الأكاديمية</h2>
                    <p class="text-xs text-slate-600">نظرة شاملة على الإيراد، التسجيلات، الطلاب، والاتجاهات.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.accounting.insights') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-chart-bar text-sky-600"></i>
                    مؤشرات المحاسبة
                </a>
                <a href="{{ route('admin.sales.insights.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-chart-pie text-emerald-600"></i>
                    Insights المبيعات
                </a>
                <a href="{{ route('admin.wallets.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-wallet text-amber-600"></i>
                    المحافظ
                </a>
            </div>
        </div>
    </section>

    {{-- بطاقات الإحصائيات --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach($statCards as $card)
            @php
                $theme = $cardThemes[$card['theme'] ?? 'blue'] ?? $cardThemes['blue'];
                $trend = $card['trend'] ?? null;
                $pct = $trend['percent'] ?? null;
                $trendUp = $pct !== null && $pct >= 0;
            @endphp
            <div class="dashboard-stat-card rounded-2xl border-2 {{ $theme['border'] }} bg-gradient-to-br {{ $theme['bg'] }} p-5 shadow-lg">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold {{ $theme['label'] }} mb-1">{{ $card['label'] }}</p>
                        <p class="text-3xl font-black bg-gradient-to-r {{ $theme['value'] }} bg-clip-text text-transparent tabular-nums">{{ $card['value'] }}</p>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br {{ $theme['icon'] }} flex items-center justify-center text-white shadow-md flex-shrink-0">
                        <i class="{{ $card['icon'] }} text-lg"></i>
                    </div>
                </div>
                <p class="text-xs font-medium {{ $theme['desc'] }} truncate">{{ $card['desc'] }}</p>
                @if($pct !== null)
                    <p class="text-xs font-bold mt-2 {{ $trendUp ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $trendUp ? '↑' : '↓' }} {{ number_format(abs((float) $pct), 1) }}% عن الشهر السابق
                    </p>
                @endif
            </div>
        @endforeach
    </div>

    {{-- اتجاه الأكاديمية --}}
    <section class="rounded-2xl border shadow-lg overflow-hidden {{ $dirBannerClass }}">
        <div class="px-4 py-4 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center {{ $dirIconClass }}">
                    <i class="fas fa-compass text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-600">اتجاه الأكاديمية</p>
                    <p class="text-lg font-black text-slate-900">{{ $direction['label'] ?? '—' }}</p>
                    <p class="text-sm text-slate-700 mt-1 max-w-3xl">{{ $direction['summary'] ?? '' }}</p>
                    <p class="text-[11px] text-slate-500 mt-2">
                        {{ $direction['previous_month_label'] ?? '—' }} ← {{ $direction['current_month_label'] ?? '—' }}
                    </p>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 min-w-0">
                @foreach($dirMetrics as $key => $m)
                    @php
                        $pct = $m['pct'] ?? null;
                        $isMoney = $key === 'revenue';
                        $trendUp = $pct !== null && $pct > 0;
                        $trendClass = $trendUp ? 'text-emerald-700' : ($pct < 0 ? 'text-rose-700' : 'text-slate-600');
                    @endphp
                    <div class="rounded-xl bg-white/80 border border-white p-3 shadow-sm">
                        <p class="text-[10px] font-semibold text-slate-500 truncate">{{ $m['label'] ?? '' }}</p>
                        <p class="text-lg font-black text-slate-900 tabular-nums">
                            {{ $isMoney ? number_format((float) ($m['current'] ?? 0), 0) : number_format((int) ($m['current'] ?? 0)) }}
                        </p>
                        @if($pct !== null)
                            <p class="text-[10px] font-bold {{ $trendClass }}">{{ $trendUp ? '↑' : ($pct < 0 ? '↓' : '→') }} {{ number_format(abs((float) $pct), 1) }}%</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- الشارتات الرئيسية --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden xl:col-span-2">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">الإيراد والتسجيلات — آخر 6 أشهر</h3>
                <p class="text-xs text-slate-600">إيراد المدفوعات + تسجيلات أونلاين/أوفلاين.</p>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 360px;">
                    <canvas id="chartAcademyMain"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">الإيراد الشهري</h3>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 300px;">
                    <canvas id="chartRevenue"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">نمو الطلاب والطلبات</h3>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 300px;">
                    <canvas id="chartStudentsOrders"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">الإيراد اليومي — 14 يوم</h3>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 280px;">
                    <canvas id="chartDailyRevenue"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">توزيع التسجيلات</h3>
                <p class="text-xs text-slate-600">أونلاين vs أوفلاين (نشطة).</p>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 280px;">
                    <canvas id="chartEnrollmentMix"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">توزيع المستخدمين</h3>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 280px;">
                    <canvas id="chartUserRoles"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">نشاط النظام — 7 أيام</h3>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 280px;">
                    <canvas id="chartWeeklyActivity"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden xl:col-span-2">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">أكثر الكورسات تسجيلاً</h3>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 300px;">
                    <canvas id="chartTopCourses"></canvas>
                </div>
            </div>
        </section>
    </div>

    {{-- إجراءات سريعة --}}
    @if(!empty($quickActions))
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-bolt text-amber-500"></i>
                مهام تحتاج انتباه
            </h3>
        </div>
        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
            @foreach($quickActions as $action)
                <a href="{{ $action['route'] }}" class="rounded-xl border border-slate-200 bg-white p-4 hover:bg-slate-50 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                            <i class="{{ $action['icon'] }} text-sm"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-800 truncate">{{ $action['title'] }}</p>
                    </div>
                    <p class="text-2xl font-black text-slate-900 tabular-nums">{{ number_format($action['count'] ?? 0) }}</p>
                    @if(!empty($action['meta']))
                        <p class="text-[10px] text-slate-500 mt-1 truncate">{{ $action['meta'] }}</p>
                    @endif
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- الفروع --}}
    @if(isset($branchesOperationalOverview) && $branchesOperationalOverview->isNotEmpty())
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-layer-group text-indigo-600"></i>
                نظرة على الفروع
            </h3>
            <p class="text-xs text-slate-600 mt-1">بيانات منفصلة لكل فرع عبر branch_id.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                        <th class="px-4 py-3 text-right font-semibold">الفرع</th>
                        <th class="px-4 py-3 text-center font-semibold">مستخدمون</th>
                        <th class="px-4 py-3 text-center font-semibold">كورسات</th>
                        <th class="px-4 py-3 text-center font-semibold">أوفلاين</th>
                        <th class="px-4 py-3 text-center font-semibold">طلبات</th>
                        <th class="px-4 py-3 text-center font-semibold">تسجيلات</th>
                        <th class="px-4 py-3 text-center font-semibold">حالة</th>
                        <th class="px-4 py-3 text-center font-semibold">عرض</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($branchesOperationalOverview as $bRow)
                        @php $isCentral = isset($centralAcademyBranchId) && (int) $bRow->id === (int) $centralAcademyBranchId; @endphp
                        <tr class="hover:bg-slate-50 {{ $isCentral ? 'bg-indigo-50/40' : '' }}">
                            <td class="px-4 py-3 font-semibold">
                                {{ $bRow->name }}
                                @if($isCentral)
                                    <span class="mr-1 text-[10px] font-bold px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-800">أساسي</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center tabular-nums">{{ number_format($bRow->users_count) }}</td>
                            <td class="px-4 py-3 text-center tabular-nums">{{ number_format($bRow->advanced_courses_count) }}</td>
                            <td class="px-4 py-3 text-center tabular-nums">{{ number_format($bRow->offline_courses_count) }}</td>
                            <td class="px-4 py-3 text-center tabular-nums">{{ number_format($bRow->orders_count) }}</td>
                            <td class="px-4 py-3 text-center tabular-nums">{{ number_format($bRow->student_course_enrollments_count) }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($bRow->is_active)
                                    <span class="text-xs font-semibold text-emerald-700">نشط</span>
                                @else
                                    <span class="text-xs text-slate-500">موقوف</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.branches.show', $bRow) }}" class="text-xs font-bold text-sky-600 hover:text-sky-800">تفاصيل</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    {{-- آخر النشاطات --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="text-base font-black text-slate-900">آخر النشاطات</h3>
                <a href="{{ route('admin.activity-log') }}" class="text-xs font-semibold text-sky-600">عرض الكل</a>
            </div>
            <div class="p-4">
                @if(isset($stats['recent_activities']) && $stats['recent_activities']->count() > 0)
                    <ul class="space-y-2">
                        @foreach($stats['recent_activities']->take(6) as $activity)
                            <li class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 text-sm">
                                <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center text-sky-600 flex-shrink-0">
                                    <i class="fas fa-history text-xs"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-slate-900 truncate">{{ $activity->user->name ?? '—' }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ $activity->action }} — {{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-500 text-center py-6">لا توجد أنشطة.</p>
                @endif
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">آخر محاولات الامتحانات</h3>
            </div>
            <div class="p-4">
                @if(isset($stats['recent_exam_attempts']) && $stats['recent_exam_attempts']->count() > 0)
                    <ul class="space-y-2">
                        @foreach($stats['recent_exam_attempts']->take(6) as $attempt)
                            <li class="flex items-center justify-between gap-3 p-2 rounded-lg hover:bg-slate-50 text-sm">
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-900 truncate">{{ $attempt->student->name ?? '—' }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ $attempt->exam->title ?? '—' }}</p>
                                </div>
                                <span class="inline-flex rounded-md px-2 py-0.5 text-xs font-bold {{ $attempt->score >= 80 ? 'bg-emerald-100 text-emerald-700' : ($attempt->score >= 60 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                    {{ $attempt->score }}%
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-500 text-center py-6">لا توجد محاولات.</p>
                @endif
            </div>
        </section>
    </div>

    {{-- آخر المستخدمين / الكورسات / مدفوعات --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @if(isset($recent_users))
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="text-sm font-black text-slate-900">آخر المستخدمين</h3>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-sky-600">الكل</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @foreach($recent_users as $user)
                    <li class="px-4 py-3 hover:bg-slate-50">
                        <p class="text-sm font-semibold text-slate-900 truncate">{{ $user->name }}</p>
                        <p class="text-xs text-slate-500">{{ $user->created_at->diffForHumans() }}</p>
                    </li>
                @endforeach
            </ul>
        </section>
        @endif

        @if(isset($recent_courses))
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="text-sm font-black text-slate-900">آخر الكورسات</h3>
                <a href="{{ route('admin.advanced-courses.index') }}" class="text-xs font-semibold text-sky-600">الكل</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse($recent_courses as $course)
                    <li class="px-4 py-3 hover:bg-slate-50">
                        <p class="text-sm font-semibold text-slate-900 truncate">{{ $course->title }}</p>
                        <p class="text-xs text-slate-500">{{ $course->created_at->diffForHumans() }}</p>
                    </li>
                @empty
                    <li class="px-4 py-8 text-center text-sm text-slate-500">لا كورسات.</li>
                @endforelse
            </ul>
        </section>
        @endif

        @if(isset($recent_payments) && $recent_payments->count() > 0)
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-sm font-black text-slate-900">آخر المدفوعات</h3>
            </div>
            <ul class="divide-y divide-slate-100">
                @foreach($recent_payments->take(5) as $payment)
                    <li class="px-4 py-3 hover:bg-slate-50 flex justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900 truncate">{{ $payment->user->name ?? '—' }}</p>
                            <p class="text-xs text-slate-500">{{ $payment->paid_at?->diffForHumans() ?? $payment->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="text-sm font-bold text-emerald-700 tabular-nums">{{ number_format($payment->amount ?? 0, 0) }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    const dash = @json($chartDashboard);
    const weekly = @json($weeklyActivityChart ?? ['labels' => [], 'values' => []]);
    const monthly = dash.monthly || {};
    const daily = dash.daily_revenue || {};
    const mix = dash.enrollment_mix || {};
    const roles = dash.user_roles || {};
    const topCourses = dash.top_courses || {};

    const palette = {
        emerald: 'rgb(16, 185, 129)', emeraldSoft: 'rgba(16, 185, 129, 0.15)',
        sky: 'rgb(14, 165, 233)', skySoft: 'rgba(14, 165, 233, 0.15)',
        indigo: 'rgb(99, 102, 241)', indigoSoft: 'rgba(99, 102, 241, 0.15)',
        amber: 'rgb(245, 158, 11)', violet: 'rgb(139, 92, 246)',
        rose: 'rgb(244, 63, 94)', slate: 'rgb(100, 116, 139)',
    };
    const doughnutColors = [palette.emerald, palette.sky, palette.indigo, palette.amber, palette.violet, palette.rose, palette.slate];
    const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 300 },
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } },
    };

    function hasData(arr) {
        return Array.isArray(arr) && arr.some(v => Number(v) > 0);
    }

    function emptyMsg(id, msg) {
        const c = document.getElementById(id);
        if (c) c.parentElement.innerHTML = '<p class="text-sm text-slate-500 text-center py-16">' + msg + '</p>';
    }

    if (hasData(monthly.revenue) || hasData(monthly.totalEnrollments)) {
        new Chart(document.getElementById('chartAcademyMain'), {
            type: 'line',
            data: {
                labels: monthly.labels || [],
                datasets: [
                    { label: 'الإيراد (ج.م)', data: monthly.revenue || [], borderColor: palette.emerald, backgroundColor: palette.emeraldSoft, tension: 0.35, fill: true, yAxisID: 'y1' },
                    { label: 'تسجيلات أونلاين', data: monthly.onlineEnrollments || [], borderColor: palette.sky, tension: 0.35, yAxisID: 'y' },
                    { label: 'تسجيلات أوفلاين', data: monthly.offlineEnrollments || [], borderColor: palette.indigo, tension: 0.35, yAxisID: 'y' },
                    { label: 'إجمالي التسجيلات', data: monthly.totalEnrollments || [], borderColor: palette.amber, borderDash: [5, 4], tension: 0.35, yAxisID: 'y' },
                ],
            },
            options: {
                ...baseOptions,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { beginAtZero: true, position: 'right', title: { display: true, text: 'تسجيلات' } },
                    y1: { beginAtZero: true, position: 'left', grid: { drawOnChartArea: false }, title: { display: true, text: 'ج.م' } },
                },
            },
        });
    } else emptyMsg('chartAcademyMain', 'لا بيانات كافية لعرض الاتجاه.');

    if (hasData(monthly.revenue)) {
        new Chart(document.getElementById('chartRevenue'), {
            type: 'bar',
            data: { labels: monthly.labels || [], datasets: [{ label: 'الإيراد', data: monthly.revenue || [], backgroundColor: 'rgba(16, 185, 129, 0.75)', borderRadius: 8 }] },
            options: { ...baseOptions, plugins: { ...baseOptions.plugins, legend: { display: false } }, scales: { y: { beginAtZero: true } } },
        });
    } else emptyMsg('chartRevenue', 'لا إيراد مسجّل.');

    if (hasData(monthly.newStudents) || hasData(monthly.orders)) {
        new Chart(document.getElementById('chartStudentsOrders'), {
            type: 'line',
            data: {
                labels: monthly.labels || [],
                datasets: [
                    { label: 'طلاب جدد', data: monthly.newStudents || [], borderColor: palette.emerald, tension: 0.35, fill: false },
                    { label: 'طلبات', data: monthly.orders || [], borderColor: palette.sky, tension: 0.35, fill: false },
                ],
            },
            options: { ...baseOptions, scales: { y: { beginAtZero: true } } },
        });
    } else emptyMsg('chartStudentsOrders', 'لا بيانات طلاب/طلبات.');

    if (hasData(daily.revenue)) {
        new Chart(document.getElementById('chartDailyRevenue'), {
            type: 'bar',
            data: { labels: daily.labels || [], datasets: [{ label: 'إيراد يومي', data: daily.revenue || [], backgroundColor: 'rgba(14, 165, 233, 0.7)', borderRadius: 6 }] },
            options: { ...baseOptions, plugins: { ...baseOptions.plugins, legend: { display: false } }, scales: { y: { beginAtZero: true } } },
        });
    } else emptyMsg('chartDailyRevenue', 'لا مدفوعات في آخر 14 يوم.');

    if (hasData(mix.values)) {
        new Chart(document.getElementById('chartEnrollmentMix'), {
            type: 'doughnut',
            data: { labels: mix.labels || [], datasets: [{ data: mix.values || [], backgroundColor: doughnutColors, borderWidth: 2 }] },
            options: baseOptions,
        });
    } else emptyMsg('chartEnrollmentMix', 'لا تسجيلات نشطة.');

    if (hasData(roles.values)) {
        new Chart(document.getElementById('chartUserRoles'), {
            type: 'doughnut',
            data: { labels: roles.labels || [], datasets: [{ data: roles.values || [], backgroundColor: doughnutColors, borderWidth: 2 }] },
            options: baseOptions,
        });
    } else emptyMsg('chartUserRoles', 'لا مستخدمين.');

    if (hasData(weekly.values)) {
        new Chart(document.getElementById('chartWeeklyActivity'), {
            type: 'line',
            data: { labels: weekly.labels || [], datasets: [{ label: 'أحداث', data: weekly.values || [], borderColor: palette.indigo, backgroundColor: palette.indigoSoft, tension: 0.35, fill: true }] },
            options: { ...baseOptions, plugins: { ...baseOptions.plugins, legend: { display: false } }, scales: { y: { beginAtZero: true } } },
        });
    } else emptyMsg('chartWeeklyActivity', 'لا نشاط في آخر 7 أيام.');

    if (hasData(topCourses.values)) {
        new Chart(document.getElementById('chartTopCourses'), {
            type: 'bar',
            data: { labels: topCourses.labels || [], datasets: [{ label: 'تسجيلات نشطة', data: topCourses.values || [], backgroundColor: 'rgba(99, 102, 241, 0.75)', borderRadius: 6 }] },
            options: { indexAxis: 'y', ...baseOptions, plugins: { ...baseOptions.plugins, legend: { display: false } }, scales: { x: { beginAtZero: true } } },
        });
    } else emptyMsg('chartTopCourses', 'لا تسجيلات على الكورسات.');
})();
</script>
@endpush
@endsection
