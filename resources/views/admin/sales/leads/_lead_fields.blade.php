@php $lead = $lead ?? null; @endphp
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @isset($salesReps)
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">مسند إلى (موظف مبيعات) <span class="text-red-500">*</span></label>
        <select name="assigned_to" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
            @foreach($salesReps as $rep)
                <option value="{{ $rep->id }}" @selected(old('assigned_to', $lead->assigned_to ?? '') == $rep->id)>{{ $rep->name }}</option>
            @endforeach
        </select>
        @error('assigned_to')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    @endisset
    @include('employee.sales._lead_fields_inner', ['lead' => $lead])
    @isset($lead)
    <div class="md:col-span-2 mt-6 pt-6 border-t border-gray-200">
        <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <i class="fas fa-star text-amber-500"></i>
            رضا العميل (CSAT) — بعد إغلاق الصفقة
        </h4>
        <p class="text-xs text-gray-500 mb-3">يُستخدم في مؤشرات الجودة الشهرية لموظف المبيعات.</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">التقييم (1–5)</label>
                <select name="csat_rating" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                    <option value="">— لا يوجد —</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" @selected((string) old('csat_rating', $lead->csat_rating ?? '') === (string) $i)>{{ $i }}</option>
                    @endfor
                </select>
                @error('csat_rating')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظة (اختياري)</label>
                <textarea name="csat_comment" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">{{ old('csat_comment', $lead->csat_comment) }}</textarea>
            </div>
        </div>
        @if($lead->csat_recorded_at)
            <p class="text-xs text-gray-400 mt-2">آخر تسجيل: {{ $lead->csat_recorded_at->format('Y-m-d H:i') }}</p>
        @endif
    </div>
    @endisset
</div>
