<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">يوم الإجازة الأسبوعية</label>
    <select name="weekly_off_day" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
        <option value="">— افتراضي (عطلة نهاية الأسبوع) —</option>
        @foreach(\App\Models\User::weeklyOffDayOptions() as $value => $label)
            <option value="{{ $value }}" @selected((string) old('weekly_off_day', $employee->weekly_off_day ?? '') === (string) $value)>{{ $label }}</option>
        @endforeach
    </select>
    <p class="text-xs text-gray-500 mt-1">يُستخدم لقفل النظام في يوم الراحة، واستثناء التقرير اليومي والخصم التلقائي. مثال: إجازة الجمعة = يعمل باقي الأيام حسب موعده.</p>
    @error('weekly_off_day')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>
