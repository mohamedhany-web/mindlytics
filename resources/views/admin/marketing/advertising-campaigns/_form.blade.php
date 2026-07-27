@php
    $campaign = $campaign ?? null;
    $assigned = $assigned ?? [];
    $platforms = \App\Models\AdvertisingCampaign::platforms();
@endphp

@if($errors->any())
    <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-slate-700 mb-1">اسم الحملة <span class="text-rose-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $campaign->name ?? '') }}" required
               class="w-full rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 text-sm" placeholder="مثال: حملة رمضان — فيسبوك">
        @error('name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">المنصة</label>
        <select name="platform" class="w-full rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 text-sm">
            <option value="">— اختر —</option>
            @foreach($platforms as $value => $label)
                <option value="{{ $value }}" @selected(old('platform', $campaign->platform ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('platform')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">التكلفة المادية <span class="text-rose-500">*</span></label>
        <div class="flex gap-2">
            <input type="number" step="0.01" min="0" name="cost" value="{{ old('cost', $campaign->cost ?? 0) }}" required
                   class="w-full rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 text-sm">
            <input type="text" name="currency" value="{{ old('currency', $campaign->currency ?? 'EGP') }}" maxlength="3"
                   class="w-24 rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 text-sm text-center" placeholder="EGP">
        </div>
        @error('cost')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">تاريخ البداية</label>
        <input type="date" name="start_date" value="{{ old('start_date', optional($campaign->start_date ?? null)->format('Y-m-d')) }}"
               class="w-full rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 text-sm">
        @error('start_date')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">تاريخ النهاية <span class="text-slate-400 font-normal">(اختياري)</span></label>
        <input type="date" name="end_date" value="{{ old('end_date', optional($campaign->end_date ?? null)->format('Y-m-d')) }}"
               class="w-full rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 text-sm">
        @error('end_date')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-slate-700 mb-1">وصف الحملة</label>
        <textarea name="description" rows="2" class="w-full rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 text-sm" placeholder="نبذة عن الحملة والجمهور المستهدف…">{{ old('description', $campaign->description ?? '') }}</textarea>
        @error('description')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-slate-700 mb-2">موظفو السيلز المسؤولون عن الحملة</label>
        <p class="text-xs text-slate-500 mb-3">هؤلاء الموظفون فقط ستظهر لهم خانة هذه الحملة في تقريرهم اليومي.</p>
        @if($salesReps->isEmpty())
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-sm">لا يوجد موظفو مبيعات نشطون بعد.</div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-56 overflow-y-auto p-3 rounded-xl border border-slate-200 bg-slate-50/50">
                @foreach($salesReps as $rep)
                    @php $isChecked = in_array($rep->id, old('sales_reps', $assigned)); @endphp
                    <label class="flex items-center gap-2 text-sm text-slate-700 bg-white rounded-lg border border-slate-200 px-3 py-2 cursor-pointer hover:border-sky-300">
                        <input type="checkbox" name="sales_reps[]" value="{{ $rep->id }}" @checked($isChecked)
                               class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                        <span class="truncate">{{ $rep->name }}</span>
                    </label>
                @endforeach
            </div>
        @endif
        @error('sales_reps')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-slate-700 mb-1">ملاحظات داخلية</label>
        <textarea name="notes" rows="2" class="w-full rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 text-sm">{{ old('notes', $campaign->notes ?? '') }}</textarea>
        @error('notes')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $campaign->is_active ?? true))
                   class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
            <span>الحملة نشطة (تظهر في تقارير الموظفين)</span>
        </label>
    </div>
</div>
