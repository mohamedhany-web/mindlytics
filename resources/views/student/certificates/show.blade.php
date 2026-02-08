@extends('layouts.app')

@section('title', 'الشهادة')
@section('header', 'الشهادة')

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .certificate-container, .certificate-container * {
            visibility: visible;
        }
        .certificate-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
    }

    /* Certificate Templates */
    .certificate-template {
        position: relative;
        overflow: hidden;
        page-break-inside: avoid;
    }

    /* Template 1: Classic Elegant */
    .template-classic {
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 50%, #f0f9ff 100%);
        border: 8px solid #1e40af;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), inset 0 0 100px rgba(30, 64, 175, 0.05);
    }

    .template-classic::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(30, 64, 175, 0.03) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }

    .template-classic::after {
        content: '';
        position: absolute;
        inset: 20px;
        border: 2px solid rgba(30, 64, 175, 0.2);
        border-radius: 12px;
        pointer-events: none;
    }

    /* Template 2: Modern Gradient */
    .template-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
        background-size: 400% 400%;
        animation: gradientShift 15s ease infinite;
        border: none;
        box-shadow: 0 25px 70px rgba(102, 126, 234, 0.4);
    }

    /* Template 3: Premium Gold */
    .template-premium {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 50%, #f5f7fa 100%);
        border: 12px solid;
        border-image: linear-gradient(135deg, #d4af37, #ffd700, #ffed4e, #ffd700, #d4af37) 1;
        box-shadow: 0 30px 80px rgba(212, 175, 55, 0.3), inset 0 0 150px rgba(255, 215, 0, 0.1);
    }

    .template-premium::before {
        content: '';
        position: absolute;
        inset: 0;
        background: 
            repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(212, 175, 55, 0.03) 10px, rgba(212, 175, 55, 0.03) 20px),
            repeating-linear-gradient(-45deg, transparent, transparent 10px, rgba(255, 215, 0, 0.03) 10px, rgba(255, 215, 0, 0.03) 20px);
        pointer-events: none;
    }

    /* Template 4: Tech Blue */
    .template-tech {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
        border: 6px solid #3b82f6;
        box-shadow: 
            0 0 0 4px rgba(59, 130, 246, 0.2),
            0 0 0 8px rgba(59, 130, 246, 0.1),
            0 30px 80px rgba(0, 0, 0, 0.5),
            inset 0 0 100px rgba(59, 130, 246, 0.1);
    }

    .template-tech::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: 
            linear-gradient(rgba(59, 130, 246, 0.1) 1px, transparent 1px),
            linear-gradient(90deg, rgba(59, 130, 246, 0.1) 1px, transparent 1px);
        background-size: 50px 50px;
        opacity: 0.3;
        pointer-events: none;
    }

    /* Template 5: Minimalist Clean */
    .template-minimal {
        background: #ffffff;
        border: 3px solid #1f2937;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    }

    /* Decorative Elements */
    .certificate-seal {
        position: absolute;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #1e40af);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
        z-index: 10;
    }

    .certificate-seal::before {
        content: '';
        position: absolute;
        inset: 8px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
    }

    .certificate-seal::after {
        content: '';
        position: absolute;
        inset: 20px;
        border: 2px solid rgba(255, 255, 255, 0.5);
        border-radius: 50%;
    }

    .certificate-corner {
        position: absolute;
        width: 80px;
        height: 80px;
        border: 4px solid;
        opacity: 0.3;
    }

    .corner-top-left {
        top: 20px;
        left: 20px;
        border-right: none;
        border-bottom: none;
        border-top-left-radius: 15px;
    }

    .corner-top-right {
        top: 20px;
        right: 20px;
        border-left: none;
        border-bottom: none;
        border-top-right-radius: 15px;
    }

    .corner-bottom-left {
        bottom: 20px;
        left: 20px;
        border-right: none;
        border-top: none;
        border-bottom-left-radius: 15px;
    }

    .corner-bottom-right {
        bottom: 20px;
        right: 20px;
        border-left: none;
        border-top: none;
        border-bottom-right-radius: 15px;
    }

    /* Watermark */
    .certificate-watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        font-size: 120px;
        font-weight: 900;
        color: rgba(0, 0, 0, 0.03);
        z-index: 1;
        white-space: nowrap;
        letter-spacing: 20px;
    }

    /* Signature Lines */
    .signature-line {
        border-top: 2px solid #1f2937;
        width: 200px;
        margin: 10px auto;
    }

    /* Animations */
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }

    .shimmer-effect {
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        background-size: 1000px 100%;
        animation: shimmer 3s infinite;
    }

    /* Template Selector */
    .template-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .template-option {
        cursor: pointer;
        padding: 1rem;
        border: 3px solid transparent;
        border-radius: 12px;
        transition: all 0.3s;
        text-align: center;
        background: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .template-option:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .template-option.active {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
    }

    .template-preview {
        width: 100%;
        height: 80px;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }

    .preview-classic { background: linear-gradient(135deg, #f0f9ff, #ffffff); border: 3px solid #1e40af; }
    .preview-modern { background: linear-gradient(135deg, #667eea, #764ba2, #f093fb); }
    .preview-premium { background: linear-gradient(135deg, #f5f7fa, #c3cfe2); border: 3px solid #d4af37; }
    .preview-tech { background: linear-gradient(135deg, #0f172a, #1e293b); border: 3px solid #3b82f6; }
    .preview-minimal { background: #ffffff; border: 3px solid #1f2937; }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Template Selector -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 no-print">
        <h3 class="text-lg font-bold text-gray-900 mb-4">اختر قالب الشهادة</h3>
        <div class="template-selector" x-data="{ selectedTemplate: 'classic' }">
            <div class="template-option" :class="{ 'active': selectedTemplate === 'classic' }" @click="selectedTemplate = 'classic'; document.getElementById('certificate-template').className = 'certificate-template template-classic'">
                <div class="template-preview preview-classic"></div>
                <span class="text-sm font-medium text-gray-700">كلاسيكي أنيق</span>
            </div>
            <div class="template-option" :class="{ 'active': selectedTemplate === 'modern' }" @click="selectedTemplate = 'modern'; document.getElementById('certificate-template').className = 'certificate-template template-modern'">
                <div class="template-preview preview-modern"></div>
                <span class="text-sm font-medium text-gray-700">حديث متدرج</span>
            </div>
            <div class="template-option" :class="{ 'active': selectedTemplate === 'premium' }" @click="selectedTemplate = 'premium'; document.getElementById('certificate-template').className = 'certificate-template template-premium'">
                <div class="template-preview preview-premium"></div>
                <span class="text-sm font-medium text-gray-700">بريميوم ذهبي</span>
            </div>
            <div class="template-option" :class="{ 'active': selectedTemplate === 'tech' }" @click="selectedTemplate = 'tech'; document.getElementById('certificate-template').className = 'certificate-template template-tech'">
                <div class="template-preview preview-tech"></div>
                <span class="text-sm font-medium text-gray-700">تقني أزرق</span>
            </div>
            <div class="template-option" :class="{ 'active': selectedTemplate === 'minimal' }" @click="selectedTemplate = 'minimal'; document.getElementById('certificate-template').className = 'certificate-template template-minimal'">
                <div class="template-preview preview-minimal"></div>
                <span class="text-sm font-medium text-gray-700">بسيط نظيف</span>
            </div>
        </div>
    </div>

    <!-- Certificate Container -->
    <div class="certificate-container">
        @include('components.certificate-templates', [
            'certificate' => $certificate,
            'template' => 'classic',
            'studentName' => auth()->user()->name
        ])
    </div>

    <!-- Action Buttons -->
    <div class="text-center space-x-4 no-print">
        <button onclick="window.print()" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 transform hover:scale-105">
            <i class="fas fa-print"></i>
            <span>طباعة الشهادة</span>
        </button>
        <a href="{{ route('student.certificates.index') }}" class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 px-8 py-3 rounded-xl font-semibold transition-all duration-300">
            <i class="fas fa-arrow-right"></i>
            <span>رجوع إلى الشهادات</span>
        </a>
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
        const element = document.getElementById('certificate-template');
        const opt = {
            margin: 0,
            filename: 'certificate-{{ $certificate->certificate_number }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };
        html2pdf().set(opt).from(element).save();
    }

    // Initialize Alpine.js for template selector
    document.addEventListener('alpine:init', () => {
        Alpine.data('templateSelector', () => ({
            selectedTemplate: 'classic',
            selectTemplate(template) {
                this.selectedTemplate = template;
                const certElement = document.getElementById('certificate-template');
                certElement.className = `certificate-template template-${template}`;
            }
        }));
    });
</script>
@endpush
@endsection
