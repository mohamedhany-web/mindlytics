@extends('layouts.student-dashboard')

@section('title', __('student.mobile_app_title'))

@section('page_heading')
    {{ __('student.mobile_app_title') }}
@endsection

@php
    use App\Support\StudentFigmaAssets;
    $sp = StudentFigmaAssets::urls();
@endphp

@section('content')
<div class="max-w-3xl mx-auto">
    <section class="sp-card overflow-hidden">
        <div class="sp-promo !min-h-[280px] !rounded-none !rounded-t-[20px] flex items-center">
            <div class="sp-promo-copy py-2">
                <p class="m-0 text-sm font-bold text-[var(--sp-accent)]">Mindlytics</p>
                <h2 class="m-0 mt-3 text-2xl sm:text-3xl font-extrabold">{{ __('student.mobile_app_heading') }}</h2>
                <p class="m-0 mt-3 text-sm sm:text-base text-white/80 leading-relaxed max-w-md">{{ __('student.mobile_app_desc') }}</p>
            </div>
            <div class="sp-promo-art hidden sm:block">
                <img src="{{ $sp['promo'] }}?v=3" alt="" width="160" height="190">
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <div class="flex items-start gap-4">
                <span class="sp-icon-bubble shrink-0" style="background:var(--sp-accent)">
                    <x-student.figma-icon name="icon-notifications.svg" />
                </span>
                <div class="min-w-0">
                    <h3 class="sp-section-title m-0">{{ __('student.mobile_app_soon_title') }}</h3>
                    <p class="text-sm text-[var(--sp-muted)] mt-2 mb-0 leading-relaxed">{{ __('student.mobile_app_soon_body') }}</p>
                </div>
            </div>

            <div class="rounded-[20px] bg-[#f7f7f5] p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1 min-w-0">
                    <p class="font-extrabold m-0 text-[var(--sp-text)]">{{ __('student.mobile_app_notify') }}</p>
                    <p class="text-sm text-[var(--sp-muted)] mt-1 mb-0">{{ __('student.mobile_app_notify_hint') }}</p>
                </div>
                <a href="{{ route('notifications') }}" class="sp-promo-btn !mt-0 self-start shrink-0">{{ __('student.notifications') }}</a>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2.5 text-sm font-extrabold shadow-sm text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition">
                    <x-student.figma-icon name="icon-dashboard.svg" box="size-5" />
                    {{ __('student.back_to_dashboard') }}
                </a>
                <a href="{{ route('my-courses.index') }}" class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2.5 text-sm font-extrabold shadow-sm text-[var(--sp-text)] hover:bg-[#f0f0ec] transition">
                    <x-student.figma-icon name="icon-courses.svg" box="size-5" />
                    {{ __('student.my_courses') }}
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
