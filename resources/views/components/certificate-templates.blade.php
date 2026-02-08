{{-- Certificate Templates Component --}}
{{-- Usage: @include('components.certificate-templates', ['certificate' => $certificate, 'template' => 'classic']) --}}

@php
    $template = $template ?? 'classic';
    $studentName = $studentName ?? ($certificate->user->name ?? auth()->user()->name ?? 'الطالب');
    $courseTitle = $courseTitle ?? ($certificate->title ?? $certificate->course_name ?? 'شهادة الإتمام');
    $courseName = $courseName ?? ($certificate->course->title ?? $certificate->course_name ?? '');
    $description = $description ?? $certificate->description ?? '';
    $certificateNumber = $certificateNumber ?? $certificate->certificate_number ?? '';
    $serialNumber = $serialNumber ?? ($certificate->serial_number ?? '');
    $issueDate = $issueDate ?? ($certificate->issued_at ? $certificate->issued_at->format('Y-m-d') : ($certificate->issue_date ? $certificate->issue_date->format('Y-m-d') : ''));
    $verificationCode = $verificationCode ?? $certificate->verification_code ?? '';
    
    // التوقيعات
    $academySignatureName = $academySignatureName ?? ($certificate->academy_signature_name ?? 'المدير العام');
    $academySignatureTitle = $academySignatureTitle ?? ($certificate->academy_signature_title ?? 'Mindlytics Academy');
    $instructorSignatureName = $instructorSignatureName ?? ($certificate->instructor_signature_name ?? ($certificate->instructor->name ?? 'المدرب المعتمد'));
    $instructorSignatureTitle = $instructorSignatureTitle ?? ($certificate->instructor_signature_title ?? 'المدرب المعتمد');
    
    // QR Code
    $verificationUrl = $verificationUrl ?? ($certificate->verification_url ?? route('public.certificates.verify', ['code' => $verificationCode]));
@endphp

<div id="certificate-template" class="certificate-template template-{{ $template }} relative mx-auto certificate-print" style="width: 297mm; height: 210mm; padding: 25mm 20mm; margin: 0 auto; box-sizing: border-box;">
    <!-- Watermark -->
    <div class="certificate-watermark">MINDLYTICS</div>

    <!-- Decorative Corners -->
    <div class="certificate-corner corner-top-left" style="border-color: currentColor;"></div>
    <div class="certificate-corner corner-top-right" style="border-color: currentColor;"></div>
    <div class="certificate-corner corner-bottom-left" style="border-color: currentColor;"></div>
    <div class="certificate-corner corner-bottom-right" style="border-color: currentColor;"></div>

    <!-- Certificate Seal (Top Right) -->
    <div class="certificate-seal" style="top: 25px; right: 25px; width: 100px; height: 100px;">
        <i class="fas fa-certificate text-white text-2xl relative z-10"></i>
    </div>

    <!-- Serial Number Badge (Top Left) -->
    @if($serialNumber)
    <div class="absolute z-20 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-3 py-2 rounded-lg shadow-lg" style="top: 25px; left: 25px; min-width: 180px;">
        <div class="text-xs font-bold mb-0.5 opacity-90">Serial No.</div>
        <div class="text-sm font-black font-mono tracking-wide">{{ $serialNumber }}</div>
    </div>
    @endif

    <!-- Certificate Content -->
    <div class="relative z-20 h-full flex flex-col">
        <!-- Top Section: Logo and Title -->
        <div class="text-center mb-8">
            <!-- Logo/Icon -->
            <div class="mb-4">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-xl bg-gradient-to-br from-blue-600 via-blue-500 to-blue-700 text-white shadow-xl mb-3">
                    <span class="text-2xl font-black">M</span>
                </div>
                <h3 class="text-lg font-black text-gray-800 mb-0.5" style="color: inherit;">Mindlytics</h3>
                <p class="text-xs font-semibold text-gray-600" style="color: inherit;">أكاديمية البرمجة</p>
            </div>

            <!-- Main Title -->
            <div class="mb-6">
                <h1 class="text-5xl font-black mb-3" style="color: inherit; text-shadow: 2px 2px 4px rgba(0,0,0,0.1); letter-spacing: 2px;">
                    شهادة إتمام
                </h1>
                <div class="w-32 h-1 mx-auto" style="background: linear-gradient(90deg, transparent, currentColor, transparent);"></div>
            </div>
        </div>

        <!-- Middle Section: Student and Course Info -->
        <div class="flex-1 flex flex-col justify-center text-center mb-8">
            <!-- Student Name -->
            <div class="mb-6">
                <p class="text-base font-medium text-gray-600 mb-3" style="color: inherit;">هذه الشهادة تمنح إلى</p>
                <h2 class="text-4xl font-black mb-2" style="color: inherit; text-shadow: 2px 2px 4px rgba(0,0,0,0.1); line-height: 1.2;">
                    {{ $studentName }}
                </h2>
            </div>

            <!-- Course Information -->
            <div class="mb-4">
                <p class="text-lg font-semibold text-gray-700 mb-2" style="color: inherit;">
                    لإتمامه بنجاح
                </p>
                <h3 class="text-3xl font-bold mb-2" style="color: inherit; line-height: 1.3;">
                    {{ $courseTitle }}
                </h3>
                @if($courseName && $courseName !== $courseTitle)
                <p class="text-base text-gray-600" style="color: inherit;">
                    في: {{ $courseName }}
                </p>
                @endif
            </div>

            @if($description)
            <div class="mb-4">
                <p class="text-sm text-gray-600 leading-relaxed max-w-2xl mx-auto" style="color: inherit;">
                    {{ $description }}
                </p>
            </div>
            @endif
        </div>

        <!-- Bottom Section: Details, QR Code, and Signatures -->
        <div class="mt-auto pt-4 border-t-2" style="border-color: rgba(0,0,0,0.15);">
            <div class="grid grid-cols-3 gap-6">
                <!-- Left Column: Issue Date + Instructor Signature -->
                <div class="text-center">
                    @if($issueDate)
                    <div class="mb-4">
                        <p class="text-xs font-semibold text-gray-700 mb-1" style="color: inherit;">تاريخ الإصدار</p>
                        <p class="text-sm text-gray-600 font-medium" style="color: inherit;">{{ $issueDate }}</p>
                    </div>
                    @endif
                    
                    <!-- Instructor Signature -->
                    <div class="mt-6">
                        @if(isset($certificate->instructor_signature) && $certificate->instructor_signature)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $certificate->instructor_signature) }}" alt="Instructor Signature" class="h-10 mx-auto object-contain">
                        </div>
                        @else
                        <div class="signature-line" style="width: 150px; margin: 8px auto;"></div>
                        @endif
                        <p class="text-xs font-semibold text-gray-700 mt-2" style="color: inherit;">{{ $instructorSignatureName }}</p>
                        <p class="text-xs text-gray-500" style="color: inherit;">{{ $instructorSignatureTitle }}</p>
                    </div>
                </div>

                <!-- Middle Column: QR Code + Verification Code -->
                <div class="text-center">
                    @if($verificationCode)
                    <div class="mb-3">
                        <p class="text-xs font-semibold text-gray-700 mb-2" style="color: inherit;">رمز التحقق</p>
                        <p class="text-xs text-gray-600 font-mono mb-3" style="color: inherit;">{{ $verificationCode }}</p>
                    </div>
                    
                    <!-- QR Code -->
                    <div class="flex flex-col items-center">
                        <div class="bg-white p-2 rounded-lg shadow-md mb-2 inline-block">
                            <div id="qr-code-{{ $verificationCode }}" class="w-16 h-16"></div>
                        </div>
                        <p class="text-xs font-semibold text-gray-700 mb-0.5" style="color: inherit;">للتحقق من الشهادة</p>
                        <p class="text-xs text-gray-500" style="color: inherit;">امسح الكود</p>
                    </div>
                    @endif
                </div>

                <!-- Right Column: Certificate Number + Academy Signature -->
                <div class="text-center">
                    @if($certificateNumber)
                    <div class="mb-4">
                        <p class="text-xs font-semibold text-gray-700 mb-1" style="color: inherit;">رقم الشهادة</p>
                        <p class="text-sm text-gray-600 font-mono font-medium" style="color: inherit;">{{ $certificateNumber }}</p>
                    </div>
                    @endif
                    
                    <!-- Academy Signature -->
                    <div class="mt-6">
                        @if(isset($certificate->academy_signature) && $certificate->academy_signature)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $certificate->academy_signature) }}" alt="Academy Signature" class="h-10 mx-auto object-contain">
                        </div>
                        @else
                        <div class="signature-line" style="width: 150px; margin: 8px auto;"></div>
                        @endif
                        <p class="text-xs font-semibold text-gray-700 mt-2" style="color: inherit;">{{ $academySignatureName }}</p>
                        <p class="text-xs text-gray-500" style="color: inherit;">{{ $academySignatureTitle }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shimmer Effect -->
    <div class="absolute inset-0 shimmer-effect pointer-events-none opacity-30"></div>
</div>

@if($verificationCode)
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qrElement = document.getElementById('qr-code-{{ $verificationCode }}');
        if (qrElement && typeof QRCode !== 'undefined') {
            new QRCode(qrElement, {
                text: '{{ $verificationUrl }}',
                width: 64,
                height: 64,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        }
    });
</script>
@endpush
@endif
