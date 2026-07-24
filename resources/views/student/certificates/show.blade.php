@extends('layouts.app')

@section('title', 'الشهادة')
@section('header', 'الشهادة')

@push('styles')
@include('components.certificate-styles')
<style>
    @media print {
        body * { visibility: hidden; }
        .certificate-container, .certificate-container * { visibility: visible; }
        .certificate-container { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
    }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto space-y-6 py-6 px-4">
    <div class="bg-white rounded-xl shadow p-5 border border-gray-200 no-print flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $certificate->title ?? 'شهادة إتمام' }}</h1>
            <p class="text-sm text-gray-600 mt-1 font-mono">Serial: {{ $certificate->serial_number ?? $certificate->certificate_number }}</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold"><i class="fas fa-print ml-1"></i>طباعة</button>
            <button onclick="downloadCertificate()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg font-semibold"><i class="fas fa-download ml-1"></i>PDF</button>
        </div>
    </div>

    <div class="certificate-container overflow-auto">
        @include('components.certificate-templates', [
            'certificate' => $certificate,
            'branding' => $branding,
            'template' => $certificate->template ?: 'achievement',
            'studentName' => $certificate->user->name ?? auth()->user()->name,
        ])
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function downloadCertificate() {
    const element = document.getElementById('certificate-template');
    html2pdf().set({
        margin: 0,
        filename: 'certificate-{{ $certificate->serial_number ?? $certificate->certificate_number }}.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true, allowTaint: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
    }).from(element).save();
}
</script>
@endpush
@endsection
