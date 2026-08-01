@php
    $workMode = old('work_mode', $employee->work_mode ?? \App\Models\User::WORK_MODE_ONLINE);
    $offlineType = old('offline_attendance_type', $employee->offline_attendance_type ?? \App\Models\User::OFFLINE_FULL_TIME);
    $onsiteDays = old('onsite_days', $employee->onsite_days ?? []);
    if (! is_array($onsiteDays)) {
        $onsiteDays = [];
    }
    $dayOptions = \App\Models\User::weeklyOffDayOptions();
@endphp
<div class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50/60 p-4 space-y-4" x-data="{
    workMode: @js($workMode),
    offlineType: @js($offlineType || 'full_time')
}">
    <div>
        <p class="text-sm font-bold text-slate-900">نوع العمل والحضور</p>
        <p class="text-xs text-slate-500 mt-1">أونلاين = حضور من أي مكان · أوفلاين = لازم ينزل المكتب ويحتاج موافقة المدير</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">نوع العمل *</label>
            <select name="work_mode" x-model="workMode" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white">
                @foreach(\App\Models\User::workModeLabels() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            @error('work_mode')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div x-show="workMode === 'offline'" x-cloak>
            <label class="block text-sm font-medium text-gray-700 mb-2">نظام نزول الأوفلاين *</label>
            <select name="offline_attendance_type" x-model="offlineType"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white">
                <option value="full_time">Full-time — كل أيام العمل حسب الشيفت</option>
                <option value="selected_days">أيام محددة</option>
            </select>
            @error('offline_attendance_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div x-show="workMode === 'offline' && offlineType === 'selected_days'" x-cloak>
        <label class="block text-sm font-medium text-gray-700 mb-2">أيام النزول للمكتب *</label>
        <div class="flex flex-wrap gap-2">
            @foreach($dayOptions as $dayIndex => $dayLabel)
                <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm cursor-pointer hover:border-blue-300">
                    <input type="checkbox" name="onsite_days[]" value="{{ $dayIndex }}"
                           @checked(in_array((string) $dayIndex, array_map('strval', $onsiteDays), true) || in_array((int) $dayIndex, array_map('intval', $onsiteDays), true))
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span>{{ $dayLabel }}</span>
                </label>
            @endforeach
        </div>
        @error('onsite_days')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        @error('onsite_days.*')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">ميعاد الحضور والانصراف (الشيفت)</label>
        <select name="work_schedule_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white">
            <option value="">افتراضي (أول موعد نشط)</option>
            @foreach($workSchedules ?? [] as $schedule)
                <option value="{{ $schedule->id }}" @selected((int) old('work_schedule_id', $employee->work_schedule_id) === (int) $schedule->id)>
                    {{ $schedule->name }} — حضور {{ \Illuminate\Support\Str::of($schedule->start_time)->substr(0, 5) }} / انصراف {{ \Illuminate\Support\Str::of($schedule->end_time)->substr(0, 5) }}
                    ({{ $schedule->required_hours }} س · سماح {{ $schedule->grace_minutes }} د)
                </option>
            @endforeach
        </select>
        @error('work_schedule_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        <p class="text-xs text-slate-500 mt-1">يُستخدم لقفل النظام ووقت الحضور/الانصراف المطلوب.</p>
    </div>
</div>
