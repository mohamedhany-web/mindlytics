@php
    $campaign = $campaign ?? null;
    $assigned = $assigned ?? [];
    $platforms = \App\Models\AdvertisingCampaign::platforms();
    $input = 'w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
@endphp

@if($errors->any())
    <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">
        <p class="font-bold mb-1">يوجد أخطاء في البيانات المدخلة</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

{{-- المعلومات الأساسية --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/80 flex items-center gap-3">
        <span class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
            <i class="fas fa-info-circle"></i>
        </span>
        <div>
            <h2 class="text-base font-bold text-gray-900">المعلومات الأساسية</h2>
            <p class="text-xs text-gray-500">اسم الحملة والمنصة والوصف</p>
        </div>
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="md:col-span-2">
            <label class="{{ $label }}">اسم الحملة <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $campaign->name ?? '') }}" required
                   class="{{ $input }}" placeholder="مثال: حملة رمضان — فيسبوك">
            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="{{ $label }}">المنصة</label>
            <select name="platform" class="{{ $input }}">
                <option value="">— اختر —</option>
                @foreach($platforms as $value => $labelOpt)
                    <option value="{{ $value }}" @selected(old('platform', $campaign->platform ?? '') === $value)>{{ $labelOpt }}</option>
                @endforeach
            </select>
            @error('platform')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="{{ $label }}">حالة الحملة</label>
            <label class="flex items-center gap-3 px-4 py-2.5 rounded-lg border border-gray-300 bg-gray-50 cursor-pointer hover:bg-white">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $campaign->is_active ?? true))
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm font-medium text-gray-800">الحملة نشطة (تظهر في تقارير الموظفين)</span>
            </label>
        </div>

        <div class="md:col-span-2">
            <label class="{{ $label }}">وصف الحملة</label>
            <textarea name="description" rows="3" class="{{ $input }}" placeholder="نبذة عن الحملة والجمهور المستهدف…">{{ old('description', $campaign->description ?? '') }}</textarea>
            @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

{{-- التكلفة والمدة --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/80 flex items-center gap-3">
        <span class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
            <i class="fas fa-coins"></i>
        </span>
        <div>
            <h2 class="text-base font-bold text-gray-900">التكلفة والمدة</h2>
            <p class="text-xs text-gray-500">الميزانية وفترة تشغيل الحملة</p>
        </div>
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
        <div>
            <label class="{{ $label }}">التكلفة المادية <span class="text-red-500">*</span></label>
            <div class="flex gap-2">
                <input type="number" step="0.01" min="0" name="cost" value="{{ old('cost', $campaign->cost ?? 0) }}" required
                       class="{{ $input }} flex-1">
                <input type="text" name="currency" value="{{ old('currency', $campaign->currency ?? 'EGP') }}" maxlength="3"
                       class="{{ $input }} w-24 text-center" placeholder="EGP">
            </div>
            @error('cost')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $label }}">تاريخ البداية</label>
            <input type="date" name="start_date" value="{{ old('start_date', optional($campaign->start_date ?? null)->format('Y-m-d')) }}"
                   class="{{ $input }}">
            @error('start_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $label }}">تاريخ النهاية <span class="text-gray-400 font-normal">(اختياري)</span></label>
            <input type="date" name="end_date" value="{{ old('end_date', optional($campaign->end_date ?? null)->format('Y-m-d')) }}"
                   class="{{ $input }}">
            @error('end_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

{{-- موظفو السيلز --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/80 flex items-center gap-3">
        <span class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center">
            <i class="fas fa-user-group"></i>
        </span>
        <div>
            <h2 class="text-base font-bold text-gray-900">موظفو السيلز المسؤولون</h2>
            <p class="text-xs text-gray-500">هؤلاء فقط ستظهر لهم خانة هذه الحملة في التقرير اليومي</p>
        </div>
    </div>
    <div class="p-5 sm:p-6 space-y-4">
        @if($salesReps->isEmpty())
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                لا يوجد موظفو مبيعات نشطون بعد.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 max-h-64 overflow-y-auto p-3 rounded-xl border border-gray-200 bg-gray-50">
                @foreach($salesReps as $rep)
                    @php $isChecked = in_array($rep->id, old('sales_reps', $assigned)); @endphp
                    <label class="flex items-center gap-2 text-sm text-gray-800 bg-white rounded-lg border border-gray-200 px-3 py-2.5 cursor-pointer hover:border-blue-300 hover:bg-blue-50/40 transition-colors">
                        <input type="checkbox" name="sales_reps[]" value="{{ $rep->id }}" @checked($isChecked)
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="truncate font-medium">{{ $rep->name }}</span>
                    </label>
                @endforeach
            </div>
        @endif
        @error('sales_reps')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

        <div>
            <label class="{{ $label }}">ملاحظات داخلية</label>
            <textarea name="notes" rows="2" class="{{ $input }}" placeholder="ملاحظات للإدارة فقط…">{{ old('notes', $campaign->notes ?? '') }}</textarea>
            @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
</div>
