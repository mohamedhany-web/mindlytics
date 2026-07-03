@extends('layouts.admin')

@section('title', 'الإطار القانوني والسياسات')
@section('header', 'الإطار القانوني')

@section('content')
@include('admin.investment._styles')

@php
    $contactEmail = old('contact_email', $policy->contact_email ?: ($platformContact['email'] ?? ''));
    $contactPhone = old('contact_phone', $policy->contact_phone ?: ($platformContact['phone'] ?? ''));
@endphp

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.investment._alerts')

    @include('admin.investment._header', [
        'title' => 'الإطار القانوني والسياسات',
        'subtitle' => 'القوانين، الشروط، آلية الاستثمار، وإخلاء المسؤولية — تظهر في الصفحة العامة',
        'icon' => 'fas fa-gavel',
    ])

    @include('admin.investment._nav', ['active' => 'policies'])

    <section class="{{ $invSectionClass }}">
        @include('admin.investment._section-head', [
            'icon' => 'fas fa-gavel',
            'title' => 'محتوى السياسات والإطار القانوني',
            'subtitle' => 'راجع النصوص مع المستشار القانوني قبل النشر',
        ])
        <form method="POST" action="{{ route('admin.investment.policies.update') }}" class="p-6 space-y-5">
            @csrf @method('PUT')
            @foreach([
                'overview' => 'نظرة عامة على قسم الاستثمار',
                'eligibility_rules' => 'قواعد الأهلية',
                'legal_framework' => 'الإطار القانوني والامتثال',
                'terms_conditions' => 'الشروط والأحكام',
                'privacy_notice' => 'خصوصية بيانات المستثمرين',
                'process_description' => 'كيفية الاستثمار (الخطوات)',
                'disclaimer' => 'إخلاء المسؤولية',
            ] as $field => $label)
                <div>
                    <label class="{{ $invLabelClass }}">{{ $label }}</label>
                    <textarea name="{{ $field }}" rows="5" class="{{ $invTextareaClass }}">{{ old($field, $policy->$field) }}</textarea>
                </div>
            @endforeach
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-slate-200">
                <div>
                    <label class="{{ $invLabelClass }}">بريد التواصل</label>
                    <input type="email" name="contact_email" value="{{ $contactEmail }}" class="{{ $invInputClass }} dir-ltr" placeholder="info@mindlytics-academy.com">
                    <p class="text-xs text-slate-500 mt-1">يُستخدم في الصفحة العامة للاستفسارات</p>
                </div>
                <div>
                    <label class="{{ $invLabelClass }}">هاتف التواصل</label>
                    <input type="text" name="contact_phone" value="{{ $contactPhone }}" class="{{ $invInputClass }} dir-ltr" placeholder="01044610507">
                </div>
            </div>
            <button type="submit" class="{{ $invBtnPrimary }}"><i class="fas fa-save"></i> حفظ السياسات</button>
        </form>
    </section>
</div>
@endsection
