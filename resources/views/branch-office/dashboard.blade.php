@extends('layouts.admin')

@section('title', 'لوحة الفرع')
@section('header', 'لوحة الفرع — ' . $branch->name)

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-6 shadow-sm max-w-[1600px] mx-auto">
        <h2 class="text-lg font-bold text-slate-900 mb-1">مرحباً، {{ auth()->user()->name }}</h2>
        <p class="text-sm text-slate-600">عرض موازٍ للوحة الإدارة المركزية مع <strong>بيانات {{ $branch->name }} فقط</strong> (قراءة وإحصاءات). التعديل والإعدادات العامة تبقى في الأكاديمية الأساسية.</p>
        @if($branch->suggestedSubdomainUrl())
            <p class="text-xs text-slate-500 mt-2">النطاق الفرعي: <a class="text-emerald-700 underline break-all" dir="ltr" href="{{ $branch->suggestedSubdomainUrl() }}">{{ $branch->suggestedSubdomainUrl() }}</a></p>
        @endif
    </div>

    {{-- صف المقاييس الرئيسية (مثل لوحة الإدارة) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 max-w-[1600px] mx-auto">
        @php $usersMetric = $metrics['users'] ?? null; $usersTrend = $usersMetric['trend'] ?? null; @endphp
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 border-2 border-blue-200/50 shadow-lg bg-white">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-bold text-blue-800/80">إجمالي المستخدمين</p>
                <i class="fas fa-users text-blue-500 text-xl"></i>
            </div>
            <p class="text-4xl font-black text-blue-900 tabular-nums">{{ number_format($usersMetric['total'] ?? 0) }}</p>
            <p class="text-xs text-blue-700/70 mt-2">جدد هذا الشهر: <span class="font-bold">{{ number_format($usersMetric['new_this_month'] ?? 0) }}</span></p>
            @if($usersTrend)
                @php $diff = (int) round($usersTrend['difference']); $percent = $usersTrend['percent']; $positive = $diff >= 0; @endphp
                <p class="text-xs mt-2 {{ $positive ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">عن الشهر الماضي: {{ $positive ? '+' : '' }}{{ number_format($diff) }} ({{ $percent >= 0 ? '+' : '' }}{{ number_format($percent, 1) }}%)</p>
            @endif
        </div>

        @php $studentsMetric = $metrics['students'] ?? null; $studentsTrend = $studentsMetric['trend'] ?? null; @endphp
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 border-2 border-emerald-200/50 shadow-lg bg-white">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-bold text-emerald-800/80">الطلاب</p>
                <i class="fas fa-user-graduate text-emerald-500 text-xl"></i>
            </div>
            <p class="text-4xl font-black text-emerald-900 tabular-nums">{{ number_format($studentsMetric['total'] ?? 0) }}</p>
            <p class="text-xs text-emerald-700/70 mt-2">جدد هذا الشهر: <span class="font-bold">{{ number_format($studentsMetric['new_this_month'] ?? 0) }}</span></p>
            @if($studentsTrend)
                @php $diff = (int) round($studentsTrend['difference']); $percent = $studentsTrend['percent']; $positive = $diff >= 0; @endphp
                <p class="text-xs mt-2 {{ $positive ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">عن الشهر الماضي: {{ $positive ? '+' : '' }}{{ number_format($diff) }} ({{ $percent >= 0 ? '+' : '' }}{{ number_format($percent, 1) }}%)</p>
            @endif
        </div>

        @php $insMetric = $metrics['instructors'] ?? null; $insTrend = $insMetric['trend'] ?? null; @endphp
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 border-2 border-indigo-200/50 shadow-lg bg-white">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-bold text-indigo-800/80">المدربون</p>
                <i class="fas fa-chalkboard-teacher text-indigo-500 text-xl"></i>
            </div>
            <p class="text-4xl font-black text-indigo-900 tabular-nums">{{ number_format($insMetric['total'] ?? 0) }}</p>
            <p class="text-xs text-indigo-700/70 mt-2">جدد هذا الشهر: <span class="font-bold">{{ number_format($insMetric['new_this_month'] ?? 0) }}</span></p>
            @if($insTrend)
                @php $diff = (int) round($insTrend['difference']); $percent = $insTrend['percent']; $positive = $diff >= 0; @endphp
                <p class="text-xs mt-2 {{ $positive ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">عن الشهر الماضي: {{ $positive ? '+' : '' }}{{ number_format($diff) }} ({{ $percent >= 0 ? '+' : '' }}{{ number_format($percent, 1) }}%)</p>
            @endif
        </div>

        @php $coursesMetric = $metrics['courses'] ?? null; $coursesTrend = $coursesMetric['trend'] ?? null; @endphp
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 border-2 border-amber-200/50 shadow-lg bg-white">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-bold text-amber-800/80">كورسات أونلاين</p>
                <i class="fas fa-book text-amber-500 text-xl"></i>
            </div>
            <p class="text-4xl font-black text-amber-900 tabular-nums">{{ number_format($coursesMetric['total'] ?? 0) }}</p>
            <p class="text-xs text-amber-700/70 mt-2">جدد هذا الشهر: <span class="font-bold">{{ number_format($coursesMetric['new_this_month'] ?? 0) }}</span></p>
            @if($coursesTrend)
                @php $diff = (int) round($coursesTrend['difference']); $percent = $coursesTrend['percent']; $positive = $diff >= 0; @endphp
                <p class="text-xs mt-2 {{ $positive ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">عن الشهر الماضي: {{ $positive ? '+' : '' }}{{ number_format($diff) }} ({{ $percent >= 0 ? '+' : '' }}{{ number_format($percent, 1) }}%)</p>
            @endif
        </div>
    </div>

    {{-- صف ثانٍ: أرقام تشغيلية للفرع --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4 max-w-[1600px] mx-auto">
        <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm text-center">
            <p class="text-xs font-semibold text-slate-500">أوفلاين</p>
            <p class="text-2xl font-black text-slate-900 mt-1 tabular-nums">{{ number_format($stats['offline_courses']) }}</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm text-center">
            <p class="text-xs font-semibold text-slate-500">مسارات</p>
            <p class="text-2xl font-black text-slate-900 mt-1 tabular-nums">{{ number_format($stats['learning_paths']) }}</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm text-center">
            <p class="text-xs font-semibold text-slate-500">تسجيلات</p>
            <p class="text-2xl font-black text-slate-900 mt-1 tabular-nums">{{ number_format($stats['enrollments']) }}</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm text-center">
            <p class="text-xs font-semibold text-slate-500">طلبات</p>
            <p class="text-2xl font-black text-slate-900 mt-1 tabular-nums">{{ number_format($stats['orders']) }}</p>
        </div>
        <div class="rounded-2xl bg-white border border-orange-200 p-4 shadow-sm text-center">
            <p class="text-xs font-semibold text-orange-700">طلبات معلّقة</p>
            <p class="text-2xl font-black text-orange-900 mt-1 tabular-nums">{{ number_format($stats['orders_pending']) }}</p>
        </div>
        <div class="rounded-2xl bg-white border border-rose-200 p-4 shadow-sm text-center">
            <p class="text-xs font-semibold text-rose-700">فواتير معلّقة</p>
            <p class="text-2xl font-black text-rose-900 mt-1 tabular-nums">{{ number_format($stats['pending_invoices']) }}</p>
        </div>
    </div>

    {{-- مالي --}}
    @php $revMetric = $metrics['monthly_revenue'] ?? null; $revTrend = $revMetric['trend'] ?? null; @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-[1600px] mx-auto">
        <div class="rounded-2xl bg-white border-2 border-emerald-200 p-6 shadow-lg">
            <p class="text-sm font-bold text-emerald-800 mb-1">إجمالي الإيرادات (مدفوعات مكتملة)</p>
            <p class="text-3xl font-black text-emerald-900">{{ number_format($stats['total_revenue'] ?? 0, 2) }} <span class="text-lg">ج.م</span></p>
        </div>
        <div class="rounded-2xl bg-white border-2 border-blue-200 p-6 shadow-lg">
            <p class="text-sm font-bold text-blue-800 mb-1">إيرادات الشهر الحالي</p>
            <p class="text-3xl font-black text-blue-900">{{ number_format($stats['monthly_revenue'] ?? 0, 2) }} <span class="text-lg">ج.م</span></p>
            @if($revTrend)
                @php $diff = (float) $revTrend['difference']; $percent = $revTrend['percent']; $positive = $diff >= 0; @endphp
                <p class="text-xs mt-2 {{ $positive ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">مقارنة بجزء مماثل من الشهر الماضي: {{ $positive ? '+' : '' }}{{ number_format($diff, 2) }} ج.م ({{ $percent >= 0 ? '+' : '' }}{{ number_format($percent, 1) }}%)</p>
            @endif
        </div>
    </div>

    {{-- نشاط أسبوعي بسيط --}}
    @if($weeklyActivity->isNotEmpty())
        @php $waMax = max((int) $weeklyActivity->max('count'), 1); @endphp
        <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm max-w-[1600px] mx-auto">
            <h3 class="font-bold text-slate-900 mb-4"><i class="fas fa-chart-bar text-sky-600 ml-2"></i> نشاط المستخدمين (آخر 7 أيام)</h3>
            <div class="flex items-end gap-2 h-36">
                @foreach($weeklyActivity as $row)
                    <div class="flex-1 flex flex-col items-center gap-1 min-w-0">
                        <div class="w-full rounded-t-lg bg-gradient-to-t from-sky-600 to-sky-400 transition-all" style="height: {{ max(8, (int) round(($row->count / $waMax) * 120)) }}px" title="{{ $row->date }}: {{ $row->count }}"></div>
                        <span class="text-[10px] text-slate-500 truncate w-full text-center" dir="ltr">{{ $row->date }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-[1600px] mx-auto">
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-slate-900"><i class="fas fa-history text-blue-600 ml-2"></i> آخر الأنشطة</h3>
            </div>
            <div class="p-4 space-y-3 max-h-80 overflow-y-auto">
                @forelse($recentActivities as $activity)
                    <div class="text-sm border border-slate-100 rounded-xl p-3">
                        <span class="font-semibold text-slate-800">{{ $activity->user->name ?? '—' }}</span>
                        <span class="text-slate-500 text-xs mr-2">{{ $activity->action }}</span>
                        <span class="text-xs text-slate-400 block mt-1">{{ $activity->created_at?->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-center text-slate-500 py-6 text-sm">لا يوجد نشاط مسجّل لمستخدمي هذا الفرع.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-slate-900"><i class="fas fa-user-plus text-emerald-600 ml-2"></i> آخر المستخدمين</h3>
                <a href="{{ route('branch.office.users') }}" class="text-sm font-semibold text-emerald-700 hover:underline">عرض الكل</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentUsers as $u)
                    <div class="px-4 py-3 flex justify-between gap-2 text-sm">
                        <div>
                            <p class="font-medium text-slate-900">{{ $u->name }}</p>
                            <p class="text-xs text-slate-500" dir="ltr">{{ $u->email }}</p>
                        </div>
                        <span class="text-xs rounded-full bg-slate-100 px-2 py-0.5 self-start">{{ $u->role }}</span>
                    </div>
                @empty
                    <p class="text-center text-slate-500 py-8">لا مستخدمين بعد.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-[1600px] mx-auto">
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-slate-900"><i class="fas fa-shopping-cart text-orange-600 ml-2"></i> آخر الطلبات</h3>
                <a href="{{ route('branch.office.orders') }}" class="text-sm font-semibold text-orange-700 hover:underline">عرض الكل</a>
            </div>
            <div class="divide-y divide-slate-100 text-sm">
                @forelse($recentOrders as $o)
                    <div class="px-4 py-3">
                        <p class="font-medium text-slate-900">#{{ $o->id }} — {{ $o->user->name ?? '—' }}</p>
                        <p class="text-xs text-slate-500">{{ number_format((float) $o->amount, 2) }} ج.م · {{ $o->status }} · {{ $o->course?->title ?? ($o->learningPath?->name ?? '—') }}</p>
                    </div>
                @empty
                    <p class="text-center text-slate-500 py-8">لا طلبات.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-slate-900"><i class="fas fa-money-check-alt text-green-600 ml-2"></i> آخر المدفوعات</h3>
                <a href="{{ route('branch.office.payments') }}" class="text-sm font-semibold text-green-700 hover:underline">عرض الكل</a>
            </div>
            <div class="divide-y divide-slate-100 text-sm">
                @forelse($recentPayments as $p)
                    <div class="px-4 py-3">
                        <p class="font-medium text-slate-900">{{ $p->user->name ?? '—' }}</p>
                        <p class="text-xs text-slate-500">{{ number_format((float) $p->amount, 2) }} ج.م · {{ $p->paid_at?->diffForHumans() ?? '—' }}</p>
                    </div>
                @empty
                    <p class="text-center text-slate-500 py-8">لا مدفوعات مكتملة بعد.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-[1600px] mx-auto">
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-rose-50">
                <h3 class="font-bold text-slate-900"><i class="fas fa-file-invoice text-rose-600 ml-2"></i> فواتير معلّقة</h3>
                <a href="{{ route('branch.office.invoices', ['status' => 'pending']) }}" class="text-sm font-semibold text-rose-700 hover:underline">عرض الكل</a>
            </div>
            <div class="divide-y divide-slate-100 text-sm">
                @forelse($pendingInvoices as $inv)
                    <div class="px-4 py-3">
                        <p class="font-medium text-slate-900">{{ $inv->invoice_number }} — {{ $inv->user->name ?? '—' }}</p>
                        <p class="text-xs text-slate-500">{{ number_format((float) $inv->total_amount, 2) }} ج.م</p>
                    </div>
                @empty
                    <p class="text-center text-slate-500 py-8">لا فواتير معلّقة.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-amber-50">
                <h3 class="font-bold text-slate-900"><i class="fas fa-book text-amber-600 ml-2"></i> آخر كورسات أونلاين</h3>
                <a href="{{ route('branch.office.courses-online') }}" class="text-sm font-semibold text-amber-700 hover:underline">عرض الكل</a>
            </div>
            <div class="divide-y divide-slate-100 text-sm">
                @forelse($recentCourses as $c)
                    <div class="px-4 py-3">
                        <p class="font-medium text-slate-900">{{ $c->localized('title') }}</p>
                        <p class="text-xs text-slate-500">{{ $c->is_active ? 'نشط' : 'موقوف' }} · {{ number_format((float) $c->price, 2) }} ج.م</p>
                    </div>
                @empty
                    <p class="text-center text-slate-500 py-8">لا كورسات لهذا الفرع.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
