@php $lead = $lead ?? null; @endphp
<div class="md:col-span-2">
    <label class="block text-sm font-medium text-gray-700 mb-1">الاسم <span class="text-red-500">*</span></label>
    <input type="text" name="name" required value="{{ old('name', $lead->name ?? '') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">الهاتف</label>
    <input type="text" name="phone" value="{{ old('phone', $lead->phone ?? '') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">البريد</label>
    <input type="email" name="email" value="{{ old('email', $lead->email ?? '') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">الشركة</label>
    <input type="text" name="company" value="{{ old('company', $lead->company ?? '') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">المصدر <span class="text-red-500">*</span></label>
    <select name="source" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
        @foreach(\App\Models\SalesLead::SOURCES as $k => $label)
            <option value="{{ $k }}" @selected(old('source', $lead->source ?? 'other') === $k)>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">المرحلة <span class="text-red-500">*</span></label>
    <select name="stage" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
        @foreach(\App\Models\SalesLead::STAGES as $k => $label)
            <option value="{{ $k }}" @selected(old('stage', $lead->stage ?? 'new') === $k)>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">الأولوية <span class="text-red-500">*</span></label>
    <select name="priority" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
        @foreach(\App\Models\SalesLead::PRIORITIES as $k => $label)
            <option value="{{ $k }}" @selected(old('priority', $lead->priority ?? 'normal') === $k)>{{ $label }}</option>
        @endforeach
    </select>
    <p class="text-xs text-gray-500 mt-1">عاجل يظهر في لوحة التحكم ويُرتّب أعلى القائمة عند اختيار ترتيب الأولوية.</p>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">قيمة متوقعة (ج.م)</label>
    <input type="number" step="0.01" min="0" name="expected_value" value="{{ old('expected_value', $lead->expected_value ?? '') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">متابعة تالية</label>
    <input type="datetime-local" name="next_follow_up_at" value="{{ old('next_follow_up_at', ($lead && $lead->next_follow_up_at) ? $lead->next_follow_up_at->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
</div>
<div class="md:col-span-2">
    <label class="block text-sm font-medium text-gray-700 mb-1">اهتمام / منتج</label>
    <textarea name="interest" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">{{ old('interest', $lead->interest ?? '') }}</textarea>
</div>
<div class="md:col-span-2">
    <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
    <textarea name="notes" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">{{ old('notes', $lead->notes ?? '') }}</textarea>
</div>
<div class="md:col-span-2">
    @php
        $savedLostReason = old('lost_reason', $lead->lost_reason ?? '');
        $matchedLossCode = collect(\App\Models\SalesLead::LOSS_REASONS)->search($savedLostReason, true);
        $lossCode = old('lost_reason_code', $matchedLossCode !== false ? $matchedLossCode : '');
        $lossCustom = old('lost_reason_custom', ($matchedLossCode === false ? $savedLostReason : ''));
    @endphp
    <label class="block text-sm font-medium text-gray-700 mb-1">سبب الخسارة (إلزامي عند مرحلة "خسارة")</label>
    <select name="lost_reason_code" id="lost_reason_code" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
        <option value="">— اختر السبب —</option>
        @foreach(\App\Models\SalesLead::LOSS_REASONS as $k => $label)
            <option value="{{ $k }}" @selected((string) $lossCode === (string) $k)>{{ $label }}</option>
        @endforeach
    </select>
    @error('lost_reason_code')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
</div>
<div class="md:col-span-2" id="lost_reason_custom_wrap" style="display: {{ ($lossCode === 'other') ? 'block' : 'none' }};">
    <label class="block text-sm font-medium text-gray-700 mb-1">اكتب سبب الخسارة</label>
    <input type="text" name="lost_reason_custom" id="lost_reason_custom" value="{{ $lossCustom }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
    @error('lost_reason_custom')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
</div>

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
