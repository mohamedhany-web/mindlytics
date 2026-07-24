{{-- Certificate of Achievement — HTML/CSS with academy branding assets --}}
@php
    $branding = $branding ?? \App\Models\CertificateBranding::current();
    $templateKey = $template ?? ($certificate->template ?? $branding->default_template ?? 'achievement');
    if ($templateKey === 'econev') {
        $templateKey = 'achievement';
    }
    $theme = match ($templateKey) {
        'achievement-teal' => 'teal',
        'achievement-navy' => 'deep',
        default => 'navy',
    };

    $studentName = $studentName ?? ($certificate->user->name ?? 'Name Surname');
    $courseTitle = $courseTitle ?? ($certificate->title ?? $certificate->course_name ?? '');
    $courseName = $courseName ?? ($certificate->course->title ?? '');
    $description = trim((string) ($description ?? ($certificate->description ?? '')));
    if ($description === '') {
        $subject = $courseName ?: $courseTitle;
        $description = $subject
            ? 'For outstanding achievement, dedication, and successful completion of the ' . $subject . ' program.'
            : 'This certificate is awarded in recognition of outstanding achievement and successful completion of the program.';
    }
    $issueDate = $issueDate ?? ($certificate->issued_at ? $certificate->issued_at->format('Y-m-d') : ($certificate->issue_date ? $certificate->issue_date->format('Y-m-d') : ''));
    $serialNumber = $serialNumber ?? ($certificate->serial_number ?? '');
    $certificateNumber = $certificateNumber ?? ($certificate->certificate_number ?? '');
    $academySignatureName = $academySignatureName ?? ($certificate->academy_signature_name ?? $branding->signature_name ?? '');
    $academyName = $branding->academy_name ?: config('app.name', 'Mindlytics');
    $academyTagline = $branding->academy_tagline ?: '';
    $sealLabel = $branding->seal_label ?: 'CERTIFICATION';
    $sealSince = $branding->seal_since ?: '2020';

    $logoUrl = $certificate?->logoUrl($branding) ?: $branding->logoUrl();
    $signatureUrl = $certificate?->signatureImageUrl($branding) ?: $branding->signatureUrl();
    $stampUrl = $certificate?->stampUrl($branding) ?: $branding->stampUrl();

    $domId = $templateDomId ?? 'certificate-template';
@endphp

<div id="{{ $domId }}"
     class="certificate-template template-achievement theme-{{ $theme }} certificate-print relative mx-auto"
     data-template="{{ $templateKey }}"
     style="width: 297mm; height: 210mm; margin: 0 auto; box-sizing: border-box;">

    <svg class="econev-art" viewBox="0 0 1123 794" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="none">
        <path class="shape-soft" d="M0 0 H210 L140 95 H0 Z" opacity="0.55"/>
        <path class="shape-primary" d="M1123 0 H920 L980 78 H1123 Z"/>
        <path class="shape-primary" d="M0 210 L95 320 L0 430 Z"/>
        <path class="shape-soft" d="M70 360 L175 455 L70 520 Z" opacity="0.45"/>
        <polygon class="shape-accent-stroke" points="155,390 195,415 195,465 155,490 115,465 115,415" fill="none" stroke-width="7"/>
        <path class="shape-accent" d="M0 794 V620 L130 720 V794 Z"/>
        <path class="shape-primary" d="M1123 300 L980 400 L1123 500 Z"/>
        <path class="shape-accent" d="M1123 794 V680 L980 760 V794 Z"/>
        <path class="shape-primary-stroke" d="M510 794 L561.5 700 L613 794" fill="none" stroke-width="10"/>
    </svg>

    {{-- Academy logo --}}
    <div class="econev-logo">
        @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="{{ $academyName }}" class="econev-logo-img">
        @else
            <div class="econev-logo-mark" aria-hidden="true">
                <span class="d1"></span>
                <span class="d2"></span>
                <span class="d3"></span>
            </div>
        @endif
        <div class="econev-logo-text">
            <strong>{{ strtoupper($academyName) }}</strong>
            @if($academyTagline)
                <span>{{ $academyTagline }}</span>
            @endif
        </div>
    </div>

    {{-- Stamp / seal --}}
    <div class="econev-seal" aria-hidden="true">
        @if($stampUrl)
            <img src="{{ $stampUrl }}" alt="Stamp" class="econev-stamp-img">
        @else
            <svg viewBox="0 0 160 175" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="econevGold-{{ $domId }}" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#E0C07A"/>
                        <stop offset="100%" stop-color="#C5A059"/>
                    </linearGradient>
                </defs>
                <g fill="none" stroke="#00334E" stroke-width="3.2" stroke-linecap="round">
                    <path d="M42 118 C22 98 22 62 48 42"/>
                    <path d="M48 110 C32 94 32 68 52 52"/>
                    <path d="M36 95 C28 88 28 72 40 62"/>
                    <path d="M118 118 C138 98 138 62 112 42"/>
                    <path d="M112 110 C128 94 128 68 108 52"/>
                    <path d="M124 95 C132 88 132 72 120 62"/>
                </g>
                <g fill="#C5A059">
                    <path d="M80 36 l2.2 4.6 5 .7-3.6 3.5.9 5.1L80 47.4 75.5 49.9l.9-5.1-3.6-3.5 5-.7z"/>
                    <path d="M62 42 l1.8 3.7 4 .6-2.9 2.8.7 4.1L62 51.4 58.4 53.4l.7-4.1-2.9-2.8 4-.6z"/>
                    <path d="M98 42 l1.8 3.7 4 .6-2.9 2.8.7 4.1L98 51.4 94.4 53.4l.7-4.1-2.9-2.8 4-.6z"/>
                </g>
                <path d="M28 128 L48 118 H112 L132 128 L120 148 H40 Z" fill="url(#econevGold-{{ $domId }})"/>
                <text x="80" y="138" text-anchor="middle" font-family="Montserrat, Arial, sans-serif" font-size="8" font-weight="700" fill="#fff" letter-spacing="0.8">{{ strtoupper($sealLabel) }}</text>
                <text x="80" y="162" text-anchor="middle" font-family="Montserrat, Arial, sans-serif" font-size="8" font-weight="700" fill="#00334E" letter-spacing="1.2">SINCE {{ $sealSince }}</text>
            </svg>
        @endif
    </div>

    <div class="econev-content">
        <h1 class="econev-title">CERTIFICATE</h1>
        <h2 class="econev-subtitle">Of Achievement</h2>
        <p class="econev-presented">This Certificate is Proudly Presented to</p>
        <div class="econev-name">{{ $studentName }}</div>
        <div class="econev-name-line"></div>
        <p class="econev-body">{{ $description }}</p>
    </div>

    <div class="econev-footer">
        <div class="econev-meta">
            <div class="econev-meta-value">{{ $issueDate ?: ' ' }}</div>
            <div class="econev-meta-line"></div>
            <div class="econev-meta-label">DATE</div>
        </div>
        <div class="econev-meta">
            <div class="econev-meta-value econev-sig-script">
                @if($signatureUrl)
                    <img src="{{ $signatureUrl }}" alt="Signature" class="econev-sig-img">
                @elseif($academySignatureName)
                    {{ $academySignatureName }}
                @endif
            </div>
            <div class="econev-meta-line"></div>
            <div class="econev-meta-label">SIGNATURE</div>
        </div>
    </div>

    @if($serialNumber || $certificateNumber)
        <div class="econev-serial">
            <span>Serial</span>
            <strong>{{ $serialNumber ?: $certificateNumber }}</strong>
        </div>
    @endif
</div>
