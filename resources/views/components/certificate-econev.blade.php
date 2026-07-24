{{-- Certificate of Achievement — pure HTML/CSS recreation (no background image) --}}
@php
    $studentName = $studentName ?? ($certificate->user->name ?? 'Name Surname');
    $courseTitle = $courseTitle ?? ($certificate->title ?? $certificate->course_name ?? '');
    $courseName = $courseName ?? ($certificate->course->title ?? '');
    $description = trim((string) ($description ?? ($certificate->description ?? '')));
    if ($description === '') {
        $subject = $courseName ?: $courseTitle;
        $description = $subject
            ? 'For outstanding achievement, dedication, and successful completion of the ' . $subject . ' program.'
            : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation.';
    }
    $issueDate = $issueDate ?? ($certificate->issued_at ? $certificate->issued_at->format('Y-m-d') : ($certificate->issue_date ? $certificate->issue_date->format('Y-m-d') : ''));
    $serialNumber = $serialNumber ?? ($certificate->serial_number ?? '');
    $certificateNumber = $certificateNumber ?? ($certificate->certificate_number ?? '');
    $academySignatureName = $academySignatureName ?? ($certificate->academy_signature_name ?? '');
    $platformName = config('app.name', 'Mindlytics');
@endphp

<div id="certificate-template"
     class="certificate-template template-econev certificate-print relative mx-auto"
     style="width: 297mm; height: 210mm; margin: 0 auto; box-sizing: border-box;">

    {{-- Geometric frame (SVG) --}}
    <svg class="econev-art" viewBox="0 0 1123 794" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="none">
        {{-- Top-left soft shape --}}
        <path d="M0 0 H210 L140 95 H0 Z" fill="#D7DEE6" opacity="0.55"/>
        {{-- Top-right navy tab --}}
        <path d="M1123 0 H920 L980 78 H1123 Z" fill="#00334E"/>
        {{-- Left navy chevron --}}
        <path d="M0 210 L95 320 L0 430 Z" fill="#00334E"/>
        {{-- Soft gray triangle mid-left --}}
        <path d="M70 360 L175 455 L70 520 Z" fill="#C9D2DB" opacity="0.45"/>
        {{-- Teal hexagon outline --}}
        <polygon points="155,390 195,415 195,465 155,490 115,465 115,415" fill="none" stroke="#00A9A5" stroke-width="7"/>
        {{-- Bottom-left teal block --}}
        <path d="M0 794 V620 L130 720 V794 Z" fill="#00A9A5"/>
        {{-- Right navy arrow --}}
        <path d="M1123 300 L980 400 L1123 500 Z" fill="#00334E"/>
        {{-- Bottom-right teal --}}
        <path d="M1123 794 V680 L980 760 V794 Z" fill="#00A9A5"/>
        {{-- Bottom center outline triangle --}}
        <path d="M510 794 L561.5 700 L613 794" fill="none" stroke="#00334E" stroke-width="10"/>
    </svg>

    {{-- Logo --}}
    <div class="econev-logo">
        <div class="econev-logo-mark" aria-hidden="true">
            <span class="d1"></span>
            <span class="d2"></span>
            <span class="d3"></span>
        </div>
        <div class="econev-logo-text">
            <strong>LOGO</strong>
            <span>{{ strtoupper($platformName) }}</span>
        </div>
    </div>

    {{-- Premium seal --}}
    <div class="econev-seal" aria-hidden="true">
        <svg viewBox="0 0 160 175" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="econevGold" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#E0C07A"/>
                    <stop offset="100%" stop-color="#C5A059"/>
                </linearGradient>
            </defs>
            {{-- Laurel left --}}
            <g fill="none" stroke="#00334E" stroke-width="3.2" stroke-linecap="round">
                <path d="M42 118 C22 98 22 62 48 42"/>
                <path d="M48 110 C32 94 32 68 52 52"/>
                <path d="M36 95 C28 88 28 72 40 62"/>
                <path d="M38 78 C32 74 32 66 40 60"/>
                {{-- Laurel right --}}
                <path d="M118 118 C138 98 138 62 112 42"/>
                <path d="M112 110 C128 94 128 68 108 52"/>
                <path d="M124 95 C132 88 132 72 120 62"/>
                <path d="M122 78 C128 74 128 66 120 60"/>
            </g>
            {{-- Leaves accents --}}
            <g fill="#00334E">
                <ellipse cx="34" cy="70" rx="5" ry="9" transform="rotate(-35 34 70)"/>
                <ellipse cx="30" cy="88" rx="5" ry="9" transform="rotate(-20 30 88)"/>
                <ellipse cx="36" cy="104" rx="5" ry="9" transform="rotate(-5 36 104)"/>
                <ellipse cx="126" cy="70" rx="5" ry="9" transform="rotate(35 126 70)"/>
                <ellipse cx="130" cy="88" rx="5" ry="9" transform="rotate(20 130 88)"/>
                <ellipse cx="124" cy="104" rx="5" ry="9" transform="rotate(5 124 104)"/>
            </g>
            {{-- Stars --}}
            <g fill="#C5A059">
                <path d="M80 36 l2.2 4.6 5 .7-3.6 3.5.9 5.1L80 47.4 75.5 49.9l.9-5.1-3.6-3.5 5-.7z"/>
                <path d="M62 42 l1.8 3.7 4 .6-2.9 2.8.7 4.1L62 51.4 58.4 53.4l.7-4.1-2.9-2.8 4-.6z"/>
                <path d="M98 42 l1.8 3.7 4 .6-2.9 2.8.7 4.1L98 51.4 94.4 53.4l.7-4.1-2.9-2.8 4-.6z"/>
                <path d="M52 54 l1.5 3.1 3.4.5-2.4 2.4.6 3.4L52 62 49 63.8l.6-3.4-2.4-2.4 3.4-.5z"/>
                <path d="M108 54 l1.5 3.1 3.4.5-2.4 2.4.6 3.4L108 62 105 63.8l.6-3.4-2.4-2.4 3.4-.5z"/>
            </g>
            <text x="80" y="78" text-anchor="middle" font-family="Montserrat, Arial, sans-serif" font-size="11" font-weight="800" fill="#00334E" letter-spacing="1">PREMIUM</text>
            <text x="80" y="92" text-anchor="middle" font-family="Montserrat, Arial, sans-serif" font-size="11" font-weight="800" fill="#00334E" letter-spacing="1">QUALITY</text>
            <path d="M58 100 H102" stroke="#00334E" stroke-width="1.5"/>
            <path d="M64 105 H96" stroke="#00334E" stroke-width="1.2"/>
            {{-- Ribbon --}}
            <path d="M28 128 L48 118 H112 L132 128 L120 148 H40 Z" fill="url(#econevGold)"/>
            <path d="M48 118 L42 138 L52 128 Z" fill="#A8863E"/>
            <path d="M112 118 L118 138 L108 128 Z" fill="#A8863E"/>
            <text x="80" y="138" text-anchor="middle" font-family="Montserrat, Arial, sans-serif" font-size="9" font-weight="700" fill="#fff" letter-spacing="0.8">CERTIFICATION</text>
            <text x="80" y="162" text-anchor="middle" font-family="Montserrat, Arial, sans-serif" font-size="8" font-weight="700" fill="#00334E" letter-spacing="1.2">SINCE 1980</text>
        </svg>
    </div>

    {{-- Center content --}}
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
                @if(!empty($certificate->academy_signature))
                    <img src="{{ asset('storage/' . $certificate->academy_signature) }}" alt="" class="econev-sig-img">
                @elseif($academySignatureName)
                    {{ $academySignatureName }}
                @else
                    {{-- empty line ready for signature --}}
                @endif
            </div>
            <div class="econev-meta-line"></div>
            <div class="econev-meta-label">SIGNATURE</div>
        </div>
    </div>

    @if($serialNumber || $certificateNumber)
        <div class="econev-serial">{{ $serialNumber ?: $certificateNumber }}</div>
    @endif
</div>
