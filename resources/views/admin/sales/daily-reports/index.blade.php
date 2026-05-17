@extends('layouts.admin')

@section('title', 'التقارير اليومية — المبيعات')
@section('header', 'التقارير اليومية — المبيعات')

@section('content')
<div class="p-4 md:p-6 space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 font-semibold text-sm">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-900">تقارير موظفي المبيعات اليومية</h2>
            <p class="text-sm text-slate-600 mt-1">نشاط + إنتاجية + مشاكل العملاء — تصدير Excel لتحليل الأنماط</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.sales.daily-reports.settings') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-sm font-semibold">إعدادات وخصم</a>
            <a href="{{ route('admin.sales.daily-reports.export', request()->query()) }}" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-bold"><i class="fas fa-file-excel ml-1"></i> Excel</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-xl bg-white border p-4"><p class="text-xs text-slate-500">إجمالي</p><p class="text-2xl font-black">{{ $stats['total'] }}</p></div>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4"><p class="text-xs text-emerald-800">مسلّمة</p><p class="text-2xl font-black text-emerald-900">{{ $stats['submitted'] }}</p></div>
        <div class="rounded-xl bg-rose-50 border border-rose-200 p-4"><p class="text-xs text-rose-800">بخصم تلقائي</p><p class="text-2xl font-black text-rose-900">{{ $stats['with_penalty'] }}</p></div>
    </div>

    <form method="get" class="bg-white rounded-2xl border p-4 flex flex-wrap gap-3 items-end">
        <div><label class="text-xs font-bold text-slate-600">من</label><input type="date" name="from" value="{{ $from->toDateString() }}" class="block rounded-lg border px-3 py-2 text-sm"></div>
        <div><label class="text-xs font-bold text-slate-600">إلى</label><input type="date" name="to" value="{{ $to->toDateString() }}" class="block rounded-lg border px-3 py-2 text-sm"></div>
        <div>
            <label class="text-xs font-bold text-slate-600">موظف</label>
            <select name="user_id" class="block rounded-lg border px-3 py-2 text-sm min-w-[180px]">
                <option value="">الكل</option>
                @foreach($reps as $rep)
                    <option value="{{ $rep->id }}" @selected($userId == $rep->id)>{{ $rep->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-bold text-slate-600">الحالة</label>
            <select name="status" class="block rounded-lg border px-3 py-2 text-sm">
                <option value="">الكل</option>
                <option value="submitted" @selected($status === 'submitted')>مسلّم</option>
                <option value="draft" @selected($status === 'draft')>مسودة</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold">تصفية</button>
    </form>

    <div class="bg-white rounded-2xl border overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-right">التاريخ</th>
                    <th class="px-4 py-3 text-right">الموظف</th>
                    <th class="px-4 py-3 text-right">الحالة</th>
                    <th class="px-4 py-3 text-right">رسائل</th>
                    <th class="px-4 py-3 text-right">مكالمات</th>
                    <th class="px-4 py-3 text-right">تواصل</th>
                    <th class="px-4 py-3 text-right"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $r)
                    <tr class="border-t hover:bg-slate-50">
                        <td class="px-4 py-2">{{ $r->report_date->format('Y-m-d') }}</td>
                        <td class="px-4 py-2 font-semibold">{{ $r->user->name ?? '—' }}</td>
                        <td class="px-4 py-2">
                            @if($r->isSubmitted())<span class="text-emerald-700 font-bold">مسلّم</span>
                            @else<span class="text-amber-700 font-bold">مسودة</span>@endif
                            @if($r->auto_deduction_id)<span class="text-rose-600 text-xs block">خصم</span>@endif
                        </td>
                        <td class="px-4 py-2">{{ $r->messages_replied ?? '—' }}</td>
                        <td class="px-4 py-2">{{ $r->calls_made ?? '—' }}</td>
                        <td class="px-4 py-2">{{ $r->contacts->count() }}</td>
                        <td class="px-4 py-2"><a href="{{ route('admin.sales.daily-reports.show', $r->id) }}" class="text-emerald-700 font-bold">تفاصيل</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">لا توجد تقارير في هذه الفترة</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
