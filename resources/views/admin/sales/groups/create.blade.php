@extends('layouts.admin')

@section('title', 'مجموعة جديدة')
@section('header', 'مجموعة عملاء جديدة')

@section('content')
<div class="max-w-3xl bg-white border rounded-xl p-6">
    <form method="post" action="{{ route('admin.sales.groups.store') }}" class="space-y-4">
        @csrf
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">اسم المجموعة</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">موظف المبيعات</label>
                <select name="assigned_to" required class="w-full border rounded-lg px-3 py-2">
                    @foreach($reps as $rep)
                        <option value="{{ $rep->id }}" @selected(old('assigned_to') == $rep->id)>{{ $rep->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">وصف</label>
            <textarea name="description" rows="2" class="w-full border rounded-lg px-3 py-2">{{ old('description') }}</textarea>
        </div>
        <button type="submit" class="px-5 py-2 bg-slate-800 text-white rounded-lg font-semibold">إنشاء — ثم أضف العملاء</button>
    </form>
</div>
@endsection
