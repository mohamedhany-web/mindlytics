@extends('layouts.employee')

@section('title', 'تعديل خطة التسويق')
@section('header', 'تعديل: ' . $plan->title)

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('employee.marketing-plans.show', $plan) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-right"></i> العودة للخطة
        </a>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6">
        <form method="post" action="{{ route('employee.marketing-plans.update', $plan) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">عنوان الخطة <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $plan->title) }}" required class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-pink-500">
                @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ملخص</label>
                <textarea name="summary" rows="3" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-pink-500">{{ old('summary', $plan->summary) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الأهداف / استراتيجية عامة</label>
                <textarea name="goals" rows="4" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-pink-500">{{ old('goals', $plan->goals) }}</textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">بداية</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $plan->start_date?->format('Y-m-d')) }}" class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">نهاية</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $plan->end_date?->format('Y-m-d')) }}" class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                <select name="status" class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
                    @foreach(['draft' => 'مسودة', 'active' => 'نشط', 'paused' => 'متوقف مؤقتاً', 'completed' => 'مكتمل'] as $v => $l)
                        <option value="{{ $v }}" {{ old('status', $plan->status) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ربط بدورة تصميم</label>
                <select name="design_task_cycle_id" class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
                    <option value="">— بدون ربط —</option>
                    @foreach($cycles as $c)
                        <option value="{{ $c->id }}" {{ (string) old('design_task_cycle_id', $plan->design_task_cycle_id) === (string) $c->id ? 'selected' : '' }}>#{{ $c->id }} — {{ $c->title }}</option>
                    @endforeach
                </select>
                @error('design_task_cycle_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="px-6 py-3 rounded-xl bg-pink-600 hover:bg-pink-700 text-white font-semibold">
                <i class="fas fa-save ml-2"></i> حفظ التعديلات
            </button>
        </form>
    </div>
</div>
@endsection
