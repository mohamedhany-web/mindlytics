@extends('layouts.admin')

@section('title', 'حجز يدوي مع تقسيط')
@section('header', 'حجز يدوي مع تقسيط')

@section('content')
@php
    $plans = $plans ?? collect();
    $onlineCourses = $onlineCourses ?? collect();
    $offlineCoursesGroups = $offlineCoursesGroups ?? collect();
@endphp
<div class="container mx-auto px-4 py-8 space-y-8">
    <div class="bg-gradient-to-br from-violet-500 via-violet-600 to-sky-600 rounded-3xl shadow-xl text-white p-8 relative overflow-hidden">
        <div class="absolute inset-y-0 right-0 w-1/3 pointer-events-none opacity-20">
            <div class="w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-3xl font-black tracking-tight">حجز يدوي + خطة تقسيط</h1>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/20">
                        <i class="fas fa-envelope text-xs"></i>
                        بريد الطالب + نوع الكورس
                    </span>
                </div>
                <p class="mt-3 text-white/75 max-w-2xl">
                    أدخل بريد الطالب المسجّل في المنصة، واختر كورساً أونلاين أو أوفلاين. إن لم يكن مسجّلاً في الكورس يُنشأ التسقيط تلقائياً ثم تُولَّد خطة الأقساط.
                </p>
            </div>
            <div class="flex flex-wrap gap-3 justify-end">
                <a href="{{ route('admin.installments.agreements.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white/15 text-white font-semibold border border-white/30 hover:bg-white/25 transition-all">
                    <i class="fas fa-arrow-right"></i>
                    الاتفاقيات
                </a>
                <a href="{{ route('admin.installments.agreements.create') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white text-violet-700 font-semibold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-link"></i>
                    ربط بتسجيل موجود
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
            <form id="manual-installment-form" action="{{ route('admin.installments.agreements.manual-booking.store') }}" method="POST" class="space-y-8">
                @csrf

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">البريد الإلكتروني للطالب *</label>
                    <input type="email" name="student_email" value="{{ old('student_email') }}" required autocomplete="off"
                           class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500"
                           placeholder="نفس البريد المستخدم في حساب الطالب">
                    @error('student_email')
                        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">نوع الكورس *</label>
                    <div class="flex flex-wrap gap-4">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="course_mode" value="online" class="text-violet-600" {{ old('course_mode', 'online') === 'online' ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-gray-800">أونلاين</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="course_mode" value="offline" class="text-violet-600" {{ old('course_mode') === 'offline' ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-gray-800">أوفلاين</span>
                        </label>
                    </div>
                    @error('course_mode')
                        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div id="block-online" class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">الكورس الأونلاين *</label>
                    <select name="advanced_course_id" id="advanced_course_id"
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                        <option value="">اختر الكورس</option>
                        @foreach($onlineCourses as $c)
                            <option value="{{ $c->id }}" {{ (string) old('advanced_course_id') === (string) $c->id ? 'selected' : '' }}>
                                {{ $c->title }} — {{ number_format($c->price ?? 0, 2) }} ج.م
                            </option>
                        @endforeach
                    </select>
                    @error('advanced_course_id')
                        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div id="block-offline" class="space-y-4 hidden">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">الكورس الأوفلاين *</label>
                        <select name="offline_course_id" id="offline_course_id"
                                class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                            <option value="">اختر الكورس</option>
                            @foreach($offlineCourses ?? collect() as $c)
                                <option value="{{ $c->id }}" {{ (string) old('offline_course_id') === (string) $c->id ? 'selected' : '' }}>
                                    {{ $c->title }} — {{ number_format($c->price ?? 0, 2) }} ج.م
                                </option>
                            @endforeach
                        </select>
                        @error('offline_course_id')
                            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">المجموعة *</label>
                        <select name="offline_group_id" id="offline_group_id"
                                class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                            <option value="">اختر المجموعة بعد اختيار الكورس</option>
                        </select>
                        @error('offline_group_id')
                            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700">خطة التقسيط *</label>
                        <select name="installment_plan_id" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500" required>
                            <option value="">اختر خطة</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" {{ old('installment_plan_id', $selectedPlanId) == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }} — {{ $plan->course->title ?? 'عامة (مناسبة لأوفلاين أو تجاوز يدوي)' }}
                                </option>
                            @endforeach
                        </select>
                        @error('installment_plan_id')
                            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500">للكورس الأوفلاين اختر خطة «عامة» غير مربوطة بكورس أونلاين.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">تاريخ البدء *</label>
                        <input type="date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" required
                               class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                        @error('start_date')
                            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">حالة الاتفاقية</label>
                        <select name="status" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
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
                                       class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500"
                                       placeholder="الخطة أو سعر الكورس">
                                <span class="absolute inset-y-0 left-4 flex items-center text-sm font-semibold text-gray-500">ج.م</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">الدفعة المقدمة</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" name="deposit_amount" value="{{ old('deposit_amount') }}"
                                       class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                                <span class="absolute inset-y-0 left-4 flex items-center text-sm font-semibold text-gray-500">ج.م</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">عدد الأقساط</label>
                            <input type="number" min="1" max="60" name="installments_count" value="{{ old('installments_count') }}"
                                   class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">ملاحظات</label>
                    <textarea name="notes" rows="3"
                              class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-violet-500 focus:border-violet-500"
                              placeholder="اختياري">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('admin.installments.agreements.index') }}" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100">إلغاء</a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl shadow">
                        <i class="fas fa-check"></i>
                        إنشاء الحجز والتقسيط
                    </button>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-lg font-black text-gray-900 mb-4">تنبيهات</h2>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-user-check mt-1 text-violet-500"></i>
                        يجب أن يكون البريد مسجّلاً مسبقاً كمستخدم في المنصة.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-chalkboard mt-1 text-violet-500"></i>
                        للأوفلاين: يجب توفر أماكن في الكورس والمجموعة قبل الإرسال.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-ban mt-1 text-violet-500"></i>
                        لا يُسمح بأكثر من اتفاقية نشطة/متأخرة لنفس تسجيل الكورس.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const groupsByCourse = @json($offlineCoursesGroups);
    const modeInputs = document.querySelectorAll('input[name="course_mode"]');
    const blockOnline = document.getElementById('block-online');
    const blockOffline = document.getElementById('block-offline');
    const selCourse = document.getElementById('advanced_course_id');
    const selOfflineCourse = document.getElementById('offline_course_id');
    const selGroup = document.getElementById('offline_group_id');

    function fillGroups(courseId) {
        if (!selGroup) return;
        const prev = @json(old('offline_group_id'));
        selGroup.innerHTML = '<option value=\"\">اختر المجموعة</option>';
        const row = (groupsByCourse || []).find(function (x) { return String(x.id) === String(courseId); });
        if (!row || !row.groups) return;
        row.groups.forEach(function (g) {
            const opt = document.createElement('option');
            opt.value = g.id;
            opt.textContent = g.name;
            if (prev && String(prev) === String(g.id)) opt.selected = true;
            selGroup.appendChild(opt);
        });
    }

    function syncMode() {
        const mode = document.querySelector('input[name=\"course_mode\"]:checked')?.value || 'online';
        if (mode === 'online') {
            blockOnline.classList.remove('hidden');
            blockOffline.classList.add('hidden');
            if (selCourse) { selCourse.disabled = false; selCourse.required = true; }
            if (selOfflineCourse) { selOfflineCourse.disabled = true; selOfflineCourse.required = false; selOfflineCourse.value = ''; }
            if (selGroup) { selGroup.disabled = true; selGroup.required = false; selGroup.innerHTML = '<option value=\"\">—</option>'; }
        } else {
            blockOnline.classList.add('hidden');
            blockOffline.classList.remove('hidden');
            if (selCourse) { selCourse.disabled = true; selCourse.required = false; selCourse.value = ''; }
            if (selOfflineCourse) { selOfflineCourse.disabled = false; selOfflineCourse.required = true; }
            if (selGroup) { selGroup.disabled = false; selGroup.required = true; }
            fillGroups(selOfflineCourse && selOfflineCourse.value);
        }
    }

    modeInputs.forEach(function (el) { el.addEventListener('change', syncMode); });
    if (selOfflineCourse) selOfflineCourse.addEventListener('change', function () { fillGroups(this.value); });

    syncMode();
    @if(old('course_mode') === 'offline' && old('offline_course_id'))
        fillGroups(@json(old('offline_course_id')));
    @endif
})();
</script>
@endpush
@endsection
