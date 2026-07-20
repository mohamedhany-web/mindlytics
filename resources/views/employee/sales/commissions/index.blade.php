@extends('layouts.employee')

@section('title', 'عمولات المبيعات')
@section('header', 'عمولات المبيعات')

@push('styles')
@include('employee.sales._styles')
@endpush

@section('content')
<div class="space-y-6">
    @include('employee.sales._hero', [
        'heroTitle' => 'عمولات المبيعات',
        'heroSubtitle' => 'ملخص العمولات المعتمدة والمعلّقة — '.$periodLabel,
        'heroIcon' => 'fa-coins',
        'backUrl' => route('employee.sales.dashboard'),
    ])

    <section class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 bg-gradient-to-l from-amber-50 to-white border-b border-amber-100">
            <h2 class="text-lg font-bold text-gray-900">ملخص العمولات — {{ $periodLabel }}</h2>
            <p class="text-xs text-gray-600 mt-1">المعتمد = بعد اعتماد الإدارة للفوز · المعلّق = won بانتظار الاعتماد</p>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 p-4">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-xs text-emerald-700 font-semibold">عمولة معتمدة</p>
                <p class="text-xl font-black text-emerald-900 tabular-nums">{{ number_format($commissionFromLeads, 2) }} ج.م</p>
            </div>
            <div class="rounded-xl border border-sky-200 bg-sky-50 p-4">
                <p class="text-xs text-sky-700 font-semibold">صفقات معتمدة</p>
                <p class="text-xl font-black text-sky-900">{{ $confirmedWins }}</p>
            </div>
            <div class="rounded-xl border border-violet-200 bg-violet-50 p-4">
                <p class="text-xs text-violet-700 font-semibold">قيمة الصفقات</p>
                <p class="text-xl font-black text-violet-900 tabular-nums">{{ number_format($expectedSum, 2) }} ج.م</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs text-amber-700 font-semibold">معلّق (تقدير)</p>
                <p class="text-xl font-black text-amber-900 tabular-nums">{{ number_format($pendingEst, 2) }} ج.م</p>
                <p class="text-[11px] text-amber-700">{{ $pendingLeads->count() }} صفقة</p>
            </div>
        </div>
    </section>

    <form method="get" class="flex flex-wrap gap-3 items-end bg-white p-4 rounded-xl border border-gray-200">
        <div>
            <label class="block text-xs text-gray-500 mb-1">العرض</label>
            <select name="view" class="border rounded-lg px-3 py-2 text-sm">
                <option value="month" @selected($view === 'month')>شهر محدد</option>
                <option value="all" @selected($view === 'all')>كل الفترات</option>
            </select>
        </div>
        @if($view === 'month')
        <div>
            <label class="block text-xs text-gray-500 mb-1">الشهر</label>
            <input type="month" name="year_month" value="{{ $yearMonth }}" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        @endif
        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm">تطبيق</button>
    </form>

    @if(($agreements ?? collect())->isNotEmpty())
    <section class="rounded-xl bg-white border border-violet-200 overflow-hidden">
        <div class="px-4 py-3 bg-violet-50 border-b border-violet-200">
            <h3 class="font-bold text-violet-900">اتفاقيات الكوميشن حسب الكورس</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="text-right py-2 px-4">النوع</th>
                    <th class="text-right py-2 px-4">الكورس</th>
                    <th class="text-right py-2 px-4">وضع الحساب</th>
                    <th class="text-right py-2 px-4">السعر</th>
                </tr></thead>
                <tbody class="divide-y">
                    @foreach($agreements as $agr)
                    <tr>
                        <td class="py-2 px-4">{{ $agr->courseTypeLabel() }}</td>
                        <td class="py-2 px-4 font-medium">{{ $agr->courseTitle() }}</td>
                        <td class="py-2 px-4 text-xs">{{ $agr->calcModeLabel() }}</td>
                        <td class="py-2 px-4">{{ $agr->coursePrice() !== null ? number_format($agr->coursePrice(), 2).' ج.م' : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    @if($pendingLeads->isNotEmpty())
    <section class="rounded-xl bg-white border border-amber-200 overflow-hidden">
        <div class="px-4 py-3 bg-amber-50 border-b border-amber-200">
            <h3 class="font-bold text-amber-900"><i class="fas fa-clock ml-1"></i> صفقات won بانتظار اعتماد الإدارة</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="text-right py-2 px-4">العميل</th>
                    <th class="text-right py-2 px-4">القيمة</th>
                    <th class="text-right py-2 px-4">تقدير العمولة</th>
                    <th class="text-right py-2 px-4"></th>
                </tr></thead>
                <tbody class="divide-y">
                    @foreach($pendingLeads as $pl)
                    <tr>
                        <td class="py-2 px-4 font-medium">{{ $pl->name }}</td>
                        <td class="py-2 px-4">{{ number_format((float) ($pl->expected_value ?? 0), 2) }} ج.م</td>
                        <td class="py-2 px-4 text-amber-700 font-semibold">{{ number_format($pendingEstimates[$pl->id] ?? 0, 2) }} ج.م</td>
                        <td class="py-2 px-4"><a href="{{ route('employee.sales.leads.show', $pl) }}" class="text-emerald-600 hover:underline">عرض</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    @if($confirmedLeads->isNotEmpty())
    <section class="rounded-xl bg-white border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 bg-gray-50 border-b">
            <h3 class="font-bold text-gray-900">صفقات معتمدة</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="text-right py-2 px-4">العميل</th>
                    <th class="text-right py-2 px-4">الكورس</th>
                    <th class="text-right py-2 px-4">التصنيف</th>
                    <th class="text-right py-2 px-4">القيمة</th>
                    <th class="text-right py-2 px-4">العمولة</th>
                    <th class="text-right py-2 px-4">تاريخ الاعتماد</th>
                </tr></thead>
                <tbody class="divide-y">
                    @foreach($confirmedLeads as $cl)
                    <tr>
                        <td class="py-2 px-4"><a href="{{ route('employee.sales.leads.show', $cl) }}" class="font-medium text-emerald-700 hover:underline">{{ $cl->name }}</a></td>
                        <td class="py-2 px-4 text-xs">{{ $cl->linkedCourseTitle() ?? '—' }}</td>
                        <td class="py-2 px-4">{{ $cl->category?->name ?? '—' }}</td>
                        <td class="py-2 px-4">{{ number_format((float) ($cl->expected_value ?? 0), 2) }} ج.م</td>
                        <td class="py-2 px-4 font-semibold text-emerald-700">{{ number_format((float) ($cl->commission_amount ?? 0), 2) }} ج.م</td>
                        <td class="py-2 px-4 text-xs text-gray-600">{{ $cl->won_confirmed_at?->format('Y-m-d') ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif
</div>
@endsection
