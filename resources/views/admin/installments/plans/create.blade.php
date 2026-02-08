@extends('layouts.admin')

@section('title', 'إضافة خطة تقسيط جديدة - Mindlytics')
@section('header', 'إضافة خطة تقسيط جديدة')

@section('content')
@php
    $frequencyUnits = $frequencyUnits ?? [];
@endphp
<div class="container mx-auto px-4 py-8 space-y-8">
    <div class="bg-gradient-to-br from-sky-500 via-sky-600 to-purple-600 rounded-3xl shadow-xl text-white p-8 relative overflow-hidden">
        <div class="absolute inset-y-0 right-0 w-1/3 pointer-events-none opacity-20">
            <div class="w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-3xl font-black tracking-tight">إنشاء خطة تقسيط</h1>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/20">
                        <i class="fas fa-calendar-alt text-xs"></i>
                        دعم الدفع المرن للطلاب
                    </span>
                </div>
                <p class="mt-3 text-white/75 max-w-2xl">
                    اربط الخطة بكورس معين، حدّد قيمة الأقساط وفتراتها، واختر إن كان النظام سيولد الخطط تلقائياً عند تسجيل الطالب.
                </p>
            </div>
            <div class="flex flex-wrap gap-3 justify-end">
                <a href="{{ route('admin.installments.plans.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white text-sky-700 font-semibold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-arrow-right"></i>
                    العودة لقائمة الخطط
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-black text-gray-900">بيانات الخطة</h2>
                    <p class="text-sm text-gray-500 mt-1">أدخل التفاصيل الأساسية، ثم تابع بتحديد الأقساط والدورية.</p>
                </div>
            </div>

            <form action="{{ route('admin.installments.plans.store') }}" method="POST" class="space-y-8">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">اسم الخطة *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        @error('name')
                            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">الكورس المرتبط (اختياري)</label>
                        <select name="advanced_course_id"
                                class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            <option value="">خطة عامة</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('advanced_course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }} ({{ number_format($course->price ?? 0, 2) }} ج.م)
                                </option>
                            @endforeach
                        </select>
                        @error('advanced_course_id')
                            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">تفاصيل الخطة</label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                              placeholder="أضف تفاصيل توضيحية إضافية عن خطة التقسيط">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-4 text-xs text-sky-700 leading-relaxed">
                    <strong class="block text-sm mb-2">نصائح سريعة</strong>
                    - اترك المبلغ الإجمالي فارغاً ليتم استخدام سعر الكورس إن وجد.
                    <br>- يمكنك استخدام الخطة لكورس واحد أو كخطة عامة لكافة الطلاب.
                </div>

                <div class="bg-gray-50 rounded-2xl px-4 py-4 space-y-4">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">الجانب المالي</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">إجمالي المبلغ</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" name="total_amount" value="{{ old('total_amount') }}"
                                       class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                       placeholder="يتم استخدام سعر الكورس إن تركته فارغًا">
                                <span class="absolute inset-y-0 left-4 flex items-center text-sm font-semibold text-gray-500">ج.م</span>
                            </div>
                            @error('total_amount')
                                <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">الدفعة المقدمة</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" name="deposit_amount" value="{{ old('deposit_amount', 0) }}"
                                       class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                <span class="absolute inset-y-0 left-4 flex items-center text-sm font-semibold text-gray-500">ج.م</span>
                            </div>
                            @error('deposit_amount')
                                <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">عدد الأقساط *</label>
                            <input type="number" min="1" max="36" name="installments_count" value="{{ old('installments_count', 6) }}" required
                                   class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            @error('installments_count')
                                <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl px-4 py-4 space-y-4">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">الدورية والسماح</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">وحدة الدورية *</label>
                            <select name="frequency_unit"
                                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                @foreach($frequencyUnits as $key => $label)
                                    <option value="{{ $key }}" {{ old('frequency_unit', 'month') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('frequency_unit')
                                <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">الفاصل الزمني *</label>
                            <input type="number" min="1" max="12" name="frequency_interval" value="{{ old('frequency_interval', 1) }}" required
                                   class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            @error('frequency_interval')
                                <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">فترة السماح (أيام)</label>
                            <input type="number" min="0" max="30" name="grace_period_days" value="{{ old('grace_period_days', 0) }}"
                                   class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            @error('grace_period_days')
                                <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <label class="inline-flex items-center gap-3 text-sm font-medium text-gray-700">
                        <input type="checkbox" name="auto_generate_on_enrollment" value="1" {{ old('auto_generate_on_enrollment') ? 'checked' : '' }} class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        إنشاء جدول الأقساط تلقائيًا عند تفعيل التسجيل
                    </label>
                    <label class="inline-flex items-center gap-3 text-sm font-medium text-gray-700">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        تفعيل الخطة فورًا
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('admin.installments.plans.index') }}" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100">إلغاء</a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl shadow">
                        <i class="fas fa-save"></i>
                        حفظ الخطة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
