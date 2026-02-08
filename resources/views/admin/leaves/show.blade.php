@extends('layouts.admin')

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
                <label class="block text-sm font-medium text-gray-500 mb-1">الموظف</label>
                <p class="text-base font-semibold text-gray-900">{{ $leave->employee->name }}</p>
                @if($leave->employee->employee_code)
                    <p class="text-sm text-gray-500 mt-1">الرمز: {{ $leave->employee->employee_code }}</p>
                @endif
                @if($leave->employee->employeeJob)
                    <p class="text-sm text-gray-500">الوظيفة: {{ $leave->employee->employeeJob->name }}</p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">نوع الإجازة</label>
                <p class="text-base font-semibold text-gray-900">{{ $leave->type_label }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">من تاريخ</label>
                <p class="text-base font-semibold text-gray-900">{{ $leave->start_date->format('Y-m-d') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">إلى تاريخ</label>
                <p class="text-base font-semibold text-gray-900">{{ $leave->end_date->format('Y-m-d') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">عدد الأيام</label>
                <p class="text-base font-semibold text-gray-900">{{ $leave->days }} يوم</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">تاريخ التقديم</label>
                <p class="text-base font-semibold text-gray-900">{{ $leave->created_at->format('Y-m-d H:i') }}</p>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">سبب الإجازة</label>
                <p class="text-base text-gray-900 whitespace-pre-wrap bg-gray-50 p-4 rounded-lg">{{ $leave->reason }}</p>
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
                <p class="text-base text-gray-900 whitespace-pre-wrap bg-gray-50 p-4 rounded-lg">{{ $leave->admin_notes }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- إجراءات المراجعة -->
    @if($leave->status === 'pending')
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">مراجعة الطلب</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- الموافقة -->
            <form action="{{ route('admin.leaves.approve', $leave) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <label for="approve_notes" class="block text-sm font-medium text-gray-700">ملاحظات (اختياري)</label>
                    <textarea name="admin_notes" id="approve_notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                              placeholder="ملاحظات حول الموافقة..."></textarea>
                    <button type="submit" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-check mr-2"></i>
                        الموافقة على الطلب
                    </button>
                </div>
            </form>

            <!-- الرفض -->
            <form action="{{ route('admin.leaves.reject', $leave) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <label for="reject_notes" class="block text-sm font-medium text-gray-700">سبب الرفض <span class="text-red-500">*</span></label>
                    <textarea name="admin_notes" id="reject_notes" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                              placeholder="اكتب سبب رفض الطلب..."></textarea>
                    <button type="submit" 
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        رفض الطلب
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- الأزرار -->
    <div class="flex items-center justify-end">
        <a href="{{ route('admin.leaves.index') }}" 
           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-right mr-2"></i>
            العودة للقائمة
        </a>
    </div>
</div>
@endsection
