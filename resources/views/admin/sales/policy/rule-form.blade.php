@extends('layouts.admin')

@section('title', $rule->exists ? 'تعديل قاعدة' : 'قاعدة جديدة')
@section('header', 'المبيعات — قواعد السيلز')

@section('content')
@php $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500'; @endphp
<div class="max-w-3xl mx-auto">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-lg p-6 space-y-4">
        <a href="{{ route('admin.sales.policy.index') }}" class="text-sm text-slate-500 hover:text-slate-800"><i class="fas fa-arrow-right ml-1"></i> رجوع</a>
        <h1 class="text-xl font-black text-slate-900">{{ $rule->exists ? 'تعديل قاعدة' : 'إضافة قاعدة' }}</h1>

        <form method="POST" action="{{ $rule->exists ? route('admin.sales.policy.rules.update', $rule) : route('admin.sales.policy.rules.store') }}" class="space-y-4">
            @csrf
            @if($rule->exists) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500">القسم *</label>
                    <select name="section_id" required class="{{ $inputClass }}">
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" @selected((int) old('section_id', $rule->section_id) === $section->id)>{{ $section->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">رقم القاعدة</label>
                    <input type="text" name="rule_number" value="{{ old('rule_number', $rule->rule_number) }}" placeholder="8 / 15 / A" class="{{ $inputClass }}">
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-slate-500">العنوان *</label>
                    <input type="text" name="title" value="{{ old('title', $rule->title) }}" required class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">English subtitle</label>
                    <input type="text" name="title_en" value="{{ old('title_en', $rule->title_en) }}" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">الترتيب</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $rule->sort_order ?? 0) }}" class="{{ $inputClass }}">
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-slate-500">المحتوى</label>
                    <textarea name="content" rows="14" class="{{ $inputClass }} font-mono text-sm">{{ old('content', $rule->content) }}</textarea>
                </div>
                <div>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $rule->is_active ?? true))> نشط
                    </label>
                </div>
            </div>

            <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold">حفظ</button>
        </form>
    </div>
</div>
@endsection
