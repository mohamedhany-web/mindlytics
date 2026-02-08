@extends('layouts.admin')

@section('title', 'إنشاء اتفاقية تقسيط')
@section('header', 'إنشاء اتفاقية تقسيط')

@section('content')
@php
    $plans = $plans ?? collect();
    $enrollments = $enrollments ?? collect();
@endphp
<div class="container mx-auto px-4 py-8 space-y-8">
    <div class="bg-gradient-to-br from-emerald-500 via-emerald-600 to-sky-600 rounded-3xl shadow-xl text-white p-8 relative overflow-hidden">
        <div class="absolute inset-y-0 right-0 w-1/3 pointer-events-none opacity-20">
            <div class="w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-3xl font-black tracking-tight">اتفاقية تقسيط جديدة</h1>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/20">
                        <i class="fas fa-user-graduate text-xs"></i>
                        ربط الطالب بخطة دفع مرنة
                    </span>
                </div>
                <p class="mt-3 text-white/75 max-w-2xl">
                    اختر خطة التقسيط المناسبة، وحدد التسجيل الخاص بالطالب، ثم راجع بيانات المبالغ لتوليد جدول السداد تلقائياً.
                </p>
            </div>
            <div class="flex flex-wrap gap-3 justify-end">
                <a href="{{ route('admin.installments.agreements.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white text-emerald-700 font-semibold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-arrow-right"></i>
                    العودة للاتفاقيات
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
            <form action="{{ route('admin.installments.agreements.store') }}" method="POST" class="space-y-8">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">خطة التقسيط *</label>
                        <select name="installment_plan_id" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                            <option value="">اختر خطة</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" {{ old('installment_plan_id', $selectedPlanId) == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }} — {{ $plan->course->title ?? 'خطة عامة' }}
                                </option>
                            @endforeach
                        </select>
                        @error('installment_plan_id')
                            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">التسجيل المرتبط *</label>
                        <select name="student_course_enrollment_id" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                            <option value="">اختر طالباً وكورساً</option>
                            @foreach($enrollments as $enrollment)
                                <option value="{{ $enrollment->id }}" {{ old('student_course_enrollment_id') == $enrollment->id ? 'selected' : '' }}>
                                    {{ $enrollment->student->name ?? 'طالب غير معروف' }} — {{ $enrollment->course->title ?? 'بدون كورس' }}
                                </option>
                            @endforeach
                        </select>
                        @error('student_course_enrollment_id')
                            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">تاريخ البدء *</label>
                        <input type="date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" required
                               class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        @error('start_date')
                            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">حالة الاتفاقية</label>
                        <select name="status" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">الحالة الافتراضية (نشط)</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ old('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl px-4 py-4 space-y-4">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">تفاصيل المبالغ</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">إجمالي المبلغ</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" name="total_amount" value="{{ old('total_amount') }}"
                                       class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                       placeholder="يتم استخدام قيمة الخطة أو الكورس تلقائياً">
                                <span class="absolute inset-y-0 left-4 flex items-center text-sm font-semibold text-gray-500">ج.م</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">الدفعة المقدمة</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" name="deposit_amount" value="{{ old('deposit_amount') }}"
                                       class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <span class="absolute inset-y-0 left-4 flex items-center text-sm font-semibold text-gray-500">ج.م</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">عدد الأقساط</label>
                            <input type="number" min="1" max="60" name="installments_count" value="{{ old('installments_count') }}"
                                   class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">ملاحظات إضافية</label>
                    <textarea name="notes" rows="3"
                              class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                              placeholder="أضف أي تعليمات أو ملاحظات تخص هذه الاتفاقية">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('admin.installments.agreements.index') }}" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100">إلغاء</a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow">
                        <i class="fas fa-save"></i>
                        حفظ الاتفاقية
                    </button>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-lg font-black text-gray-900 mb-4">دليل سريع</h2>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-info-circle mt-1 text-emerald-500"></i>
                        تأكد من اختيار خطة نشطة أو مرتبطة بكورس يسمح بالتقسيط.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-user mt-1 text-emerald-500"></i>
                        لا يمكن تفعيل أكثر من اتفاقية نشطة لنفس تسجيل الطالب في نفس الوقت.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-calculator mt-1 text-emerald-500"></i>
                        اترك حقول المبالغ فارغة لاستخدام قيم الخطة بشكل افتراضي.
                    </li>
                </ul>
            </div>

            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-lg font-black text-gray-900 mb-4">بحث عن تسجيل</h2>
                <p class="text-xs text-gray-500 mb-4">استخدم الحقول التالية لتصفية التسجيلات المتاحة قبل إنشاء الاتفاقية.</p>
                <form method="GET" class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">اسم الطالب أو رقم الهاتف</label>
                        <input type="text" name="student" value="{{ request('student') }}" class="w-full px-4 py-2 rounded-2xl border border-gray-200 bg-gray-50 text-sm text-gray-700">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">اسم الكورس</label>
                        <input type="text" name="course" value="{{ request('course') }}" class="w-full px-4 py-2 rounded-2xl border border-gray-200 bg-gray-50 text-sm text-gray-700">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold">
                            <i class="fas fa-search"></i>
                            تطبيق البحث
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
