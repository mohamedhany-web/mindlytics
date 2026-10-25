@extends('layouts.admin')

@section('title', 'تعديل خطة')
@section('header', 'تعديل: '.$plan->title)

@section('content')
<div class="max-w-3xl">
    <form method="post" action="{{ route('admin.moderator-marketing-plans.update', $plan) }}" class="rounded-2xl bg-white border p-6 space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-semibold mb-1">المشرف</label>
            <select name="moderator_id" class="w-full rounded-xl border px-3 py-2 text-sm">
                @foreach($moderators as $m)
                    <option value="{{ $m->id }}" @selected(old('moderator_id', $plan->moderator_id)==$m->id)>{{ $m->name }}</option>
                @endforeach
            </select>
        </div>
        <div><label class="block text-sm font-semibold mb-1">العنوان</label><input type="text" name="title" value="{{ old('title', $plan->title) }}" required class="w-full rounded-xl border px-3 py-2 text-sm"></div>
        <div><label class="block text-sm font-semibold mb-1">ملخص</label><textarea name="summary" rows="3" class="w-full rounded-xl border px-3 py-2 text-sm">{{ old('summary', $plan->summary) }}</textarea></div>
        <div><label class="block text-sm font-semibold mb-1">الأهداف</label><textarea name="goals" rows="3" class="w-full rounded-xl border px-3 py-2 text-sm">{{ old('goals', $plan->goals) }}</textarea></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs mb-1">من</label><input type="date" name="start_date" value="{{ old('start_date', $plan->start_date?->format('Y-m-d')) }}" class="w-full rounded-xl border px-3 py-2 text-sm"></div>
            <div><label class="block text-xs mb-1">إلى</label><input type="date" name="end_date" value="{{ old('end_date', $plan->end_date?->format('Y-m-d')) }}" class="w-full rounded-xl border px-3 py-2 text-sm"></div>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">الحالة</label>
            <select name="status" class="w-full rounded-xl border px-3 py-2 text-sm">
                @foreach(['draft','active','paused','completed'] as $s)
                    <option value="{{ $s }}" @selected(old('status', $plan->status)===$s)>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-6 py-2.5 bg-pink-600 text-white rounded-xl font-semibold text-sm">حفظ</button>
    </form>
</div>
@endsection
