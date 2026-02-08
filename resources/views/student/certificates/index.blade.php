@extends('layouts.app')

@section('title', 'شهاداتي')
@section('header', 'شهاداتي')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">شهاداتي</h1>
        <p class="text-sm text-gray-500">الشهادات التي حصلت عليها من إتمام الكورسات</p>
    </div>

    @if(isset($stats))
    <div class="grid grid-cols-2 gap-3 sm:gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">إجمالي الشهادات</p>
                    <p class="text-2xl font-bold text-sky-600 leading-none">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-sky-100 flex items-center justify-center text-sky-600 flex-shrink-0">
                    <i class="fas fa-certificate"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">المُصدرة</p>
                    <p class="text-2xl font-bold text-emerald-600 leading-none">{{ $stats['issued'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
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
                    {{ $certificate->title ?? $certificate->course_name ?? 'شهادة الإتمام' }}
                </h3>
                @if($certificate->course)
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $certificate->course->title }}</p>
                @endif
                <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500 mb-3">
                    <span><i class="fas fa-calendar text-sky-500 ml-1"></i>{{ ($certificate->issued_at ? $certificate->issued_at->format('Y-m-d') : ($certificate->issue_date ? $certificate->issue_date->format('Y-m-d') : '-')) }}</span>
                    @if($certificate->certificate_number)
                    <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">#{{ substr($certificate->certificate_number, -6) }}</span>
                    @endif
                </div>
                <span class="inline-flex items-center gap-2 text-sky-600 font-semibold text-sm">
                    عرض الشهادة <i class="fas fa-arrow-left"></i>
                </span>
            </div>
        </a>
        @endforeach
    </div>
    @if($certificates->hasPages())
    <div class="flex justify-center">{{ $certificates->links() }}</div>
    @endif
    @else
    <div class="rounded-xl p-10 sm:p-12 text-center bg-gray-50 border border-dashed border-gray-200">
        <div class="w-16 h-16 bg-sky-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-sky-600">
            <i class="fas fa-certificate text-2xl"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">لا توجد شهادات</h3>
        <p class="text-sm text-gray-500 mb-6 max-w-sm mx-auto">لم يتم إصدار أي شهادات لك حتى الآن. أكمل الكورسات للحصول على شهادات الإتمام.</p>
        <a href="{{ route('my-courses.index') }}" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors">
            <i class="fas fa-book-open"></i> عرض كورساتي
        </a>
    </div>
    @endif
</div>
@endsection
