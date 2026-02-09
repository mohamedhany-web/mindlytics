@extends('layouts.admin')

@section('title', 'تفاصيل طلب المدرب - Mindlytics')
@section('header', 'تفاصيل طلب المدرب')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4">
        <a href="{{ route('admin.instructor-requests.index') }}"
           class="inline-flex items-center gap-2 text-sky-600 hover:text-sky-800 font-semibold">
            <i class="fas fa-arrow-right"></i>
            رجوع للقائمة
        </a>
    </div>

    <!-- معلومات الطلب -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">معلومات الطلب</h2>
            <span class="px-3 py-1 text-sm font-semibold rounded-full
                @if($instructorRequest->status === 'pending') bg-amber-100 text-amber-800
                @elseif($instructorRequest->status === 'approved') bg-emerald-100 text-emerald-800
                @else bg-rose-100 text-rose-800
                @endif">
                @if($instructorRequest->status === 'pending') قيد المراجعة
                @elseif($instructorRequest->status === 'approved') موافق عليها
                @else مرفوض
                @endif
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">المدرب</label>
                <p class="text-base font-semibold text-gray-900">{{ $instructorRequest->instructor->name ?? '-' }}</p>
                @if($instructorRequest->instructor && $instructorRequest->instructor->email)
                    <p class="text-sm text-gray-500 mt-1">{{ $instructorRequest->instructor->email }}</p>
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">تاريخ التقديم</label>
                <p class="text-base font-semibold text-gray-900">{{ $instructorRequest->created_at->format('Y-m-d H:i') }}</p>
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-500 mb-1">الموضوع</label>
            <p class="text-lg font-bold text-gray-900">{{ $instructorRequest->subject }}</p>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-500 mb-1">نص الطلب</label>
            <p class="text-base text-gray-900 whitespace-pre-wrap bg-gray-50 p-4 rounded-lg border border-gray-100">{{ $instructorRequest->message }}</p>
        </div>
    </div>

    <!-- رد الإدارة (إن وجد) -->
    @if($instructorRequest->admin_reply)
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">رد الإدارة</h2>
        <p class="text-gray-900 whitespace-pre-wrap bg-sky-50 p-4 rounded-lg border border-sky-100">{{ $instructorRequest->admin_reply }}</p>
        <p class="text-sm text-gray-500 mt-3">
            {{ $instructorRequest->replied_at?->format('Y-m-d H:i') }}
            @if($instructorRequest->repliedByUser)
                — {{ $instructorRequest->repliedByUser->name }}
            @endif
        </p>
    </div>
    @endif

    <!-- نموذج الرد (للطلبات قيد المراجعة) -->
    @if($instructorRequest->status === 'pending')
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">الرد على الطلب</h2>

        <form action="{{ route('admin.instructor-requests.respond', $instructorRequest) }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نص الرد *</label>
                <textarea name="admin_reply" rows="5" required
                          class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                          placeholder="اكتب ردك للمدرب...">{{ old('admin_reply') }}</textarea>
                @error('admin_reply')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap gap-4">
                <button type="submit" name="status" value="approved"
                        class="px-6 py-3 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition-colors">
                    <i class="fas fa-check ml-2"></i>
                    موافقة مع إرسال الرد
                </button>
                <button type="submit" name="status" value="rejected"
                        class="px-6 py-3 rounded-xl bg-rose-600 text-white font-bold hover:bg-rose-700 transition-colors">
                    <i class="fas fa-times ml-2"></i>
                    رفض مع إرسال الرد
                </button>
            </div>
        </form>

        <p class="text-sm text-gray-500 mt-4">
            سيتم حفظ الرد وستظهر الحالة (موافق عليه / مرفوض) مع نص الرد للمدرب في صفحة طلباته.
        </p>
    </div>
    @endif
</div>
@endsection
