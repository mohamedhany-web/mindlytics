@extends('layouts.admin')

@section('title', 'خطة استثمارية جديدة')
@section('header', 'خطة جديدة')

@section('content')
@include('investment::admin._styles')

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('investment::admin._alerts')
    @include('investment::admin._nav', ['active' => 'plans'])
    @include('investment::admin._header', ['title' => 'إنشاء خطة استثمارية', 'subtitle' => 'حدد الشروط، العوائد، والإطار القانوني للخطة', 'icon' => 'fas fa-plus-circle'])

    <section class="{{ $invSectionClass }}">
        @include('investment::admin._section-head', [
            'icon' => 'fas fa-edit',
            'title' => 'بيانات الخطة الاستثمارية',
            'subtitle' => 'جميع الحقول المطلوبة لعرض الفرصة في الصفحة العامة',
        ])
        <form method="POST" action="{{ route('admin.investment.plans.store') }}" class="p-6 space-y-5">
            @csrf
            @include('investment::admin.plans._form')
            <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-200">
                <button type="submit" class="{{ $invBtnPrimary }}"><i class="fas fa-check"></i> حفظ الخطة</button>
                <a href="{{ route('admin.investment.plans.index') }}" class="{{ $invBtnSecondary }}">إلغاء</a>
            </div>
        </form>
    </section>
</div>
@endsection
