@extends('layouts.admin')

@section('title', 'منحة جديدة')
@section('header', 'إنشاء منحة')

@section('content')
@include('admin.scholarships._styles')

<div class="w-full space-y-6">
    @include('admin.scholarships._alerts')

    @include('admin.scholarships._header', [
        'title' => 'إنشاء منحة جديدة',
        'subtitle' => 'سيتم إنشاء كورس معزول تلقائياً ورابط تسجيل خاص',
        'icon' => 'fas fa-plus-circle',
        'actions' => '<a href="' . route('admin.scholarships.programs.index') . '" class="' . $schBtnSecondary . '"><i class="fas fa-arrow-right"></i><span>رجوع للمنح</span></a>',
    ])

    <section class="{{ $schSectionClass }} w-full">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900">بيانات المنحة</h3>
        </div>
        <form method="POST" action="{{ route('admin.scholarships.programs.store') }}" class="p-6 space-y-5">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div>
                    <label class="{{ $schLabelClass }}"><i class="fas fa-award text-blue-600 text-sm"></i> اسم المنحة *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="{{ $schInputClass }}">
                    @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="{{ $schLabelClass }}"><i class="fas fa-chalkboard-teacher text-blue-600 text-sm"></i> المدرب *</label>
                    <select name="instructor_id" required class="{{ $schSelectClass }}">
                        <option value="">اختر المدرب</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" @selected(old('instructor_id') == $instructor->id)>{{ $instructor->name }} — {{ $instructor->email }}</option>
                        @endforeach
                    </select>
                    @error('instructor_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="lg:col-span-2">
                    <label class="{{ $schLabelClass }}"><i class="fas fa-align-right text-blue-600 text-sm"></i> الوصف (للمدرب)</label>
                    <textarea name="description" rows="4" class="{{ $schTextareaClass }}">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="{{ $schLabelClass }}"><i class="fas fa-calendar text-blue-600 text-sm"></i> بداية التسجيل</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" class="{{ $schInputClass }}">
                </div>
                <div>
                    <label class="{{ $schLabelClass }}"><i class="fas fa-calendar text-blue-600 text-sm"></i> نهاية التسجيل</label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}" class="{{ $schInputClass }}">
                </div>
            </div>
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                <span class="text-sm text-slate-700 font-medium">المنحة نشطة</span>
            </label>
            <div class="flex flex-wrap gap-3 pt-2 border-t border-slate-200">
                <button type="submit" class="{{ $schBtnPrimary }}"><i class="fas fa-check"></i><span>إنشاء المنحة</span></button>
                <a href="{{ route('admin.scholarships.programs.index') }}" class="{{ $schBtnSecondary }}">إلغاء</a>
            </div>
        </form>
    </section>
</div>
@endsection
