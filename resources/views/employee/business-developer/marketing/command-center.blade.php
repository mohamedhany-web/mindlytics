@extends('layouts.employee')

@section('title', 'مركز التسويق — Business Developer')
@section('header', 'مركز التسويق التنفيذي')

@push('styles')
<style>
    .bd-mkt {
        --bd-ink: #0f172a;
        --bd-teal: #0f766e;
        --bd-amber: #b45309;
    }
    .bd-mkt .hero {
        background:
            radial-gradient(1200px 400px at 100% -20%, rgba(15, 118, 110, 0.18), transparent 60%),
            radial-gradient(900px 320px at 0% 120%, rgba(180, 83, 9, 0.12), transparent 55%),
            linear-gradient(135deg, #0f172a 0%, #134e4a 55%, #115e59 100%);
    }
    .bd-mkt .glass {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.14);
        backdrop-filter: blur(8px);
    }
    .bd-mkt .bar {
        transition: height .35s ease;
    }
    .bd-mkt .plan-card {
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }
    .bd-mkt .plan-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        border-color: #99f6e4;
    }
</style>
@endpush

@section('content')
@php
    $statusMeta = [
        'draft' => ['مسودة', 'bg-slate-100 text-slate-700 border-slate-200'],
        'active' => ['نشط', 'bg-emerald-50 text-emerald-800 border-emerald-200'],
        'paused' => ['متوقف', 'bg-amber-50 text-amber-900 border-amber-200'],
        'completed' => ['مكتمل', 'bg-sky-50 text-sky-800 border-sky-200'],
    ];
    $evtLabel = fn ($s) => \App\Models\ModeratorMarketingCalendarEvent::statusLabel($s);
@endphp

<div class="bd-mkt w-full space-y-6">
    {{-- Hero --}}
    <section class="hero rounded-3xl overflow-hidden text-white shadow-xl">
        <div class="p-5 sm:p-8 lg:p-10">
            <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-6">
                <div class="max-w-2xl">
                    <p class="text-teal-200/90 text-xs font-bold tracking-[0.2em] uppercase mb-2">Business Developer · Marketing Command</p>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black leading-tight">مركز التسويق التنفيذي</h1>
                    <p class="mt-3 text-sm sm:text-base text-teal-50/85 leading-relaxed">
                        رؤية موحّدة لكل خطط المشرفين، المنصات، وجدولة المحتوى — مع متابعة التنفيذ والتأكيدات المتأخرة.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    <a href="{{ route('employee.marketing-plans.create') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-white text-slate-900 px-4 py-2.5 text-sm font-bold hover:bg-teal-50">
                        <i class="fas fa-plus"></i> خطة جديدة
                    </a>
                    <a href="{{ route('employee.marketing-plans.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl glass text-white px-4 py-2.5 text-sm font-semibold hover:bg-white/15">
                        <i class="fas fa-list"></i> إدارة الخطط
                    </a>
                    <a href="{{ route('employee.marketing-today.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl glass text-white px-4 py-2.5 text-sm font-semibold hover:bg-white/15">
                        <i class="fas fa-check-double"></i> مهام اليوم
                    </a>
                    <a href="{{ route('employee.calendar') }}"
                       class="inline-flex items-center gap-2 rounded-xl glass text-white px-4 py-2.5 text-sm font-semibold hover:bg-white/15">
                        <i class="fas fa-calendar-alt"></i> التقويم
                    </a>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-3">
                @foreach([
                    ['الخطط', $stats['total'], 'fa-layer-group'],
                    ['نشطة', $stats['active'], 'fa-bolt'],
                    ['مشرفون', $stats['moderators'], 'fa-user-tie'],
                    ['منصات', $stats['platforms'], 'fa-share-nodes'],
                    ['أحداث الأسبوع', $stats['events_week'], 'fa-calendar-week'],
                    ['منشور هذا الأسبوع', $stats['published_week'], 'fa-circle-check'],
                    ['تأكيدات متأخرة', $stats['overdue_confirm'], 'fa-triangle-exclamation'],
                    ['إجمالي الأحداث', $stats['events_total'], 'fa-chart-simple'],
                ] as [$label, $value, $icon])
                    <div class="glass rounded-2xl p-3.5">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <p class="text-[11px] text-teal-100/80 font-semibold">{{ $label }}</p>
                            <i class="fas {{ $icon }} text-teal-200/70 text-xs"></i>
                        </div>
                        <p class="text-2xl font-black tabular-nums">{{ number_format($value) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if($stats['overdue_confirm'] > 0)
        <section class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 sm:px-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <p class="text-sm font-black text-rose-900"><i class="fas fa-bell ml-1"></i> تنبيه تنفيذ</p>
                <p class="text-xs text-rose-800 mt-0.5">يوجد {{ $stats['overdue_confirm'] }} حدثاً يتطلب تأكيد تنفيذ ولم يُؤكَّد بعد موعده.</p>
            </div>
            <a href="#bd-overdue" class="inline-flex items-center gap-2 rounded-xl bg-rose-700 hover:bg-rose-800 text-white px-4 py-2 text-sm font-semibold">عرض المتأخرات</a>
        </section>
    @endif

    {{-- Timeline + platform mix --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <section class="xl:col-span-2 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between gap-2">
                <h2 class="text-sm font-black text-slate-900"><i class="fas fa-chart-column text-teal-600 ml-1"></i> إيقاع الأسبوع القادم</h2>
                <span class="text-[11px] font-semibold text-slate-500">حتى {{ $weekEnd->format('Y-m-d') }}</span>
            </div>
            <div class="p-4 sm:p-5">
                <div class="flex items-end gap-2 sm:gap-3 h-36">
                    @foreach($timelineDays as $day)
                        @php $h = (int) round(($day['count'] / $maxDay) * 100); @endphp
                        <div class="flex-1 flex flex-col items-center gap-2 min-w-0">
                            <div class="w-full flex flex-col justify-end h-28 rounded-lg bg-slate-50 border border-slate-100 px-1 pt-2">
                                <div class="bar w-full rounded-md bg-teal-500/90 mx-auto"
                                     style="height: {{ max($day['count'] > 0 ? 12 : 4, $h) }}%"
                                     title="{{ $day['count'] }} حدث · {{ $day['published'] }} منشور"></div>
                            </div>
                            <div class="text-center min-w-0">
                                <p class="text-[10px] font-bold text-slate-700 truncate">{{ $day['label'] }}</p>
                                <p class="text-xs font-black text-teal-700 tabular-nums">{{ $day['count'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100">
                <h2 class="text-sm font-black text-slate-900"><i class="fas fa-share-nodes text-amber-600 ml-1"></i> مزيج المنصات</h2>
            </div>
            <div class="p-4 space-y-3">
                @forelse($platformMix as $row)
                    @php
                        $pct = $stats['platforms'] > 0 ? round(($row['total'] / $stats['platforms']) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-semibold text-slate-800">{{ $row['label'] }}</span>
                            <span class="tabular-nums text-slate-500">{{ $row['total'] }} · {{ $pct }}%</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full bg-amber-500" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 text-center py-8">لا منصات مربوطة بعد.</p>
                @endforelse

                <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-[11px]">
                    @foreach(['scheduled' => 'مجدول', 'published' => 'منشور', 'draft' => 'مسودة', 'idea' => 'فكرة'] as $key => $label)
                        <div class="rounded-lg bg-slate-50 border border-slate-100 px-2.5 py-2 flex justify-between">
                            <span class="text-slate-600">{{ $label }}</span>
                            <span class="font-black text-slate-900 tabular-nums">{{ $eventStatusMix[$key] ?? 0 }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    {{-- Plans grid --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <h2 class="text-base font-black text-slate-900">كل خطط التسويق</h2>
                <p class="text-xs text-slate-500 mt-0.5">عرض تنفيذي عبر كل المشرفين — اضغط للدخول لإدارة الخطة</p>
            </div>
            <form method="get" class="flex flex-wrap gap-2 items-center">
                <select name="status" class="rounded-xl border-slate-300 text-sm" onchange="this.form.submit()">
                    <option value="">كل الحالات</option>
                    @foreach(['active' => 'نشط', 'draft' => 'مسودة', 'paused' => 'متوقف', 'completed' => 'مكتمل'] as $val => $label)
                        <option value="{{ $val }}" @selected($statusFilter === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                @if($statusFilter)
                    <a href="{{ route('employee.business-developer.marketing') }}" class="text-xs font-semibold text-teal-700 hover:underline">مسح الفلتر</a>
                @endif
            </form>
        </div>

        <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($plans as $plan)
                @php
                    $st = $statusMeta[$plan->status] ?? [$plan->status, 'bg-slate-100 text-slate-700 border-slate-200'];
                    $health = ($plan->overdue_confirm_count ?? 0) > 0
                        ? 'border-rose-200'
                        : (($plan->status === 'active') ? 'border-teal-200' : 'border-slate-200');
                @endphp
                <a href="{{ route('employee.marketing-plans.show', $plan) }}"
                   class="plan-card block rounded-2xl border {{ $health }} bg-gradient-to-br from-white to-slate-50 p-4">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="min-w-0">
                            <p class="text-base font-black text-slate-900 truncate">{{ $plan->title }}</p>
                            <p class="text-xs text-slate-500 mt-1">
                                <i class="fas fa-user-tie text-teal-600 ml-0.5"></i>
                                {{ $plan->moderator?->name ?? '—' }}
                            </p>
                        </div>
                        <span class="inline-flex rounded-lg border px-2 py-0.5 text-[11px] font-bold {{ $st[1] }}">{{ $st[0] }}</span>
                    </div>

                    @if($plan->summary)
                        <p class="text-xs text-slate-600 line-clamp-2 mb-3">{{ $plan->summary }}</p>
                    @endif

                    <div class="grid grid-cols-4 gap-2 mb-3">
                        <div class="rounded-lg bg-white border border-slate-100 p-2 text-center">
                            <p class="text-[10px] text-slate-500">منصات</p>
                            <p class="text-sm font-black tabular-nums">{{ $plan->platforms_count }}</p>
                        </div>
                        <div class="rounded-lg bg-white border border-slate-100 p-2 text-center">
                            <p class="text-[10px] text-slate-500">أحداث</p>
                            <p class="text-sm font-black tabular-nums">{{ $plan->calendar_events_count }}</p>
                        </div>
                        <div class="rounded-lg bg-white border border-slate-100 p-2 text-center">
                            <p class="text-[10px] text-slate-500">منشور</p>
                            <p class="text-sm font-black text-emerald-700 tabular-nums">{{ $plan->published_count }}</p>
                        </div>
                        <div class="rounded-lg bg-white border border-slate-100 p-2 text-center">
                            <p class="text-[10px] text-slate-500">متأخر</p>
                            <p class="text-sm font-black {{ ($plan->overdue_confirm_count ?? 0) > 0 ? 'text-rose-700' : 'text-slate-700' }} tabular-nums">{{ $plan->overdue_confirm_count }}</p>
                        </div>
                    </div>

                    @if($plan->platforms->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 mb-3">
                            @foreach($plan->platforms->take(5) as $plat)
                                <span class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-700">
                                    <span class="w-2 h-2 rounded-full" style="background: {{ $plat->color_hex ?: '#0f766e' }}"></span>
                                    {{ $plat->displayName() }}
                                </span>
                            @endforeach
                            @if($plan->platforms->count() > 5)
                                <span class="text-[10px] text-slate-500 font-semibold">+{{ $plan->platforms->count() - 5 }}</span>
                            @endif
                        </div>
                    @endif

                    <div class="flex items-center justify-between text-[11px] text-slate-500">
                        <span>
                            @if($plan->start_date || $plan->end_date)
                                {{ $plan->start_date?->format('Y-m-d') ?? '—' }} → {{ $plan->end_date?->format('Y-m-d') ?? '—' }}
                            @else
                                بدون فترة محددة
                            @endif
                        </span>
                        <span class="font-bold text-teal-700">فتح الخطة ←</span>
                    </div>
                </a>
            @empty
                <div class="md:col-span-2 xl:col-span-3 rounded-2xl border border-dashed border-slate-300 p-12 text-center">
                    <i class="fas fa-bullhorn text-3xl text-slate-300 mb-3"></i>
                    <p class="font-bold text-slate-800">لا توجد خطط تسويق بعد</p>
                    <p class="text-sm text-slate-500 mt-1 mb-4">أنشئ أول خطة أو اطلب من المشرفين إنشاء خططهم.</p>
                    <a href="{{ route('employee.marketing-plans.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 text-sm font-semibold">
                        <i class="fas fa-plus"></i> إنشاء خطة
                    </a>
                </div>
            @endforelse
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        {{-- Upcoming --}}
        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-black text-slate-900"><i class="fas fa-clock text-sky-600 ml-1"></i> القادم خلال 14 يوماً</h2>
                <span class="text-[11px] font-semibold text-slate-500">{{ $upcoming->count() }}</span>
            </div>
            <div class="divide-y divide-slate-100 max-h-[28rem] overflow-y-auto">
                @forelse($upcoming as $evt)
                    <div class="px-4 py-3 hover:bg-slate-50/80 flex gap-3 items-start">
                        <div class="w-14 shrink-0 text-center rounded-lg bg-slate-50 border border-slate-100 py-1.5">
                            <p class="text-[10px] font-bold text-slate-500">{{ $evt->starts_at?->format('m/d') }}</p>
                            <p class="text-xs font-black text-slate-800 tabular-nums">{{ $evt->starts_at?->format('H:i') }}</p>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-slate-900 truncate">{{ $evt->title }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                {{ $evt->plan?->title ?? '—' }}
                                · {{ $evt->plan?->moderator?->name ?? '—' }}
                                @if($evt->platform)
                                    · <span class="font-semibold" style="color: {{ $evt->platform->color_hex ?: '#0f766e' }}">{{ $evt->platform->displayName() }}</span>
                                @endif
                            </p>
                            <div class="mt-1 flex flex-wrap gap-1.5">
                                <span class="inline-flex rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold text-slate-700">{{ $evtLabel($evt->status) }}</span>
                                @if($evt->assignee)
                                    <span class="inline-flex rounded-md bg-teal-50 px-1.5 py-0.5 text-[10px] font-bold text-teal-800">{{ $evt->assignee->name }}</span>
                                @endif
                            </div>
                        </div>
                        @if($evt->plan_id)
                            <a href="{{ route('employee.marketing-plans.show', $evt->plan_id) }}" class="text-[11px] font-bold text-teal-700 hover:underline shrink-0">الخطة</a>
                        @endif
                    </div>
                @empty
                    <div class="p-10 text-center text-sm text-slate-500">لا أحداث مجدولة في الأسبوعين القادمين.</div>
                @endforelse
            </div>
        </section>

        {{-- Overdue --}}
        <section id="bd-overdue" class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between bg-rose-50/50">
                <h2 class="text-sm font-black text-rose-950"><i class="fas fa-triangle-exclamation text-rose-600 ml-1"></i> تأكيدات متأخرة</h2>
                <span class="text-[11px] font-semibold text-rose-700">{{ $overdue->count() }}</span>
            </div>
            <div class="divide-y divide-slate-100 max-h-[28rem] overflow-y-auto">
                @forelse($overdue as $evt)
                    <div class="px-4 py-3 hover:bg-rose-50/40 flex gap-3 items-start">
                        <div class="w-14 shrink-0 text-center rounded-lg bg-rose-50 border border-rose-100 py-1.5">
                            <p class="text-[10px] font-bold text-rose-600">{{ $evt->starts_at?->format('m/d') }}</p>
                            <p class="text-xs font-black text-rose-900 tabular-nums">{{ $evt->starts_at?->format('H:i') }}</p>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-slate-900 truncate">{{ $evt->title }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                {{ $evt->plan?->title ?? '—' }} · {{ $evt->plan?->moderator?->name ?? '—' }}
                                @if($evt->assignee) · المسؤول: {{ $evt->assignee->name }} @endif
                            </p>
                        </div>
                        @if($evt->plan_id)
                            <a href="{{ route('employee.marketing-plans.show', $evt->plan_id) }}" class="text-[11px] font-bold text-rose-700 hover:underline shrink-0">متابعة</a>
                        @endif
                    </div>
                @empty
                    <div class="p-10 text-center text-sm text-emerald-700 font-semibold">
                        <i class="fas fa-check-circle ml-1"></i> لا تأكيدات متأخرة — الوضع ممتاز.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
