@extends('layouts.admin')

@section('title', 'تفاصيل الاتفاقية')
@section('header', 'تفاصيل الاتفاقية')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $agreement->title }}</h1>
                <p class="text-gray-600 mt-1">عرض تفاصيل الاتفاقية</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.offline-agreements.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-right mr-2"></i>العودة
                </a>
                <a href="{{ route('admin.offline-agreements.edit', $agreement) }}" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-edit mr-2"></i>تعديل
                </a>
            </div>
        </div>
    </div>

    <!-- معلومات الاتفاقية -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">معلومات الاتفاقية</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-600 mb-1">رقم الاتفاقية</p>
                    <p class="font-semibold text-gray-900 text-lg">{{ $agreement->agreement_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">نوع الاتفاقية</p>
                    <p class="font-semibold text-gray-900 text-lg">{{ $agreement->billing_type_label ?? 'بالجلسة' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">المدرب</p>
                    <p class="font-semibold text-gray-900 text-lg">{{ $agreement->instructor?->name ?? '—' }}</p>
                </div>
                @if($agreement->course)
                <div>
                    <p class="text-sm text-gray-600 mb-1">الكورس الأوفلاين</p>
                    <p class="font-semibold text-gray-900 text-lg">{{ $agreement->course->title }}</p>
                </div>
                @endif
                <div>
                    <p class="text-sm text-gray-600 mb-1">تاريخ البدء</p>
                    <p class="font-semibold text-gray-900 text-lg">{{ $agreement->start_date->format('Y-m-d') }}</p>
                </div>
                @if($agreement->end_date)
                <div>
                    <p class="text-sm text-gray-600 mb-1">تاريخ الانتهاء</p>
                    <p class="font-semibold text-gray-900 text-lg">{{ $agreement->end_date->format('Y-m-d') }}</p>
                </div>
                @endif
                @if(($agreement->billing_type ?? 'per_session') === 'per_session')
                <div>
                    <p class="text-sm text-gray-600 mb-1">الراتب لكل جلسة</p>
                    <p class="font-semibold text-gray-900 text-lg">{{ number_format($agreement->salary_per_session ?? 0, 2) }} ج.م</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">عدد الجلسات</p>
                    <p class="font-semibold text-gray-900 text-lg">{{ $agreement->sessions_count ?? 0 }}</p>
                </div>
                @elseif(($agreement->billing_type ?? '') === 'monthly')
                <div>
                    <p class="text-sm text-gray-600 mb-1">الراتب الشهري</p>
                    <p class="font-semibold text-gray-900 text-lg">{{ number_format($agreement->monthly_amount ?? 0, 2) }} ج.م</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">عدد الأشهر</p>
                    <p class="font-semibold text-gray-900 text-lg">{{ $agreement->months_count ?? 0 }}</p>
                </div>
                @elseif(($agreement->billing_type ?? '') === 'course_percentage')
                <div>
                    <p class="text-sm text-gray-600 mb-1">الكورس الأونلاين</p>
                    <p class="font-semibold text-gray-900 text-lg">{{ $agreement->advancedCourse?->title ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">نسبة المدرب</p>
                    <p class="font-semibold text-gray-900 text-lg">{{ number_format($agreement->course_percentage ?? 0, 2) }}%</p>
                </div>
                @endif
                @if(($agreement->billing_type ?? '') !== 'course_percentage')
                <div>
                    <p class="text-sm text-gray-600 mb-1">المبلغ الإجمالي</p>
                    <p class="font-semibold text-gray-900 text-2xl text-blue-600">{{ number_format($agreement->total_amount ?? 0, 2) }} ج.م</p>
                </div>
                @else
                <div>
                    <p class="text-sm text-gray-600 mb-1">إجمالي أرباح التفعيلات</p>
                    <p class="font-semibold text-gray-900 text-2xl text-blue-600">{{ number_format($agreement->payments->where('type', 'course_activation')->sum('amount'), 2) }} ج.م</p>
                </div>
                @endif
                <div>
                    <p class="text-sm text-gray-600 mb-1">حالة الدفع</p>
                    @php
                        $paymentColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'partial' => 'bg-blue-100 text-blue-800',
                            'paid' => 'bg-green-100 text-green-800',
                            'overdue' => 'bg-red-100 text-red-800',
                        ];
                        $paymentTexts = [
                            'pending' => 'معلق',
                            'partial' => 'جزئي',
                            'paid' => 'مدفوع',
                            'overdue' => 'متأخر',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $paymentColors[$agreement->payment_status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $paymentTexts[$agreement->payment_status] ?? $agreement->payment_status }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">الحالة</p>
                    @php
                        $statusColors = [
                            'draft' => 'bg-gray-100 text-gray-800',
                            'active' => 'bg-green-100 text-green-800',
                            'completed' => 'bg-blue-100 text-blue-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $statusTexts = [
                            'draft' => 'مسودة',
                            'active' => 'نشط',
                            'completed' => 'مكتمل',
                            'cancelled' => 'ملغي',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $statusColors[$agreement->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $statusTexts[$agreement->status] ?? $agreement->status }}
                    </span>
                </div>
            </div>
            @if($agreement->description)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-600 mb-2">الوصف</p>
                <p class="text-gray-900 leading-relaxed">{{ $agreement->description }}</p>
            </div>
            @endif
            @if($agreement->terms)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-600 mb-2">شروط الاتفاقية</p>
                <p class="text-gray-900 whitespace-pre-line leading-relaxed">{{ $agreement->terms }}</p>
            </div>
            @endif
            @if($agreement->notes)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-600 mb-2">ملاحظات</p>
                <p class="text-gray-900 leading-relaxed">{{ $agreement->notes }}</p>
            </div>
            @endif

            @if(($agreement->billing_type ?? '') === 'course_percentage' && $agreement->payments->where('type', 'course_activation')->isNotEmpty())
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">تفعيلات الطلاب (نسبة المدرب)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr class="text-xs font-semibold text-gray-700 uppercase">
                                <th class="px-4 py-3 text-right">التاريخ</th>
                                <th class="px-4 py-3 text-right">الطالب</th>
                                <th class="px-4 py-3 text-right">مبلغ التفعيل (ج.م)</th>
                                <th class="px-4 py-3 text-right">نسبة المدرب (ج.م)</th>
                                <th class="px-4 py-3 text-right">حالة الدفع</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($agreement->payments->where('type', 'course_activation') as $p)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $p->created_at?->format('Y-m-d') ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $p->enrollment?->student?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $p->enrollment ? number_format($p->enrollment->final_price ?? 0, 2) : '—' }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-blue-600">{{ number_format($p->amount, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($p->status === 'paid') bg-green-100 text-green-800
                                        @elseif($p->status === 'approved') bg-amber-100 text-amber-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        @if($p->status === 'paid') مدفوع
                                        @elseif($p->status === 'approved') موافق عليه
                                        @else قيد المراجعة
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
