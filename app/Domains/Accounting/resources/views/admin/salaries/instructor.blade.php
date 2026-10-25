@extends('layouts.admin')

@section('title', 'ماليات المدرب - ' . $instructor->name)
@section('header', 'ماليات المدرب - ' . $instructor->name)

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ route('admin.salaries.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium inline-flex items-center gap-1 mb-2">
                    <i class="fas fa-arrow-right"></i>
                    العودة إلى قائمة المدربين
                </a>
                <h1 class="text-2xl font-bold text-gray-900">{{ $instructor->name }}</h1>
                <p class="text-gray-600 mt-1">جميع المطلوب دفعه والمدفوع — يمكنك الدفع مسبقاً أو في أي وقت</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-amber-200">
            <p class="text-sm font-semibold text-gray-600 mb-1">مطلوب الدفع لهذا المدرب</p>
            <p class="text-3xl font-black text-amber-700">{{ number_format($pendingTotal, 2) }} ج.م</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-green-200">
            <p class="text-sm font-semibold text-gray-600 mb-1">تم الدفع لهذا المدرب</p>
            <p class="text-3xl font-black text-green-700">{{ number_format($paidTotal, 2) }} ج.م</p>
        </div>
    </div>

    {{-- جدول الاتفاقيات كاملة --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-slate-50">
            <h2 class="text-lg font-bold text-gray-900">جدول الاتفاقيات</h2>
            <p class="text-sm text-gray-600 mt-0.5">الاتفاقيات المرتبطة بهذا المدرب — يمكنك دفع المبلغ الآن من زر «دفع الآن»</p>
        </div>
        @if($agreements->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-100 text-right text-sm text-gray-700">
                    <tr>
                        <th class="px-6 py-3">رقم الاتفاقية</th>
                        <th class="px-6 py-3">العنوان</th>
                        <th class="px-6 py-3">النوع</th>
                        <th class="px-6 py-3">المبلغ/المعدل (ج.م)</th>
                        <th class="px-6 py-3">من</th>
                        <th class="px-6 py-3">إلى</th>
                        <th class="px-6 py-3">الحالة</th>
                        <th class="px-6 py-3">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($agreements as $agr)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-mono text-sm">{{ $agr->agreement_number ?? '—' }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $agr->title ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($agr->type === 'monthly_salary') راتب شهري
                            @elseif($agr->type === 'hourly_rate') سعر بالساعة
                            @elseif($agr->type === 'course_price') سعر للكورس
                            @else {{ $agr->type ?? '—' }}
                            @endif
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-900">{{ number_format((float)($agr->rate ?? 0), 2) }} ج.م</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $agr->start_date ? $agr->start_date->format('Y-m-d') : '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $agr->end_date ? $agr->end_date->format('Y-m-d') : '—' }}</td>
                        <td class="px-6 py-4">
                            @if($agr->status === 'active') <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">نشط</span>
                            @elseif($agr->status === 'draft') <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">مسودة</span>
                            @elseif($agr->status === 'completed') <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">مكتمل</span>
                            @else <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">{{ $agr->status ?? '—' }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if((float)($agr->rate ?? 0) > 0)
                            <form action="{{ route('admin.salaries.pay-now-from-agreement', [$instructor, $agr]) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-lg font-medium">
                                    <i class="fas fa-money-bill-wave"></i>
                                    دفع الآن
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-gray-400">لا يوجد مبلغ</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-8 text-center text-gray-500">
            <p class="font-medium">لا توجد اتفاقيات لهذا المدرب.</p>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-slate-50">
            <h2 class="text-lg font-bold text-gray-900">قائمة المدفوعات</h2>
            <p class="text-sm text-gray-600 mt-0.5">المطلوب دفعه والمدفوع — دفع وتحويل من الزر أدناه لأي مدفوعة مطلوب دفعها</p>
        </div>
        @if($payments->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-100 text-right text-sm text-gray-700">
                    <tr>
                        <th class="px-6 py-3">رقم المدفوعة</th>
                        <th class="px-6 py-3">الاتفاقية</th>
                        <th class="px-6 py-3">النوع</th>
                        <th class="px-6 py-3">المبلغ</th>
                        <th class="px-6 py-3">الحالة</th>
                        <th class="px-6 py-3">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($payments as $p)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-mono text-sm">{{ $p->payment_number }}</td>
                        <td class="px-6 py-4 text-sm">{{ $p->agreement->title ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $p->type_label }}</td>
                        <td class="px-6 py-4 font-bold text-gray-900">{{ number_format($p->amount, 2) }} ج.م</td>
                        <td class="px-6 py-4">
                            @if($p->status === 'pending')
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">قيد المراجعة</span>
                            @elseif($p->status === 'approved')
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">مطلوب الدفع</span>
                            @else
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">تم الدفع</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($p->status === 'pending')
                                <span class="text-xs text-slate-500">في انتظار الاعتماد</span>
                            @elseif($p->status === 'approved')
                                <a href="{{ route('admin.salaries.pay', $p) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg font-medium">
                                    <i class="fas fa-money-bill-wave"></i>
                                    دفع وتحويل
                                </a>
                            @else
                                @if($p->transfer_receipt_path)
                                    <a href="{{ asset('storage/' . $p->transfer_receipt_path) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm rounded-lg font-medium">
                                        <i class="fas fa-receipt"></i>
                                        إيصال
                                    </a>
                                @else
                                    —
                                @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center text-gray-500">
            <i class="fas fa-money-check-alt text-4xl text-gray-300 mb-4"></i>
            <p class="font-medium">لا توجد مدفوعات مسجّلة لهذا المدرب.</p>
            <p class="text-sm mt-1">يمكن إنشاء مدفوعات من الاتفاقيات من إدارة الاتفاقيات.</p>
        </div>
        @endif
    </div>
</div>
@endsection
