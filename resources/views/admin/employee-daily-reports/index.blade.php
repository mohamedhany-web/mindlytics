@extends('layouts.admin')

@section('title', 'التقارير اليومية للموظفين')
@section('header', 'رقابة التقارير اليومية')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">{{ session('success') }}</div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center text-white"><i class="fas fa-clipboard-check"></i></div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">رقابة التقارير اليومية</h2>
                    <p class="text-xs text-slate-600">متابعة التزام الموظفين — غرامة تلقائية عند عدم الإرسال</p>
                </div>
            </div>
            <a href="{{ route('admin.employee-daily-reports.settings') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-sm font-semibold hover:bg-white">الإعدادات</a>
        </div>
        <div class="grid grid-cols-3 gap-3 p-4">
            <div class="rounded-xl border p-4"><p class="text-xs text-slate-600">مُرسل اليوم</p><p class="text-2xl font-black">{{ $stats['total_today'] }}</p></div>
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4"><p class="text-xs text-rose-700">لم يُرسلوا اليوم</p><p class="text-2xl font-black text-rose-800">{{ $stats['missing_today'] }}</p></div>
            <div class="rounded-xl border p-4"><p class="text-xs text-slate-600">موظفون نشطون</p><p class="text-2xl font-black">{{ $stats['employees'] }}</p></div>
        </div>
    </section>

    @if(count($missingToday) > 0)
    <section class="rounded-xl border border-rose-200 bg-rose-50 p-4">
        <p class="font-bold text-rose-900 mb-2"><i class="fas fa-exclamation-triangle ml-1"></i> لم يُرسلوا تقرير اليوم:</p>
        <div class="flex flex-wrap gap-2">
            @foreach($missingToday as $emp)
                <span class="px-3 py-1 rounded-lg bg-white border border-rose-200 text-sm font-semibold text-rose-800">{{ $emp->name }}</span>
            @endforeach
        </div>
    </section>
    @endif

    <section class="rounded-2xl bg-white border shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b bg-slate-50 font-bold">نسب الالتزام — الشهر الحالي</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50"><tr>
                    <th class="text-right px-4 py-2">الموظف</th>
                    <th class="text-center px-4 py-2">مطلوب</th>
                    <th class="text-center px-4 py-2">مُرسل</th>
                    <th class="text-center px-4 py-2">%</th>
                </tr></thead>
                <tbody class="divide-y">
                    @foreach($complianceRows as $row)
                    <tr>
                        <td class="px-4 py-2 font-semibold">{{ $row['employee']->name }}</td>
                        <td class="px-4 py-2 text-center">{{ $row['required'] }}</td>
                        <td class="px-4 py-2 text-center">{{ $row['submitted'] }}</td>
                        <td class="px-4 py-2 text-center font-bold {{ ($row['rate'] ?? 100) < 80 ? 'text-rose-600' : 'text-emerald-600' }}">{{ $row['rate'] !== null ? $row['rate'].'%' : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="rounded-2xl bg-white border shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b bg-slate-50 font-bold">التقارير</div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="text-right px-4 py-2">الموظف</th>
                <th class="text-right px-4 py-2">التاريخ</th>
                <th class="text-right px-4 py-2">الحالة</th>
                <th class="text-right px-4 py-2"></th>
            </tr></thead>
            <tbody class="divide-y">
                @foreach($reports as $r)
                <tr>
                    <td class="px-4 py-2">{{ $r->user->name ?? '—' }}</td>
                    <td class="px-4 py-2">{{ $r->report_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-2">{{ $r->isSubmitted() ? 'مُرسل' : 'مسودة' }}</td>
                    <td class="px-4 py-2"><a href="{{ route('admin.employee-daily-reports.show', $r->id) }}" class="text-sky-600 font-semibold">عرض</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $reports->links() }}</div>
    </section>
</div>
@endsection
