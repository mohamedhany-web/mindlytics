@extends('layouts.app')

@section('title', 'نظام الاتفاقيات - Mindlytics')
@section('header', 'نظام الاتفاقيات')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-sm font-semibold mb-2">إجمالي الأرباح</p>
                    <p class="text-3xl font-black">{{ number_format($stats['total_earned'], 2) }} ج.م</p>
                </div>
                <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm font-semibold mb-2">معلق</p>
                    <p class="text-3xl font-black">{{ number_format($stats['pending_amount'], 2) }} ج.م</p>
                </div>
                <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-semibold mb-2">إجمالي المدفوعات</p>
                    <p class="text-3xl font-black">{{ number_format($stats['total_payments']) }}</p>
                </div>
                <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-receipt text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Agreement Card -->
    @if($activeAgreement)
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl p-6 border-2 border-emerald-200 mb-8 shadow-lg">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <span class="bg-emerald-500 text-white px-4 py-1 rounded-full text-sm font-bold">نشط</span>
                    <h3 class="text-2xl font-black text-gray-900">{{ $activeAgreement->title }}</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600 font-semibold">رقم الاتفاقية</p>
                        <p class="text-gray-900 font-black text-lg">{{ $activeAgreement->agreement_number }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-semibold">النوع</p>
                        <p class="text-gray-900 font-black text-lg">
                            @if($activeAgreement->type == 'course_price')
                                سعر للكورس
                            @elseif($activeAgreement->type == 'hourly_rate')
                                سعر للساعة
                            @else
                                راتب شهري
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-semibold">المعدل</p>
                        <p class="text-gray-900 font-black text-lg">{{ number_format($activeAgreement->rate, 2) }} ج.م</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('instructor.agreements.show', $activeAgreement) }}" 
               class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-lg">
                <i class="fas fa-eye ml-2"></i>
                عرض التفاصيل
            </a>
        </div>
    </div>
    @endif

    <!-- Agreements List -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-2xl font-black text-gray-900 flex items-center gap-3">
                <i class="fas fa-handshake text-emerald-600"></i>
                جميع الاتفاقيات
            </h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-900">رقم الاتفاقية</th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-900">العنوان</th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-900">النوع</th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-900">المعدل</th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-900">الحالة</th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-900">تاريخ البدء</th>
                        <th class="px-6 py-4 text-center text-sm font-bold text-gray-900">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($agreements as $agreement)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-900">{{ $agreement->agreement_number }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $agreement->title }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                @if($agreement->type == 'course_price') bg-blue-100 text-blue-700
                                @elseif($agreement->type == 'hourly_rate') bg-purple-100 text-purple-700
                                @else bg-indigo-100 text-indigo-700
                                @endif">
                                @if($agreement->type == 'course_price')
                                    سعر للكورس
                                @elseif($agreement->type == 'hourly_rate')
                                    سعر للساعة
                                @else
                                    راتب شهري
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-900">{{ number_format($agreement->rate, 2) }} ج.م</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                @if($agreement->status == 'active') bg-emerald-100 text-emerald-700
                                @elseif($agreement->status == 'draft') bg-gray-100 text-gray-700
                                @elseif($agreement->status == 'suspended') bg-amber-100 text-amber-700
                                @elseif($agreement->status == 'terminated') bg-rose-100 text-rose-700
                                @else bg-blue-100 text-blue-700
                                @endif">
                                @if($agreement->status == 'active') نشط
                                @elseif($agreement->status == 'draft') مسودة
                                @elseif($agreement->status == 'suspended') معلق
                                @elseif($agreement->status == 'terminated') منتهي
                                @else مكتمل
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600">{{ $agreement->start_date->format('Y-m-d') }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('instructor.agreements.show', $agreement) }}" 
                               class="inline-flex items-center justify-center w-10 h-10 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-xl transition-colors"
                               title="عرض">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-handshake text-gray-400 text-2xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">لا توجد اتفاقيات</p>
                                    <p class="text-sm text-gray-600 mt-1">لم يتم إنشاء أي اتفاقيات بعد</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
