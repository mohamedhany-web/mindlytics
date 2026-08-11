@php
    $isEdit = isset($internship);
    $action = $isEdit ? route('admin.internships.update', $internship) : route('admin.internships.store');
@endphp

@extends('layouts.admin')

@section('title', $isEdit ? 'تعديل فرصة تدريب' : 'فرصة تدريب جديدة')
@section('header', $isEdit ? 'تعديل فرصة تدريب' : 'فرصة تدريب جديدة')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800 font-semibold">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-red-800 text-sm">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.internships.index') }}" class="text-sky-700 font-semibold hover:underline">← العودة للقائمة</a>
        @if($isEdit)
            <a href="{{ route('public.internships.show', $internship->slug) }}" target="_blank" class="text-sm text-slate-600 hover:underline">معاينة عامة</a>
        @endif
    </div>

    <form method="POST" action="{{ $action }}" class="bg-white border border-slate-200 rounded-2xl p-6 space-y-5">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-bold mb-1">عنوان الفرصة *</label>
                <input type="text" name="title" value="{{ old('title', $isEdit ? $internship->title : '') }}" required class="w-full rounded-xl border-slate-200">
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">الرابط (slug)</label>
                <input type="text" name="slug" value="{{ old('slug', $isEdit ? $internship->slug : '') }}" dir="ltr" class="w-full rounded-xl border-slate-200" placeholder="auto-from-title">
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">القسم / التخصص</label>
                <input type="text" name="department" value="{{ old('department', $isEdit ? $internship->department : '') }}" class="w-full rounded-xl border-slate-200" placeholder="Frontend / Data / Marketing">
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">النوع *</label>
                <select name="type" class="w-full rounded-xl border-slate-200" required>
                    @foreach(\App\Models\Internship::types() as $key => $label)
                        <option value="{{ $key }}" @selected(old('type', $isEdit ? $internship->type : 'onsite') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">الحالة *</label>
                <select name="status" class="w-full rounded-xl border-slate-200" required>
                    @foreach(\App\Models\Internship::statuses() as $key => $label)
                        <option value="{{ $key }}" @selected(old('status', $isEdit ? $internship->status : 'draft') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">الموقع</label>
                <input type="text" name="location" value="{{ old('location', $isEdit ? $internship->location : '') }}" class="w-full rounded-xl border-slate-200">
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">المدة</label>
                <input type="text" name="duration" value="{{ old('duration', $isEdit ? $internship->duration : '') }}" class="w-full rounded-xl border-slate-200" placeholder="3 أشهر">
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">عدد المقاعد</label>
                <input type="number" name="seats" min="1" value="{{ old('seats', $isEdit ? $internship->seats : '') }}" class="w-full rounded-xl border-slate-200">
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">ترتيب العرض</label>
                <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $isEdit ? $internship->sort_order : 0) }}" class="w-full rounded-xl border-slate-200">
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">تاريخ البداية</label>
                <input type="date" name="starts_at" value="{{ old('starts_at', $isEdit && $internship->starts_at ? $internship->starts_at->format('Y-m-d') : '') }}" class="w-full rounded-xl border-slate-200">
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">تاريخ النهاية</label>
                <input type="date" name="ends_at" value="{{ old('ends_at', $isEdit && $internship->ends_at ? $internship->ends_at->format('Y-m-d') : '') }}" class="w-full rounded-xl border-slate-200">
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">آخر موعد للتقديم</label>
                <input type="date" name="application_deadline" value="{{ old('application_deadline', $isEdit && $internship->application_deadline ? $internship->application_deadline->format('Y-m-d') : '') }}" class="w-full rounded-xl border-slate-200">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold mb-1">ملخص قصير</label>
            <textarea name="summary" rows="2" class="w-full rounded-xl border-slate-200">{{ old('summary', $isEdit ? $internship->summary : '') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-bold mb-1">الوصف</label>
            <textarea name="description" rows="5" class="w-full rounded-xl border-slate-200">{{ old('description', $isEdit ? $internship->description : '') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-bold mb-1">المتطلبات</label>
            <textarea name="requirements" rows="4" class="w-full rounded-xl border-slate-200">{{ old('requirements', $isEdit ? $internship->requirements : '') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-bold mb-1">المميزات / ما ستتعلمه</label>
            <textarea name="benefits" rows="4" class="w-full rounded-xl border-slate-200">{{ old('benefits', $isEdit ? $internship->benefits : '') }}</textarea>
        </div>

        <div class="flex flex-wrap gap-4">
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_published" value="1" class="rounded border-slate-300 text-sky-600" @checked(old('is_published', $isEdit ? $internship->is_published : false))>
                نشر في الصفحة العامة (يتطلب الحالة: مفتوحة)
            </label>
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_featured" value="1" class="rounded border-slate-300 text-amber-500" @checked(old('is_featured', $isEdit ? $internship->is_featured : false))>
                Featured
            </label>
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
            <button class="px-5 py-2.5 rounded-xl bg-sky-600 text-white font-semibold">{{ $isEdit ? 'حفظ التعديلات' : 'إنشاء الفرصة' }}</button>
            @if($isEdit)
                <button form="delete-form" class="px-5 py-2.5 rounded-xl bg-rose-600 text-white font-semibold" onclick="return confirm('حذف فرصة التدريب وكل الطلبات المرتبطة؟')">حذف</button>
            @endif
        </div>
    </form>

    @if($isEdit)
        <form id="delete-form" method="POST" action="{{ route('admin.internships.destroy', $internship) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
        <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-600">
            عدد الطلبات: <strong>{{ $internship->applications_count ?? $internship->applications()->count() }}</strong>
            ·
            <a href="{{ route('admin.internship-applications.index', ['internship_id' => $internship->id]) }}" class="text-sky-700 font-semibold hover:underline">عرض الطلبات</a>
        </div>
    @endif
</div>
@endsection
