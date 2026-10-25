@php
    $workMode = old('work_mode', $employee->work_mode ?? \App\Models\User::WORK_MODE_ONLINE);
    $offlineType = old('offline_attendance_type', $employee->offline_attendance_type ?? \App\Models\User::OFFLINE_FULL_TIME);
    $onsiteDays = old('onsite_days', $employee->onsite_days ?? []);
    if (! is_array($onsiteDays)) {
        $onsiteDays = [];
    }
    $dayOptions = \App\Models\User::weeklyOffDayOptions();
    $planOld = old('work_week_plan');
    $existingPlan = is_array($planOld) ? $planOld : ($employee->normalizedWorkWeekPlan() ?? []);
    $defaultSchedule = null;
    foreach ($workSchedules ?? [] as $s) {
        if ((int) old('work_schedule_id', $employee->work_schedule_id) === (int) $s->id) {
            $defaultSchedule = $s;
            break;
        }
    }
    $defaultStart = $defaultSchedule ? \Illuminate\Support\Str::of($defaultSchedule->start_time)->substr(0, 5) : '09:00';
    $defaultEnd = $defaultSchedule ? \Illuminate\Support\Str::of($defaultSchedule->end_time)->substr(0, 5) : '17:00';
    $defaultHours = $defaultSchedule?->required_hours ?? 8;
@endphp
<div class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50/60 p-4 space-y-4" x-data="{
    workMode: @js($workMode),
    offlineType: @js($offlineType || 'full_time'),
    useCustomWeek: @js(! empty($existingPlan) || $workMode === 'hybrid')
}">
    <div>
        <p class="text-sm font-bold text-slate-900">نوع العمل والجدول الأسبوعي</p>
        <p class="text-xs text-slate-500 mt-1">
            أونلاين = عن بُعد · أوفلاين = مكتب بموافقة المدير · Hybrid = أيام أوفلاين وأيام أونلاين.
            يمكن تخصيص ميعاد مختلف لكل يوم.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">نوع العمل *</label>
            <select name="work_mode" x-model="workMode" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white"
                    @change="if (workMode === 'hybrid') useCustomWeek = true">
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
                <option value="full_time">Full-time — كل أيام العمل (حسب الجدول/يوم الراحة)</option>
                <option value="selected_days">أيام محددة فقط</option>
            </select>
            @error('offline_attendance_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">الشيفت الافتراضي (ميعاد الحضور/الانصراف)</label>
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
        <p class="text-xs text-slate-500 mt-1">يُستخدم كأساس لأي يوم بلا مواعيد مخصّصة في الجدول الأسبوعي.</p>
    </div>

    <div x-show="workMode === 'offline' && offlineType === 'selected_days' && !useCustomWeek" x-cloak>
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
    </div>

    <div class="rounded-xl border border-indigo-100 bg-white p-4 space-y-3">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" x-model="useCustomWeek" name="use_custom_week" value="1"
                   class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                   @checked(! empty($existingPlan) || $workMode === 'hybrid')>
            <span class="text-sm font-bold text-slate-900">تفعيل الجدول الأسبوعي التفصيلي</span>
        </label>
        <p class="text-xs text-slate-500">حدد لكل يوم: راحة / عمل، أوفلاين أو أونلاين، وميعاد الحضور إن اختلف عن الشيفت الافتراضي.</p>

        <div x-show="useCustomWeek" x-cloak class="overflow-x-auto">
            <table class="min-w-full text-sm text-right border border-slate-200 rounded-lg overflow-hidden">
                <thead class="bg-slate-50 text-xs text-slate-500">
                    <tr>
                        <th class="px-3 py-2">اليوم</th>
                        <th class="px-3 py-2">يعمل؟</th>
                        <th class="px-3 py-2">الوضع</th>
                        <th class="px-3 py-2">حضور</th>
                        <th class="px-3 py-2">انصراف</th>
                        <th class="px-3 py-2">ساعات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($dayOptions as $dayIndex => $dayLabel)
                        @php
                            $row = $existingPlan[$dayIndex] ?? $existingPlan[(string) $dayIndex] ?? [];
                            $active = (bool) old("work_week_plan.$dayIndex.active", $row['active'] ?? false);
                            $dayMode = old("work_week_plan.$dayIndex.attendance_mode", $row['attendance_mode'] ?? ($workMode === 'offline' ? 'offline' : 'online'));
                            $start = old("work_week_plan.$dayIndex.start_time", $row['start_time'] ?? '');
                            $end = old("work_week_plan.$dayIndex.end_time", $row['end_time'] ?? '');
                            $hours = old("work_week_plan.$dayIndex.required_hours", $row['required_hours'] ?? '');
                        @endphp
                        <tr class="bg-white" x-data="{ active: @js($active) }">
                            <td class="px-3 py-2 font-semibold text-slate-800">{{ $dayLabel }}</td>
                            <td class="px-3 py-2">
                                <input type="hidden" name="work_week_plan[{{ $dayIndex }}][active]" value="0">
                                <input type="checkbox" name="work_week_plan[{{ $dayIndex }}][active]" value="1"
                                       x-model="active" @checked($active)
                                       class="w-4 h-4 text-emerald-600 border-gray-300 rounded">
                            </td>
                            <td class="px-3 py-2">
                                <select name="work_week_plan[{{ $dayIndex }}][attendance_mode]"
                                        class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-white"
                                        :disabled="!active">
                                    <option value="online" @selected($dayMode === 'online')>أونلاين</option>
                                    <option value="offline" @selected($dayMode === 'offline')>أوفلاين</option>
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                <input type="time" name="work_week_plan[{{ $dayIndex }}][start_time]" value="{{ $start }}"
                                       class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs"
                                       :disabled="!active" placeholder="{{ $defaultStart }}">
                            </td>
                            <td class="px-3 py-2">
                                <input type="time" name="work_week_plan[{{ $dayIndex }}][end_time]" value="{{ $end }}"
                                       class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs"
                                       :disabled="!active" placeholder="{{ $defaultEnd }}">
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" step="0.5" min="0" max="24"
                                       name="work_week_plan[{{ $dayIndex }}][required_hours]" value="{{ $hours }}"
                                       class="w-20 px-2 py-1.5 border border-slate-200 rounded-lg text-xs"
                                       :disabled="!active" placeholder="{{ $defaultHours }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="text-[11px] text-slate-500 mt-2">اترك الحضور/الانصراف/الساعات فارغة لاستخدام الشيفت الافتراضي في ذلك اليوم.</p>
            @error('work_week_plan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>
</div>
