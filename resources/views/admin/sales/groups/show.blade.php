@extends('layouts.admin')

@section('title', $group->name)
@section('header', 'مجموعة: '.$group->name)

@section('content')
<div class="space-y-4">
    @if(session('success'))<div class="text-sm text-emerald-700">{{ session('success') }}</div>@endif

    <form method="post" action="{{ route('admin.sales.groups.update', $group) }}" class="bg-white border rounded-xl p-5 space-y-4">
        @csrf @method('PUT')
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">الاسم</label>
                <input type="text" name="name" value="{{ old('name', $group->name) }}" required class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">موظف مسند</label>
                <select name="assigned_to" required class="w-full border rounded-lg px-3 py-2">
                    @foreach($reps as $rep)
                        <option value="{{ $rep->id }}" @selected(old('assigned_to', $group->assigned_to) == $rep->id)>{{ $rep->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">وصف</label>
            <input type="text" name="description" value="{{ old('description', $group->description) }}" class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-bold mb-2">اختر العملاء (يُعاد إسنادهم للموظف المحدد)</label>
            <div class="max-h-80 overflow-y-auto border rounded-lg p-3 space-y-1 text-sm">
                @foreach($availableLeads as $lead)
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="lead_ids[]" value="{{ $lead->id }}" class="rounded"
                            @checked(old('lead_ids') ? in_array($lead->id, old('lead_ids', [])) : (int)$lead->sales_lead_group_id === (int)$group->id)>
                        <span>{{ $lead->name }}</span>
                        <span class="text-slate-400 text-xs">{{ $lead->phone }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <button type="submit" class="px-5 py-2 bg-slate-800 text-white rounded-lg font-semibold">حفظ</button>
    </form>

    <form method="post" action="{{ route('admin.sales.groups.destroy', $group) }}" onsubmit="return confirm('حذف المجموعة؟')">
        @csrf @method('DELETE')
        <button type="submit" class="text-rose-700 text-sm">حذف المجموعة</button>
    </form>
</div>
@endsection
