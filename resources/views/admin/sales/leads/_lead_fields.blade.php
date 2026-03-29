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
</div>
