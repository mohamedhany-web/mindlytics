@extends('layouts.app')

@section('title', 'الشهادة')
@section('header', 'الشهادة')

@push('styles')
<style>
    @media print {
        body * { visibility: hidden; }
        .certificate-print-area, .certificate-print-area * { visibility: visible; }
        .certificate-print-area { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
    }
    .certificate-scale-wrap { overflow: auto; background: #1e2225; border-radius: 1rem; padding: 1rem; }
</style>
@endpush

@section('content')
@php
    $serial = $certificate->serial_number ?? $certificate->certificate_number;
    $verifyUrl = route('public.certificates.verify', ['code' => $serial]);
@endphp
<div class="max-w-6xl mx-auto space-y-6 py-6 px-4">
    @if(session('success'))
        <div class="no-print rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow p-5 border border-gray-200 no-print flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $certificate->title ?? 'شهادة إتمام' }}</h1>
            <p class="text-sm text-gray-600 mt-1 font-mono">Serial: {{ $serial }}</p>
            <p class="text-xs text-slate-500 mt-1">التحقق:
                <a href="{{ $verifyUrl }}" target="_blank" class="text-sky-600 underline break-all">{{ $verifyUrl }}</a>
            </p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold"><i class="fas fa-print ml-1"></i>طباعة</button>
            <button onclick="downloadCertificate()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg font-semibold"><i class="fas fa-download ml-1"></i>PDF</button>
            <a href="{{ route('student.certificates.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg font-semibold">رجوع</a>
        </div>
    </div>

    <div class="certificate-print-area certificate-scale-wrap">
        @include('components.certificate-templates', [
            'certificate' => $certificate,
            'branding' => $branding,
            'template' => $certificate->template ?: 'emerald-classic',
            'studentName' => data_get($certificate->metadata, 'display_name') ?: ($certificate->user->name ?? auth()->user()->name),
        ])
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
@endsection
