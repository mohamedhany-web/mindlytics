@extends('layouts.app')

@section('title', __('student.my_certificates_title'))
@section('header', __('student.my_certificates_title'))

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">{{ __('student.my_certificates_title') }}</h1>
        <p class="text-sm text-gray-500">بعد إكمال أي كورس 100٪ تقدر تختار تصميم الشهادة وتنزلها باسمك مع رقم تسلسلي موثّق.</p>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    @if(isset($stats))
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('student.total_certificates') }}</p>
            <p class="text-2xl font-bold text-sky-600 leading-none mt-1">{{ $stats['total'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('student.issued_label') }}</p>
            <p class="text-2xl font-bold text-emerald-600 leading-none mt-1">{{ $stats['issued'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm col-span-2 sm:col-span-1">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">جاهزة للإصدار</p>
            <p class="text-2xl font-bold text-amber-600 leading-none mt-1">{{ $stats['ready'] ?? 0 }}</p>
        </div>
    </div>
    @endif

    @if(isset($eligible) && $eligible->isNotEmpty())
    <section class="bg-gradient-to-br from-emerald-50 to-white rounded-2xl border border-emerald-200 p-5 shadow-sm">
        <h2 class="text-base font-black text-emerald-950 mb-1">كورسات مكتملة — اختر شهادتك</h2>
        <p class="text-sm text-emerald-800/80 mb-4">أنجزت هذه الكورسات 100٪. اختر التصميم لإصدار شهادة برقم تسلسلي يمكن التحقق منه.</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($eligible as $enrollment)
                <div class="bg-white rounded-xl border border-emerald-100 p-4 flex flex-col gap-3">
                    <div>
                        <p class="font-bold text-slate-900 line-clamp-2">{{ $enrollment->course?->title ?? 'كورس' }}</p>
                        <p class="text-xs text-slate-500 mt-1">إنجاز {{ rtrim(rtrim(number_format((float) $enrollment->progress, 2), '0'), '.') }}٪</p>
                    </div>
                    <a href="{{ route('student.certificates.claim', $enrollment->advanced_course_id) }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-4 py-2.5">
                        <i class="fas fa-certificate"></i> اختيار التصميم وإصدار الشهادة
                    </a>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    @if(isset($certificates) && $certificates->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($certificates as $certificate)
        <a href="{{ route('student.certificates.show', $certificate) }}" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md hover:border-sky-200 transition-all block">
            <div class="p-4 sm:p-5">
                <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600 mb-3">
                    <i class="fas fa-certificate text-xl"></i>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2 leading-snug">
                    {{ $certificate->title ?? $certificate->course_name ?? __('student.completion_certificate') }}
                </h3>
                @if($certificate->course)
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $certificate->course->title }}</p>
                @endif
                <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500 mb-3">
                    <span><i class="fas fa-calendar text-sky-500 ml-1"></i>{{ ($certificate->issued_at ? $certificate->issued_at->format('Y-m-d') : ($certificate->issue_date ? $certificate->issue_date->format('Y-m-d') : '-')) }}</span>
                    @if($certificate->serial_number)
                    <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">{{ $certificate->serial_number }}</span>
                    @elseif($certificate->certificate_number)
                    <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">#{{ substr($certificate->certificate_number, -6) }}</span>
                    @endif
                </div>
                <span class="inline-flex items-center gap-2 text-sky-600 font-semibold text-sm">
                    {{ __('student.view_certificate') }} <i class="fas fa-arrow-left"></i>
                </span>
            </div>
        </a>
        @endforeach
    </div>
    @if($certificates->hasPages())
    <div class="flex justify-center">{{ $certificates->links() }}</div>
    @endif
    @elseif(!isset($eligible) || $eligible->isEmpty())
    <div class="rounded-xl p-10 sm:p-12 text-center bg-gray-50 border border-dashed border-gray-200">
        <div class="w-16 h-16 bg-sky-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-sky-600">
            <i class="fas fa-certificate text-2xl"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('student.no_certificates') }}</h3>
        <p class="text-sm text-gray-500 mb-6 max-w-sm mx-auto">أكمل أي كورس بنسبة 100٪ ثم ارجع هنا لاختيار تصميم الشهادة.</p>
        <a href="{{ route('my-courses.index') }}" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors">
            <i class="fas fa-book-open"></i> {{ __('student.view_my_courses') }}
        </a>
    </div>
    @endif
</div>
@endsection
