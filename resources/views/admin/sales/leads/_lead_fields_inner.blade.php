@php
    $lead = $lead ?? null;
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500';
    $labelClass = 'block text-xs font-semibold text-slate-700 mb-1';
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    <div class="md:col-span-2 xl:col-span-3">
        <label class="{{ $labelClass }}">الاسم <span class="text-rose-600">*</span></label>
        <input type="text" name="name" required value="{{ old('name', $lead->name ?? '') }}" class="{{ $inputClass }}">
        @error('name')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="{{ $labelClass }}">الهاتف</label>
        <input type="text" name="phone" value="{{ old('phone', $lead->phone ?? '') }}" class="{{ $inputClass }}">
        @error('phone')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="{{ $labelClass }}">البريد</label>
        <input type="email" name="email" value="{{ old('email', $lead->email ?? '') }}" class="{{ $inputClass }}">
        @error('email')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="{{ $labelClass }}">الشركة</label>
        <input type="text" name="company" value="{{ old('company', $lead->company ?? '') }}" class="{{ $inputClass }}">
        @error('company')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="{{ $labelClass }}">المصدر <span class="text-rose-600">*</span></label>
        <select name="source" required class="{{ $inputClass }}">
            @foreach(\App\Models\SalesLead::SOURCES as $k => $label)
                <option value="{{ $k }}" @selected(old('source', $lead->source ?? 'other') === $k)>{{ $label }}</option>
            @endforeach
        </select>
        @error('source')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="{{ $labelClass }}">المرحلة <span class="text-rose-600">*</span></label>
        <select name="stage" required class="{{ $inputClass }}">
            @foreach(\App\Models\SalesLead::STAGES as $k => $label)
                <option value="{{ $k }}" @selected(old('stage', $lead->stage ?? 'new') === $k)>{{ $label }}</option>
            @endforeach
        </select>
        @error('stage')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="{{ $labelClass }}">الأولوية <span class="text-rose-600">*</span></label>
        <select name="priority" required class="{{ $inputClass }}">
            @foreach(\App\Models\SalesLead::PRIORITIES as $k => $label)
                <option value="{{ $k }}" @selected(old('priority', $lead->priority ?? 'normal') === $k)>{{ $label }}</option>
            @endforeach
        </select>
        <p class="text-[11px] text-slate-500 mt-1">«عاجل» يظهر أعلى القائمة عند ترتيب الأولوية.</p>
        @error('priority')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="{{ $labelClass }}">قيمة متوقعة (ج.م)</label>
        <input type="number" step="0.01" min="0" name="expected_value" value="{{ old('expected_value', $lead->expected_value ?? '') }}" class="{{ $inputClass }}">
        @error('expected_value')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="{{ $labelClass }}">متابعة تالية</label>
        <input type="datetime-local" name="next_follow_up_at"
               value="{{ old('next_follow_up_at', ($lead && $lead->next_follow_up_at) ? $lead->next_follow_up_at->format('Y-m-d\TH:i') : '') }}"
               class="{{ $inputClass }}">
        @error('next_follow_up_at')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2 xl:col-span-3">
        <label class="{{ $labelClass }}">اهتمام / منتج</label>
        <textarea name="interest" rows="2" class="{{ $inputClass }}">{{ old('interest', $lead->interest ?? '') }}</textarea>
        @error('interest')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2 xl:col-span-3">
        <label class="{{ $labelClass }}">ملاحظات</label>
        <textarea name="notes" rows="3" class="{{ $inputClass }}">{{ old('notes', $lead->notes ?? '') }}</textarea>
        @error('notes')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    @php
        $savedLostReason = old('lost_reason', $lead->lost_reason ?? '');
        $matchedLossCode = collect(\App\Models\SalesLead::LOSS_REASONS)->search($savedLostReason, true);
        $lossCode = old('lost_reason_code', $matchedLossCode !== false ? $matchedLossCode : '');
        $lossCustom = old('lost_reason_custom', ($matchedLossCode === false ? $savedLostReason : ''));
    @endphp
    <div class="md:col-span-2 xl:col-span-3 pt-2 border-t border-slate-100">
        <label class="{{ $labelClass }}">سبب الخسارة (إلزامي عند مرحلة «خسارة»)</label>
        <select name="lost_reason_code" id="lost_reason_code" class="{{ $inputClass }}">
            <option value="">— اختر السبب —</option>
            @foreach(\App\Models\SalesLead::LOSS_REASONS as $k => $label)
                <option value="{{ $k }}" @selected((string) $lossCode === (string) $k)>{{ $label }}</option>
            @endforeach
        </select>
        @error('lost_reason_code')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2 xl:col-span-3" id="lost_reason_custom_wrap" style="display: {{ ($lossCode === 'other') ? 'block' : 'none' }};">
        <label class="{{ $labelClass }}">اكتب سبب الخسارة</label>
        <input type="text" name="lost_reason_custom" id="lost_reason_custom" value="{{ $lossCustom }}" class="{{ $inputClass }}">
        @error('lost_reason_custom')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const setupLossReasonToggle = () => {
            const select = document.getElementById('lost_reason_code');
            const stage = document.querySelector('select[name="stage"]');
            const customWrap = document.getElementById('lost_reason_custom_wrap');
            const customInput = document.getElementById('lost_reason_custom');
            if (!select || !stage || !customWrap || !customInput) return;

            const refresh = () => {
                customWrap.style.display = select.value === 'other' ? 'block' : 'none';
                select.required = stage.value === 'lost';
                customInput.required = stage.value === 'lost' && select.value === 'other';
            };

            select.addEventListener('change', refresh);
            stage.addEventListener('change', refresh);
            refresh();
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupLossReasonToggle, { once: true });
        } else {
            setupLossReasonToggle();
        }
    })();
</script>
@endpush
