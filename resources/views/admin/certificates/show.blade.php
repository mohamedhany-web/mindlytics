@extends('layouts.admin')

@section('title', 'تفاصيل الشهادة')
@section('header', 'تفاصيل الشهادة')

@section('content')
@php
    $activeTemplate = $certificate->template ?: ($branding->default_template ?? 'emerald-classic');
    if ($activeTemplate === 'econev' || ! array_key_exists($activeTemplate, $templates)) {
        $activeTemplate = array_key_exists('emerald-classic', $templates) ? 'emerald-classic' : array_key_first($templates);
    }
    $serial = $certificate->serial_number ?? $certificate->certificate_number;
@endphp
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 no-print">
        <div class="flex justify-between items-start mb-6 gap-4 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">شهادة #{{ $certificate->certificate_number }}</h1>
                <p class="text-gray-600 mt-1">تاريخ الإنشاء: {{ $certificate->created_at->format('Y-m-d') }}</p>
                @if($serial)
                    <p class="mt-2 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-900 text-white text-sm font-mono">
                        <i class="fas fa-barcode"></i>
                        Serial: {{ $serial }}
                    </p>
                @endif
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('admin.certificates.branding') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium">هوية الشهادات</a>
                <a href="{{ route('admin.certificates.edit', $certificate) }}" class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg font-medium">تعديل</a>
                <a href="{{ route('admin.certificates.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-medium">رجوع</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-gray-50 rounded-xl p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">معلومات الطالب</h3>
                <div class="space-y-2 text-sm">
                    <div><span class="text-gray-600">الاسم:</span> <span class="font-medium text-gray-900 mr-2">{{ $certificate->user->name ?? 'غير معروف' }}</span></div>
                    <div><span class="text-gray-600">البريد:</span> <span class="font-medium text-gray-900 mr-2">{{ $certificate->user->email ?? '-' }}</span></div>
                    <div><span class="text-gray-600">الهاتف:</span> <span class="font-medium text-gray-900 mr-2">{{ $certificate->user->phone ?? '-' }}</span></div>
                </div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">معلومات الشهادة</h3>
                <div class="space-y-2 text-sm">
                    <div><span class="text-gray-600">العنوان:</span> <span class="font-medium text-gray-900 mr-2">{{ $certificate->title ?? $certificate->course_name ?? '-' }}</span></div>
                    <div><span class="text-gray-600">الكورس:</span> <span class="font-medium text-gray-900 mr-2">{{ $certificate->course->title ?? ($certificate->course_name ?? '-') }}</span></div>
                    <div><span class="text-gray-600">التصميم:</span> <span class="font-medium text-gray-900 mr-2">{{ $templates[$activeTemplate]['name'] ?? $activeTemplate }}</span></div>
                    <div><span class="text-gray-600">السيريال:</span> <span class="font-medium text-gray-900 mr-2 font-mono">{{ $serial ?? '-' }}</span></div>
                    <div><span class="text-gray-600">تاريخ الإصدار:</span> <span class="font-medium text-gray-900 mr-2">{{ ($certificate->issued_at ? $certificate->issued_at->format('Y-m-d') : ($certificate->issue_date ? $certificate->issue_date->format('Y-m-d') : '-')) }}</span></div>
                </div>
            </div>
        </div>

        @if($certificate->description)
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">الوصف</h3>
                <p class="text-gray-600">{{ $certificate->description }}</p>
            </div>
        @endif
    </div>

    <div class="rounded-xl border border-gray-200 bg-slate-900 p-4 overflow-auto">
        @include('components.certificate-templates', [
            'certificate' => $certificate,
            'branding' => $branding,
            'template' => $activeTemplate,
            'templateDomId' => 'certificate-template',
            'studentName' => data_get($certificate->metadata, 'display_name') ?: ($certificate->user->name ?? 'Student'),
        ])
    </div>

    <div class="text-center flex flex-wrap justify-center gap-3 no-print">
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold">
            <i class="fas fa-print"></i> طباعة الشهادة
        </button>
        <button type="button" onclick="downloadCertificate()" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-semibold">
            <i class="fas fa-download"></i> تحميل PDF
        </button>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function downloadCertificate() {
    const element = document.getElementById('certificate-template');
    if (!element) { window.print(); return; }
    html2pdf().set({
        margin: 0,
        filename: 'certificate-{{ $serial }}.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true, allowTaint: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
    }).from(element).save();
}
</script>
@endpush

@push('styles')
<style>
@media print {
    body * { visibility: hidden !important; }
    #certificate-template, #certificate-template * { visibility: visible !important; }
    #certificate-template { position: absolute; left: 0; top: 0; }
    .no-print { display: none !important; }
}
</style>
@endpush
@endsection
