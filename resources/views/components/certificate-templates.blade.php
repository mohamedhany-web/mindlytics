@php
    $template = $template ?? 'achievement';
    if ($template === 'econev' || ! array_key_exists($template, \App\Models\Certificate::availableTemplates())) {
        $template = 'achievement';
    }
@endphp

@include('components.certificate-econev', [
    'certificate' => $certificate ?? null,
    'branding' => $branding ?? null,
    'template' => $template,
    'templateDomId' => $templateDomId ?? 'certificate-template',
    'studentName' => $studentName ?? null,
    'courseTitle' => $courseTitle ?? null,
    'courseName' => $courseName ?? null,
    'description' => $description ?? null,
    'certificateNumber' => $certificateNumber ?? null,
    'serialNumber' => $serialNumber ?? null,
    'issueDate' => $issueDate ?? null,
    'academySignatureName' => $academySignatureName ?? null,
])
