@php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500';
@endphp

<div class="space-y-5">
    <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">اسم الفريق <span class="text-rose-600">*</span></label>
        <input type="text" name="name" value="{{ old('name', $team->name ?? '') }}" required class="{{ $inputClass }}" placeholder="مثال: فريق المبيعات — القاهرة">
        @error('name')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">الوصف</label>
        <textarea name="description" rows="3" class="{{ $inputClass }}" placeholder="وصف مختصر للفريق (اختياري)">{{ old('description', $team->description ?? '') }}</textarea>
        @error('description')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">مدير المبيعات <span class="text-rose-600">*</span></label>
        <select name="manager_id" required class="{{ $inputClass }}">
            <option value="">— اختر مدير المبيعات —</option>
            @foreach($managers as $m)
                <option value="{{ $m->id }}" @selected(old('manager_id', $team->manager_id ?? '') == $m->id)>{{ $m->name }}</option>
            @endforeach
        </select>
        <p class="text-xs text-slate-500 mt-1"><i class="fas fa-info-circle text-teal-600 ml-0.5"></i> يجب أن يكون الموظف بوظيفة «مدير مبيعات» — مدير واحد لكل فريق.</p>
        @error('manager_id')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-xs font-semibold text-slate-700 mb-2">أعضاء الفريق (موظفو مبيعات)</label>
        <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-3 max-h-72 overflow-y-auto">
            @if($salesReps->isEmpty())
                <p class="text-sm text-slate-500 text-center py-6">لا يوجد موظفو مبيعات متاحون — أضف موظفين بوظيفة «مبيعات» أولاً.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($salesReps as $rep)
                        <label class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-white border border-slate-200 hover:border-teal-300 cursor-pointer transition-colors text-sm">
                            <input type="checkbox" name="member_ids[]" value="{{ $rep->id }}"
                                   class="rounded border-slate-300 text-teal-600 focus:ring-teal-500"
                                   @checked(in_array($rep->id, old('member_ids', $selectedMemberIds ?? [])))>
                            <span class="font-medium text-slate-800">{{ $rep->name }}</span>
                        </label>
                    @endforeach
                </div>
            @endif
        </div>
        @error('member_ids')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <label class="flex items-center gap-2.5 px-4 py-3 rounded-xl border border-slate-200 bg-white cursor-pointer">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500"
               @checked(old('is_active', $team->is_active ?? true))>
        <span class="text-sm font-semibold text-slate-800">فريق نشط</span>
    </label>
</div>
