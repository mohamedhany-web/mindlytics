@php
    $currentOff = old('weekly_off_day', $employee->weekly_off_day ?? '');
    $previewUser = clone $employee;
    $previewUser->weekly_off_day = $currentOff === '' || $currentOff === null ? null : (int) $currentOff;
    $nextOff = $previewUser->nextWeeklyOffDate();
    $isOffToday = $previewUser->isWeeklyOff(now());
@endphp
<div class="sm:col-span-2 rounded-xl border border-amber-200 bg-amber-50/40 p-4 space-y-3">
    <div>
        <label class="block text-sm font-bold text-slate-800 mb-1">يوم الإجازة الأسبوعية *</label>
        <p class="text-xs text-slate-600 leading-relaxed">
            هذا الحقل هو <strong>المصدر الوحيد</strong> ليوم راحة الموظف. يُحسب عليه قفل النظام، سجل الحضور (`يوم راحة`)، واستثناء التقرير اليومي.
            موعد العمل يحدد <em>ساعات</em> الدوام فقط وليس يوم الإجازة.
        </p>
    </div>
    <select name="weekly_off_day" id="weekly_off_day"
            class="w-full px-4 py-2.5 border border-amber-300 rounded-lg bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
        <option value="">— افتراضي (السبت والأحد) —</option>
        @foreach(\App\Models\User::weeklyOffDayOptions() as $value => $label)
            <option value="{{ $value }}" @selected((string) $currentOff === (string) $value)>{{ $label }}</option>
        @endforeach
    </select>
    @error('weekly_off_day')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror

    <div class="flex flex-wrap gap-2 text-xs">
        @if($isOffToday)
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-violet-100 text-violet-900 border border-violet-200 font-bold">
                <i class="fas fa-umbrella-beach"></i> اليوم راحة حسب هذا الإعداد
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white text-slate-700 border border-slate-200 font-semibold">
                <i class="fas fa-calendar-day text-amber-600"></i>
                أقرب يوم راحة:
                <strong>{{ $nextOff->locale('ar')->translatedFormat('l j F Y') }}</strong>
            </span>
        @endif
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white text-slate-600 border border-slate-200">
            مثال: إجازة الجمعة = يعمل باقي الأيام حسب موعده
        </span>
    </div>
</div>
