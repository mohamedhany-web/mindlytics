@extends('layouts.employee')

@section('title', 'التقارير اليومية للفريق')
@section('header', 'التقارير اليومية — أعضاء الفريق')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4 sm:p-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-file-lines text-teal-600"></i>
                التقارير اليومية
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">{{ $team->name }} — مراجعة تقارير الموظفين</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('employee.sales-manager.campaign-reports.index') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-bullhorn text-violet-600"></i> تقارير الكامبين
            </a>
            <a href="{{ route('employee.sales-manager.kpi.targets') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-bullseye text-emerald-600"></i> أهداف KPI
            </a>
            <a href="{{ route('employee.sales-manager.team-reports.index') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white px-3 py-2 text-sm font-semibold">
                <i class="fas fa-clipboard-check"></i> تقرير الفريق
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-2xl border border-slate-200 p-4">
            <p class="text-xs text-slate-500">مُسلَّمة (الفترة)</p>
            <p class="text-2xl font-black text-slate-900 tabular-nums">{{ $stats['submitted'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-amber-200 bg-amber-50/40 p-4">
            <p class="text-xs text-amber-800">بانتظار مراجعتك</p>
            <p class="text-2xl font-black text-amber-800 tabular-nums">{{ $stats['pending_review'] }}</p>
        </div>
        <a href="{{ route('employee.sales-manager.campaign-reports.index') }}" class="bg-white rounded-2xl border border-slate-200 p-4 hover:border-violet-300 transition-colors">
            <p class="text-xs text-slate-500">تقارير الكامبين</p>
            <p class="text-sm font-bold text-violet-700 mt-2">عرض وتحليل ←</p>
        </a>
        <a href="{{ route('employee.sales-manager.kpi.index') }}" class="bg-white rounded-2xl border border-slate-200 p-4 hover:border-teal-300 transition-colors">
            <p class="text-xs text-slate-500">مؤشرات الفريق</p>
            <p class="text-sm font-bold text-teal-700 mt-2">لوحة KPIs ←</p>
        </a>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 bg-white rounded-2xl border border-slate-200 p-4">
        <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="px-3 py-2 border border-slate-300 rounded-xl text-sm">
        <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="px-3 py-2 border border-slate-300 rounded-xl text-sm">
        <select name="status" class="px-3 py-2 border border-slate-300 rounded-xl text-sm">
            <option value="">كل الحالات</option>
            <option value="submitted" @selected(request('status')==='submitted')>مُسلَّم</option>
            <option value="draft" @selected(request('status')==='draft')>مسودة</option>
        </select>
        <button class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-sm font-semibold">تصفية</button>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-xs text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-right">الموظف</th>
                    <th class="px-4 py-3 text-right">التاريخ</th>
                    <th class="px-4 py-3 text-right">الحالة</th>
                    <th class="px-4 py-3 text-right">مراجع</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($reports as $r)
                    <tr class="hover:bg-slate-50 {{ ! $r->manager_reviewed_at && $r->status === 'submitted' ? 'bg-amber-50/40' : '' }}">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $r->user->name ?? '—' }}</td>
                        <td class="px-4 py-3 tabular-nums">{{ $r->report_date?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-lg px-2 py-0.5 text-[11px] font-bold {{ $r->status === 'submitted' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                {{ $r->status === 'submitted' ? 'مُسلَّم' : 'مسودة' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $r->manager_reviewed_at ? 'تمت المراجعة' : '—' }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('employee.sales-manager.daily-reports.show', $r) }}" class="text-teal-700 font-semibold hover:underline">عرض</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">لا توجد تقارير.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($reports->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $reports->links() }}</div>
        @endif
    </div>
</div>
@endsection
