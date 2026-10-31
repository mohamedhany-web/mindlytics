@extends('layouts.admin')

@section('title', 'حساب المدرب - ' . $instructor->name)
@section('header', 'حساب المدرب - ' . $instructor->name)

@section('content')
<div class="p-4 md:p-6 space-y-6" style="background: #f8fafc;">
    {{-- رأس الصفحة --}}
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.accounting.instructor-accounts.index') }}" class="text-slate-600 hover:text-slate-900 text-sm font-medium inline-flex items-center gap-1 mb-2">
                    <i class="fas fa-arrow-right"></i>
                    العودة إلى حسابات المدربين
                </a>
                <h1 class="text-2xl font-bold text-slate-900">{{ $instructor->name }}</h1>
                <p class="text-slate-600 mt-1">{{ $instructor->email ?? '' }} @if($instructor->phone) — {{ $instructor->phone }} @endif</p>
            </div>
            <a href="{{ route('admin.salaries.instructor', $instructor) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium">
                <i class="fas fa-money-bill-wave"></i>
                الماليات والدفع
            </a>
        </div>
    </div>

    {{-- ملخص الأرقام --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow p-6 border-2 border-amber-200">
            <p class="text-sm font-semibold text-slate-600 mb-1">مطلوب الدفع</p>
            <p class="text-2xl font-black text-amber-700">{{ number_format($totals['pending'], 2) }} ج.م</p>
        </div>
        <div class="bg-white rounded-2xl shadow p-6 border-2 border-emerald-200">
            <p class="text-sm font-semibold text-slate-600 mb-1">تم الدفع (إجمالي)</p>
            <p class="text-2xl font-black text-emerald-700">{{ number_format($totals['paid'], 2) }} ج.م</p>
        </div>
        <div class="bg-white rounded-2xl shadow p-6 border-2 border-blue-200">
            <p class="text-sm font-semibold text-slate-600 mb-1">من تفعيلات الطلاب (نسبة الكورس)</p>
            <p class="text-2xl font-black text-blue-700">{{ number_format($totals['from_activations'], 2) }} ج.م</p>
        </div>
    </div>

    {{-- الاتفاقيات --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h2 class="text-lg font-bold text-slate-900">الاتفاقيات</h2>
            <p class="text-sm text-slate-600 mt-0.5">جميع اتفاقيات هذا المدرب (بما فيها نسبة من الكورس)</p>
        </div>
        @if($agreements->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-slate-100 text-sm text-slate-700">
                    <tr>
                        <th class="px-6 py-3">رقم الاتفاقية</th>
                        <th class="px-6 py-3">العنوان</th>
                        <th class="px-6 py-3">النوع</th>
                        <th class="px-6 py-3">المبلغ / النسبة</th>
                        <th class="px-6 py-3">من - إلى</th>
                        <th class="px-6 py-3">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($agreements as $agr)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-mono text-sm">{{ $agr->agreement_number ?? '—' }}</td>
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $agr->title ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if(($agr->billing_type ?? '') === 'course_percentage')
                                <span class="inline-flex px-2 py-1 rounded-lg text-xs font-semibold bg-blue-100 text-blue-800">نسبة من الكورس</span>
                                @if($agr->advancedCourse)<span class="block text-xs text-slate-500 mt-1">{{ $agr->advancedCourse->title }}</span>@endif
                            @else
                                {{ $agr->type_label ?? $agr->type }}
                            @endif
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-900">
                            @if(($agr->billing_type ?? '') === 'course_percentage')
                                {{ number_format($agr->course_percentage ?? 0, 2) }}%
                            @else
                                {{ number_format((float)($agr->rate ?? 0), 2) }} ج.م
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $agr->start_date ? $agr->start_date->format('Y-m-d') : '—' }}
                            @if($agr->end_date) — {{ $agr->end_date->format('Y-m-d') }} @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($agr->status === 'active') <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">نشط</span>
                            @elseif($agr->status === 'draft') <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">مسودة</span>
                            @elseif($agr->status === 'completed') <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">مكتمل</span>
                            @else <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">{{ $agr->status ?? '—' }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-8 text-center text-slate-500">لا توجد اتفاقيات لهذا المدرب.</div>
        @endif
    </div>

    {{-- تفعيلات الطلاب (نسبة من الكورس) — لكل تفعيل: الطالب، مبلغ الشراء، نسبة المدرب، حصته --}}
    @if($activationPayments->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 bg-blue-50">
            <h2 class="text-lg font-bold text-slate-900">تفعيلات الطلاب (نسبة من الكورس)</h2>
            <p class="text-sm text-slate-600 mt-0.5">كل تفعيل لطالب في كورس مربوط باتفاقية نسبة — نسبة المدرب وحصته من مبلغ الشراء</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-slate-100 text-sm text-slate-700">
                    <tr>
                        <th class="px-6 py-3">التاريخ</th>
                        <th class="px-6 py-3">الطالب</th>
                        <th class="px-6 py-3">الكورس</th>
                        <th class="px-6 py-3">مبلغ الشراء (ج.م)</th>
                        <th class="px-6 py-3">نسبة المدرب</th>
                        <th class="px-6 py-3">حصة المدرب (ج.م)</th>
                        <th class="px-6 py-3">حالة الدفع</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($activationPayments as $p)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-sm">{{ $p->created_at?->format('Y-m-d') ?? '—' }}</td>
                        <td class="px-6 py-4 font-medium">{{ $p->enrollment?->student?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $p->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $p->enrollment ? number_format($p->enrollment->final_price ?? 0, 2) : '—' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($p->agreement)
                                {{ number_format($p->agreement->course_percentage ?? 0, 2) }}%
                            @else — @endif
                        </td>
                        <td class="px-6 py-4 font-bold text-blue-700">{{ number_format($p->amount, 2) }} ج.م</td>
                        <td class="px-6 py-4">
                            @if($p->status === 'paid') <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">مدفوع</span>
                            @elseif($p->status === 'approved') <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">موافق عليه</span>
                            @else <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">قيد المراجعة</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 text-left">
            <span class="font-bold text-slate-900">إجمالي أرباح التفعيلات: {{ number_format($activationPayments->sum('amount'), 2) }} ج.م</span>
        </div>
    </div>
    @endif

    {{-- قائمة المدفوعات الكاملة --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h2 class="text-lg font-bold text-slate-900">سجل المدفوعات</h2>
            <p class="text-sm text-slate-600 mt-0.5">جميع المدافيع (قيد المراجعة، موافق عليه، مدفوع)</p>
        </div>
        @if($payments->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-slate-100 text-sm text-slate-700">
                    <tr>
                        <th class="px-6 py-3">رقم المدفوعة</th>
                        <th class="px-6 py-3">الاتفاقية / الوصف</th>
                        <th class="px-6 py-3">النوع</th>
                        <th class="px-6 py-3">المبلغ</th>
                        <th class="px-6 py-3">الحالة</th>
                        <th class="px-6 py-3">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($payments as $p)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-mono text-sm">{{ $p->payment_number }}</td>
                        <td class="px-6 py-4 text-sm">{{ $p->agreement->title ?? $p->description ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $p->type_label ?? $p->type }}</td>
                        <td class="px-6 py-4 font-bold text-slate-900">{{ number_format($p->amount, 2) }} ج.م</td>
                        <td class="px-6 py-4">
                            @if($p->status === 'pending') <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">قيد المراجعة</span>
                            @elseif($p->status === 'approved') <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">موافق عليه</span>
                            @else <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">مدفوع</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($p->status === 'approved')
                                <a href="{{ route('admin.salaries.pay', $p) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-lg font-medium">دفع وتحويل</a>
                            @elseif($p->status === 'paid' && $p->transfer_receipt_path)
                                <a href="{{ asset('storage/' . $p->transfer_receipt_path) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm rounded-lg font-medium">إيصال</a>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center text-slate-500">لا توجد مدفوعات مسجّلة.</div>
        @endif
    </div>
</div>
@endsection
