@extends('layouts.admin')

@section('title', 'وظيفة جديدة — HR')
@section('header', 'وظيفة جديدة — HR')

@section('content')

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.hr._nav', ['active' => 'jobs'])
    @include('admin.hr._alerts')

    @include('admin.hr._page-header', [
        'title' => 'وظيفة جديدة',
        'subtitle' => 'أنشئ الوظيفة ثم انشرها لتظهر في صفحة التوظيف العامة.',
        'icon' => 'fas fa-plus-circle',
        'actions' => '<a href="' . route('admin.hr.jobs.index') . '" class="' . $hrBtnSecondary . '"><i class="fas fa-arrow-right"></i> رجوع للقائمة</a>',
    ])

    <section class="{{ $hrSectionClass }} max-w-4xl">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/80">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-edit text-pink-600"></i>
                بيانات الوظيفة
            </h3>
        </div>
        <form method="post" action="{{ route('admin.hr.jobs.store') }}" class="p-5 sm:p-6 space-y-6">
            @csrf
            @include('admin.hr.jobs._form')

            <div class="flex flex-wrap items-center gap-2 pt-4 border-t border-slate-200">
                <button type="submit" class="{{ $hrBtnPrimary }}">
                    <i class="fas fa-save"></i>
                    إنشاء الوظيفة
                </button>
                <a href="{{ route('admin.hr.jobs.index') }}" class="{{ $hrBtnSecondary }}">إلغاء</a>
            </div>
        </form>
    </section>
</div>
@endsection
