@extends('layouts.admin')

@section('title', 'طلب حجز #' . $offlineCourseBooking->id)
@section('header', 'طلب حجز أوفلاين #' . $offlineCourseBooking->id)

@section('content')
<div class="space-y-6 max-w-4xl">
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.offline-course-bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-800 rounded-lg text-sm hover:bg-gray-200">
            <i class="fas fa-arrow-right ml-2"></i> القائمة
        </a>
        <a href="{{ route('admin.offline-courses.show', $offlineCourseBooking->course) }}" class="inline-flex items-center px-4 py-2 bg-purple-50 text-purple-800 rounded-lg text-sm hover:bg-purple-100">
            صفحة الكورس
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 space-y-4">
        <h2 class="text-lg font-bold text-gray-900">بيانات الطلب</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div><dt class="text-gray-500">الطالب</dt><dd class="font-semibold text-gray-900">{{ $offlineCourseBooking->user->name }}</dd></div>
            <div><dt class="text-gray-500">البريد</dt><dd class="text-gray-800">{{ $offlineCourseBooking->user->email }}</dd></div>
            <div><dt class="text-gray-500">الكورس</dt><dd class="font-semibold text-gray-900">{{ $offlineCourseBooking->course->title }}</dd></div>
            @if($offlineCourseBooking->requestedGroup)
                <div class="sm:col-span-2"><dt class="text-gray-500">المجموعة المطلوبة (رابط الحجز)</dt><dd class="font-semibold text-purple-800">{{ $offlineCourseBooking->requestedGroup->name }}</dd></div>
            @endif
            <div><dt class="text-gray-500">سعر الكورس</dt><dd class="text-gray-800">{{ number_format((float) $offlineCourseBooking->course->price, 2) }} ج.م</dd></div>
            <div><dt class="text-gray-500">طريقة الدفع</dt><dd class="text-gray-800">{{ $offlineCourseBooking->payment_method === 'wallet' ? 'محفظة إلكترونية' : 'تحويل بنكي' }}</dd></div>
            <div><dt class="text-gray-500">الاسم</dt><dd class="text-gray-800">{{ $offlineCourseBooking->transfer_name ?: '—' }}</dd></div>
            @if($offlineCourseBooking->wallet)
                <div><dt class="text-gray-500">قناة التحويل</dt><dd class="text-gray-800">{{ \App\Models\Wallet::typeLabel($offlineCourseBooking->wallet->type) }} @if($offlineCourseBooking->wallet->name) — {{ $offlineCourseBooking->wallet->name }} @endif</dd></div>
            @endif
            <div><dt class="text-gray-500">الحالة</dt><dd class="font-semibold">{{ $offlineCourseBooking->status }}</dd></div>
            <div><dt class="text-gray-500">تاريخ الإرسال</dt><dd class="text-gray-800">{{ $offlineCourseBooking->created_at?->format('Y-m-d H:i') }}</dd></div>
        </dl>
        @if($offlineCourseBooking->student_notes)
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs text-gray-500 mb-1">ملاحظات الطالب</p>
                <p class="text-gray-800 whitespace-pre-wrap">{{ $offlineCourseBooking->student_notes }}</p>
            </div>
        @endif
        @if($offlineCourseBooking->payment_proof)
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs text-gray-500 mb-2">إيصال التحويل</p>
                <a href="{{ asset('storage/' . $offlineCourseBooking->payment_proof) }}" target="_blank" rel="noopener" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                    <i class="fas fa-image ml-2"></i> عرض الصورة
                </a>
            </div>
        @endif
        @if($offlineCourseBooking->admin_notes)
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs text-gray-500 mb-1">ملاحظات الإدارة</p>
                <p class="text-gray-800 whitespace-pre-wrap">{{ $offlineCourseBooking->admin_notes }}</p>
            </div>
        @endif
    </div>

    @if($offlineCourseBooking->isPending())
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 space-y-4">
            <h2 class="text-lg font-bold text-gray-900">قبول الطلب وإضافة للمجموعة</h2>
            <p class="text-sm text-gray-600">عند الموافقة يُنشأ تسجيل نشط للطالب، وتُحسب الفاتورة/الدفعة بالكامل بمبلغ سعر الكورس (إن كان أكبر من صفر).</p>
            <form action="{{ route('admin.offline-course-bookings.approve', $offlineCourseBooking) }}" method="post" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">المجموعة *</label>
                    <select name="group_id" required class="w-full max-w-md rounded-lg border-gray-300">
                        <option value="">— اختر مجموعة نشطة —</option>
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}" @disabled(! $g->canEnroll())>
                                {{ $g->name }} — متاح {{ $g->availableSeats() }} / {{ $g->max_students }}
                                @if(! $g->canEnroll()) (غير متاحة) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('group_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات (اختياري)</label>
                    <textarea name="admin_notes" rows="2" class="w-full rounded-lg border-gray-300">{{ old('admin_notes') }}</textarea>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700">موافقة وتفعيل التسجيل</button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-lg border border-red-100 p-6">
            <h2 class="text-lg font-bold text-red-800 mb-2">رفض الطلب</h2>
            <form action="{{ route('admin.offline-course-bookings.reject', $offlineCourseBooking) }}" method="post" class="space-y-3">
                @csrf
                <textarea name="admin_notes" rows="2" class="w-full rounded-lg border-gray-300" placeholder="سبب الرفض (اختياري)">{{ old('admin_notes') }}</textarea>
                <button type="submit" class="px-6 py-2.5 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700">رفض الطلب</button>
            </form>
        </div>
    @else
        <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 text-sm text-slate-700">
            تمت معالجة هذا الطلب
            @if($offlineCourseBooking->reviewed_at)
                في {{ $offlineCourseBooking->reviewed_at->format('Y-m-d H:i') }}
            @endif
            @if($offlineCourseBooking->assignedGroup)
                — المجموعة: {{ $offlineCourseBooking->assignedGroup->name }}
            @endif
        </div>
    @endif
</div>
@endsection
