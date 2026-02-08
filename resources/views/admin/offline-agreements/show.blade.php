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
                    <p class="text-sm text-gray-600 mb-1">المدرب</p>
                    <p class="font-semibold text-gray-900 text-lg">{{ $agreement->instructor->name }}</p>
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
                <div>
                    <p class="text-sm text-gray-600 mb-1">الراتب لكل جلسة</p>
                    <p class="font-semibold text-gray-900 text-lg">{{ number_format($agreement->salary_per_session, 2) }} ر.س</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">عدد الجلسات</p>
                    <p class="font-semibold text-gray-900 text-lg">{{ $agreement->sessions_count }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">المبلغ الإجمالي</p>
                    <p class="font-semibold text-gray-900 text-2xl text-blue-600">{{ number_format($agreement->total_amount, 2) }} ر.س</p>
                </div>
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
        </div>
    </div>
</div>
@endsection
