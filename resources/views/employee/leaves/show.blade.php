@extends('layouts.employee')

@section('title', 'تفاصيل طلب الإجازة')
@section('header', 'تفاصيل طلب الإجازة')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- معلومات الطلب -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">معلومات الطلب</h2>
            <span class="px-3 py-1 text-sm font-semibold rounded-full
                @if($leave->status === 'pending') bg-yellow-100 text-yellow-800
                @elseif($leave->status === 'approved') bg-green-100 text-green-800
                @elseif($leave->status === 'rejected') bg-red-100 text-red-800
                @else bg-gray-100 text-gray-800
                @endif">
                {{ $leave->status_label }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">نوع الإجازة</label>
                <p class="text-base font-semibold text-gray-900">{{ $leave->type_label }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">عدد الأيام</label>
                <p class="text-base font-semibold text-gray-900">{{ $leave->days }} يوم</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">من تاريخ</label>
                <p class="text-base font-semibold text-gray-900">{{ $leave->start_date->format('Y-m-d') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">إلى تاريخ</label>
                <p class="text-base font-semibold text-gray-900">{{ $leave->end_date->format('Y-m-d') }}</p>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">سبب الإجازة</label>
                <p class="text-base text-gray-900 whitespace-pre-wrap">{{ $leave->reason }}</p>
            </div>
        </div>
    </div>

    <!-- معلومات المراجعة -->
    @if($leave->status !== 'pending' && $leave->reviewer)
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">معلومات المراجعة</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">تمت المراجعة بواسطة</label>
                <p class="text-base font-semibold text-gray-900">{{ $leave->reviewer->name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">تاريخ المراجعة</label>
                <p class="text-base font-semibold text-gray-900">{{ $leave->reviewed_at->format('Y-m-d H:i') }}</p>
            </div>

            @if($leave->admin_notes)
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">ملاحظات الإدارة</label>
                <p class="text-base text-gray-900 whitespace-pre-wrap">{{ $leave->admin_notes }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- الأزرار -->
    <div class="flex items-center justify-end gap-4">
        <a href="{{ route('employee.leaves.index') }}" 
           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-right mr-2"></i>
            العودة للقائمة
        </a>
        
        @if($leave->status === 'pending')
            <form action="{{ route('employee.leaves.destroy', $leave) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الطلب؟');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    إلغاء الطلب
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
