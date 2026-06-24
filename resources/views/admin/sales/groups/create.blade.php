@extends('layouts.admin')

@section('title', 'مجموعة جديدة')
@section('header', 'مجموعة عملاء جديدة')

@section('content')
<div class="max-w-3xl bg-white border rounded-xl p-6">
    <form method="post" action="{{ route('admin.sales.groups.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">اسم المجموعة</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">موظفو المبيعات (يمكن اختيار أكثر من واحد أو الكل)</label>
            <div class="max-h-48 overflow-y-auto border rounded-lg p-3 space-y-2">
                @foreach($reps as $rep)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="member_ids[]" value="{{ $rep->id }}" class="rounded"
                            @checked(collect(old('member_ids', []))->contains($rep->id))>
                        <span>{{ $rep->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('member_ids')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">وصف</label>
            <textarea name="description" rows="2" class="w-full border rounded-lg px-3 py-2">{{ old('description') }}</textarea>
        </div>
        <button type="submit" class="px-5 py-2 bg-slate-800 text-white rounded-lg font-semibold">إنشاء — ثم أضف العملاء</button>
    </form>
</div>
@endsection
