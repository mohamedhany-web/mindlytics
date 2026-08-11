@php
    $isEdit = isset($internship);
    $action = $isEdit ? route('admin.internships.update', $internship) : route('admin.internships.store');
    $inputClass = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-500/70 focus:border-cyan-500';
    $labelClass = 'block text-sm font-semibold text-slate-800 mb-1.5';
@endphp

@extends('layouts.admin')

@section('title', $isEdit ? 'تعديل فرصة تدريب' : 'فرصة تدريب جديدة')
@section('header', $isEdit ? 'تعديل فرصة تدريب' : 'فرصة تدريب جديدة')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-6" style="background: #f8fafc; min-height: 100%;">
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fas {{ $isEdit ? 'fa-edit' : 'fa-plus-circle' }} text-cyan-600"></i>
                <span>{{ $isEdit ? 'تعديل فرصة تدريب' : 'إنشاء فرصة تدريب جديدة' }}</span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                {{ $isEdit ? 'حدّث بيانات الفرصة والنشر والموعد النهائي للتقديم.' : 'أضف فرصة تدريب لتظهر في الصفحة العامة ويستقبل النظام طلبات التقديم.' }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.internships.index') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 shadow-sm">
                <i class="fas fa-arrow-right"></i>
                <span>رجوع للقائمة</span>
            </a>
            @if($isEdit)
                <a href="{{ route('public.internships.show', $internship->slug) }}" target="_blank"
                   class="inline-flex items-center gap-2 rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-2.5 text-sm font-semibold text-cyan-800 hover:bg-cyan-100">
                    <i class="fas fa-external-link-alt"></i>
                    <span>معاينة عامة</span>
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800 text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-rose-800 text-sm">
            <div class="font-bold mb-1 flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> يرجى تصحيح الأخطاء التالية:</div>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($isEdit)
        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm flex flex-wrap items-center justify-between gap-3">
            <div class="text-sm text-slate-600">
                عدد الطلبات:
                <strong class="text-slate-900">{{ $internship->applications_count ?? $internship->applications()->count() }}</strong>
            </div>
            <a href="{{ route('admin.internship-applications.index', ['internship_id' => $internship->id]) }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-cyan-700 hover:text-cyan-900">
                <i class="fas fa-inbox"></i>
                عرض طلبات هذه الفرصة
            </a>
        </div>
    @endif

    <form method="POST" action="{{ $action }}" class="space-y-6">
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- البيانات الأساسية --}}
        <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 bg-slate-50/80">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-info-circle text-cyan-600"></i>
                    البيانات الأساسية
                </h3>
            </div>
            <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="{{ $labelClass }}">عنوان الفرصة <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $isEdit ? $internship->title : '') }}" required class="{{ $inputClass }}" placeholder="مثال: Frontend Development Internship">
                </div>
                <div>
                    <label class="{{ $labelClass }}">الرابط (slug)</label>
                    <input type="text" name="slug" value="{{ old('slug', $isEdit ? $internship->slug : '') }}" dir="ltr" class="{{ $inputClass }}" placeholder="auto-from-title">
                    <p class="text-xs text-slate-500 mt-1">اتركه فارغاً ليُنشأ تلقائياً من العنوان.</p>
                </div>
                <div>
                    <label class="{{ $labelClass }}">القسم / التخصص</label>
                    <input type="text" name="department" value="{{ old('department', $isEdit ? $internship->department : '') }}" class="{{ $inputClass }}" placeholder="Frontend / Data / Marketing">
                </div>
                <div>
                    <label class="{{ $labelClass }}">نوع الحضور <span class="text-rose-500">*</span></label>
                    <select name="type" required class="{{ $inputClass }}">
                        @foreach(\App\Models\Internship::types() as $key => $label)
                            <option value="{{ $key }}" @selected(old('type', $isEdit ? $internship->type : 'onsite') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $labelClass }}">الحالة <span class="text-rose-500">*</span></label>
                    <select name="status" required class="{{ $inputClass }}">
                        @foreach(\App\Models\Internship::statuses() as $key => $label)
                            <option value="{{ $key }}" @selected(old('status', $isEdit ? $internship->status : 'draft') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $labelClass }}">الموقع</label>
                    <input type="text" name="location" value="{{ old('location', $isEdit ? $internship->location : '') }}" class="{{ $inputClass }}" placeholder="القاهرة / Hybrid / Remote">
                </div>
                <div>
                    <label class="{{ $labelClass }}">المدة</label>
                    <input type="text" name="duration" value="{{ old('duration', $isEdit ? $internship->duration : '') }}" class="{{ $inputClass }}" placeholder="3 أشهر">
                </div>
                <div>
                    <label class="{{ $labelClass }}">عدد المقاعد</label>
                    <input type="number" name="seats" min="1" value="{{ old('seats', $isEdit ? $internship->seats : '') }}" class="{{ $inputClass }}" placeholder="اختياري">
                </div>
                <div>
                    <label class="{{ $labelClass }}">ترتيب العرض</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $isEdit ? $internship->sort_order : 0) }}" class="{{ $inputClass }}">
                </div>
            </div>
        </section>

        {{-- التواريخ --}}
        <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 bg-slate-50/80">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-cyan-600"></i>
                    التواريخ والمواعيد
                </h3>
            </div>
            <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="{{ $labelClass }}">تاريخ البداية</label>
                    <input type="date" name="starts_at" value="{{ old('starts_at', $isEdit && $internship->starts_at ? $internship->starts_at->format('Y-m-d') : '') }}" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}">تاريخ النهاية</label>
                    <input type="date" name="ends_at" value="{{ old('ends_at', $isEdit && $internship->ends_at ? $internship->ends_at->format('Y-m-d') : '') }}" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}">آخر موعد للتقديم</label>
                    <input type="date" name="application_deadline" value="{{ old('application_deadline', $isEdit && $internship->application_deadline ? $internship->application_deadline->format('Y-m-d') : '') }}" class="{{ $inputClass }}">
                </div>
            </div>
        </section>

        {{-- المحتوى --}}
        <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 bg-slate-50/80">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-align-right text-cyan-600"></i>
                    المحتوى والتفاصيل
                </h3>
            </div>
            <div class="p-5 sm:p-6 space-y-5">
                <div>
                    <label class="{{ $labelClass }}">ملخص قصير</label>
                    <textarea name="summary" rows="2" class="{{ $inputClass }}" placeholder="سطر أو سطرين يظهران في بطاقة الفرصة">{{ old('summary', $isEdit ? $internship->summary : '') }}</textarea>
                </div>
                <div>
                    <label class="{{ $labelClass }}">الوصف</label>
                    <textarea name="description" rows="5" class="{{ $inputClass }}" placeholder="تفاصيل البرنامج، المهام، أسلوب العمل...">{{ old('description', $isEdit ? $internship->description : '') }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="{{ $labelClass }}">المتطلبات</label>
                        <textarea name="requirements" rows="5" class="{{ $inputClass }}" placeholder="مهارة لكل سطر">{{ old('requirements', $isEdit ? $internship->requirements : '') }}</textarea>
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">المميزات / ما ستتعلمه</label>
                        <textarea name="benefits" rows="5" class="{{ $inputClass }}" placeholder="ميزة لكل سطر">{{ old('benefits', $isEdit ? $internship->benefits : '') }}</textarea>
                    </div>
                </div>
            </div>
        </section>

        {{-- النشر --}}
        <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 bg-slate-50/80">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-globe text-cyan-600"></i>
                    النشر والعرض
                </h3>
            </div>
            <div class="p-5 sm:p-6 space-y-4">
                <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-3 cursor-pointer hover:bg-slate-50">
                    <input type="checkbox" name="is_published" value="1" class="mt-1 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" @checked(old('is_published', $isEdit ? $internship->is_published : false))>
                    <span>
                        <span class="block text-sm font-semibold text-slate-800">نشر في الصفحة العامة</span>
                        <span class="block text-xs text-slate-500 mt-0.5">يتطلب أن تكون الحالة «مفتوحة للتقديم».</span>
                    </span>
                </label>
                <label class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50/50 px-4 py-3 cursor-pointer hover:bg-amber-50">
                    <input type="checkbox" name="is_featured" value="1" class="mt-1 rounded border-slate-300 text-amber-500 focus:ring-amber-500" @checked(old('is_featured', $isEdit ? $internship->is_featured : false))>
                    <span>
                        <span class="block text-sm font-semibold text-slate-800">تمييز الفرصة (Featured)</span>
                        <span class="block text-xs text-slate-500 mt-0.5">تظهر في أعلى قائمة الفرص العامة.</span>
                    </span>
                </label>
            </div>
        </section>

        <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
            <a href="{{ route('admin.internships.index') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 shadow-sm">
                <i class="fas fa-arrow-right"></i>
                <span>إلغاء</span>
            </a>
            <div class="flex flex-wrap gap-2">
                @if($isEdit)
                    <button type="submit" form="delete-form"
                            class="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-700 hover:bg-rose-100"
                            onclick="return confirm('حذف فرصة التدريب وكل الطلبات المرتبطة؟')">
                        <i class="fas fa-trash"></i>
                        <span>حذف</span>
                    </button>
                @endif
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 px-6 py-2.5 text-sm font-semibold text-white shadow-lg hover:shadow-xl">
                    <i class="fas fa-save"></i>
                    <span>{{ $isEdit ? 'حفظ التعديلات' : 'إنشاء الفرصة' }}</span>
                </button>
            </div>
        </div>
    </form>

    @if($isEdit)
        <form id="delete-form" method="POST" action="{{ route('admin.internships.destroy', $internship) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif
</div>
@endsection
