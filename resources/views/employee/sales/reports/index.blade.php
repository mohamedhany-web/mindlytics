@extends('layouts.employee')

@section('title', 'تقارير المبيعات (Excel)')
@section('header', 'تقارير المبيعات')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div class="space-y-1">
            <p class="text-sm text-gray-600 max-w-3xl">
                صفحة تقارير مخصصة لاستخراج ملف Excel شامل عن كل ما يخص عملك في المبيعات خلال فترة معينة: مؤشرات الأداء (KPIs)، العملاء المحتملون ذوو الصلة بالفترة، أنشطة CRM، وسجل النظام (مبيعات).
            </p>
            <a href="{{ route('employee.sales.leads.index') }}" class="text-sm text-emerald-700 font-semibold hover:underline">← العملاء المحتملون</a>
        </div>
    </div>

    @if($error)
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm font-semibold">{{ $error }}</div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5 md:p-6">
        <form method="get" action="{{ route('employee.sales.reports.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">من تاريخ</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">إلى تاريخ</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm" required>
            </div>

            <div class="flex flex-wrap gap-2 md:col-span-2 lg:col-span-2">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-md">
                    <i class="fas fa-sync-alt"></i> تحديث المعاينة
                </button>
                <a href="{{ route('employee.sales.reports.export', request()->query()) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-l from-emerald-600 to-teal-600 text-white rounded-xl text-sm font-bold shadow-lg border border-emerald-400/40">
                    <i class="fas fa-file-excel"></i> تصدير Excel كامل
                </a>
            </div>
        </form>
    </div>

    @if($start && $end && !$error)
        <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-sm">
            <p class="text-sm text-emerald-900 font-bold mb-2">الفترة: {{ $start->format('Y-m-d') }} — {{ $end->format('Y-m-d') }} ({{ max(1, (int) $start->diffInDays($end) + 1) }} يوماً)</p>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                <div class="rounded-xl bg-white border border-emerald-100 p-3">
                    <p class="text-xs text-gray-500 font-semibold">المؤشر المركّب</p>
                    <p class="text-2xl font-black text-emerald-700 tabular-nums">{{ $periodReport['composite'] ?? '—' }}</p>
                </div>
                <div class="rounded-xl bg-white border border-emerald-100 p-3">
                    <p class="text-xs text-gray-500 font-semibold">عملاء في التقرير</p>
                    <p class="text-2xl font-black text-slate-800 tabular-nums">{{ $counts['leads'] ?? 0 }}</p>
                </div>
                <div class="rounded-xl bg-white border border-emerald-100 p-3">
                    <p class="text-xs text-gray-500 font-semibold">أنشطة CRM</p>
                    <p class="text-2xl font-black text-slate-800 tabular-nums">{{ $counts['activities'] ?? 0 }}</p>
                </div>
                <div class="rounded-xl bg-white border border-emerald-100 p-3">
                    <p class="text-xs text-gray-500 font-semibold">Leads أنشأتها أنا</p>
                    <p class="text-2xl font-black text-slate-800 tabular-nums">{{ $counts['leads_created_by_me'] ?? 0 }}</p>
                </div>
            </div>

            <div class="mt-3 text-xs text-gray-600">
                Leads محولة/منشأة من الإدارة ومُسندة إليك (داخل الفترة): <span class="font-bold">{{ $counts['leads_assigned_to_me_created_by_admin'] ?? 0 }}</span>
            </div>

            @if(!empty($periodReport['alert_flags']))
                <ul class="mt-4 text-sm text-amber-900 space-y-1 list-disc list-inside">
                    @foreach($periodReport['alert_flags'] as $f)
                        <li>{{ $f }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-slate-50">
                <h2 class="text-base font-black text-gray-900">تفصيل KPIs (معاينة)</h2>
            </div>
            <div class="overflow-x-auto p-4">
                <table class="min-w-[640px] w-full text-sm">
                    <thead>
                        <tr class="bg-emerald-800 text-white">
                            <th class="px-3 py-2 text-right">المؤشر</th>
                            <th class="px-3 py-2 text-center">الفعلي</th>
                            <th class="px-3 py-2 text-center">الهدف (فترة)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach(($periodReport['kpi_lines'] ?? []) as $line)
                            <tr class="hover:bg-emerald-50/50">
                                <td class="px-3 py-2 font-medium text-gray-800">{{ $line['label'] ?? '' }}</td>
                                <td class="px-3 py-2 text-center tabular-nums">{{ $line['actual'] ?? '—' }}</td>
                                <td class="px-3 py-2 text-center tabular-nums">{{ $line['target'] ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
                <h3 class="text-sm font-black text-gray-900 mb-2">عملاء محتملون (عينة)</h3>
                @if($leadsSample->isEmpty())
                    <p class="text-xs text-gray-500">لا توجد صفوف في المعاينة.</p>
                @else
                    <ul class="text-xs space-y-2 text-gray-700">
                        @foreach($leadsSample as $l)
                            <li class="border-b border-gray-100 pb-2">
                                <span class="font-bold">{{ $l->name }}</span>
                                <span class="text-gray-500"> — {{ \App\Models\SalesLead::stageLabel($l->stage) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
                <h3 class="text-sm font-black text-gray-900 mb-2">أنشطة CRM (عينة)</h3>
                @if($activitiesSample->isEmpty())
                    <p class="text-xs text-gray-500">لا توجد صفوف في المعاينة.</p>
                @else
                    <ul class="text-xs space-y-2 text-gray-700">
                        @foreach($activitiesSample as $a)
                            <li class="border-b border-gray-100 pb-2">
                                <span class="font-bold">{{ \App\Models\SalesActivity::typeLabel($a->type) }}</span>
                                @if($a->lead)
                                    <span class="text-gray-500"> — {{ $a->lead->name }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
                <h3 class="text-sm font-black text-gray-900 mb-2">سجل النظام (عينة)</h3>
                @if($auditSample->isEmpty())
                    <p class="text-xs text-gray-500">لا توجد صفوف في المعاينة.</p>
                @else
                    <ul class="text-xs space-y-2 text-gray-700">
                        @foreach($auditSample as $log)
                            <li class="border-b border-gray-100 pb-2">{{ \Illuminate\Support\Str::limit($log->description ?? $log->action, 80) }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

