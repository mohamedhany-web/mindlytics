@extends('layouts.employee')

@section('title', 'مراجعة أداء — '.$employee->name)
@section('header', 'تفاصيل الرقابة اليومية')

@section('content')
@php
    $t = match ($row['tone']) {
        'excellent' => ['text' => 'text-emerald-700', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200'],
        'good' => ['text' => 'text-sky-700', 'bg' => 'bg-sky-50', 'border' => 'border-sky-200'],
        'warning' => ['text' => 'text-amber-700', 'bg' => 'bg-amber-50', 'border' => 'border-amber-200'],
        default => ['text' => 'text-rose-700', 'bg' => 'bg-rose-50', 'border' => 'border-rose-200'],
    };
@endphp

<div class="space-y-5">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4 sm:p-5">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <a href="{{ route('employee.sales-manager.scorecard.index', ['date' => $date->toDateString()]) }}" class="text-xs text-slate-500 hover:text-teal-700">
                    <i class="fas fa-arrow-right ml-1"></i> العودة لمركز الرقابة
                </a>
                <h1 class="text-xl font-black text-slate-900 mt-1">{{ $employee->name }}</h1>
                <p class="text-sm text-slate-500">{{ $date->format('Y-m-d') }} · {{ $team->name }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="rounded-2xl border {{ $t['border'] }} {{ $t['bg'] }} px-5 py-3 text-center">
                    <p class="text-[11px] font-semibold text-slate-500">Verified Score</p>
                    <p class="text-3xl font-black tabular-nums {{ $t['text'] }}">{{ $row['verified_score'] }}</p>
                </div>
                <a href="{{ route('employee.sales-manager.scorecard.employee.pdf', ['employee' => $employee->id, 'date' => $date->toDateString()]) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-slate-800 text-white px-4 py-2 text-sm font-semibold">
                    <i class="fas fa-file-pdf"></i> طباعة PDF
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl bg-white border border-slate-200 p-4 sm:p-5">
                <h2 class="font-black text-slate-900 mb-3">أعمدة الدرجة</h2>
                <div class="space-y-3">
                    @foreach($row['pillars'] as $key => $pillar)
                        <div class="rounded-xl border border-slate-100 p-3">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-sm font-bold text-slate-800">{{ $pillar['label'] }}</p>
                                <p class="font-black tabular-nums text-slate-900">{{ $pillar['score'] }}/100</p>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100 overflow-hidden mb-2">
                                <div class="h-full bg-teal-500 rounded-full" style="width: {{ min(100, (float)$pillar['score']) }}%"></div>
                            </div>
                            <ul class="flex flex-wrap gap-2">
                                @foreach($pillar['details'] ?? [] as $d)
                                    <li class="text-[11px] px-2 py-1 rounded-lg bg-slate-50 text-slate-600">{{ $d }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 p-4 sm:p-5">
                <h2 class="font-black text-slate-900 mb-3">القنوات (موثّق فقط يدخل الدرجة)</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
                    <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">محاولات اتصال</p><p class="font-black">{{ $row['sos']['call_attempts_daily'] }}</p></div>
                    <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">تم الرد</p><p class="font-black">{{ $row['sos']['calls_answered_daily'] }}</p></div>
                    <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">مؤهل</p><p class="font-black">{{ $row['sos']['qualified_conversations_daily'] }}</p></div>
                    <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">اجتماعات</p><p class="font-black">{{ $row['sos']['discovery_sessions_daily'] }}</p></div>
                    <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">واتساب CRM</p><p class="font-black">{{ $row['channels']['whatsapp_crm'] }}</p></div>
                    <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">سوشيال مرتبط</p><p class="font-black">{{ $row['channels']['meta_outbound_linked'] }}</p></div>
                    <div class="rounded-xl bg-amber-50 p-3 border border-amber-100"><p class="text-xs text-amber-700">واتساب غير مرتبط</p><p class="font-black text-amber-800">{{ $row['channels']['whatsapp_outbound_unlinked'] }}</p></div>
                    <div class="rounded-xl bg-amber-50 p-3 border border-amber-100"><p class="text-xs text-amber-700">سوشيال غير مرتبط</p><p class="font-black text-amber-800">{{ $row['channels']['meta_outbound_unlinked'] }}</p></div>
                    <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">كولد معمول / مستلم</p><p class="font-black">{{ $row['cold']['worked_today'] }} / {{ $row['cold']['assigned_today'] }}</p></div>
                    <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">مدفوع CRM</p><p class="font-black">{{ $row['financial']['crm_declared_paid'] }}</p></div>
                    <div class="rounded-xl bg-emerald-50 p-3 border border-emerald-100"><p class="text-xs text-emerald-700">مدفوع مؤكد</p><p class="font-black text-emerald-800">{{ $row['financial']['finance_verified_paid'] }}</p></div>
                    <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">إيراد مؤكد</p><p class="font-black tabular-nums">{{ number_format($row['financial']['finance_verified_revenue'], 0) }}</p></div>
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 font-black text-slate-900">نشاط CRM الموثّق ({{ $row['activities']->count() }})</div>
                <div class="overflow-x-auto max-h-80">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-xs text-slate-600">
                            <tr>
                                <th class="text-right px-3 py-2">الوقت</th>
                                <th class="text-right px-3 py-2">النوع</th>
                                <th class="text-right px-3 py-2">العميل</th>
                                <th class="text-right px-3 py-2">النتيجة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($row['activities'] as $act)
                                <tr>
                                    <td class="px-3 py-2 tabular-nums text-xs">{{ $act->created_at?->format('H:i') }}</td>
                                    <td class="px-3 py-2">{{ \App\Models\SalesActivity::typeLabel($act->type) }}</td>
                                    <td class="px-3 py-2">{{ $act->lead?->name ?? '—' }}</td>
                                    <td class="px-3 py-2 text-xs">{{ \App\Models\SalesActivity::outcomeLabel($act->outcome) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-3 py-6 text-center text-slate-400">لا يوجد نشاط مرتبط بعميل هذا اليوم</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 font-black text-slate-900">عملاء تم التواصل معهم</div>
                <div class="overflow-x-auto max-h-64">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-xs text-slate-600">
                            <tr>
                                <th class="text-right px-3 py-2">الاسم</th>
                                <th class="text-right px-3 py-2">المرحلة</th>
                                <th class="text-right px-3 py-2">المصدر</th>
                                <th class="text-right px-3 py-2">كولد</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($row['leads_touched'] as $lead)
                                <tr>
                                    <td class="px-3 py-2">{{ $lead->name }}</td>
                                    <td class="px-3 py-2 text-xs">{{ $lead->stage }}</td>
                                    <td class="px-3 py-2 text-xs">{{ $lead->source }}</td>
                                    <td class="px-3 py-2 text-xs">{{ $lead->import_batch ? 'نعم' : '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-3 py-6 text-center text-slate-400">لا عملاء</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl bg-white border border-slate-200 p-4 sm:p-5">
                <h2 class="font-black text-slate-900 mb-2">حضور والتزام</h2>
                <ul class="space-y-2 text-sm text-slate-700">
                    <li>الحالة: <span class="font-bold">{{ $row['attendance']['status'] }}</span></li>
                    <li>حضور: <span class="font-bold">{{ $row['attendance']['clocked_in'] ? 'نعم' : 'لا' }}</span>
                        @if($row['attendance']['is_late']) <span class="text-amber-600">(متأخر)</span> @endif
                        @if($row['attendance']['is_absent']) <span class="text-rose-600">(غياب)</span> @endif
                    </li>
                    <li>تقرير يومي: <span class="font-bold {{ $row['daily_report_submitted'] ? 'text-emerald-700' : 'text-rose-600' }}">{{ $row['daily_report_submitted'] ? 'مسلّم' : 'غير مسلّم' }}</span></li>
                    <li>انقطاع: <span class="font-bold tabular-nums">{{ $row['attendance']['offline_minutes'] }} د</span></li>
                </ul>
            </div>

            @if(count($row['exceptions']) > 0)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                    <h2 class="font-black text-amber-900 mb-2 text-sm">استثناءات لا تُحتسب</h2>
                    <ul class="space-y-1">
                        @foreach($row['exceptions'] as $ex)
                            <li class="text-xs text-amber-800">• {{ $ex['label'] }} ({{ $ex['count'] }})</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-2xl bg-white border border-slate-200 p-4 sm:p-5">
                <h2 class="font-black text-slate-900 mb-1">مراجعة المدير</h2>
                <p class="text-[11px] text-slate-500 mb-3">الاعتماد يحفظ snapshot الدرجة. لا يُنشأ خصم مالي تلقائياً — التوصية للمراجعة فقط.</p>

                @if($row['review']?->isApproved())
                    <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-3 py-2 text-sm text-emerald-800 mb-3">
                        معتمد في {{ $row['review']->approved_at?->format('Y-m-d H:i') }}
                        · التوصية: {{ $row['review']->recommendationLabel() }}
                        @if($row['review']->proposed_deduction_amount)
                            · خصم مقترح: {{ number_format((float)$row['review']->proposed_deduction_amount, 2) }} ج.م
                        @endif
                    </div>
                    @if($row['review']->manager_notes)
                        <p class="text-sm text-slate-700 whitespace-pre-wrap mb-3">{{ $row['review']->manager_notes }}</p>
                    @endif
                @endif

                <form method="POST" action="{{ route('employee.sales-manager.scorecard.review', $employee) }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date->toDateString() }}">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">الحالة</label>
                        <select name="status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" @disabled($row['review']?->isApproved())>
                            @foreach(\App\Models\SalesManagerDailyReview::STATUSES as $val => $label)
                                <option value="{{ $val }}" @selected(old('status', $row['review']?->status ?? 'reviewed') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">التوصية</label>
                        <select name="recommendation" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" @disabled($row['review']?->isApproved())>
                            @foreach($recommendations as $val => $label)
                                <option value="{{ $val }}" @selected(old('recommendation', $row['review']?->recommendation ?? $row['suggested_recommendation']) === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">مبلغ خصم مقترح (اختياري)</label>
                        <input type="number" step="0.01" min="0" name="proposed_deduction_amount"
                               value="{{ old('proposed_deduction_amount', $row['review']?->proposed_deduction_amount ?? '') }}"
                               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" @disabled($row['review']?->isApproved())>
                        <p class="text-[10px] text-slate-400 mt-1">للتوثيق فقط — لا يُطبَّق على الراتب تلقائياً</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">ملاحظات المدير</label>
                        <textarea name="manager_notes" rows="4" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" @disabled($row['review']?->isApproved())>{{ old('manager_notes', $row['review']?->manager_notes ?? '') }}</textarea>
                    </div>
                    @unless($row['review']?->isApproved())
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold px-4 py-2.5 text-sm">
                            <i class="fas fa-check"></i> حفظ المراجعة
                        </button>
                    @endunless
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
