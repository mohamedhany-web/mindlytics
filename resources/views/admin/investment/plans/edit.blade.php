@extends('layouts.admin')

@section('title', 'تعديل خطة — ' . $plan->title)
@section('header', 'تعديل الخطة')

@section('content')
@include('admin.investment._styles')

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.investment._alerts')
    @include('admin.investment._nav', ['active' => 'plans'])
    @include('admin.investment._header', ['title' => 'تعديل: ' . $plan->title, 'subtitle' => $plan->planTypeLabel() . ' · ' . $plan->riskLevelLabel(), 'icon' => 'fas fa-edit'])

    <section class="{{ $invSectionClass }}">
        @include('admin.investment._section-head', [
            'icon' => 'fas fa-edit',
            'title' => 'تعديل بيانات الخطة',
        ])
        <form method="POST" action="{{ route('admin.investment.plans.update', $plan) }}" class="p-6 space-y-5">
            @csrf @method('PUT')
            @include('admin.investment.plans._form')
            <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-200">
                <button type="submit" class="{{ $invBtnPrimary }}"><i class="fas fa-save"></i> حفظ</button>
                <a href="{{ route('admin.investment.plans.show', $plan) }}" class="{{ $invBtnSecondary }}">عرض</a>
            </div>
        </form>
    </section>
</div>
@endsection
