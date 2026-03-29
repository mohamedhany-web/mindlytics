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
    <label class="block text-sm font-medium text-gray-700 mb-1">سبب الخسارة (إن وُجد)</label>
    <input type="text" name="lost_reason" value="{{ old('lost_reason', $lead->lost_reason ?? '') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
</div>
