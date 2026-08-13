@extends('layouts.employee')

@section('title', 'التقرير اليومي')
@section('header', 'التقرير اليومي')

@push('styles')
@include('employee.sales._styles')
@endpush

@section('content')
@php
    $heroActions = '<a href="'.route('employee.sales.daily-reports.edit', ['date' => $date->toDateString()]).'" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-lg"><i class="fas fa-pen"></i> '.($report?->isSubmitted() ? 'عرض التقرير' : 'تعبئة / تعديل').'</a>';
@endphp
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-semibold">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="rounded-2xl bg-sky-50 border border-sky-200 text-sky-900 px-4 py-3 text-sm">{{ session('info') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl bg-rose-50 border-2 border-rose-200 text-rose-800 px-4 py-3 text-sm font-semibold">{{ session('error') }}</div>
    @endif
    @if($autoSynced ?? false)
        <div class="rounded-xl bg-sky-50 border border-sky-200 text-sky-900 px-4 py-3 text-sm">
            <p class="font-semibold"><i class="fas fa-magic ml-1"></i> تم تحديث مسودة اليوم تلقائياً من نشاطك</p>
            <p class="mt-1">راجع الأرقام ثم سلّم التقرير قبل {{ $settings['deadline_time'] ?? '23:59' }}.</p>
        </div>
    @endif

    @include('employee.sales._hero', [
        'heroTitle' => 'التقرير اليومي الإلزامي',
        'heroSubtitle' => 'يُعبَّأ تلقائياً من مكالماتك ومتابعاتك وواتساب — راجع ثم سلّم.',
        'heroIcon' => 'fa-clipboard-check',
        'backUrl' => route('employee.sales.dashboard'),
        'heroActions' => $heroActions,
    ])

    @if($date->isToday() && !($isWorkDayToday ?? true))
        <div class="rounded-2xl border-2 border-sky-200 bg-sky-50 px-5 py-4 text-sky-900 text-sm">
            <p class="font-bold"><i class="fas fa-umbrella-beach ml-1"></i> اليوم لا يُطلَب فيه تقرير يومي</p>
            <p class="mt-1">
                @if($isLeaveToday ?? false)
                    لديك إجازة معتمدة اليوم.
                @elseif($isWeeklyOffToday ?? false)
                    اليوم هو إجازتك الأسبوعية: <strong>{{ auth()->user()->weeklyOffDayLabel() }}</strong>.
                @else
                    هذا اليوم مستثنى من أيام العمل.
                @endif
            </p>
        </div>
    @elseif(($settings['enabled'] ?? true) && !$todaySubmitted && $date->isToday())
        <div class="rounded-2xl border-2 border-amber-300 bg-amber-50 px-5 py-4 text-amber-900 text-sm">
            <p class="font-bold"><i class="fas fa-clock ml-1"></i> لم يُسلَّم تقرير اليوم بعد</p>
            <p class="mt-1">إجازتك الأسبوعية: <strong>{{ auth()->user()->weeklyOffDayLabel() ?? 'عطلة نهاية الأسبوع' }}</strong> — آخر موعد: {{ $settings['deadline_time'] ?? '23:59' }} — عدم التسليم قد يُنشئ خصماً بقيمة {{ number_format($settings['penalty_amount'] ?? 50, 2) }} ج.م.</p>
        </div>
    @endif

    <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <form method="get" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">تاريخ</label>
                <input type="date" name="date" value="{{ $date->toDateString() }}" max="{{ today()->toDateString() }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold">عرض</button>
        </form>

        @if($report)
            <div class="mt-6 grid md:grid-cols-2 gap-6">
                <div>
                    <h2 class="font-bold text-gray-900 mb-3"><i class="fas fa-bolt text-slate-500 ml-1"></i> نشاط اليوم <span class="text-xs font-normal text-slate-500">(تلقائي)</span></h2>
                    <dl class="text-sm space-y-1">
                        <div class="flex justify-between"><dt>ردود رسائل</dt><dd class="font-bold">{{ $report->messages_replied ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt>مؤهلون</dt><dd class="font-bold">{{ $report->leads_qualified ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt>حجوزات من Leads</dt><dd class="font-bold">{{ $report->bookings_from_leads ?? '—' }}</dd></div>
                    </dl>
                    @if($report->activity_notes)<p class="mt-2 text-xs text-gray-600">{{ $report->activity_notes }}</p>@endif
                </div>
                <div>
                    <h2 class="font-bold text-gray-900 mb-3"><i class="fas fa-phone text-slate-500 ml-1"></i> الإنتاجية <span class="text-xs font-normal text-slate-500">(تلقائي)</span></h2>
                    <dl class="text-sm space-y-1">
                        <div class="flex justify-between"><dt>أشخاص تم العمل عليهم</dt><dd class="font-bold">{{ $report->numbers_worked ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt>متابعات</dt><dd class="font-bold">{{ $report->followups_done ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt>مكالمات / اجتماعات / ردود</dt><dd class="font-bold">{{ $report->calls_made ?? '—' }} / {{ $report->meetings_held ?? '—' }} / {{ $report->calls_answered ?? '—' }}</dd></div>
                    </dl>
                </div>
            </div>
            <p class="mt-4 text-xs">
                الحالة:
                @if($report->isSubmitted())
                    <span class="text-emerald-700 font-bold">مسلّم {{ $report->submitted_at?->format('Y-m-d H:i') }}</span>
                @else
                    <span class="text-amber-700 font-bold">مسودة — أكمل الحقول ثم سلّم</span>
                @endif
                @if($report->auto_deduction_id)
                    <span class="text-rose-700 font-bold mr-2">| تم تسجيل خصم تلقائي</span>
                @endif
            </p>
            @if(!$report->isSubmitted() && ($isWorkDayToday ?? true) && $date->isToday())
                <form method="post" action="{{ route('employee.sales.daily-reports.sync-auto') }}" class="mt-4">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date->toDateString() }}">
                    <button type="submit" class="text-sm text-slate-600 hover:text-slate-900 font-medium"><i class="fas fa-sync ml-1"></i> تحديث من النشاط الآن</button>
                </form>
            @endif
        @else
            <p class="mt-4 text-sm text-gray-500">لا يوجد تقرير لهذا التاريخ — ابدأ العمل على العملاء وسيُنشأ تلقائياً.</p>
        @endif
    </section>

    <section class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
        <h2 class="px-5 py-3 font-bold text-gray-900 border-b bg-gray-50 text-sm">آخر التقارير</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-2 text-right">التاريخ</th>
                        <th class="px-4 py-2 text-right">الحالة</th>
                        <th class="px-4 py-2 text-right">مكالمات</th>
                        <th class="px-4 py-2 text-right"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent as $r)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $r->report_date->format('Y-m-d') }}</td>
                            <td class="px-4 py-2">{{ $r->isSubmitted() ? 'مسلّم' : 'مسودة' }}</td>
                            <td class="px-4 py-2">{{ $r->calls_made ?? '—' }}</td>
                            <td class="px-4 py-2"><a href="{{ route('employee.sales.daily-reports.edit', ['date' => $r->report_date->toDateString()]) }}" class="text-emerald-700 font-semibold">فتح</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">لا توجد تقارير بعد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
