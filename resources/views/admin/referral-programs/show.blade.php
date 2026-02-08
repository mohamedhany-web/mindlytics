@extends('layouts.admin')

@section('title', 'تفاصيل برنامج الإحالات - Mindlytics')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-gift text-sky-600 ml-3"></i>
                        {{ $referralProgram->name }}
                    </h1>
                    <p class="text-gray-600">{{ $referralProgram->description }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.referral-programs.edit', $referralProgram) }}" 
                       class="bg-gradient-to-l from-amber-600 to-amber-500 hover:from-amber-700 hover:to-amber-600 text-white px-5 py-2.5 rounded-lg font-medium transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                        <i class="fas fa-edit"></i>
                        تعديل
                    </a>
                    <a href="{{ route('admin.referral-programs.index') }}" 
                       class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2.5 rounded-lg font-medium transition-colors">
                        <i class="fas fa-arrow-right ml-2"></i>
                        العودة
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 border-r-4 border-sky-500">
                <p class="text-gray-500 text-sm font-medium mb-1">إجمالي الإحالات</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_referrals']) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 border-r-4 border-emerald-500">
                <p class="text-gray-500 text-sm font-medium mb-1">مكتملة</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['completed_referrals']) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 border-r-4 border-amber-500">
                <p class="text-gray-500 text-sm font-medium mb-1">قيد الانتظار</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['pending_referrals']) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 border-r-4 border-purple-500">
                <p class="text-gray-500 text-sm font-medium mb-1">إجمالي الخصومات</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_discount_given'], 2) }} ج.م</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 border-r-4 border-pink-500">
                <p class="text-gray-500 text-sm font-medium mb-1">إجمالي المكافآت</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_rewards_given'], 2) }} ج.م</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Program Details -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="fas fa-info-circle text-sky-600"></i>
                    تفاصيل البرنامج
                </h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">الحالة</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($referralProgram->is_active && $referralProgram->isValid()) bg-emerald-100 text-emerald-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            @if($referralProgram->is_active && $referralProgram->isValid()) نشط
                            @else معطل
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">نوع الخصم للمحال</span>
                        <span class="font-medium text-gray-900">{{ $referralProgram->discount_type == 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">قيمة الخصم</span>
                        <span class="font-bold text-gray-900">
                            @if($referralProgram->discount_type == 'percentage')
                                {{ number_format($referralProgram->discount_value, 0) }}%
                            @else
                                {{ number_format($referralProgram->discount_value, 2) }} ج.م
                            @endif
                        </span>
                    </div>
                    @if($referralProgram->maximum_discount)
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">الحد الأقصى للخصم</span>
                        <span class="font-medium text-gray-900">{{ number_format($referralProgram->maximum_discount, 2) }} ج.م</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">مدة صلاحية الخصم</span>
                        <span class="font-medium text-gray-900">{{ $referralProgram->discount_valid_days }} يوم</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">الحد الأقصى لاستخدام الخصم</span>
                        <span class="font-medium text-gray-900">{{ $referralProgram->max_discount_uses_per_referred }} مرة</span>
                    </div>
                    @if($referralProgram->referrer_reward_value)
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">مكافأة المحيل</span>
                        <span class="font-bold text-emerald-600">
                            @if($referralProgram->referrer_reward_type == 'percentage')
                                {{ number_format($referralProgram->referrer_reward_value, 0) }}%
                            @elseif($referralProgram->referrer_reward_type == 'points')
                                {{ number_format($referralProgram->referrer_reward_value, 0) }} نقطة
                            @else
                                {{ number_format($referralProgram->referrer_reward_value, 2) }} ج.م
                            @endif
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Referrals List -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="fas fa-list text-sky-600"></i>
                    آخر الإحالات
                </h2>
                
                @if($referralProgram->referrals->count() > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($referralProgram->referrals()->latest()->take(10)->get() as $referral)
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-medium text-gray-900">{{ $referral->referred->name ?? 'غير معروف' }}</p>
                                <p class="text-sm text-gray-500">محال من: {{ $referral->referrer->name ?? 'غير معروف' }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @if($referral->status == 'completed') bg-emerald-100 text-emerald-800
                                @else bg-amber-100 text-amber-800
                                @endif">
                                {{ $referral->status == 'completed' ? 'مكتملة' : 'قيد الانتظار' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">الخصم: {{ number_format($referral->discount_amount ?? 0, 2) }} ج.م</span>
                            <span class="text-gray-600">{{ $referral->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.referrals.index', ['program_id' => $referralProgram->id]) }}" 
                       class="text-sky-600 hover:text-sky-800 font-medium">
                        عرض جميع الإحالات <i class="fas fa-arrow-left ml-1"></i>
                    </a>
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-user-friends text-gray-400 text-4xl mb-3"></i>
                    <p class="text-gray-500">لا توجد إحالات لهذا البرنامج</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
