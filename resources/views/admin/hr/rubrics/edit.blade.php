@extends('layouts.admin')

@section('title', 'تعديل قالب تقييم — HR')
@section('header', 'تعديل قالب تقييم — HR')

@section('content')

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.hr._nav', ['active' => 'rubrics'])
    @include('admin.hr._alerts')

    @include('admin.hr._page-header', [
        'title' => 'تعديل قالب تقييم',
        'subtitle' => 'القالب: ' . $rubric->name,
        'icon' => 'fas fa-star-half-alt',
        'actions' => '
            <a href="' . route('admin.hr.rubrics.index') . '" class="' . $hrBtnSecondary . '"><i class="fas fa-arrow-right"></i> رجوع</a>
            <form method="post" action="' . route('admin.hr.rubrics.destroy', $rubric) . '" onsubmit="return confirm(\'حذف القالب؟\');" class="inline">
                ' . csrf_field() . method_field('DELETE') . '
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border-2 border-rose-200 bg-rose-50 text-rose-700 text-sm font-semibold hover:bg-rose-100 transition-all">
                    <i class="fas fa-trash"></i> حذف
                </button>
            </form>
        ',
    ])

    <section class="{{ $hrSectionClass }} max-w-4xl">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/80">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-code text-pink-600"></i>
                بيانات القالب
            </h3>
        </div>
        <form method="post" action="{{ route('admin.hr.rubrics.update', $rubric) }}" class="p-5 sm:p-6 space-y-6">
            @csrf @method('PUT')
            @include('admin.hr.rubrics._form', ['rubric' => $rubric])

            <div class="flex flex-wrap items-center gap-2 pt-4 border-t border-slate-200">
                <button type="submit" class="{{ $hrBtnPrimary }}">
                    <i class="fas fa-save"></i>
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
