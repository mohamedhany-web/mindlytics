@extends('layouts.app')

@section('title', 'تعديل الواجب - ' . $assignment->title)
@section('header', 'تعديل الواجب')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 mb-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="{{ route('instructor.assignments.index') }}" class="hover:text-sky-600">الواجبات</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">تعديل</span>
        </nav>
        <h1 class="text-xl font-bold text-slate-800">تعديل الواجب: {{ $assignment->title }}</h1>
    </div>

    <form action="{{ route('instructor.assignments.update', $assignment) }}" method="POST" class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 space-y-5">
        @csrf
        @method('PUT')
        <input type="hidden" name="advanced_course_id" value="{{ $assignment->advanced_course_id ?? $assignment->course_id }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label for="group_id" class="block text-sm font-semibold text-slate-700 mb-1">للمجموعة (اختياري)</label>
                <select name="group_id" id="group_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                    <option value="">لجميع طلاب الكورس</option>
                    @foreach($courseGroups as $g)
                        <option value="{{ $g->id }}" {{ old('group_id', $assignment->group_id) == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-slate-500">إن اخترت مجموعة سيظهر الواجب لأعضاءها فقط ويمكنهم التسليم جماعياً</p>
                @error('group_id')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label for="title" class="block text-sm font-semibold text-slate-700 mb-1">عنوان الواجب <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $assignment->title) }}" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                @error('title')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">الوصف</label>
                <textarea name="description" id="description" rows="3" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white resize-none">{{ old('description', $assignment->description) }}</textarea>
                @error('description')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label for="instructions" class="block text-sm font-semibold text-slate-700 mb-1">التعليمات</label>
                <textarea name="instructions" id="instructions" rows="4" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white resize-none">{{ old('instructions', $assignment->instructions) }}</textarea>
                @error('instructions')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="due_date" class="block text-sm font-semibold text-slate-700 mb-1">تاريخ الاستحقاق</label>
                <input type="datetime-local" name="due_date" id="due_date" value="{{ old('due_date', $assignment->due_date?->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                @error('due_date')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="max_score" class="block text-sm font-semibold text-slate-700 mb-1">الدرجة الكلية <span class="text-red-500">*</span></label>
                <input type="number" name="max_score" id="max_score" value="{{ old('max_score', $assignment->max_score) }}" min="1" max="1000" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                @error('max_score')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="inline-flex items-center gap-3 cursor-pointer p-3 rounded-xl border border-slate-200 hover:bg-slate-50 w-full">
                    <input type="checkbox" name="allow_late_submission" value="1" {{ old('allow_late_submission', $assignment->allow_late_submission) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-sky-500 focus:ring-sky-500/20">
                    <span class="text-sm font-medium text-slate-700">السماح بالتسليم المتأخر</span>
                </label>
            </div>
            <div class="md:col-span-2">
                <label for="status" class="block text-sm font-semibold text-slate-700 mb-1">الحالة <span class="text-red-500">*</span></label>
                <select name="status" id="status" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                    <option value="draft" {{ old('status', $assignment->status) == 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="published" {{ old('status', $assignment->status) == 'published' ? 'selected' : '' }}>منشور</option>
                    <option value="archived" {{ old('status', $assignment->status) == 'archived' ? 'selected' : '' }}>مؤرشف</option>
                </select>
                @error('status')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-5 border-t border-slate-200">
            <a href="{{ route('instructor.assignments.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-center">إلغاء</a>
            <button type="submit" class="px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold">حفظ التعديلات</button>
        </div>
    </form>
</div>
@endsection
