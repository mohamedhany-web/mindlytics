@extends('layouts.admin')

@section('title', 'تعديل منحة')
@section('header', 'تعديل منحة')

@section('content')
@include('admin.scholarships._styles')

<div class="space-y-6">
    @include('admin.scholarships._alerts')
    @include('admin.scholarships._nav', ['active' => 'programs'])

    @include('admin.scholarships._header', [
        'title' => 'تعديل: ' . $program->name,
        'subtitle' => 'تحديث بيانات المنحة والمدرب ومواعيد التسجيل',
        'icon' => 'fas fa-edit',
    ])

    <section class="{{ $schSectionClass }} max-w-3xl">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900">بيانات المنحة</h3>
        </div>
        <form method="POST" action="{{ route('admin.scholarships.programs.update', $program) }}" class="p-6 space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="{{ $schLabelClass }}"><i class="fas fa-award text-blue-600 text-sm"></i> اسم المنحة *</label>
                <input type="text" name="name" value="{{ old('name', $program->name) }}" required class="{{ $schInputClass }}">
            </div>
            <div>
                <label class="{{ $schLabelClass }}"><i class="fas fa-align-right text-blue-600 text-sm"></i> الوصف</label>
                <textarea name="description" rows="4" class="{{ $schTextareaClass }}">{{ old('description', $program->description) }}</textarea>
            </div>
            <div>
                <label class="{{ $schLabelClass }}"><i class="fas fa-chalkboard-teacher text-blue-600 text-sm"></i> المدرب *</label>
                <select name="instructor_id" required class="{{ $schSelectClass }}">
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" @selected(old('instructor_id', $program->instructor_id) == $instructor->id)>{{ $instructor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="{{ $schLabelClass }}">بداية التسجيل</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($program->starts_at)->format('Y-m-d\TH:i')) }}" class="{{ $schInputClass }}">
                </div>
                <div>
                    <label class="{{ $schLabelClass }}">نهاية التسجيل</label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($program->ends_at)->format('Y-m-d\TH:i')) }}" class="{{ $schInputClass }}">
                </div>
            </div>
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $program->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                <span class="text-sm text-slate-700 font-medium">المنحة نشطة</span>
            </label>
            <div class="flex flex-wrap gap-3 pt-2 border-t border-slate-200">
                <button type="submit" class="{{ $schBtnPrimary }}"><i class="fas fa-save"></i><span>حفظ التعديلات</span></button>
                <a href="{{ route('admin.scholarships.programs.show', $program) }}" class="{{ $schBtnSecondary }}">رجوع</a>
            </div>
        </form>
    </section>
</div>
@endsection
