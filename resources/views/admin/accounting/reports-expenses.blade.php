@extends('layouts.admin')
@section('title', 'المصروفات - التقارير المحاسبية')
@section('header', 'المصروفات')
@section('content')
<div class="w-full space-y-6">
    <nav class="text-sm text-slate-500 mb-2">
        <a href="{{ route('admin.accounting.reports') }}" class="text-sky-600 hover:text-sky-700">التقارير المحاسبية</a>
        <span class="mx-1">/</span>
        <span class="text-slate-700">المصروفات</span>
    </nav>
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
                <h2 class="text-xl font-bold text-slate-900">المصروفات في الفترة</h2>
                <a href="{{ route('admin.accounting.reports.export', array_merge(request()->query(), ['type' => 'expenses'])) }}" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-700">
                    <i class="fas fa-file-excel"></i> تصدير Excel
                </a>
            </div>
            <form method="GET" action="{{ route('admin.accounting.reports.expenses') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">الفترة</label>
                    <select name="period" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm" onchange="this.form.submit()">
                        <option value="day" {{ ($period ?? '') == 'day' ? 'selected' : '' }}>اليوم</option>
                        <option value="week" {{ ($period ?? '') == 'week' ? 'selected' : '' }}>هذا الأسبوع</option>
                        <option value="month" {{ ($period ?? '') == 'month' ? 'selected' : '' }}>هذا الشهر</option>
                        <option value="year" {{ ($period ?? '') == 'year' ? 'selected' : '' }}>هذه السنة</option>
                        <option value="all" {{ ($period ?? '') == 'all' ? 'selected' : '' }}>الكل</option>
                        <option value="custom" {{ ($period ?? '') == 'custom' ? 'selected' : '' }}>مخصص</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">من تاريخ</label>
                    <input type="date" name="start_date" value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">إلى تاريخ</label>
                    <input type="date" name="end_date" value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm" />
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700">
                        <i class="fas fa-filter"></i> تطبيق
                    </button>
                </div>
            </form>
            <p class="text-sm text-slate-500 mt-3">من {{ $startDate->format('Y-m-d') }} إلى {{ $endDate->format('Y-m-d') }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase text-slate-500">
                        <th class="px-4 py-3">رقم المصروف</th>
                        <th class="px-4 py-3">العنوان</th>
                        <th class="px-4 py-3">الفئة</th>
                        <th class="px-4 py-3">المبلغ</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($items as $expense)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3"><a href="{{ route('admin.expenses.show', $expense) }}" class="font-semibold text-sky-600 hover:text-sky-700">{{ $expense->expense_number ?? '—' }}</a></td>
                        <td class="px-4 py-3">{{ $expense->title ?? '—' }}</td>
                        <td class="px-4 py-3">{{ \App\Models\Expense::categoryLabel($expense->category ?? '') }}</td>
                        <td class="px-4 py-3 font-semibold text-rose-600">{{ number_format($expense->amount, 2) }} ج.م</td>
                        <td class="px-4 py-3">
                            @if($expense->status == 'approved')<span class="rounded-full px-2 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700">معتمد</span>
                            @elseif($expense->status == 'pending')<span class="rounded-full px-2 py-0.5 text-xs font-semibold bg-amber-100 text-amber-700">معلق</span>
                            @else<span class="rounded-full px-2 py-0.5 text-xs font-semibold bg-rose-100 text-rose-700">مرفوض</span>@endif
                        </td>
                        <td class="px-4 py-3">{{ $expense->expense_date ? $expense->expense_date->format('Y-m-d') : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">لا توجد مصروفات في هذه الفترة.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($items->hasPages())
        <div class="px-5 py-4 border-t border-slate-200">{{ $items->links() }}</div>
        @endif
    </section>
</div>
@endsection
