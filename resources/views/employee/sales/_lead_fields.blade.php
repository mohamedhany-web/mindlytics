@php
    $lead = $lead ?? null;
    $groups = $groups ?? collect();
    $input = 'w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500';
    $label = 'block text-xs font-bold text-slate-600 mb-1';
    $savedLostReason = old('lost_reason', $lead->lost_reason ?? '');
    $matchedLossCode = collect(\App\Models\SalesLead::LOSS_REASONS)->search($savedLostReason, true);
    $lossCode = old('lost_reason_code', $matchedLossCode !== false ? $matchedLossCode : '');
    $lossCustom = old('lost_reason_custom', ($matchedLossCode === false ? $savedLostReason : ''));
    $isLost = old('stage', $lead->stage ?? '') === 'lost';
@endphp

<div class="space-y-5">
    <section class="panel-card overflow-hidden">
        <div class="panel-card-head px-4 sm:px-5 py-3">
            <h3 class="font-bold text-slate-800">بيانات العميل</h3>
        </div>
        <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <div class="md:col-span-2 xl:col-span-3">
                <label class="{{ $label }}">الاسم <span class="text-rose-600">*</span></label>
                <input type="text" name="name" required value="{{ old('name', $lead->name ?? '') }}" class="{{ $input }}">
                @error('name')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $label }}">الهاتف</label>
                <input type="text" name="phone" value="{{ old('phone', $lead->phone ?? '') }}" dir="ltr" class="{{ $input }}">
                @error('phone')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $label }}">البريد</label>
                <input type="email" name="email" value="{{ old('email', $lead->email ?? '') }}" class="{{ $input }}">
                @error('email')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $label }}">الشركة</label>
                <input type="text" name="company" value="{{ old('company', $lead->company ?? '') }}" class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">المصدر <span class="text-rose-600">*</span></label>
                <select name="source" required class="{{ $input }}">
                    @foreach(\App\Models\SalesLead::SOURCES as $k => $lab)
                        <option value="{{ $k }}" @selected(old('source', $lead->source ?? 'other') === $k)>{{ $lab }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">الأولوية <span class="text-rose-600">*</span></label>
                <select name="priority" required class="{{ $input }}">
                    @foreach(\App\Models\SalesLead::PRIORITIES as $k => $lab)
                        <option value="{{ $k }}" @selected(old('priority', $lead->priority ?? 'normal') === $k)>{{ $lab }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">المجموعة</label>
                <select name="sales_lead_group_id" class="{{ $input }}">
                    <option value="">— بدون مجموعة —</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" @selected(old('sales_lead_group_id', $lead->sales_lead_group_id ?? '') == $group->id)>
                            {{ $group->name }}@if($group->is_admin_managed ?? false) (إدارة) @endif
                        </option>
                    @endforeach
                </select>
                @error('sales_lead_group_id')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    <section class="panel-card overflow-hidden">
        <div class="panel-card-head px-4 sm:px-5 py-3">
            <h3 class="font-bold text-slate-800">الاهتمام والكورس</h3>
        </div>
        <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div>
                <label class="{{ $label }}">اهتمام العميل <span class="text-rose-600">*</span></label>
                <select name="interest_type_id" required class="{{ $input }}">
                    <option value="">— اختر —</option>
                    @foreach($interestTypes ?? \App\Models\SalesInterestType::active()->ordered()->get() as $itype)
                        <option value="{{ $itype->id }}" @selected(old('interest_type_id', $lead->interest_type_id ?? '') == $itype->id)>{{ $itype->name_ar }}</option>
                    @endforeach
                </select>
                @error('interest_type_id')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $label }}">قيمة متوقعة (ج.م)</label>
                <input type="number" step="0.01" min="0" name="expected_value" value="{{ old('expected_value', $lead->expected_value ?? '') }}" class="{{ $input }}">
                @error('expected_value')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            @include('sales._course_picker', [
                'lead' => $lead,
                'coursesCatalogUrl' => route('employee.sales.courses.index'),
                'inputClass' => $input,
                'labelClass' => $label,
            ])
            <div class="md:col-span-2 xl:col-span-4">
                <label class="{{ $label }}">تفاصيل الاهتمام</label>
                <textarea name="interest" rows="2" class="{{ $input }}">{{ old('interest', $lead->interest ?? '') }}</textarea>
            </div>
        </div>
    </section>

    <section class="panel-card overflow-hidden">
        <div class="panel-card-head px-4 sm:px-5 py-3">
            <h3 class="font-bold text-slate-800">المتابعة</h3>
        </div>
        <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="{{ $label }}">موعد المتابعة <span class="text-rose-600">*</span></label>
                <input type="datetime-local" name="next_follow_up_at"
                       value="{{ old('next_follow_up_at', ($lead && $lead->next_follow_up_at) ? $lead->next_follow_up_at->format('Y-m-d\TH:i') : now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i')) }}"
                       class="{{ $input }}">
                @error('next_follow_up_at')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $label }}">الإجراء التالي <span class="text-rose-600">*</span></label>
                <select name="follow_up_channel" class="{{ $input }}">
                    <option value="">— اختر —</option>
                    @foreach(\App\Models\SalesLead::FOLLOW_UP_CHANNELS as $k => $lab)
                        <option value="{{ $k }}" @selected(old('follow_up_channel', $lead->follow_up_channel ?? 'call') === $k)>{{ $lab }}</option>
                    @endforeach
                </select>
                @error('follow_up_channel')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    <section class="panel-card overflow-hidden">
        <div class="panel-card-head px-4 sm:px-5 py-3">
            <h3 class="font-bold text-slate-800">ملاحظات</h3>
        </div>
        <div class="p-4 sm:p-5 space-y-4">
            <div>
                <label class="{{ $label }}">ملاحظات عامة</label>
                <textarea name="notes" rows="3" class="{{ $input }}">{{ old('notes', $lead->notes ?? '') }}</textarea>
            </div>

            @if($isLost)
                <div class="rounded-lg border border-rose-200 bg-rose-50/50 p-4 space-y-3">
                    <p class="text-sm font-bold text-rose-900">سبب الخسارة</p>
                    <select name="lost_reason_code" id="lost_reason_code" class="{{ $input }}">
                        <option value="">— اختر السبب —</option>
                        @foreach(\App\Models\SalesLead::LOSS_REASONS as $k => $lab)
                            <option value="{{ $k }}" @selected((string) $lossCode === (string) $k)>{{ $lab }}</option>
                        @endforeach
                    </select>
                    @error('lost_reason_code')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                    <div id="lost_reason_custom_wrap" style="display: {{ ($lossCode === 'other') ? 'block' : 'none' }};">
                        <label class="{{ $label }}">اكتب سبب الخسارة</label>
                        <input type="text" name="lost_reason_custom" id="lost_reason_custom" value="{{ $lossCustom }}" class="{{ $input }}">
                        @error('lost_reason_custom')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>

<input type="hidden" name="stage" value="{{ old('stage', $lead->stage) }}">

@if($isLost)
<script>
    (function () {
        const select = document.getElementById('lost_reason_code');
        const customWrap = document.getElementById('lost_reason_custom_wrap');
        const customInput = document.getElementById('lost_reason_custom');
        if (!select || !customWrap || !customInput) return;
        const refresh = () => {
            customWrap.style.display = select.value === 'other' ? 'block' : 'none';
            select.required = true;
            customInput.required = select.value === 'other';
        };
        select.addEventListener('change', refresh);
        refresh();
    })();
</script>
@endif
