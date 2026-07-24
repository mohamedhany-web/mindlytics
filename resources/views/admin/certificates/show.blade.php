@extends('layouts.admin')

@section('title', 'تفاصيل الشهادة')
@section('header', 'تفاصيل الشهادة')

@push('styles')
@include('components.certificate-styles')
<style>
    @media print {
        body * { visibility: hidden; }
        .certificate-container, .certificate-container * { visibility: visible; }
        .certificate-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print { display: none !important; }
    }
</style>
@endpush
@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 no-print">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">شهادة #{{ $certificate->certificate_number }}</h1>
                <p class="text-gray-600 mt-1">تاريخ الإنشاء: {{ $certificate->created_at->format('Y-m-d') }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.certificates.edit', $certificate) }}" class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-edit ml-2"></i>تعديل
                </a>
                <a href="{{ route('admin.certificates.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i>رجوع
                </a>
            </div>
        </div>

        <!-- Certificate Info -->
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
                    @if($certificate->course)
                    <div><span class="text-gray-600">الكورس:</span> <span class="font-medium text-gray-900 mr-2">{{ $certificate->course->title }}</span></div>
                    @elseif($certificate->course_name)
                    <div><span class="text-gray-600">الكورس:</span> <span class="font-medium text-gray-900 mr-2">{{ $certificate->course_name }}</span></div>
                    @endif
                    <div><span class="text-gray-600">الحالة:</span> 
                        @php
                            $status = $certificate->status ?? ($certificate->is_verified ? 'issued' : 'pending');
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($status == 'issued') bg-green-100 text-green-800
                            @elseif($status == 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif mr-2">
                            {{ $status == 'issued' ? 'مُصدرة' : ($status == 'pending' ? 'معلقة' : 'ملغاة') }}
                        </span>
                    </div>
                    <div><span class="text-gray-600">تاريخ الإصدار:</span> <span class="font-medium text-gray-900 mr-2">{{ ($certificate->issued_at ? $certificate->issued_at->format('Y-m-d') : ($certificate->issue_date ? $certificate->issue_date->format('Y-m-d') : '-')) }}</span></div>
                    <div><span class="text-gray-600">رمز التحقق:</span> <span class="font-medium text-gray-900 mr-2 font-mono">{{ $certificate->verification_code ?? '-' }}</span></div>
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

    <!-- Template Selector + Certificate -->
    <div x-data="{ selectedTemplate: 'econev' }" class="space-y-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 no-print">
            <h3 class="text-lg font-bold text-gray-900 mb-4">اختر قالب الشهادة</h3>
            <div class="template-selector">
                <div class="template-option" :class="{ 'active': selectedTemplate === 'econev' }" @click="selectedTemplate = 'econev'">
                    <div class="template-preview preview-econev"></div>
                    <span class="text-sm font-medium text-gray-700">Certificate of Achievement</span>
                </div>
                <div class="template-option" :class="{ 'active': selectedTemplate === 'classic' }" @click="selectedTemplate = 'classic'; $nextTick(() => { const el = document.getElementById('certificate-template-legacy'); if (el) el.className = 'certificate-template template-classic relative mx-auto certificate-print'; })">
                    <div class="template-preview preview-classic"></div>
                    <span class="text-sm font-medium text-gray-700">كلاسيكي أنيق</span>
                </div>
                <div class="template-option" :class="{ 'active': selectedTemplate === 'modern' }" @click="selectedTemplate = 'modern'; $nextTick(() => { const el = document.getElementById('certificate-template-legacy'); if (el) el.className = 'certificate-template template-modern relative mx-auto certificate-print'; })">
                    <div class="template-preview preview-modern"></div>
                    <span class="text-sm font-medium text-gray-700">حديث متدرج</span>
                </div>
                <div class="template-option" :class="{ 'active': selectedTemplate === 'premium' }" @click="selectedTemplate = 'premium'; $nextTick(() => { const el = document.getElementById('certificate-template-legacy'); if (el) el.className = 'certificate-template template-premium relative mx-auto certificate-print'; })">
                    <div class="template-preview preview-premium"></div>
                    <span class="text-sm font-medium text-gray-700">بريميوم ذهبي</span>
                </div>
                <div class="template-option" :class="{ 'active': selectedTemplate === 'tech' }" @click="selectedTemplate = 'tech'; $nextTick(() => { const el = document.getElementById('certificate-template-legacy'); if (el) el.className = 'certificate-template template-tech relative mx-auto certificate-print'; })">
                    <div class="template-preview preview-tech"></div>
                    <span class="text-sm font-medium text-gray-700">تقني أزرق</span>
                </div>
                <div class="template-option" :class="{ 'active': selectedTemplate === 'minimal' }" @click="selectedTemplate = 'minimal'; $nextTick(() => { const el = document.getElementById('certificate-template-legacy'); if (el) el.className = 'certificate-template template-minimal relative mx-auto certificate-print'; })">
                    <div class="template-preview preview-minimal"></div>
                    <span class="text-sm font-medium text-gray-700">بسيط نظيف</span>
                </div>
            </div>
        </div>

        <div class="certificate-container" x-show="selectedTemplate === 'econev'" x-cloak>
            @include('components.certificate-econev', [
                'certificate' => $certificate,
                'studentName' => $certificate->user->name ?? 'Name Surname',
            ])
        </div>

        <div class="certificate-container" x-show="selectedTemplate !== 'econev'" x-cloak>
            @include('components.certificate-templates', [
                'certificate' => $certificate,
                'template' => 'classic',
                'templateDomId' => 'certificate-template-legacy',
                'studentName' => $certificate->user->name ?? 'الطالب'
            ])
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="text-center space-x-4 no-print">
        <button onclick="window.print()" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 transform hover:scale-105">
            <i class="fas fa-print"></i>
            <span>طباعة الشهادة</span>
        </button>
        <button onclick="downloadCertificate()" class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg shadow-green-500/30 hover:shadow-xl hover:shadow-green-500/40 transform hover:scale-105">
            <i class="fas fa-download"></i>
            <span>تحميل PDF</span>
        </button>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function downloadCertificate() {
        const visible = Array.from(document.querySelectorAll('.certificate-container')).find(el => el.offsetParent !== null);
        const element = (visible && visible.querySelector('.certificate-template')) || document.getElementById('certificate-template');
        const opt = {
            margin: 0,
            filename: 'certificate-{{ $certificate->certificate_number }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, allowTaint: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };
        html2pdf().set(opt).from(element).save();
    }
</script>
@endpush
@endsection
