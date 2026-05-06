@extends('layouts.admin')

@section('title', 'Insights — المبيعات')
@section('header', 'Insights — تقرير أداء موظف المبيعات')

@section('content')
<div class="p-4 md:p-6 space-y-6" style="background:#f8fafc;min-height:100vh;">
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5 md:p-6">
        <form method="get" action="{{ route('admin.sales.insights.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-gray-600 mb-1">الموظف</label>
                <select name="user_id" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm" required>
                    @foreach($salesReps as $r)
                        <option value="{{ $r->id }}" @selected((string) $rep->id === (string) $r->id)>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">الفترة</label>
                <select name="period" id="period_sel" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm">
                    <option value="day" @selected(request('period', 'week') === 'day')>يومي</option>
                    <option value="week" @selected(request('period', 'week') === 'week')>أسبوعي</option>
                    <option value="month" @selected(request('period', 'week') === 'month')>شهري</option>
                    <option value="custom" @selected(request('period', 'week') === 'custom')>مخصص</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">من</label>
                <input type="date" name="date_from" id="date_from" value="{{ $start->toDateString() }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">إلى</label>
                <input type="date" name="date_to" id="date_to" value="{{ $end->toDateString() }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="flex flex-wrap gap-2 lg:col-span-6">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-md">
                    <i class="fas fa-sync-alt"></i> تحديث
                </button>
                <button type="button" onclick="downloadInsightsPdf()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-slate-950 text-white rounded-xl text-sm font-bold shadow-md">
                    <i class="fas fa-file-pdf"></i> PDF للموظف
                </button>
                <a href="{{ route('admin.sales.commissions.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-bold shadow-md">
                    <i class="fas fa-coins"></i> كوميشن المبيعات
                </a>
            </div>
            <input type="hidden" name="_preset_period_label" value="{{ $periodLabel }}">
        </form>
        <p class="text-xs text-slate-500 mt-3">الفترة الفعلية: <strong>{{ $start->format('Y-m-d') }}</strong> إلى <strong>{{ $end->format('Y-m-d') }}</strong>.</p>
    </div>

    <div id="insights-pdf-root" class="space-y-6">
        <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <p class="text-sm text-gray-700">الموظف: <span class="font-black text-gray-900">{{ $rep->name }}</span></p>
                    <p class="text-xs text-gray-500 mt-1">Insights — {{ $periodLabel }} — {{ $start->format('Y-m-d') }} → {{ $end->format('Y-m-d') }}</p>
                </div>
                @php
                    $statusClass = match($decision['status'] ?? 'good') {
                        'excellent' => 'bg-emerald-600 text-white',
                        'good' => 'bg-sky-600 text-white',
                        'warning' => 'bg-amber-600 text-white',
                        default => 'bg-rose-600 text-white',
                    };
                @endphp
                <div class="px-4 py-2 rounded-xl {{ $statusClass }}">
                    <p class="text-xs font-bold opacity-90">القرار</p>
                    <p class="text-sm font-black">{{ $decision['status_label'] ?? '—' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm mt-4">
                <div class="rounded-xl bg-white border border-emerald-100 p-3">
                    <p class="text-xs text-gray-500 font-semibold">المؤشر المركّب</p>
                    <p class="text-2xl font-black text-emerald-700 tabular-nums">{{ $decision['composite'] ?? ($periodReport['composite'] ?? '—') }}</p>
                </div>
                <div class="rounded-xl bg-white border border-emerald-100 p-3">
                    <p class="text-xs text-gray-500 font-semibold">Leads في التقرير</p>
                    <p class="text-2xl font-black text-slate-800 tabular-nums">{{ $counts['leads'] ?? 0 }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">أنشأها الموظف: {{ $counts['leads_created_by'] ?? 0 }}</p>
                </div>
                <div class="rounded-xl bg-white border border-emerald-100 p-3">
                    <p class="text-xs text-gray-500 font-semibold">أنشطة CRM</p>
                    <p class="text-2xl font-black text-slate-800 tabular-nums">{{ $counts['activities'] ?? 0 }}</p>
                </div>
                <div class="rounded-xl bg-white border border-emerald-100 p-3">
                    <p class="text-xs text-gray-500 font-semibold">wins معتمدة (كوميشن)</p>
                    <p class="text-2xl font-black text-emerald-800 tabular-nums">{{ $commission['confirmed_wins'] ?? 0 }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">كوميشن: {{ number_format((float) ($commission['commission_from_leads'] ?? 0), 2) }} ج.م</p>
                </div>
            </div>

            @if(!empty($decision['flags']))
                <ul class="mt-4 text-sm text-amber-900 space-y-1 list-disc list-inside">
                    @foreach($decision['flags'] as $f)
                        <li>{{ $f }}</li>
                    @endforeach
                </ul>
            @endif

            <div class="mt-4 rounded-xl border border-slate-200 bg-white p-4">
                <p class="text-xs font-bold text-slate-600 mb-2">توصيات النظام</p>
                <ul class="text-sm text-slate-800 space-y-1 list-disc list-inside">
                    @foreach(($decision['recommendations'] ?? []) as $rec)
                        <li>{{ $rec }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-slate-50">
                <h2 class="text-base font-black text-gray-900">تفصيل KPIs (الفترة)</h2>
            </div>
            <div class="overflow-x-auto p-4">
                <table class="min-w-[740px] w-full text-sm">
                    <thead>
                        <tr class="bg-emerald-800 text-white">
                            <th class="px-3 py-2 text-right">المؤشر</th>
                            <th class="px-3 py-2 text-center">الفعلي</th>
                            <th class="px-3 py-2 text-center">الهدف (فترة)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($periodReport['kpi_lines'] ?? [] as $line)
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
                <h3 class="text-sm font-black text-gray-900 mb-2">Leads (عينة)</h3>
                @if($leadsSample->isEmpty())
                    <p class="text-xs text-gray-500">لا توجد صفوف في الفترة.</p>
                @else
                    <ul class="text-xs space-y-2 text-gray-700">
                        @foreach($leadsSample as $l)
                            <li class="border-b border-gray-100 pb-2">
                                <span class="font-bold">{{ $l->name }}</span>
                                <span class="text-gray-500"> — {{ \App\Models\SalesLead::stageLabel($l->stage) }}</span>
                                @if($l->email)<span class="text-gray-400"> — {{ $l->email }}</span>@endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
                <h3 class="text-sm font-black text-gray-900 mb-2">أنشطة CRM (عينة)</h3>
                @if($activitiesSample->isEmpty())
                    <p class="text-xs text-gray-500">لا توجد أنشطة في الفترة.</p>
                @else
                    <ul class="text-xs space-y-2 text-gray-700">
                        @foreach($activitiesSample as $a)
                            <li class="border-b border-gray-100 pb-2">
                                <span class="font-bold">{{ \App\Models\SalesActivity::typeLabel($a->type) }}</span>
                                @if($a->lead)<span class="text-gray-500"> — {{ $a->lead->name }} ({{ \App\Models\SalesLead::stageLabel($a->lead->stage) }})</span>@endif
                                <span class="text-gray-400"> — {{ $a->created_at?->format('Y-m-d H:i') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h3 class="text-sm font-black text-gray-900">الكورسات المرتبطة بعملاء الموظف (حسب الإيميل)</h3>
                <span class="text-xs text-slate-500">حسابات مطابقة: {{ (int) ($courses['matched_users'] ?? 0) }}</span>
            </div>
            @if(!empty($courses['note']))
                <p class="text-xs text-amber-800">{{ $courses['note'] }}</p>
            @endif
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-3">
                    <p class="text-xs font-bold text-slate-700 mb-2">تسجيلات كورسات الموقع (Online enrollments)</p>
                    <p class="text-xs text-slate-500 mb-2">الإجمالي في الفترة: {{ (int) data_get($courses, 'online_enrollments.count', 0) }}</p>
                    <ul class="text-xs space-y-2 text-slate-800">
                        @forelse(data_get($courses, 'online_enrollments.rows', collect()) as $e)
                            <li class="border-b border-slate-200 pb-2">
                                <span class="font-bold">{{ $e->user->name ?? '—' }}</span>
                                <span class="text-slate-500"> — {{ $e->course->title ?? '—' }}</span>
                                <span class="text-slate-400"> — {{ $e->enrolled_at?->format('Y-m-d') }}</span>
                            </li>
                        @empty
                            <li class="text-slate-500">—</li>
                        @endforelse
                    </ul>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-3">
                    <p class="text-xs font-bold text-slate-700 mb-2">طلبات شراء / Orders</p>
                    <p class="text-xs text-slate-500 mb-2">الإجمالي في الفترة: {{ (int) data_get($courses, 'orders.count', 0) }}</p>
                    <ul class="text-xs space-y-2 text-slate-800">
                        @forelse(data_get($courses, 'orders.rows', collect()) as $o)
                            <li class="border-b border-slate-200 pb-2">
                                <span class="font-bold">{{ $o->user->name ?? '—' }}</span>
                                <span class="text-slate-500">
                                    — {{ $o->course->title ?? ($o->learningPath->name ?? '—') }}
                                </span>
                                <span class="text-slate-400"> — {{ $o->created_at?->format('Y-m-d') }}</span>
                            </li>
                        @empty
                            <li class="text-slate-500">—</li>
                        @endforelse
                    </ul>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-3">
                    <p class="text-xs font-bold text-slate-700 mb-2">حجوزات أوفلاين (Offline bookings)</p>
                    <p class="text-xs text-slate-500 mb-2">الإجمالي في الفترة: {{ (int) data_get($courses, 'offline_bookings.count', 0) }}</p>
                    <ul class="text-xs space-y-2 text-slate-800">
                        @forelse(data_get($courses, 'offline_bookings.rows', collect()) as $b)
                            <li class="border-b border-slate-200 pb-2">
                                <span class="font-bold">{{ $b->user->name ?? '—' }}</span>
                                <span class="text-slate-500"> — {{ $b->course->title ?? '—' }}</span>
                                <span class="text-slate-400"> — جروب: {{ $b->assignedGroup->name ?? ($b->requestedGroup->name ?? '—') }}</span>
                            </li>
                        @empty
                            <li class="text-slate-500">—</li>
                        @endforelse
                    </ul>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-3">
                    <p class="text-xs font-bold text-slate-700 mb-2">تسجيلات أوفلاين (Enrollments)</p>
                    <p class="text-xs text-slate-500 mb-2">الإجمالي في الفترة: {{ (int) data_get($courses, 'offline_enrollments.count', 0) }}</p>
                    <ul class="text-xs space-y-2 text-slate-800">
                        @forelse(data_get($courses, 'offline_enrollments.rows', collect()) as $en)
                            <li class="border-b border-slate-200 pb-2">
                                <span class="font-bold">{{ $en->student->name ?? '—' }}</span>
                                <span class="text-slate-500"> — {{ $en->course->title ?? '—' }}</span>
                                <span class="text-slate-400"> — {{ ($en->enrollment_channel ?? 'offline') === 'online' ? 'أونلاين' : 'أوفلاين' }} — جروب: {{ $en->group->name ?? '—' }}</span>
                            </li>
                        @empty
                            <li class="text-slate-500">—</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
            <h3 class="text-sm font-black text-gray-900 mb-2">سجل النظام (عينة)</h3>
            @if($auditSample->isEmpty())
                <p class="text-xs text-gray-500">لا توجد سجلات في الفترة.</p>
            @else
                <ul class="text-xs space-y-2 text-gray-700">
                    @foreach($auditSample as $log)
                        <li class="border-b border-gray-100 pb-2">
                            <span class="font-bold">{{ $log->user->name ?? '—' }}</span>
                            <span class="text-gray-400"> — {{ $log->created_at?->format('Y-m-d H:i') }}</span>
                            <div class="text-gray-700 mt-1 whitespace-pre-wrap">{{ \Illuminate\Support\Str::limit($log->description ?? $log->action, 180) }}</div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function downloadInsightsPdf() {
        const element = document.getElementById('insights-pdf-root');
        const opt = {
            margin: 8,
            filename: 'sales-insights-{{ $rep->id }}-{{ $start->format('Y-m-d') }}-{{ $end->format('Y-m-d') }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }

    (function () {
        const sel = document.getElementById('period_sel');
        const from = document.getElementById('date_from');
        const to = document.getElementById('date_to');
        if (!sel || !from || !to) return;

        function toggleCustom() {
            const isCustom = sel.value === 'custom';
            from.disabled = !isCustom;
            to.disabled = !isCustom;
        }

        sel.addEventListener('change', toggleCustom);
        toggleCustom();
    })();
</script>
@endpush
@endsection

