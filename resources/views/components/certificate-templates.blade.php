{{-- Routes certificate templates: new Mindlytics designs or legacy achievement --}}
@php
    $template = $template ?? ($certificate->template ?? 'emerald-classic');
    $designs = array_keys(\App\Services\CertificateIssueService::designCatalog());
    $legacy = array_keys(\App\Models\Certificate::legacyTemplates());
    $isDesign = in_array($template, $designs, true);

    $studentName = $studentName
        ?? data_get($certificate, 'metadata.display_name')
        ?? $certificate?->user?->name
        ?? 'Student';
    $courseTitle = $courseTitle
        ?? $courseName
        ?? $certificate?->course?->title
        ?? $certificate?->course_name
        ?? 'Course';
    $serialNumber = $serialNumber
        ?? $certificate?->serial_number
        ?? $certificate?->certificate_number
        ?? '—';
    $issueDate = $issueDate
        ?? $certificate?->issued_at
        ?? $certificate?->issue_date
        ?? now();
    $issueDateFormatted = $issueDate instanceof \Carbon\Carbon
        ? $issueDate->format('j / n / Y')
        : \Carbon\Carbon::parse($issueDate)->format('j / n / Y');
    $instructorName = $instructorName
        ?? $certificate?->instructor_signature_name
        ?? $certificate?->instructor?->name
        ?? $certificate?->course?->instructor?->name
        ?? 'Instructor';
    $isRtl = $template === 'cairo-gold-arabic';
@endphp

@if($isDesign)
    @include('components.certificates.designs.'.$template, [
        'studentName' => $studentName,
        'courseTitle' => $courseTitle,
        'serialNumber' => $serialNumber,
        'issueDateFormatted' => $issueDateFormatted,
        'instructorName' => $instructorName,
        'instructorLabel' => $isRtl ? 'المدرّب' : 'Instructor',
        'serialLabel' => $isRtl ? 'الرقم التسلسلي' : 'Serial Number',
        'dateLabel' => $isRtl ? 'التاريخ' : 'Date',
        'templateDomId' => $templateDomId ?? 'certificate-template',
        'branding' => $branding ?? \App\Models\CertificateBranding::current(),
    ])
@else
    @php
        if ($template === 'econev' || ! in_array($template, $legacy, true)) {
            $template = 'achievement';
        }
    @endphp
    @include('components.certificate-econev', [
        'certificate' => $certificate ?? null,
        'branding' => $branding ?? null,
        'template' => $template,
        'templateDomId' => $templateDomId ?? 'certificate-template',
        'studentName' => $studentName,
        'courseTitle' => $courseTitle,
        'courseName' => $courseTitle,
        'description' => $description ?? null,
        'certificateNumber' => $certificateNumber ?? null,
        'serialNumber' => $serialNumber,
        'issueDate' => $issueDate,
        'academySignatureName' => $academySignatureName ?? $serialNumber,
    ])
@endif
